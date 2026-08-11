<?php

namespace App\Services;

use App\Contracts\PortalUser;
use App\Mail\TemplateMailable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use InvalidArgumentException;

class PortalMailService
{
    public function __construct(
        protected EmailTemplateService $emailTemplateService
    ) {}

    public function sendEmailVerification(PortalUser $user): void
    {
        $verificationLink = $user->verificationUrlForEmail();

        $rendered = $this->renderTemplate($user->portalKey(), 'email_verification', [
            'name' => $user->name,
            'email' => $user->email,
            'verification_link' => $verificationLink,
            'portal_label' => $this->label($user->portalKey()),
        ]);

        $this->send(
            $user->email,
            $rendered['subject'] ?? 'Verify your email address',
            $rendered['content'] ?? '<p>Please verify your email.</p><p><a href="'.$verificationLink.'">Verify email</a></p>',
        );
    }

    public function sendPasswordReset(PortalUser $user, string $token): void
    {
        $resetLink = $this->passwordResetLink($user->portalKey(), $token, $user->email);

        $rendered = $this->renderTemplate($user->portalKey(), 'password_reset', [
            'name' => $user->name,
            'email' => $user->email,
            'reset_link' => $resetLink,
            'portal_label' => $this->label($user->portalKey()),
        ]);

        $this->send(
            $user->email,
            $rendered['subject'] ?? 'Reset your password',
            $rendered['content'] ?? '<p><a href="'.$resetLink.'">Reset your password</a></p>',
        );
    }

    public function sendWelcome(PortalUser $user, string $token): void
    {
        $resetLink = $this->passwordResetLink($user->portalKey(), $token, $user->email, setup: true);
        $replacements = [
            'name' => $user->name,
            'email' => $user->email,
            'reset_link' => $resetLink,
            'portal_label' => $this->label($user->portalKey()),
        ];

        $rendered = $this->renderTemplate($user->portalKey(), 'welcome', $replacements)
            ?? $this->renderTemplate($user->portalKey(), 'password_setup', $replacements);

        $this->send(
            $user->email,
            $rendered['subject'] ?? 'Set your password',
            $rendered['content'] ?? '<p><a href="'.$resetLink.'">Set your password</a></p>',
        );
    }

    public function sendPasswordSetup(PortalUser $user, string $token): void
    {
        $resetLink = $this->passwordResetLink($user->portalKey(), $token, $user->email, setup: true);
        $replacements = [
            'name' => $user->name,
            'email' => $user->email,
            'reset_link' => $resetLink,
            'portal_label' => $this->label($user->portalKey()),
        ];

        $rendered = $this->renderTemplate($user->portalKey(), 'password_setup', $replacements)
            ?? $this->renderTemplate($user->portalKey(), 'welcome', $replacements);

        $this->send(
            $user->email,
            $rendered['subject'] ?? 'Set your password',
            $rendered['content'] ?? '<p><a href="'.$resetLink.'">Set your password</a></p>',
        );
    }

    public function sendWelcomeWithPasswordSetup(PortalUser $user): string
    {
        $portal = $user->portalKey();

        return Password::broker($this->config($portal, 'password_broker'))->sendResetLink(
            ['email' => $user->email],
            function (PortalUser $portalUser, string $token) {
                $this->sendWelcome($portalUser, $token);
            }
        );
    }

    public function sendPasswordResetLink(PortalUser $user): string
    {
        return Password::broker($this->config($user->portalKey(), 'password_broker'))->sendResetLink(
            ['email' => $user->email]
        );
    }

    /**
     * @param  array<string, string>  $replacements
     * @return array{subject: string, content: string}|null
     */
    protected function renderTemplate(string $portal, string $templateKey, array $replacements): ?array
    {
        $emailType = $this->templateType($portal, $templateKey);

        return $this->emailTemplateService->render($emailType, $replacements);
    }

    protected function passwordResetLink(string $portal, string $token, string $email, bool $setup = false): string
    {
        $params = [
            'token' => $token,
            'email' => $email,
        ];

        if ($setup) {
            $params['setup'] = 1;
        }

        return route($this->config($portal, 'password_reset_route'), $params);
    }

    protected function templateType(string $portal, string $templateKey): string
    {
        $templates = $this->config($portal, 'templates');

        if (! is_array($templates) || empty($templates[$templateKey])) {
            throw new InvalidArgumentException("Missing email template key [{$templateKey}] for portal [{$portal}].");
        }

        return $templates[$templateKey];
    }

    protected function label(string $portal): string
    {
        return (string) $this->config($portal, 'label');
    }

    protected function config(string $portal, string $key): mixed
    {
        $value = config("portals.{$portal}.{$key}");

        if ($value === null) {
            throw new InvalidArgumentException("Missing portals config [{$portal}.{$key}].");
        }

        return $value;
    }

    protected function send(string $toEmail, string $subject, string $content): void
    {
        Mail::to($toEmail)->send(new TemplateMailable($toEmail, $subject, $content));
    }
}
