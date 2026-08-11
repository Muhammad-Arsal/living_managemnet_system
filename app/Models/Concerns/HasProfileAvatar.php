<?php

namespace App\Models\Concerns;

trait HasProfileAvatar
{
    public function getAvatarUrlAttribute(): ?string
    {
        $path = $this->profile?->avatar;

        if (! $path) {
            return null;
        }

        // Root-relative so avatars work regardless of APP_URL host/port
        // (e.g. localhost vs 127.0.0.1:8000).
        return '/storage/'.ltrim($path, '/');
    }
}
