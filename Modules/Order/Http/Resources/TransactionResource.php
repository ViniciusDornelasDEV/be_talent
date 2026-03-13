<?php

declare(strict_types=1);

namespace Modules\Order\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
{
    public static $wrap = null;

    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'client'     => $this->client ? [
                'id'    => $this->client->id,
                'name'  => $this->client->name,
                'email' => $this->client->email,
            ] : null,
            'status'     => $this->status,
            'amount'     => $this->amount,
            'gateway'    => $this->gateway ? [
                'id'   => $this->gateway->id,
                'name' => $this->gateway->name,
            ] : null,
            'created_at' => $this->created_at,
        ];
    }
}

