<?php

namespace App\Models;

use App\Models\Concerns\AuditsModelChanges;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use OwenIt\Auditing\Contracts\Auditable;

class Tenant extends Model implements Auditable
{
    use AuditsModelChanges;

    protected $fillable = [
        'first_name',
        'last_name',
        'mobile_number',
        'email',
        'address_line_1',
        'address_line_2',
        'address_line_3',
        'city',
        'county',
        'postcode',
        'country',
    ];

    public function tenancies(): HasMany
    {
        return $this->hasMany(Tenancy::class)->orderByDesc('started_on');
    }

    public function currentTenancy(): HasOne
    {
        return $this->hasOne(Tenancy::class)->whereNull('ended_on');
    }

    public function getFullNameAttribute(): string
    {
        return trim($this->first_name.' '.$this->last_name);
    }

    public function getInitialsAttribute(): string
    {
        return strtoupper(substr($this->first_name, 0, 1).substr($this->last_name, 0, 1));
    }

    public function isCurrent(): bool
    {
        if ($this->relationLoaded('currentTenancy')) {
            return $this->currentTenancy !== null;
        }

        return $this->currentTenancy()->exists();
    }

    public function statusLabel(): string
    {
        return $this->isCurrent() ? 'Current' : 'Past';
    }

    public function scopeCurrent(Builder $query): Builder
    {
        return $query->whereHas('currentTenancy');
    }

    public function scopePast(Builder $query): Builder
    {
        return $query->whereDoesntHave('currentTenancy');
    }
}
