<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\WishlistItem;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    // GET /api/wishlist
    public function index(Request $request)
    {
        $userId = $request->user()->id;

        $items = WishlistItem::with('product.category')
            ->where('user_id', $userId)
            ->get();

        return response()->json($items);
    }

    // POST /api/wishlist
    // This "toggles" the product: if it's already wishlisted, remove it.
    // If it's not wishlisted yet, add it.
    public function toggle(Request $request)
    {
        $userId = $request->user()->id;

        $data = $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $existingItem = WishlistItem::where('user_id', $userId)
            ->where('product_id', $data['product_id'])
            ->first();

        if ($existingItem) {
            $existingItem->delete();

            return response()->json([
                'message' => 'Removed from wishlist',
                'wishlisted' => false,
            ]);
        }

        $newItem = WishlistItem::create([
            'user_id' => $userId,
            'product_id' => $data['product_id'],
        ]);
        $newItem->load('product.category');

        return response()->json([
            'message' => 'Added to wishlist',
            'wishlisted' => true,
            'item' => $newItem,
        ], 201);
    }

    // DELETE /api/wishlist/{id}
    public function destroy(Request $request, $id)
    {
        $userId = $request->user()->id;

        $item = WishlistItem::where('user_id', $userId)->find($id);

        if (! $item) {
            return response()->json(['message' => 'Wishlist item not found'], 404);
        }

        $item->delete();

        return response()->json(['message' => 'Removed from wishlist']);
    }
}
