<?php

declare(strict_types=1);

namespace Modules\Product\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Support\Money;

class ProductResource extends JsonResource
{
    public static $wrap = null;

    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'name'       => $this->name,
            'amount'     => Money::centsToDecimal((int) $this->amount),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}

