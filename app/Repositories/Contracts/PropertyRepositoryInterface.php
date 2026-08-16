<?php

namespace App\Repositories\Contracts;

use App\Models\Property;
use App\Models\PropertyImage;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface PropertyRepositoryInterface
{
    public function paginateFiltered(?string $column, ?string $search, ?string $occupancy, ?int $propertyTypeId, int $perPage = 15): LengthAwarePaginator;

    public function listVacant(): Collection;

    public function create(array $data): Property;

    public function update(Property $property, array $data): Property;

    public function delete(Property $property): void;

    public function addImage(Property $property, array $data): PropertyImage;

    public function nextImageSortOrder(Property $property): int;

    public function deleteImage(PropertyImage $image): void;
}
