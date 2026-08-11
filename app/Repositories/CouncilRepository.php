<?php

namespace App\Repositories;

use App\Models\Council;
use App\Repositories\Contracts\CouncilRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class CouncilRepository implements CouncilRepositoryInterface
{
    public function findById(int $id): ?Council
    {
        return Council::query()->with('profile')->find($id);
    }

    public function findByEmail(string $email): ?Council
    {
        return Council::query()->where('email', $email)->first();
    }

    public function create(array $data): Council
    {
        return Council::query()->create($data);
    }

    public function update(Council $council, array $data): Council
    {
        $council->update($data);

        return $council->refresh();
    }

    public function createWithProfile(array $councilData, array $profileData = []): Council
    {
        return DB::transaction(function () use ($councilData, $profileData) {
            $council = $this->create($councilData);
            $council->profile()->create($profileData);

            return $council->load('profile');
        });
    }

    public function updateWithProfile(Council $council, array $councilData, array $profileData = []): Council
    {
        return DB::transaction(function () use ($council, $councilData, $profileData) {
            if ($councilData !== []) {
                $council->update($councilData);
            }

            $council->profile()->updateOrCreate(
                ['council_id' => $council->id],
                $profileData
            );

            return $council->refresh()->load('profile');
        });
    }

    public function markLastLogin(Council $council): void
    {
        $council->forceFill(['last_login_at' => now()])->save();
    }

    public function paginateFiltered(?string $column, ?string $search, int $perPage = 15): LengthAwarePaginator
    {
        $allowed = ['name', 'email'];

        return Council::query()
            ->with('profile')
            ->when(
                $search !== null && $search !== '' && $column !== null && in_array($column, $allowed, true),
                fn ($query) => $query->where($column, 'like', '%'.$search.'%')
            )
            ->latest()
            ->paginate($perPage)
            ->withQueryString();
    }

    public function delete(Council $council): void
    {
        $council->delete();
    }
}
