<?php

namespace App\Contracts\Services;

use App\Models\Cart;
use App\Models\Product;

interface CartServiceInterface
{
    public function addToCart(Product $product, array $cart): Cart;

    public function destroy(Cart $cart): Cart;
}
