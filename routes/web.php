<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SimulationController;

Route::get('/institutions', [SimulationController::class, 'institutions']);
Route::get('/agreements', [SimulationController::class, 'agreements']);
Route::post('/simulate', [SimulationController::class, 'simulate']);
Route::get('/', function () {
    return view('welcome');
});

//Temporário para gerar o token. 
Route::get('/csrf-token', function () {
    return response()->json(['csrf_token' => csrf_token()]);
});