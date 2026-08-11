<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CouncilProfile extends Model
{
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
