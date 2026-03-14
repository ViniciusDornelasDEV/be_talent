<?php

declare(strict_types=1);

namespace Modules\Gateway\Services\Payment;

use Illuminate\Support\Facades\Http;
use Modules\Gateway\Services\PaymentResult;
use Modules\Order\Models\Transaction;

class Gateway2Strategy extends AbstractGateway
{
    public function getName(): string
    {
        return 'gateway2';
    }

    public function charge(Transaction $transaction, array $client, array $card): PaymentResult
    {
        $response = Http::withHeaders([
            'Gateway-Auth-Token'  => 'tk_f2198cc671b5289fa856',
            'Gateway-Auth-Secret' => '3d15e8ed6131446ea7e3456728b1211f',
        ])->post(env('GATEWAY2_URL').'/transacoes', [
            'valor'        => $transaction->amount,
            'nome'         => $client['name'] ?? '',
            'email'        => $client['email'] ?? '',
            'numeroCartao' => $card['number'] ?? '',
            'cvv'          => $card['cvv'] ?? '',
        ]);

        if (! $response->successful()) {
            return PaymentResult::failure();
        }

        $data = $response->json();
        $externalId = isset($data['id']) ? (string) $data['id'] : (isset($data['uuid']) ? (string) $data['uuid'] : null);

        return PaymentResult::success(null, $externalId);
    }

    public function refund(Transaction $transaction): bool
    {
        $externalId = $transaction->external_id;
        if ($externalId === null || $externalId === '') {
            return false;
        }

        $response = Http::withHeaders([
            'Gateway-Auth-Token'  => 'tk_f2198cc671b5289fa856',
            'Gateway-Auth-Secret' => '3d15e8ed6131446ea7e3456728b1211f',
        ])->post(env('GATEWAY2_URL').'/transacoes/reembolso', [
            'id' => $externalId,
        ]);

        return $response->successful();
    }
}

