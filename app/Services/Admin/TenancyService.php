<?php

namespace App\Services\Admin;

use App\Enums\RentalMode;
use App\Models\Property;
use App\Models\Room;
use App\Models\Tenancy;
use App\Models\Tenant;
use App\Repositories\Contracts\TenancyRepositoryInterface;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TenancyService
{
    public function __construct(
        private readonly TenancyRepositoryInterface $tenancyRepository,
    ) {}

    public function assignWhole(Property $property, Tenant $tenant, string $startedOn): Tenancy
    {
        if ($property->rental_mode !== RentalMode::Whole) {
            throw ValidationException::withMessages([
                'tenant_id' => 'This property is rented per room. Assign a tenant to a specific room.',
            ]);
        }

        if ($this->tenancyRepository->currentForProperty($property)) {
            throw ValidationException::withMessages([
                'tenant_id' => 'This property already has a current tenant. End that tenancy first.',
            ]);
        }

        $this->assertTenantAvailable($tenant);
        $this->assertTenantDateOk($tenant, $startedOn);

        if ($this->tenancyRepository->propertyHasOverlap($property, $startedOn)) {
            throw ValidationException::withMessages([
                'started_on' => 'The start date overlaps a previous tenancy on this property.',
            ]);
        }

        try {
            return DB::transaction(function () use ($property, $tenant, $startedOn) {
                return $this->tenancyRepository->create([
                    'property_id' => $property->id,
                    'room_id' => null,
                    'tenant_id' => $tenant->id,
                    'started_on' => $startedOn,
                    'ended_on' => null,
                ]);
            });
        } catch (QueryException) {
            throw ValidationException::withMessages([
                'tenant_id' => 'This property already has a current tenant, or this tenant already has a current tenancy.',
            ]);
        }
    }

    public function assignRoom(Property $property, Room $room, Tenant $tenant, string $startedOn): Tenancy
    {
        if ($property->rental_mode !== RentalMode::PerRoom) {
            throw ValidationException::withMessages([
                'room_id' => 'This property is rented as a Whole Property. Assign a tenant to the property, not a room.',
            ]);
        }

        if ((int) $room->property_id !== (int) $property->id) {
            throw ValidationException::withMessages([
                'room_id' => 'The selected room does not belong to this property.',
            ]);
        }

        if ($this->tenancyRepository->currentForRoom($room)) {
            throw ValidationException::withMessages([
                'room_id' => 'This room already has a current tenant. End that tenancy first.',
            ]);
        }

        $this->assertTenantAvailable($tenant);
        $this->assertTenantDateOk($tenant, $startedOn);

        if ($this->tenancyRepository->roomHasOverlap($room, $startedOn)) {
            throw ValidationException::withMessages([
                'started_on' => 'The start date overlaps a previous tenancy on this room.',
            ]);
        }

        try {
            return DB::transaction(function () use ($property, $room, $tenant, $startedOn) {
                return $this->tenancyRepository->create([
                    'property_id' => $property->id,
                    'room_id' => $room->id,
                    'tenant_id' => $tenant->id,
                    'started_on' => $startedOn,
                    'ended_on' => null,
                ]);
            });
        } catch (QueryException) {
            throw ValidationException::withMessages([
                'room_id' => 'This room already has a current tenant, or this tenant already has a current tenancy.',
            ]);
        }
    }

    public function assign(Property $property, Tenant $tenant, string $startedOn, ?Room $room = null): Tenancy
    {
        if ($property->isPerRoom()) {
            if ($room === null) {
                throw ValidationException::withMessages([
                    'room_id' => 'A room is required for per-room properties.',
                ]);
            }

            return $this->assignRoom($property, $room, $tenant, $startedOn);
        }

        if ($room !== null) {
            throw ValidationException::withMessages([
                'room_id' => 'Rooms cannot be assigned on whole-house properties.',
            ]);
        }

        return $this->assignWhole($property, $tenant, $startedOn);
    }

    public function endCurrentWhole(Property $property, string $endedOn): Tenancy
    {
        if ($property->rental_mode !== RentalMode::Whole) {
            throw ValidationException::withMessages([
                'ended_on' => 'End room tenancies individually for per-room properties.',
            ]);
        }

        $tenancy = $this->tenancyRepository->currentForProperty($property);

        if ($tenancy === null) {
            throw ValidationException::withMessages([
                'ended_on' => 'This property does not have a current tenant.',
            ]);
        }

        return $this->endTenancy($tenancy, $endedOn);
    }

    public function endCurrentRoom(Room $room, string $endedOn): Tenancy
    {
        $tenancy = $this->tenancyRepository->currentForRoom($room);

        if ($tenancy === null) {
            throw ValidationException::withMessages([
                'ended_on' => 'This room does not have a current tenant.',
            ]);
        }

        return $this->endTenancy($tenancy, $endedOn);
    }

    public function endCurrent(Property $property, string $endedOn): Tenancy
    {
        return $this->endCurrentWhole($property, $endedOn);
    }

    public function endCurrentForTenant(Tenant $tenant, string $endedOn): Tenancy
    {
        $tenancy = $this->tenancyRepository->currentForTenant($tenant);

        if ($tenancy === null) {
            throw ValidationException::withMessages([
                'ended_on' => 'This tenant does not have a current property.',
            ]);
        }

        return $this->endTenancy($tenancy, $endedOn);
    }

    private function endTenancy(Tenancy $tenancy, string $endedOn): Tenancy
    {
        if ($tenancy->started_on->toDateString() > $endedOn) {
            throw ValidationException::withMessages([
                'ended_on' => 'The end date must be on or after the tenancy start date.',
            ]);
        }

        return $this->tenancyRepository->end($tenancy, $endedOn);
    }

    private function assertTenantAvailable(Tenant $tenant): void
    {
        if ($this->tenancyRepository->currentForTenant($tenant)) {
            throw ValidationException::withMessages([
                'tenant_id' => 'This tenant already has a current tenancy.',
            ]);
        }
    }

    private function assertTenantDateOk(Tenant $tenant, string $startedOn): void
    {
        if ($this->tenancyRepository->tenantHasOverlap($tenant, $startedOn)) {
            throw ValidationException::withMessages([
                'started_on' => 'The start date overlaps a previous tenancy for this tenant.',
            ]);
        }
    }
}
