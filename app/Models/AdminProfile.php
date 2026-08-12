<?php

namespace App\Models;

use App\Models\Concerns\AuditsModelChanges;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Contracts\Auditable;

class AdminProfile extends Model implements Auditable
{
    use AuditsModelChanges;

    protected $fillable = [
        'admin_id',
        'phone',
        'avatar',
        'job_title',
        'bio',
    ];

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class);
    }
}
