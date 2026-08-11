<?php

namespace App\Services\Council;

use App\Models\Council;
use App\Repositories\Contracts\CouncilRepositoryInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class CouncilProfileService
{
    public function __construct(
        private readonly CouncilRepositoryInterface $councils
    ) {}

    public function update(Council $council, array $data): Council
    {
        $council->loadMissing('profile');

        $councilData = [
            'name' => $data['name'],
            'email' => $data['email'],
        ];

        if (! empty($data['password'])) {
            $councilData['password'] = $data['password'];
        }

        if ($council->email !== $data['email']) {
            $councilData['email_verified_at'] = null;
        }

        $profileData = [
            'phone' => $data['phone'] ?? null,
            'organization' => $data['organization'] ?? null,
            'bio' => $data['bio'] ?? null,
        ];

        if (($data['avatar'] ?? null) instanceof UploadedFile) {
            $this->deleteAvatar($council->profile?->avatar);
            $profileData['avatar'] = $data['avatar']->store('avatars/councils', 'public');
        }

        return $this->councils->updateWithProfile($council, $councilData, $profileData);
    }

    protected function deleteAvatar(?string $path): void
    {
        if ($path) {
            Storage::disk('public')->delete($path);
        }
    }
}
