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

    // This tells Laravel to automatically convert these columns into the
    // right PHP type when we read the model, and back when we save it.
    // - tags / sizes / colors: stored as JSON text in the DB, but we want
    //   them to come back out as normal PHP arrays.
    // - in_stock: stored as 0/1, but we want true/false.
    // - price fields: kept as strings with 2 decimal places so we don't get
    //   weird float rounding.
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

    // A product belongs to one category.
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
