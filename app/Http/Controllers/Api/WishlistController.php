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
        $items = WishlistItem::with('product.category')
            ->where('user_id', $request->user()->id)
            ->get();

        return response()->json($items);
    }

    // POST /api/wishlist
    // this "toggles" - if the product is already wishlisted it removes it,
    // otherwise it adds it. Matches how toggleWishlist() worked in the app before
    public function toggle(Request $request)
    {
        $data = $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $existing = WishlistItem::where('user_id', $request->user()->id)
            ->where('product_id', $data['product_id'])
            ->first();

        if ($existing) {
            $existing->delete();

            return response()->json(['message' => 'Removed from wishlist', 'wishlisted' => false]);
        }

        $item = WishlistItem::create([
            'user_id' => $request->user()->id,
            'product_id' => $data['product_id'],
        ]);
        $item->load('product.category');

        return response()->json(['message' => 'Added to wishlist', 'wishlisted' => true, 'item' => $item], 201);
    }

    // DELETE /api/wishlist/{id}
    public function destroy(Request $request, $id)
    {
        $item = WishlistItem::where('user_id', $request->user()->id)->find($id);

        if (! $item) {
            return response()->json(['message' => 'Wishlist item not found'], 404);
        }

        $item->delete();

        return response()->json(['message' => 'Removed from wishlist']);
    }
}
