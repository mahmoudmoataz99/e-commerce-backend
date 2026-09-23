<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PromoCode;
use Illuminate\Http\Request;

class PromoCodeController extends Controller
{
    // POST /api/promo-codes/check
    // used on the checkout screen when they type a code and hit "Apply"
    public function check(Request $request)
    {
        $data = $request->validate([
            'code' => 'required|string',
        ]);

        // always check uppercase since thats how we save them, so
        // "save10" and "SAVE10" both work the same
        $code = strtoupper($data['code']);

        $promo = PromoCode::where('code', $code)->where('is_active', true)->first();

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
