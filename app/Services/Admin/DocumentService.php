<?php

namespace App\Services\Admin;

use App\Models\Document;
use App\Repositories\Contracts\DocumentRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DocumentService
{
    public function __construct(
        private readonly DocumentRepositoryInterface $documentRepository,
    ) {}

    /**
     * @param  array<int, mixed>  $files
     */
    public function storeMany(Model $documentable, array $files): void
    {
        $disk = (string) config('documents.disk', 'local');
        $directory = $this->directory($documentable);

        foreach ($files as $file) {
            if (! $file instanceof UploadedFile || ! $file->isValid()) {
                continue;
            }

            $path = $file->store($directory, $disk);

            if (! is_string($path) || $path === '') {
                continue;
            }

            $this->documentRepository->add($documentable, [
                'original_name' => $file->getClientOriginalName(),
                'disk' => $disk,
                'path' => $path,
                'mime_type' => $file->getMimeType() ?: $file->getClientMimeType(),
                'size' => $file->getSize() ?: 0,
            ]);
        }
    }

    public function download(Model $documentable, Document $document): StreamedResponse
    {
        abort_unless(
            $document->documentable_type === $documentable->getMorphClass()
            && (int) $document->documentable_id === (int) $documentable->getKey(),
            404
        );

        $disk = Storage::disk($document->disk);

        abort_unless($disk->exists($document->path), 404);

        return $disk->download($document->path, $document->original_name);
    }

    public function delete(Model $documentable, Document $document): void
    {
        abort_unless(
            $document->documentable_type === $documentable->getMorphClass()
            && (int) $document->documentable_id === (int) $documentable->getKey(),
            404
        );

        $this->deleteFile($document);
        $this->documentRepository->delete($document);
    }

    public function deleteAll(Model $documentable): void
    {
        $documentable->loadMissing('documents');

        foreach ($documentable->documents as $document) {
            $this->deleteFile($document);
            $this->documentRepository->delete($document);
        }
    }

    private function directory(Model $documentable): string
    {
        return 'documents/'.$documentable->getMorphClass().'/'.$documentable->getKey();
    }

    private function deleteFile(Document $document): void
    {
        if ($document->path !== '') {
            Storage::disk($document->disk)->delete($document->path);
        }
    }
}
