<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CalculationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'expression' => $this->expression,
            'result' => $this->result,
            'operator' => $this->operator,
            'operand_a' => $this->operand_a,
            'operand_b' => $this->operand_b,
            'created_at' => $this->created_at->toIso8601String(),
        ];
    }
}
