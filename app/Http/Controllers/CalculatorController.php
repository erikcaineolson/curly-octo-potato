<?php

namespace App\Http\Controllers;

use App\Http\Resources\CalculationResource;
use App\Models\Calculation;
use Inertia\Inertia;
use Inertia\Response;

class CalculatorController extends Controller
{
    public function index(): Response
    {
        $calculations = Calculation::orderByDesc('id')->paginate(50);

        return Inertia::render('Calculator', [
            'calculations' => CalculationResource::collection($calculations),
        ]);
    }
}
