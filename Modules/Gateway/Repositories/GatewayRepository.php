<?php

declare(strict_types=1);

namespace Modules\Gateway\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Modules\Gateway\Models\Gateway;

class GatewayRepository
{
    public function __construct(
        private readonly Gateway $model,
    ) {}

    public function all(): Collection
    {
        return $this->model->newQuery()->orderBy('priority')->get();
    }

    public function findById(int $id): ?Gateway
    {
        return $this->model->newQuery()->find($id);
    }

    public function save(Gateway $gateway): Gateway
    {
        $gateway->save();

        return $gateway;
    }

    public function activeOrderedByPriority(): Collection
    {
        return $this->model->newQuery()
            ->where('is_active', true)
            ->orderBy('priority')
            ->get();
    }
}

