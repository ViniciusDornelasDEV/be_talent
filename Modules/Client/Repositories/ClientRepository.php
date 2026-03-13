<?php

declare(strict_types=1);

namespace Modules\Client\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Modules\Client\Models\Client;

class ClientRepository
{
    public function __construct(
        private readonly Client $model,
    ) {}

    public function all(): Collection
    {
        return $this->model->newQuery()->orderBy('id')->get();
    }

    public function findById(int $id): ?Client
    {
        return $this->model->newQuery()->find($id);
    }

    public function findOrCreateByEmailOrName(string $email = null, string $name = null): Client
    {
        if ($email !== null) {
            return $this->model->newQuery()->firstOrCreate(
                ['email' => $email],
                ['name' => $name ?? $email],
            );
        }

        return $this->model->newQuery()->firstOrCreate(
            ['name' => $name],
            ['email' => null],
        );
    }
}

