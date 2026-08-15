<?php

namespace App\Services\Tickets;

use App\Models\Ticket;
use App\Models\TicketAttachment;
use App\Models\TicketReply;
use App\Repositories\Contracts\TicketRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class TicketAttachmentService
{
    public function __construct(
        private readonly TicketRepositoryInterface $ticketRepository,
    ) {}

    /**
     * @param  array<int, UploadedFile>|null  $files
     */
    public function storeMany(Ticket $ticket, Model $uploader, ?array $files, ?TicketReply $reply = null): void
    {
        if ($files === null || $files === []) {
            return;
        }

        $disk = (string) config('tickets.attachments.disk', 'local');

        foreach ($files as $file) {
            if (! $file instanceof UploadedFile) {
                continue;
            }

            $path = $file->store('tickets/'.$ticket->id, $disk);

            if (! is_string($path) || $path === '') {
                continue;
            }

            $this->ticketRepository->addAttachment($ticket, [
                'uploader_type' => $uploader->getMorphClass(),
                'uploader_id' => $uploader->getKey(),
                'original_name' => $file->getClientOriginalName(),
                'disk' => $disk,
                'path' => $path,
                'mime_type' => $file->getMimeType() ?: $file->getClientMimeType(),
                'size' => $file->getSize() ?: 0,
            ], $reply);
        }
    }

    public function download(Ticket $ticket, TicketAttachment $attachment)
    {
        abort_unless($attachment->ticket_id === $ticket->id, 404);

        $disk = Storage::disk($attachment->disk);

        abort_unless($disk->exists($attachment->path), 404);

        return $disk->download($attachment->path, $attachment->original_name);
    }
}
