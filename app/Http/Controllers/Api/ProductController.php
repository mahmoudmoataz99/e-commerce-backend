<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    // GET /api/products
    // supports ?category=tops  and  ?search=hoodie  and  ?tag=sale
    // this is what feeds the shop screen and the search
    public function index(Request $request)
    {
        $query = Product::with('category');

        // filter by category if they gave us one and its not "all"
        if ($request->has('category') && $request->category !== 'all') {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        // simple search by name, nothing fancy like full text search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', '%' . $search . '%');
        }

        // filter by tag, like "sale" or "new" or "bestseller"
        // this is used for the FEATURED_PRODUCTS / SALE_PRODUCTS sections
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
