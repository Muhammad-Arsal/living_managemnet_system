<?php

namespace App\Services;

use App\Contracts\PortalUser;

class AdminMailService
{
    public function __construct(
        protected PortalMailService $portalMailService
    ) {}

    public function sendEmailVerification(PortalUser $user): void
    {
        $this->portalMailService->sendEmailVerification($user);
    }

    public function sendPasswordReset(PortalUser $user, string $token): void
    {
        $this->portalMailService->sendPasswordReset($user, $token);
    }

    public function sendPasswordSetup(PortalUser $user, string $token): void
    {
        $this->portalMailService->sendPasswordSetup($user, $token);
    }

    public function sendWelcomeWithPasswordSetup(PortalUser $user): string
    {
        return $this->portalMailService->sendWelcomeWithPasswordSetup($user);
    }

    public function sendPasswordResetLink(PortalUser $user): string
    {
        return $this->portalMailService->sendPasswordResetLink($user);
    }
}
