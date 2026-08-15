<?php

namespace App\Repositories\Contracts;

use App\Models\TicketType;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface TicketTypeRepositoryInterface
{
    public function paginate(int $perPage = 15): LengthAwarePaginator;

    public function listActive(): Collection;

    public function findById(int $id): ?TicketType;

    public function create(array $data): TicketType;

    public function update(TicketType $ticketType, array $data): TicketType;

    public function delete(TicketType $ticketType): void;
}
