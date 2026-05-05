<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Sanctum\PersonalAccessToken;

/**
 * @method static OwnedByUser()
 * @method static find(int $productId)
 * @method whereBelongsTo(Category $category)
 *
 * @mixin Builder
 *
 * @property int $id
 * @property int $user_id
 * @property int $category_id
 * @property string $product_name
 * @property string $slug
 * @property string $description
 * @property numeric $price
 * @property int $quantity
 * @property numeric $discount
 * @property bool $is_active
 * @property array<array-key, mixed>|null $product_images
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Collection<int, Cart> $carts
 * @property-read int|null $carts_count
 * @property-read Category $category
 * @property-read Collection<int, PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @property-read User|null $user
 * @property-read Collection<int, Wishlist> $wishlist
 * @property-read int|null $wishlist_count
 *
 * @method static \Database\Factories\ProductFactory factory($count = null, $state = [])
 * @method static Builder<static>|Product newModelQuery()
 * @method static Builder<static>|Product newQuery()
 * @method static Builder<static>|Product onlyTrashed()
 * @method static Builder<static>|Product query()
 * @method static Builder<static>|Product whereCategoryId($value)
 * @method static Builder<static>|Product whereCreatedAt($value)
 * @method static Builder<static>|Product whereDeletedAt($value)
 * @method static Builder<static>|Product whereDescription($value)
 * @method static Builder<static>|Product whereDiscount($value)
 * @method static Builder<static>|Product whereId($value)
 * @method static Builder<static>|Product whereIsActive($value)
 * @method static Builder<static>|Product wherePrice($value)
 * @method static Builder<static>|Product whereProductImages($value)
 * @method static Builder<static>|Product whereProductName($value)
 * @method static Builder<static>|Product whereQuantity($value)
 * @method static Builder<static>|Product whereSlug($value)
 * @method static Builder<static>|Product whereUpdatedAt($value)
 * @method static Builder<static>|Product whereUserId($value)
 * @method static Builder<static>|Product withTrashed(bool $withTrashed = true)
 * @method static Builder<static>|Product withoutTrashed()
 *
 * @mixin Eloquent
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

    public function carts(): HasMany
    {
        return $this->hasMany(Cart::class);
    }
}
