<?php

namespace App\Services\Admin;

use App\Models\Staff;
use App\Repositories\Contracts\StaffRepositoryInterface;

class StaffManagementService
{
    public function __construct(
        private readonly StaffRepositoryInterface $staffRepository,
    ) {}

    public function store(array $data): Staff
    {
        $staff = $this->staffRepository->createWithProfile([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'is_active' => $data['is_active'] ?? true,
        ]);

        $staff->sendEmailVerificationNotification();

        return $staff;
    }

    public function update(Staff $staff, array $data): Staff
    {
        $payload = [
            'name' => $data['name'],
            'email' => $data['email'],
            'is_active' => $data['is_active'] ?? $staff->is_active,
        ];

        if (! empty($data['password'])) {
            $payload['password'] = $data['password'];
        }

        if (($payload['email'] ?? null) !== $staff->email) {
            $payload['email_verified_at'] = null;
        }

        return $this->staffRepository->update($staff, $payload);
    }

    public function delete(Staff $staff): void
    {
        $this->staffRepository->delete($staff);
    }
}
