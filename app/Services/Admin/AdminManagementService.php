<?php

namespace App\Services\Admin;

use App\Models\Admin;
use App\Repositories\Contracts\AdminRepositoryInterface;
use App\Services\AdminMailService;
use Illuminate\Support\Str;

class AdminManagementService
{
    public function __construct(
        private readonly AdminRepositoryInterface $adminRepository,
        private readonly AdminMailService $adminMailService,
    ) {}

    public function store(array $data): Admin
    {
        $admin = $this->adminRepository->createWithProfile([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Str::password(32),
            'is_active' => $data['is_active'] ?? true,
        ]);

        $this->adminMailService->sendWelcomeWithPasswordSetup($admin);

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
