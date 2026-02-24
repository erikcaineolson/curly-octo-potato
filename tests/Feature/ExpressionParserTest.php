<?php

namespace Tests\Feature;

use App\Services\ExpressionParser;
use DomainException;
use Tests\TestCase;

class ExpressionParserTest extends TestCase
{
    private ExpressionParser $parser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->parser = new ExpressionParser();
    }

    public function test_simple_addition(): void
    {
        $this->assertEqualsWithDelta(5.0, $this->parser->evaluate('2+3'), 0.0001);
    }

    public function test_operator_precedence(): void
    {
        $this->assertEqualsWithDelta(14.0, $this->parser->evaluate('2+3*4'), 0.0001);
    }

    public function test_parentheses_override_precedence(): void
    {
        $this->assertEqualsWithDelta(20.0, $this->parser->evaluate('(2+3)*4'), 0.0001);
    }

    public function test_exponentiation(): void
    {
        $this->assertEqualsWithDelta(8.0, $this->parser->evaluate('2^3'), 0.0001);
    }

    public function test_right_associative_exponentiation(): void
    {
        // 2^3^2 = 2^(3^2) = 2^9 = 512
        $this->assertEqualsWithDelta(512.0, $this->parser->evaluate('2^3^2'), 0.0001);
    }

    public function test_square_root(): void
    {
        $this->assertEqualsWithDelta(3.0, $this->parser->evaluate('sqrt(9)'), 0.0001);
    }

    public function test_complex_expression_from_spec(): void
    {
        // sqrt((((9*9)/12)+(13-4))*2)^2
        // Inner: (81/12) + 9 = 6.75 + 9 = 15.75
        // * 2 = 31.5
        // sqrt(31.5) ≈ 5.6124...
        // ^2 = 31.5
        $result = $this->parser->evaluate('sqrt((((9*9)/12)+(13-4))*2)^2');
        $this->assertEqualsWithDelta(31.5, $result, 0.0001);
    }

    public function test_unary_minus(): void
    {
        $this->assertEqualsWithDelta(-5.0, $this->parser->evaluate('-5'), 0.0001);
        $this->assertEqualsWithDelta(-2.0, $this->parser->evaluate('-5+3'), 0.0001);
    }

    public function test_nested_parentheses(): void
    {
        $this->assertEqualsWithDelta(6.0, $this->parser->evaluate('((2+1))*2'), 0.0001);
    }

    public function test_division(): void
    {
        $this->assertEqualsWithDelta(2.5, $this->parser->evaluate('5/2'), 0.0001);
    }

    public function test_division_by_zero_throws(): void
    {
        $this->expectException(DomainException::class);
        $this->parser->evaluate('5/0');
    }

    public function test_empty_expression_throws(): void
    {
        $this->expectException(DomainException::class);
        $this->parser->evaluate('');
    }

    public function test_invalid_input_throws(): void
    {
        $this->expectException(DomainException::class);
        $this->parser->evaluate('abc');
    }

    public function test_whitespace_is_ignored(): void
    {
        $this->assertEqualsWithDelta(5.0, $this->parser->evaluate(' 2 + 3 '), 0.0001);
    }

    public function test_decimal_numbers(): void
    {
        $this->assertEqualsWithDelta(3.7, $this->parser->evaluate('1.2+2.5'), 0.0001);
    }

    public function test_subtraction(): void
    {
        $this->assertEqualsWithDelta(7.0, $this->parser->evaluate('10-3'), 0.0001);
    }

    public function test_multiplication(): void
    {
        $this->assertEqualsWithDelta(24.0, $this->parser->evaluate('6*4'), 0.0001);
    }
}
