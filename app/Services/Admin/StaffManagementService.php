<?php

namespace App\Services\Admin;

use App\Models\Staff;
use App\Repositories\Contracts\StaffRepositoryInterface;
use App\Services\StaffMailService;
use Illuminate\Support\Str;

class StaffManagementService
{
    public function __construct(
        private readonly StaffRepositoryInterface $staffRepository,
        private readonly StaffMailService $staffMailService,
    ) {}

    public function store(array $data): Staff
    {
        $staff = $this->staffRepository->createWithProfile([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Str::password(32),
            'is_active' => $data['is_active'] ?? true,
        ]);

        $this->staffMailService->sendWelcomeWithPasswordSetup($staff);

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
