<?php

namespace App\Contracts\Services;

use App\Models\Category;
use Illuminate\Support\Collection;

interface CategoryServiceInterface
{
    public function getCategories(): Collection;

    public function create(array $data): Category;

    public function delete(Category $category): bool;

    public function update(Category $category): bool;

    public function toggleStatus(Category $category): bool;
}
