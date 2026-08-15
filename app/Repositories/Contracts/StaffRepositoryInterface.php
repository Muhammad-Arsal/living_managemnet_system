<?php

namespace App\Repositories\Contracts;

use App\Models\Staff;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface StaffRepositoryInterface
{
    public function listActive(): Collection;

    public function findActiveById(int $id): ?Staff;

    public function findById(int $id): ?Staff;

    public function findByEmail(string $email): ?Staff;

    public function create(array $data): Staff;

    public function update(Staff $staff, array $data): Staff;

    public function createWithProfile(array $staffData, array $profileData = []): Staff;

    public function updateWithProfile(Staff $staff, array $staffData, array $profileData = []): Staff;

    public function markLastLogin(Staff $staff): void;

    public function paginateFiltered(?string $column, ?string $search, int $perPage = 15): LengthAwarePaginator;

    public function delete(Staff $staff): void;
}
