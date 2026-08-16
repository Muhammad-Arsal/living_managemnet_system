<?php

namespace App\Repositories\Contracts;

use App\Models\PropertyType;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface PropertyTypeRepositoryInterface
{
    public function paginate(int $perPage = 15): LengthAwarePaginator;

    public function listActive(): Collection;

    /**
     * @return Collection<int, PropertyType>
     */
    public function listForPropertyForm(?int $includeId = null): Collection;

    public function create(array $data): PropertyType;

    public function update(PropertyType $propertyType, array $data): PropertyType;

    public function delete(PropertyType $propertyType): void;
}
