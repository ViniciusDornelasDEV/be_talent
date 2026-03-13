<?php

declare(strict_types=1);

namespace Modules\Order\Repositories;

use Modules\Order\Models\TransactionProduct;

class TransactionProductRepository
{
    public function __construct(
        private readonly TransactionProduct $model,
    ) {}

    public function create(array $data): TransactionProduct
    {
        return $this->model->newQuery()->create($data);
    }
}

