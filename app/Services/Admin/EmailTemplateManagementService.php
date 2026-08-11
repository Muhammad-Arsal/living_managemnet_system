<?php

namespace App\Services\Admin;

use App\Models\EmailTemplate;
use App\Repositories\Contracts\EmailTemplateRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EmailTemplateManagementService
{
    public function __construct(
        private readonly EmailTemplateRepositoryInterface $emailTemplateRepository,
    ) {}

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->emailTemplateRepository->paginate($perPage);
    }

    public function store(array $data): EmailTemplate
    {
        return $this->emailTemplateRepository->create($data);
    }

    public function update(EmailTemplate $emailTemplate, array $data): EmailTemplate
    {
        return $this->emailTemplateRepository->update($emailTemplate, $data);
    }

    public function delete(EmailTemplate $emailTemplate): void
    {
        $this->emailTemplateRepository->delete($emailTemplate);
    }
}
