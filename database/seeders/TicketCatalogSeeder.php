<?php

namespace Database\Seeders;

use App\Models\TicketPriority;
use App\Models\TicketType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TicketCatalogSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['General', 'Maintenance', 'Billing'] as $index => $name) {
            TicketType::query()->updateOrCreate(
                ['slug' => Str::slug($name)],
                [
                    'name' => $name,
                    'is_active' => true,
                    'sort_order' => $index + 1,
                ]
            );
        }

        foreach (['Low', 'Medium', 'High', 'Urgent'] as $index => $name) {
            TicketPriority::query()->updateOrCreate(
                ['slug' => Str::slug($name)],
                [
                    'name' => $name,
                    'is_active' => true,
                    'sort_order' => $index + 1,
                ]
            );
        }
    }
}
