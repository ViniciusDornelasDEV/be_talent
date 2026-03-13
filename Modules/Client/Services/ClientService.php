<?php

declare(strict_types=1);

namespace Modules\Client\Services;

use Illuminate\Database\Eloquent\Collection;
use Modules\Client\Models\Client;
use Modules\Client\Repositories\ClientRepository;

class ClientService
{
    public function __construct(
        private readonly ClientRepository $clients,
    ) {}

    public function list(): Collection
    {
        return $this->clients->all();
    }

    public function findWithTransactions(int $id): ?Client
    {
        $client = $this->clients->findById($id);

        if (! $client) {
            return null;
        }

        $client->load('transactions');

        return $client;
    }
}

