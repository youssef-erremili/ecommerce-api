<?php

namespace App\Contracts\Services;

use App\Models\Product;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

interface ProductServiceInterface
{
    public function create(User $user, array $data): Product;

    public function paginate(int $perPage = 20): LengthAwarePaginator;

    public function update(Product $product, array $data): Product;

    public function destroy(Product $product): bool;

    public function uploadImages(Product $product, array $images): Product;
}
