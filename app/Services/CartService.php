<?php

namespace App\Services;

use App\Contracts\Services\CartServiceInterface;
use App\Models\Cart;
use App\Models\Product;
use App\Support\ApiMessages;
use Exception;
use Illuminate\Support\Facades\DB;
use LogicException;
use Throwable;

class CartService implements CartServiceInterface
{
    /**
     * @throws Exception
     * @throws Throwable
     */
    public function addToCart(Product $product, array $cart): Cart
    {
        $quantity = $cart['quantity'] ?? 1;

        return DB::transaction(function () use ($product, $quantity) {

            $item = auth()->user()->carts()
                ->where('product_id', $product->id)
                ->lockForUpdate()
                ->first();

            if ($item) {
                /** @var Cart $item */
                $item->increment('quantity', $quantity);

                return $item->fresh()->load('user', 'product');
            }

            return auth()->user()->carts()->create([
                'product_id' => $product->id,
                'quantity' => $quantity,
            ])->load('user', 'product');
        });
    }

    public function destroy(Cart $cart): Cart
    {
        $holder = $cart->delete();

        if (! $holder) {
            throw new LogicException(ApiMessages::AN_ERROR_OCCURRED);
        }

        return $cart;
    }
}
