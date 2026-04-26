<?php

namespace App\Traits;

use App\Models\Product;
use Illuminate\Support\Str;

trait GenerateProductSlug
{
    public function slug(string $name): string
    {
        do {
            $slug = Str::slug($name).'-'.Str::random(8);
        } while (
            Product::query()->where('slug', $slug)->exists()
        );

        return $slug;
    }
}
