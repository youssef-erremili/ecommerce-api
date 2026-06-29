<?php

namespace App\Services;

use App\Contracts\Services\HomeServiceInterface;
use App\Enums\AccountType;
use App\Models\Product;
use App\Models\User;
use App\Support\ApiMessages;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Laravel\Scout\Builder as ScoutBuilder;

class HomeService implements HomeServiceInterface
{
    /**
     * @throws Exception
     */
    public function paginate(int $perPage = 30): LengthAwarePaginator
    {
        $holder = Product::query()
            ->with('user', 'category')
            ->where('is_active', true)
            ->whereHas('user', function (Builder|User $query) {
                $query->active();
            })
            ->whereHas('user', function (Builder|User $query) {
                $query->topTierSeller();
            })
            ->whereHas('category', function (Builder $query) {
                $query->where('is_active', true);
            })
            ->latest()
            ->paginate($perPage);

        if (! $holder) {
            throw new Exception(ApiMessages::AN_ERROR_OCCURRED);
        }

        return $holder;
    }

    /**
     * @return Product one product only
     *
     * @throws Exception
     */
    public function getProduct(string $slug): Product
    {
        $holder = Product::with(['user', 'category'])
            ->where('slug', $slug)
            ->first();

        if (! $holder || $holder->count() === 0) {
            throw new Exception(ApiMessages::PRODUCT_NOT_FOUND);
        }

        return $holder;
    }

    /**
     * @return LengthAwarePaginator user products only
     *
     * @throws Exception
     */
    public function getSellerData(string $slug, int $perPage = 12): LengthAwarePaginator
    {
        $holder = User::with(
            [
                'products' => function ($query) {
                    $query->whereHas('category', function ($q) {
                        $q->where('is_active', true);
                    });
                },
                'products.category',
            ])
            ->where('slug', $slug)
            ->paginate($perPage);

        if (! $holder) {
            throw new Exception(ApiMessages::AN_ERROR_OCCURRED);
        }

        if ($holder->count() === 0) {
            throw new Exception(ApiMessages::PRODUCT_NOT_FOUND);
        }

        return $holder;
    }

    /**
     * @throws Exception
     */
    public function search(string|int $query, int $perPage = 30): LengthAwarePaginator
    {
        $holder = Product::search($query)
            ->when(request('user'), function (ScoutBuilder $user) {
                $user->where('is_active', true);
                $user->where('account_type', AccountType::VENDOR);
            })
            ->when(request('category'), function (ScoutBuilder $category) {
                $category->where('is_active', true);
            })
            ->where('is_active', true)
            ->paginate($perPage);

        if (! $holder) {
            throw new Exception(ApiMessages::AN_ERROR_OCCURRED, 500);
        }

        if ($holder->count() === 0) {
            throw new Exception(ApiMessages::PRODUCT_NOT_FOUND, 404);
        }

        return $holder;
    }
}
