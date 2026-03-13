<?php

declare(strict_types=1);

namespace Modules\Order\Repositories;

use Modules\Order\Models\Client;

class ClientRepository
{
    public function __construct(
        private readonly Client $model,
    ) {}

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

