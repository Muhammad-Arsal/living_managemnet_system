<?php

namespace App\Repositories;

use App\Models\Admin;
use App\Repositories\Contracts\AdminRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class AdminRepository implements AdminRepositoryInterface
{
    public function listActive(): Collection
    {
        return Admin::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
    }

    public function findActiveById(int $id): ?Admin
    {
        return Admin::query()
            ->where('is_active', true)
            ->find($id);
    }

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

    public function updateWithProfile(Admin $admin, array $adminData, array $profileData = []): Admin
    {
        return DB::transaction(function () use ($admin, $adminData, $profileData) {
            if ($adminData !== []) {
                $admin->update($adminData);
            }

            $admin->profile()->updateOrCreate(
                ['admin_id' => $admin->id],
                $profileData
            );

            return $admin->refresh()->load('profile');
        });
    }

    public function markLastLogin(Admin $admin): void
    {
        $admin->forceFill(['last_login_at' => now()])->save();
    }

    public function paginateFiltered(?string $column, ?string $search, int $perPage = 15): LengthAwarePaginator
    {
        $allowed = ['name', 'email'];

        return Admin::query()
            ->with('profile')
            ->when(
                $search !== null && $search !== '' && $column !== null && in_array($column, $allowed, true),
                fn ($query) => $query->where($column, 'like', '%'.$search.'%')
            )
            ->latest()
            ->paginate($perPage)
            ->withQueryString();
    }

    public function delete(Admin $admin): void
    {
        $admin->delete();
    }
}
