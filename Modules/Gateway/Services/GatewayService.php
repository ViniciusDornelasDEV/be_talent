<?php

declare(strict_types=1);

namespace Modules\Gateway\Services;

use Modules\Gateway\Models\Gateway;
use Modules\Gateway\Repositories\GatewayRepository;

class GatewayService
{
    public function __construct(
        private readonly GatewayRepository $gateways,
    ) {}

    public function toggleActive(Gateway $gateway): Gateway
    {
        $gateway->is_active = ! $gateway->is_active;

        return $this->gateways->save($gateway);
    }

    public function updatePriority(Gateway $gateway, int $priority): Gateway
    {
        $gateway->priority = $priority;

        return $this->gateways->save($gateway);
    }
}

