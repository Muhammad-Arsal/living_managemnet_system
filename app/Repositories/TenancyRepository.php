<?php

namespace App\Repositories;

use App\Models\Property;
use App\Models\Tenancy;
use App\Models\Tenant;
use App\Repositories\Contracts\TenancyRepositoryInterface;

class TenancyRepository implements TenancyRepositoryInterface
{
    public function create(array $data): Tenancy
    {
        return Tenancy::query()->create($data);
    }

    public function end(Tenancy $tenancy, string $endedOn): Tenancy
    {
        $tenancy->update(['ended_on' => $endedOn]);

        return $tenancy->refresh();
    }

    public function currentForProperty(Property $property): ?Tenancy
    {
        return $property->currentTenancy()->first();
    }

    public function currentForTenant(Tenant $tenant): ?Tenancy
    {
        return $tenant->currentTenancy()->first();
    }

    public function hasAnyForTenant(Tenant $tenant): bool
    {
        return $tenant->tenancies()->exists();
    }

    public function hasAnyForProperty(Property $property): bool
    {
        return $property->tenancies()->exists();
    }

    public function propertyHasOverlap(Property $property, string $startedOn): bool
    {
        return Tenancy::query()
            ->where('property_id', $property->id)
            ->whereNotNull('ended_on')
            ->where('ended_on', '>=', $startedOn)
            ->exists();
    }

    public function tenantHasOverlap(Tenant $tenant, string $startedOn): bool
    {
        return Tenancy::query()
            ->where('tenant_id', $tenant->id)
            ->whereNotNull('ended_on')
            ->where('ended_on', '>=', $startedOn)
            ->exists();
    }
}
