<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    // this data is copied straight from the react native app's data/products.ts
    // file, so the app will look exactly the same once its wired up to the api
    public function run(): void
    {
        $products = [
            [
                'name' => 'Classic White Tee',
                'price' => 29.99,
                'original_price' => 45.00,
                'image' => 'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?w=400',
                'category' => 'tops',
                'description' => 'A timeless white t-shirt made from 100% organic cotton. Super soft, breathable, and perfect for any casual look.',
                'rating' => 4.5,
                'review_count' => 128,
                'in_stock' => true,
                'tags' => ['new', 'sale'],
                'sizes' => ['XS', 'S', 'M', 'L', 'XL'],
                'colors' => ['White', 'Black', 'Gray'],
            ],
            [
                'name' => 'Slim Fit Chinos',
                'price' => 59.99,
                'original_price' => null,
                'image' => 'https://images.unsplash.com/photo-1473966968600-fa801b869a1a?w=400',
                'category' => 'bottoms',
                'description' => 'These slim-fit chinos are comfortable and stylish. Great for work or casual outings.',
                'rating' => 4.3,
                'review_count' => 85,
                'in_stock' => true,
                'tags' => ['bestseller'],
                'sizes' => ['28', '30', '32', '34', '36'],
                'colors' => ['Beige', 'Navy', 'Olive'],
            ],
            [
                'name' => 'Oversized Hoodie',
                'price' => 79.99,
                'original_price' => 99.99,
                'image' => 'https://images.unsplash.com/photo-1556821840-3a63f15732ce?w=400',
                'category' => 'tops',
                'description' => 'Stay cozy in this oversized hoodie. Perfect for lounging or layering on cooler days.',
                'rating' => 4.7,
                'review_count' => 210,
                'in_stock' => true,
                'tags' => ['new', 'sale'],
                'sizes' => ['S', 'M', 'L', 'XL', 'XXL'],
                'colors' => ['Gray', 'Black', 'Cream'],
            ],
            [
                'name' => 'Leather Jacket',
                'price' => 199.99,
                'original_price' => null,
                'image' => 'https://images.unsplash.com/photo-1551028719-00167b16eac5?w=400',
                'category' => 'outerwear',
                'description' => 'A classic leather jacket that never goes out of style. Made from genuine leather with a sleek finish.',
                'rating' => 4.8,
                'review_count' => 63,
                'in_stock' => true,
                'tags' => ['premium'],
                'sizes' => ['S', 'M', 'L', 'XL'],
                'colors' => ['Black', 'Brown'],
            ],
            [
                'name' => 'Canvas Tote Bag',
                'price' => 24.99,
                'original_price' => null,
                'image' => 'https://images.unsplash.com/photo-1544816155-12df9643f363?w=400',
                'category' => 'accessories',
                'description' => 'A spacious canvas tote bag. Eco-friendly and perfect for everyday use.',
                'rating' => 4.2,
                'review_count' => 44,
                'in_stock' => true,
                'tags' => ['eco'],
                'sizes' => null,
                'colors' => ['Natural', 'Black', 'Navy'],
            ],
            [
                'name' => 'Running Sneakers',
                'price' => 89.99,
                'original_price' => 120.00,
                'image' => 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=400',
                'category' => 'footwear',
                'description' => 'Lightweight running sneakers built for performance and style. Cushioned sole for all-day comfort.',
                'rating' => 4.6,
                'review_count' => 172,
                'in_stock' => true,
                'tags' => ['sale', 'bestseller'],
                'sizes' => ['38', '39', '40', '41', '42', '43', '44'],
                'colors' => ['White/Red', 'Black/White', 'Blue/Gray'],
            ],
            [
                'name' => 'Denim Jacket',
                'price' => 119.99,
                'original_price' => null,
                'image' => 'https://images.unsplash.com/photo-1544022613-e87ca75a784a?w=400',
                'category' => 'outerwear',
                'description' => 'A rugged denim jacket that pairs well with everything. Vintage wash finish for a classic look.',
                'rating' => 4.4,
                'review_count' => 91,
                'in_stock' => false,
                'tags' => ['vintage'],
                'sizes' => ['S', 'M', 'L', 'XL'],
                'colors' => ['Light Blue', 'Dark Blue'],
            ],
            [
                'name' => 'Floral Summer Dress',
                'price' => 54.99,
                'original_price' => 74.99,
                'image' => 'https://images.unsplash.com/photo-1572804013309-59a88b7e92f1?w=400',
                'category' => 'tops',
                'description' => 'A breezy floral dress perfect for summer days. Lightweight fabric with a flattering cut.',
                'rating' => 4.5,
                'review_count' => 138,
                'in_stock' => true,
                'tags' => ['new', 'sale'],
                'sizes' => ['XS', 'S', 'M', 'L'],
                'colors' => ['Pink Floral', 'Blue Floral'],
            ],
            [
                'name' => 'Wool Beanie',
                'price' => 19.99,
                'original_price' => null,
                'image' => 'https://images.unsplash.com/photo-1576871337622-98d48d1cf531?w=400',
                'category' => 'accessories',
                'description' => 'A warm wool beanie to keep you cozy in colder months. One size fits most.',
                'rating' => 4.1,
                'review_count' => 56,
                'in_stock' => true,
                'tags' => [],
                'sizes' => null,
                'colors' => ['Gray', 'Black', 'Red', 'Navy'],
            ],
            [
                'name' => 'High-Waist Jeans',
                'price' => 69.99,
                'original_price' => null,
                'image' => 'https://images.unsplash.com/photo-1541099649105-f69ad21f3246?w=400',
                'category' => 'bottoms',
                'description' => 'Flattering high-waist jeans with a modern straight cut. Made from premium stretch denim.',
                'rating' => 4.6,
                'review_count' => 203,
                'in_stock' => true,
                'tags' => ['bestseller'],
                'sizes' => ['24', '26', '28', '30', '32'],
                'colors' => ['Light Wash', 'Dark Wash', 'Black'],
            ],
        ];

        foreach ($products as $product) {
            $category = Category::where('slug', $product['category'])->first();

            Product::updateOrCreate(
                ['slug' => Str::slug($product['name'])],
                [
                    'name' => $product['name'],
                    'price' => $product['price'],
                    'original_price' => $product['original_price'],
                    'image' => $product['image'],
                    'category_id' => $category->id,
                    'description' => $product['description'],
                    'rating' => $product['rating'],
                    'review_count' => $product['review_count'],
                    'in_stock' => $product['in_stock'],
                    'tags' => $product['tags'],
                    'sizes' => $product['sizes'],
                    'colors' => $product['colors'],
                ]
            );
        }
    }
}
