<?php

namespace App\Repositories\Contracts;

use App\Models\Council;

interface CouncilRepositoryInterface
{
    public function findById(int $id): ?Council;

    public function findByEmail(string $email): ?Council;

    public function create(array $data): Council;

    public function update(Council $council, array $data): Council;

    public function createWithProfile(array $councilData, array $profileData = []): Council;

    public function markLastLogin(Council $council): void;
}
