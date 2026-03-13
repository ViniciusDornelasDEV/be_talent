<?php

namespace Modules\User\Policies;

use Modules\User\Models\User;

class UserPolicy
{
    protected function isAdminOrManager(User $user): bool
    {
        return in_array(strtoupper($user->role), ['ADMIN', 'MANAGER']);
    }

    public function index(User $user): bool
    {
        return $this->isAdminOrManager($user);
    }
    
    public function create(User $user): bool
    {
        return $this->isAdminOrManager($user);
    }

    public function update(User $user): bool
    {
        return $this->isAdminOrManager($user);
    }
}