<?php

namespace App\Repositories\Contracts;

use App\Models\Staff;

interface StaffRepositoryInterface
{
    public function findById(int $id): ?Staff;

    public function findByEmail(string $email): ?Staff;

    public function create(array $data): Staff;

    public function update(Staff $staff, array $data): Staff;

    public function createWithProfile(array $staffData, array $profileData = []): Staff;

    public function updateWithProfile(Staff $staff, array $staffData, array $profileData = []): Staff;

    public function markLastLogin(Staff $staff): void;
}
