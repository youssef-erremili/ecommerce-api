<?php

namespace App\Contracts\Services;

use App\Models\Product;
use Illuminate\Pagination\LengthAwarePaginator;

interface HomeServiceInterface
{
    public function paginate(int $perPage = 30): LengthAwarePaginator;

    public function getProduct(string $slug): Product;

    public function getSellerData(string $slug, int $perPage = 12): LengthAwarePaginator;
}
