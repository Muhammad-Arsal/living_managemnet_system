<?php

namespace App\Services\Tickets;

use App\Models\Ticket;
use App\Models\TicketReply;
use App\Notifications\TicketActivityNotification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;

class TicketNotificationService
{
    public function __construct(
        private readonly TicketActorService $ticketActorService,
        private readonly TicketMailService $ticketMailService,
    ) {}

    public function notifyCreated(Ticket $ticket, Model $sender): void
    {
        $assignee = $ticket->assignee;

        if ($assignee === null) {
            return;
        }

        $this->notifyActors(collect([$assignee]), $ticket, $sender, 'created', null);
    }

    public function notifyReplied(Ticket $ticket, Model $sender, TicketReply $reply): void
    {
        $recipients = $ticket->participants
            ->map(fn ($participant) => $participant->participant)
            ->filter();

        $this->notifyActors($recipients, $ticket, $sender, 'replied', $reply);
    }

    /**
     * @param  Collection<int, Model>  $recipients
     */
    private function notifyActors(Collection $recipients, Ticket $ticket, Model $sender, string $event, ?TicketReply $reply): void
    {
        $seen = [];

        foreach ($recipients as $recipient) {
            if (! $recipient instanceof Model) {
                continue;
            }

            $key = $this->ticketActorService->actorKey($recipient);

            if (isset($seen[$key]) || $this->ticketActorService->isSame($recipient, $sender)) {
                continue;
            }

            $seen[$key] = true;

            $this->notifyOne($recipient, $ticket, $sender, $event, $reply);
        }
    }

    private function notifyOne(Model $recipient, Ticket $ticket, Model $sender, string $event, ?TicketReply $reply): void
    {
        $title = $event === 'created'
            ? 'New ticket assigned to you'
            : 'New reply on '.$ticket->reference;

        $message = $event === 'created'
            ? $this->ticketActorService->displayName($sender).' created '.$ticket->reference.': '.$ticket->subject
            : $this->ticketActorService->displayName($sender).' replied to '.$ticket->reference;

        if (in_array(Notifiable::class, class_uses_recursive($recipient), true)) {
            $recipient->notify(new TicketActivityNotification($ticket, [
                'event' => $event,
                'title' => $title,
                'message' => $message,
                'url' => $this->ticketActorService->ticketUrl($recipient, $ticket),
            ]));
        }

        if ($event === 'created') {
            $this->ticketMailService->sendCreated($recipient, $ticket, $sender);
        } elseif ($reply !== null) {
            $this->ticketMailService->sendReplied($recipient, $ticket, $sender, $reply);
        }
    }
}
