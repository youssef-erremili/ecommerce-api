<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $sort = (Category::max('sort_order') ?? 0) + 1;

        return [
            'category_name' => fake()->unique()->words(2, true),
            'description' => fake()->sentence(),
            'slug' => fake()->slug(2),
            'sort_order' => fn () => $sort,
            'is_active' => fake()->boolean(),
        ];
    }
}
