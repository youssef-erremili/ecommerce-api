<?php

namespace App\Observers;

use App\Models\Product;
use App\Traits\GenerateProductSlug;

class ProductObserver
{
    use GenerateProductSlug;

    /**
     * Handle the Product "creating" event.
     */
    public function creating(Product $product): void
    {
        $product->slug = $this->slug($product->product_name);
    }

    /**
     * Handle the Product "updating" event.
     */
    public function updating(Product $product): void
    {
        if ($product->isDirty('product_name')) {
            $product->slug = $this->slug($product->product_name);
        }
    }

    public function deleted(Product $product): void {}
}
