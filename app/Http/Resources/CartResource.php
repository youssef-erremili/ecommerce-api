<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'full name' => $this->user->first_name.' '.$this->user->last_name,
            'product name' => $this->product->product_name,
            'quantity' => $this->quantity,
            'thumbnail' => $this->product->product_images[0],
            'price' => $this->product->price,
        ];
    }
}
