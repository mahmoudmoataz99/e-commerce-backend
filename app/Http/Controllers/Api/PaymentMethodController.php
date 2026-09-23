<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;

class PaymentMethodController extends Controller
{
    // GET /api/payment-methods
    public function index(Request $request)
    {
        $userId = $request->user()->id;

        $methods = PaymentMethod::where('user_id', $userId)->get();

        return response()->json($methods);
    }

    // POST /api/payment-methods
    // Again: this is NOT a real payment integration. It only stores a
    // label and the last 4 digits for display. Never send full card
    // numbers here - this is just a demo/portfolio project.
    public function store(Request $request)
    {
        $userId = $request->user()->id;

        $data = $request->validate([
            'label' => 'required|string|max:100',
            'last4' => 'nullable|string|max:4',
            'type' => 'nullable|string|in:card,apple',
            'is_default' => 'nullable|boolean',
        ]);

        if (! empty($data['is_default'])) {
            PaymentMethod::where('user_id', $userId)->update(['is_default' => false]);
        }

        $method = PaymentMethod::create([
            'user_id' => $userId,
            'label' => $data['label'],
            'last4' => $data['last4'] ?? null,
            'type' => $data['type'] ?? 'card',
            'is_default' => $data['is_default'] ?? false,
        ]);

        return response()->json($method, 201);
    }

    // PUT /api/payment-methods/{id}/default
    // Used when the user taps a saved card to make it their default one.
    public function setDefault(Request $request, $id)
    {
        $userId = $request->user()->id;

        $method = PaymentMethod::where('user_id', $userId)->find($id);

        if (! $method) {
            return response()->json(['message' => 'Payment method not found'], 404);
        }

        // Turn off "default" on everything first, then turn it on for
        // just this one.
        PaymentMethod::where('user_id', $userId)->update(['is_default' => false]);

        $method->is_default = true;
        $method->save();

        return response()->json($method);
    }

    // DELETE /api/payment-methods/{id}
    public function destroy(Request $request, $id)
    {
        $userId = $request->user()->id;

        $method = PaymentMethod::where('user_id', $userId)->find($id);

        if (! $method) {
            return response()->json(['message' => 'Payment method not found'], 404);
        }

        $method->delete();

        return response()->json(['message' => 'Payment method removed']);
    }
}
