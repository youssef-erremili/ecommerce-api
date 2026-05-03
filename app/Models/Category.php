<?php

namespace App\Models;

use App\Traits\GenerateSlug;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'category_name',
    'slug',
    'description',
    'sort_order',
    'is_active',
])]
class Category extends Model
{
    use GenerateSlug;

    private const int SORT_ORDER_INCREMENT = 1;

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    protected function setSortOrder(): void
    {
        $this->sort_order = (self::max('sort_order') ?? 0) + self::SORT_ORDER_INCREMENT;
    }

    protected static function booted(): void
    {
        static::creating(function (Category $category) {
            $category->slug = $category->slug($category->category_name, Category::class);
            $category->setSortOrder();
        });
    }
}
