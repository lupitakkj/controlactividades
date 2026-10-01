<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use App\Models\SemanaOperativa;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardEjecutivoController extends Controller
{
    public function index(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | 1. FECHA ACTUAL
        |--------------------------------------------------------------------------
        */

        $hoy = Carbon::today();


        /*
        |--------------------------------------------------------------------------
        | 2. MODO DEL DASHBOARD
        |--------------------------------------------------------------------------
        |
        | AUTOMATICO:
        |   - Determina la semana según fecha y hora.
        |   - El cambio de semana ocurre cada martes a las 18:30.
        |
        | MANUAL:
        |   - Permite seleccionar cualquier semana de SemanaOperativa.
        |
        */

        $modo = strtolower(
            $request->input('modo', 'automatico')
        );

        if (!in_array($modo, ['automatico', 'manual'])) {
            $modo = 'automatico';
        }


        /*
        |--------------------------------------------------------------------------
        | 3. OBTENER SEMANA OPERATIVA
        |--------------------------------------------------------------------------
        */

        if ($modo === 'manual' && $request->filled('semana')) {

            /*
            |--------------------------------------------------------------------------
            | MODO MANUAL
            |--------------------------------------------------------------------------
            */

            $semanaOperativa = SemanaOperativa::where(
                'semana',
                (int) $request->input('semana')
            )
                ->where(
                    'anio',
                    $hoy->year
                )
                ->first();
        } else {

            /*
            |--------------------------------------------------------------------------
            | MODO AUTOMÁTICO
            |--------------------------------------------------------------------------
            |
            | La semana se activa cada martes a las 18:30.
            |
            */

            $ahora = Carbon::now();

            // Buscar la última semana cuyo inicio ya llegó
            $semanaCandidata = SemanaOperativa::whereDate(
                'fecha_inicio',
                '<=',
                $ahora->toDateString()
            )
                ->orderByDesc('fecha_inicio')
                ->first();

            if ($semanaCandidata) {

                // Momento exacto de activación
                $momentoActivacion = Carbon::parse(
                    $semanaCandidata->fecha_inicio
                )->setTime(18, 30, 0);

                /*
                |--------------------------------------------------------------------------
                | Si todavía no son las 18:30 del día de inicio,
                | seguimos utilizando la semana anterior.
                |--------------------------------------------------------------------------
                */

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
        }


        /*
        |--------------------------------------------------------------------------
        | 4. RESPALDO
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
        | 5. VALIDAR SEMANA
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
        | 6. LISTA DE SEMANAS OPERATIVAS
        |--------------------------------------------------------------------------
        */

        $semanasOperativas = SemanaOperativa::orderBy('anio')
            ->orderBy('semana')
            ->get();


        /*
        |--------------------------------------------------------------------------
        | 7. PERIODO DE LA SEMANA
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
        | 8. FECHA DE CORTE
        |--------------------------------------------------------------------------
        */

        if (
            $hoy->gte($inicioSemana)
            && $hoy->lte($finSemana->copy()->startOfDay())
        ) {

            // Semana actual
            $fechaCorte = $hoy->copy()->startOfDay();
        } elseif (
            $hoy->lt($inicioSemana)
        ) {

            // Semana futura
            $fechaCorte = $inicioSemana->copy();
        } else {

            // Semana histórica
            $fechaCorte = $finSemana->copy()->startOfDay();
        }


        /*
        |--------------------------------------------------------------------------
        | 9. OBTENER PEDIDOS
        |--------------------------------------------------------------------------
        |
        | IMPORTANTE:
        |
        | Aquí NO se excluyen pedidos internos.
        |
        | La carga de trabajo debe mostrar TODOS los pedidos.
        |
        */

        $pedidos = Pedido::with([
            'controlOperativo',
            'partidas.despieceProcesos.proceso',
        ])
            ->get();


        /*
        |--------------------------------------------------------------------------
        | 10. SEPARAR INTERNOS Y EXTERNOS
        |--------------------------------------------------------------------------
        |
        | Esta separación será la base de todos los indicadores.
        |
        | pedido_interno = 1  -> INTERNO
        | pedido_interno = 0  -> EXTERNO
        |
        */

        $pedidosExternos = $pedidos
            ->filter(function ($pedido) {
                return (int) $pedido->pedido_interno === 0;
            })
            ->values();

        $pedidosInternos = $pedidos
            ->filter(function ($pedido) {
                return (int) $pedido->pedido_interno === 1;
            })
            ->values();


        /*
        |--------------------------------------------------------------------------
        | 11. CONSTRUIR CARGA DE TRABAJO
        |--------------------------------------------------------------------------
        |
        | La carga de trabajo SIEMPRE incluye:
        |
        |   - Pedidos externos
        |   - Pedidos internos
        |   - Pedidos atrasados
        |   - Terminados durante la semana
        |
        */

        /*
        |--------------------------------------------------------------------------
        | REGLA ÚNICA PARA CONSTRUIR LA CARGA CRÍTICA
        |--------------------------------------------------------------------------
        |
        | Esta misma regla se utiliza tanto para la semana seleccionada como para
        | cada semana histórica de la tendencia.
        |
        | La carga general incluye externos + internos.
        | Después se separa en externos e internos.
        | Los KPI ejecutivos utilizan SOLO la versión externa.
        |
        */

        $calcularPedidosCriticos = function (
            $inicio,
            $fin,
            $corte
        ) use ($pedidos) {

            return $pedidos
                ->filter(function ($pedido) use (
                    $inicio,
                    $fin,
                    $corte
                ) {

                    /*
                    |--------------------------------------------------------------------------
                    | FECHA DE ENTREGA
                    |--------------------------------------------------------------------------
                    */

                    $fechaEntrega = null;

                    if ($pedido->fecha_entrega) {

                        $fechaEntrega = Carbon::parse(
                            $pedido->fecha_entrega
                        )->startOfDay();
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | FECHA TERMINADO
                    |--------------------------------------------------------------------------
                    */

                    $fechaTerminado = null;

                    if ($pedido->fecha_terminado) {

                        $fechaTerminado = Carbon::parse(
                            $pedido->fecha_terminado
                        )->startOfDay();
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | ESTADO REAL
                    |--------------------------------------------------------------------------
                    */

                    $terminado =
                        $pedido->avance !== null
                        && $pedido->avance >= 1;


                    /*
                    |--------------------------------------------------------------------------
                    | 1. TERMINADO DURANTE LA SEMANA
                    |--------------------------------------------------------------------------
                    */

                    if (
                        $terminado
                        && $fechaTerminado
                        && $fechaTerminado->gte($inicio)
                        && $fechaTerminado->lte(
                            $fin->copy()->startOfDay()
                        )
                    ) {

                        return true;
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | 2. PEDIDOS NO TERMINADOS
                    |--------------------------------------------------------------------------
                    */

                    if (!$terminado) {

                        /*
                        |--------------------------------------------------------------------------
                        | Pedido programado de la semana
                        |--------------------------------------------------------------------------
                        */

                        if (
                            $fechaEntrega
                            && $fechaEntrega->gte($inicio)
                            && $fechaEntrega->lte(
                                $fin->copy()->startOfDay()
                            )
                        ) {

                            return true;
                        }


                        /*
                        |--------------------------------------------------------------------------
                        | Pedido atrasado
                        |--------------------------------------------------------------------------
                        */

                        if (
                            $fechaEntrega
                            && $fechaEntrega->lt($corte)
                        ) {

                            return true;
                        }
                    }


                    return false;
                })
                ->values();
        };


        /*
        |--------------------------------------------------------------------------
        | CARGA CRÍTICA DE LA SEMANA SELECCIONADA
        |--------------------------------------------------------------------------
        */

        $pedidosCriticos = $calcularPedidosCriticos(
            $inicioSemana,
            $finSemana,
            $fechaCorte
        );


        /*
        |--------------------------------------------------------------------------
        | 12. SEPARAR LA CARGA CRÍTICA
        |--------------------------------------------------------------------------
        |
        | La tabla de carga utiliza $pedidosCriticos completo.
        |
        | Los KPI ejecutivos utilizan SOLO externos.
        |
        | Los KPI internos utilizan SOLO internos.
        |
        */

        $pedidosCriticosExternos = $pedidosCriticos
            ->filter(function ($pedido) {
                return (int) $pedido->pedido_interno === 0;
            })
            ->values();

        $pedidosCriticosInternos = $pedidosCriticos
            ->filter(function ($pedido) {
                return (int) $pedido->pedido_interno === 1;
            })
            ->values();


        /*
        |--------------------------------------------------------------------------
        | 13. CALCULAR SEMÁFORO DE CADA PEDIDO
        |--------------------------------------------------------------------------
        |
        | Se calcula para externos e internos.
        | Esto permite utilizarlo después en ambos grupos.
        |
        */

        foreach ($pedidosCriticos as $pedido) {

            /*
            |--------------------------------------------------------------------------
            | Pedido terminado
            |--------------------------------------------------------------------------
            */

            if (
                $pedido->avance !== null
                && $pedido->avance >= 1
            ) {

                $pedido->semaforo_dashboard = 'TERMINADO';

                $pedido->dias_restantes_dashboard = 0;

                $pedido->dias_atraso_dashboard = 0;

                continue;
            }


            /*
            |--------------------------------------------------------------------------
            | Fecha producción
            |--------------------------------------------------------------------------
            */

            $fechaEntrega = $pedido->fecha_entrega;

            if (!$fechaEntrega) {

                $pedido->semaforo_dashboard = 'SIN FECHA';

                $pedido->dias_restantes_dashboard = 0;

                $pedido->dias_atraso_dashboard = 0;

                continue;
            }

            $fechaEntrega = Carbon::parse(
                $fechaEntrega
            )->startOfDay();

            $diasRestantes = $fechaCorte->diffInDays(
                $fechaEntrega,
                false
            );

            $pedido->dias_restantes_dashboard =
                $diasRestantes;


            /*
            |--------------------------------------------------------------------------
            | DÍAS DE ATRASO
            |--------------------------------------------------------------------------
            */

            if ($diasRestantes < 0) {

                $pedido->dias_atraso_dashboard =
                    abs($diasRestantes);
            } else {

                $pedido->dias_atraso_dashboard = 0;
            }


            /*
            |--------------------------------------------------------------------------
            | SEMÁFORO
            |--------------------------------------------------------------------------
            */

            if ($diasRestantes < 0) {

                $pedido->semaforo_dashboard = 'ATRASADO';
            } elseif ($diasRestantes <= 7) {

                $pedido->semaforo_dashboard = 'EN RIESGO';
            } else {

                $pedido->semaforo_dashboard = 'EN TIEMPO';
            }
        }


        /*
        |--------------------------------------------------------------------------
        | 14. AVANCE SEMANAL
        |--------------------------------------------------------------------------
        |
        | El avance ejecutivo NO considera pedidos internos.
        |
        */

        $pedidosAvance = $pedidosCriticosExternos
            ->filter(function ($pedido) {
                return $pedido->avance !== null;
            })
            ->values();

        $sumaAvancePonderado = 0;
        $sumaPiezas = 0;

        foreach ($pedidosAvance as $pedido) {

            $piezasPedido = $pedido->partidas->sum(function ($partida) {
                return (float) $partida->cantidad;
            });

            if ($piezasPedido <= 0) {
                continue;
            }

            $sumaAvancePonderado +=
                ((float) $pedido->avance * $piezasPedido);

            $sumaPiezas += $piezasPedido;
        }

        $avanceSemanal = $sumaPiezas > 0
            ? $sumaAvancePonderado / $sumaPiezas
            : 0;

        $avanceSemanal = max(
            0,
            min(1, $avanceSemanal)
        );


        /*
        |--------------------------------------------------------------------------
        | 15. KPI EJECUTIVO
        |--------------------------------------------------------------------------
        |
        | IMPORTANTE:
        |
        | TODO este bloque trabaja SOLO con pedidos externos.
        |
        */

        $cargaTotal = $pedidosCriticosExternos->count();


        /*
        |--------------------------------------------------------------------------
        | TERMINADOS
        |--------------------------------------------------------------------------
        |
        | Son los pedidos externos terminados durante la semana
        | seleccionada.
        |
        */

        $terminados = $pedidosCriticosExternos
            ->filter(function ($pedido) {

                return $pedido->avance !== null
                    && $pedido->avance >= 1;
            })
            ->count();


        /*
        |--------------------------------------------------------------------------
        | EN PRODUCCIÓN
        |--------------------------------------------------------------------------
        */

        $enProduccion =
            $cargaTotal - $terminados;


        /*
        |--------------------------------------------------------------------------
        | EN RIESGO
        |--------------------------------------------------------------------------
        */

        $enRiesgo = $pedidosCriticosExternos
            ->filter(function ($pedido) {
                return $pedido->semaforo_dashboard === 'EN RIESGO';
            })
            ->count();


        /*
        |--------------------------------------------------------------------------
        | ATRASADOS
        |--------------------------------------------------------------------------
        */

        $atrasados = $pedidosCriticosExternos
            ->filter(function ($pedido) {
                return $pedido->semaforo_dashboard === 'ATRASADO';
            })
            ->count();


        /*
        |--------------------------------------------------------------------------
        | 16. OTD - PEDIDOS PROGRAMADOS DE LA SEMANA
        |--------------------------------------------------------------------------
        |
        | SOLO pedidos externos.
        |
        */

        $pedidosOTD = $pedidosCriticosExternos
            ->filter(function ($pedido) use (
                $inicioSemana,
                $finSemana
            ) {

                if (!$pedido->controlOperativo) {
                    return false;
                }

                if (!$pedido->fecha_entrega) {
                    return false;
                }

                $fechaEntrega = Carbon::parse(
                    $pedido->fecha_entrega
                )->startOfDay();

                return $fechaEntrega->betweenIncluded(
                    $inicioSemana->copy()->startOfDay(),
                    $finSemana->copy()->endOfDay()
                );
            })
            ->values();


        /*
        |--------------------------------------------------------------------------
        | TOTALES OTD
        |--------------------------------------------------------------------------
        */

        $otdProgramados = $pedidosOTD->count();

        $otdEntregados = 0;

        $otdATiempo = 0;

        $otdFueraTiempo = 0;

        $otdPendientes = 0;

        $otdPedidosPendientes = collect();


        /*
        |--------------------------------------------------------------------------
        | CALCULAR OTD
        |--------------------------------------------------------------------------
        */

        foreach ($pedidosOTD as $pedido) {

            /*
            |--------------------------------------------------------------------------
            | Pendiente
            |--------------------------------------------------------------------------
            */

            if (!$pedido->fecha_terminado) {

                $otdPendientes++;

                $otdPedidosPendientes->push($pedido);

                continue;
            }


            /*
            |--------------------------------------------------------------------------
            | Entregado
            |--------------------------------------------------------------------------
            */

            $otdEntregados++;


            $fechaTerminado = Carbon::parse(
                $pedido->fecha_terminado
            )->startOfDay();

            $fechaCompromiso = Carbon::parse(
                $pedido->fecha_entrega
            )->startOfDay();


            /*
            |--------------------------------------------------------------------------
            | A tiempo / fuera de tiempo
            |--------------------------------------------------------------------------
            */

            if ($fechaTerminado->lte($fechaCompromiso)) {

                $otdATiempo++;
            } else {

                $otdFueraTiempo++;
            }
        }


        /*
        |--------------------------------------------------------------------------
        | PORCENTAJE OTD
        |--------------------------------------------------------------------------
        */

        $otdPorcentaje = $otdProgramados > 0
            ? $otdATiempo / $otdProgramados
            : 0;


        /*
        |--------------------------------------------------------------------------
        | 17. RECUPERADOS
        |--------------------------------------------------------------------------
        |
        | Un recuperado:
        |
        |   1. Es externo.
        |   2. Terminó durante la semana.
        |   3. Tenía fecha de producción anterior al inicio
        |      de la semana.
        |
        */

        $recuperados = $pedidosCriticosExternos
            ->filter(function ($pedido) use (
                $inicioSemana,
                $finSemana
            ) {

                if (!$pedido->fecha_terminado) {
                    return false;
                }


                $fechaTerminado = Carbon::parse(
                    $pedido->fecha_terminado
                )->startOfDay();

                $fechaEntrega = Carbon::parse(
                    $pedido->fecha_entrega
                )->startOfDay();


                /*
                |--------------------------------------------------------------------------
                | Terminó durante la semana
                |--------------------------------------------------------------------------
                */

                $terminadoEnSemana =
                    $fechaTerminado->betweenIncluded(
                        $inicioSemana->copy()->startOfDay(),
                        $finSemana->copy()->endOfDay()
                    );


                /*
                |--------------------------------------------------------------------------
                | Ya estaba atrasado al iniciar la semana
                |--------------------------------------------------------------------------
                */

                $eraAtrasado =
                    $fechaEntrega->lt(
                        $inicioSemana->copy()->startOfDay()
                    );


                return $terminadoEnSemana
                    && $eraAtrasado;
            })
            ->count();


        /*
        |--------------------------------------------------------------------------
        | 18. PRODUCCIÓN EJECUTIVA
        |--------------------------------------------------------------------------
        |
        | SOLO EXTERNOS.
        |
        */

        $pedidosProduccion = $pedidosCriticosExternos
            ->values();


        /*
        |--------------------------------------------------------------------------
        | CARGA TOTAL
        |--------------------------------------------------------------------------
        */

        $produccionCargaTotal =
            $pedidosProduccion->count();


        /*
        |--------------------------------------------------------------------------
        | TERMINADOS
        |--------------------------------------------------------------------------
        */

        $produccionTerminados =
            $pedidosProduccion
            ->filter(function ($pedido) {

                return $pedido->avance !== null
                    && $pedido->avance >= 1;
            })
            ->values();


        /*
        |--------------------------------------------------------------------------
        | TERMINADOS DURANTE LA SEMANA
        |--------------------------------------------------------------------------
        */

        $terminadosDuranteSemana =
            $produccionTerminados
            ->filter(function ($pedido) use (
                $inicioSemana,
                $finSemana
            ) {

                if (!$pedido->fecha_terminado) {
                    return false;
                }

                $fechaTerminado = Carbon::parse(
                    $pedido->fecha_terminado
                )->startOfDay();

                return $fechaTerminado->betweenIncluded(
                    $inicioSemana->copy()->startOfDay(),
                    $finSemana->copy()->endOfDay()
                );
            })
            ->values();


        /*
        |--------------------------------------------------------------------------
        | RECUPERADOS DE PRODUCCIÓN
        |--------------------------------------------------------------------------
        */

        $produccionRecuperados =
            $terminadosDuranteSemana
            ->filter(function ($pedido) use ($inicioSemana) {

                if (!$pedido->fecha_entrega) {
                    return false;
                }

                $fechaEntrega = Carbon::parse(
                    $pedido->fecha_entrega
                )->startOfDay();

                return $fechaEntrega->lt(
                    $inicioSemana->copy()->startOfDay()
                );
            })
            ->count();


        /*
        |--------------------------------------------------------------------------
        | TERMINADOS NORMALES DE LA SEMANA
        |--------------------------------------------------------------------------
        */

        $produccionTerminadosSemana =
            $terminadosDuranteSemana->count()
            - $produccionRecuperados;


        /*
        |--------------------------------------------------------------------------
        | EN PRODUCCIÓN
        |--------------------------------------------------------------------------
        */

        $produccionEnProduccion =
            $produccionCargaTotal
            - $produccionTerminados->count();


        /*
        |--------------------------------------------------------------------------
        | PORCENTAJE DE AVANCE REAL
        |--------------------------------------------------------------------------
        |
        | Se utiliza el avance real de cada pedido.
        |
        | $pedido->avance ya considera:
        | - piezas de cada partida
        | - procesos aplicables
        | - procesos N/A
        | - porcentaje realizado
        |
        */

        $produccionPorcentaje = 0;

        $sumaAvancePonderado = 0;
        $sumaPiezas = 0;

        foreach ($pedidosProduccion as $pedido) {

            if ($pedido->avance === null) {
                continue;
            }

            $piezasPedido = $pedido->partidas->sum(function ($partida) {
                return (float) $partida->cantidad;
            });

            if ($piezasPedido <= 0) {
                continue;
            }

            $sumaAvancePonderado +=
                ((float) $pedido->avance * $piezasPedido);

            $sumaPiezas += $piezasPedido;
        }

        if ($sumaPiezas > 0) {

            $produccionPorcentaje =
                $sumaAvancePonderado / $sumaPiezas;
        }

        $produccionPorcentaje = max(
            0,
            min(1, $produccionPorcentaje)
        );


        /*
        |--------------------------------------------------------------------------
        | 19. ESTADÍSTICAS DE PEDIDOS INTERNOS
        |--------------------------------------------------------------------------
        |
        | Los internos NO contaminan los KPI ejecutivos.
        |
        | Aquí se calculan por separado.
        |
        */

        $internosCargaTotal =
            $pedidosCriticosInternos->count();


        /*
        |--------------------------------------------------------------------------
        | INTERNOS TERMINADOS
        |--------------------------------------------------------------------------
        */

        $internosTerminados =
            $pedidosCriticosInternos
            ->filter(function ($pedido) {

                return $pedido->avance !== null
                    && $pedido->avance >= 1;
            })
            ->values();


        /*
        |--------------------------------------------------------------------------
        | INTERNOS EN PRODUCCIÓN
        |--------------------------------------------------------------------------
        */

        $internosEnProduccion =
            $internosCargaTotal
            - $internosTerminados->count();


        /*
        |--------------------------------------------------------------------------
        | INTERNOS TERMINADOS DURANTE LA SEMANA
        |--------------------------------------------------------------------------
        */

        $internosTerminadosSemana =
            $internosTerminados
            ->filter(function ($pedido) use (
                $inicioSemana,
                $finSemana
            ) {

                if (!$pedido->fecha_terminado) {
                    return false;
                }

                $fechaTerminado = Carbon::parse(
                    $pedido->fecha_terminado
                )->startOfDay();

                return $fechaTerminado->betweenIncluded(
                    $inicioSemana->copy()->startOfDay(),
                    $finSemana->copy()->endOfDay()
                );
            })
            ->count();


        /*
        |--------------------------------------------------------------------------
        | INTERNOS EN RIESGO
        |--------------------------------------------------------------------------
        */

        $internosEnRiesgo =
            $pedidosCriticosInternos
            ->filter(function ($pedido) {
                return $pedido->semaforo_dashboard === 'EN RIESGO';
            })
            ->count();


        /*
        |--------------------------------------------------------------------------
        | INTERNOS ATRASADOS
        |--------------------------------------------------------------------------
        */

        $internosAtrasados =
            $pedidosCriticosInternos
            ->filter(function ($pedido) {
                return $pedido->semaforo_dashboard === 'ATRASADO';
            })
            ->count();


        /*
        |--------------------------------------------------------------------------
        | INTERNOS RECUPERADOS
        |--------------------------------------------------------------------------
        */

        $internosRecuperados =
            $pedidosCriticosInternos
            ->filter(function ($pedido) use (
                $inicioSemana,
                $finSemana
            ) {

                if (!$pedido->fecha_terminado) {
                    return false;
                }

                if (!$pedido->controlOperativo) {
                    return false;
                }

                if (!$pedido->controlOperativo->fecha_produccion) {
                    return false;
                }


                $fechaTerminado = Carbon::parse(
                    $pedido->fecha_terminado
                )->startOfDay();

                $fechaProduccion = Carbon::parse(
                    $pedido->controlOperativo->fecha_produccion
                )->startOfDay();


                $terminadoEnSemana =
                    $fechaTerminado->betweenIncluded(
                        $inicioSemana->copy()->startOfDay(),
                        $finSemana->copy()->endOfDay()
                    );


                $eraAtrasado =
                    $fechaProduccion->lt(
                        $inicioSemana->copy()->startOfDay()
                    );


                return $terminadoEnSemana
                    && $eraAtrasado;
            })
            ->count();


        /*
        |--------------------------------------------------------------------------
        | 20. TENDENCIA OTD
        |--------------------------------------------------------------------------
        |
        | IMPORTANTE:
        |
        | Ya NO utilizamos:
        |
        |     subDays($i * 7)
        |
        | Se utilizan las semanas REALES de SemanaOperativa.
        |
        | La tendencia siempre utiliza:
        |
        |     4 semanas anteriores
        |     +
        |     semana seleccionada
        |
        | Y SOLO PEDIDOS EXTERNOS.
        |
        */

        $tendenciaOtd = collect();


        /*
        |--------------------------------------------------------------------------
        | OBTENER LAS 5 SEMANAS REALES
        |--------------------------------------------------------------------------
        */

        $semanasTendencia = SemanaOperativa::query()
            ->whereDate(
                'fecha_inicio',
                '<=',
                $inicioSemana->toDateString()
            )
            ->orderByDesc('fecha_inicio')
            ->take(5)
            ->get()
            ->sortBy('fecha_inicio')
            ->values();


        /*
        |--------------------------------------------------------------------------
        | CALCULAR CADA SEMANA
        |--------------------------------------------------------------------------
        */

        foreach ($semanasTendencia as $semanaTendencia) {

            /*
    |--------------------------------------------------------------------------
    | FECHAS REALES DE LA SEMANA
    |--------------------------------------------------------------------------
    */

            $inicioTendencia = Carbon::parse(
                $semanaTendencia->fecha_inicio
            )->startOfDay();

            $finTendencia = Carbon::parse(
                $semanaTendencia->fecha_fin
            )->endOfDay();


            /*
            |--------------------------------------------------------------------------
            | FECHA DE CORTE DE ESTA SEMANA
            |--------------------------------------------------------------------------
            |
            | Se utiliza la misma regla de fecha de corte que en la semana
            | seleccionada. Esto permite reconstruir la carga crítica propia
            | de cada semana histórica.
            |
            */

            if (
                $hoy->gte($inicioTendencia)
                && $hoy->lte($finTendencia->copy()->startOfDay())
            ) {

                $fechaCorteTendencia = $hoy->copy()->startOfDay();
            } elseif ($hoy->lt($inicioTendencia)) {

                $fechaCorteTendencia = $inicioTendencia->copy();
            } else {

                $fechaCorteTendencia =
                    $finTendencia->copy()->startOfDay();
            }


            /*
            |--------------------------------------------------------------------------
            | CARGA CRÍTICA DE ESTA SEMANA
            |--------------------------------------------------------------------------
            |
            | Cada punto de la tendencia reconstruye la misma carga crítica
            | que tendría el dashboard si esa semana estuviera seleccionada.
            |
            | Después se separan SOLO los pedidos externos.
            |
            */

            $pedidosCriticosTendencia =
                $calcularPedidosCriticos(
                    $inicioTendencia,
                    $finTendencia,
                    $fechaCorteTendencia
                );

            $pedidosCriticosExternosTendencia =
                $pedidosCriticosTendencia
                ->filter(function ($pedido) {
                    return (int) $pedido->pedido_interno === 0;
                })
                ->values();


            /*
            |--------------------------------------------------------------------------
            | PEDIDOS PROGRAMADOS DE ESTA SEMANA
            |--------------------------------------------------------------------------
            |
            | La tendencia ejecutiva utiliza exclusivamente los pedidos
            | críticos externos de ESTA semana.
            |
            */

            $programados = $pedidosCriticosExternosTendencia
                ->filter(function ($pedido) use (
                    $inicioTendencia,
                    $finTendencia
                ) {

                    if (!$pedido->controlOperativo) {
                        return false;
                    }

                    if (!$pedido->fecha_entrega) {
                        return false;
                    }

                    $fechaEntrega = Carbon::parse(
                        $pedido->fecha_entrega
                    )->startOfDay();

                    return $fechaEntrega->betweenIncluded(
                        $inicioTendencia->copy()->startOfDay(),
                        $finTendencia->copy()->endOfDay()
                    );
                })
                ->values();


            /*
    |--------------------------------------------------------------------------
    | TOTALES
    |--------------------------------------------------------------------------
    */

            $totalProgramados =
                $programados->count();

            $entregados = 0;

            $entregadosATiempo = 0;

            $entregadosTarde = 0;


            /*
    |--------------------------------------------------------------------------
    | CALCULAR ENTREGADOS
    |--------------------------------------------------------------------------
    */

            foreach ($programados as $pedido) {

                /*
        |--------------------------------------------------------------------------
        | PENDIENTE
        |--------------------------------------------------------------------------
        */

                if (!$pedido->fecha_terminado) {
                    continue;
                }


                /*
        |--------------------------------------------------------------------------
        | ENTREGADO
        |--------------------------------------------------------------------------
        */

                $entregados++;


                /*
        |--------------------------------------------------------------------------
        | FECHA DE TERMINADO
        |--------------------------------------------------------------------------
        */

                $fechaTerminado = Carbon::parse(
                    $pedido->fecha_terminado
                )->startOfDay();


                /*
        |--------------------------------------------------------------------------
        | FECHA COMPROMISO
        |--------------------------------------------------------------------------
        */

                $fechaCompromiso = Carbon::parse(
                    $pedido->fecha_entrega
                )->startOfDay();


                /*
        |--------------------------------------------------------------------------
        | A TIEMPO / FUERA DE TIEMPO
        |--------------------------------------------------------------------------
        */

                if ($fechaTerminado->lte($fechaCompromiso)) {

                    $entregadosATiempo++;
                } else {

                    $entregadosTarde++;
                }
            }


            /*
    |--------------------------------------------------------------------------
    | PENDIENTES
    |--------------------------------------------------------------------------
    */

            $pendientes =
                $totalProgramados
                - $entregados;


            /*
    |--------------------------------------------------------------------------
    | PORCENTAJES
    |--------------------------------------------------------------------------
    */

            if ($totalProgramados > 0) {

                $porcentajeOtd =
                    $entregadosATiempo
                    / $totalProgramados;


                $porcentajeFueraTiempo =
                    $entregadosTarde
                    / $totalProgramados;


                $porcentajePendientes =
                    $pendientes
                    / $totalProgramados;
            } else {

                $porcentajeOtd = 0;

                $porcentajeFueraTiempo = 0;

                $porcentajePendientes = 0;
            }


            /*
    |--------------------------------------------------------------------------
    | GUARDAR SEMANA
    |--------------------------------------------------------------------------
    */

            $tendenciaOtd->push([

                'semana' =>
                $semanaTendencia->semana,

                'periodo' =>
                $inicioTendencia->format('d M')
                    . ' - '
                    . $finTendencia->format('d M'),

                'otd' =>
                $porcentajeOtd,

                'fuera_tiempo' =>
                $porcentajeFueraTiempo,

                'pendientes' =>
                $porcentajePendientes,

                'programados' =>
                $totalProgramados,

                'entregados' =>
                $entregados,

                'a_tiempo' =>
                $entregadosATiempo,

                'pendientes_cantidad' =>
                $pendientes,
            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | 21. EN TIEMPO DEL RESUMEN
        |--------------------------------------------------------------------------
        |
        | Este valor corresponde a los pedidos externos programados
        | para la semana y terminados a tiempo.
        |
        */

        $enTiempo = $otdATiempo;


        /*
        |--------------------------------------------------------------------------
        | 22. DEVOLVER DASHBOARD
        |--------------------------------------------------------------------------
        */

        return view('dashboard-produccion', [

            /*
            |--------------------------------------------------------------------------
            | SEMANA
            |--------------------------------------------------------------------------
            */

            'modo' =>
            $modo,

            'semanasOperativas' =>
            $semanasOperativas,

            'semana' =>
            $semana,

            'inicioSemana' =>
            $inicioSemana,

            'finSemana' =>
            $finSemana,

            'fechaCorte' =>
            $fechaCorte,

            'semanaOperativa' =>
            $semanaOperativa,


            /*
            |--------------------------------------------------------------------------
            | CARGA DE TRABAJO
            |--------------------------------------------------------------------------
            |
            | IMPORTANTE:
            | Aquí siguen entrando TODOS:
            | externos + internos.
            |
            */

            'pedidosCriticos' =>
            $pedidosCriticos,


            /*
            |--------------------------------------------------------------------------
            | KPI EJECUTIVO - SOLO EXTERNOS
            |--------------------------------------------------------------------------
            */

            'cargaTotal' =>
            $cargaTotal,

            'enProduccion' =>
            $enProduccion,

            'terminados' =>
            $terminados,

            'enTiempo' =>
            $enTiempo,

            'enRiesgo' =>
            $enRiesgo,

            'atrasados' =>
            $atrasados,

            'recuperados' =>
            $recuperados,

            'avanceSemanal' =>
            $avanceSemanal,


            /*
            |--------------------------------------------------------------------------
            | OTD
            |--------------------------------------------------------------------------
            */

            'otdProgramados' =>
            $otdProgramados,

            'otdEntregados' =>
            $otdEntregados,

            'otdATiempo' =>
            $otdATiempo,

            'otdFueraTiempo' =>
            $otdFueraTiempo,

            'otdPendientes' =>
            $otdPendientes,

            'otdPorcentaje' =>
            $otdPorcentaje,

            'otdPedidosPendientes' =>
            $otdPedidosPendientes,


            /*
            |--------------------------------------------------------------------------
            | PRODUCCIÓN EJECUTIVA - EXTERNOS
            |--------------------------------------------------------------------------
            */

            'produccionCargaTotal' =>
            $produccionCargaTotal,

            'produccionTerminados' =>
            $produccionTerminados->count(),

            'produccionTerminadosSemana' =>
            $produccionTerminadosSemana,

            'produccionRecuperados' =>
            $produccionRecuperados,

            'produccionEnProduccion' =>
            $produccionEnProduccion,

            'produccionPorcentaje' =>
            $produccionPorcentaje,


            /*
            |--------------------------------------------------------------------------
            | ESTADÍSTICAS INTERNAS
            |--------------------------------------------------------------------------
            */

            'internosCargaTotal' =>
            $internosCargaTotal,

            'internosTerminados' =>
            $internosTerminados->count(),

            'internosEnProduccion' =>
            $internosEnProduccion,

            'internosTerminadosSemana' =>
            $internosTerminadosSemana,

            'internosEnRiesgo' =>
            $internosEnRiesgo,

            'internosAtrasados' =>
            $internosAtrasados,

            'internosRecuperados' =>
            $internosRecuperados,


            /*
            |--------------------------------------------------------------------------
            | TENDENCIA
            |--------------------------------------------------------------------------
            */

            'tendenciaOtd' =>
            $tendenciaOtd,

            'pedidosCriticosExternos' =>
            $pedidosCriticosExternos,
        ]);
    }
}
