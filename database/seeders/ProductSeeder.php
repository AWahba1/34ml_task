<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        Product::create([
            'title' => 'T-shirt',
            'is_in_stock' => true,
            'average_rating' => 4.5,
        ]);

        Product::create([
            'title' => 'Jeans',
            'is_in_stock' => false,
            'average_rating' => 3.0,
        ]);

        Product::create([
            'title' => 'Sweatshirt',
            'is_in_stock' => true,
            'average_rating' => 5.0,
        ]);

    }
}
