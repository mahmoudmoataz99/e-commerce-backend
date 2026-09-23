<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;

class CategoryController extends Controller
{
    // GET /api/categories
    // Sends back the list of categories. Used for the category filter
    // chips at the top of the shop screen.
    public function index()
    {
        $categories = Category::orderBy('id')->get();

        return response()->json($categories);
    }
}
