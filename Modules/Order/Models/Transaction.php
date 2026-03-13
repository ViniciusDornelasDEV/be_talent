<?php

declare(strict_types=1);

namespace Modules\Order\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Transaction extends Model
{
    protected $table = 'transactions';

    protected $fillable = [
        'client_id',
        'gateway_id',
        'external_id',
        'status',
        'amount',
        'card_last_numbers',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(\Modules\Client\Models\Client::class);
    }

    public function gateway(): BelongsTo
    {
        return $this->belongsTo(\Modules\Gateway\Models\Gateway::class);
    }

    public function transactionProducts(): HasMany
    {
        return $this->hasMany(TransactionProduct::class);
    }
}

