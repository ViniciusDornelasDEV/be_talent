<?php

namespace Modules\User\Http\Controllers;

use App\Helpers\ApiResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use Modules\User\Http\Requests\InsertUserRequest;
use Modules\User\Http\Requests\UpdateUserRequest;
use Modules\User\Services\UserService;
use Modules\User\Models\User;
use Modules\User\Http\Resources\UserResource;

class UserController extends Controller
{
    public function __construct(
        protected UserService $service
    ) {}

    public function index(Request $request)
    {
        Gate::authorize('index', User::class);
        $users = $this->service->list();

        return ApiResponse::success(
            UserResource::collection($users)->resolve(),
            201
        );
    }

    public function create(InsertUserRequest $request)
    {
        $user = $this->service->create($request->validated());

        return ApiResponse::success($user, 201);
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $updatedUser = $this->service->update($user, $request->validated());

        return ApiResponse::success($updatedUser);
    }
}
