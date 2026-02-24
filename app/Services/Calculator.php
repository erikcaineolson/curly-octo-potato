<?php

namespace App\Services;

use DomainException;

class Calculator
{
    public function calculate(float $a, float $b, string $operator): float
    {
        return match ($operator) {
            'add' => $a + $b,
            'subtract' => $a - $b,
            'multiply' => $a * $b,
            'divide' => $b != 0
                ? $a / $b
                : throw new DomainException('Division by zero.'),
        };
    }

    public function buildExpression(float $a, float $b, string $operator): string
    {
        $symbol = match ($operator) {
            'add' => '+',
            'subtract' => '-',
            'multiply' => '*',
            'divide' => '/',
        };

        $formatA = fmod($a, 1) == 0 ? number_format($a, 0, '.', '') : (string) $a;
        $formatB = fmod($b, 1) == 0 ? number_format($b, 0, '.', '') : (string) $b;

        return "{$formatA} {$symbol} {$formatB}";
    }
}
