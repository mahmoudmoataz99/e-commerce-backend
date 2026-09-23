<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// A Category is something like "Tops", "Bottoms", "Footwear", etc.
class Category extends Model
{
    protected $fillable = ['slug', 'name', 'icon'];

    // One category can have many products.
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
