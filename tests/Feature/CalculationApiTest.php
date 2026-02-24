<?php

namespace Tests\Feature;

use App\Models\Calculation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CalculationApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_addition_returns_correct_result(): void
    {
        $response = $this->postJson('/api/calculations', [
            'operand_a' => 9,
            'operand_b' => 3,
            'operator' => 'add',
        ]);

        $response->assertStatus(201);
        $this->assertEquals(12, $response->json('data.result'));
        $this->assertEquals('9 + 3', $response->json('data.expression'));
    }

    public function test_subtraction_returns_correct_result(): void
    {
        $response = $this->postJson('/api/calculations', [
            'operand_a' => 9,
            'operand_b' => 3,
            'operator' => 'subtract',
        ]);

        $response->assertStatus(201);
        $this->assertEquals(6, $response->json('data.result'));
        $this->assertEquals('9 - 3', $response->json('data.expression'));
    }

    public function test_multiplication_returns_correct_result(): void
    {
        $response = $this->postJson('/api/calculations', [
            'operand_a' => 9,
            'operand_b' => 3,
            'operator' => 'multiply',
        ]);

        $response->assertStatus(201);
        $this->assertEquals(27, $response->json('data.result'));
        $this->assertEquals('9 * 3', $response->json('data.expression'));
    }

    public function test_division_returns_correct_result(): void
    {
        $response = $this->postJson('/api/calculations', [
            'operand_a' => 9,
            'operand_b' => 3,
            'operator' => 'divide',
        ]);

        $response->assertStatus(201);
        $this->assertEquals(3, $response->json('data.result'));
        $this->assertEquals('9 / 3', $response->json('data.expression'));
    }

    public function test_division_by_zero_returns_422(): void
    {
        $response = $this->postJson('/api/calculations', [
            'operand_a' => 9,
            'operand_b' => 0,
            'operator' => 'divide',
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('message', 'Division by zero.');
    }

    public function test_invalid_operator_returns_422(): void
    {
        $response = $this->postJson('/api/calculations', [
            'operand_a' => 9,
            'operand_b' => 3,
            'operator' => 'modulus',
        ]);

        $response->assertStatus(422);
    }

    public function test_missing_fields_returns_422(): void
    {
        $response = $this->postJson('/api/calculations', []);

        $response->assertStatus(422);
    }

    public function test_index_returns_calculations_newest_first(): void
    {
        $first = Calculation::create([
            'expression' => '1 + 1',
            'result' => 2,
            'operator' => 'add',
            'operand_a' => 1,
            'operand_b' => 1,
        ]);

        $second = Calculation::create([
            'expression' => '2 + 2',
            'result' => 4,
            'operator' => 'add',
            'operand_a' => 2,
            'operand_b' => 2,
        ]);

        $response = $this->getJson('/api/calculations');

        $response->assertOk()
            ->assertJsonCount(2, 'data');

        // Second created should appear first (newest first by ID)
        $data = $response->json('data');
        $this->assertEquals($second->id, $data[0]['id']);
        $this->assertEquals($first->id, $data[1]['id']);
    }

    public function test_destroy_soft_deletes_single_calculation(): void
    {
        $calc = Calculation::create([
            'expression' => '5 + 5',
            'result' => 10,
            'operator' => 'add',
            'operand_a' => 5,
            'operand_b' => 5,
        ]);

        $response = $this->deleteJson("/api/calculations/{$calc->id}");

        $response->assertStatus(204);

        $this->assertSoftDeleted('calculations', ['id' => $calc->id]);
    }

    public function test_destroy_all_soft_deletes_everything(): void
    {
        Calculation::create([
            'expression' => '1 + 1',
            'result' => 2,
            'operator' => 'add',
            'operand_a' => 1,
            'operand_b' => 1,
        ]);

        Calculation::create([
            'expression' => '2 + 2',
            'result' => 4,
            'operator' => 'add',
            'operand_a' => 2,
            'operand_b' => 2,
        ]);

        $response = $this->deleteJson('/api/calculations/all');

        $response->assertStatus(204);

        $this->assertDatabaseCount('calculations', 2);
        $this->assertEquals(0, Calculation::count());
        $this->assertEquals(2, Calculation::withTrashed()->count());
    }

    public function test_expression_calculation_works(): void
    {
        $response = $this->postJson('/api/calculations', [
            'expression' => '2+3*4',
        ]);

        $response->assertStatus(201);
        $this->assertEquals(14, $response->json('data.result'));
    }

    public function test_complex_expression_from_spec(): void
    {
        $response = $this->postJson('/api/calculations', [
            'expression' => 'sqrt((((9*9)/12)+(13-4))*2)^2',
        ]);

        $response->assertStatus(201);
        $this->assertEqualsWithDelta(31.5, $response->json('data.result'), 0.0001);
    }

    public function test_decimal_operands(): void
    {
        $response = $this->postJson('/api/calculations', [
            'operand_a' => 3.5,
            'operand_b' => 1.5,
            'operator' => 'add',
        ]);

        $response->assertStatus(201);
        $this->assertEquals(5, $response->json('data.result'));
    }

    public function test_calculation_is_persisted(): void
    {
        $this->postJson('/api/calculations', [
            'operand_a' => 10,
            'operand_b' => 5,
            'operator' => 'subtract',
        ]);

        $this->assertDatabaseHas('calculations', [
            'operator' => 'subtract',
            'operand_a' => 10,
            'operand_b' => 5,
            'result' => 5,
        ]);
    }
}
