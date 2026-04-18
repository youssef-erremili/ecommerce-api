<?php

namespace App\Services;

use App\Contracts\Services\ProductServiceInterface;
use App\Jobs\DeleteProductImage;
use App\Models\Product;
use App\Models\User;
use App\Support\ApiMessages;
use App\Traits\FileManager;
use App\Traits\GenerateProductSlug;
use Exception;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;

class ProductService implements ProductServiceInterface
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

    /**
     * @throws Exception
     */
    public function create(User $user, array $data): Product
    {
        // 1 handle image upload
        $data['product_images'] = $this->upload($data['product_images']);
        $data['slug'] = $this->slug($data['product_name']);

        // 2 store product in db
        $holder = $user->products()
            ->create($data)
            ->load(['user', 'category']);

        if (! $holder) {
            throw new Exception(ApiMessages::PRODUCT_CREATION_FAILED);
        }

        return $holder;
    }

    /**
     * @throws Exception
     */
    public function destroy(Product $product): bool
    {
        $images = $product->product_images;
        $isDeleted = $product->delete();

        if (! $isDeleted) {
            Log::error('Service Error: '.ApiMessages::PRODUCT_DELETION_FAILED);
            throw new Exception(ApiMessages::AN_ERROR_OCCURRED);
        }

        if (count($images) > 0) {
            DeleteProductImage::dispatch($images);
        }

        return true;
    }

    public function paginate(int $perPage = 20): LengthAwarePaginator
    {
        return Product::OwnedByUser()->paginate($perPage);
    }

    /**
     * @throws Exception
     */
    public function update(Product $product, array $data): Product
    {
        try {
            $data['product_images'] = $this->upload($data['product_images']);
            $product->update($data);

        } catch (Exception $exception) {
            Log::error(ApiMessages::PRODUCT_NOT_FOUND.$exception->getMessage());
            throw new Exception(ApiMessages::PRODUCT_NOT_FOUND);
        }

        return $product;
    }
}
