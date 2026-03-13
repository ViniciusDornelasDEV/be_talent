<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Requests\IndexCitiesByStateRequest;
use App\Http\Resources\CityResource;
use App\Http\Resources\StateResource;
use App\Services\StateService;
use Illuminate\Http\JsonResponse;

class StateController extends Controller
{
    public function __construct(
        protected StateService $service
    ) {}

    public function index(): JsonResponse
    {
        $states = $this->service->listStates();

        return ApiResponse::success(
            StateResource::collection($states)->resolve(),
            200
        );
    }

    public function cities(IndexCitiesByStateRequest $request): JsonResponse
    {
        $cities = $this->service->listCitiesByState((int) $request->route('state_id'));

        return ApiResponse::success(
            CityResource::collection($cities)->resolve(),
            200
        );
    }
}
