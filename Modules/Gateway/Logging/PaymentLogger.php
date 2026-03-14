<?php

declare(strict_types=1);

namespace Modules\Gateway\Logging;

use Illuminate\Support\Facades\Log;

class PaymentLogger
{
    public static function gatewayAttempt(string $gateway, int $transactionId, array $context = []): void
    {
        Log::info('Payment: attempting gateway', array_merge([
            'transaction_id' => $transactionId,
            'gateway'        => $gateway,
        ], $context));
    }

    public static function gatewayFailure(string $gateway, int $transactionId, string $error = ''): void
    {
        $context = [
            'transaction_id' => $transactionId,
            'gateway'        => $gateway,
        ];
        if ($error !== '') {
            $context['error'] = $error;
        }
        Log::warning('Payment: gateway charge failed', $context);
    }

    public static function gatewayException(
        string $gateway,
        int $transactionId,
        string $message,
        ?int $gatewayId = null,
        string $exceptionClass = '',
    ): void {
        $context = [
            'transaction_id'    => $transactionId,
            'gateway'          => $gateway,
            'exception_message' => $message,
        ];
        if ($gatewayId !== null) {
            $context['gateway_id'] = $gatewayId;
        }
        if ($exceptionClass !== '') {
            $context['exception_class'] = $exceptionClass;
        }
        Log::error('Payment: exception while charging gateway', $context);
    }

    public static function paymentSuccess(int $transactionId, ?int $gatewayId, ?string $externalId): void
    {
        $context = [
            'transaction_id' => $transactionId,
        ];
        if ($gatewayId !== null) {
            $context['gateway_id'] = $gatewayId;
        }
        if ($externalId !== null) {
            $context['external_id'] = $externalId;
        }
        Log::info('Payment: charge succeeded', $context);
    }

    public static function paymentFailure(int $transactionId): void
    {
        Log::warning('Payment: all gateways failed', [
            'transaction_id' => $transactionId,
        ]);
    }

    public static function transactionNotFound(int $transactionId): void
    {
        Log::warning('Payment: transaction not found', [
            'transaction_id' => $transactionId,
        ]);
    }

    public static function transactionAlreadyProcessed(int $transactionId, string $status): void
    {
        Log::info('Payment: transaction already processed', [
            'transaction_id' => $transactionId,
            'status'        => $status,
        ]);
    }

    public static function jobStarted(int $transactionId, ?string $clientEmail = null): void
    {
        $context = ['transaction_id' => $transactionId];
        if ($clientEmail !== null) {
            $context['client_email'] = $clientEmail;
        }
        Log::info('Payment: job started', $context);
    }

    public static function gatewayLoginAttempt(int $transactionId, string $gateway): void
    {
        Log::info('Payment: gateway login attempt', [
            'transaction_id' => $transactionId,
            'gateway'        => $gateway,
        ]);
    }

    public static function gatewayLoginFailed(int $transactionId, string $gateway, int $status, mixed $body): void
    {
        Log::warning('Payment: gateway login failed', [
            'transaction_id' => $transactionId,
            'gateway'       => $gateway,
            'status'        => $status,
            'body'          => $body,
        ]);
    }

    public static function gatewayTokenInvalid(int $transactionId, string $gateway): void
    {
        Log::warning('Payment: gateway token missing or invalid', [
            'transaction_id' => $transactionId,
            'gateway'       => $gateway,
        ]);
    }

    public static function gatewayChargeRequest(
        int $transactionId,
        string $gateway,
        float $amount,
        ?string $clientEmail = null,
    ): void {
        $context = [
            'transaction_id' => $transactionId,
            'gateway'       => $gateway,
            'amount'        => $amount,
        ];
        if ($clientEmail !== null) {
            $context['client_email'] = $clientEmail;
        }
        Log::info('Payment: sending charge request', $context);
    }

    public static function gatewayChargeRequestFailed(
        int $transactionId,
        string $gateway,
        int $status,
        mixed $body,
    ): void {
        Log::warning('Payment: charge request failed', [
            'transaction_id' => $transactionId,
            'gateway'       => $gateway,
            'status'        => $status,
            'body'          => $body,
        ]);
    }

    public static function gatewayChargeSuccess(int $transactionId, string $gateway, ?string $externalId): void
    {
        $context = [
            'transaction_id' => $transactionId,
            'gateway'       => $gateway,
        ];
        if ($externalId !== null) {
            $context['external_id'] = $externalId;
        }
        Log::info('Payment: charge request succeeded', $context);
    }
}
