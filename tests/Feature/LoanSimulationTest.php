<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LoanSimulationTest extends TestCase
{
    use RefreshDatabase;


    public function simulate(Request $request)
    {
        
        return response()->json([
            [
                'instituicao' => 'Banco X',
                'parcela' => $request->parcela,
                'valor_parcela' => 1000,
                'taxa' => 0.02,
                'convenio' => $request->convenios[0],
            ]
        ], 200);
    }
}


