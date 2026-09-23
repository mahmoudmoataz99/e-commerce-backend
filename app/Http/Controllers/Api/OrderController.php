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
    // gets the order history for the "My Orders" screen, newest first
    public function index(Request $request)
    {
        $orders = Order::with('items')
            ->where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($orders);
    }

    // GET /api/orders/{id}
    // used for the order-confirmation screen / order details
    public function show(Request $request, $id)
    {
        $order = Order::with('items')
            ->where('user_id', $request->user()->id)
            ->find($id);

        if (! $order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        return response()->json($order);
    }

    // POST /api/orders
    // this is the "place order" button on checkout.
    // I am calculating the totals here on the server instead of trusting whatever
    // the app sends, that way people cant cheat the prices by editing the app
    public function store(Request $request)
    {
        $data = $request->validate([
            'address' => 'required|string',
            'payment_method' => 'required|string',
            'promo_code' => 'nullable|string',
            'shipping_method' => 'nullable|string|in:std,exp', // std = standard, exp = express
        ]);

        $cartItems = CartItem::with('product')
            ->where('user_id', $request->user()->id)
            ->get();

        if ($cartItems->isEmpty()) {
            return response()->json(['message' => 'Your cart is empty'], 422);
        }

        // add up the price of everything in the cart
        $subtotal = 0;
        foreach ($cartItems as $cartItem) {
            $subtotal += $cartItem->product->price * $cartItem->quantity;
        }

        // tax is just a flat 10% like it was in the app before
        $tax = round($subtotal * 0.1, 2);

        // check the promo code if they gave one
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
            // if the code was invalid we just silently ignore it here,
            // the frontend should already have validated it with /api/promo-codes/check first
        }

        // express shipping costs extra, standard is free
        $shippingCost = ($data['shipping_method'] ?? 'std') === 'exp' ? 9.99 : 0;

        $total = $subtotal + $tax - $discount + $shippingCost;

        // wrap this in a transaction so if anything fails halfway through,
        // we dont end up with a half created order in the database
        $order = DB::transaction(function () use ($data, $cartItems, $subtotal, $tax, $discount, $shippingCost, $total, $promoCodeUsed, $request) {
            $order = Order::create([
                'user_id' => $request->user()->id,
                // simple order number, similar to what the app used to generate
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

            foreach ($cartItems as $cartItem) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $cartItem->product_id,
                    'product_name' => $cartItem->product->name,
                    'product_image' => $cartItem->product->image,
                    'price' => $cartItem->product->price,
                    'quantity' => $cartItem->quantity,
                    'selected_size' => $cartItem->selected_size,
                    'selected_color' => $cartItem->selected_color,
                ]);
            }

            // empty the cart now that the order has been placed
            CartItem::where('user_id', $request->user()->id)->delete();

            return $order;
        });

        $order->load('items');

        return response()->json($order, 201);
    }
}
