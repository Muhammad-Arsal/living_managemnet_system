<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Enums\DocumentTemplateKey;
use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\Admin\Property\GenerateDocumentRequest;
use App\Models\Property;
use App\Models\Tenant;
use App\Repositories\Contracts\TenantRepositoryInterface;
use App\Services\Admin\DocumentGenerationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class PropertyDocumentGenerationController extends Controller
{
    public function __construct(
        private readonly DocumentGenerationService $documentGenerationService,
        private readonly TenantRepositoryInterface $tenantRepository,
    ) {}

    public function create(Request $request, Property $property): View|Response
    {
        $templates = $this->documentGenerationService->availableTemplates();
        $selectedTemplate = DocumentTemplateKey::tryFrom((string) $request->query('template', ''));

        $currentTenants = $this->documentGenerationService->currentTenantsForProperty($property);
        $allTenants = $this->tenantRepository->listForSelect();

        $tenantId = $request->integer('tenant_id') ?: null;
        $tenant = $tenantId
            ? Tenant::query()->find($tenantId)
            : ($currentTenants->count() === 1 ? $currentTenants->first() : null);

        $tenancy = $tenant
            ? $this->documentGenerationService->findTenancyForTenant($property, $tenant)
            : null;

        $resolved = $selectedTemplate
            ? $this->documentGenerationService->resolveFields($selectedTemplate, $property, $tenant, $tenancy)
            : ['values' => [], 'meta' => []];

        $fieldValues = old('fields', $resolved['values']);

        $data = [
            'property' => $property,
            'templates' => $templates,
            'selectedTemplate' => $selectedTemplate,
            'currentTenants' => $currentTenants,
            'allTenants' => $allTenants,
            'selectedTenant' => $tenant,
            'fieldValues' => $fieldValues,
            'fieldMeta' => $resolved['meta'],
        ];

        if ($request->boolean('partial')) {
            return response()
                ->view('backend::admin.properties.documents.partials.generate-workspace', $data)
                ->header('Cache-Control', 'no-store');
        }

        return view('backend::admin.properties.documents.generate', $data);
    }

    public function preview(GenerateDocumentRequest $request, Property $property): Response
    {
        $template = DocumentTemplateKey::from($request->validated('template'));
        $fields = $this->documentGenerationService->mergeFieldValues(
            $template,
            $request->validated('fields')
        );

        $html = $this->documentGenerationService->renderHtml($template, $fields);

        return response($html, 200, [
            'Content-Type' => 'text/html; charset=UTF-8',
            'Cache-Control' => 'no-store',
        ]);
    }

    public function store(GenerateDocumentRequest $request, Property $property): RedirectResponse
    {
        $template = DocumentTemplateKey::from($request->validated('template'));
        $tenant = Tenant::query()->findOrFail($request->validated('tenant_id'));
        $fields = $this->documentGenerationService->mergeFieldValues(
            $template,
            $request->validated('fields')
        );

        try {
            $document = $this->documentGenerationService->generateAndStore(
                $property,
                $template,
                $fields,
                $tenant
            );
        } catch (\Throwable $exception) {
            report($exception);

            return back()
                ->withInput()
                ->with('error', 'Document generation failed. Please review the fields and try again.');
        }

        return redirect()
            ->route('admin.properties.edit', ['property' => $property, 'tab' => 'documents'])
            ->with('success', $document->original_name.' was generated and saved to this property.');
    }
}
