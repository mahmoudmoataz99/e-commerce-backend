<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'order_number',
        'subtotal',
        'tax',
        'discount',
        'shipping_cost',
        'total',
        'address',
        'payment_method',
        'promo_code',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Every order has a bunch of order items (the products that were bought).
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
