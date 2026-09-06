<?php

namespace App\Services\Admin;

use App\Enums\RentalMode;
use App\Models\Document;
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
        private readonly DocumentService $documentService,
        private readonly RoomService $roomService,
    ) {}

    public function store(array $data): Property
    {
        $mode = RentalMode::from($data['rental_mode']);

        return DB::transaction(function () use ($data, $mode) {
            $property = $this->propertyRepository->create($this->payload($data));
            $this->storeImages($property, $data['images'] ?? []);
            $this->documentService->storeMany($property, $data['documents'] ?? []);

            if ($mode === RentalMode::PerRoom) {
                $this->roomService->createMany($property, $data['rooms'] ?? []);
            }

            return $property->load(['images', 'documents', 'rooms']);
        });
    }

    public function update(Property $property, array $data): Property
    {
        return DB::transaction(function () use ($property, $data) {
            $newMode = RentalMode::from($data['rental_mode']);
            $oldMode = $property->rental_mode;

            if ($oldMode !== $newMode) {
                $this->changeRentalMode($property, $newMode, $data['rooms'] ?? []);
            }

            $this->propertyRepository->update($property, $this->payload($data));

            return $property->refresh()->load(['images', 'propertyType', 'documents', 'rooms']);
        });
    }

    /**
     * @param  array<int, mixed>  $files
     */
    public function storeDocuments(Property $property, array $files): Property
    {
        $this->documentService->storeMany($property, $files);

        return $property->refresh()->load('documents');
    }

    /**
     * @param  array<int, mixed>  $files
     */
    public function addImages(Property $property, array $files): Property
    {
        $this->storeImages($property, $files);

        return $property->refresh()->load('images');
    }

    public function deleteDocument(Property $property, Document $document): void
    {
        $this->documentService->delete($property, $document);
    }

    public function delete(Property $property): void
    {
        if ($this->tenancyRepository->hasAnyForProperty($property)) {
            throw ValidationException::withMessages([
                'property' => 'This property has tenancy history and cannot be deleted.',
            ]);
        }

        DB::transaction(function () use ($property) {
            $property->load(['images', 'documents', 'rooms']);

            foreach ($property->rooms as $room) {
                $room->delete();
            }

            foreach ($property->images as $image) {
                $this->deleteImageFile($image);
            }

            $this->documentService->deleteAll($property);
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
            if (! $file instanceof UploadedFile || ! $file->isValid()) {
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
     * @param  array<int, array{name: string}>  $rooms
     */
    private function changeRentalMode(Property $property, RentalMode $newMode, array $rooms): void
    {
        if ($this->tenancyRepository->hasOpenForProperty($property)) {
            throw ValidationException::withMessages([
                'rental_mode' => 'End all current tenancies before changing rental mode.',
            ]);
        }

        if ($newMode === RentalMode::Whole) {
            if ($this->tenancyRepository->anyRoomHasTenancyHistory($property)) {
                throw ValidationException::withMessages([
                    'rental_mode' => 'Cannot switch to Whole Property while rooms have tenancy history.',
                ]);
            }

            $this->roomService->deleteAllForProperty($property->load('rooms'));

            return;
        }

        if ($rooms === []) {
            throw ValidationException::withMessages([
                'rooms' => 'Add at least one room when switching to per-room rental.',
            ]);
        }

        $property->rental_mode = RentalMode::PerRoom;
        $this->roomService->createMany($property, $rooms);
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function payload(array $data): array
    {
        return [
            'property_type_id' => $data['property_type_id'],
            'rental_mode' => $data['rental_mode'],
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
