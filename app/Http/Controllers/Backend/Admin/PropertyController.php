<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\Admin\Property\AssignTenancyRequest;
use App\Http\Requests\Backend\Admin\Property\EndTenancyRequest;
use App\Http\Requests\Backend\Admin\Property\StorePropertyRequest;
use App\Http\Requests\Backend\Admin\Property\UpdatePropertyRequest;
use App\Models\Property;
use App\Models\PropertyImage;
use App\Models\Tenant;
use App\Repositories\Contracts\PropertyRepositoryInterface;
use App\Repositories\Contracts\PropertyTypeRepositoryInterface;
use App\Repositories\Contracts\TenantRepositoryInterface;
use App\Services\Admin\PropertyService;
use App\Services\Admin\TenancyService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PropertyController extends Controller
{
    public function __construct(
        private readonly PropertyService $propertyService,
        private readonly TenancyService $tenancyService,
        private readonly PropertyRepositoryInterface $propertyRepository,
        private readonly PropertyTypeRepositoryInterface $propertyTypeRepository,
        private readonly TenantRepositoryInterface $tenantRepository,
    ) {}

    public function index(Request $request): View
    {
        $filterColumns = [
            'name' => 'Name',
            'postcode' => 'Postcode',
            'city' => 'City / town',
        ];

        $propertyTypes = $this->propertyTypeRepository->listActive();
        $properties = $this->propertyRepository->paginateFiltered(
            $request->string('column')->toString() ?: null,
            $request->string('search')->trim()->toString() ?: null,
            $request->string('occupancy')->toString() ?: null,
            $request->integer('property_type_id') ?: null,
        );

        return view('backend::admin.properties.index', compact('properties', 'filterColumns', 'propertyTypes'));
    }

    public function create(): View
    {
        $propertyTypes = $this->propertyTypeRepository->listActive();

        return view('backend::admin.properties.create', compact('propertyTypes'));
    }

    public function store(StorePropertyRequest $request): RedirectResponse
    {
        $this->propertyService->store($request->validated());

        return redirect()
            ->route('admin.properties.index')
            ->with('success', 'Property created successfully.');
    }

    public function edit(Property $property): View
    {
        $property->load([
            'propertyType',
            'images',
            'currentTenancy.tenant',
            'tenancies.tenant',
        ]);

        $propertyTypes = $this->propertyTypeRepository->listForPropertyForm($property->property_type_id);
        $assignableTenants = $this->tenantRepository->listAssignable();

        return view('backend::admin.properties.edit', compact('property', 'propertyTypes', 'assignableTenants'));
    }

    public function update(UpdatePropertyRequest $request, Property $property): RedirectResponse
    {
        $this->propertyService->update($property, $request->validated());

        return redirect()
            ->route('admin.properties.edit', ['property' => $property, 'tab' => 'overview'])
            ->with('success', 'Property updated successfully.');
    }

    public function destroy(Property $property): RedirectResponse
    {
        try {
            $this->propertyService->delete($property);
        } catch (ValidationException $exception) {
            return back()->with('error', $exception->validator->errors()->first());
        }

        return redirect()
            ->route('admin.properties.index')
            ->with('success', 'Property deleted successfully.');
    }

    public function destroyImage(Property $property, PropertyImage $propertyImage): RedirectResponse
    {
        abort_unless($propertyImage->property_id === $property->id, 404);

        $this->propertyService->deleteImage($property, $propertyImage);

        return redirect()
            ->route('admin.properties.edit', ['property' => $property, 'tab' => 'overview'])
            ->with('success', 'Image removed.');
    }

    public function storeTenancy(AssignTenancyRequest $request, Property $property): RedirectResponse
    {
        $tenant = Tenant::query()->findOrFail($request->validated('tenant_id'));

        try {
            $this->tenancyService->assign($property, $tenant, $request->validated('started_on'));
        } catch (ValidationException $exception) {
            return back()
                ->withInput()
                ->withErrors($exception->errors())
                ->with('error', $exception->validator->errors()->first());
        }

        return redirect()
            ->route('admin.properties.edit', ['property' => $property, 'tab' => 'current'])
            ->with('success', 'Tenant assigned successfully.');
    }

    public function endTenancy(EndTenancyRequest $request, Property $property): RedirectResponse
    {
        try {
            $this->tenancyService->endCurrent($property, $request->validated('ended_on'));
        } catch (ValidationException $exception) {
            return back()
                ->withInput()
                ->withErrors($exception->errors())
                ->with('error', $exception->validator->errors()->first());
        }

        return redirect()
            ->route('admin.properties.edit', ['property' => $property, 'tab' => 'current'])
            ->with('success', 'Tenancy ended successfully.');
    }
}
