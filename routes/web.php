<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ActividadController;
use App\Http\Controllers\AgendaController;
use App\Http\Controllers\CatalogoMaterialController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\ExistenciasController;
use App\Http\Controllers\ImportacionController;
use App\Http\Controllers\ControlOperativoController;
use App\Http\Controllers\DashboardDireccionController;
use App\Http\Controllers\DashboardEjecutivoController;
use App\Http\Controllers\DespieceController;
use App\Http\Controllers\PedidosTerminadosController;
use App\Models\Partida;

Route::redirect('/', '/login');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/dashboard', function () {

        if (
            !auth()->user()->hasAnyRole([
                'Diseñador',
                'Supervisor',
                'Administrador'
            ])
        ) {
            return redirect()->route('dashboard.produccion');
        }

        return app(DashboardController::class)->index(
            request()
        );
    })->name('dashboard');
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

    Route::post('/actividad/{id}/reasignar', [ActividadController::class, 'reasignar'])
        ->name('actividad.reasignar');
    Route::get('/archivo/{id}/descargar', [ActividadController::class, 'descargarArchivo'])
        ->name('archivo.descargar');
    Route::post(
        '/actividades/orden',
        [ActividadController::class, 'guardarOrden']
    )->name('actividades.orden');
    Route::get('/agenda', [AgendaController::class, 'index'])
        ->name('agenda.index');
    Route::get('/agenda/eventos', [AgendaController::class, 'eventos'])
        ->name('agenda.eventos');
    Route::post('/agenda', [AgendaController::class, 'store'])
        ->name('agenda.store');
    Route::get('/importacion-pedidos', [ImportacionController::class, 'index'])
        ->middleware('can:ver importacion pedidos')
        ->name('importaciones.index');

    Route::post('/importacion-pedidos/importar', [ImportacionController::class, 'importar'])
        ->middleware('can:importar pedidos')
        ->name('importaciones.importar');

    Route::get('/dashboard-produccion', function () {
        return view('dashboard-produccion');
    })->name('dashboard.produccion');
    Route::get('/control-operativo', [
        ControlOperativoController::class,
        'index'
    ])->name('control.operativo');

    Route::get('/prueba-despiece', function () {

        $partida = Partida::with([
            'despieceProcesos.proceso'
        ])->first();

        if (!$partida) {
            return 'No hay partidas registradas.';
        }

        return response()->json([
            'partida_id' => $partida->id,
            'clave' => $partida->clave,
            'descripcion' => $partida->descripcion,
            'cantidad' => $partida->cantidad,
            'procesos' => $partida->despieceProcesos->map(function ($dp) {
                return [
                    'proceso_id' => $dp->proceso_id,
                    'proceso' => $dp->proceso?->nombre,
                    'aplica' => $dp->aplica,
                    'cantidad_realizada' => $dp->cantidad_realizada,
                    'porcentaje' => $dp->porcentaje,
                ];
            }),
        ]);
    });

    Route::get('/despiece', [DespieceController::class, 'index'])
        ->name('despiece.index');

    Route::post('/despiece/proceso', [
        DespieceController::class,
        'guardarProceso'
    ])->name('despiece.proceso.guardar');

    Route::get('/prueba-avance-pedido/{pedido}', function ($pedidoNo) {

        $pedido = \App\Models\Pedido::with([
            'partidas.despieceProcesos'
        ])
            ->where('pedido_no', $pedidoNo)
            ->firstOrFail();

        return response()->json([
            'pedido' => $pedido->pedido_no,
            'cantidad_partidas' => $pedido->partidas->count(),
            'avance' => $pedido->avance,
            'avance_porcentaje' => $pedido->avance !== null
                ? round($pedido->avance * 100, 2)
                : null,
        ]);
    });

    Route::get('/pedidos-terminados', [
        PedidosTerminadosController::class,
        'index'
    ])->name('pedidos.terminados');


    Route::patch('/control-operativo/{pedido}/fecha-produccion', [
        ControlOperativoController::class,
        'actualizarFechaProduccion'
    ])->name('control-operativo.fecha-produccion');


    Route::patch('/control-operativo/{pedido}/pedido-interno', [
        ControlOperativoController::class,
        'actualizarPedidoInterno'
    ])->name('control-operativo.pedido-interno');

    Route::patch('/control-operativo/{pedido}/comentario', [
        ControlOperativoController::class,
        'actualizarComentario'
    ])->name('control-operativo.comentario');

    // Actividades del Control Operativo
    Route::post(
        '/control-operativo/{pedido}/actividad',
        [ControlOperativoController::class, 'crearActividad']
    )->name('control-operativo.actividad.crear');

    Route::patch(
        '/control-operativo/actividad/{actividad}',
        [ControlOperativoController::class, 'actualizarActividad']
    )->name('control-operativo.actividad.actualizar');

    Route::patch(
        '/control-operativo/actividad/{actividad}/eliminar',
        [ControlOperativoController::class, 'eliminarActividad']
    )->name('control-operativo.actividad.eliminar');


    Route::patch('/control-operativo/{pedido}/responsable', [
        ControlOperativoController::class,
        'actualizarResponsable'
    ])->name('control-operativo.responsable');

    Route::patch('/control-operativo/{pedido}/prioridad', [
        ControlOperativoController::class,
        'actualizarPrioridad'
    ])->name('control-operativo.prioridad');

    Route::patch('/control-operativo/{pedido}/estado', [
        ControlOperativoController::class,
        'actualizarEstado'
    ])->name('control-operativo.estado');

    Route::get('/dashboard-produccion', [
        DashboardEjecutivoController::class,
        'index'
    ])->name('dashboard.produccion');

    Route::get(
        '/catalogo-materiales',
        [CatalogoMaterialController::class, 'index']
    )->name('catalogo-materiales.index');

    Route::post(
        '/catalogo-materiales',
        [CatalogoMaterialController::class, 'guardar']
    )->name('catalogo-materiales.guardar');

    Route::patch(
        '/catalogo-materiales/{catalogoMaterial}',
        [CatalogoMaterialController::class, 'actualizar']
    )->name('catalogo-materiales.actualizar');


    Route::get('/dashboard-direccion', [
        DashboardDireccionController::class,
        'index'
    ])->name('dashboard.direccion');
});

require __DIR__ . '/auth.php';


Route::get('/existencias/{clave}', [ExistenciasController::class, 'consulta'])
    ->name('existencias.consulta');

Route::get('/existencias/estado/{uuid}', [ExistenciasController::class, 'estado'])
    ->name('existencias.estado');

// ==========================================
// CUTLIST - SIN AUTENTICACIÓN
// ==========================================

Route::get('/cutlist', function () {
    return view('cutlist.index');
})->name('cutlist.index');
