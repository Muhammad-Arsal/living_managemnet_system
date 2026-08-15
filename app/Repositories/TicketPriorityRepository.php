<?php

namespace App\Repositories;

use App\Models\TicketPriority;
use App\Repositories\Contracts\TicketPriorityRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class TicketPriorityRepository implements TicketPriorityRepositoryInterface
{
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return TicketPriority::query()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate($perPage);
    }

    public function listActive(): Collection
    {
        return TicketPriority::query()
            ->active()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
    }

    public function listOrdered(): Collection
    {
        return TicketPriority::query()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
    }

    public function findById(int $id): ?TicketPriority
    {
        return TicketPriority::query()->find($id);
    }

    public function create(array $data): TicketPriority
    {
        return TicketPriority::query()->create($data);
    }

    public function update(TicketPriority $ticketPriority, array $data): TicketPriority
    {
        $ticketPriority->update($data);

        return $ticketPriority->refresh();
    }

    public function delete(TicketPriority $ticketPriority): void
    {
        $ticketPriority->delete();
    }
}
