<?php

declare(strict_types=1);

namespace Modules\Product\Services;

use Illuminate\Database\Eloquent\Collection;
use Modules\Product\Models\Product;
use Modules\Product\Repositories\ProductRepository;

class ProductService
{
    public function __construct(
        private readonly ProductRepository $repository,
    ) {}

    public function list(): Collection
    {
        return $this->repository->all();
    }

    public function create(array $data): Product
    {
        return $this->repository->create($data);
    }

    public function update(Product $product, array $data): Product
    {
        return $this->repository->update($product, $data);
    }
}

