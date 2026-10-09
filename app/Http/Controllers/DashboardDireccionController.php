<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use App\Models\SemanaOperativa;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardDireccionController extends Controller
{
    public function index(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | 1. FECHA Y HORA ACTUAL
        |--------------------------------------------------------------------------
        */

        $ahora = Carbon::now();
        $hoy = Carbon::today();


        /*
        |--------------------------------------------------------------------------
        | 2. OBTENER SEMANA OPERATIVA ACTUAL
        |--------------------------------------------------------------------------
        |
        | La semana cambia cada martes a las 18:30.
        |
        */

        $semanaCandidata = SemanaOperativa::whereDate(
            'fecha_inicio',
            '<=',
            $ahora->toDateString()
        )
            ->orderByDesc('fecha_inicio')
            ->first();


        if ($semanaCandidata) {

            $momentoActivacion = Carbon::parse(
                $semanaCandidata->fecha_inicio
            )->setTime(18, 30, 0);


            if ($ahora->lt($momentoActivacion)) {

                $semanaOperativa = SemanaOperativa::where(
                    'fecha_inicio',
                    '<',
                    $semanaCandidata->fecha_inicio
                )
                    ->orderByDesc('fecha_inicio')
                    ->first();
            } else {

                $semanaOperativa = $semanaCandidata;
            }
        } else {

            $semanaOperativa = null;
        }


        /*
        |--------------------------------------------------------------------------
        | 3. RESPALDO DE SEMANA OPERATIVA
        |--------------------------------------------------------------------------
        */

        if (!$semanaOperativa) {

            $semanaOperativa = SemanaOperativa::where(
                'anio',
                $hoy->year
            )
                ->whereDate(
                    'fecha_inicio',
                    '<=',
                    $hoy->toDateString()
                )
                ->whereDate(
                    'fecha_fin',
                    '>=',
                    $hoy->toDateString()
                )
                ->first();
        }


        /*
        |--------------------------------------------------------------------------
        | 4. VALIDAR SEMANA
        |--------------------------------------------------------------------------
        */

        if (!$semanaOperativa) {

            abort(
                404,
                'No existe una semana operativa configurada.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | 5. DATOS DE LA SEMANA ACTUAL
        |--------------------------------------------------------------------------
        */

        $semana = $semanaOperativa->semana;

        $inicioSemana = Carbon::parse(
            $semanaOperativa->fecha_inicio
        )->startOfDay();

        $finSemana = Carbon::parse(
            $semanaOperativa->fecha_fin
        )->endOfDay();

   

        /*
        |--------------------------------------------------------------------------
        | 6. OBTENER PEDIDOS
        |--------------------------------------------------------------------------
        */



        $pedidos = Pedido::with([
            'controlOperativo',
            'partidas.despieceProcesos.proceso',
        ])->get();


        /*
        |--------------------------------------------------------------------------
        | 6. PLANEACIÓN ECONÓMICA
        |--------------------------------------------------------------------------
        |
        | Planeación considera TODOS los pedidos:
        | - Externos
        | - Internos
        |
        | Meta semanal  = $630,000
        | Meta mensual  = $2,520,000
        |
        */

        $metaSemanal = 630000;
        $metaMensual = 2520000;
        /*
        |--------------------------------------------------------------------------
        | PLANEACIÓN SEMANAL
        |--------------------------------------------------------------------------
        |
        | La semana de planeación se determina de la misma manera que la
        | proyección:
        |
        | - Pedido pendiente:
        |       usa fecha_entrega.
        |
        | - Pedido terminado:
        |       usa fecha_terminado.
        |
        | Esto significa que si un pedido estaba programado para una semana
        | posterior pero se termina antes, el importe entra en la semana
        | en la que realmente se terminó.
        |
        | Así Planeación semanal y Proyección siempre manejan la misma base.
        |
        */

        $pedidosPlaneacionSemanal = $pedidos->filter(
            function ($pedido) use ($inicioSemana, $finSemana) {

                /*
                |--------------------------------------------------------------------------
                | PEDIDO YA TERMINADO
                |--------------------------------------------------------------------------
                |
                | Si existe fecha_terminado, esta es la fecha que determina
                | la semana real en la que se realizó.
                |
                */

                if (!empty($pedido->fecha_terminado)) {

                    $fechaReal = Carbon::parse(
                        $pedido->fecha_terminado
                    )->startOfDay();

                    return $fechaReal->between(
                        $inicioSemana->copy()->startOfDay(),
                        $finSemana->copy()->endOfDay()
                    );
                }


                /*
                |--------------------------------------------------------------------------
                | PEDIDO PENDIENTE
                |--------------------------------------------------------------------------
                |
                | Si todavía no está terminado, conserva su fecha programada.
                |
                */

                if (empty($pedido->fecha_entrega)) {
                    return false;
                }

                $fechaEntrega = Carbon::parse(
                    $pedido->fecha_entrega
                )->startOfDay();

                return $fechaEntrega->between(
                    $inicioSemana->copy()->startOfDay(),
                    $finSemana->copy()->endOfDay()
                );
            }
        );


        /*
        |--------------------------------------------------------------------------
        | IMPORTE PROGRAMADO / REAL DE LA SEMANA
        |--------------------------------------------------------------------------
        */

        $programadoSemanal = $pedidosPlaneacionSemanal->sum(
            function ($pedido) {

                return (float) $pedido->importe_total;
            }
        );


        /*
        |--------------------------------------------------------------------------
        | FALTANTE SEMANAL
        |--------------------------------------------------------------------------
        */

        $faltanteSemanal = max(
            0,
            $metaSemanal - $programadoSemanal
        );


        /*
        |--------------------------------------------------------------------------
        | PORCENTAJE SEMANAL
        |--------------------------------------------------------------------------
        */

        $porcentajeSemanal = $metaSemanal > 0
            ? ($programadoSemanal / $metaSemanal) * 100
            : 0;





        /*
|--------------------------------------------------------------------------
| PLANEACIÓN MENSUAL
|--------------------------------------------------------------------------
*/

        $inicioMes = $inicioSemana->copy()->startOfMonth();
        $finMes = $inicioSemana->copy()->endOfMonth();


        $pedidosPlaneacionMensual = $pedidos->filter(
            function ($pedido) use ($inicioMes, $finMes) {

                if (empty($pedido->fecha_entrega)) {
                    return false;
                }

                $fechaEntrega = Carbon::parse(
                    $pedido->fecha_entrega
                )->startOfDay();

                return $fechaEntrega->between(
                    $inicioMes->copy()->startOfDay(),
                    $finMes->copy()->startOfDay()
                );
            }
        );


        $programadoMensual = $pedidosPlaneacionMensual->sum(
            function ($pedido) {
                return (float) $pedido->importe_total;
            }
        );


        $faltanteMensual = max(
            0,
            $metaMensual - $programadoMensual
        );


        $porcentajeMensual = $metaMensual > 0
            ? ($programadoMensual / $metaMensual) * 100
            : 0;


        /*
        |--------------------------------------------------------------------------
        | 7. IDENTIFICAR PEDIDOS ACTIVOS
        |--------------------------------------------------------------------------
        |
        | Activo:
        | - Avance menor al 100%
        | - No está TERMINADO
        |
        */

        $pedidosActivosCollection = $pedidos->filter(function ($pedido) {

            $estado = strtoupper(
                trim(
                    $pedido->controlOperativo?->estado_operativo ?? ''
                )
            );

            return $pedido->avance < 1
                && in_array(
                    $estado,
                    ['EN PROCESO', 'POR INICIAR'],
                    true
                );
        });


        /*
        |--------------------------------------------------------------------------
        | 8. CANTIDAD DE PEDIDOS ACTIVOS
        |--------------------------------------------------------------------------
        */

        $pedidosActivos = $pedidosActivosCollection->count();


        /*
        |--------------------------------------------------------------------------
        | 9. VALOR TOTAL DE PEDIDOS ACTIVOS
        |--------------------------------------------------------------------------
        */

        $valorPedidos = $pedidosActivosCollection->sum(function ($pedido) {

            return (float) $pedido->importe_total;
        });


        /*
        |--------------------------------------------------------------------------
        | 10. PEDIDOS ENTREGADOS DURANTE LA SEMANA ACTUAL
        |--------------------------------------------------------------------------
        |
        | Se utiliza fecha_terminado.
        |
        | Estos pedidos NO forman parte de los pedidos activos.
        |
        */

        $pedidosEntregadosSemana = $pedidos->filter(function ($pedido) use (
            $inicioSemana,
            $finSemana
        ) {

            if (empty($pedido->fecha_terminado)) {
                return false;
            }

            $fechaTerminado = Carbon::parse(
                $pedido->fecha_terminado
            )->startOfDay();

            return $fechaTerminado->between(
                $inicioSemana->copy()->startOfDay(),
                $finSemana->copy()->startOfDay()
            );
        });


        /*
        |--------------------------------------------------------------------------
        | 11. IMPORTE ENTREGADO EN LA SEMANA
        |--------------------------------------------------------------------------
        */

        $valorEntregadoSemana = $pedidosEntregadosSemana->sum(
            function ($pedido) {

                return (float) $pedido->importe_total;
            }
        );


        /*
        |--------------------------------------------------------------------------
        | 12. PEDIDOS EN PRODUCCIÓN
        |--------------------------------------------------------------------------
        |
        | EN PRODUCCIÓN se determina ÚNICAMENTE por estado operativo:
        |
        |   estado_operativo = EN PROCESO
        |
        | La fecha de producción NO modifica esta categoría.
        |
        */

        $pedidosEnProduccion = $pedidosActivosCollection->filter(
            function ($pedido) {

                $estado = strtoupper(
                    trim(
                        $pedido->controlOperativo?->estado_operativo ?? ''
                    )
                );

                return $estado === 'EN PROCESO';
            }
        );


        /*
        |--------------------------------------------------------------------------
        | 13. IMPORTE EN PRODUCCIÓN
        |--------------------------------------------------------------------------
        */

        $valorEnProduccion = $pedidosEnProduccion->sum(
            function ($pedido) {

                return (float) $pedido->importe_total;
            }
        );


        /*
        |--------------------------------------------------------------------------
        | 14. PEDIDOS POR INICIAR
        |--------------------------------------------------------------------------
        |
        | POR INICIAR se determina ÚNICAMENTE por estado operativo:
        |
        |   estado_operativo = POR INICIAR
        |
        */

        $pedidosPorIniciar = $pedidosActivosCollection->filter(
            function ($pedido) {

                $estado = strtoupper(
                    trim(
                        $pedido->controlOperativo?->estado_operativo ?? ''
                    )
                );

                return $estado === 'POR INICIAR';
            }
        );


        /*
        |--------------------------------------------------------------------------
        | 15. IMPORTE POR INICIAR
        |--------------------------------------------------------------------------
        */

        $valorPorIniciar = $pedidosPorIniciar->sum(
            function ($pedido) {

                return (float) $pedido->importe_total;
            }
        );


        /*
        |--------------------------------------------------------------------------
        | 16. PEDIDOS ATRASADOS
        |--------------------------------------------------------------------------
        |
        | ATRASADO es una subdivisión de EN PROCESO:
        |
        |   EN PROCESO + fecha_entrega < hoy = ATRASADO
        |
        */

        $pedidosAtrasados = $pedidosActivosCollection->filter(
            function ($pedido) use ($hoy) {

                if (empty($pedido->fecha_entrega)) {
                    return false;
                }

                $fechaEntrega = Carbon::parse(
                    $pedido->fecha_entrega
                )->startOfDay();

                return $fechaEntrega->lt($hoy);
            }
        );


        /*
        |--------------------------------------------------------------------------
        | 17. IMPORTE ATRASADO
        |--------------------------------------------------------------------------
        */

        $valorAtrasado = $pedidosAtrasados->sum(
            function ($pedido) {

                return (float) $pedido->importe_total;
            }
        );


        /*
        |--------------------------------------------------------------------------
        | 18. PEDIDOS POR ENTREGAR
        |--------------------------------------------------------------------------
        |
        | POR ENTREGAR es la otra subdivisión de EN PROCESO:
        |
        |   EN PROCESO + fecha_entrega >= hoy = POR ENTREGAR
        |
        */

        $pedidosPorEntregar = $pedidosEnProduccion->filter(
            function ($pedido) use ($hoy) {

                if (empty($pedido->fecha_entrega)) {
                    return false;
                }

                $fechaEntrega = Carbon::parse(
                    $pedido->fecha_entrega
                )->startOfDay();

                return $fechaEntrega->gte($hoy);
            }
        );


        /*
        |--------------------------------------------------------------------------
        | 19. IMPORTE POR ENTREGAR
        |--------------------------------------------------------------------------
        */

        $valorPorEntregar = $pedidosPorEntregar->sum(
            function ($pedido) {

                return (float) $pedido->importe_total;
            }
        );



        /*
        |--------------------------------------------------------------------------
        | 20. SEMÁFORO DE PEDIDOS
        |--------------------------------------------------------------------------
        |
        | SIN FECHA
        |    No tiene fecha de entrega y NO está TERMINADO.
        |
        | Para pedidos activos (EN PROCESO y POR INICIAR):
        |
        |    ATRASADO
        |       fecha_entrega < hoy
        |
        |    EN RIESGO
        |       faltan 7 días o menos para la fecha de entrega.
        |
        |    EN TIEMPO
        |       faltan más de 7 días para la fecha de entrega.
        |
        | La población coincide con Control Operativo:
        | EN PROCESO + POR INICIAR, excluyendo TERMINADO.
        |
        */

        $pedidosEnTiempo = collect();
        $pedidosEnRiesgo = collect();

        /*
         * SIN FECHA
         *
         * Regla:
         * - fecha_entrega = NULL / vacía
         * - estado_operativo != TERMINADO
         *
         * La regla se aplica a TODOS los pedidos.
         */
        $pedidosSinFecha = $pedidos->filter(
            function ($pedido) {

                $estado = strtoupper(
                    trim(
                        $pedido->controlOperativo?->estado_operativo ?? ''
                    )
                );

                $fechaEntrega =
                    $pedido->fecha_entrega;

                return empty($fechaEntrega)
                    && $estado !== 'TERMINADO';
            }
        )->values();


        /*
         * SEMÁFORO PARA PEDIDOS ACTIVOS
         *
         * IMPORTANTE:
         * El Control Operativo considera para el semáforo los pedidos
         * activos que pueden estar EN PROCESO o POR INICIAR.
         *
         * Por eso aquí NO utilizamos únicamente $pedidosEnProduccion.
         * Utilizamos $pedidosActivosCollection para que un pedido
         * POR INICIAR con fecha de entrega a más de 7 días también
         * se contabilice como EN TIEMPO.
         */
        foreach ($pedidosActivosCollection as $pedido) {

            $fechaEntrega =
                $pedido->fecha_entrega;

            /*
             * Los pedidos EN PROCESO sin fecha ya fueron incluidos
             * en SIN FECHA.
             */
            if (empty($fechaEntrega)) {
                continue;
            }

            $fechaEntrega = Carbon::parse(
                $fechaEntrega
            )->startOfDay();

            /*
            * ATRASADO
            */
            if ($fechaEntrega->lt($hoy)) {

                continue;
            }

            /*
            * DÍAS RESTANTES
            */
            $diasRestantes = $hoy->diffInDays(
                $fechaEntrega,
                false
            );

            /*
            * EN RIESGO
            *
            * Si faltan 7 días o menos para la fecha de entrega.
            */
            if ($diasRestantes <= 7) {

                $pedidosEnRiesgo->push($pedido);
            } else {

                /*
                * EN TIEMPO
                *
                * Faltan más de 7 días.
                */
                $pedidosEnTiempo->push($pedido);
            }
        }


        /*
        |--------------------------------------------------------------------------
        | CANTIDADES DEL SEMÁFORO
        |--------------------------------------------------------------------------
        */

        $cantidadEnTiempo =
            $pedidosEnTiempo->count();

        $cantidadEnRiesgo =
            $pedidosEnRiesgo->count();

        $cantidadAtrasados =
            $pedidosAtrasados->count();

        $cantidadSinFecha =
            $pedidosSinFecha->count();


        /*
        |--------------------------------------------------------------------------
        | IMPORTES DEL SEMÁFORO
        |--------------------------------------------------------------------------
        */

        $valorEnTiempo = $pedidosEnTiempo->sum(
            function ($pedido) {
                return (float) $pedido->importe_total;
            }
        );

        $valorEnRiesgo = $pedidosEnRiesgo->sum(
            function ($pedido) {
                return (float) $pedido->importe_total;
            }
        );

        $valorSinFecha = $pedidosSinFecha->sum(
            function ($pedido) {
                return (float) $pedido->importe_total;
            }
        );


        /*
        |--------------------------------------------------------------------------
        | 20. COMPROBACIÓN DE LA DISTRIBUCIÓN
        |--------------------------------------------------------------------------
        |
        | Comprobaciones operativas:
        |
        |   POR ENTREGAR + ATRASADO = EN PRODUCCIÓN
        |
        |   EN PRODUCCIÓN + POR INICIAR = VALOR DE PEDIDOS
        |
        | Estas comprobaciones son independientes de la dona, que ahora
        | utiliza los seis estados de situación.
        |
        */

        $valorSubdivididoProduccion =
            $valorPorEntregar
            + $valorAtrasado;

        $diferenciaProduccion =
            $valorEnProduccion
            - $valorSubdivididoProduccion;

        $valorDistribuido =
            $valorEnProduccion
            + $valorPorIniciar;

        $diferenciaTotalPedidos =
            $valorPedidos
            - $valorDistribuido;


        /*
        |--------------------------------------------------------------------------
        | 21. SITUACIÓN ECONÓMICA
        |--------------------------------------------------------------------------
        |
        | La dona utilizará estos valores.
        |
        */

        $situacionImportes = [

            'Entregados' => $valorEntregadoSemana,

            'Por iniciar' => $valorPorIniciar,

            'En tiempo' => $valorEnTiempo,

            'En riesgo' => $valorEnRiesgo,

            'Atrasados' => $valorAtrasado,

            'Sin fecha' => $valorSinFecha,

        ];


        /*
        |--------------------------------------------------------------------------
        | 22. CANTIDAD DE PEDIDOS POR SITUACIÓN
        |--------------------------------------------------------------------------
        */

        $situacionPedidos = [

            'Entregados' => $pedidosEntregadosSemana->count(),

            'Por iniciar' => $pedidosPorIniciar->count(),

            'En tiempo' => $cantidadEnTiempo,

            'En riesgo' => $cantidadEnRiesgo,

            'Atrasados' => $cantidadAtrasados,

            'Sin fecha' => $cantidadSinFecha,

        ];


        /*
        |--------------------------------------------------------------------------
        | 23. VALOR TOTAL REPRESENTADO EN LA DONA
        |--------------------------------------------------------------------------
        |
        | Es:
        |
        | Entregados de la semana
        | +
        | Todos los pedidos activos
        |
        */

        $valorTotalDona =
            $valorEntregadoSemana
            + $valorPorIniciar
            + $valorEnTiempo
            + $valorEnRiesgo
            + $valorAtrasado
            + $valorSinFecha;
        /*
        |--------------------------------------------------------------------------
        | 24. NAVEGACIÓN DE SEMANAS PARA LA PROYECCIÓN
        |--------------------------------------------------------------------------
        |
        | La proyección muestra 4 semanas.
        |
        | offset_proyeccion = 0
        |     Semana actual + 3 siguientes.
        |
        | offset_proyeccion = -1
        |     Una semana hacia atrás.
        |
        | offset_proyeccion = +1
        |     Una semana hacia adelante.
        |
        | El resto del dashboard continúa utilizando la semana operativa actual.
        | Solamente se desplaza la sección de PROYECCIÓN.
        |
        */

        $offsetProyeccion = (int) $request->query(
            'offset_proyeccion',
            0
        );


        /*
        |--------------------------------------------------------------------------
        | Todas las semanas disponibles
        |--------------------------------------------------------------------------
        */

        $semanasDisponibles = SemanaOperativa::orderBy(
            'fecha_inicio'
        )->get();


        /*
        |--------------------------------------------------------------------------
        | Ubicar la semana operativa actual
        |--------------------------------------------------------------------------
        */

        $indiceSemanaActual =
            $semanasDisponibles->search(
                function ($semanaDisponible) use ($semanaOperativa) {

                    return $semanaDisponible->id ==
                        $semanaOperativa->id;
                }
            );


        if ($indiceSemanaActual === false) {
            $indiceSemanaActual = 0;
        }


        /*
        |--------------------------------------------------------------------------
        | Primera semana que se mostrará
        |--------------------------------------------------------------------------
        |
        | Cada movimiento cambia una semana.
        | Siempre intentamos mostrar 4 semanas.
        |
        */

        $indiceInicioProyeccion =
            $indiceSemanaActual + $offsetProyeccion;


        $maxIndiceInicio =
            max(
                0,
                $semanasDisponibles->count() - 4
            );


        $indiceInicioProyeccion =
            max(
                0,
                min(
                    $indiceInicioProyeccion,
                    $maxIndiceInicio
                )
            );


        /*
        |--------------------------------------------------------------------------
        | Semanas visibles
        |--------------------------------------------------------------------------
        */

        $proximasSemanas =
            $semanasDisponibles
                ->slice(
                    $indiceInicioProyeccion,
                    4
                )
                ->values();


        /*
        |--------------------------------------------------------------------------
        | Botones de navegación
        |--------------------------------------------------------------------------
        */

        $puedeRetrocederProyeccion =
            $indiceInicioProyeccion > 0;


        $puedeAvanzarProyeccion =
            (
                $indiceInicioProyeccion + 4
            ) < $semanasDisponibles->count();


        /*
        |--------------------------------------------------------------------------
        | 25. PROYECCIÓN DE LAS SEMANAS SELECCIONADAS
        |--------------------------------------------------------------------------
        */

        $proyeccionSemanas = collect();


        foreach ($proximasSemanas as $semanaFutura) {

            $inicio = Carbon::parse(
                $semanaFutura->fecha_inicio
            )->startOfDay();

            $fin = Carbon::parse(
                $semanaFutura->fecha_fin
            )->endOfDay();


            /*
            |--------------------------------------------------------------------------
            | PEDIDOS DE LA SEMANA
            |--------------------------------------------------------------------------
            |
            | Pedido entregado:
            |     semana de fecha_terminado.
            |
            | Pedido pendiente:
            |     semana de fecha_entrega.
            |
            */

            $pedidosSemana = $pedidos->filter(
                function ($pedido) use ($inicio, $fin) {

                    /*
                    | Pedido ya terminado
                    */

                    if (!empty($pedido->fecha_terminado)) {

                        $fechaTerminado = Carbon::parse(
                            $pedido->fecha_terminado
                        )->startOfDay();

                        return $fechaTerminado->between(
                            $inicio->copy()->startOfDay(),
                            $fin->copy()->endOfDay()
                        );
                    }


                    /*
                    | Pedido pendiente
                    */

                    if (empty($pedido->fecha_entrega)) {
                        return false;
                    }

                    $fechaEntrega = Carbon::parse(
                        $pedido->fecha_entrega
                    )->startOfDay();

                    return $fechaEntrega->between(
                        $inicio->copy()->startOfDay(),
                        $fin->copy()->endOfDay()
                    );
                }
            );


            /*
            |--------------------------------------------------------------------------
            | IMPORTE TOTAL
            |--------------------------------------------------------------------------
            */

            $importeSemana = $pedidosSemana->sum(
                function ($pedido) {

                    return (float) $pedido->importe_total;
                }
            );


            /*
            |--------------------------------------------------------------------------
            | ENTREGADOS
            |--------------------------------------------------------------------------
            */

            $pedidosEntregadosSemana = $pedidosSemana
                ->filter(function ($pedido) {

                    return !empty($pedido->fecha_terminado);
                })
                ->map(function ($pedido) {

                    return [
                        'id' => $pedido->id,

                        'pedido_no' => $pedido->pedido_no,

                        'cliente' => $pedido->cliente,

                        'importe' => (float) $pedido->importe_total,

                        'fecha_terminado' =>
                        Carbon::parse(
                            $pedido->fecha_terminado
                        )->format('d/m/Y'),
                    ];
                })
                ->values()
                ->all();


            /*
            |--------------------------------------------------------------------------
            | PENDIENTES
            |--------------------------------------------------------------------------
            */

            $pedidosPendientesSemana = $pedidosSemana
                ->filter(function ($pedido) {

                    return empty($pedido->fecha_terminado);
                })
                ->map(function ($pedido) {

                    return [
                        'id' => $pedido->id,

                        'pedido_no' => $pedido->pedido_no,

                        'cliente' => $pedido->cliente,

                        'importe' => (float) $pedido->importe_total,

                        'fecha_entrega' =>
                        !empty($pedido->fecha_entrega)
                            ? Carbon::parse(
                                $pedido->fecha_entrega
                            )->format('d/m/Y')
                            : null,
                    ];
                })
                ->values()
                ->all();


            /*
            |--------------------------------------------------------------------------
            | IMPORTES
            |--------------------------------------------------------------------------
            */

            $importeEntregado = collect(
                $pedidosEntregadosSemana
            )->sum(
                function ($pedido) {

                    return (float) $pedido['importe'];
                }
            );


            $importePendiente = collect(
                $pedidosPendientesSemana
            )->sum(
                function ($pedido) {

                    return (float) $pedido['importe'];
                }
            );


            /*
            |--------------------------------------------------------------------------
            | GUARDAR SEMANA
            |--------------------------------------------------------------------------
            */

            $proyeccionSemanas->push([

                'semana' =>
                $semanaFutura->semana,

                'inicio' =>
                $inicio,

                'fin' =>
                $fin,

                'importe' =>
                $importeSemana,

                'importe_entregado' =>
                $importeEntregado,

                'importe_pendiente' =>
                $importePendiente,

                'cantidad' =>
                $pedidosSemana->count(),

                'pedidos_entregados' =>
                $pedidosEntregadosSemana,

                'pedidos_pendientes' =>
                $pedidosPendientesSemana,
            ]);
        }








        /*
|--------------------------------------------------------------------------
| 26. PEDIDOS QUE REQUIEREN ATENCIÓN
|--------------------------------------------------------------------------
|
| Se muestran TODOS los pedidos:
|
|   - ATRASADOS
|   - EN RIESGO
|
| No se excluyen pedidos internos.
|
*/

        $pedidosAtencion = $pedidosAtrasados
            ->merge($pedidosEnRiesgo)
            ->unique('id')
            ->sortBy(function ($pedido) use ($pedidosAtrasados) {

                /*
        | Atrasados primero
        | En riesgo después
        */

                $esAtrasado = $pedidosAtrasados->contains(
                    function ($pedidoAtrasado) use ($pedido) {
                        return $pedidoAtrasado->id == $pedido->id;
                    }
                );

                return $esAtrasado ? 0 : 1;
            })
            ->values();


        /*
        |--------------------------------------------------------------------------
        | 27. ENVIAR INFORMACIÓN A LA VISTA
        |--------------------------------------------------------------------------
        */

        return view('dashboard-direccion.index', [

            /*
            |--------------------------------------------------------------------------
            | Semana actual
            |--------------------------------------------------------------------------
            */

            'semana' => $semana,

            'inicioSemana' => $inicioSemana,

            'finSemana' => $finSemana,






            /*
            |--------------------------------------------------------------------------
            | Pedidos
            |--------------------------------------------------------------------------
            */

            'pedidos' => $pedidos,

            'pedidosActivos' => $pedidosActivos,


            'metaSemanal' => $metaSemanal,
            'programadoSemanal' => $programadoSemanal,
            'faltanteSemanal' => $faltanteSemanal,
            'porcentajeSemanal' => $porcentajeSemanal,

            'metaMensual' => $metaMensual,
            'programadoMensual' => $programadoMensual,
            'faltanteMensual' => $faltanteMensual,
            'porcentajeMensual' => $porcentajeMensual,


            /*
            |--------------------------------------------------------------------------
            | Valores generales
            |--------------------------------------------------------------------------
            */

            'valorPedidos' => $valorPedidos,

            'valorEnProduccion' => $valorEnProduccion,

            /*
            |--------------------------------------------------------------------------
            | Semáforo de producción
            |--------------------------------------------------------------------------
            */

            'cantidadEnTiempo' => $cantidadEnTiempo,
            'cantidadEnRiesgo' => $cantidadEnRiesgo,
            'cantidadAtrasados' => $cantidadAtrasados,
            'cantidadSinFecha' => $cantidadSinFecha,

            'valorEnTiempo' => $valorEnTiempo,
            'valorEnRiesgo' => $valorEnRiesgo,
            'valorSinFecha' => $valorSinFecha,

            'pedidosEnTiempo' => $pedidosEnTiempo,
            'pedidosEnRiesgo' => $pedidosEnRiesgo,
            'pedidosAtrasados' => $pedidosAtrasados,
            'pedidosSinFecha' => $pedidosSinFecha,

            'valorPorIniciar' => $valorPorIniciar,

            'valorPorEntregar' => $valorPorEntregar,

            'valorAtrasado' => $valorAtrasado,

            'pedidosPorIniciar' => $pedidosPorIniciar,

            'pedidosPorEntregar' => $pedidosPorEntregar,

            'valorEntregadoSemana' => $valorEntregadoSemana,

            /*
            |--------------------------------------------------------------------------
            | Comprobaciones
            |--------------------------------------------------------------------------
            */

            'valorSubdivididoProduccion' => $valorSubdivididoProduccion,

            'diferenciaProduccion' => $diferenciaProduccion,

            'diferenciaTotalPedidos' => $diferenciaTotalPedidos,


            /*
            |--------------------------------------------------------------------------
            | Dona
            |--------------------------------------------------------------------------
            */

            'valorTotalDona' => $valorTotalDona,

            'valorDistribuido' => $valorDistribuido,

            'situacionImportes' => $situacionImportes,

            'situacionPedidos' => $situacionPedidos,


            /*
            |--------------------------------------------------------------------------
            | Próximas semanas
            |--------------------------------------------------------------------------
            */

            'proyeccionSemanas' => $proyeccionSemanas,

            'offsetProyeccion' =>
            $offsetProyeccion,

            'puedeRetrocederProyeccion' =>
            $puedeRetrocederProyeccion,

            'puedeAvanzarProyeccion' =>
            $puedeAvanzarProyeccion,


            /*
            |--------------------------------------------------------------------------
            | Atención
            |--------------------------------------------------------------------------
            */

            'pedidosAtencion' => $pedidosAtencion,

        ]);
    }
}
