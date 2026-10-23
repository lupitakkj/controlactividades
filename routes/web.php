<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ActividadController;
use App\Http\Controllers\AgendaController;
use App\Http\Controllers\AlmacenController;
use App\Http\Controllers\AlmacenSalidaController;
use App\Http\Controllers\CatalogoMaterialController;
use App\Http\Controllers\CompraOrdenController;
use App\Http\Controllers\CompraRecepcionController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\ExistenciasController;
use App\Http\Controllers\ImportacionController;
use App\Http\Controllers\ControlOperativoController;
use App\Http\Controllers\DashboardDireccionController;
use App\Http\Controllers\DashboardEjecutivoController;
use App\Http\Controllers\DespieceController;
use App\Http\Controllers\PedidosTerminadosController;
use App\Http\Controllers\ProduccionProductoController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\ReporteEntradasController;
use App\Http\Controllers\TicketCalidadController;
use App\Models\Partida;
use Illuminate\Support\Facades\DB;

Route::redirect('/', '/login');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/dashboard', function () {

        // Compras inicia directamente en el módulo de órdenes de compra.
        if (auth()->user()->hasRole('Compras')) {
            return redirect()->route('compras.ordenes.index');
        }

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

    Route::get('/actividad/{actividad}/bitacora-pausas', [ActividadController::class, 'listarPausas'])
        ->name('actividad.bitacora-pausas');
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

    Route::post(
        '/actividad/{actividad}/subtareas',
        [ActividadController::class, 'crearSubtarea']
    )->name('actividad.subtareas.crear');

    Route::put(
        '/actividad/{actividad}/subtareas/{subtarea}',
        [ActividadController::class, 'actualizarSubtarea']
    )->name('actividad.subtareas.actualizar');

    Route::delete(
        '/actividad/{actividad}/subtareas/{subtarea}',
        [ActividadController::class, 'eliminarSubtarea']
    )->name('actividad.subtareas.eliminar');

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

    Route::post('/importacion-pedidos/procesar-archivo', [ImportacionController::class, 'importar'])
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

    Route::get('/produccion-producto', [
        ProduccionProductoController::class,
        'index'
    ])->name('produccion.producto');

    Route::get('/tickets-calidad', [
        TicketCalidadController::class,
        'index'
    ])
        ->middleware('can:ver tickets calidad')
        ->name('tickets-calidad.index');

    Route::get('/tickets-calidad/crear', [
        TicketCalidadController::class,
        'create'
    ])
        ->middleware('can:crear tickets calidad')
        ->name('tickets-calidad.create');

    Route::post('/tickets-calidad', [
        TicketCalidadController::class,
        'store'
    ])
        ->middleware('can:crear tickets calidad')
        ->name('tickets-calidad.store');

    Route::get('/tickets-calidad/{id}', [
        TicketCalidadController::class,
        'show'
    ])
        ->middleware('can:ver tickets calidad')
        ->name('tickets-calidad.show');

    Route::patch('/tickets-calidad/{id}/estado', [
        TicketCalidadController::class,
        'actualizarEstado'
    ])
        ->middleware('can:atender tickets calidad')
        ->name('tickets-calidad.estado');

    Route::get('/tickets-calidad/archivo/{id}/descargar', [
        TicketCalidadController::class,
        'descargarArchivo'
    ])
        ->middleware('can:ver tickets calidad')
        ->name('tickets-calidad.archivo.descargar');

    Route::get(
        '/actividad/{actividad}/subtareas',
        [ActividadController::class, 'listarSubtareas']
    )->name('actividad.subtareas.listar');



    Route::prefix('compras')->name('compras.')->group(function () {
        Route::get('/proveedores', [ProveedorController::class, 'index'])->name('proveedores.index');
        Route::post('/proveedores', [ProveedorController::class, 'store'])->name('proveedores.store');
        Route::put('/proveedores/{proveedor}', [ProveedorController::class, 'update'])->name('proveedores.update');
        Route::patch('/proveedores/{proveedor}/estado', [ProveedorController::class, 'cambiarEstado'])->name('proveedores.estado');

        Route::get('/almacenes', [AlmacenController::class, 'index'])->name('almacenes.index');
        Route::post('/almacenes', [AlmacenController::class, 'store'])->name('almacenes.store');
        Route::put('/almacenes/{almacen}', [AlmacenController::class, 'update'])->name('almacenes.update');
        Route::patch('/almacenes/{almacen}/estado', [AlmacenController::class, 'cambiarEstado'])->name('almacenes.estado');

        Route::get('/ordenes', [CompraOrdenController::class, 'index'])->name('ordenes.index');
        Route::get('/ordenes/crear', [CompraOrdenController::class, 'create'])->name('ordenes.create');
        Route::post('/ordenes/leer-pdf', [CompraOrdenController::class, 'leerPdf'])->name('ordenes.leer-pdf');
        Route::post('/ordenes', [CompraOrdenController::class, 'store'])->name('ordenes.store');
        Route::get('/ordenes/{orden}', [CompraOrdenController::class, 'show'])->name('ordenes.show');

        Route::post('/ordenes/proveedores', [CompraOrdenController::class, 'guardarProveedorDesdeOrden'])
            ->name('ordenes.proveedores.store');
        Route::post('/ordenes/{orden}/recepciones', [CompraRecepcionController::class, 'store'])
            ->name('recepciones.store');
        Route::post('/ordenes/{orden}/cerrar-con-faltantes', [CompraRecepcionController::class, 'cerrarConFaltantes'])
            ->name('recepciones.cerrar-faltantes');
        Route::get('/recepciones/{recepcion}/evidencia', [CompraRecepcionController::class, 'descargarEvidencia'])
            ->name('recepciones.evidencia');
        Route::get('/reportes/entradas', [ReporteEntradasController::class, 'index'])
            ->name('reportes.entradas.index');

        Route::get('/reportes/entradas/exportar', [ReporteEntradasController::class, 'exportar'])
            ->name('reportes.entradas.exportar');

        Route::get('/ordenes/{orden}/editar', [CompraOrdenController::class, 'edit'])->name('ordenes.edit');
        Route::put('/ordenes/{orden}', [CompraOrdenController::class, 'update'])->name('ordenes.update');
    });

    Route::get('/salidas-almacen', [AlmacenSalidaController::class, 'index'])->name('almacen-salidas.index');
    Route::get('/salidas-almacen/crear', [AlmacenSalidaController::class, 'create'])->name('almacen-salidas.create');
    Route::post('/salidas-almacen', [AlmacenSalidaController::class, 'store'])->name('almacen-salidas.store');
    Route::get('/salidas-almacen/exportar', [AlmacenSalidaController::class, 'exportarListado'])->name('almacen-salidas.exportar-listado');
    Route::get('/salidas-almacen/{salida}/exportar', [AlmacenSalidaController::class, 'exportarSalida'])->name('almacen-salidas.exportar');
    Route::get('/salidas-almacen/notificaciones', [AlmacenSalidaController::class, 'notificaciones'])->name('almacen-salidas.notificaciones');
    Route::get('/salidas-almacen/notificaciones/{notificacion}/leer', [AlmacenSalidaController::class, 'marcarNotificacionLeida'])->name('almacen-salidas.notificaciones.leer');
    Route::get('/salidas-almacen/{salida}', [AlmacenSalidaController::class, 'show'])->name('almacen-salidas.show');
    Route::post('/salidas-almacen/{salida}/solicitar-cancelacion', [AlmacenSalidaController::class, 'solicitarCancelacion'])->name('almacen-salidas.cancelaciones.solicitar');
    Route::post('/salidas-almacen/solicitudes/{solicitud}/resolver', [AlmacenSalidaController::class, 'resolverCancelacion'])->name('almacen-salidas.cancelaciones.resolver');
    Route::get('/salidas-almacen', [AlmacenSalidaController::class, 'index'])->name('almacen-salidas.index');
    Route::get('/salidas-almacen/crear', [AlmacenSalidaController::class, 'create'])->name('almacen-salidas.create');
    Route::post('/salidas-almacen', [AlmacenSalidaController::class, 'store'])->name('almacen-salidas.store');
    Route::get('/salidas-almacen/notificaciones', [AlmacenSalidaController::class, 'notificaciones'])->name('almacen-salidas.notificaciones');
    Route::get('/salidas-almacen/notificaciones/{notificacion}/leer', [AlmacenSalidaController::class, 'marcarNotificacionLeida'])->name('almacen-salidas.notificaciones.leer');
    Route::get('/salidas-almacen/{salida}', [AlmacenSalidaController::class, 'show'])->name('almacen-salidas.show');
    Route::post('/salidas-almacen/{salida}/solicitar-cancelacion', [AlmacenSalidaController::class, 'solicitarCancelacion'])->name('almacen-salidas.cancelaciones.solicitar');
    Route::post('/salidas-almacen/solicitudes/{solicitud}/resolver', [AlmacenSalidaController::class, 'resolverCancelacion'])->name('almacen-salidas.cancelaciones.resolver');


    Route::patch('/control-operativo/{pedido}/resultado-entrega', [ControlOperativoController::class, 'actualizarResultadoEntrega'])
        ->name('control-operativo.resultado-entrega');
});

require __DIR__ . '/auth.php';

Route::get('/prueba-ticketsapp', function () {
    try {
        $resultado = DB::connection('ticketsapp')
            ->select('SELECT 1 AS conectado');

        return response()->json([
            'ok' => true,
            'mensaje' => 'Conexión a ticketsapp correcta',
            'resultado' => $resultado,
        ]);
    } catch (\Throwable $e) {
        return response()->json([
            'ok' => false,
            'mensaje' => $e->getMessage(),
        ], 500);
    }
});
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

Route::get('/prueba-tablas-ticketsapp', function () {

    try {

        $tablas = DB::connection('ticketsapp')
            ->select('SHOW TABLES');

        return response()->json([
            'ok' => true,
            'tablas' => $tablas,
        ]);
    } catch (\Throwable $e) {

        return response()->json([
            'ok' => false,
            'mensaje' => $e->getMessage(),
        ], 500);
    }
});

Route::get('/prueba-estructura-ticketsapp', function () {

    try {

        $columnas = DB::connection('ticketsapp')
            ->select('SHOW COLUMNS FROM tickets');

        return response()->json([
            'ok' => true,
            'columnas' => $columnas,
        ]);
    } catch (\Throwable $e) {

        return response()->json([
            'ok' => false,
            'mensaje' => $e->getMessage(),
        ], 500);
    }
});

Route::get('/prueba-estructura-mensajes-ticket', function () {

    try {

        $columnas = DB::connection('ticketsapp')
            ->select('SHOW COLUMNS FROM mensajes_ticket');

        return response()->json([
            'ok' => true,
            'columnas' => $columnas,
        ]);
    } catch (\Throwable $e) {

        return response()->json([
            'ok' => false,
            'mensaje' => $e->getMessage(),
        ], 500);
    }
});

Route::get('/prueba-tickets-existentes', function () {

    try {

        $tickets = DB::connection('ticketsapp')
            ->table('tickets')
            ->select([
                'id',
                'ticket_codigo',
                'nombre',
                'correo',
                'area',
                'tipo_calidad',
                'prioridad',
                'estado',
                'problema',
                'numero_orden',
                'fecha',
            ])
            ->orderByDesc('id')
            ->limit(20)
            ->get();

        return response()->json([
            'ok' => true,
            'tickets' => $tickets,
        ]);
    } catch (\Throwable $e) {

        return response()->json([
            'ok' => false,
            'mensaje' => $e->getMessage(),
        ], 500);
    }
});

Route::get('/prueba-estructura-ticket-archivos', function () {

    try {

        $columnas = DB::connection('ticketsapp')
            ->select('SHOW COLUMNS FROM ticket_archivos');

        return response()->json([
            'ok' => true,
            'columnas' => $columnas,
        ]);
    } catch (\Throwable $e) {

        return response()->json([
            'ok' => false,
            'mensaje' => $e->getMessage(),
        ], 500);
    }
});

Route::get('/prueba-archivos-ticket/{ticketId}', function ($ticketId) {

    try {

        $archivos = DB::connection('ticketsapp')
            ->table('ticket_archivos')
            ->where('ticket_id', $ticketId)
            ->select([
                'id',
                'ticket_id',
                'nombre_archivo',
                'ruta',
                'tipo',
                'tamaño',
                'fecha_subida',
            ])
            ->orderBy('id')
            ->get();

        return response()->json([
            'ok' => true,
            'archivos' => $archivos,
        ]);
    } catch (\Throwable $e) {

        return response()->json([
            'ok' => false,
            'mensaje' => $e->getMessage(),
        ], 500);
    }
});
