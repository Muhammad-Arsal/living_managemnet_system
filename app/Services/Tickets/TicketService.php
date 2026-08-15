<?php

namespace App\Services\Tickets;

use App\Enums\TicketParticipantRole;
use App\Enums\TicketStatus;
use App\Models\Ticket;
use App\Models\TicketReply;
use App\Repositories\Contracts\TicketPriorityRepositoryInterface;
use App\Repositories\Contracts\TicketRepositoryInterface;
use App\Repositories\Contracts\TicketTypeRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use InvalidArgumentException;

class TicketService
{
    public function __construct(
        private readonly TicketRepositoryInterface $ticketRepository,
        private readonly TicketTypeRepositoryInterface $ticketTypeRepository,
        private readonly TicketPriorityRepositoryInterface $ticketPriorityRepository,
        private readonly TicketAssignmentService $ticketAssignmentService,
        private readonly TicketActorService $ticketActorService,
        private readonly TicketNotificationService $ticketNotificationService,
        private readonly TicketAttachmentService $ticketAttachmentService,
    ) {}

    /**
     * @param  array{search?: string|null, status?: string|null, ticket_priority_id?: int|null}  $filters
     */
    public function paginateForActor(Model $actor, array $filters, int $perPage = 15): LengthAwarePaginator
    {
        if ($this->ticketActorService->canViewAll($actor)) {
            return $this->ticketRepository->paginateAll($filters, $perPage);
        }

        return $this->ticketRepository->paginateForParticipant($actor, $filters, $perPage);
    }

    public function create(Model $creator, array $data): Ticket
    {
        if (! $this->ticketActorService->canCreate($creator)) {
            throw new InvalidArgumentException('This actor cannot create tickets.');
        }

        $type = $this->ticketTypeRepository->findById((int) $data['ticket_type_id']);
        $priority = $this->ticketPriorityRepository->findById((int) $data['ticket_priority_id']);

        if ($type === null || ! $type->is_active) {
            throw new InvalidArgumentException('The selected ticket type is not available.');
        }

        if ($priority === null || ! $priority->is_active) {
            throw new InvalidArgumentException('The selected ticket priority is not available.');
        }

        $assignee = $this->ticketAssignmentService->resolveAssignee($creator, (int) $data['assignee_id']);

        $ticket = DB::transaction(function () use ($creator, $assignee, $data) {
            $ticket = $this->ticketRepository->create([
                'reference' => 'TMP-'.Str::ulid(),
                'subject' => $data['subject'],
                'body' => $data['body'],
                'ticket_type_id' => $data['ticket_type_id'],
                'ticket_priority_id' => $data['ticket_priority_id'],
                'status' => TicketStatus::Open->value,
                'creator_type' => $creator->getMorphClass(),
                'creator_id' => $creator->getKey(),
                'assignee_type' => $assignee->getMorphClass(),
                'assignee_id' => $assignee->getKey(),
            ]);

            $this->ticketRepository->addParticipant($ticket, $creator, TicketParticipantRole::Creator->value);
            $this->ticketRepository->addParticipant($ticket, $assignee, TicketParticipantRole::Assignee->value);
            $this->ticketAttachmentService->storeMany($ticket, $creator, $data['attachments'] ?? null);

            return $ticket;
        });

        $ticket = $this->ticketRepository->findById($ticket->id) ?? $ticket;
        $this->ticketNotificationService->notifyCreated($ticket, $creator);

        return $ticket;
    }

    public function reply(Ticket $ticket, Model $author, array $data): TicketReply
    {
        $reply = DB::transaction(function () use ($ticket, $author, $data) {
            if (! $ticket->hasParticipant($author)) {
                $this->ticketRepository->addParticipant(
                    $ticket,
                    $author,
                    TicketParticipantRole::Subscriber->value
                );
            }

            $reply = $this->ticketRepository->addReply($ticket, $author, $data['body']);
            $this->ticketAttachmentService->storeMany($ticket, $author, $data['attachments'] ?? null, $reply);
            $this->ticketRepository->touch($ticket);

            $participant = $this->ticketRepository->findParticipant($ticket, $author);
            if ($participant !== null) {
                $this->ticketRepository->markParticipantRead($participant);
            }

            return $reply;
        });

        $ticket = $this->ticketRepository->findById($ticket->id) ?? $ticket->load(['participants.participant', 'type', 'priority']);
        $this->ticketNotificationService->notifyReplied($ticket, $author, $reply);

        return $reply;
    }

    public function markRead(Ticket $ticket, Model $actor): void
    {
        $participant = $this->ticketRepository->findParticipant($ticket, $actor);

        if ($participant === null) {
            return;
        }

        $this->ticketRepository->markParticipantRead($participant);
    }
}
