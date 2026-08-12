<?php

namespace App\Repositories;

use App\Repositories\Contracts\AuditRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use OwenIt\Auditing\Models\Audit;

class AuditRepository implements AuditRepositoryInterface
{
    public function paginateFiltered(
        ?string $event,
        ?string $auditableType,
        ?string $fromDate,
        ?string $toDate,
        int $perPage = 20,
    ): LengthAwarePaginator {
        $allowedEvents = ['created', 'updated', 'deleted', 'restored'];

        return Audit::query()
            ->with('user')
            ->when(
                $event !== null && $event !== '' && in_array($event, $allowedEvents, true),
                fn ($query) => $query->where('event', $event)
            )
            ->when(
                $auditableType !== null && $auditableType !== '',
                fn ($query) => $query->where('auditable_type', $auditableType)
            )
            ->when(
                $fromDate !== null && $fromDate !== '',
                fn ($query) => $query->whereDate('created_at', '>=', Carbon::parse($fromDate)->toDateString())
            )
            ->when(
                $toDate !== null && $toDate !== '',
                fn ($query) => $query->whereDate('created_at', '<=', Carbon::parse($toDate)->toDateString())
            )
            ->latest()
            ->paginate($perPage)
            ->withQueryString();
    }

    public function distinctAuditableTypes(): Collection
    {
        return Audit::query()
            ->select('auditable_type')
            ->distinct()
            ->orderBy('auditable_type')
            ->pluck('auditable_type')
            ->filter()
            ->values();
    }

    public function findById(int $id): ?Audit
    {
        return Audit::query()->with('user')->find($id);
    }
}
