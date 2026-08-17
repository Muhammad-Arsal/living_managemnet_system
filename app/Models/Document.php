<?php

namespace App\Models;

use App\Models\Concerns\AuditsModelChanges;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use OwenIt\Auditing\Contracts\Auditable;

class Document extends Model implements Auditable
{
    use AuditsModelChanges;

    protected $fillable = [
        'documentable_type',
        'documentable_id',
        'original_name',
        'disk',
        'path',
        'mime_type',
        'size',
    ];

    protected function casts(): array
    {
        return [
            'size' => 'integer',
        ];
    }

    public function documentable(): MorphTo
    {
        return $this->morphTo();
    }

    public function formattedSize(): string
    {
        $bytes = $this->size;

        if ($bytes < 1024) {
            return $bytes.' B';
        }

        if ($bytes < 1048576) {
            return number_format($bytes / 1024, 1).' KB';
        }

        return number_format($bytes / 1048576, 1).' MB';
    }

    public function extensionLabel(): string
    {
        $extension = strtoupper((string) pathinfo((string) $this->original_name, PATHINFO_EXTENSION));

        return $extension !== '' ? $extension : 'FILE';
    }
}
