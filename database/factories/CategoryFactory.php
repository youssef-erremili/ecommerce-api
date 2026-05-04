<?php

namespace Database\Factories;

use App\Models\Category;
use App\Traits\GenerateSlug;
use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryFactory extends Factory
{
    use GenerateSlug;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {

        $category_name = fake()->unique()->words(3, true);
        $sort = (Category::max('sort_order') ?? 0) + 1;

        return [
            'category_name' => $category_name,
            'description' => fake()->sentence(),
            'slug' => fn () => $this->slug($category_name, Category::class),
            'sort_order' => fn () => $sort,
            'is_active' => fake()->boolean(),
        ];
    }
}
