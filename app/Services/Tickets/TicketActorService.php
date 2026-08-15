<?php

namespace App\Services\Tickets;

use App\Contracts\PortalUser;
use App\Models\Ticket;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

class TicketActorService
{
    public function portalKey(Model $actor): string
    {
        if ($actor instanceof PortalUser) {
            return $actor->portalKey();
        }

        throw new InvalidArgumentException('Ticket actor must implement PortalUser.');
    }

    public function canCreate(Model $actor): bool
    {
        return in_array($this->portalKey($actor), config('tickets.creators', []), true);
    }

    public function canViewAll(Model $actor): bool
    {
        return in_array($this->portalKey($actor), config('tickets.view_all', []), true);
    }

    public function canReplyAll(Model $actor): bool
    {
        return in_array($this->portalKey($actor), config('tickets.reply_all', []), true);
    }

    public function isSame(Model $left, Model $right): bool
    {
        return $left->getMorphClass() === $right->getMorphClass()
            && (int) $left->getKey() === (int) $right->getKey();
    }

    public function actorKey(Model $actor): string
    {
        return $actor->getMorphClass().':'.$actor->getKey();
    }

    public function displayName(Model $actor): string
    {
        return (string) ($actor->name ?? class_basename($actor));
    }

    public function email(Model $actor): ?string
    {
        $email = $actor->email ?? null;

        return is_string($email) && $email !== '' ? $email : null;
    }

    public function ticketUrl(Model $actor, Ticket $ticket): string
    {
        return route($this->portalKey($actor).'.tickets.show', $ticket);
    }
}
