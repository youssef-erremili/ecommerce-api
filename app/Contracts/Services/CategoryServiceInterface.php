<?php

namespace App\Contracts\Services;

use App\Models\Category;
use Illuminate\Pagination\LengthAwarePaginator;

interface CategoryServiceInterface
{
    public function paginate(int $perPage = 20): LengthAwarePaginator;

    public function create(array $data): Category;

    public function delete(Category $category): bool;

    public function update(Category $category): bool;

    public function toggleStatus(Category $category): bool;
}
