<?php

namespace App\Contracts\Services;

use App\Models\Category;

interface CategoryServiceInterface
{
    public function create(array $data): Category;

    public function delete(Category $category): bool;

    public function update(Category $category): bool;

    public function toggleStatus(Category $category): bool;
}
