<?php

declare(strict_types=1);

namespace Modules\Client\Policies;

use Modules\Client\Models\Client;
use Modules\User\Models\User;

class ClientPolicy
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

    public function view(User $user, Client $client): bool
    {
        return $this->canAccess($user);
    }
}

