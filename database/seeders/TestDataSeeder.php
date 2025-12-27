<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Product;
use App\Models\Category;

class TestDataSeeder extends Seeder
{
    public function run(): void
    {
        // Create seller
        $seller = User::firstOrCreate(
            ['email' => 'seller@example.com'],
            [
                'name' => 'Test Seller',
                'password' => bcrypt('password123'),
                'role' => 'seller',
                'balance' => 100
            ]
        );

        // Create products
        Product::firstOrCreate(
            ['slug' => 'iphone-15-pro'],
            [
                'user_id' => $seller->id,
                'name' => 'iPhone 15 Pro',
                'description' => 'Latest Apple smartphone with A17 chip and titanium design',
                'price' => 999.99,
                'discount_price' => 899.99,
                'stock' => 10,
                'category_id' => 1,
                'status' => 'active'
            ]
        );

        Product::firstOrCreate(
            ['slug' => 'samsung-galaxy-s24'],
            [
                'user_id' => $seller->id,
                'name' => 'Samsung Galaxy S24',
                'description' => 'Flagship Android phone with AI features',
                'price' => 799.99,
                'stock' => 15,
                'category_id' => 1,
                'status' => 'active'
            ]
        );

        Product::firstOrCreate(
            ['slug' => 'nike-air-max'],
            [
                'user_id' => $seller->id,
                'name' => 'Nike Air Max',
                'description' => 'Classic running shoes with Air cushioning',
                'price' => 129.99,
                'stock' => 50,
                'category_id' => 2,
                'status' => 'active'
            ]
        );

        // Update test buyer balance
        User::where('email', 'test@example.com')->update(['balance' => 2000]);

        echo "Test data created!\n";
    }
}
