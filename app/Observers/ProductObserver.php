<?php

namespace App\Observers;

use App\Models\Product;
use App\Traits\GenerateSlug;

class ProductObserver
{
    use GenerateSlug;

    /**
     * Handle the Product "creating" event.
     */
    public function creating(Product $product): void
    {
        $product->slug = $this->slug($product->product_name, Product::class);
    }

    /**
     * Handle the Product "updating" event.
     */
    public function updating(Product $product): void
    {
        if ($product->isDirty('product_name')) {
            $product->slug = $this->slug($product->product_name, Product::class);
        }
    }
}
