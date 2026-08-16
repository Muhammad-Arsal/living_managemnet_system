<?php

namespace Database\Seeders;

use App\Models\PropertyType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PropertyTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            'House',
            'Flat',
            'Bungalow',
            'Maisonette',
            'Studio',
            'Terraced House',
            'Semi-Detached House',
            'Detached House',
        ];

        foreach ($types as $index => $name) {
            PropertyType::query()->updateOrCreate(
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
