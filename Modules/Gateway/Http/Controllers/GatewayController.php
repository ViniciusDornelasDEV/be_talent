<?php

declare(strict_types=1);

namespace Modules\Gateway\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use Modules\Gateway\Http\Requests\ToggleGatewayRequest;
use Modules\Gateway\Http\Requests\UpdateGatewayPriorityRequest;
use Modules\Gateway\Models\Gateway;
use Modules\Gateway\Services\GatewayService;

class GatewayController extends Controller
{
    public function __construct(
        private readonly GatewayService $service,
    ) {}

    public function toggleActive(ToggleGatewayRequest $request, Gateway $gateway)
    {
        $updated = $this->service->toggleActive($gateway);

        return ApiResponse::success([
            'id'        => $updated->id,
            'is_active' => $updated->is_active,
        ]);
    }

    public function updatePriority(UpdateGatewayPriorityRequest $request, Gateway $gateway)
    {
        $validated = $request->validated();
        $updated = $this->service->updatePriority($gateway, (int) $validated['priority']);

        return ApiResponse::success([
            'id'       => $updated->id,
            'priority' => $updated->priority,
        ]);
    }
}

