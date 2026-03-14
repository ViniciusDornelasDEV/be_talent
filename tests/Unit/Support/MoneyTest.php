<?php

declare(strict_types=1);

namespace Tests\Unit\Support;

use App\Support\Money;
use PHPUnit\Framework\TestCase;

class MoneyTest extends TestCase
{
    public function test_cents_to_decimal_1000_returns_10_00(): void
    {
        $this->assertSame('10.00', Money::centsToDecimal(1000));
    }

    public function test_cents_to_decimal_1250_returns_12_50(): void
    {
        $this->assertSame('12.50', Money::centsToDecimal(1250));
    }

    public function test_cents_to_decimal_9999_returns_99_99(): void
    {
        $this->assertSame('99.99', Money::centsToDecimal(9999));
    }

    public function test_cents_to_decimal_zero_returns_0_00(): void
    {
        $this->assertSame('0.00', Money::centsToDecimal(0));
    }
}
