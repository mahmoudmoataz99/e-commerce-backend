<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use Illuminate\Http\Request;

class CartController extends Controller
{
    // GET /api/cart
    // Gets every cart item for the logged in user, with the product info
    // (and the product's category) attached so the app doesn't have to
    // make extra requests.
    public function index(Request $request)
    {
        $userId = $request->user()->id;

        $items = CartItem::with('product.category')
            ->where('user_id', $userId)
            ->get();

        return response()->json($items);
    }

    // POST /api/cart
    // Adds a product to the cart. If the exact same product + size + color
    // is already in the cart, we just increase the quantity instead of
    // creating a duplicate row.
    public function store(Request $request)
    {
        $userId = $request->user()->id;

        $data = $request->validate([
            'product_id' => 'required|exists:products,id',
            'selected_size' => 'nullable|string',
            'selected_color' => 'nullable|string',
            'quantity' => 'nullable|integer|min:1',
        ]);

        $quantityToAdd = $data['quantity'] ?? 1;
        $size = $data['selected_size'] ?? null;
        $color = $data['selected_color'] ?? null;

        // See if this exact combo is already in the cart.
        $existingItem = CartItem::where('user_id', $userId)
            ->where('product_id', $data['product_id'])
            ->where('selected_size', $size)
            ->where('selected_color', $color)
            ->first();

        if ($existingItem) {
            $existingItem->quantity = $existingItem->quantity + $quantityToAdd;
            $existingItem->save();
            $item = $existingItem;
        } else {
            $item = CartItem::create([
                'user_id' => $userId,
                'product_id' => $data['product_id'],
                'selected_size' => $size,
                'selected_color' => $color,
                'quantity' => $quantityToAdd,
            ]);
        }

        $item->load('product.category');

        return response()->json($item, 201);
    }

    // PUT /api/cart/{id}
    // Used when the user taps the + / - buttons to change the quantity.
    public function update(Request $request, $id)
    {
        $userId = $request->user()->id;

        $item = CartItem::where('user_id', $userId)->find($id);

        if (! $item) {
            return response()->json(['message' => 'Cart item not found'], 404);
        }

        $data = $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $item->quantity = $data['quantity'];
        $item->save();
        $item->load('product.category');

        return response()->json($item);
    }

    // DELETE /api/cart/{id}
    public function destroy(Request $request, $id)
    {
        $userId = $request->user()->id;

        $item = CartItem::where('user_id', $userId)->find($id);

        if (! $item) {
            return response()->json(['message' => 'Cart item not found'], 404);
        }

        $item->delete();

        return response()->json(['message' => 'Item removed from cart']);
    }

    // DELETE /api/cart
    // Empties the whole cart. Used after checkout, or from a "clear cart" button.
    public function clear(Request $request)
    {
        $userId = $request->user()->id;

        CartItem::where('user_id', $userId)->delete();

        return response()->json(['message' => 'Cart cleared']);
    }
}
