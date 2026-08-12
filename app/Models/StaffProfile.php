<?php

namespace App\Models;

use App\Models\Concerns\AuditsModelChanges;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Contracts\Auditable;

class StaffProfile extends Model implements Auditable
{
    use AuditsModelChanges;

    protected $fillable = [
        'staff_id',
        'phone',
        'avatar',
        'job_title',
        'bio',
    ];

    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class);
    }
}
