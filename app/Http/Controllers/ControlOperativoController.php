<?php

namespace App\Http\Controllers;

use App\Models\ActividadControlOperativo;
use App\Models\Pedido;
use App\Models\Proceso;
use Illuminate\Http\Request;

class ControlOperativoController extends Controller
{
    /**
     * Mostrar Control Operativo.
     */
    public function index(Request $request)
    {
        $pedidos = Pedido::with([
            'controlOperativo',

            'actividadesControlOperativo' => function ($query) {
                $query->where('activa', true);
            },

            'partidas.despieceProcesos.proceso',
        ])
            ->orderByRaw('CAST(pedido_no AS UNSIGNED) ASC')
            ->get();


        /*
        |--------------------------------------------------------------------------
        | PROCESO CON MENOR AVANCE
        |--------------------------------------------------------------------------
        */

        foreach ($pedidos as $pedido) {

            $pedido->proceso_menor_avance =
                $this->obtenerProcesoMenorAvance($pedido);
        }
        /*
            |--------------------------------------------------------------------------
            | PEDIDOS ACTIVOS
            |--------------------------------------------------------------------------
            |
            | No contamos pedidos internos.
            | Tampoco contamos pedidos con avance de 100%.
            |
            */

        $pedidosActivos = $pedidos->filter(function ($pedido) {

            $estado = strtoupper(
                trim(
                    $pedido->controlOperativo?->estado_operativo ?? ''
                )
            );

            return $pedido->avance !== null
                && $pedido->avance < 1
                && $estado !== 'TERMINADO';
        });




        /*
        |--------------------------------------------------------------------------
        | AVANCE GLOBAL
        |--------------------------------------------------------------------------
        |
        | Avance global ponderado por cantidad de piezas.
        |
        | Cada pedido pesa de acuerdo con la cantidad total
        | de piezas que contiene.
        |
        | Fórmula:
        |
        |   SUMA(avance pedido × piezas pedido)
        |   ---------------------------------
        |          SUMA(piezas pedido)
        |
        | Los pedidos internos también participan.
        |
        */

        $sumaAvancePonderado = 0;
        $sumaPiezas = 0;

        foreach ($pedidosActivos as $pedido) {

            /*
            |--------------------------------------------------------------------------
            | CANTIDAD TOTAL DE PIEZAS DEL PEDIDO
            |--------------------------------------------------------------------------
            */

            $piezasPedido = $pedido->partidas->sum(function ($partida) {

                return (float) $partida->cantidad;
            });

            /*
            |--------------------------------------------------------------------------
            | IGNORAR PEDIDOS SIN PIEZAS VÁLIDAS
            |--------------------------------------------------------------------------
            */

            if ($piezasPedido <= 0) {
                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | ACUMULAR AVANCE PONDERADO
            |--------------------------------------------------------------------------
            */

            $sumaAvancePonderado +=
                ((float) $pedido->avance * $piezasPedido);

            $sumaPiezas += $piezasPedido;
        }

        /*
        |--------------------------------------------------------------------------
        | CALCULAR AVANCE GLOBAL
        |--------------------------------------------------------------------------
        */

        if ($sumaPiezas > 0) {

            $avanceGlobal =
                $sumaAvancePonderado / $sumaPiezas;
        } else {

            $avanceGlobal = 0;
        }


        /*
        |--------------------------------------------------------------------------
        | VISTA
        |--------------------------------------------------------------------------
        */

        return view(
            'control-operativo.index',
            compact(
                'pedidos',
                'pedidosActivos',
                'avanceGlobal'
            )
        );
    }


    /**
     * Actualizar Fecha de Producción.
     */
    public function actualizarFechaProduccion(
        Request $request,
        Pedido $pedido
    ) {
        $request->validate([
            'fecha_produccion' => [
                'nullable',
                'date'
            ],
        ]);

        $control = $pedido->controlOperativo;

        if (!$control) {
            $control = $pedido->controlOperativo()->create([
                'fecha_produccion' => $request->fecha_produccion,
                'estado_operativo' => 'Por Iniciar',
            ]);
        } else {
            $control->fecha_produccion =
                $request->fecha_produccion;

            $control->save();
        }

        return response()->json([
            'success' => true,
            'fecha_produccion' =>
            $control->fecha_produccion
                ? $control->fecha_produccion->format('Y-m-d')
                : null,
        ]);
    }

    /**
     * Crear actividad.
     */
    public function crearActividad(
        Request $request,
        Pedido $pedido
    ) {
        $request->validate([
            'actividad' => [
                'required',
                'string',
                'max:1000'
            ],

            'responsable' => [
                'nullable',
                'string',
                'max:255'
            ],

            'fecha_limite' => [
                'nullable',
                'date'
            ],
        ]);

        $actividad = $pedido->actividadesControlOperativo()->create([
            'actividad' => $request->input('actividad'),
            'responsable' => $request->input('responsable'),
            'completada' => false,
            'fecha_limite' => $request->input('fecha_limite'),
        ]);

        return response()->json([
            'success' => true,

            'actividad' => $actividad,

            'url_actualizar' => route(
                'control-operativo.actividad.actualizar',
                $actividad
            ),

            'url_eliminar' => route(
                'control-operativo.actividad.eliminar',
                $actividad
            ),
        ]);
    }


    /**
     * Actualizar actividad.
     */
    public function actualizarActividad(
        Request $request,
        ActividadControlOperativo $actividad
    ) {
        $request->validate([
            'actividad' => [
                'sometimes',
                'required',
                'string',
                'max:1000'
            ],

            'responsable' => [
                'sometimes',
                'nullable',
                'string',
                'max:255'
            ],

            'completada' => [
                'sometimes',
                'boolean'
            ],

            'fecha_limite' => [
                'sometimes',
                'nullable',
                'date'
            ],
        ]);

        $datos = [];

        if ($request->has('actividad')) {
            $datos['actividad'] =
                $request->input('actividad');
        }

        if ($request->has('responsable')) {
            $datos['responsable'] =
                $request->input('responsable');
        }

        if ($request->has('completada')) {
            $datos['completada'] =
                $request->boolean('completada');
        }

        if ($request->has('fecha_limite')) {
            $datos['fecha_limite'] =
                $request->input('fecha_limite');
        }

        $actividad->update($datos);

        return response()->json([
            'success' => true,
            'actividad' => $actividad->fresh(),
        ]);
    }


    /**
     * Eliminar actividad visualmente.
     * No se borra de la base de datos.
     */
    public function eliminarActividad(
        ActividadControlOperativo $actividad
    ) {
        $actividad->activa = false;
        $actividad->save();

        return response()->json([
            'success' => true,
        ]);
    }

    public function actualizarPrioridad(Request $request, Pedido $pedido)
    {
        $request->validate([
            'prioridad' => [
                'nullable',
                'in:1,2,3,4',
            ],
        ]);

        $control = $pedido->controlOperativo;

        if (!$control) {

            $control = $pedido->controlOperativo()->create([
                'prioridad' => $request->input('prioridad'),
                'estado_operativo' => 'Por Iniciar',
            ]);
        } else {

            $control->prioridad =
                $request->input('prioridad');

            $control->save();
        }

        return response()->json([
            'success' => true,
            'prioridad' => $control->prioridad,
        ]);
    }

    /**
     * Actualizar Pedido Interno.
     */
    public function actualizarPedidoInterno(
        Request $request,
        Pedido $pedido
    ) {
        $request->validate([
            'pedido_interno' => [
                'required',
                'boolean',
            ],
        ]);

        $pedido->pedido_interno =
            $request->boolean('pedido_interno');

        $pedido->save();

        return response()->json([
            'success' => true,
            'pedido_interno' => $pedido->pedido_interno,
        ]);
    }

    /**
     * Actualizar Estado Operativo.
     */
    public function actualizarEstado(
        Request $request,
        Pedido $pedido
    ) {
        $request->validate([
            'estado_operativo' => [
                'required',
                'in:Por Iniciar,En proceso,Cancelado,Terminado',
            ],
        ]);

        $nuevoEstado = $request->input('estado_operativo');

        /*
        |--------------------------------------------------------------------------
        | OBTENER / CREAR CONTROL OPERATIVO
        |--------------------------------------------------------------------------
        */

        $control = $pedido->controlOperativo;

        if (!$control) {

            $control = $pedido->controlOperativo()->create([
                'estado_operativo' => $nuevoEstado,
            ]);
        } else {

            $control->estado_operativo = $nuevoEstado;
            $control->save();
        }


        /*
        |--------------------------------------------------------------------------
        | SI EL PEDIDO SE MARCA COMO TERMINADO
        |--------------------------------------------------------------------------
        */

        if ($nuevoEstado === 'Terminado') {

            /*
            |--------------------------------------------------------------------------
            | GUARDAR FECHA DE TERMINADO
            |--------------------------------------------------------------------------
            */

            $pedido->fecha_terminado = now()->toDateString();
            $pedido->save();

            /*
        |--------------------------------------------------------------------------
        | PROCESOS DE CADA PARTIDA 
        |--------------------------------------------------------------------------
        |
        | Respetamos los procesos que estén como N/A.
        |
        | Los procesos que aplican se llevan al 100%.
        |
        */

            $pedido->load([
                'partidas.despieceProcesos.proceso'
            ]);

            foreach ($pedido->partidas as $partida) {

                foreach ($partida->despieceProcesos as $despiece) {

                    /*
                |--------------------------------------------------------------------------
                | N/A
                |--------------------------------------------------------------------------
                |
                | Si el proceso no aplica, NO lo modificamos.
                |
                */

                    if (!$despiece->aplica) {
                        continue;
                    }


                    /*
                |--------------------------------------------------------------------------
                | PROCESO APLICABLE
                |--------------------------------------------------------------------------
                |
                | Lo llevamos al 100%.
                |
                */

                    $despiece->cantidad_realizada =
                        $partida->cantidad;

                    $despiece->porcentaje = 1;

                    $despiece->save();
                }
            }
        }


        return response()->json([
            'success' => true,
            'estado_operativo' => $control->estado_operativo,
        ]);
    }

    /**
     * Obtener el proceso con menor avance de un pedido.
     */
    private function obtenerProcesoMenorAvance(Pedido $pedido): array
    {
        $procesos = Proceso::where('activo', true)
            ->orderBy('orden')
            ->get();

        $procesosParciales = [];
        $procesosCero = [];
        $procesosAplicables = 0;
        $procesosCompletos = 0;

        foreach ($procesos as $proceso) {

            $suma = 0;
            $cantidad = 0;

            foreach ($pedido->partidas as $partida) {

                $despiece = $partida->despieceProcesos
                    ->firstWhere('proceso_id', $proceso->id);

                // Si no existe el proceso para la partida,
                // se considera 0%
                if (!$despiece) {
                    $suma += 0;
                    $cantidad++;
                    continue;
                }

                // N/A no participa
                if (!$despiece->aplica) {
                    continue;
                }

                $porcentaje = (float) $despiece->porcentaje;

                // Limitar entre 0 y 100%
                $porcentaje = max(0, min(1, $porcentaje));

                $suma += $porcentaje;
                $cantidad++;
            }

            if ($cantidad === 0) {
                continue;
            }

            $porcentajeProceso = $suma / $cantidad;

            $procesosAplicables++;

            /*
        |------------------------------------------------------------
        | 1. PROCESOS ENTRE 1% Y 99%
        |------------------------------------------------------------
        */
            if ($porcentajeProceso > 0 && $porcentajeProceso < 1) {

                $procesosParciales[] = [
                    'proceso_id' => $proceso->id,
                    'proceso' => $proceso->nombre,
                    'porcentaje' => $porcentajeProceso,
                ];

                continue;
            }

            /*
        |------------------------------------------------------------
        | 2. PROCESOS EN 0%
        |------------------------------------------------------------
        */
            if ($porcentajeProceso == 0) {

                $procesosCero[] = [
                    'proceso_id' => $proceso->id,
                    'proceso' => $proceso->nombre,
                    'porcentaje' => 0,
                ];

                continue;
            }

            /*
        |------------------------------------------------------------
        | 3. PROCESOS AL 100%
        |------------------------------------------------------------
        */
            if ($porcentajeProceso >= 1) {
                $procesosCompletos++;
            }
        }

        /*
    |------------------------------------------------------------
    | PRIORIDAD 1:
    | Existe algún proceso entre 1% y 99%.
    | Mostrar el menor.
    |------------------------------------------------------------
    */
        if (!empty($procesosParciales)) {

            usort($procesosParciales, function ($a, $b) {
                return $a['porcentaje'] <=> $b['porcentaje'];
            });

            return $procesosParciales[0];
        }

        /*
    |------------------------------------------------------------
    | PRIORIDAD 2:
    | No hay procesos parciales, pero existen procesos en 0%.
    | Mostrar 0%.
    |------------------------------------------------------------
    */
        if (!empty($procesosCero)) {
            return $procesosCero[0];
        }

        /*
    |------------------------------------------------------------
    | PRIORIDAD 3:
    | Todos los procesos aplicables están al 100%.
    |------------------------------------------------------------
    */
        if ($procesosAplicables > 0 && $procesosCompletos === $procesosAplicables) {

            return [
                'proceso_id' => null,
                'proceso' => 'COMPLETADO',
                'porcentaje' => 1,
            ];
        }

        /*
    |------------------------------------------------------------
    | Sin procesos
    |------------------------------------------------------------
    */
        return [
            'proceso_id' => null,
            'proceso' => 'SIN PROCESOS',
            'porcentaje' => null,
        ];
    }
}
