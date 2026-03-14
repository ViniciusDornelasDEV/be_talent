<?php

declare(strict_types=1);

namespace Modules\Order\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Support\Money;

class TransactionDetailResource extends JsonResource
{
    public static $wrap = null;

    public function toArray(Request $request): array
    {
        return [
            'id'                => $this->id,
            'status'            => $this->status,
            'amount'            => Money::centsToDecimal((int) $this->amount),
            'card_last_numbers' => $this->card_last_numbers,
            'gateway'           => $this->gateway ? [
                'id'   => $this->gateway->id,
                'name' => $this->gateway->name,
            ] : null,
            'client'            => $this->client ? [
                'id'    => $this->client->id,
                'name'  => $this->client->name,
                'email' => $this->client->email,
            ] : null,
            'items'             => TransactionItemResource::collection($this->transactionProducts)->resolve(),
            'created_at'        => $this->created_at,
            'updated_at'        => $this->updated_at,
        ];
    }
}

