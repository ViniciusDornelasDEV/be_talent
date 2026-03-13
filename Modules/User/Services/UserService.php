<?php

namespace Modules\User\Services;

use Illuminate\Database\Eloquent\Collection;
use Modules\User\Models\User;
use Modules\User\Repositories\UserRepository;

class UserService
{
    public function __construct(
        protected UserRepository $repository
    ) {}

    public function list(): Collection
    {
        return $this->repository->all();
    }

    public function create(array $data): User
    {
        $payload = [
            'email'    => $data['email'],
            'password' => $data['password'],
            'role'     => $data['role'],
        ];

        return $this->repository->create($payload);
    }

    public function update(User $user, array $data): User
    {
        $payload = [
            'email'  => $data['email'],
            'role'   => $data['role'],
        ];

        if (! empty($data['password'])) {
            $payload['password'] = $data['password'];
        }

        return $this->repository->update($user, $payload);
    }
}
