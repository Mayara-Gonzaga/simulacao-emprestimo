<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class SimulationController extends Controller
{
    public function institutions()
    {
        $path = storage_path('app/institutions.json');
        Log::info('Checking institutions file at: ' . $path);
        Log::info('File exists (file_exists): ' . (file_exists($path) ? 'Yes' : 'No'));
        Log::info('Is readable: ' . (is_readable($path) ? 'Yes' : 'No'));
        Log::info('Storage::exists: ' . (Storage::exists('institutions.json') ? 'Yes' : 'No'));

        if (!Storage::exists('institutions.json')) {
            Log::error('Institutions file not found at: ' . $path);
            return response()->json(['error' => 'Institutions file not found'], 500);
        }

        $data = json_decode(Storage::get('institutions.json'), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::error('Invalid JSON in institutions file: ' . json_last_error_msg());
            return response()->json(['error' => 'Invalid JSON in institutions file'], 500);
        }

        return response()->json($data);
    }

    public function agreements()
    {
        $path = storage_path('app/agreements.json');
        Log::info('Checking agreements file at: ' . $path);
        Log::info('File exists (file_exists): ' . (file_exists($path) ? 'Yes' : 'No'));
        Log::info('Is readable: ' . (is_readable($path) ? 'Yes' : 'No'));
        Log::info('Storage::exists: ' . (Storage::exists('agreements.json') ? 'Yes' : 'No'));

        if (!Storage::exists('agreements.json')) {
            Log::error('Agreements file not found at: ' . $path);
            return response()->json(['error' => 'Agreements file not found'], 500);
        }

        $data = json_decode(Storage::get('agreements.json'), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::error('Invalid JSON in agreements file: ' . json_last_error_msg());
            return response()->json(['error' => 'Invalid JSON in agreements file'], 500);
        }

        return response()->json($data);
    }

    public function simulate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'valor_emprestimo' => 'required|numeric|min:0',
            'instituicoes' => 'nullable|array',
            'instituicoes.*' => 'string',
            'convenios' => 'nullable|array',
            'convenios.*' => 'string',
            'parcela' => 'nullable|integer|min:1'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $path = storage_path('app/rates.json');
        Log::info('Checking rates file at: ' . $path);
        Log::info('File exists (file_exists): ' . (file_exists($path) ? 'Yes' : 'No'));
        Log::info('Is readable: ' . (is_readable($path) ? 'Yes' : 'No'));
        Log::info('Storage::exists: ' . (Storage::exists('rates.json') ? 'Yes' : 'No'));

        if (!Storage::exists('rates.json')) {
            Log::error('Rates file not found at: ' . $path);
            return response()->json(['error' => 'Rates file not found'], 500);
        }

        $rates = json_decode(Storage::get('rates.json'), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::error('Invalid JSON in rates file: ' . json_last_error_msg());
            return response()->json(['error' => 'Invalid JSON in rates file'], 500);
        }

        $valor = $request->input('valor_emprestimo');
        $filtroInstituicoes = $request->input('instituicoes', []);
        $filtroConvenios = $request->input('convenios', []);
        $filtroParcela = $request->input('parcela');

        $response = [];

        foreach ($rates as $rate) {
            $inst = $rate['institution'];
            $conv = $rate['agreement'];

            if (!empty($filtroInstituicoes) && !in_array($inst, $filtroInstituicoes)) {
                continue;
            }
            if (!empty($filtroConvenios) && !in_array($conv, $filtroConvenios)) {
                continue;
            }

            foreach ($rate['installments'] as $index => $parcela) {
                if ($filtroParcela && $filtroParcela != $parcela) {
                    continue;
                }
                if (!isset($rate['coefficients'][$index])) {
                    continue;
                }

                $coef = $rate['coefficients'][$index];
                $valorParcela = round($valor * $coef, 2);

                $response[$inst][] = [
                    'taxa' => $rate['rate'],
                    'parcelas' => $parcela,
                    'valor_parcela' => $valorParcela,
                    'convenio' => $conv
                ];
            }
        }

        return response()->json($response);
    }
}