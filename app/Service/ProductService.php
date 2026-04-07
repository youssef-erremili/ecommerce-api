<?php

namespace App\Service;

use App\Models\User;
use App\Traits\HandlesImageUpload;

class ProductService
{
    use HandlesImageUpload;

    /**
     * Create a new class instance.
     */
    public function create(User $user, array $data)
    {
        // 1 handle image upload
        $images = $this->upload($data['product_images']);
        $data['product_images'] = $images;

        // 2 attach category using name

        // 3 trigger events and jobs in background

        // 4 store product in db
        return $user->products()
            ->create($data)
            ->load(['user', 'category']);
    }
}
