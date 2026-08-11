<?php

namespace App\Models\Concerns;

use Illuminate\Support\Facades\URL;

trait IsPortalUser
{
    abstract public function portalKey(): string;

    public function verificationUrlForEmail(): string
    {
        $portal = $this->portalKey();

        return URL::temporarySignedRoute(
            "{$portal}.verification.verify",
            now()->addDays(7),
            [
                'id' => $this->getKey(),
                'hash' => sha1($this->getEmailForVerification()),
            ]
        );
    }

    public function getEmailForVerification()
    {
        return $this->email;
    }

    public function markEmailAsVerified(): bool
    {
        return $this->forceFill([
            'email_verified_at' => $this->freshTimestamp(),
        ])->save();
    }
}
