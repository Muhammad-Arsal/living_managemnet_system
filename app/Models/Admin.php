<?php

namespace App\Models;

use App\Contracts\PortalUser;
use App\Models\Concerns\HasInitials;
use App\Models\Concerns\HasProfileAvatar;
use App\Models\Concerns\IsPortalUser;
use App\Services\AdminMailService;
use Illuminate\Auth\Passwords\CanResetPassword as CanResetPasswordTrait;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Admin extends Authenticatable implements CanResetPassword, PortalUser
{
    use CanResetPasswordTrait;
    use HasInitials;
    use HasProfileAvatar;
    use IsPortalUser;
    use Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'email_verified_at',
        'is_active',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'is_active' => 'boolean',
            'password' => 'hashed',
        ];
    }

    public function profile(): HasOne
    {
        return $this->hasOne(AdminProfile::class);
    }

    public function hasVerifiedEmail(): bool
    {
        return $this->email_verified_at !== null;
    }

    public function portalKey(): string
    {
        return 'admin';
    }

    public function sendPasswordResetNotification($token): void
    {
        app(AdminMailService::class)->sendPasswordReset($this, $token);
    }

    public function sendEmailVerificationNotification(): void
    {
        app(AdminMailService::class)->sendEmailVerification($this);
    }
}
