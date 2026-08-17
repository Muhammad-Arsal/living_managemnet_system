<?php

namespace App\Repositories;

use App\Models\Tenant;
use App\Repositories\Contracts\TenantRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class TenantRepository implements TenantRepositoryInterface
{
    public function paginateFiltered(?string $column, ?string $search, ?string $status, int $perPage = 15): LengthAwarePaginator
    {
        $allowed = ['first_name', 'last_name', 'email', 'postcode'];

        return Tenant::query()
            ->with(['currentTenancy.property'])
            ->when(
                $search !== null && $search !== '' && $column !== null && in_array($column, $allowed, true),
                function ($query) use ($column, $search) {
                    if ($column === 'postcode') {
                        return $query->whereHas('currentTenancy.property', function ($propertyQuery) use ($search) {
                            $propertyQuery->where('postcode', 'like', '%'.$search.'%');
                        });
                    }

                    return $query->where($column, 'like', '%'.$search.'%');
                }
            )
            ->when($status === 'current', fn ($query) => $query->current())
            ->when($status === 'past', fn ($query) => $query->past())
            ->latest()
            ->paginate($perPage)
            ->withQueryString();
    }

    public function listAssignable(?int $includeId = null): Collection
    {
        return Tenant::query()
            ->where(function ($query) use ($includeId) {
                $query->whereDoesntHave('currentTenancy');

                if ($includeId !== null) {
                    $query->orWhere('id', $includeId);
                }
            })
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();
    }

    public function create(array $data): Tenant
    {
        return Tenant::query()->create($data);
    }

    public function update(Tenant $tenant, array $data): Tenant
    {
        $tenant->update($data);

        return $tenant->refresh();
    }

    public function delete(Tenant $tenant): void
    {
        $tenant->delete();
    }
}
