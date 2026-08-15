<?php

namespace App\Policies;

use App\Models\Ticket;
use App\Services\Tickets\TicketActorService;
use Illuminate\Database\Eloquent\Model;

class TicketPolicy
{
    public function __construct(
        private readonly TicketActorService $ticketActorService,
    ) {}

    public function viewAny(Model $actor): bool
    {
        return $this->ticketActorService->canViewAll($actor)
            || $this->ticketActorService->canCreate($actor)
            || array_key_exists($this->ticketActorService->portalKey($actor), config('portals', []));
    }

    public function view(Model $actor, Ticket $ticket): bool
    {
        if ($this->ticketActorService->canViewAll($actor)) {
            return true;
        }

        return $ticket->hasParticipant($actor);
    }

    public function create(Model $actor): bool
    {
        return $this->ticketActorService->canCreate($actor);
    }

    public function reply(Model $actor, Ticket $ticket): bool
    {
        if ($this->ticketActorService->canReplyAll($actor)) {
            return true;
        }

        return $ticket->hasParticipant($actor);
    }
}
