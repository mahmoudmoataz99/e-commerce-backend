<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'price',
        'original_price',
        'image',
        'category_id',
        'description',
        'rating',
        'review_count',
        'in_stock',
        'tags',
        'sizes',
        'colors',
    ];

    // this tells laravel to automatically turn these into the right php type
    // so tags/sizes/colors come back as real arrays, not json strings
    // and in_stock comes back as true/false not 1/0
    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'original_price' => 'decimal:2',
            'rating' => 'decimal:1',
            'in_stock' => 'boolean',
            'tags' => 'array',
            'sizes' => 'array',
            'colors' => 'array',
        ];
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
