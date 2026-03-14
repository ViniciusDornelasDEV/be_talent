<?php

declare(strict_types=1);

namespace Modules\Order\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\Gateway\Logging\PaymentLogger;
use Modules\Gateway\Services\PaymentService;
use Modules\Order\Models\Transaction;

class ProcessPaymentJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries = 3;
    public int $timeout = 30;
    public array $backoff = [5, 15, 30];

    public function __construct(
        private readonly int $transactionId,
        private readonly array $clientData,
        private readonly array $cardData,
    ) {}

    public function handle(PaymentService $payments): void
    {
        $transaction = Transaction::query()->find($this->transactionId);

        if (! $transaction) {
            PaymentLogger::transactionNotFound($this->transactionId);
            return;
        }

        if ($transaction->status !== 'pending') {
            PaymentLogger::transactionAlreadyProcessed($transaction->id, $transaction->status);

            return;
        }

        PaymentLogger::jobStarted($transaction->id, $this->clientData['email'] ?? null);

        $result = $payments->process($transaction, $this->clientData, $this->cardData);

        if ($result->success) {
            $transaction->status      = 'paid';
            $transaction->gateway_id  = $result->gatewayId;
            $transaction->external_id = $result->externalId;

            PaymentLogger::paymentSuccess($transaction->id, $result->gatewayId, $result->externalId);
        } else {
            $transaction->status = 'failed';

            PaymentLogger::paymentFailure($transaction->id);
        }

        $transaction->save();
    }
}

