<?php

namespace App\Services\Admin;

use App\Repositories\Contracts\AdminRepositoryInterface;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AdminAuthService
{
    public function __construct(
        private readonly AdminRepositoryInterface $admins
    ) {}

    public function attempt(array $credentials, bool $remember = false): void
    {
        $this->ensureIsNotRateLimited($credentials['email']);

        $admin = $this->admins->findByEmail($credentials['email']);

        if (! $admin || ! $admin->is_active || ! Auth::guard('admin')->attempt([
            'email' => $credentials['email'],
            'password' => $credentials['password'],
        ], $remember)) {
            RateLimiter::hit($this->throttleKey($credentials['email']), 15 * 60);

            throw ValidationException::withMessages([
                'email' => __('These credentials do not match our records.'),
            ]);
        }

        RateLimiter::clear($this->throttleKey($credentials['email']));
        session()->regenerate();

        $this->admins->markLastLogin(Auth::guard('admin')->user());
    }

    public function logout(): void
    {
        Auth::guard('admin')->logout();
        session()->invalidate();
        session()->regenerateToken();
    }

    protected function throttleKey(string $email): string
    {
        return Str::lower($email).'|'.request()->ip().'|admin';
    }

    protected function ensureIsNotRateLimited(string $email): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey($email), 5)) {
            return;
        }

        event(new Lockout(request()));

        $seconds = RateLimiter::availableIn($this->throttleKey($email));

        throw ValidationException::withMessages([
            'email' => 'Too many login attempts. Please try again in '.$seconds.' seconds.',
        ]);
    }
}
