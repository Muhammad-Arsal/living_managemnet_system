<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\Admin\Property\EndTenancyRequest;
use App\Http\Requests\Backend\Admin\Tenant\AssignPropertyRequest;
use App\Http\Requests\Backend\Admin\Tenant\StoreTenantDocumentsRequest;
use App\Http\Requests\Backend\Admin\Tenant\StoreTenantRequest;
use App\Http\Requests\Backend\Admin\Tenant\UpdateTenantRequest;
use App\Models\Document;
use App\Models\Property;
use App\Models\Tenant;
use App\Repositories\Contracts\PropertyRepositoryInterface;
use App\Repositories\Contracts\TenantRepositoryInterface;
use App\Services\Admin\DocumentService;
use App\Services\Admin\TenancyService;
use App\Services\Admin\TenantService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TenantController extends Controller
{
    public function __construct(
        private readonly TenantService $tenantService,
        private readonly TenancyService $tenancyService,
        private readonly DocumentService $documentService,
        private readonly TenantRepositoryInterface $tenantRepository,
        private readonly PropertyRepositoryInterface $propertyRepository,
    ) {}

    public function index(Request $request): View
    {
        $filterColumns = [
            'first_name' => 'First name',
            'last_name' => 'Last name',
            'email' => 'Email',
            'postcode' => 'Postcode',
        ];

        $tenants = $this->tenantRepository->paginateFiltered(
            $request->string('column')->toString() ?: null,
            $request->string('search')->trim()->toString() ?: null,
            $request->string('status')->toString() ?: null,
        );

        return view('backend::admin.tenants.index', compact('tenants', 'filterColumns'));
    }

    public function create(): View
    {
        return view('backend::admin.tenants.create');
    }

    public function store(StoreTenantRequest $request): RedirectResponse
    {
        $this->tenantService->store($request->validated());

        return redirect()
            ->route('admin.tenants.index')
            ->with('success', 'Tenant created successfully.');
    }

    public function edit(Tenant $tenant): View
    {
        $tenant->load(['tenancies.property.propertyType', 'currentTenancy.property', 'documents']);
        $vacantProperties = $this->propertyRepository->listVacant();

        return view('backend::admin.tenants.edit', compact('tenant', 'vacantProperties'));
    }

    public function update(UpdateTenantRequest $request, Tenant $tenant): RedirectResponse
    {
        $this->tenantService->update($tenant, $request->validated());

        return redirect()
            ->route('admin.tenants.edit', ['tenant' => $tenant, 'tab' => 'overview'])
            ->with('success', 'Tenant updated successfully.');
    }

    public function destroy(Tenant $tenant): RedirectResponse
    {
        try {
            $this->tenantService->delete($tenant);
        } catch (ValidationException $exception) {
            return back()->with('error', $exception->validator->errors()->first());
        }

        return redirect()
            ->route('admin.tenants.index')
            ->with('success', 'Tenant deleted successfully.');
    }

    public function storeDocuments(StoreTenantDocumentsRequest $request, Tenant $tenant): RedirectResponse
    {
        $this->tenantService->storeDocuments($tenant, $request->validated('documents') ?? []);

        return redirect()
            ->route('admin.tenants.edit', ['tenant' => $tenant, 'tab' => 'documents'])
            ->with('success', 'Documents uploaded successfully.');
    }

    public function downloadDocument(Tenant $tenant, Document $document): StreamedResponse
    {
        return $this->documentService->download($tenant, $document);
    }

    public function destroyDocument(Tenant $tenant, Document $document): RedirectResponse
    {
        $this->tenantService->deleteDocument($tenant, $document);

        return redirect()
            ->route('admin.tenants.edit', ['tenant' => $tenant, 'tab' => 'documents'])
            ->with('success', 'Document removed.');
    }

    public function storeTenancy(AssignPropertyRequest $request, Tenant $tenant): RedirectResponse
    {
        $property = Property::query()->findOrFail($request->validated('property_id'));

        try {
            $this->tenancyService->assign($property, $tenant, $request->validated('started_on'));
        } catch (ValidationException $exception) {
            return back()
                ->withInput()
                ->withErrors($exception->errors())
                ->with('error', $exception->validator->errors()->first());
        }

        return redirect()
            ->route('admin.tenants.edit', ['tenant' => $tenant, 'tab' => 'current'])
            ->with('success', 'Property assigned successfully.');
    }

    public function endTenancy(EndTenancyRequest $request, Tenant $tenant): RedirectResponse
    {
        try {
            $this->tenancyService->endCurrentForTenant($tenant, $request->validated('ended_on'));
        } catch (ValidationException $exception) {
            return back()
                ->withInput()
                ->withErrors($exception->errors())
                ->with('error', $exception->validator->errors()->first());
        }

        return redirect()
            ->route('admin.tenants.edit', ['tenant' => $tenant, 'tab' => 'current'])
            ->with('success', 'Tenancy ended successfully.');
    }
}
