<?php

namespace App\Models;

use App\Models\Concerns\AuditsModelChanges;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Contracts\Auditable;

class Tenancy extends Model implements Auditable
{
    use AuditsModelChanges;

    protected $fillable = [
        'tenant_id',
        'property_id',
        'room_id',
        'started_on',
        'ended_on',
        'active_tenant_id',
        'active_whole_property_id',
        'active_room_id',
    ];

    protected function casts(): array
    {
        return [
            'started_on' => 'date',
            'ended_on' => 'date',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (Tenancy $tenancy): void {
            if ($tenancy->ended_on === null) {
                $tenancy->active_tenant_id = $tenancy->tenant_id;

                if ($tenancy->room_id === null) {
                    $tenancy->active_whole_property_id = $tenancy->property_id;
                    $tenancy->active_room_id = null;
                } else {
                    $tenancy->active_room_id = $tenancy->room_id;
                    $tenancy->active_whole_property_id = null;
                }
            } else {
                $tenancy->active_tenant_id = null;
                $tenancy->active_whole_property_id = null;
                $tenancy->active_room_id = null;
            }
        });
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function isCurrent(): bool
    {
        return $this->ended_on === null;
    }

    public function isWhole(): bool
    {
        return $this->room_id === null;
    }

    public function locationLabel(): string
    {
        if ($this->room_id === null) {
            return 'Whole property';
        }

        if ($this->relationLoaded('room') && $this->room !== null) {
            return $this->room->name;
        }

        return $this->room?->name ?? 'Room';
    }

    public function statusLabel(): string
    {
        if ($this->ended_on === null) {
            if ($this->started_on !== null && $this->started_on->isFuture()) {
                return 'Upcoming';
            }

            return 'Current';
        }

        return 'Ended';
    }
}
