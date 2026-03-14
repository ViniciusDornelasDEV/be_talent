<?php

declare(strict_types=1);

namespace Modules\Client\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Support\Money;
use Modules\Order\Models\TransactionProduct;
use Modules\Product\Models\Product;

class ClientTransactionResource extends JsonResource
{
    public static $wrap = null;

    public function toArray(Request $request): array
    {
        /** @var \Modules\Order\Models\Transaction $transaction */
        $transaction = $this->resource;

        $items = TransactionProduct::query()
            ->where('transaction_id', $transaction->id)
            ->get()
            ->map(function (TransactionProduct $tp): array {
                $product = Product::query()->find($tp->product_id);

                return [
                    'product_id'   => $tp->product_id,
                    'product_name' => $product?->name,
                    'quantity'     => $tp->quantity,
                    'amount'       => Money::centsToDecimal((int) $tp->amount),
                ];
            })->all();

        return [
            'id'                => $transaction->id,
            'status'            => $transaction->status,
            'amount'            => Money::centsToDecimal((int) $transaction->amount),
            'card_last_numbers' => $transaction->card_last_numbers,
            'gateway_id'        => $transaction->gateway_id,
            'external_id'       => $transaction->external_id,
            'created_at'        => $transaction->created_at,
            'updated_at'        => $transaction->updated_at,
            'items'             => $items,
        ];
    }
}

