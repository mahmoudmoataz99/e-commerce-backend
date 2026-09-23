<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    // GET /api/cart
    // gets all the cart items for the logged in user, with the product info attached
    public function index(Request $request)
    {
        $items = CartItem::with('product.category')
            ->where('user_id', $request->user()->id)
            ->get();

        return response()->json($items);
    }

    // POST /api/cart
    // adds a product to the cart. if its already in there (same product + size + color)
    // then we just add 1 to the quantity instead of making a duplicate row
    public function store(Request $request)
    {
        $data = $request->validate([
            'product_id' => 'required|exists:products,id',
            'selected_size' => 'nullable|string',
            'selected_color' => 'nullable|string',
            'quantity' => 'nullable|integer|min:1',
        ]);

        $quantity = $data['quantity'] ?? 1;

        // check if we already have this exact combo in the cart
        $existing = CartItem::where('user_id', $request->user()->id)
            ->where('product_id', $data['product_id'])
            ->where('selected_size', $data['selected_size'] ?? null)
            ->where('selected_color', $data['selected_color'] ?? null)
            ->first();

        if ($existing) {
            $existing->quantity = $existing->quantity + $quantity;
            $existing->save();
            $item = $existing;
        } else {
            $item = CartItem::create([
                'user_id' => $request->user()->id,
                'product_id' => $data['product_id'],
                'selected_size' => $data['selected_size'] ?? null,
                'selected_color' => $data['selected_color'] ?? null,
                'quantity' => $quantity,
            ]);
        }

        $item->load('product.category');

        return response()->json($item, 201);
    }

    // PUT /api/cart/{id}
    // used when they tap the + / - buttons to change quantity
    public function update(Request $request, $id)
    {
        $item = CartItem::where('user_id', $request->user()->id)->find($id);

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
        $item = CartItem::where('user_id', $request->user()->id)->find($id);

        if (! $item) {
            return response()->json(['message' => 'Cart item not found'], 404);
        }

        $item->delete();

        return response()->json(['message' => 'Item removed from cart']);
    }

    // DELETE /api/cart
    // wipes the whole cart, used after checkout is done or a "clear cart" button
    public function clear(Request $request)
    {
        CartItem::where('user_id', $request->user()->id)->delete();

        return response()->json(['message' => 'Cart cleared']);
    }
}
