<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCalculationRequest;
use App\Http\Resources\CalculationResource;
use App\Models\Calculation;
use App\Services\ExpressionParser;
use DomainException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CalculationController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $calculations = Calculation::orderByDesc('id')->paginate(50);

        return CalculationResource::collection($calculations);
    }

    public function store(StoreCalculationRequest $request): JsonResponse
    {
        $data = $request->validated();

        try {
            if ($request->filled('expression') && !$request->filled('operator')) {
                $result = $this->evaluateExpression($data['expression']);

                $calculation = Calculation::create([
                    'expression' => $data['expression'],
                    'result' => $result,
                ]);
            } else {
                $result = $this->calculateSimple(
                    (float) $data['operand_a'],
                    (float) $data['operand_b'],
                    $data['operator']
                );

                $expression = $this->buildExpression(
                    (float) $data['operand_a'],
                    (float) $data['operand_b'],
                    $data['operator']
                );

                $calculation = Calculation::create([
                    'expression' => $expression,
                    'result' => $result,
                    'operator' => $data['operator'],
                    'operand_a' => (float) $data['operand_a'],
                    'operand_b' => (float) $data['operand_b'],
                ]);
            }
        } catch (DomainException $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'errors' => ['calculation' => [$e->getMessage()]],
            ], 422);
        }

        return (new CalculationResource($calculation))
            ->response()
            ->setStatusCode(201);
    }

    public function destroy(Calculation $calculation): JsonResponse
    {
        $calculation->delete();

        return response()->json(null, 204);
    }

    public function destroyAll(): JsonResponse
    {
        Calculation::query()->delete();

        return response()->json(null, 204);
    }

    private function calculateSimple(float $a, float $b, string $operator): float
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

    private function buildExpression(float $a, float $b, string $operator): string
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

    private function evaluateExpression(string $expression): float
    {
        $parser = new ExpressionParser();

        return $parser->evaluate($expression);
    }
}
