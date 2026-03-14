<?php

declare(strict_types=1);

namespace Modules\Order\Policies;

use Modules\Gateway\Logging\PaymentLogger;
use Modules\Order\Models\Transaction;
use Modules\User\Models\User;

class OrderPolicy
{
    private const ALLOWED_ROLES = ['ADMIN', 'USER'];

    private const REFUND_ROLES = ['ADMIN', 'FINANCE'];

    private function canAccess(User $user): bool
    {
        return in_array(strtoupper($user->role), self::ALLOWED_ROLES, true);
    }

    public function viewAny(User $user): bool
    {
        return $this->canAccess($user);
    }

    public function view(User $user, Transaction $transaction): bool
    {
        return $this->canAccess($user);
    }

    public function refund(User $user, Transaction $transaction): bool
    {
        $allowed = in_array(strtoupper($user->role), self::REFUND_ROLES, true);

        if (! $allowed) {
            PaymentLogger::refundUnauthorizedAttempt(
                $user->id,
                $transaction->id,
                $user->role,
                $transaction->gateway_id,
            );

            return false;
        }

        return true;
    }
}

