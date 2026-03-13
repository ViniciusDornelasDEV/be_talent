<?php

declare(strict_types=1);

namespace Modules\Client\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Order\Models\Transaction;

class Client extends Model
{
    protected $table = 'clients';

    protected $fillable = [
        'name',
        'email',
    ];

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }
}

