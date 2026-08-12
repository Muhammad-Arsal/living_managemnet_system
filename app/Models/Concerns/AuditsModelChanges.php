<?php

namespace App\Models\Concerns;

use OwenIt\Auditing\Auditable;

trait AuditsModelChanges
{
    use Auditable;

    /**
     * @var list<string>
     */
    protected array $auditExclude = [
        'password',
        'remember_token',
    ];
}
