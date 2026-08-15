<?php

namespace App\Repositories;

use App\Enums\TicketParticipantRole;
use App\Models\Ticket;
use App\Models\TicketAttachment;
use App\Models\TicketParticipant;
use App\Models\TicketReply;
use App\Repositories\Contracts\TicketRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class TicketRepository implements TicketRepositoryInterface
{
    public function paginateAll(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        return $this->filteredQuery($filters)
            ->latest()
            ->paginate($perPage)
            ->withQueryString();
    }

    public function paginateForParticipant(Model $actor, array $filters, int $perPage = 15): LengthAwarePaginator
    {
        return $this->filteredQuery($filters)
            ->whereHas('participants', function (Builder $query) use ($actor) {
                $query->where('participant_type', $actor->getMorphClass())
                    ->where('participant_id', $actor->getKey());
            })
            ->latest()
            ->paginate($perPage)
            ->withQueryString();
    }

    public function findById(int $id): ?Ticket
    {
        return Ticket::query()
            ->with([
                'type',
                'priority',
                'creator',
                'assignee',
                'participants.participant',
                'openingAttachments',
                'replies.author',
                'replies.attachments',
            ])
            ->find($id);
    }

    public function create(array $data): Ticket
    {
        $ticket = Ticket::query()->create($data);

        if (empty($ticket->reference) || str_starts_with($ticket->reference, 'TMP-')) {
            $ticket->forceFill([
                'reference' => 'TKT-'.str_pad((string) $ticket->id, 6, '0', STR_PAD_LEFT),
            ])->save();
        }

        return $ticket->refresh();
    }

    public function addParticipant(Ticket $ticket, Model $actor, string $role): TicketParticipant
    {
        $existing = $this->findParticipant($ticket, $actor);

        if ($existing !== null) {
            return $existing;
        }

        return $ticket->participants()->create([
            'participant_type' => $actor->getMorphClass(),
            'participant_id' => $actor->getKey(),
            'role' => $role,
            'last_read_at' => $role === TicketParticipantRole::Creator->value ? now() : null,
        ]);
    }

    public function findParticipant(Ticket $ticket, Model $actor): ?TicketParticipant
    {
        return $ticket->participants()
            ->where('participant_type', $actor->getMorphClass())
            ->where('participant_id', $actor->getKey())
            ->first();
    }

    public function markParticipantRead(TicketParticipant $participant): void
    {
        $participant->forceFill(['last_read_at' => now()])->save();
    }

    public function addReply(Ticket $ticket, Model $author, string $body): TicketReply
    {
        return $ticket->replies()->create([
            'author_type' => $author->getMorphClass(),
            'author_id' => $author->getKey(),
            'body' => $body,
        ]);
    }

    public function addAttachment(Ticket $ticket, array $data, ?TicketReply $reply = null): TicketAttachment
    {
        return $ticket->attachments()->create([
            ...$data,
            'ticket_reply_id' => $reply?->id,
        ]);
    }

    public function touch(Ticket $ticket): void
    {
        $ticket->touch();
    }

    /**
     * @param  array{search?: string|null, status?: string|null, ticket_priority_id?: int|null}  $filters
     */
    private function filteredQuery(array $filters): Builder
    {
        $search = $filters['search'] ?? null;
        $status = $filters['status'] ?? null;
        $priorityId = $filters['ticket_priority_id'] ?? null;

        return Ticket::query()
            ->with(['type', 'priority', 'creator', 'assignee', 'participants'])
            ->when(
                is_string($search) && $search !== '',
                function (Builder $query) use ($search) {
                    $query->where(function (Builder $inner) use ($search) {
                        $inner->where('reference', 'like', '%'.$search.'%')
                            ->orWhere('subject', 'like', '%'.$search.'%');
                    });
                }
            )
            ->when(
                is_string($status) && $status !== '',
                fn (Builder $query) => $query->where('status', $status)
            )
            ->when(
                is_int($priorityId) && $priorityId > 0,
                fn (Builder $query) => $query->where('ticket_priority_id', $priorityId)
            );
    }
}
