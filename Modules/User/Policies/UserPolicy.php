<?php

namespace Modules\User\Policies;

use Modules\User\Models\User;

class UserPolicy
{
    public function index(User $user): bool
    {
        return strtoupper($user->role) === 'ADMIN';
    }
    
    public function create(User $user): bool
    {
        return strtoupper($user->role) === 'ADMIN';
    }

    public function update(User $user): bool
    {
        return strtoupper($user->role) === 'ADMIN';
    }
}
