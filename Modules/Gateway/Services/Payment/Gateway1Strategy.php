<?php

declare(strict_types=1);

namespace Modules\Gateway\Services\Payment;

use Illuminate\Support\Facades\Http;
use Modules\Gateway\Logging\PaymentLogger;
use Modules\Gateway\Services\PaymentResult;
use Modules\Order\Models\Transaction;

class Gateway1Strategy extends AbstractGateway
{
    public function getName(): string
    {
        return 'gateway1';
    }

    public function charge(Transaction $transaction, array $client, array $card): PaymentResult
    {
        PaymentLogger::gatewayLoginAttempt($transaction->id, $this->getName());

        $loginResponse = Http::post(env('GATEWAY1_URL').'/login', [
            'email' => 'dev@betalent.tech',
            'token' => 'FEC9BB078BF338F464F96B48089EB498',
        ]);

        if (! $loginResponse->successful()) {
            PaymentLogger::gatewayLoginFailed(
                $transaction->id,
                $this->getName(),
                $loginResponse->status(),
                $loginResponse->json(),
            );

            return PaymentResult::failure();
        }

        $token = $loginResponse->json('token');

        if (! is_string($token) || $token === '') {
            PaymentLogger::gatewayTokenInvalid($transaction->id, $this->getName());

            return PaymentResult::failure();
        }

        $amount = (float) $transaction->amount;

        PaymentLogger::gatewayChargeRequest(
            $transaction->id,
            $this->getName(),
            $amount,
            $client['email'] ?? null,
        );

        $response = Http::withToken($token)->post(env('GATEWAY1_URL').'/transactions', [
            'amount'     => $amount,
            'name'       => $client['name'] ?? '',
            'email'      => $client['email'] ?? '',
            'cardNumber' => $card['number'] ?? '',
            'cvv'        => $card['cvv'] ?? '',
        ]);

        if (! $response->successful()) {
            PaymentLogger::gatewayChargeRequestFailed(
                $transaction->id,
                $this->getName(),
                $response->status(),
                $response->json(),
            );

            return PaymentResult::failure();
        }

        $data = $response->json();
        $externalId = isset($data['id']) ? (string) $data['id'] : null;

        PaymentLogger::gatewayChargeSuccess($transaction->id, $this->getName(), $externalId);

        return PaymentResult::success(null, $externalId);
    }

    public function refund(Transaction $transaction): bool
    {
        $externalId = $transaction->external_id;
        if ($externalId === null || $externalId === '') {
            return false;
        }

        $loginResponse = Http::post(env('GATEWAY1_URL').'/login', [
            'email' => 'dev@betalent.tech',
            'token' => 'FEC9BB078BF338F464F96B48089EB498',
        ]);

        if (! $loginResponse->successful()) {
            return false;
        }

        $token = $loginResponse->json('token');
        if (! is_string($token) || $token === '') {
            return false;
        }

        $response = Http::withToken($token)->post(
            env('GATEWAY1_URL').'/transactions/'.$externalId.'/charge_back',
        );

        return $response->successful();
    }
}

