<?php

namespace App\Services;

use App\Contracts\Services\CategoryServiceInterface;
use App\Models\Category;
use App\Support\ApiMessages;
use Exception;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class CategoryService implements CategoryServiceInterface
{
    /**
     * Create a new class instance.
     *
     * @throws Exception
     */
    public function paginate(int $perPage = 20): LengthAwarePaginator
    {
        $holder = DB::table('categories')->paginate($perPage);

        if (! $holder) {
            throw new Exception(ApiMessages::AN_ERROR_OCCURRED);
        }

        return $holder;
    }

    /**
     * @throws Exception
     */
    public function create(array $data): Category
    {
        $holder = Category::query()->create($data);

        if (! $holder) {
            throw new Exception(ApiMessages::AN_ERROR_OCCURRED);
        }

        $holder->refresh();

        return $holder;
    }

    public function delete(Category $category): bool
    {
        // TODO: Implement delete() method.
    }

    public function update(Category $category): bool
    {
        // TODO: Implement update() method.
    }

    public function toggleStatus(Category $category): bool
    {
        // TODO: Implement toggleStatus() method.
    }
}
