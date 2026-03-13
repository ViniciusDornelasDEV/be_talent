<?php

declare(strict_types=1);

namespace Modules\Order\Policies;

use Modules\Order\Models\Transaction;
use Modules\User\Models\User;

class OrderPolicy
{
    private const ALLOWED_ROLES = ['ADMIN', 'USER'];

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
}

