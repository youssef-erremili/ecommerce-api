<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

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
        $name = fake()->name;

        return [
            'user_id' => fake()->randomElement(User::where('account_type', 'vendor')->pluck('id')->toArray()),
            'category_id' => fake()->randomElement(Category::where('is_active', true)->pluck('id')->toArray()),
            'product_name' => $name,
            'slug' => Str::lower(Str::slug($name.'-'.fake()->slug(1))),
            'description' => fake()->realText(),
            'price' => fake()->randomNumber(2, 2),
            'quantity' => fake()->randomNumber(2, true),
            'discount' => fake()->randomNumber(2, true),
            'is_active' => fake()->boolean(100),
            'created_at' => fake()->date(),
            'product_images' => [
                'https://qxtagomijcgmgxfzpnmg.storage.supabase.co/storage/v1/s3/ProductsAssets/images/Y6cjhyG5frLTbgHXoWEz2si4R2jcefRtNwUbRZVO.png',
                'https://qxtagomijcgmgxfzpnmg.storage.supabase.co/storage/v1/s3/ProductsAssets/images/AkSR6V7319BdSyKHBoIGZZWRlRcF62SsKe6EmEPR.png',
            ],
        ];
    }
}
