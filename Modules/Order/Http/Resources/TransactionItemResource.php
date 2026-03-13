<?php

declare(strict_types=1);

namespace Modules\Order\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionItemResource extends JsonResource
{
    public static $wrap = null;

    public function toArray(Request $request): array
    {
        return [
            'product_id'   => $this->product_id,
            'product_name' => $this->product?->name,
            'quantity'     => $this->quantity,
            'amount'       => $this->amount,
        ];
    }
}

