<?php

namespace App\Models;

use App\Enums\RentalMode;
use App\Models\Concerns\AuditsModelChanges;
use App\Models\Concerns\HasDocuments;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use OwenIt\Auditing\Contracts\Auditable;

class Property extends Model implements Auditable
{
    use AuditsModelChanges;
    use HasDocuments;

    protected $fillable = [
        'property_type_id',
        'rental_mode',
        'name',
        'address_line_1',
        'address_line_2',
        'address_line_3',
        'city',
        'county',
        'postcode',
        'country',
    ];

    protected function casts(): array
    {
        return [
            'rental_mode' => RentalMode::class,
        ];
    }

    public function propertyType(): BelongsTo
    {
        return $this->belongsTo(PropertyType::class);
    }

    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class)->orderBy('sort_order')->orderBy('id');
    }

    public function tenancies(): HasMany
    {
        return $this->hasMany(Tenancy::class)->orderByDesc('started_on');
    }

    public function currentTenancy(): HasOne
    {
        return $this->hasOne(Tenancy::class)->whereNull('ended_on')->whereNull('room_id');
    }

    public function currentTenancies(): HasMany
    {
        return $this->hasMany(Tenancy::class)->whereNull('ended_on');
    }

    public function images(): HasMany
    {
        return $this->hasMany(PropertyImage::class)->orderBy('sort_order')->orderBy('id');
    }

    public function coverImage(): HasOne
    {
        return $this->hasOne(PropertyImage::class)->oldestOfMany();
    }

    public function isWhole(): bool
    {
        return $this->rental_mode === RentalMode::Whole;
    }

    public function isPerRoom(): bool
    {
        return $this->rental_mode === RentalMode::PerRoom;
    }

    public function occupiedRoomCount(): int
    {
        if (! $this->isPerRoom()) {
            return $this->isOccupied() ? 1 : 0;
        }

        if ($this->relationLoaded('rooms')) {
            return $this->rooms->filter(fn (Room $room) => $room->isOccupied())->count();
        }

        return $this->rooms()->whereHas('currentTenancy')->count();
    }

    public function roomCount(): int
    {
        if ($this->relationLoaded('rooms')) {
            return $this->rooms->count();
        }

        return $this->rooms()->count();
    }

    public function isOccupied(): bool
    {
        if ($this->isPerRoom()) {
            $roomCount = $this->roomCount();

            return $roomCount > 0 && $this->occupiedRoomCount() === $roomCount;
        }

        if ($this->relationLoaded('currentTenancy')) {
            return $this->currentTenancy !== null;
        }

        return $this->currentTenancy()->exists();
    }

    public function isPartiallyOccupied(): bool
    {
        if (! $this->isPerRoom()) {
            return false;
        }

        $occupied = $this->occupiedRoomCount();
        $total = $this->roomCount();

        return $occupied > 0 && $occupied < $total;
    }

    public function isVacant(): bool
    {
        if ($this->isPerRoom()) {
            return $this->occupiedRoomCount() === 0;
        }

        return ! $this->isOccupied();
    }

    public function hasAssignableVacancy(): bool
    {
        if ($this->isWhole()) {
            return $this->isVacant();
        }

        return $this->rooms()->whereDoesntHave('currentTenancy')->exists();
    }

    public function occupancyLabel(): string
    {
        if ($this->isPerRoom()) {
            if ($this->isVacant()) {
                return 'Vacant';
            }

            if ($this->isPartiallyOccupied()) {
                return 'Partially occupied';
            }

            return 'Fully occupied';
        }

        return $this->isOccupied() ? 'Occupied' : 'Vacant';
    }

    public function occupancySummary(): string
    {
        if ($this->isPerRoom()) {
            return $this->occupiedRoomCount().' of '.$this->roomCount().' rooms occupied';
        }

        return $this->occupancyLabel();
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
        return $query->where(function (Builder $builder) {
            $builder
                ->where(function (Builder $whole) {
                    $whole->where('rental_mode', RentalMode::Whole)
                        ->whereHas('currentTenancy');
                })
                ->orWhere(function (Builder $perRoom) {
                    $perRoom->where('rental_mode', RentalMode::PerRoom)
                        ->whereHas('rooms')
                        ->whereDoesntHave('rooms', fn (Builder $room) => $room->whereDoesntHave('currentTenancy'));
                });
        });
    }

    public function scopeVacant(Builder $query): Builder
    {
        return $query->where(function (Builder $builder) {
            $builder
                ->where(function (Builder $whole) {
                    $whole->where('rental_mode', RentalMode::Whole)
                        ->whereDoesntHave('currentTenancy');
                })
                ->orWhere(function (Builder $perRoom) {
                    $perRoom->where('rental_mode', RentalMode::PerRoom)
                        ->whereDoesntHave('rooms', fn (Builder $room) => $room->whereHas('currentTenancy'));
                });
        });
    }

    public function scopePartiallyOccupied(Builder $query): Builder
    {
        return $query
            ->where('rental_mode', RentalMode::PerRoom)
            ->whereHas('rooms', fn (Builder $room) => $room->whereHas('currentTenancy'))
            ->whereHas('rooms', fn (Builder $room) => $room->whereDoesntHave('currentTenancy'));
    }

    public function scopeAssignable(Builder $query): Builder
    {
        return $query->where(function (Builder $builder) {
            $builder
                ->where(function (Builder $whole) {
                    $whole->where('rental_mode', RentalMode::Whole)
                        ->whereDoesntHave('currentTenancy');
                })
                ->orWhere(function (Builder $perRoom) {
                    $perRoom->where('rental_mode', RentalMode::PerRoom)
                        ->whereHas('rooms', fn (Builder $room) => $room->whereDoesntHave('currentTenancy'));
                });
        });
    }
}
