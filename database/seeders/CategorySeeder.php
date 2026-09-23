<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    // these match the CATEGORIES array that was hardcoded in data/products.ts
    // in the react native app. Not including "all" since thats just a filter
    // option on the frontend, not a real category
    public function run(): void
    {
        $categories = [
            ['slug' => 'tops', 'name' => 'Tops', 'icon' => 'Shirt'],
            ['slug' => 'bottoms', 'name' => 'Bottoms', 'icon' => 'User'],
            ['slug' => 'outerwear', 'name' => 'Outerwear', 'icon' => 'Shirt'],
            ['slug' => 'accessories', 'name' => 'Accessories', 'icon' => 'ShoppingBag'],
            ['slug' => 'footwear', 'name' => 'Footwear', 'icon' => 'Footprints'],
        ];

        foreach ($categories as $category) {
            // updateOrCreate so running the seeder twice doesnt make duplicates
            Category::updateOrCreate(['slug' => $category['slug']], $category);
        }
    }
}
