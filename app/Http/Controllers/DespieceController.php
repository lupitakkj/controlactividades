<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use App\Models\Proceso;
use App\Models\Partida;
use App\Models\DespieceProceso;
use Illuminate\Http\Request;

class DespieceController extends Controller
{
    /**
     * =========================================================================
     * DESPIECE
     * =========================================================================
     *
     * Reglas:
     *
     * - Cada partida debe tener todos los procesos activos.
     * - Cada proceso comienza con:
     *      aplica = true
     *      cantidad_realizada = 0
     *      porcentaje = 0
     *
     * - Si un proceso no aplica:
     *      aplica = false
     *      cantidad_realizada = 0
     *      porcentaje = 0
     *
     * - N/A NO participa en el promedio.
     *
     * - Una cantidad de 0 SÍ participa en el promedio
     *   y representa 0% de avance.
     *
     * - El porcentaje se calcula:
     *
     *      cantidad_realizada / cantidad_de_partida
     *
     * - El avance de una partida es el promedio de los procesos
     *   aplicables.
     *
     * - El avance del pedido se calcula en el modelo Pedido.
     *
     * - Los pedidos con avance de 100% NO aparecen en Despiece.
     *
     * - Cuando un pedido llega al 100%, se guarda la fecha de terminado.
     *
     */
    public function index(Request $request)
    {
        /*
         * ================================================================
         * PROCESOS ACTIVOS
         * ================================================================
         */
        $procesos = Proceso::where('activo', true)
            ->orderBy('orden')
            ->get();


        /*
         * ================================================================
         * PEDIDOS
         * ================================================================
         *
         * Cargamos las partidas y sus procesos.
         */
        $pedidos = Pedido::with([
            'partidas.despieceProcesos.proceso'
        ])
            ->orderBy('fecha_entrega', 'asc')
            ->orderBy('pedido_no', 'asc')
            ->get();


        /*
         * ================================================================
         * ASEGURAR QUE TODAS LAS PARTIDAS TENGAN TODOS LOS PROCESOS
         * ================================================================
         *
         * Si falta un proceso, se crea automáticamente con:
         *
         *      aplica = true
         *      cantidad_realizada = 0
         *      porcentaje = 0
         *
         * firstOrCreate NO modifica registros existentes.
         */
        foreach ($pedidos as $pedido) {

            foreach ($pedido->partidas as $partida) {

                foreach ($procesos as $proceso) {

                    DespieceProceso::firstOrCreate(
                        [
                            'partida_id' => $partida->id,
                            'proceso_id' => $proceso->id,
                        ],
                        [
                            'aplica' => true,
                            'cantidad_realizada' => 0,
                            'porcentaje' => 0,
                        ]
                    );
                }
            }
        }


        /*
         * ================================================================
         * VOLVER A CARGAR LAS RELACIONES
         * ================================================================
         */
        $pedidos->load([
            'partidas.despieceProcesos.proceso'
        ]);


        /*
         * ================================================================
         * CONSTRUIR FILAS DEL DESPIECE
         * ================================================================
         *
         * Primero verificamos los pedidos completos.
         *
         * Si alguno ya está al 100% y todavía no tiene fecha de
         * terminado, se registra la fecha actual.
         *
         * Esto también permite detectar pedidos que llegaron al
         * 100% antes de que esta lógica fuera implementada.
         */
        foreach ($pedidos as $pedido) {

            if (
                $pedido->avance !== null &&
                $pedido->avance >= 1 &&
                $pedido->fecha_terminado === null
            ) {

                $pedido->fecha_terminado =
                    now()->toDateString();

                $pedido->save();
            }
        }


        /*
         * ================================================================
         * EXCLUIR PEDIDOS TERMINADOS
         * ================================================================
         *
         * IMPORTANTE:
         *
         * No se elimina absolutamente nada.
         *
         * Solamente se excluyen de la vista de Despiece.
         */
        $pedidos = $pedidos
            ->filter(function ($pedido) {

                return $pedido->avance === null
                    || $pedido->avance < 1;

            })
            ->values();


        /*
         * ================================================================
         * CONSTRUIR FILAS DEL DESPIECE
         * ================================================================
         */
        $filasDespiece = [];

        foreach ($pedidos as $pedido) {

            $avancePedido = $pedido->avance;

            foreach ($pedido->partidas as $partida) {

                $fila = [
                    'pedido_id' => $pedido->id,

                    'pedido_no' => $pedido->pedido_no,

                    'partida_id' => $partida->id,

                    'clave' => $partida->clave,

                    'descripcion' => $partida->descripcion,

                    'cantidad' => $partida->cantidad,

                    'avance_pedido' => $avancePedido,
                ];


                /*
                 * ========================================================
                 * PROCESOS
                 * ========================================================
                 */
                foreach ($procesos as $proceso) {

                    $despiece = $partida
                        ->despieceProcesos
                        ->firstWhere(
                            'proceso_id',
                            $proceso->id
                        );


                    /*
                     * Cantidad realizada.
                     *
                     * Si no existe, mostramos 0.
                     */
                    $fila[
                        'proceso_' . $proceso->id
                    ] =
                        $despiece
                            ? (float) $despiece->cantidad_realizada
                            : 0;


                    /*
                     * Indica si el proceso aplica.
                     *
                     * true  = proceso normal
                     * false = N/A
                     */
                    $fila[
                        'proceso_aplica_' . $proceso->id
                    ] =
                        $despiece
                            ? (bool) $despiece->aplica
                            : true;
                }


                $filasDespiece[] = $fila;
            }
        }


        /*
         * ================================================================
         * DATOS DE PROCESOS PARA JAVASCRIPT
         * ================================================================
         */
        $procesosData = $procesos
            ->map(function ($proceso) {

                return [
                    'id' => $proceso->id,
                    'nombre' => $proceso->nombre,
                ];

            })
            ->values();


        /*
         * ================================================================
         * VISTA
         * ================================================================
         */
        return view(
            'despiece.index',
            compact(
                'pedidos',
                'procesos',
                'filasDespiece',
                'procesosData'
            )
        );
    }


    /**
     * =========================================================================
     * GUARDAR PROCESO
     * =========================================================================
     */
    public function guardarProceso(Request $request)
    {
        /*
         * ================================================================
         * VALIDACIÓN
         * ================================================================
         */
        $datos = $request->validate([

            'partida_id' => [
                'required',
                'integer',
                'exists:partidas,id',
            ],

            'proceso_id' => [
                'required',
                'integer',
                'exists:procesos,id',
            ],

            'aplica' => [
                'required',
                'boolean',
            ],

            'cantidad_realizada' => [
                'nullable',
                'numeric',
                'min:0',
            ],

        ]);


        /*
         * ================================================================
         * OBTENER PARTIDA
         * ================================================================
         */
        $partida = Partida::findOrFail(
            $datos['partida_id']
        );


        /*
         * ================================================================
         * OBTENER PROCESO
         * ================================================================
         */
        $proceso = Proceso::findOrFail(
            $datos['proceso_id']
        );


        /*
         * ================================================================
         * VALIDAR PROCESO ACTIVO
         * ================================================================
         */
        if (!$proceso->activo) {

            return response()->json([
                'ok' => false,

                'mensaje' =>
                    'El proceso seleccionado no está activo.',

            ], 422);
        }


        /*
         * ================================================================
         * CANTIDAD TOTAL DE LA PARTIDA
         * ================================================================
         */
        $cantidadPartida =
            (float) $partida->cantidad;


        if ($cantidadPartida <= 0) {

            return response()->json([
                'ok' => false,

                'mensaje' =>
                    'La partida tiene una cantidad inválida.',

            ], 422);
        }


        /*
         * ================================================================
         * PROCESO N/A
         * ================================================================
         */
        if (!$datos['aplica']) {

            $despieceProceso =
                DespieceProceso::updateOrCreate(
                    [
                        'partida_id' =>
                            $partida->id,

                        'proceso_id' =>
                            $proceso->id,
                    ],

                    [
                        'aplica' =>
                            false,

                        'cantidad_realizada' =>
                            0,

                        'porcentaje' =>
                            0,
                    ]
                );


            /*
             * ============================================================
             * CALCULAR AVANCE DE PARTIDA
             * ============================================================
             */
            $avance =
                $this->calcularAvancePartida(
                    $partida->id
                );


            /*
             * ============================================================
             * RESPUESTA
             * ============================================================
             */
            return response()->json([

                'ok' => true,

                'mensaje' =>
                    'Proceso marcado como N/A.',

                'partida_id' =>
                    $partida->id,

                'proceso_id' =>
                    $proceso->id,

                'aplica' =>
                    false,

                'cantidad_realizada' =>
                    0,

                'porcentaje' =>
                    0,

                'avance' =>
                    $avance,

            ]);
        }


        /*
         * ================================================================
         * PROCESO NORMAL
         * ================================================================
         *
         * Si llega NULL porque se quitó el N/A,
         * lo convertimos en 0.
         */
        $cantidadRealizada =
            $datos['cantidad_realizada'] ?? 0;


        $cantidadRealizada =
            (float) $cantidadRealizada;


        /*
         * ================================================================
         * NO SUPERAR LA CANTIDAD DE LA PARTIDA
         * ================================================================
         */
        if (
            $cantidadRealizada >
            $cantidadPartida
        ) {

            return response()->json([

                'ok' => false,

                'mensaje' =>
                    'La cantidad realizada no puede ser mayor que '
                    . $cantidadPartida
                    . ' piezas.',

            ], 422);
        }


        /*
         * ================================================================
         * CALCULAR PORCENTAJE
         * ================================================================
         */
        $porcentaje =
            $cantidadRealizada /
            $cantidadPartida;


        /*
         * Asegurar rango 0 - 1
         */
        $porcentaje =
            max(
                0,
                min(
                    1,
                    $porcentaje
                )
            );


        /*
         * ================================================================
         * GUARDAR
         * ================================================================
         */
        $despieceProceso =
            DespieceProceso::updateOrCreate(
                [
                    'partida_id' =>
                        $partida->id,

                    'proceso_id' =>
                        $proceso->id,
                ],

                [
                    'aplica' =>
                        true,

                    'cantidad_realizada' =>
                        $cantidadRealizada,

                    'porcentaje' =>
                        $porcentaje,
                ]
            );


        /*
         * ================================================================
         * RECALCULAR AVANCE DE PARTIDA
         * ================================================================
         */
        $avance =
            $this->calcularAvancePartida(
                $partida->id
            );


        /*
         * ================================================================
         * VERIFICAR PEDIDO
         * ================================================================
         *
         * Después de guardar el proceso, comprobamos si el pedido
         * completo llegó al 100%.
         */
        $pedido =
            $partida->pedido()
                ->with([
                    'partidas.despieceProcesos'
                ])
                ->first();


        $pedidoTerminado = false;


        if ($pedido) {

            $pedidoTerminado =
                $this->pedidoEstaTerminado(
                    $pedido
                );


            /*
             * ============================================================
             * GUARDAR FECHA DE TERMINADO
             * ============================================================
             *
             * Solamente se guarda si todavía no existe.
             *
             * Esto conserva la fecha histórica.
             */
            if (
                $pedidoTerminado &&
                $pedido->fecha_terminado === null
            ) {

                $pedido->fecha_terminado =
                    now()->toDateString();

                $pedido->save();
            }
        }


        /*
         * ================================================================
         * RESPUESTA
         * ================================================================
         */
        return response()->json([

            'ok' => true,

            'mensaje' =>
                $pedidoTerminado
                    ? 'Proceso guardado. El pedido quedó terminado.'
                    : 'Proceso guardado correctamente.',

            'partida_id' =>
                $partida->id,

            'proceso_id' =>
                $proceso->id,

            'aplica' =>
                true,

            'cantidad_realizada' =>
                (float)
                $despieceProceso->cantidad_realizada,

            'porcentaje' =>
                (float)
                $despieceProceso->porcentaje,

            'avance' =>
                $avance,

            'pedido_terminado' =>
                $pedidoTerminado,

            'fecha_terminado' =>
                $pedido &&
                $pedido->fecha_terminado
                    ? $pedido->fecha_terminado->format('Y-m-d')
                    : null,

        ]);
    }


    /**
     * =========================================================================
     * CALCULAR AVANCE DE UNA PARTIDA
     * =========================================================================
     */
    private function calcularAvancePartida(
        int $partidaId
    ): ?float {

        /*
         * ================================================================
         * PARTIDA
         * ================================================================
         */
        $partida =
            Partida::with(
                'despieceProcesos'
            )
            ->findOrFail(
                $partidaId
            );


        /*
         * ================================================================
         * PROCESOS ACTIVOS
         * ================================================================
         */
        $procesosActivos =
            Proceso::where(
                'activo',
                true
            )
            ->orderBy(
                'orden'
            )
            ->get();


        if (
            $procesosActivos->isEmpty()
        ) {

            return 0;
        }


        /*
         * ================================================================
         * INDEXAR CAPTURAS
         * ================================================================
         */
        $despieces =
            $partida
                ->despieceProcesos
                ->keyBy(
                    'proceso_id'
                );


        $sumaPorcentajes = 0;

        $cantidadProcesosAplicables = 0;

        $hayProcesoPendiente = false;


        /*
         * ================================================================
         * RECORRER LOS PROCESOS
         * ================================================================
         */
        foreach (
            $procesosActivos as $proceso
        ) {

            $despiece =
                $despieces->get(
                    $proceso->id
                );


            /*
             * Si falta el registro,
             * se considera pendiente.
             */
            if (!$despiece) {

                $hayProcesoPendiente = true;

                continue;
            }


            /*
             * ============================================================
             * N/A
             * ============================================================
             */
            if (!$despiece->aplica) {

                continue;
            }


            /*
             * ============================================================
             * PORCENTAJE
             * ============================================================
             */
            $porcentaje =
                (float)
                $despiece->porcentaje;


            /*
             * Asegurar rango 0 - 1
             */
            $porcentaje =
                max(
                    0,
                    min(
                        1,
                        $porcentaje
                    )
                );


            /*
             * 0 TAMBIÉN CUENTA
             */
            $sumaPorcentajes +=
                $porcentaje;


            $cantidadProcesosAplicables++;
        }


        /*
         * ================================================================
         * NO HAY PROCESOS APLICABLES
         * ================================================================
         */
        if (
            $cantidadProcesosAplicables === 0
        ) {

            return 0;
        }


        /*
         * ================================================================
         * PROMEDIO
         * ================================================================
         */
        $avance =
            $sumaPorcentajes /
            $cantidadProcesosAplicables;


        /*
         * ================================================================
         * SI FALTA UN PROCESO
         * ================================================================
         */
        if (
            $hayProcesoPendiente &&
            $avance >= 1
        ) {

            $avance = 0.9999;
        }


        /*
         * ================================================================
         * ASEGURAR RANGO
         * ================================================================
         */
        return max(
            0,
            min(
                1,
                $avance
            )
        );
    }


    /**
     * =========================================================================
     * VERIFICAR SI EL PEDIDO ESTÁ TERMINADO
     * =========================================================================
     *
     * Un pedido solamente se considera TERMINADO cuando:
     *
     * 1. Tiene partidas.
     * 2. Todas las partidas tienen todos los procesos activos.
     * 3. Cada proceso aplicable tiene 100%.
     * 4. Los procesos N/A están resueltos.
     *
     * Esta función NO modifica información.
     */
    private function pedidoEstaTerminado(
        Pedido $pedido
    ): bool {

        /*
         * ================================================================
         * PROCESOS ACTIVOS
         * ================================================================
         */
        $procesosActivos =
            Proceso::where(
                'activo',
                true
            )
            ->orderBy(
                'orden'
            )
            ->get();


        /*
         * Si no existen procesos,
         * no podemos considerar terminado el pedido.
         */
        if (
            $procesosActivos->isEmpty()
        ) {

            return false;
        }


        /*
         * ================================================================
         * PARTIDAS
         * ================================================================
         */
        $partidas =
            $pedido->partidas;


        /*
         * Un pedido sin partidas
         * no puede estar terminado.
         */
        if (
            $partidas->isEmpty()
        ) {

            return false;
        }


        /*
         * ================================================================
         * REVISAR CADA PARTIDA
         * ================================================================
         */
        foreach ($partidas as $partida) {

            /*
             * Cantidad inválida
             */
            if (
                (float) $partida->cantidad <= 0
            ) {

                return false;
            }


            $despieces =
                $partida
                    ->despieceProcesos
                    ->keyBy(
                        'proceso_id'
                    );


            /*
             * ============================================================
             * REVISAR TODOS LOS PROCESOS
             * ============================================================
             */
            foreach (
                $procesosActivos as $proceso
            ) {

                $despiece =
                    $despieces->get(
                        $proceso->id
                    );


                /*
                 * Falta el proceso.
                 */
                if (!$despiece) {

                    return false;
                }


                /*
                 * N/A está resuelto.
                 */
                if (!$despiece->aplica) {

                    continue;
                }


                /*
                 * ========================================================
                 * PROCESO NORMAL
                 * ========================================================
                 *
                 * Debe tener 100%.
                 */
                $porcentaje =
                    (float)
                    $despiece->porcentaje;


                if (
                    $porcentaje < 1
                ) {

                    return false;
                }
            }
        }


        /*
         * ================================================================
         * TODAS LAS PARTIDAS Y PROCESOS ESTÁN COMPLETOS
         * ================================================================
         */
        return true;
    }
}