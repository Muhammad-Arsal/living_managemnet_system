<?php

namespace App\Services\Staff;

use App\Models\Staff;
use App\Repositories\Contracts\StaffRepositoryInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class StaffProfileService
{
    public function __construct(
        private readonly StaffRepositoryInterface $staff
    ) {}

    public function update(Staff $staff, array $data): Staff
    {
        $staff->loadMissing('profile');

        $staffData = [
            'name' => $data['name'],
            'email' => $data['email'],
        ];

        if (! empty($data['password'])) {
            $staffData['password'] = $data['password'];
        }

        if ($staff->email !== $data['email']) {
            $staffData['email_verified_at'] = null;
        }

        $profileData = [
            'phone' => $data['phone'] ?? null,
            'job_title' => $data['job_title'] ?? null,
            'bio' => $data['bio'] ?? null,
        ];

        if (($data['avatar'] ?? null) instanceof UploadedFile) {
            $this->deleteAvatar($staff->profile?->avatar);
            $profileData['avatar'] = $data['avatar']->store('avatars/staff', 'public');
        }

        return $this->staff->updateWithProfile($staff, $staffData, $profileData);
    }

    protected function deleteAvatar(?string $path): void
    {
        if ($path) {
            Storage::disk('public')->delete($path);
        }
    }
}
