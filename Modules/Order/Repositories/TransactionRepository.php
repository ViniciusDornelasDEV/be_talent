<?php

declare(strict_types=1);

namespace Modules\Order\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Modules\Order\Models\Transaction;

class TransactionRepository
{
    public function __construct(
        private readonly Transaction $model,
    ) {}

    public function create(array $data): Transaction
    {
        return $this->model->newQuery()->create($data);
    }

    public function allWithRelations(): Collection
    {
        return $this->model->newQuery()
            ->with(['client', 'gateway'])
            ->orderByDesc('id')
            ->get();
    }

    public function findWithRelations(int $id): ?Transaction
    {
        return $this->model->newQuery()
            ->with(['client', 'gateway', 'transactionProducts.product'])
            ->find($id);
    }
}

