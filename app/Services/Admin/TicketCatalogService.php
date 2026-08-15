<?php

namespace App\Services\Admin;

use App\Models\TicketPriority;
use App\Models\TicketType;
use App\Repositories\Contracts\TicketPriorityRepositoryInterface;
use App\Repositories\Contracts\TicketTypeRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class TicketCatalogService
{
    public function __construct(
        private readonly TicketTypeRepositoryInterface $ticketTypeRepository,
        private readonly TicketPriorityRepositoryInterface $ticketPriorityRepository,
    ) {}

    public function paginateTypes(int $perPage = 15): LengthAwarePaginator
    {
        return $this->ticketTypeRepository->paginate($perPage);
    }

    public function paginatePriorities(int $perPage = 15): LengthAwarePaginator
    {
        return $this->ticketPriorityRepository->paginate($perPage);
    }

    public function storeType(array $data): TicketType
    {
        return $this->ticketTypeRepository->create($this->payload($data));
    }

    public function updateType(TicketType $ticketType, array $data): TicketType
    {
        return $this->ticketTypeRepository->update($ticketType, $this->payload($data, $ticketType->slug));
    }

    public function deleteType(TicketType $ticketType): void
    {
        if ($ticketType->tickets()->exists()) {
            throw ValidationException::withMessages([
                'ticket_type' => 'This ticket type is used by existing tickets. Deactivate it instead of deleting.',
            ]);
        }

        $this->ticketTypeRepository->delete($ticketType);
    }

    public function storePriority(array $data): TicketPriority
    {
        return $this->ticketPriorityRepository->create($this->payload($data));
    }

    public function updatePriority(TicketPriority $ticketPriority, array $data): TicketPriority
    {
        return $this->ticketPriorityRepository->update($ticketPriority, $this->payload($data, $ticketPriority->slug));
    }

    public function deletePriority(TicketPriority $ticketPriority): void
    {
        if ($ticketPriority->tickets()->exists()) {
            throw ValidationException::withMessages([
                'ticket_priority' => 'This ticket priority is used by existing tickets. Deactivate it instead of deleting.',
            ]);
        }

        $this->ticketPriorityRepository->delete($ticketPriority);
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array{name: string, slug: string, is_active: bool, sort_order: int}
     */
    private function payload(array $data, ?string $existingSlug = null): array
    {
        return [
            'name' => $data['name'],
            'slug' => $existingSlug ?: Str::slug($data['name']),
            'is_active' => (bool) ($data['is_active'] ?? true),
            'sort_order' => (int) ($data['sort_order'] ?? 0),
        ];
    }
}
