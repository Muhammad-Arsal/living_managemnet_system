<?php

namespace App\Repositories;

use App\Models\Document;
use App\Repositories\Contracts\DocumentRepositoryInterface;
use Illuminate\Database\Eloquent\Model;

class DocumentRepository implements DocumentRepositoryInterface
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function add(Model $documentable, array $data): Document
    {
        return $documentable->documents()->create($data);
    }

    public function delete(Document $document): void
    {
        $document->delete();
    }
}
