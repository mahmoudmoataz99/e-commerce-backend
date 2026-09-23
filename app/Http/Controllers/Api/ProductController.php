<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    // GET /api/products
    // Supports optional query params: ?category=tops  ?search=hoodie  ?tag=sale
    // This feeds the shop screen and the search bar.
    public function index(Request $request)
    {
        $query = Product::with('category');

        // If they picked a category (and it's not "all"), only show
        // products in that category.
        if ($request->has('category') && $request->category !== 'all') {
            $categorySlug = $request->category;

            $query->whereHas('category', function ($categoryQuery) use ($categorySlug) {
                $categoryQuery->where('slug', $categorySlug);
            });
        }

        // Simple search by product name. Nothing fancy, just a "like" match.
        if ($request->filled('search')) {
            $searchText = $request->search;
            $query->where('name', 'like', '%' . $searchText . '%');
        }

        // Filter by a tag like "sale", "new" or "bestseller".
        // Used for things like the featured products / sale products section.
        if ($request->filled('tag')) {
            $tag = $request->tag;
            $query->whereJsonContains('tags', $tag);
        }

        $products = $query->orderBy('id')->get();

        return response()->json($products);
    }

    // GET /api/products/{id}
    public function show($id)
    {
        $product = Product::with('category')->find($id);

        if (! $product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        return response()->json($product);
    }
}
