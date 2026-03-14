<?php

declare(strict_types=1);

namespace Modules\Gateway\Services\Payment;

class GatewayFactory
{
    public function make(string $name): AbstractGateway
    {
        return match (strtolower($name)) {
            'gateway1', 'gateway 1' => app(Gateway1Strategy::class),
            'gateway2', 'gateway 2' => app(Gateway2Strategy::class),
            default => throw new \InvalidArgumentException("Unsupported gateway [{$name}]"),
        };
    }
}

