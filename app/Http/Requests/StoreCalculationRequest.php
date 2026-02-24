<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCalculationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'expression' => ['nullable', 'string', 'max:500'],
            'operand_a' => ['nullable', 'numeric'],
            'operand_b' => ['nullable', 'numeric'],
            'operator' => ['nullable', 'string', 'in:add,subtract,multiply,divide'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $hasExpression = $this->filled('expression');
            $hasSimple = $this->filled('operand_a') && $this->filled('operand_b') && $this->filled('operator');

            if (!$hasExpression && !$hasSimple) {
                $validator->errors()->add(
                    'mode',
                    'Provide either an expression or operand_a, operand_b, and operator.'
                );
            }
        });
    }
}
