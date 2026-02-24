<?php

namespace App\Services;

use DomainException;

class ExpressionParser
{
    private string $input;
    private int $pos;
    private int $length;

    public function evaluate(string $input): float
    {
        $this->input = preg_replace('/\s+/', '', $input);
        $this->pos = 0;
        $this->length = strlen($this->input);

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

    /**
     * expression = term (('+' | '-') term)*
     */
    private function parseExpression(): float
    {
        $result = $this->parseTerm();

        while ($this->pos < $this->length && in_array($this->input[$this->pos], ['+', '-'])) {
            $op = $this->input[$this->pos];
            $this->pos++;
            $right = $this->parseTerm();
            $result = $op === '+' ? $result + $right : $result - $right;
        }

        return $result;
    }

    /**
     * term = power (('*' | '/') power)*
     */
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

    /**
     * power = unary ('^' power)?   (right-associative)
     */
    private function parsePower(): float
    {
        $base = $this->parseUnary();

        if ($this->pos < $this->length && $this->input[$this->pos] === '^') {
            $this->pos++;
            $exponent = $this->parsePower(); // right-associative recursion
            return pow($base, $exponent);
        }

        return $base;
    }

    /**
     * unary = ('-' unary) | function
     */
    private function parseUnary(): float
    {
        if ($this->pos < $this->length && $this->input[$this->pos] === '-') {
            $this->pos++;
            return -$this->parseUnary();
        }

        return $this->parseFunction();
    }

    /**
     * function = 'sqrt(' expression ')' | atom
     */
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

    /**
     * atom = number | '(' expression ')'
     */
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
