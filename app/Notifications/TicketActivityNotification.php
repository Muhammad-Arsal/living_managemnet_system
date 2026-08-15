<?php

namespace App\Notifications;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TicketActivityNotification extends Notification
{
    use Queueable;

    /**
     * @param  array{title: string, message: string, url: string, event: string}  $payload
     */
    public function __construct(
        public Ticket $ticket,
        public array $payload,
    ) {}

    /**
     * @return list<string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'ticket_id' => $this->ticket->id,
            'reference' => $this->ticket->reference,
            'event' => $this->payload['event'],
            'title' => $this->payload['title'],
            'message' => $this->payload['message'],
            'url' => $this->payload['url'],
        ];
    }
}
