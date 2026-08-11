<?php

namespace App\Repositories;

use App\Models\Admin;
use App\Repositories\Contracts\AdminRepositoryInterface;
use Illuminate\Support\Facades\DB;

class AdminRepository implements AdminRepositoryInterface
{
    public function findById(int $id): ?Admin
    {
        return Admin::query()->with('profile')->find($id);
    }

    public function findByEmail(string $email): ?Admin
    {
        return Admin::query()->where('email', $email)->first();
    }

    public function create(array $data): Admin
    {
        return Admin::query()->create($data);
    }

    public function update(Admin $admin, array $data): Admin
    {
        $admin->update($data);

        return $admin->refresh();
    }

    public function createWithProfile(array $adminData, array $profileData = []): Admin
    {
        return DB::transaction(function () use ($adminData, $profileData) {
            $admin = $this->create($adminData);
            $admin->profile()->create($profileData);

            return $admin->load('profile');
        });
    }

    public function markLastLogin(Admin $admin): void
    {
        $admin->forceFill(['last_login_at' => now()])->save();
    }
}
