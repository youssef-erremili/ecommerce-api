<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property-read Order|null $order
 * @property-read Product|null $product
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItems newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItems newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItems query()
 *
 * @mixin \Eloquent
 */
#[Fillable([
    'order_id',
    'product_id',
    'quantity',
    'price',
])]
class OrderItems extends Model
{
    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'quantity' => 'integer',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
