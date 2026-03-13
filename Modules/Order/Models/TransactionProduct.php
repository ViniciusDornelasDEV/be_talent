<?php

declare(strict_types=1);

namespace Modules\Order\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransactionProduct extends Model
{
    protected $table = 'transaction_products';

    protected $fillable = [
        'transaction_id',
        'product_id',
        'quantity',
        'amount',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(\Modules\Product\Models\Product::class);
    }
}

