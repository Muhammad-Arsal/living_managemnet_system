<?php

namespace App\Http\Requests\Concerns;

trait ValidatesDocuments
{
    /**
     * @return array<string, mixed>
     */
    protected function documentRules(bool $required = false): array
    {
        $maxFiles = (int) config('documents.max_files', 10);
        $maxKilobytes = (int) config('documents.max_kilobytes', 10240);
        $mimes = implode(',', config('documents.mimes', ['pdf', 'jpg', 'jpeg', 'png']));

        return [
            'documents' => array_values(array_filter([
                $required ? 'required' : 'nullable',
                'array',
                $required ? 'min:1' : null,
                'max:'.$maxFiles,
            ])),
            'documents.*' => ['file', 'max:'.$maxKilobytes, 'mimes:'.$mimes],
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function documentAttributes(): array
    {
        return [
            'documents' => 'documents',
            'documents.*' => 'document',
        ];
    }
}
