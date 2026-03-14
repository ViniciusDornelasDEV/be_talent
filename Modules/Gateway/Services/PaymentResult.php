<?php

declare(strict_types=1);

namespace Modules\Gateway\Services;

class PaymentResult
{
    public function __construct(
        public bool $success,
        public ?int $gatewayId = null,
        public ?string $externalId = null,
    ) {}

    public static function success(?int $gatewayId = null, ?string $externalId = null): self
    {
        return new self(true, $gatewayId, $externalId);
    }

    public static function failure(): self
    {
        return new self(false);
    }

    public function withGatewayId(int $gatewayId): self
    {
        return new self($this->success, $gatewayId, $this->externalId);
    }
}

