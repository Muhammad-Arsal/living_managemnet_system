<?php

namespace App\Services\Admin;

use App\Models\Admin;
use App\Repositories\Contracts\AdminRepositoryInterface;

class AdminManagementService
{
    public function __construct(
        private readonly AdminRepositoryInterface $adminRepository,
    ) {}

    public function store(array $data): Admin
    {
        $admin = $this->adminRepository->createWithProfile([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'is_active' => $data['is_active'] ?? true,
        ]);

        $admin->sendEmailVerificationNotification();

        return $admin;
    }

    public function update(Admin $admin, array $data): Admin
    {
        $payload = [
            'name' => $data['name'],
            'email' => $data['email'],
            'is_active' => $data['is_active'] ?? $admin->is_active,
        ];

        if (! empty($data['password'])) {
            $payload['password'] = $data['password'];
        }

        if (($payload['email'] ?? null) !== $admin->email) {
            $payload['email_verified_at'] = null;
        }

        return $this->adminRepository->update($admin, $payload);
    }

    public function delete(Admin $admin): void
    {
        $this->adminRepository->delete($admin);
    }
}
