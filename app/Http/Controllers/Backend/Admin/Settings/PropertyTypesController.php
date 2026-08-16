<?php

namespace App\Http\Controllers\Backend\Admin\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\Admin\Settings\StorePropertyTypeRequest;
use App\Http\Requests\Backend\Admin\Settings\UpdatePropertyTypeRequest;
use App\Models\PropertyType;
use App\Services\Admin\PropertyTypeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PropertyTypesController extends Controller
{
    public function __construct(
        private readonly PropertyTypeService $propertyTypeService,
    ) {}

    public function index(): View
    {
        $propertyTypes = $this->propertyTypeService->paginate();

        return view('backend::admin.settings.property-types.index', compact('propertyTypes'));
    }

    public function create(): View
    {
        return view('backend::admin.settings.property-types.create');
    }

    public function store(StorePropertyTypeRequest $request): RedirectResponse
    {
        $this->propertyTypeService->store($request->validated());

        return redirect()
            ->route('admin.settings.property-types.index')
            ->with('success', 'Property type created successfully.');
    }

    public function edit(PropertyType $propertyType): View
    {
        return view('backend::admin.settings.property-types.edit', compact('propertyType'));
    }

    public function update(UpdatePropertyTypeRequest $request, PropertyType $propertyType): RedirectResponse
    {
        $this->propertyTypeService->update($propertyType, $request->validated());

        return redirect()
            ->route('admin.settings.property-types.index')
            ->with('success', 'Property type updated successfully.');
    }

    public function destroy(PropertyType $propertyType): RedirectResponse
    {
        try {
            $this->propertyTypeService->delete($propertyType);
        } catch (ValidationException $exception) {
            return back()->with('error', $exception->validator->errors()->first());
        }

        return redirect()
            ->route('admin.settings.property-types.index')
            ->with('success', 'Property type deleted successfully.');
    }
}
