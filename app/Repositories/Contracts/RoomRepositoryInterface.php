<?php

namespace App\Repositories\Contracts;

use App\Models\Property;
use App\Models\Room;
use Illuminate\Support\Collection;

interface RoomRepositoryInterface
{
    public function create(Property $property, array $data): Room;

    public function update(Room $room, array $data): Room;

    public function delete(Room $room): void;

    public function nextSortOrder(Property $property): int;

    public function countForProperty(Property $property): int;

    /**
     * @return Collection<int, Room>
     */
    public function vacantForProperty(Property $property): Collection;

    public function findForProperty(Property $property, int $roomId): ?Room;
}
