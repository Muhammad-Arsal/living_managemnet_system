<?php

namespace App\Services\Admin;

use App\Models\Tenant;
use App\Repositories\Contracts\TenancyRepositoryInterface;
use App\Repositories\Contracts\TenantRepositoryInterface;
use Illuminate\Validation\ValidationException;

class TenantService
{
    public function __construct(
        private readonly TenantRepositoryInterface $tenantRepository,
        private readonly TenancyRepositoryInterface $tenancyRepository,
    ) {}

    public function store(array $data): Tenant
    {
        return $this->tenantRepository->create($this->payload($data));
    }

    public function update(Tenant $tenant, array $data): Tenant
    {
        return $this->tenantRepository->update($tenant, $this->payload($data));
    }

    public function delete(Tenant $tenant): void
    {
        if ($this->tenancyRepository->hasAnyForTenant($tenant)) {
            throw ValidationException::withMessages([
                'tenant' => 'This tenant has tenancy history and cannot be deleted.',
            ]);
        }

        $this->tenantRepository->delete($tenant);
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
            'address_line_1' => $data['address_line_1'],
            'address_line_2' => $data['address_line_2'] ?? null,
            'address_line_3' => $data['address_line_3'] ?? null,
            'city' => $data['city'],
            'county' => $data['county'] ?? null,
            'postcode' => $data['postcode'],
            'country' => $data['country'],
        ];
    }
}
