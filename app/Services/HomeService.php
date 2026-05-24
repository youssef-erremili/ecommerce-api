<?php

namespace App\Services;

use App\Contracts\Services\HomeServiceInterface;
use App\Models\Product;
use App\Models\User;
use App\Support\ApiMessages;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class HomeService implements HomeServiceInterface
{
    /**
     * @throws Exception
     */
    public function index(int $perPage = 30): LengthAwarePaginator
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
     * @return Collection one product only
     *
     * @throws Exception
     */
    public function getProduct(string $slug): Collection
    {
        $holder = Product::query()->where('slug', $slug)->get();

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
}
