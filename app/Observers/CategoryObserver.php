<?php

namespace App\Observers;

use App\Models\Category;
use App\Traits\GenerateSlug;

class CategoryObserver
{
    use GenerateSlug;

    public function creating(Category $category): void
    {
        $category->slug = $this->slug($category->category_name, Category::class);
    }

    public function updating(Category $category): void
    {
        if ($category->isDirty('category_name')) {
            $category->slug = $this->slug($category->category_name, Category::class);
        }
    }
}
