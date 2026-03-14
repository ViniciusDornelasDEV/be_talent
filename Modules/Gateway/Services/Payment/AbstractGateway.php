<?php

declare(strict_types=1);

namespace Modules\Gateway\Services\Payment;

use Modules\Gateway\Services\PaymentResult;
use Modules\Order\Models\Transaction;

abstract class AbstractGateway
{
    abstract public function getName(): string;

    abstract public function charge(Transaction $transaction, array $client, array $card): PaymentResult;

    abstract public function refund(Transaction $transaction): array;
}

