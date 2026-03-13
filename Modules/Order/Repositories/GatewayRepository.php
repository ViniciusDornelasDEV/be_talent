<?php

declare(strict_types=1);

namespace Modules\Order\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Modules\Order\Models\Gateway;

class GatewayRepository
{
    public function __construct(
        private readonly Gateway $model,
    ) {}

    public function activeOrderedByPriority(): Collection
    {
        return $this->model->newQuery()
            ->where('is_active', true)
            ->orderBy('priority')
            ->get();
    }
}

