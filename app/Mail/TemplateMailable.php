<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TemplateMailable extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $toEmail,
        string $subject,
        public string $emailContent,
    ) {
        $this->subject = $subject;
        $this->afterCommit();
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            to: [$this->toEmail],
            subject: $this->subject,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.layout',
            with: [
                'subject' => $this->subject,
                'content' => $this->emailContent,
            ]
        );
    }
}
