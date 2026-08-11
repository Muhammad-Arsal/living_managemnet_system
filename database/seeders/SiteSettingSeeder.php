<?php

namespace Database\Seeders;

use App\Enums\SiteSettingKey;
use App\Models\SiteSetting;
use Illuminate\Database\Seeder;

class SiteSettingSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            SiteSettingKey::AdminEmail->value => 'admin@example.com',
            SiteSettingKey::FromEmail->value => 'noreply@example.com',
            SiteSettingKey::SiteName->value => 'Living Management System',
            SiteSettingKey::Address->value => '',
            SiteSettingKey::Phone->value => '',
            SiteSettingKey::Logo->value => null,
            SiteSettingKey::Favicon->value => null,
        ];

        foreach ($defaults as $key => $value) {
            SiteSetting::query()->updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }
    }
}
