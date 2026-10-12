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
     * @return array<string, array{label: string, type: string, source: ?string, required: bool, default?: string}>
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
     * Resolve auto-fill values and field status for the generate form.
     *
     * @return array{values: array<string, string>, meta: array<string, array{status: string, source_label: ?string, required: bool, label: string, type: string}>}
     */
    public function resolveFields(DocumentTemplateKey $template, Property $property, ?Tenant $tenant, ?Tenancy $tenancy = null): array
    {
        $definitions = $this->fieldDefinitions($template);
        $context = $this->buildContext($property, $tenant, $tenancy);

        $values = [];
        $meta = [];

        foreach ($definitions as $key => $definition) {
            $source = $definition['source'] ?? null;
            $default = (string) ($definition['default'] ?? '');
            $resolved = $source ? $this->resolveSource($source, $context) : '';
            $value = $resolved !== '' ? $resolved : $default;
            $required = (bool) ($definition['required'] ?? false);

            if ($value !== '') {
                $status = $source ? 'prefilled' : 'editable';
            } else {
                $status = $required ? 'missing' : 'editable';
            }

            $values[$key] = $value;
            $meta[$key] = [
                'label' => (string) $definition['label'],
                'type' => (string) ($definition['type'] ?? 'text'),
                'required' => $required,
                'status' => $status,
                'source_label' => $this->sourceLabel($source),
            ];
        }

        return compact('values', 'meta');
    }

    /**
     * @param  array<string, mixed>  $input
     * @return array<string, string>
     */
    public function mergeFieldValues(DocumentTemplateKey $template, array $input): array
    {
        $definitions = $this->fieldDefinitions($template);
        $merged = [];

        foreach ($definitions as $key => $definition) {
            $raw = $input[$key] ?? ($definition['default'] ?? '');
            $merged[$key] = is_scalar($raw) ? trim((string) $raw) : '';
        }

        return $merged;
    }

    /**
     * Format date fields for human-readable document output.
     *
     * @param  array<string, string>  $fields
     * @return array<string, string>
     */
    public function presentFields(DocumentTemplateKey $template, array $fields): array
    {
        $definitions = $this->fieldDefinitions($template);

        foreach ($definitions as $key => $definition) {
            if (($definition['type'] ?? '') !== 'date') {
                continue;
            }

            $value = $fields[$key] ?? '';
            if ($value === '') {
                continue;
            }

            try {
                $fields[$key] = Carbon::parse($value)->format('d M Y');
            } catch (\Throwable) {
                // Keep the raw value if it is not a parseable date.
            }
        }

        return $fields;
    }

    /**
     * @param  array<string, string>  $fields
     */
    public function renderHtml(DocumentTemplateKey $template, array $fields): string
    {
        return view($template->view(), [
            'fields' => $this->presentFields($template, $fields),
            'template' => $template,
        ])->render();
    }

    /**
     * @param  array<string, string>  $fields
     */
    public function generatePdf(DocumentTemplateKey $template, array $fields): string
    {
        return Pdf::loadHTML($this->renderHtml($template, $fields))
            ->setPaper('a4')
            ->output();
    }

    /**
     * @param  array<string, string>  $fields
     */
    public function generateAndStore(
        Property $property,
        DocumentTemplateKey $template,
        array $fields,
        ?Tenant $tenant = null,
    ): Document {
        $pdf = $this->generatePdf($template, $fields);
        $originalName = $this->buildFilename($template, $tenant);

        return $this->documentService->storeGenerated($property, $pdf, $originalName, 'application/pdf');
    }

    /**
     * Current tenants on the property (whole or per-room) for the resident picker.
     *
     * @return Collection<int, Tenant>
     */
    public function currentTenantsForProperty(Property $property): Collection
    {
        $property->loadMissing([
            'currentTenancy.tenant',
            'rooms.currentTenancy.tenant',
            'currentTenancies.tenant',
        ]);

        return $property->currentTenancies
            ->map(fn (Tenancy $tenancy) => $tenancy->tenant)
            ->filter()
            ->unique('id')
            ->values();
    }

    public function findTenancyForTenant(Property $property, Tenant $tenant): ?Tenancy
    {
        return $property->currentTenancies()
            ->where('tenant_id', $tenant->id)
            ->with('room')
            ->first();
    }

    /**
     * @return array<string, mixed>
     */
    private function buildContext(Property $property, ?Tenant $tenant, ?Tenancy $tenancy): array
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
        return match ($source) {
            'today' => (string) $context['today'],
            'site_name' => (string) $context['site_name'],
            'site_phone' => (string) $context['site_phone'],
            'tenant.full_name' => (string) ($context['tenant']?->full_name ?? ''),
            'tenant.first_name' => (string) ($context['tenant']?->first_name ?? ''),
            'tenant.email' => (string) ($context['tenant']?->email ?? ''),
            'tenant.mobile_number' => (string) ($context['tenant']?->mobile_number ?? ''),
            'property.formatted_address' => (string) $context['property']->formattedAddress(),
            'property.postcode' => (string) ($context['property']->postcode ?? ''),
            'property.name' => (string) ($context['property']->name ?? ''),
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
            'site_name' => 'Site settings',
            'site_phone' => 'Site settings',
            'tenant.full_name', 'tenant.first_name', 'tenant.email', 'tenant.mobile_number' => 'Tenant record',
            'property.formatted_address', 'property.postcode', 'property.name' => 'Property record',
            'tenancy.started_on' => 'Tenancy record',
            default => 'System',
        };
    }

    private function buildFilename(DocumentTemplateKey $template, ?Tenant $tenant): string
    {
        $parts = [$template->filenamePrefix()];

        if ($tenant) {
            $safe = preg_replace('/[^A-Za-z0-9\-]+/', '-', $tenant->full_name) ?: 'resident';
            $parts[] = trim($safe, '-');
        }

        $parts[] = Carbon::now()->format('Y-m-d-His');

        return implode('-', $parts).'.pdf';
    }
}
