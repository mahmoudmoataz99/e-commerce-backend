<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// One row here = one line in someone's shopping cart.
class CartItem extends Model
{
    protected $fillable = [
        'user_id',
        'product_id',
        'quantity',
        'selected_size',
        'selected_color',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
