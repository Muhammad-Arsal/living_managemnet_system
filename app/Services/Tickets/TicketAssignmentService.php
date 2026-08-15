<?php

namespace App\Services\Tickets;

use App\Models\Admin;
use App\Models\Staff;
use App\Repositories\Contracts\AdminRepositoryInterface;
use App\Repositories\Contracts\StaffRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use InvalidArgumentException;

class TicketAssignmentService
{
    public function __construct(
        private readonly StaffRepositoryInterface $staffRepository,
        private readonly AdminRepositoryInterface $adminRepository,
        private readonly TicketActorService $ticketActorService,
    ) {}

    /**
     * @return array{portal: string, model: class-string}|null
     */
    public function assignableConfig(Model $creator): ?array
    {
        $config = config('tickets.assignable.'.$this->ticketActorService->portalKey($creator));

        return is_array($config) ? $config : null;
    }

    public function assigneesFor(Model $creator): Collection
    {
        $config = $this->assignableConfig($creator);

        if ($config === null) {
            return collect();
        }

        return match ($config['model']) {
            Staff::class => $this->staffRepository->listActive(),
            Admin::class => $this->adminRepository->listActive(),
            default => throw new InvalidArgumentException('Unsupported assignable model ['.$config['model'].'].'),
        };
    }

    public function resolveAssignee(Model $creator, int $assigneeId): Model
    {
        $config = $this->assignableConfig($creator);

        if ($config === null) {
            throw new InvalidArgumentException('This actor cannot assign tickets.');
        }

        $assignee = match ($config['model']) {
            Staff::class => $this->staffRepository->findActiveById($assigneeId),
            Admin::class => $this->adminRepository->findActiveById($assigneeId),
            default => throw new InvalidArgumentException('Unsupported assignable model ['.$config['model'].'].'),
        };

        if ($assignee === null) {
            throw new InvalidArgumentException('The selected assignee is not available.');
        }

        return $assignee;
    }
}
