<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'user_id',
    'category_id',
    'product_name',
    'slug',
    'description',
    'price',
    'quantity',
    'discount',
    'is_active',
    'product_images',
])]
class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected function casts(): array
    {
        return [[
            'email_verified_at' => 'datetime',
            'is_active' => 'boolean',
            'price' => 'decimal:2',
            'discount' => 'decimal:2',
            'quantity' => 'integer',
            'product_image' => 'array',
        ]];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
