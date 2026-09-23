<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    // GET /api/profile
    public function show(Request $request)
    {
        return response()->json($request->user());
    }

    // PUT /api/profile
    // Lets the logged in user update their own name / email / phone.
    public function update(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            // Make sure the new email isn't already used by someone else,
            // but don't complain if it's just their own current email.
            'email' => [
                'sometimes',
                'string',
                'email',
                Rule::unique('users')->ignore($user->id),
            ],
            'phone' => ['nullable', 'string', 'max:30'],
        ]);

        $user->update($data);

        return response()->json($user->fresh());
    }
}
