<?php

namespace App\Services\Admin;

use App\Models\Property;
use App\Models\PropertyImage;
use App\Repositories\Contracts\PropertyRepositoryInterface;
use App\Repositories\Contracts\TenancyRepositoryInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class PropertyService
{
    public function __construct(
        private readonly PropertyRepositoryInterface $propertyRepository,
        private readonly TenancyRepositoryInterface $tenancyRepository,
    ) {}

    public function store(array $data): Property
    {
        return DB::transaction(function () use ($data) {
            $property = $this->propertyRepository->create($this->payload($data));
            $this->storeImages($property, $data['images'] ?? []);

            return $property->load('images');
        });
    }

    public function update(Property $property, array $data): Property
    {
        return DB::transaction(function () use ($property, $data) {
            $this->propertyRepository->update($property, $this->payload($data));
            $this->storeImages($property, $data['images'] ?? []);

            return $property->refresh()->load(['images', 'propertyType']);
        });
    }

    public function delete(Property $property): void
    {
        if ($this->tenancyRepository->hasAnyForProperty($property)) {
            throw ValidationException::withMessages([
                'property' => 'This property has tenancy history and cannot be deleted.',
            ]);
        }

        DB::transaction(function () use ($property) {
            $property->load('images');

            foreach ($property->images as $image) {
                $this->deleteImageFile($image);
            }

            $this->propertyRepository->delete($property);
        });
    }

    public function deleteImage(Property $property, PropertyImage $image): void
    {
        $this->deleteImageFile($image);
        $this->propertyRepository->deleteImage($image);
    }

    /**
     * @param  array<int, mixed>  $files
     */
    public function storeImages(Property $property, array $files): void
    {
        $disk = (string) config('properties.images.disk', 'public');
        $sortOrder = $this->propertyRepository->nextImageSortOrder($property);

        foreach ($files as $file) {
            if (! $file instanceof UploadedFile) {
                continue;
            }

            $path = $file->store('properties/'.$property->id, $disk);

            if (! is_string($path) || $path === '') {
                continue;
            }

            $this->propertyRepository->addImage($property, [
                'original_name' => $file->getClientOriginalName(),
                'disk' => $disk,
                'path' => $path,
                'mime_type' => $file->getMimeType() ?: $file->getClientMimeType(),
                'size' => $file->getSize() ?: 0,
                'sort_order' => $sortOrder,
            ]);

            $sortOrder++;
        }
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function payload(array $data): array
    {
        return [
            'property_type_id' => $data['property_type_id'],
            'name' => $data['name'],
            'address_line_1' => $data['address_line_1'],
            'address_line_2' => $data['address_line_2'] ?? null,
            'address_line_3' => $data['address_line_3'] ?? null,
            'city' => $data['city'],
            'county' => $data['county'] ?? null,
            'postcode' => $data['postcode'],
            'country' => $data['country'],
        ];
    }

    private function deleteImageFile(PropertyImage $image): void
    {
        if ($image->path !== '') {
            Storage::disk($image->disk)->delete($image->path);
        }
    }
}
