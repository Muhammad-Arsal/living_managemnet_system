<?php

namespace App\Repositories;

use App\Models\Property;
use App\Models\PropertyImage;
use App\Repositories\Contracts\PropertyRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class PropertyRepository implements PropertyRepositoryInterface
{
    public function paginateFiltered(?string $column, ?string $search, ?string $occupancy, ?int $propertyTypeId, int $perPage = 15): LengthAwarePaginator
    {
        $allowed = ['name', 'postcode', 'city'];

        return Property::query()
            ->with(['propertyType', 'coverImage', 'currentTenancy.tenant'])
            ->when(
                $search !== null && $search !== '' && $column !== null && in_array($column, $allowed, true),
                fn ($query) => $query->where($column, 'like', '%'.$search.'%')
            )
            ->when($occupancy === 'occupied', fn ($query) => $query->occupied())
            ->when($occupancy === 'vacant', fn ($query) => $query->vacant())
            ->when($propertyTypeId, fn ($query) => $query->where('property_type_id', $propertyTypeId))
            ->latest()
            ->paginate($perPage)
            ->withQueryString();
    }

    public function listVacant(): Collection
    {
        return Property::query()
            ->vacant()
            ->orderBy('name')
            ->get();
    }

    public function create(array $data): Property
    {
        return Property::query()->create($data);
    }

    public function update(Property $property, array $data): Property
    {
        $property->update($data);

        return $property->refresh();
    }

    public function delete(Property $property): void
    {
        $property->delete();
    }

    public function addImage(Property $property, array $data): PropertyImage
    {
        return $property->images()->create($data);
    }

    public function nextImageSortOrder(Property $property): int
    {
        return (int) $property->images()->max('sort_order') + 1;
    }

    public function deleteImage(PropertyImage $image): void
    {
        $image->delete();
    }
}
