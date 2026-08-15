<?php

namespace App\Models;

use App\Enums\TicketParticipantRole;
use App\Models\Concerns\AuditsModelChanges;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use OwenIt\Auditing\Contracts\Auditable;

class TicketParticipant extends Model implements Auditable
{
    use AuditsModelChanges;

    protected $fillable = [
        'ticket_id',
        'participant_type',
        'participant_id',
        'role',
        'last_read_at',
    ];

    protected function casts(): array
    {
        return [
            'role' => TicketParticipantRole::class,
            'last_read_at' => 'datetime',
        ];
    }

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    public function participant(): MorphTo
    {
        return $this->morphTo();
    }
}
