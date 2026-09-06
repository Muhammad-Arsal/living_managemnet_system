<?php

namespace App\Enums;

enum RentalMode: string
{
    case Whole = 'whole';
    case PerRoom = 'per_room';

    public function label(): string
    {
        return match ($this) {
            self::Whole => 'Whole Property',
            self::PerRoom => 'Per room',
        };
    }

    /**
     * @return array<string, string>
     */
    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $mode) => [$mode->value => $mode->label()])
            ->all();
    }
}
