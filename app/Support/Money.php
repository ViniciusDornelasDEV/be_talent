<?php

declare(strict_types=1);

namespace App\Support;

class Money
{
    /**
     * Convert integer cents to a decimal string with two decimal places.
     * Use when presenting monetary values in API responses.
     */
    public static function centsToDecimal(int $amount): string
    {
        return number_format(round($amount / 100, 2), 2, '.', '');
    }
}
