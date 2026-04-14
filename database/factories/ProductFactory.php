<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => 1,
            'category_id' => 1,
            'product_name' => fake()->name,
            'slug' => fake()->slug(7),
            'description' => fake()->realText(),
            'price' => fake()->randomNumber(),
            'quantity' => fake()->randomNumber(),
            'discount' => fake()->randomNumber(),
            'is_active' => fake()->boolean(),
            'created_at' => fake()->date(),
            'product_images' => [
                'https://qxtagomijcgmgxfzpnmg.storage.supabase.co/storage/v1/s3/ProductsAssets/images/Y6cjhyG5frLTbgHXoWEz2si4R2jcefRtNwUbRZVO.png',
                'https://qxtagomijcgmgxfzpnmg.storage.supabase.co/storage/v1/s3/ProductsAssets/images/AkSR6V7319BdSyKHBoIGZZWRlRcF62SsKe6EmEPR.png',
            ],
        ];
    }
}
