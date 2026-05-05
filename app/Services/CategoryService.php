<?php

namespace App\Services;

use App\Contracts\Services\CategoryServiceInterface;
use App\Models\Category;
use App\Models\Product;
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
    protected Product $product;

    public function __construct(Product $product)
    {
        $this->product = $product;
    }

    /**
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

    public function delete(Category $category): bool {}

    /**
     * @throws Exception
     */
    public function update(Category $category, array $data): bool
    {
        // I want to prevent update category if this category belongs to any product
        $hasReservedCategory = Product::where('category_id', $category->id)->count();

        if ($hasReservedCategory > 0) {
            throw new Exception(ApiMessages::CANNOT_UPDATE_CATEGORY);
        }

        $holder = $category->update($data);

        if (! $holder) {
            throw new Exception(ApiMessages::AN_ERROR_OCCURRED);
        }

        return $holder;
    }

    /**
     * @throws Exception
     */
    public function toggleStatus(Category $category, bool $status): Category
    {
        if ($category->is_active === $status) {
            throw new Exception(ApiMessages::CANNOT_REUSE_STATUS);
        }

        $holder = $category->updateQuietly([
            'is_active' => $status,
        ]);

        if (! $holder) {
            throw new Exception(ApiMessages::AN_ERROR_OCCURRED);
        }

        return $category;
    }
}
