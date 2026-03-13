<?php

declare(strict_types=1);

namespace Modules\Gateway\Models;

use Illuminate\Database\Eloquent\Model;

class Gateway extends Model
{
    protected $table = 'gateways';

    protected $fillable = [
        'name',
        'is_active',
        'priority',
    ];
}

