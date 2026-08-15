<?php

namespace App\Repositories\Contracts;

use App\Models\TicketPriority;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface TicketPriorityRepositoryInterface
{
    public function paginate(int $perPage = 15): LengthAwarePaginator;

    public function listActive(): Collection;

    public function listOrdered(): Collection;

    public function findById(int $id): ?TicketPriority;

    public function create(array $data): TicketPriority;

    public function update(TicketPriority $ticketPriority, array $data): TicketPriority;

    public function delete(TicketPriority $ticketPriority): void;
}
