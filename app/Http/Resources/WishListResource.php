<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WishListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'products' => [
                'id' => $this->product->id,
                'product_name' => $this->product->product_name,
                'price' => $this->product->price,
                'product_images' => $this->product->product_images[0],
            ],
        ];
    }
}
