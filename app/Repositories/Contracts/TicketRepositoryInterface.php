<?php

namespace App\Repositories\Contracts;

use App\Models\Ticket;
use App\Models\TicketAttachment;
use App\Models\TicketParticipant;
use App\Models\TicketReply;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;

interface TicketRepositoryInterface
{
    public function paginateAll(array $filters, int $perPage = 15): LengthAwarePaginator;

    public function paginateForParticipant(Model $actor, array $filters, int $perPage = 15): LengthAwarePaginator;

    public function findById(int $id): ?Ticket;

    public function create(array $data): Ticket;

    public function addParticipant(Ticket $ticket, Model $actor, string $role): TicketParticipant;

    public function findParticipant(Ticket $ticket, Model $actor): ?TicketParticipant;

    public function markParticipantRead(TicketParticipant $participant): void;

    public function addReply(Ticket $ticket, Model $author, string $body): TicketReply;

    public function addAttachment(Ticket $ticket, array $data, ?TicketReply $reply = null): TicketAttachment;

    public function touch(Ticket $ticket): void;
}
