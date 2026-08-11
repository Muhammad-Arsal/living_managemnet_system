<?php

namespace App\Contracts;

/**
 * Portal authenticatable users (admin, staff, council).
 * Models must expose name + email attributes and implement these methods.
 */
interface PortalUser
{
    public function getKey();

    public function getEmailForVerification();

    public function verificationUrlForEmail(): string;

    /**
     * Portal key from config/portals.php (e.g. admin, staff, council).
     */
    public function portalKey(): string;
}
