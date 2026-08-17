<?php

namespace App\Repositories\Contracts;

use App\Models\Document;
use Illuminate\Database\Eloquent\Model;

interface DocumentRepositoryInterface
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function add(Model $documentable, array $data): Document;

    public function delete(Document $document): void;
}
