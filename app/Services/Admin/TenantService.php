<?php

namespace App\Services\Admin;

use App\Models\Document;
use App\Models\Tenant;
use App\Repositories\Contracts\TenancyRepositoryInterface;
use App\Repositories\Contracts\TenantRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TenantService
{
    public function __construct(
        private readonly TenantRepositoryInterface $tenantRepository,
        private readonly TenancyRepositoryInterface $tenancyRepository,
        private readonly DocumentService $documentService,
    ) {}

    public function store(array $data): Tenant
    {
        return DB::transaction(function () use ($data) {
            $tenant = $this->tenantRepository->create($this->payload($data));
            $this->documentService->storeMany($tenant, $data['documents'] ?? []);

            return $tenant->load('documents');
        });
    }

    public function update(Tenant $tenant, array $data): Tenant
    {
        return $this->tenantRepository->update($tenant, $this->payload($data));
    }

    /**
     * @param  array<int, mixed>  $files
     */
    public function storeDocuments(Tenant $tenant, array $files): Tenant
    {
        $this->documentService->storeMany($tenant, $files);

        return $tenant->refresh()->load('documents');
    }

    public function deleteDocument(Tenant $tenant, Document $document): void
    {
        $this->documentService->delete($tenant, $document);
    }

    public function delete(Tenant $tenant): void
    {
        if ($this->tenancyRepository->hasAnyForTenant($tenant)) {
            throw ValidationException::withMessages([
                'tenant' => 'This tenant has tenancy history and cannot be deleted.',
            ]);
        }

        DB::transaction(function () use ($tenant) {
            $this->documentService->deleteAll($tenant);
            $this->tenantRepository->delete($tenant);
        });
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function payload(array $data): array
    {
        return [
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'mobile_number' => $data['mobile_number'],
            'email' => $data['email'],
        ];
    }
}
