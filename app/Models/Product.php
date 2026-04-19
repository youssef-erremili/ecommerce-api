<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;

/**
 * @method static OwnedByUser()
 * @method static find(int $productId)
 */
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
    use HasApiTokens, HasFactory, SoftDeletes;

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'price' => 'decimal:2',
            'discount' => 'decimal:2',
            'quantity' => 'integer',
            'product_images' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function scopeOwnedByUser($query)
    {
        return $query->where('user_id', auth()->id());
    }

    public function wishlist(): HasMany
    {
        return $this->HasMany(Wishlist::class);
    }
}
