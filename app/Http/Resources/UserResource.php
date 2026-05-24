<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @method getProfileImage()
 *
 * @property mixed $id
 * @property mixed $first_name
 * @property mixed $last_name
 * @property mixed $slug
 * @property mixed $physical_address
 * @property mixed $phone_number
 * @property mixed $email
 * @property mixed $email_verified_at
 * @property mixed $products
 */
class UserResource extends JsonResource
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
            'full_name' => $this->first_name.' '.$this->last_name,
            'email' => $this->email,
            'physical_address' => $this->physical_address,
            'slug' => $this->slug,
            'profile' => $this->getProfileImage(),
            'phone_number' => $this->phone_number,
            'email_verified_at' => $this->email_verified_at,
            'products' => ProductResource::collection($this->whenLoaded('products')),
        ];
    }
}
