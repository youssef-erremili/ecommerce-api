<?php

namespace App\Services;

use App\Contracts\Services\HomeServiceInterface;
use App\Models\Product;
use App\Models\User;
use App\Support\ApiMessages;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

class HomeService implements HomeServiceInterface
{
    /**
     * @throws Exception
     */
    public function index(int $perPage = 30): LengthAwarePaginator
    {
        $holder = Product::query()
            ->where('is_active', true)
            ->whereHas('user', function (Builder|User $query) {
                $query->active();
            })
            ->whereHas('user', function (Builder|User $query) {
                $query->topTierSeller();
            })
            ->latest()
            ->paginate($perPage);

        if (! $holder) {
            throw new Exception(ApiMessages::AN_ERROR_OCCURRED);
        }

        return $holder;
    }
}
