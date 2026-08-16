<?php

namespace App\Repositories\Contracts;

use App\Models\Property;
use App\Models\Tenancy;
use App\Models\Tenant;

interface TenancyRepositoryInterface
{
    public function create(array $data): Tenancy;

    public function end(Tenancy $tenancy, string $endedOn): Tenancy;

    public function currentForProperty(Property $property): ?Tenancy;

    public function currentForTenant(Tenant $tenant): ?Tenancy;

    public function hasAnyForTenant(Tenant $tenant): bool;

    public function hasAnyForProperty(Property $property): bool;

    public function propertyHasOverlap(Property $property, string $startedOn): bool;

    public function tenantHasOverlap(Tenant $tenant, string $startedOn): bool;
}
