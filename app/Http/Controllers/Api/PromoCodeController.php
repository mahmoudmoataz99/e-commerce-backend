<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PromoCode;
use Illuminate\Http\Request;

class PromoCodeController extends Controller
{
    // POST /api/promo-codes/check
    // Used on the checkout screen when the user types a code and hits "Apply".
    public function check(Request $request)
    {
        $data = $request->validate([
            'code' => 'required|string',
        ]);

        // Promo codes are always saved in uppercase, so we uppercase
        // whatever the user typed before looking it up. That way
        // "save10" and "SAVE10" both work the same.
        $code = strtoupper($data['code']);

        $promo = PromoCode::where('code', $code)
            ->where('is_active', true)
            ->first();

        if (! $promo) {
            return response()->json([
                'valid' => false,
                'message' => 'That promo code is not valid.',
            ], 404);
        }

        return response()->json([
            'valid' => true,
            'code' => $promo->code,
            'discount_percent' => $promo->discount_percent,
            'message' => 'You saved ' . $promo->discount_percent . '% off your order.',
        ]);
    }
}
