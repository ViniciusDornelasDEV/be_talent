<?php

namespace Modules\User\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Modules\User\Models\User;

class UserRepository
{
    public function all(): Collection
    {
        return User::orderBy('email')->get();
    }

    public function create(array $data): User
    {
        return User::create($data);
    }

    public function update(User $user, array $data): User
    {
        $user->update($data);

        return $user->fresh();
    }
}
