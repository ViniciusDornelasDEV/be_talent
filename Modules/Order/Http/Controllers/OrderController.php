<?php

declare(strict_types=1);

namespace Modules\Order\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Modules\Gateway\Services\PaymentService;
use Modules\Order\Http\Requests\PurchaseRequest;
use Modules\Order\Http\Resources\TransactionResource;
use Modules\Order\Http\Resources\TransactionDetailResource;
use Modules\Order\Models\Transaction;
use Modules\Order\Services\OrderService;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class OrderController extends Controller
{
    public function __construct(
        private readonly OrderService $service,
        private readonly PaymentService $paymentService,
    ) {}

    public function purchase(PurchaseRequest $request)
    {
        $transaction = $this->service->purchase($request->validated());

        return ApiResponse::success(
            TransactionResource::make($transaction)->resolve(),
            201,
        );
    }

    public function index(Request $request)
    {
        Gate::authorize('viewAny', Transaction::class);

        $transactions = $this->service->listTransactions();

        return ApiResponse::success(
            TransactionResource::collection($transactions)->resolve(),
        );
    }

    public function show(int $transaction)
    {
        $order = $this->service->getTransactionWithDetails($transaction);

        if (! $order) {
            throw new NotFoundHttpException();
        }

        Gate::authorize('view', $order);

        return ApiResponse::success(
            TransactionDetailResource::make($order)->resolve(),
        );
    }

    public function refund(int $transaction)
    {
        $order = $this->service->getTransactionWithDetails($transaction);

        if (! $order) {
            throw new NotFoundHttpException();
        }

        Gate::authorize('refund', $order);

        $success = $this->paymentService->refund($order);

        if (! $success) {
            return ApiResponse::error('Refund could not be processed', 422);
        }

        return ApiResponse::success(
            TransactionDetailResource::make($order->fresh())->resolve(),
        );
    }
}

