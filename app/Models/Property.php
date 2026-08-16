<?php

namespace App\Models;

use App\Models\Concerns\AuditsModelChanges;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use OwenIt\Auditing\Contracts\Auditable;

class Property extends Model implements Auditable
{
    use AuditsModelChanges;

    protected $fillable = [
        'property_type_id',
        'name',
        'address_line_1',
        'address_line_2',
        'address_line_3',
        'city',
        'county',
        'postcode',
        'country',
    ];

    public function propertyType(): BelongsTo
    {
        return $this->belongsTo(PropertyType::class);
    }

    public function tenancies(): HasMany
    {
        return $this->hasMany(Tenancy::class)->orderByDesc('started_on');
    }

    public function currentTenancy(): HasOne
    {
        return $this->hasOne(Tenancy::class)->whereNull('ended_on');
    }

    public function images(): HasMany
    {
        return $this->hasMany(PropertyImage::class)->orderBy('sort_order')->orderBy('id');
    }

    public function coverImage(): HasOne
    {
        return $this->hasOne(PropertyImage::class)->oldestOfMany();
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

    public function formattedAddress(): string
    {
        return collect([
            $this->address_line_1,
            $this->address_line_2,
            $this->address_line_3,
            $this->city,
            $this->county,
            $this->postcode,
            $this->country,
        ])->filter()->implode(', ');
    }

    public function scopeOccupied(Builder $query): Builder
    {
        return $query->whereHas('currentTenancy');
    }

    public function scopeVacant(Builder $query): Builder
    {
        return $query->whereDoesntHave('currentTenancy');
    }
}
