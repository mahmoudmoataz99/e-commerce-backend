<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// NOTE: this is just a simple "saved card label" table for demo purposes.
// It does NOT store real card numbers - only a label and the last 4 digits
// so the app can show something like "Visa •••• 4242".
class PaymentMethod extends Model
{
    protected $fillable = ['user_id', 'label', 'last4', 'type', 'is_default'];

    protected function casts(): array
    {
        return [
            'is_default' => 'boolean',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
