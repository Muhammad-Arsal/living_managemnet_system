<?php

namespace App\Services\Admin;

use App\Enums\DocumentTemplateKey;
use App\Enums\SiteSettingKey;
use App\Models\Document;
use App\Models\Property;
use App\Models\SiteSetting;
use App\Models\Tenancy;
use App\Models\Tenant;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

class DocumentGenerationService
{
    public function __construct(
        private readonly DocumentService $documentService,
    ) {}

    /**
     * @return list<array{key: DocumentTemplateKey, label: string, description: string, icon: string}>
     */
    public function availableTemplates(): array
    {
        return array_map(
            fn (DocumentTemplateKey $key) => [
                'key' => $key,
                'label' => $key->label(),
                'description' => $key->description(),
                'icon' => $key->icon(),
            ],
            DocumentTemplateKey::cases()
        );
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public function fieldDefinitions(DocumentTemplateKey $template): array
    {
        $fields = config('document-templates.templates.'.$template->value.'.fields');

        if (! is_array($fields) || $fields === []) {
            throw ValidationException::withMessages([
                'template' => 'Unknown document template.',
            ]);
        }

        return $fields;
    }

    /**
     * @return array{values: array<string, mixed>, meta: array<string, array<string, mixed>>}
     */
    public function resolveFields(DocumentTemplateKey $template, Tenant $tenant, ?Property $property = null, ?Tenancy $tenancy = null): array
    {
        $definitions = $this->fieldDefinitions($template);
        $context = $this->buildContext($tenant, $property, $tenancy);

        $values = [];
        $meta = [];

        foreach ($definitions as $key => $definition) {
            $type = (string) ($definition['type'] ?? 'text');
            $source = $definition['source'] ?? null;
            $required = (bool) ($definition['required'] ?? false);
            $columns = is_array($definition['columns'] ?? null) ? $definition['columns'] : [];

            if ($type === 'checkboxes') {
                $default = is_array($definition['default'] ?? null) ? $definition['default'] : [];
                $value = $default;
                $filled = $value !== [];
            } elseif ($type === 'score_row') {
                $value = [];
                foreach (array_keys($columns) as $column) {
                    $value[$column] = '';
                }
                $filled = false;
            } else {
                $default = (string) ($definition['default'] ?? '');
                $resolved = $source ? $this->resolveSource($source, $context) : '';
                $value = $resolved !== '' ? $resolved : $default;
                $filled = $value !== '';
            }

            if ($filled) {
                $status = $source ? 'prefilled' : 'editable';
            } else {
                $status = $required ? 'missing' : 'editable';
            }

            $values[$key] = $value;
            $meta[$key] = [
                'label' => (string) $definition['label'],
                'type' => $type,
                'required' => $required,
                'status' => $status,
                'source_label' => $this->sourceLabel(is_string($source) ? $source : null),
                'options' => is_array($definition['options'] ?? null) ? $definition['options'] : [],
                'columns' => $columns,
            ];
        }

        return compact('values', 'meta');
    }

    /**
     * @param  array<string, mixed>  $input
     * @return array<string, mixed>
     */
    public function mergeFieldValues(DocumentTemplateKey $template, array $input): array
    {
        $definitions = $this->fieldDefinitions($template);
        $merged = [];

        foreach ($definitions as $key => $definition) {
            $type = (string) ($definition['type'] ?? 'text');
            $raw = $input[$key] ?? ($definition['default'] ?? ($type === 'checkboxes' ? [] : ''));

            if ($type === 'checkboxes') {
                $allowed = array_values(array_unique(array_merge(
                    array_map('strval', array_keys($definition['options'] ?? [])),
                    array_map('strval', array_values($definition['options'] ?? []))
                )));
                $selected = is_array($raw) ? $raw : [];
                $merged[$key] = array_values(array_filter(
                    array_map(static fn ($value) => trim((string) $value), $selected),
                    static fn (string $value) => $value !== '' && in_array($value, $allowed, true)
                ));

                continue;
            }

            if ($type === 'score_row') {
                $columns = array_keys($definition['columns'] ?? [
                    'initial' => 'Initial Score',
                    'm3' => '3 Month Review',
                    'm6' => '6 Month Review',
                    'm9' => '9 Month Review',
                    'm12' => '12 Month Review',
                ]);
                $row = is_array($raw) ? $raw : [];
                $merged[$key] = [];
                foreach ($columns as $column) {
                    $cell = $row[$column] ?? '';
                    $merged[$key][$column] = is_scalar($cell) ? trim((string) $cell) : '';
                }

                continue;
            }

            $merged[$key] = is_scalar($raw) ? trim((string) $raw) : '';
        }

        return $merged;
    }

    /**
     * @param  array<string, mixed>  $fields
     * @return array<string, mixed>
     */
    public function presentFields(DocumentTemplateKey $template, array $fields): array
    {
        $definitions = $this->fieldDefinitions($template);

        foreach ($definitions as $key => $definition) {
            $type = (string) ($definition['type'] ?? 'text');

            if ($type === 'date') {
                $value = $fields[$key] ?? '';
                if (! is_string($value) || $value === '') {
                    continue;
                }

                try {
                    $fields[$key] = Carbon::parse($value)->format('d M Y');
                } catch (\Throwable) {
                    // Keep raw value.
                }
            }

            if (in_array($type, ['radio', 'select'], true) && isset($definition['options']) && is_string($fields[$key] ?? null)) {
                $fields[$key.'_label'] = $definition['options'][$fields[$key]] ?? $fields[$key];
            }
        }

        return $fields;
    }

    /**
     * @param  array<string, mixed>  $fields
     */
    public function renderHtml(DocumentTemplateKey $template, array $fields): string
    {
        return view($template->view(), [
            'fields' => $this->presentFields($template, $fields),
            'definitions' => $this->fieldDefinitions($template),
            'template' => $template,
        ])->render();
    }

    /**
     * @param  array<string, mixed>  $fields
     */
    public function generatePdf(DocumentTemplateKey $template, array $fields): string
    {
        return Pdf::loadHTML($this->renderHtml($template, $fields))
            ->setPaper('a4')
            ->output();
    }

    /**
     * @param  array<string, mixed>  $fields
     */
    public function generateAndStore(
        Tenant $tenant,
        DocumentTemplateKey $template,
        array $fields,
    ): Document {
        $pdf = $this->generatePdf($template, $fields);
        $originalName = $this->buildFilename($template, $tenant);

        return $this->documentService->storeGenerated($tenant, $pdf, $originalName, 'application/pdf');
    }

    /**
     * @return Collection<int, Tenancy>
     */
    public function tenanciesForTenant(Tenant $tenant): Collection
    {
        return $tenant->tenancies()
            ->with(['property', 'room'])
            ->orderByDesc('started_on')
            ->get();
    }

    public function resolveTenancyContext(Tenant $tenant, ?int $tenancyId = null): array
    {
        $tenancies = $this->tenanciesForTenant($tenant);

        $tenancy = null;
        if ($tenancyId) {
            $tenancy = $tenancies->firstWhere('id', $tenancyId);
        }

        if (! $tenancy) {
            $tenancy = $tenancies->first(fn (Tenancy $item) => $item->ended_on === null) ?? $tenancies->first();
        }

        return [
            'tenancies' => $tenancies,
            'tenancy' => $tenancy,
            'property' => $tenancy?->property,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function buildContext(Tenant $tenant, ?Property $property, ?Tenancy $tenancy): array
    {
        return [
            'property' => $property,
            'tenant' => $tenant,
            'tenancy' => $tenancy,
            'site_name' => (string) SiteSetting::getValue(SiteSettingKey::SiteName->value, ''),
            'site_phone' => (string) SiteSetting::getValue(SiteSettingKey::Phone->value, ''),
            'today' => Carbon::today()->format('Y-m-d'),
        ];
    }

    /**
     * @param  array<string, mixed>  $context
     */
    private function resolveSource(string $source, array $context): string
    {
        /** @var ?Property $property */
        $property = $context['property'];

        return match ($source) {
            'today' => (string) $context['today'],
            'site_name' => (string) $context['site_name'],
            'site_phone' => (string) $context['site_phone'],
            'tenant.full_name' => (string) ($context['tenant']?->full_name ?? ''),
            'tenant.first_name' => (string) ($context['tenant']?->first_name ?? ''),
            'tenant.email' => (string) ($context['tenant']?->email ?? ''),
            'tenant.mobile_number' => (string) ($context['tenant']?->mobile_number ?? ''),
            'property.formatted_address' => $property ? $property->formattedAddress() : '',
            'property.postcode' => (string) ($property?->postcode ?? ''),
            'property.name' => (string) ($property?->name ?? ''),
            'tenancy.started_on' => $context['tenancy']?->started_on
                ? $context['tenancy']->started_on->format('Y-m-d')
                : '',
            default => '',
        };
    }

    private function sourceLabel(?string $source): ?string
    {
        if ($source === null || $source === '') {
            return null;
        }

        return match ($source) {
            'today' => 'Today',
            'site_name', 'site_phone' => 'Site settings',
            'tenant.full_name', 'tenant.first_name', 'tenant.email', 'tenant.mobile_number' => 'Tenant record',
            'property.formatted_address', 'property.postcode', 'property.name' => 'Property record',
            'tenancy.started_on' => 'Tenancy record',
            default => 'System',
        };
    }

    private function buildFilename(DocumentTemplateKey $template, Tenant $tenant): string
    {
        $safe = preg_replace('/[^A-Za-z0-9\-]+/', '-', $tenant->full_name) ?: 'resident';

        return implode('-', [
            $template->filenamePrefix(),
            trim($safe, '-'),
            Carbon::now()->format('Y-m-d-His'),
        ]).'.pdf';
    }
}
