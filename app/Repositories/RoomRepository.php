<?php

namespace App\Repositories;

use App\Models\Property;
use App\Models\Room;
use App\Repositories\Contracts\RoomRepositoryInterface;
use Illuminate\Support\Collection;

class RoomRepository implements RoomRepositoryInterface
{
    public function create(Property $property, array $data): Room
    {
        return $property->rooms()->create($data);
    }

    public function update(Room $room, array $data): Room
    {
        $room->update($data);

        return $room->refresh();
    }

    public function delete(Room $room): void
    {
        $room->delete();
    }

    public function nextSortOrder(Property $property): int
    {
        return (int) $property->rooms()->max('sort_order') + 1;
    }

    public function countForProperty(Property $property): int
    {
        return $property->rooms()->count();
    }

    public function vacantForProperty(Property $property): Collection
    {
        return $property->rooms()
            ->whereDoesntHave('currentTenancy')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();
    }

    public function findForProperty(Property $property, int $roomId): ?Room
    {
        return $property->rooms()->whereKey($roomId)->first();
    }
}
