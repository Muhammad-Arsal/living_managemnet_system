<?php

namespace App\Repositories;

use App\Models\PropertyType;
use App\Repositories\Contracts\PropertyTypeRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class PropertyTypeRepository implements PropertyTypeRepositoryInterface
{
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return PropertyType::query()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate($perPage);
    }

    public function listActive(): Collection
    {
        return PropertyType::query()
            ->active()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
    }

    public function listForPropertyForm(?int $includeId = null): Collection
    {
        return PropertyType::query()
            ->where(function ($query) use ($includeId) {
                $query->where('is_active', true);

                if ($includeId !== null) {
                    $query->orWhere('id', $includeId);
                }
            })
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
    }

    public function create(array $data): PropertyType
    {
        return PropertyType::query()->create($data);
    }

    public function update(PropertyType $propertyType, array $data): PropertyType
    {
        $propertyType->update($data);

        return $propertyType->refresh();
    }

    public function delete(PropertyType $propertyType): void
    {
        $propertyType->delete();
    }
}
