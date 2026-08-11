<?php

namespace App\Repositories;

use App\Models\Staff;
use App\Repositories\Contracts\StaffRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class StaffRepository implements StaffRepositoryInterface
{
    public function findById(int $id): ?Staff
    {
        return Staff::query()->with('profile')->find($id);
    }

    public function findByEmail(string $email): ?Staff
    {
        return Staff::query()->where('email', $email)->first();
    }

    public function create(array $data): Staff
    {
        return Staff::query()->create($data);
    }

    public function update(Staff $staff, array $data): Staff
    {
        $staff->update($data);

        return $staff->refresh();
    }

    public function createWithProfile(array $staffData, array $profileData = []): Staff
    {
        return DB::transaction(function () use ($staffData, $profileData) {
            $staff = $this->create($staffData);
            $staff->profile()->create($profileData);

            return $staff->load('profile');
        });
    }

    public function updateWithProfile(Staff $staff, array $staffData, array $profileData = []): Staff
    {
        return DB::transaction(function () use ($staff, $staffData, $profileData) {
            if ($staffData !== []) {
                $staff->update($staffData);
            }

            $staff->profile()->updateOrCreate(
                ['staff_id' => $staff->id],
                $profileData
            );

            return $staff->refresh()->load('profile');
        });
    }

    public function markLastLogin(Staff $staff): void
    {
        $staff->forceFill(['last_login_at' => now()])->save();
    }

    public function paginateFiltered(?string $column, ?string $search, int $perPage = 15): LengthAwarePaginator
    {
        $allowed = ['name', 'email'];

        return Staff::query()
            ->when(
                $search !== null && $search !== '' && $column !== null && in_array($column, $allowed, true),
                fn ($query) => $query->where($column, 'like', '%'.$search.'%')
            )
            ->latest()
            ->paginate($perPage)
            ->withQueryString();
    }

    public function delete(Staff $staff): void
    {
        $staff->delete();
    }
}
