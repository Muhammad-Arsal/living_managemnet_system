<?php

namespace App\Repositories\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use OwenIt\Auditing\Models\Audit;

interface AuditRepositoryInterface
{
    /**
     * @return LengthAwarePaginator<int, Audit>
     */
    public function paginateFiltered(
        ?string $event,
        ?string $auditableType,
        ?string $fromDate,
        ?string $toDate,
        int $perPage = 20,
    ): LengthAwarePaginator;

    /**
     * @return Collection<int, string>
     */
    public function distinctAuditableTypes(): Collection;

    public function findById(int $id): ?Audit;
}
