<?php

namespace App\Service;

use App\Jobs\DeleteProductImage;
use App\Models\Product;
use App\Models\User;
use App\Support\ApiMessages;
use App\Traits\FileManager;
use App\Traits\GenerateProductSlug;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;

class ProductService
{
    use AuthorizesRequests;
    use FileManager;
    use GenerateProductSlug;

    /**
     * Create a new product instance.
     */
    private Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

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

    public function show(): LengthAwarePaginator
    {
        return Product::OwnedByUser()->paginate(15);
    }

    public function update(Product $product, array $data): Product
    {
        try {
            $data['product_images'] = $this->upload($data['product_images']);
            $product->update($data);

        } catch (\Exception $exception) {
            Log::error(ApiMessages::PRODUCT_NOT_FOUND.$exception->getMessage());
        }

        return $product;
    }
}
