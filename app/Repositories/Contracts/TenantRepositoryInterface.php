<?php

namespace App\Repositories\Contracts;

use App\Models\Tenant;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface TenantRepositoryInterface
{
    public function paginateFiltered(?string $column, ?string $search, ?string $status, int $perPage = 15): LengthAwarePaginator;

    public function listAssignable(?int $includeId = null): Collection;

    public function create(array $data): Tenant;

    public function update(Tenant $tenant, array $data): Tenant;

    public function delete(Tenant $tenant): void;
}
