<?php

namespace App\Services\Admin;

use App\Models\PropertyType;
use App\Repositories\Contracts\PropertyTypeRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class PropertyTypeService
{
    public function __construct(
        private readonly PropertyTypeRepositoryInterface $propertyTypeRepository,
    ) {}

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->propertyTypeRepository->paginate($perPage);
    }

    public function store(array $data): PropertyType
    {
        return $this->propertyTypeRepository->create($this->payload($data));
    }

    public function update(PropertyType $propertyType, array $data): PropertyType
    {
        return $this->propertyTypeRepository->update($propertyType, $this->payload($data, $propertyType->slug));
    }

    public function delete(PropertyType $propertyType): void
    {
        if ($propertyType->properties()->exists()) {
            throw ValidationException::withMessages([
                'property_type' => 'This property type is used by existing properties. Deactivate it instead of deleting.',
            ]);
        }

        $this->propertyTypeRepository->delete($propertyType);
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array{name: string, slug: string, is_active: bool, sort_order: int}
     */
    private function payload(array $data, ?string $existingSlug = null): array
    {
        return [
            'name' => $data['name'],
            'slug' => $existingSlug ?: Str::slug($data['name']),
            'is_active' => (bool) ($data['is_active'] ?? true),
            'sort_order' => (int) ($data['sort_order'] ?? 0),
        ];
    }
}
