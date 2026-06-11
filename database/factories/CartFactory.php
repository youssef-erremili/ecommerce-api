<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CartFactory extends Factory
{
    public function definition(): array
    {
        $vendorIds = User::where('account_type', 'vendor')->pluck('id')->toArray();
        $productIds = Product::where('is_active', true)->pluck('id')->toArray();

        if (empty($vendorIds)) {
            $vendorIds = [User::factory()->create(['account_type' => 'vendor'])->id];
        }
        if (empty($productIds)) {
            $productIds = [Product::factory()->create(['is_active' => true])->id];
        }

        return [
            'user_id' => fn () => fake()->randomElement($vendorIds),
            'product_id' => fn () => fake()->randomElement($productIds),
            'quantity' => $this->faker->numberBetween(1, 5),
        ];
    }
}
