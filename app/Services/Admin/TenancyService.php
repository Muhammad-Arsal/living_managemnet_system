<?php

namespace App\Services\Admin;

use App\Models\Property;
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

    public function assign(Property $property, Tenant $tenant, string $startedOn): Tenancy
    {
        if ($this->tenancyRepository->currentForProperty($property)) {
            throw ValidationException::withMessages([
                'tenant_id' => 'This property already has a current tenant. End that tenancy first.',
            ]);
        }

        if ($this->tenancyRepository->currentForTenant($tenant)) {
            throw ValidationException::withMessages([
                'tenant_id' => 'This tenant already has a current property.',
            ]);
        }

        if ($this->tenancyRepository->propertyHasOverlap($property, $startedOn)) {
            throw ValidationException::withMessages([
                'started_on' => 'The start date overlaps a previous tenancy on this property.',
            ]);
        }

        if ($this->tenancyRepository->tenantHasOverlap($tenant, $startedOn)) {
            throw ValidationException::withMessages([
                'started_on' => 'The start date overlaps a previous tenancy for this tenant.',
            ]);
        }

        try {
            return DB::transaction(function () use ($property, $tenant, $startedOn) {
                return $this->tenancyRepository->create([
                    'property_id' => $property->id,
                    'tenant_id' => $tenant->id,
                    'started_on' => $startedOn,
                    'ended_on' => null,
                ]);
            });
        } catch (QueryException) {
            throw ValidationException::withMessages([
                'tenant_id' => 'This property already has a current tenant, or this tenant already has a current property.',
            ]);
        }
    }

    public function endCurrent(Property $property, string $endedOn): Tenancy
    {
        $tenancy = $this->tenancyRepository->currentForProperty($property);

        if ($tenancy === null) {
            throw ValidationException::withMessages([
                'ended_on' => 'This property does not have a current tenant.',
            ]);
        }

        if ($tenancy->started_on->toDateString() > $endedOn) {
            throw ValidationException::withMessages([
                'ended_on' => 'The end date must be on or after the tenancy start date.',
            ]);
        }

        return $this->tenancyRepository->end($tenancy, $endedOn);
    }

    public function endCurrentForTenant(Tenant $tenant, string $endedOn): Tenancy
    {
        $tenancy = $this->tenancyRepository->currentForTenant($tenant);

        if ($tenancy === null) {
            throw ValidationException::withMessages([
                'ended_on' => 'This tenant does not have a current property.',
            ]);
        }

        return $this->endCurrent($tenancy->property, $endedOn);
    }
}
