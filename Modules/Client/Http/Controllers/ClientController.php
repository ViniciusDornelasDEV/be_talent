<?php

declare(strict_types=1);

namespace Modules\Client\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Modules\Client\Http\Resources\ClientResource;
use Modules\Client\Http\Resources\ClientTransactionResource;
use Modules\Client\Models\Client;
use Modules\Client\Services\ClientService;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ClientController extends Controller
{
    public function __construct(
        private readonly ClientService $service,
    ) {}

    public function index(Request $request)
    {
        Gate::authorize('viewAny', Client::class);

        $clients = $this->service->list();

        return ApiResponse::success(
            ClientResource::collection($clients)->resolve(),
        );
    }

    public function show(int $clientId)
    {
        $client = $this->service->findWithTransactions($clientId);

        if (! $client) {
            throw new NotFoundHttpException();
        }

        Gate::authorize('view', $client);

        $transactions = $client->transactions;

        return ApiResponse::success([
            'client'       => ClientResource::make($client)->resolve(),
            'transactions' => ClientTransactionResource::collection($transactions)->resolve(),
        ]);
    }
}

