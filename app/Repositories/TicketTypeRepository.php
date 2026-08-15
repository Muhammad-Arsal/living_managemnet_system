<?php

namespace App\Repositories;

use App\Models\TicketType;
use App\Repositories\Contracts\TicketTypeRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class TicketTypeRepository implements TicketTypeRepositoryInterface
{
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return TicketType::query()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate($perPage);
    }

    public function listActive(): Collection
    {
        return TicketType::query()
            ->active()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
    }

    public function findById(int $id): ?TicketType
    {
        return TicketType::query()->find($id);
    }

    public function create(array $data): TicketType
    {
        return TicketType::query()->create($data);
    }

    public function update(TicketType $ticketType, array $data): TicketType
    {
        $ticketType->update($data);

        return $ticketType->refresh();
    }

    public function delete(TicketType $ticketType): void
    {
        $ticketType->delete();
    }
}
