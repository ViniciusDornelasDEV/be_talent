<?php

declare(strict_types=1);

namespace Modules\Product\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Modules\Product\Http\Requests\StoreProductRequest;
use Modules\Product\Http\Requests\UpdateProductRequest;
use Modules\Product\Http\Resources\ProductResource;
use Modules\Product\Models\Product;
use Modules\Product\Services\ProductService;

class ProductController extends Controller
{
    public function __construct(
        private readonly ProductService $service,
    ) {}

    public function index(Request $request)
    {
        Gate::authorize('viewAny', Product::class);

        $products = $this->service->list();

        return ApiResponse::success(
            ProductResource::collection($products)->resolve(),
            200
        );
    }

    public function store(StoreProductRequest $request)
    {
        $product = $this->service->create($request->validated());

        return ApiResponse::success(
            ProductResource::make($product)->resolve(),
            201,
        );
    }

    public function update(UpdateProductRequest $request, Product $product)
    {
        $updated = $this->service->update($product, $request->validated());

        return ApiResponse::success(
            ProductResource::make($updated)->resolve(),
        );
    }
}

