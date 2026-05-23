<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed $user
 * @property mixed $product_images
 * @property mixed $product_name
 * @property mixed $discount
 * @property mixed $quantity
 * @property mixed $description
 * @property mixed $slug
 * @property mixed $price
 * @property mixed $id
 * @property mixed $category
 */
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
            'vendor' => $this->whenLoaded('user', function () {
                return [
                    'full_name' => $this->user->first_name.' '.$this->user->last_name,
                    'slug' => $this->user->slug,
                    'email_address' => $this->user->email,
                ];
            }),

            'category' => $this->whenLoaded('category', function () {
                return [
                    'category' => $this->category->category_name,
                    'category_slug' => $this->category->category_slug,
                ];
            }),
        ];
    }
}
