<?php

namespace App\Services\Tickets;

use App\Mail\TemplateMailable;
use App\Models\Ticket;
use App\Models\TicketReply;
use App\Services\EmailTemplateService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Mail;

class TicketMailService
{
    public function __construct(
        private readonly EmailTemplateService $emailTemplateService,
        private readonly TicketActorService $ticketActorService,
    ) {}

    public function sendCreated(Model $recipient, Ticket $ticket, Model $sender): void
    {
        $this->send(
            $recipient,
            (string) config('tickets.email_types.created'),
            $this->replacements($recipient, $ticket, $sender, null),
            'New ticket '.$ticket->reference,
            '<p>A new ticket has been assigned to you.</p><p><a href="'.$this->ticketActorService->ticketUrl($recipient, $ticket).'">View ticket</a></p>',
        );
    }

    public function sendReplied(Model $recipient, Ticket $ticket, Model $sender, TicketReply $reply): void
    {
        $this->send(
            $recipient,
            (string) config('tickets.email_types.replied'),
            $this->replacements($recipient, $ticket, $sender, $reply),
            'New reply on ticket '.$ticket->reference,
            '<p>There is a new reply on ticket '.$ticket->reference.'.</p><p><a href="'.$this->ticketActorService->ticketUrl($recipient, $ticket).'">View ticket</a></p>',
        );
    }

    /**
     * @param  array<string, string>  $replacements
     */
    private function send(Model $recipient, string $emailType, array $replacements, string $fallbackSubject, string $fallbackContent): void
    {
        $email = $this->ticketActorService->email($recipient);

        if ($email === null) {
            return;
        }

        $rendered = $this->emailTemplateService->render($emailType, $replacements);

        Mail::to($email)->send(new TemplateMailable(
            $email,
            $rendered['subject'] ?? $fallbackSubject,
            $rendered['content'] ?? $fallbackContent,
        ));
    }

    /**
     * @return array<string, string>
     */
    private function replacements(Model $recipient, Ticket $ticket, Model $sender, ?TicketReply $reply): array
    {
        $excerpt = $reply?->body ?? $ticket->body;

        return [
            'name' => $this->ticketActorService->displayName($recipient),
            'sender_name' => $this->ticketActorService->displayName($sender),
            'ticket_reference' => (string) $ticket->reference,
            'ticket_subject' => $ticket->subject,
            'ticket_type' => (string) ($ticket->type?->name ?? ''),
            'ticket_priority' => (string) ($ticket->priority?->name ?? ''),
            'ticket_url' => $this->ticketActorService->ticketUrl($recipient, $ticket),
            'message_excerpt' => mb_strimwidth(strip_tags($excerpt), 0, 240, '…'),
        ];
    }
}
