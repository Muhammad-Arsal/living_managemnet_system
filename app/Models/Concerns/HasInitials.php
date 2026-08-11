<?php

namespace App\Models\Concerns;

trait HasInitials
{
    public function getInitialsAttribute(): string
    {
        $parts = preg_split('/\s+/', trim((string) $this->name)) ?: [];

        if ($parts === [] || $parts[0] === '') {
            return 'U';
        }

        $initials = strtoupper(substr($parts[0], 0, 1));

        if (isset($parts[1])) {
            $initials .= strtoupper(substr($parts[1], 0, 1));
        }

        return $initials;
    }
}
