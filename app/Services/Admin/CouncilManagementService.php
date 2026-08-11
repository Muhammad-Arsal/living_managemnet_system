<?php

namespace App\Services\Admin;

use App\Models\Council;
use App\Repositories\Contracts\CouncilRepositoryInterface;

class CouncilManagementService
{
    public function __construct(
        private readonly CouncilRepositoryInterface $councilRepository,
    ) {}

    public function store(array $data): Council
    {
        $council = $this->councilRepository->createWithProfile([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'is_active' => $data['is_active'] ?? true,
        ]);

        $council->sendEmailVerificationNotification();

        return $council;
    }

    public function update(Council $council, array $data): Council
    {
        $payload = [
            'name' => $data['name'],
            'email' => $data['email'],
            'is_active' => $data['is_active'] ?? $council->is_active,
        ];

        if (! empty($data['password'])) {
            $payload['password'] = $data['password'];
        }

        if (($payload['email'] ?? null) !== $council->email) {
            $payload['email_verified_at'] = null;
        }

        return $this->councilRepository->update($council, $payload);
    }

    public function delete(Council $council): void
    {
        $this->councilRepository->delete($council);
    }
}
