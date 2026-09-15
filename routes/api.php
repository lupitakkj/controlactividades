<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BridgeController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\HojaController;
use App\Http\Controllers\PiezaController;
use App\Http\Controllers\CutListController;

Route::get('/bridge/pendiente', [BridgeController::class, 'pendiente'])
    ->name('bridge.pendiente');

Route::post('/bridge/resultado', [BridgeController::class, 'resultado'])
    ->name('bridge.resultado');

Route::get('/materiales', [MaterialController::class, 'index']);
Route::get('/materiales/{id}', [MaterialController::class, 'show']);

Route::get('/hojas', [HojaController::class, 'index']);
Route::get('/hojas/{id}', [HojaController::class, 'show']);
Route::post('/hojas', [HojaController::class, 'store']);
Route::put('/hojas/{id}', [HojaController::class, 'update']);
Route::delete('/hojas/{id}', [HojaController::class, 'destroy']);

Route::get('/piezas', [PiezaController::class, 'index']);
Route::get('/piezas/{id}', [PiezaController::class, 'show']);
Route::post('/piezas', [PiezaController::class, 'store']);
Route::put('/piezas/{id}', [PiezaController::class, 'update']);
Route::delete('/piezas/{id}', [PiezaController::class, 'destroy']);

Route::post('/cutlist/optimizar', [CutListController::class, 'optimizar']);