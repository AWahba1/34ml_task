<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Option;

class OptionSeeder extends Seeder
{
    public function run(): void
    {
        Option::create([
            'product_id' => 1,
            'name' => 'color',
            'values' => ["red", "green", "blue"],
        ]);

        Option::create([
            'product_id' => 1,
            'name' => 'size',
            'values' => ['small', 'medium', 'large'],
        ]);


        Option::create([
            'product_id' => 2,
            'name' => 'size',
            'values' => ['small', 'medium'],
        ]);

        Option::create([
            'product_id' => 2,
            'name' => 'color',
            'values' => ['red', 'blue'],
        ]);

        Option::create([
            'product_id' => 3,
            'name' => 'color',
            'values' => ["green", "blue"]
        ]);

    }
}
