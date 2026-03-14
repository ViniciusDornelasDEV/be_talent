<?php

declare(strict_types=1);

namespace Modules\Product\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Modules\Product\Models\Product;

class ProductRepository
{
    public function __construct(
        private readonly Product $model,
    ) {}

    public function all(): Collection
    {
        return $this->model->newQuery()->orderBy('id')->get();
    }

    public function create(array $data): Product
    {
        return $this->model->newQuery()->create($data);
    }

    public function update(Product $product, array $data): Product
    {
        $product->fill($data);
        $product->save();

        return $product;
    }

    public function findById(int $id): ?Product
    {
        return $this->model->newQuery()->find($id);
    }

    public function findByIds(array $ids): Collection
    {
        if ($ids === []) {
            return new Collection();
        }

        return $this->model->newQuery()
            ->whereIn('id', $ids)
            ->get();
    }
}

