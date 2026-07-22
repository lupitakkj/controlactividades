<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ActividadController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\UsuarioController;

Route::redirect('/', '/login');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');
    Route::post('/actividades', [ActividadController::class, 'store'])
        ->name('actividades.store');
    Route::post('/actividad/{id}/iniciar', [ActividadController::class, 'iniciar'])
        ->name('actividad.iniciar');
    Route::post('/actividad/{id}/pausar', [ActividadController::class, 'pausar'])
        ->name('actividad.pausar');
    Route::post('/actividad/{id}/terminar', [ActividadController::class, 'terminar'])
        ->name('actividad.terminar');
    Route::post('/actividad/mover/{id}', [ActividadController::class, 'mover'])
        ->name('actividad.mover');
    Route::post('/actividad/{id}/comentario', [ActividadController::class, 'comentar'])
        ->name('actividad.comentar');
    Route::post('/actividad/{id}/archivo', [ActividadController::class, 'subirArchivo'])
        ->name('actividad.archivo');
    Route::put('/actividad/{actividad}', [ActividadController::class, 'update'])
        ->name('actividad.update');
    Route::get('/reportes', [ReporteController::class, 'index'])
        ->name('reportes');
    Route::get('/usuarios', [UsuarioController::class, 'index'])
        ->name('usuarios.index');
    Route::post('/usuarios', [UsuarioController::class, 'store'])
        ->name('usuarios.store');
});

require __DIR__ . '/auth.php';
