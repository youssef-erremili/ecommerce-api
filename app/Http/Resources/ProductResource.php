<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'product_name' => $this->product_name,
            'description' => $this->description,
            'price' => $this->price,
            'slug' => $this->slug,
            'discount' => $this->discount,
            'quantity' => $this->quantity,
            'product_images' => collect($this->product_images)->map(function ($image) {
                return $image;
            }),
            'vendor' => [
                'first_name' => $this->user->first_name,
                'last_name' => $this->user->last_name,
                'email' => $this->user->email,
            ],
            'category' => [
                'category' => $this->category->category_name ?? null,
                'category_slug' => $this->category->category_slug ?? null,
            ],
        ];
    }
}
