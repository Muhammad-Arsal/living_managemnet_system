<?php

namespace App\Repositories\Contracts;

use App\Models\Admin;

interface AdminRepositoryInterface
{
    public function findById(int $id): ?Admin;

    public function findByEmail(string $email): ?Admin;

    public function create(array $data): Admin;

    public function update(Admin $admin, array $data): Admin;

    public function createWithProfile(array $adminData, array $profileData = []): Admin;

    public function updateWithProfile(Admin $admin, array $adminData, array $profileData = []): Admin;

    public function markLastLogin(Admin $admin): void;
}
