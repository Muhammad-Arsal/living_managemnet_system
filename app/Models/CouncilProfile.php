<?php

namespace App\Models;

use App\Models\Concerns\AuditsModelChanges;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Contracts\Auditable;

class CouncilProfile extends Model implements Auditable
{
    use AuditsModelChanges;

    protected $fillable = [
        'council_id',
        'phone',
        'avatar',
        'organization',
        'bio',
    ];

    public function council(): BelongsTo
    {
        return $this->belongsTo(Council::class);
    }
}
