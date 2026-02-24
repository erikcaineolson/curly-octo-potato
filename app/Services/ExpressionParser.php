<?php

namespace App\Services;

use DomainException;

/**
 * Recursive-descent expression parser. Not reentrant (uses instance state).
 */
class ExpressionParser
{
    private const MAX_DEPTH = 50;

    private string $input;
    private int $pos;
    private int $length;
    private int $depth;

    public function evaluate(string $input): float
    {
        $this->input = preg_replace('/\s+/', '', $input);
        $this->pos = 0;
        $this->length = strlen($this->input);
        $this->depth = 0;

        if ($this->length === 0) {
            throw new DomainException('Empty expression.');
        }

        $result = $this->parseExpression();

        if ($this->pos < $this->length) {
            throw new DomainException(
                "Unexpected character '{$this->input[$this->pos]}' at position {$this->pos}."
            );
        }

        return $result;
    }

    private function parseExpression(): float
    {
        $this->depth++;

        if ($this->depth > self::MAX_DEPTH) {
            throw new DomainException('Expression is too deeply nested.');
        }

        $result = $this->parseTerm();

        while ($this->pos < $this->length && in_array($this->input[$this->pos], ['+', '-'])) {
            $op = $this->input[$this->pos];
            $this->pos++;
            $right = $this->parseTerm();
            $result = $op === '+' ? $result + $right : $result - $right;
        }

        $this->depth--;

        return $result;
    }

    private function parseTerm(): float
    {
        $result = $this->parsePower();

        while ($this->pos < $this->length && in_array($this->input[$this->pos], ['*', '/'])) {
            $op = $this->input[$this->pos];
            $this->pos++;
            $right = $this->parsePower();

            if ($op === '/') {
                if ($right == 0) {
                    throw new DomainException('Division by zero.');
                }
                $result /= $right;
            } else {
                $result *= $right;
            }
        }

        return $result;
    }

    private function parsePower(): float
    {
        $base = $this->parseUnary();

        if ($this->pos < $this->length && $this->input[$this->pos] === '^') {
            $this->pos++;
            $exponent = $this->parsePower();
            return pow($base, $exponent);
        }

        return $base;
    }

    private function parseUnary(): float
    {
        $negateCount = 0;

        while ($this->pos < $this->length && $this->input[$this->pos] === '-') {
            $this->pos++;
            $negateCount++;
        }

        $result = $this->parseFunction();

        return ($negateCount % 2 === 1) ? -$result : $result;
    }

    private function parseFunction(): float
    {
        if ($this->matchWord('sqrt(')) {
            $value = $this->parseExpression();
            $this->expect(')');

            if ($value < 0) {
                throw new DomainException('Cannot take square root of a negative number.');
            }

            return sqrt($value);
        }

        return $this->parseAtom();
    }

    private function parseAtom(): float
    {
        if ($this->pos < $this->length && $this->input[$this->pos] === '(') {
            $this->pos++;
            $result = $this->parseExpression();
            $this->expect(')');
            return $result;
        }

        return $this->parseNumber();
    }

    private function parseNumber(): float
    {
        $start = $this->pos;

        while ($this->pos < $this->length && (ctype_digit($this->input[$this->pos]) || $this->input[$this->pos] === '.')) {
            $this->pos++;
        }

        if ($this->pos === $start) {
            $char = $this->pos < $this->length ? $this->input[$this->pos] : 'end of input';
            throw new DomainException("Expected number at position {$this->pos}, got '{$char}'.");
        }

        $number = substr($this->input, $start, $this->pos - $start);

        if (!is_numeric($number)) {
            throw new DomainException("Invalid number '{$number}' at position {$start}.");
        }

        return (float) $number;
    }

    private function matchWord(string $word): bool
    {
        $len = strlen($word);

        if ($this->pos + $len <= $this->length && substr($this->input, $this->pos, $len) === $word) {
            $this->pos += $len;
            return true;
        }

        return false;
    }

    private function expect(string $char): void
    {
        if ($this->pos >= $this->length || $this->input[$this->pos] !== $char) {
            $actual = $this->pos < $this->length ? $this->input[$this->pos] : 'end of input';
            throw new DomainException("Expected '{$char}' at position {$this->pos}, got '{$actual}'.");
        }

        $this->pos++;
    }
}
