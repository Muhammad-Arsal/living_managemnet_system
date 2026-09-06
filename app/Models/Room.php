<?php

namespace App\Models;

use App\Models\Concerns\AuditsModelChanges;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use OwenIt\Auditing\Contracts\Auditable;

class Room extends Model implements Auditable
{
    use AuditsModelChanges;

    protected $fillable = [
        'property_id',
        'name',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
        ];
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function tenancies(): HasMany
    {
        return $this->hasMany(Tenancy::class)->orderByDesc('started_on');
    }

    public function currentTenancy(): HasOne
    {
        return $this->hasOne(Tenancy::class)->whereNull('ended_on');
    }

    public function isOccupied(): bool
    {
        if ($this->relationLoaded('currentTenancy')) {
            return $this->currentTenancy !== null;
        }

        return $this->currentTenancy()->exists();
    }

    public function occupancyLabel(): string
    {
        return $this->isOccupied() ? 'Occupied' : 'Vacant';
    }
}
