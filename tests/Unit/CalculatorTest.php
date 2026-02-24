<?php

namespace Tests\Unit;

use App\Services\Calculator;
use DomainException;
use PHPUnit\Framework\TestCase;

class CalculatorTest extends TestCase
{
    private Calculator $calculator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->calculator = new Calculator();
    }

    public function test_addition(): void
    {
        $this->assertEqualsWithDelta(12.0, $this->calculator->calculate(9, 3, 'add'), 0.0001);
    }

    public function test_subtraction(): void
    {
        $this->assertEqualsWithDelta(6.0, $this->calculator->calculate(9, 3, 'subtract'), 0.0001);
    }

    public function test_multiplication(): void
    {
        $this->assertEqualsWithDelta(27.0, $this->calculator->calculate(9, 3, 'multiply'), 0.0001);
    }

    public function test_division(): void
    {
        $this->assertEqualsWithDelta(3.0, $this->calculator->calculate(9, 3, 'divide'), 0.0001);
    }

    public function test_division_by_zero_throws(): void
    {
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Division by zero.');
        $this->calculator->calculate(9, 0, 'divide');
    }

    public function test_decimal_calculation(): void
    {
        $this->assertEqualsWithDelta(5.0, $this->calculator->calculate(3.5, 1.5, 'add'), 0.0001);
    }

    public function test_build_expression_integer_operands(): void
    {
        $this->assertEquals('9 + 3', $this->calculator->buildExpression(9, 3, 'add'));
        $this->assertEquals('9 - 3', $this->calculator->buildExpression(9, 3, 'subtract'));
        $this->assertEquals('9 * 3', $this->calculator->buildExpression(9, 3, 'multiply'));
        $this->assertEquals('9 / 3', $this->calculator->buildExpression(9, 3, 'divide'));
    }

    public function test_build_expression_decimal_operands(): void
    {
        $this->assertEquals('3.5 + 1.5', $this->calculator->buildExpression(3.5, 1.5, 'add'));
    }
}
