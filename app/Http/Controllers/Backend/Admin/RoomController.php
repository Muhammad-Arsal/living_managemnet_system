<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\Admin\Property\AssignRoomTenancyRequest;
use App\Http\Requests\Backend\Admin\Property\EndTenancyRequest;
use App\Http\Requests\Backend\Admin\Property\StoreRoomRequest;
use App\Http\Requests\Backend\Admin\Property\UpdateRoomRequest;
use App\Models\Property;
use App\Models\Room;
use App\Models\Tenant;
use App\Services\Admin\RoomService;
use App\Services\Admin\TenancyService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\ValidationException;

class RoomController extends Controller
{
    public function __construct(
        private readonly RoomService $roomService,
        private readonly TenancyService $tenancyService,
    ) {}

    public function store(StoreRoomRequest $request, Property $property): RedirectResponse
    {
        try {
            $this->roomService->store($property, $request->validated());
        } catch (ValidationException $exception) {
            return back()
                ->withInput()
                ->withErrors($exception->errors())
                ->with('error', $exception->validator->errors()->first());
        }

        return redirect()
            ->route('admin.properties.edit', ['property' => $property, 'tab' => 'rooms'])
            ->with('success', 'Room added successfully.');
    }

    public function update(UpdateRoomRequest $request, Property $property, Room $room): RedirectResponse
    {
        try {
            $this->roomService->update($property, $room, $request->validated());
        } catch (ValidationException $exception) {
            return back()
                ->withInput()
                ->withErrors($exception->errors())
                ->with('error', $exception->validator->errors()->first());
        }

        return redirect()
            ->route('admin.properties.edit', ['property' => $property, 'tab' => 'rooms'])
            ->with('success', 'Room updated successfully.');
    }

    public function destroy(Property $property, Room $room): RedirectResponse
    {
        try {
            $this->roomService->delete($property, $room);
        } catch (ValidationException $exception) {
            return back()->with('error', $exception->validator->errors()->first());
        }

        return redirect()
            ->route('admin.properties.edit', ['property' => $property, 'tab' => 'rooms'])
            ->with('success', 'Room deleted successfully.');
    }

    public function storeTenancy(AssignRoomTenancyRequest $request, Property $property, Room $room): RedirectResponse
    {
        abort_unless((int) $room->property_id === (int) $property->id, 404);

        $tenant = Tenant::query()->findOrFail($request->validated('tenant_id'));

        try {
            $this->tenancyService->assignRoom($property, $room, $tenant, $request->validated('started_on'));
        } catch (ValidationException $exception) {
            return back()
                ->withInput()
                ->withErrors($exception->errors())
                ->with('error', $exception->validator->errors()->first());
        }

        return redirect()
            ->route('admin.properties.edit', ['property' => $property, 'tab' => 'rooms'])
            ->with('success', 'Tenant assigned to room successfully.');
    }

    public function endTenancy(EndTenancyRequest $request, Property $property, Room $room): RedirectResponse
    {
        abort_unless((int) $room->property_id === (int) $property->id, 404);

        try {
            $this->tenancyService->endCurrentRoom($room, $request->validated('ended_on'));
        } catch (ValidationException $exception) {
            return back()
                ->withInput()
                ->withErrors($exception->errors())
                ->with('error', $exception->validator->errors()->first());
        }

        return redirect()
            ->route('admin.properties.edit', ['property' => $property, 'tab' => 'rooms'])
            ->with('success', 'Room tenancy ended successfully.');
    }
}
