<?php

namespace App\Models;

use App\Enums\TicketStatus;
use App\Models\Concerns\AuditsModelChanges;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use OwenIt\Auditing\Contracts\Auditable;

class Ticket extends Model implements Auditable
{
    use AuditsModelChanges;

    protected $fillable = [
        'reference',
        'subject',
        'body',
        'ticket_type_id',
        'ticket_priority_id',
        'status',
        'creator_type',
        'creator_id',
        'assignee_type',
        'assignee_id',
    ];

    protected function casts(): array
    {
        return [
            'status' => TicketStatus::class,
        ];
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(TicketType::class, 'ticket_type_id');
    }

    public function priority(): BelongsTo
    {
        return $this->belongsTo(TicketPriority::class, 'ticket_priority_id');
    }

    public function creator(): MorphTo
    {
        return $this->morphTo();
    }

    public function assignee(): MorphTo
    {
        return $this->morphTo();
    }

    public function participants(): HasMany
    {
        return $this->hasMany(TicketParticipant::class);
    }

    public function replies(): HasMany
    {
        return $this->hasMany(TicketReply::class)->oldest();
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(TicketAttachment::class);
    }

    public function openingAttachments(): HasMany
    {
        return $this->attachments()->whereNull('ticket_reply_id');
    }

    public function hasParticipant(Model $actor): bool
    {
        return $this->participants()
            ->where('participant_type', $actor->getMorphClass())
            ->where('participant_id', $actor->getKey())
            ->exists();
    }

    public function participantFor(Model $actor): ?TicketParticipant
    {
        return $this->participants()
            ->where('participant_type', $actor->getMorphClass())
            ->where('participant_id', $actor->getKey())
            ->first();
    }

    public function isUnreadFor(Model $actor): bool
    {
        $participant = $this->relationLoaded('participants')
            ? $this->participants->first(function (TicketParticipant $participant) use ($actor) {
                return $participant->participant_type === $actor->getMorphClass()
                    && (int) $participant->participant_id === (int) $actor->getKey();
            })
            : $this->participantFor($actor);

        if ($participant === null) {
            return false;
        }

        if ($participant->last_read_at === null) {
            return true;
        }

        return $this->updated_at !== null && $this->updated_at->gt($participant->last_read_at);
    }
}
