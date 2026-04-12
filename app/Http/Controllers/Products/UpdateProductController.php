<?php

namespace App\Http\Controllers\Products;

use App\Http\Controllers\Controller;
use App\Http\Requests\Products\UpdateProductRequest;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class UpdateProductController extends Controller
{
    /**
     * Handle the incoming request.
     */
    use AuthorizesRequests;

    public function __invoke(UpdateProductRequest $request)
    {
        //        $this->authorize('view', $product);
        //        $service->update();
    }
}
