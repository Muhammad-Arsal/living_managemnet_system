<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Enums\DocumentTemplateKey;
use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\Admin\Tenant\GenerateDocumentRequest;
use App\Models\Tenant;
use App\Services\Admin\DocumentGenerationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class TenantDocumentGenerationController extends Controller
{
    public function __construct(
        private readonly DocumentGenerationService $documentGenerationService,
    ) {}

    public function create(Request $request, Tenant $tenant): View|Response
    {
        $templates = $this->documentGenerationService->availableTemplates();
        $selectedTemplate = DocumentTemplateKey::tryFrom((string) $request->query('template', ''));

        $context = $this->documentGenerationService->resolveTenancyContext(
            $tenant,
            $request->integer('tenancy_id') ?: null
        );

        $resolved = $selectedTemplate
            ? $this->documentGenerationService->resolveFields(
                $selectedTemplate,
                $tenant,
                $context['property'],
                $context['tenancy']
            )
            : ['values' => [], 'meta' => []];

        $fieldValues = old('fields', $resolved['values']);

        $data = [
            'tenant' => $tenant,
            'templates' => $templates,
            'selectedTemplate' => $selectedTemplate,
            'tenancies' => $context['tenancies'],
            'selectedTenancy' => $context['tenancy'],
            'property' => $context['property'],
            'fieldValues' => $fieldValues,
            'fieldMeta' => $resolved['meta'],
        ];

        if ($request->boolean('partial')) {
            return response()
                ->view('backend::admin.tenants.documents.partials.generate-workspace', $data)
                ->header('Cache-Control', 'no-store');
        }

        return view('backend::admin.tenants.documents.generate', $data);
    }

    public function preview(GenerateDocumentRequest $request, Tenant $tenant): Response
    {
        $template = DocumentTemplateKey::from($request->validated('template'));
        $fields = $template->isStatic()
            ? []
            : $this->documentGenerationService->mergeFieldValues(
                $template,
                $request->validated('fields') ?? []
            );

        $html = $this->documentGenerationService->renderHtml($template, $fields);

        return response($html, 200, [
            'Content-Type' => 'text/html; charset=UTF-8',
            'Cache-Control' => 'no-store',
        ]);
    }

    public function store(GenerateDocumentRequest $request, Tenant $tenant): RedirectResponse
    {
        $template = DocumentTemplateKey::from($request->validated('template'));
        $fields = $template->isStatic()
            ? []
            : $this->documentGenerationService->mergeFieldValues(
                $template,
                $request->validated('fields') ?? []
            );

        try {
            $document = $this->documentGenerationService->generateAndStore(
                $tenant,
                $template,
                $fields
            );
        } catch (\Throwable $exception) {
            report($exception);

            return back()
                ->withInput()
                ->with('error', 'Document generation failed. Please review the fields and try again.');
        }

        return redirect()
            ->route('admin.tenants.edit', ['tenant' => $tenant, 'tab' => 'documents'])
            ->with('success', $document->original_name.' was generated and saved to this tenant.');
    }
}
