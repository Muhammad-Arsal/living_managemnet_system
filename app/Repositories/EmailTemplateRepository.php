<?php

namespace App\Repositories;

use App\Models\EmailTemplate;
use App\Repositories\Contracts\EmailTemplateRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EmailTemplateRepository implements EmailTemplateRepositoryInterface
{
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return EmailTemplate::query()->latest()->paginate($perPage);
    }

    public function create(array $data): EmailTemplate
    {
        return EmailTemplate::query()->create($data);
    }

    public function update(EmailTemplate $emailTemplate, array $data): EmailTemplate
    {
        $emailTemplate->update($data);

        return $emailTemplate->refresh();
    }

    public function delete(EmailTemplate $emailTemplate): void
    {
        $emailTemplate->delete();
    }
}
