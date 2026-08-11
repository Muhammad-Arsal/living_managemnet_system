<?php

namespace App\Repositories;

use App\Models\SiteSetting;
use App\Repositories\Contracts\SiteSettingRepositoryInterface;

class SiteSettingRepository implements SiteSettingRepositoryInterface
{
    public function allKeyed(): array
    {
        return SiteSetting::query()->pluck('value', 'key')->toArray();
    }

    public function getValue(string $key, mixed $default = null): mixed
    {
        return SiteSetting::getValue($key, $default);
    }

    public function setValue(string $key, mixed $value): void
    {
        SiteSetting::setValue($key, $value);
    }
}
