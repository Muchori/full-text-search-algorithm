<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductTestSeeder extends Seeder
{
    public function run()
    {
        // Clear existing products
        Product::truncate();
        
        // Create test products
        $products = [
            [
                'name' => 'iPhone 13 Pro',
                'description' => 'Latest Apple smartphone with advanced camera system',
                'price' => 999.99,
                'stock' => 50
            ],
            [
                'name' => 'Samsung Galaxy S21',
                'description' => 'Android smartphone with great performance',
                'price' => 799.99,
                'stock' => 30
            ],
            [
                'name' => 'Apple MacBook Pro',
                'description' => 'Professional laptop for developers and creators',
                'price' => 1299.99,
                'stock' => 25
            ],
            [
                'name' => 'Gaming Laptop Pro',
                'description' => 'High performance laptop with RTX 3080',
                'price' => 1999.99,
                'stock' => 10
            ],
            [
                'name' => 'Wireless Pro Headphones',
                'description' => 'Noise cancelling bluetooth headphones',
                'price' => 199.99,
                'stock' => 100
            ]
        ];
        
        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
