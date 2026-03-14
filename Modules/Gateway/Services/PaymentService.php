<?php

declare(strict_types=1);

namespace Modules\Gateway\Services;

use Modules\Gateway\Logging\PaymentLogger;
use Modules\Gateway\Repositories\GatewayRepository;
use Modules\Gateway\Services\Payment\AbstractGateway;
use Modules\Gateway\Services\Payment\GatewayFactory;
use Modules\Gateway\Services\PaymentResult;
use Modules\Order\Models\Transaction;

class PaymentService
{
    public function __construct(
        private readonly GatewayRepository $gateways,
        private readonly GatewayFactory $factory,
    ) {}

    public function process(Transaction $transaction, array $clientData, array $cardData): PaymentResult
    {
        $candidates = $this->gateways->activeOrderedByPriority();

        foreach ($candidates as $gateway) {
            $strategy = $this->factory->make($gateway->name);

            $result = $this->attemptGatewayCharge($gateway, $transaction, $strategy, $clientData, $cardData);

            if ($result->success) {
                return $result;
            }
        }

        PaymentLogger::paymentFailure($transaction->id);

        return PaymentResult::failure();
    }

    private function attemptGatewayCharge(
        object $gatewayModel,
        Transaction $transaction,
        AbstractGateway $strategy,
        array $clientData,
        array $cardData,
    ): PaymentResult {
        PaymentLogger::gatewayAttempt($gatewayModel->name, $transaction->id, [
            'priority' => $gatewayModel->priority,
        ]);

        try {
            $result = $strategy->charge($transaction, $clientData, $cardData);

            if ($result->success) {
                PaymentLogger::paymentSuccess(
                    $transaction->id,
                    $gatewayModel->id,
                    $result->externalId,
                );

                return $result->withGatewayId($gatewayModel->id);
            }

            PaymentLogger::gatewayFailure($gatewayModel->name, $transaction->id, 'Charge failed');
        } catch (\Throwable $e) {
            PaymentLogger::gatewayException(
                $gatewayModel->name,
                $transaction->id,
                $e->getMessage(),
                $gatewayModel->id,
                $e::class,
            );

            report($e);
        }

        return PaymentResult::failure();
    }
}

