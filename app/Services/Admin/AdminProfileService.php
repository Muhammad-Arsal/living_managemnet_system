<?php

namespace App\Services\Admin;

use App\Models\Admin;
use App\Repositories\Contracts\AdminRepositoryInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class AdminProfileService
{
    public function __construct(
        private readonly AdminRepositoryInterface $admins
    ) {}

    public function update(Admin $admin, array $data): Admin
    {
        $admin->loadMissing('profile');

        $adminData = [
            'name' => $data['name'],
            'email' => $data['email'],
        ];

        if (! empty($data['password'])) {
            $adminData['password'] = $data['password'];
        }

        if ($admin->email !== $data['email']) {
            $adminData['email_verified_at'] = null;
        }

        $profileData = [
            'phone' => $data['phone'] ?? null,
            'job_title' => $data['job_title'] ?? null,
            'bio' => $data['bio'] ?? null,
        ];

        if (($data['avatar'] ?? null) instanceof UploadedFile) {
            $this->deleteAvatar($admin->profile?->avatar);
            $profileData['avatar'] = $data['avatar']->store('avatars/admins', 'public');
        }

        return $this->admins->updateWithProfile($admin, $adminData, $profileData);
    }

    protected function deleteAvatar(?string $path): void
    {
        if ($path) {
            Storage::disk('public')->delete($path);
        }
    }
}
