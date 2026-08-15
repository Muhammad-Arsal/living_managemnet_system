<?php

namespace App\Repositories\Contracts;

use App\Models\Admin;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface AdminRepositoryInterface
{
    public function listActive(): Collection;

    public function findActiveById(int $id): ?Admin;

    public function findById(int $id): ?Admin;

    public function findByEmail(string $email): ?Admin;

    public function create(array $data): Admin;

    public function update(Admin $admin, array $data): Admin;

    public function createWithProfile(array $adminData, array $profileData = []): Admin;

    public function updateWithProfile(Admin $admin, array $adminData, array $profileData = []): Admin;

    public function markLastLogin(Admin $admin): void;

    public function paginateFiltered(?string $column, ?string $search, int $perPage = 15): LengthAwarePaginator;

    public function delete(Admin $admin): void;
}
