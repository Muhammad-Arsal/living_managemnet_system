<?php

namespace App\Repositories\Contracts;

use App\Models\EmailTemplate;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface EmailTemplateRepositoryInterface
{
    public function paginate(int $perPage = 15): LengthAwarePaginator;

    public function create(array $data): EmailTemplate;

    public function update(EmailTemplate $emailTemplate, array $data): EmailTemplate;

    public function delete(EmailTemplate $emailTemplate): void;
}
