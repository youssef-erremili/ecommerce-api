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
    public function __construct(
        protected Product $product,
        protected Category $category
    ) {}

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

    /**
     * @throws Exception
     */
    public function delete(Category $category): bool
    {
        $this->deleteOrUpdate('delete', $category);

        $holder = $category->delete();

        if (! $holder) {
            throw new Exception(ApiMessages::AN_ERROR_OCCURRED);
        }

        return $holder;
    }

    /**
     * @throws Exception
     */
    public function update(Category $category, array $data): bool
    {
        // I want to prevent update category if this category belongs to any product
        $this->deleteOrUpdate('update', $category);

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

    private function deleteOrUpdate(string $action, Category $category): void
    {
        $message = ($action === 'update')
                ? ApiMessages::CANNOT_UPDATE_CATEGORY
                : ApiMessages::CANNOT_DELETE_CATEGORY;

        // I want to prevent UPDATE or DELETE category if this category belongs to any product
        $hasReservedCategory = Product::whereBelongsTo($category)->count();

        if ($hasReservedCategory > 0) {
            abort(422, $message);
        }
    }
}
