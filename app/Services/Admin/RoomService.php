<?php

namespace App\Services\Admin;

use App\Enums\RentalMode;
use App\Models\Property;
use App\Models\Room;
use App\Repositories\Contracts\RoomRepositoryInterface;
use App\Repositories\Contracts\TenancyRepositoryInterface;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class RoomService
{
    public function __construct(
        private readonly RoomRepositoryInterface $roomRepository,
        private readonly TenancyRepositoryInterface $tenancyRepository,
    ) {}

    /**
     * @param  array{name: string}  $data
     */
    public function store(Property $property, array $data): Room
    {
        $this->assertPerRoom($property);

        try {
            return $this->roomRepository->create($property, [
                'name' => $data['name'],
                'sort_order' => $this->roomRepository->nextSortOrder($property),
            ]);
        } catch (QueryException) {
            throw ValidationException::withMessages([
                'name' => 'A room with this name already exists on this property.',
            ]);
        }
    }

    /**
     * @param  array{name: string}  $data
     */
    public function update(Property $property, Room $room, array $data): Room
    {
        $this->assertRoomBelongs($property, $room);
        $this->assertPerRoom($property);

        try {
            return $this->roomRepository->update($room, [
                'name' => $data['name'],
            ]);
        } catch (QueryException) {
            throw ValidationException::withMessages([
                'name' => 'A room with this name already exists on this property.',
            ]);
        }
    }

    public function delete(Property $property, Room $room): void
    {
        $this->assertRoomBelongs($property, $room);
        $this->assertPerRoom($property);

        if ($this->tenancyRepository->hasAnyForRoom($room)) {
            throw ValidationException::withMessages([
                'room' => 'This room has tenancy history and cannot be deleted.',
            ]);
        }

        if ($this->roomRepository->countForProperty($property) <= 1) {
            throw ValidationException::withMessages([
                'room' => 'A per-room property must keep at least one room.',
            ]);
        }

        $this->roomRepository->delete($room);
    }

    /**
     * @param  array<int, array{name: string}>  $rooms
     */
    public function createMany(Property $property, array $rooms): void
    {
        $this->assertPerRoom($property);

        $sortOrder = $this->roomRepository->nextSortOrder($property);

        foreach ($rooms as $room) {
            $this->roomRepository->create($property, [
                'name' => $room['name'],
                'sort_order' => $sortOrder,
            ]);
            $sortOrder++;
        }
    }

    public function deleteAllForProperty(Property $property): void
    {
        foreach ($property->rooms()->get() as $room) {
            if ($this->tenancyRepository->hasAnyForRoom($room)) {
                throw ValidationException::withMessages([
                    'rental_mode' => 'Cannot switch to Whole Property while rooms have tenancy history.',
                ]);
            }

            $this->roomRepository->delete($room);
        }
    }

    private function assertPerRoom(Property $property): void
    {
        if ($property->rental_mode !== RentalMode::PerRoom) {
            throw ValidationException::withMessages([
                'room' => 'Rooms can only be managed on per-room properties.',
            ]);
        }
    }

    private function assertRoomBelongs(Property $property, Room $room): void
    {
        if ((int) $room->property_id !== (int) $property->id) {
            abort(404);
        }
    }
}
