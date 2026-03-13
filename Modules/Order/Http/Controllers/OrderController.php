<?php

declare(strict_types=1);

namespace Modules\Order\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use Modules\Order\Http\Requests\PurchaseRequest;
use Modules\Order\Services\OrderService;
use Modules\Order\Http\Resources\TransactionResource;

class OrderController extends Controller
{
    public function __construct(
        private readonly OrderService $service,
    ) {}

    public function purchase(PurchaseRequest $request)
    {
        $transaction = $this->service->purchase($request->validated());

        return ApiResponse::success(
            TransactionResource::make($transaction)->resolve(),
            201,
        );
    }
}

