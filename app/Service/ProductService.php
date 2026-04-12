<?php

namespace App\Service;

use App\Jobs\DeleteProductImage;
use App\Models\Product;
use App\Models\User;
use App\Traits\FileManager;
use App\Traits\GenerateProductSlug;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class ProductService
{
    use AuthorizesRequests;
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

    public function destroy(Product $product): bool
    {
        try {
            $images = $product->product_images;
            $holder = $product->delete();
            if ($holder) {
                if (! empty($images)) {
                    DeleteProductImage::dispatch($images);
                }

                return true;
            }
        } catch (\Exception $exception) {
            Log::error('Service Error: Product deletion failed. '.$exception->getMessage());
        }

        return false;
    }

    public function show(int|string $id): Collection
    {
        return Product::where('user_id', $id)->latest()->get();
    }

    public function update(Product $product, array $data): Product
    {
        $data['product_images'] = $this->upload($data['product_images']);
        $product->update($data);

        return $product;
    }
}
