<?php

declare(strict_types=1);

namespace Modules\Order\Repositories;

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
}

