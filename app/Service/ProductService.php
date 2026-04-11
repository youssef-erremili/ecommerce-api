<?php

namespace App\Service;

use App\Models\Product;
use App\Models\User;
use App\Traits\FileManager;
use App\Traits\GenerateProductSlug;

class ProductService
{
    use FileManager;
    use GenerateProductSlug;

    /**
     * Create a new product instance.
     */
    public function create(User $user, array $data): Product
    {
        // 1 handle image upload
        $data['product_images'] = $this->upload($data['product_images']);
        $data['slug'] = $this->slug($data['product_name']);

        // 2 store product in db
        return $user->products()
            ->create($data)
            ->load(['user', 'category']);
    }
}
