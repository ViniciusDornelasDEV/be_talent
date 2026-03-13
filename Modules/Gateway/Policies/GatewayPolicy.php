<?php

declare(strict_types=1);

namespace Modules\Gateway\Policies;

use Modules\User\Models\User;
use Modules\Gateway\Models\Gateway;

class GatewayPolicy
{
    private const ALLOWED_ROLES = ['ADMIN', 'USER'];

    private function canManage(User $user): bool
    {
        return in_array(strtoupper($user->role), self::ALLOWED_ROLES, true);
    }

    public function toggle(User $user, Gateway $gateway): bool
    {
        return $this->canManage($user);
    }

    public function updatePriority(User $user, Gateway $gateway): bool
    {
        return $this->canManage($user);
    }
}

