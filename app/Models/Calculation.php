<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Calculation extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'expression',
        'result',
        'operator',
        'operand_a',
        'operand_b',
    ];

    protected function casts(): array
    {
        return [
            'result' => 'float',
            'operand_a' => 'float',
            'operand_b' => 'float',
        ];
    }
}
