<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Address;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    // GET /api/addresses
    public function index(Request $request)
    {
        $addresses = Address::where('user_id', $request->user()->id)
            ->select('id', 'label', 'address', 'is_default')
            ->get();

        return response()->json($addresses);
    }

    // POST /api/addresses
    public function store(Request $request)
    {
        $data = $request->validate([
            'label' => 'required|string|max:100',
            'address' => 'required|string|max:500',
            'is_default' => 'nullable|boolean',
        ]);

        // if they want this to be default, un-default all the others first
        if (! empty($data['is_default'])) {
            Address::where('user_id', $request->user()->id)->update(['is_default' => false]);
        }

        $address = Address::create([
            'user_id' => $request->user()->id,
            'label' => $data['label'],
            'address' => $data['address'],
            'is_default' => $data['is_default'] ?? false,
        ]);

        return response()->json($address, 201);
    }

    // PUT /api/addresses/{id}
    public function update(Request $request, $id)
    {
        $address = Address::where('user_id', $request->user()->id)->find($id);

        if (! $address) {
            return response()->json(['message' => 'Address not found'], 404);
        }

        $data = $request->validate([
            'label' => 'sometimes|string|max:100',
            'address' => 'sometimes|string|max:500',
            'is_default' => 'nullable|boolean',
        ]);

        if (! empty($data['is_default'])) {
            Address::where('user_id', $request->user()->id)->update(['is_default' => false]);
        }

        $address->update($data);

        return response()->json($address);
    }

    // DELETE /api/addresses/{id}
    public function destroy(Request $request, $id)
    {
        $address = Address::where('user_id', $request->user()->id)->find($id);

        if (! $address) {
            return response()->json(['message' => 'Address not found'], 404);
        }

        $address->delete();

        return response()->json(['message' => 'Address deleted']);
    }
}
