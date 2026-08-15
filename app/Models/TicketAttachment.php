<?php

namespace App\Models;

use App\Models\Concerns\AuditsModelChanges;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use OwenIt\Auditing\Contracts\Auditable;

class TicketAttachment extends Model implements Auditable
{
    use AuditsModelChanges;

    protected $fillable = [
        'ticket_id',
        'ticket_reply_id',
        'uploader_type',
        'uploader_id',
        'original_name',
        'disk',
        'path',
        'mime_type',
        'size',
    ];

    protected function casts(): array
    {
        return [
            'size' => 'integer',
        ];
    }

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    public function reply(): BelongsTo
    {
        return $this->belongsTo(TicketReply::class, 'ticket_reply_id');
    }

    public function uploader(): MorphTo
    {
        return $this->morphTo();
    }

    public function formattedSize(): string
    {
        $bytes = $this->size;

        if ($bytes < 1024) {
            return $bytes.' B';
        }

        if ($bytes < 1048576) {
            return number_format($bytes / 1024, 1).' KB';
        }

        return number_format($bytes / 1048576, 1).' MB';
    }
}
