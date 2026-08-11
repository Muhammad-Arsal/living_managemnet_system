<?php

namespace Database\Seeders;

use App\Repositories\Contracts\AdminRepositoryInterface;
use App\Repositories\Contracts\CouncilRepositoryInterface;
use App\Repositories\Contracts\StaffRepositoryInterface;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            SiteSettingSeeder::class,
            EmailTemplateSeeder::class,
        ]);

        $admins = app(AdminRepositoryInterface::class);
        $staff = app(StaffRepositoryInterface::class);
        $councils = app(CouncilRepositoryInterface::class);

        if (! $admins->findByEmail('admin@example.com')) {
            $admins->createWithProfile([
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'password' => 'password',
                'email_verified_at' => now(),
                'is_active' => true,
            ], [
                'job_title' => 'System Administrator',
            ]);
        }

        if (! $staff->findByEmail('staff@example.com')) {
            $staff->createWithProfile([
                'name' => 'Staff User',
                'email' => 'staff@example.com',
                'password' => 'password',
                'email_verified_at' => now(),
                'is_active' => true,
            ], [
                'job_title' => 'Staff Member',
            ]);
        }

        if (! $councils->findByEmail('council@example.com')) {
            $councils->createWithProfile([
                'name' => 'Council User',
                'email' => 'council@example.com',
                'password' => 'password',
                'email_verified_at' => now(),
                'is_active' => true,
            ], [
                'organization' => 'Local Council',
            ]);
        }
    }
}
