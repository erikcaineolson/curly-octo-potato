<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCalculationRequest;
use App\Http\Resources\CalculationResource;
use App\Models\Calculation;
use App\Services\Calculator;
use App\Services\ExpressionParser;
use DomainException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CalculationController extends Controller
{
    public function __construct(
        private readonly Calculator $calculator,
        private readonly ExpressionParser $expressionParser,
    ) {}

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
                $result = $this->expressionParser->evaluate($data['expression']);

                $calculation = Calculation::create([
                    'expression' => $data['expression'],
                    'result' => $result,
                ]);
            } else {
                $a = (float) $data['operand_a'];
                $b = (float) $data['operand_b'];
                $operator = $data['operator'];

                $result = $this->calculator->calculate($a, $b, $operator);
                $expression = $this->calculator->buildExpression($a, $b, $operator);

                $calculation = Calculation::create([
                    'expression' => $expression,
                    'result' => $result,
                    'operator' => $operator,
                    'operand_a' => $a,
                    'operand_b' => $b,
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
        Calculation::query()
            ->chunkById(500, function ($calculations) {
                $calculations->each->delete();
            });

        return response()->json(null, 204);
    }
}
