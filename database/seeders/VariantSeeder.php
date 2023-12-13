<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Variant;

class VariantSeeder extends Seeder
{
    public function run(): void
    {
        // T-shirt
        Variant::create([
            'product_id' => 1,
            'title' => 'Polo T-shirt',
            'option1' => 'red',
            'option2' => 'small',
            'price' => 19.99,
            'stock' => 10,
            'is_in_stock' => true
        ]);

        Variant::create([
            'product_id' => 1,
            'title' => 'Polo T-shirt',
            'option1' => 'blue',
            'option2' => 'large',
            'price' => 19.99,
            'stock' => 10,
            'is_in_stock' => true
        ]);

        Variant::create([
            'product_id' => 1,
            'title' => 'V-Neck T-shirt',
            'option1' => 'long',
            'option2' => 'blue',
            'price' => 19.99,
            'stock' => 10,
            'is_in_stock' => true
        ]);

        Variant::create([
            'product_id' => 1,
            'title' => 'V-Neck T-shirt',
            'option1' => 'medium',
            'option2' => 'grey',
            'price' => 19.99,
            'stock' => 10,
            'is_in_stock' => true
        ]);

        // Jeans
        Variant::create([
            'product_id' => 2,
            'title' => 'Slim Fit Jeans',
            'option1' => 'large',
            'option2' => 'blue',
            'price' => 29.99,
            'stock' => 10,
            'is_in_stock' => true
        ]);

        Variant::create([
            'product_id' => 2,
            'title' => 'Regular Fit Jeans',
            'option1' => 'small',
            'option2' => 'black',
            'price' => 29.99,
            'stock' => 10,
            'is_in_stock' => true
        ]);

        // Sweatshirts

        Variant::create([
            'product_id' => 3,
            'title' => 'Crewneck Sweater',
            'option1' => 'black',
            'option2' => 'large',
            'price' => 39.99,
            'stock' => 10,
            'is_in_stock' => true
        ]);

        Variant::create([
            'product_id' => 3,
            'title' => 'V-neck Sweater',
            'option1' => 'red',
            'option2' => 'medium',
            'price' => 39.99,
            'stock' => 10,
            'is_in_stock' => true
        ]);
    }
}
