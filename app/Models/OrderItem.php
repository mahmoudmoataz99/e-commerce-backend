<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// This is basically a snapshot copy of a cart item, saved at the moment
// the order was placed, so order history stays correct even if the
// product's price/name/image changes (or gets deleted) later.
class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'product_id',
        'product_name',
        'product_image',
        'price',
        'quantity',
        'selected_size',
        'selected_color',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
