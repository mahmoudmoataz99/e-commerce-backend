<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PromoCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    // GET /api/orders
    // Gets the order history for the "My Orders" screen, newest first.
    public function index(Request $request)
    {
        $userId = $request->user()->id;

        $orders = Order::with('items')
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($orders);
    }

    // GET /api/orders/{id}
    // Used for the order confirmation screen / order details screen.
    public function show(Request $request, $id)
    {
        $userId = $request->user()->id;

        $order = Order::with('items')
            ->where('user_id', $userId)
            ->find($id);

        if (! $order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        return response()->json($order);
    }

    // POST /api/orders
    // This is the "place order" button on checkout.
    //
    // Important: we calculate all the money totals here on the server,
    // instead of trusting whatever numbers the app sends. That way nobody
    // can cheat the prices by editing the app or the request.
    public function store(Request $request)
    {
        $userId = $request->user()->id;

        $data = $request->validate([
            'address' => 'required|string',
            'payment_method' => 'required|string',
            'promo_code' => 'nullable|string',
            // std = standard shipping, exp = express shipping
            'shipping_method' => 'nullable|string|in:std,exp',
        ]);

        $cartItems = CartItem::with('product')
            ->where('user_id', $userId)
            ->get();

        if ($cartItems->isEmpty()) {
            return response()->json(['message' => 'Your cart is empty'], 422);
        }

        // Step 1: add up the price of everything in the cart.
        $subtotal = 0;
        foreach ($cartItems as $cartItem) {
            $subtotal = $subtotal + ($cartItem->product->price * $cartItem->quantity);
        }

        // Step 2: tax. Just a flat 10%, same as before.
        $tax = round($subtotal * 0.1, 2);

        // Step 3: promo code discount, if they used one.
        $discount = 0;
        $promoCodeUsed = null;

        if (! empty($data['promo_code'])) {
            $promo = PromoCode::where('code', strtoupper($data['promo_code']))
                ->where('is_active', true)
                ->first();

            if ($promo) {
                $discount = round($subtotal * ($promo->discount_percent / 100), 2);
                $promoCodeUsed = $promo->code;
            }
            // If the code turned out to be invalid, we just quietly ignore
            // it here. The app should already have checked it with
            // /api/promo-codes/check before letting the user get this far.
        }

        // Step 4: shipping cost. Express costs extra, standard is free.
        $shippingMethod = $data['shipping_method'] ?? 'std';
        $shippingCost = 0;
        if ($shippingMethod === 'exp') {
            $shippingCost = 9.99;
        }

        // Step 5: the final total.
        $total = $subtotal + $tax - $discount + $shippingCost;

        // Everything below happens inside one database transaction. That
        // way, if anything fails partway through, we don't end up with a
        // half-created order sitting in the database.
        $order = DB::transaction(function () use (
            $data,
            $cartItems,
            $subtotal,
            $tax,
            $discount,
            $shippingCost,
            $total,
            $promoCodeUsed,
            $userId
        ) {
            $newOrder = Order::create([
                'user_id' => $userId,
                // A simple, human friendly order number like "ORD-A1B2C3D4".
                'order_number' => 'ORD-' . strtoupper(substr(uniqid(), -8)),
                'subtotal' => $subtotal,
                'tax' => $tax,
                'discount' => $discount,
                'shipping_cost' => $shippingCost,
                'total' => $total,
                'address' => $data['address'],
                'payment_method' => $data['payment_method'],
                'promo_code' => $promoCodeUsed,
                'status' => 'processing',
            ]);

            // Copy every cart item into an order item. We save a snapshot
            // of the product's name/image/price so the order history still
            // looks right even if the product changes or gets deleted later.
            foreach ($cartItems as $cartItem) {
                OrderItem::create([
                    'order_id' => $newOrder->id,
                    'product_id' => $cartItem->product_id,
                    'product_name' => $cartItem->product->name,
                    'product_image' => $cartItem->product->image,
                    'price' => $cartItem->product->price,
                    'quantity' => $cartItem->quantity,
                    'selected_size' => $cartItem->selected_size,
                    'selected_color' => $cartItem->selected_color,
                ]);
            }

            // Now that the order is placed, empty the cart.
            CartItem::where('user_id', $userId)->delete();

            return $newOrder;
        });

        $order->load('items');

        return response()->json($order, 201);
    }
}
