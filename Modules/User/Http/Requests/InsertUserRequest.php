<?php

namespace Modules\User\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Modules\User\Models\User;

class InsertUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('create', User::class);
    }

    public function rules(): array
    {
        return [
            'email'    => 'required|email|max:255|unique:users,email',
            'password' => 'required|string',
            'role'     => 'required|in:ADMIN,MANAGER,FINANCE,USER',
        ];
    }
}
