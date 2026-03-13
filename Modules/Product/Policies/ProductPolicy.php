<?php

declare(strict_types=1);

namespace Modules\Product\Policies;

use Modules\User\Models\User;
use Modules\Product\Models\Product;

class ProductPolicy
{
    private const ALLOWED_ROLES = ['ADMIN', 'MANAGER', 'FINANCE'];

    private function canManage(User $user): bool
    {
        return in_array(strtoupper($user->role), self::ALLOWED_ROLES, true);
    }

    public function viewAny(User $user): bool
    {
        return $this->canManage($user);
    }

    public function create(User $user): bool
    {
        return $this->canManage($user);
    }

    public function update(User $user, Product $product): bool
    {
        return $this->canManage($user);
    }
}

