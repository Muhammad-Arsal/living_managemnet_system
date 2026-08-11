<?php

namespace App\Repositories;

use App\Models\Council;
use App\Repositories\Contracts\CouncilRepositoryInterface;
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

    public function markLastLogin(Council $council): void
    {
        $council->forceFill(['last_login_at' => now()])->save();
    }
}
