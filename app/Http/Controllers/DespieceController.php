<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use App\Models\Proceso;
use App\Models\Partida;
use App\Models\ControlOperativo;
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
     * - Cada partida puede tener procesos activos.
     * - Proceso normal:
     *      aplica = true
     *      cantidad_realizada = cantidad capturada
     *      porcentaje = cantidad_realizada / cantidad_partida
     *
     * - Proceso N/A:
     *      aplica = false
     *      cantidad_realizada = 0
     *      porcentaje = 0
     *
     * - N/A NO participa en el promedio.
     * - Una cantidad de 0 SÍ participa en el promedio.
     * - El avance de una partida es el promedio de sus procesos aplicables.
     * - El avance del pedido es el promedio de sus partidas.
     * - Los pedidos con avance de 100% NO aparecen en Despiece.
     * - fecha_terminado solamente se guarda al terminar realmente un pedido.
     *
     */

    public function index(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | 1. PROCESOS ACTIVOS
        |--------------------------------------------------------------------------
        |
        | IMPORTANTE:
        | Se consulta UNA SOLA VEZ.
        |
        */

        $procesos = Proceso::query()
            ->select([
                'id',
                'nombre',
                'orden',
                'activo',
            ])
            ->where('activo', true)
            ->orderBy('orden')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | 2. PEDIDOS
        |--------------------------------------------------------------------------
        |
        | Cargamos únicamente las columnas necesarias.
        |
        | También cargamos todas las relaciones en una sola carga.
        |
        */

        $pedidos = Pedido::query()
            ->select([
                'id',
                'pedido_no',
                'fecha_entrega',
            ])
            ->with([
                'partidas' => function ($query) {

                    $query->select([
                        'id',
                        'pedido_id',
                        'clave',
                        'descripcion',
                        'cantidad',
                    ]);
                },

                'partidas.despieceProcesos' => function ($query) {

                    $query->select([
                        'id',
                        'partida_id',
                        'proceso_id',
                        'aplica',
                        'cantidad_realizada',
                        'porcentaje',
                    ]);
                },
            ])
            ->orderBy('fecha_entrega', 'asc')
            ->orderBy('pedido_no', 'asc')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | 3. CALCULAR AVANCE UNA SOLA VEZ POR PEDIDO
        |--------------------------------------------------------------------------
        |
        | Antes:
        |
        |     $pedido->avance
        |
        | podía ejecutarse varias veces.
        |
        | Ahora calculamos el avance una sola vez y lo guardamos en memoria.
        |
        */

        $pedidosConAvance = collect();

        foreach ($pedidos as $pedido) {

            $avancePedido = $this->calcularAvancePedido(
                $pedido,
                $procesos
            );

            /*
            |--------------------------------------------------------------------------
            | EXCLUIR TERMINADOS
            |--------------------------------------------------------------------------
            */

            if (
                $avancePedido !== null &&
                $avancePedido >= 1
            ) {
                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | GUARDAR AVANCE EN MEMORIA
            |--------------------------------------------------------------------------
            |
            | No modifica la base de datos.
            |
            */

            $pedido->setAttribute(
                'avance_calculado',
                $avancePedido
            );

            $pedidosConAvance->push($pedido);
        }

        $pedidos = $pedidosConAvance->values();

        /*
        |--------------------------------------------------------------------------
        | 4. CONSTRUIR FILAS
        |--------------------------------------------------------------------------
        */

        $filasDespiece = [];

        foreach ($pedidos as $pedido) {

            /*
            |--------------------------------------------------------------------------
            | AVANCE DEL PEDIDO
            |--------------------------------------------------------------------------
            */

            $avancePedido =
                $pedido->getAttribute('avance_calculado');

            /*
            |--------------------------------------------------------------------------
            | PARTIDAS
            |--------------------------------------------------------------------------
            */

            foreach ($pedido->partidas as $partida) {

                /*
                |--------------------------------------------------------------------------
                | INDEXAR DESPIECES
                |--------------------------------------------------------------------------
                |
                | Esto evita hacer firstWhere() repetidamente.
                |
                */

                $despieces =
                    $partida->despieceProcesos
                    ->keyBy('proceso_id');

                /*
                |--------------------------------------------------------------------------
                | FILA
                |--------------------------------------------------------------------------
                */

                $fila = [

                    'pedido_id' =>
                    $pedido->id,

                    'pedido_no' =>
                    $pedido->pedido_no,

                    'partida_id' =>
                    $partida->id,

                    'clave' =>
                    $partida->clave,

                    'descripcion' =>
                    $partida->descripcion,

                    'cantidad' =>
                    $partida->cantidad,

                    'avance_pedido' =>
                    $avancePedido,
                ];

                /*
                |--------------------------------------------------------------------------
                | PROCESOS
                |--------------------------------------------------------------------------
                */

                foreach ($procesos as $proceso) {

                    $despiece =
                        $despieces->get($proceso->id);

                    /*
                    |--------------------------------------------------------------------------
                    | CANTIDAD REALIZADA
                    |--------------------------------------------------------------------------
                    */

                    $fila['proceso_' . $proceso->id] =
                        $despiece
                        ? (float) $despiece->cantidad_realizada
                        : null;

                    /*
                    |--------------------------------------------------------------------------
                    | APLICA
                    |--------------------------------------------------------------------------
                    */

                    $fila['proceso_aplica_' . $proceso->id] =
                        $despiece
                        ? (bool) $despiece->aplica
                        : null;
                }

                $filasDespiece[] = $fila;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | 5. DATOS PARA JAVASCRIPT
        |--------------------------------------------------------------------------
        */

        $procesosData = $procesos
            ->map(function ($proceso) {

                return [
                    'id' =>
                    $proceso->id,

                    'nombre' =>
                    $proceso->nombre,
                ];
            })
            ->values();

        /*
        |--------------------------------------------------------------------------
        | 6. VISTA
        |--------------------------------------------------------------------------
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
     * CALCULAR AVANCE DEL PEDIDO
     * =========================================================================
     *
     * No realiza consultas a la base de datos.
     *
     * Todo se calcula usando las relaciones ya cargadas.
     *
     */

    private function calcularAvancePedido(
        Pedido $pedido,
        $procesosActivos
    ): ?float {

        $partidas = $pedido->partidas;

        /*
        |--------------------------------------------------------------------------
        | SIN PARTIDAS
        |--------------------------------------------------------------------------
        */

        if ($partidas->isEmpty()) {
            return null;
        }

        /*
        |--------------------------------------------------------------------------
        | SIN PROCESOS ACTIVOS
        |--------------------------------------------------------------------------
        */

        if ($procesosActivos->isEmpty()) {
            return 0;
        }

        /*
        |--------------------------------------------------------------------------
        | ACUMULADORES DEL PEDIDO
        |--------------------------------------------------------------------------
        |
        | Cada partida pesa de acuerdo con su cantidad de piezas.
        |
        | Fórmula:
        |
        |   SUMA(avance partida × cantidad partida)
        |   --------------------------------------
        |          SUMA(cantidad partida)
        |
        */

        $sumaAvancePonderado = 0;

        $sumaCantidad = 0;


        /*
        |--------------------------------------------------------------------------
        | RECORRER PARTIDAS
        |--------------------------------------------------------------------------
        */

        foreach ($partidas as $partida) {

            /*
            |--------------------------------------------------------------------------
            | CANTIDAD DE LA PARTIDA
            |--------------------------------------------------------------------------
            */

            $cantidadPartida = (float) $partida->cantidad;

            /*
            |--------------------------------------------------------------------------
            | IGNORAR CANTIDADES INVÁLIDAS
            |--------------------------------------------------------------------------
            */

            if ($cantidadPartida <= 0) {
                continue;
            }


            /*
            |--------------------------------------------------------------------------
            | INDEXAR DESPIECES
            |--------------------------------------------------------------------------
            */

            $despieces = $partida->despieceProcesos
                ->keyBy('proceso_id');


            /*
            |--------------------------------------------------------------------------
            | ACUMULADORES DE LA PARTIDA
            |--------------------------------------------------------------------------
            */

            $sumaPorcentajes = 0;

            $cantidadProcesosAplicables = 0;

            $hayProcesoPendiente = false;


            /*
            |--------------------------------------------------------------------------
            | RECORRER PROCESOS ACTIVOS
            |--------------------------------------------------------------------------
            */

            foreach ($procesosActivos as $proceso) {

                $despiece = $despieces->get($proceso->id);


                /*
            |--------------------------------------------------------------------------
            | PROCESO SIN REGISTRO
            |--------------------------------------------------------------------------
            |
            | No existe todavía en despiece_procesos.
            |
            | Esto significa:
            |
            |     - No se ha capturado
            |     - Está pendiente
            |     - Su avance es 0%
            |     - SÍ participa en el promedio
            |
            */

                if (!$despiece) {

                    $hayProcesoPendiente = true;

                    $cantidadProcesosAplicables++;

                    continue;
                }


                /*
            |--------------------------------------------------------------------------
            | PROCESO N/A
            |--------------------------------------------------------------------------
            |
            | Si aplica = false:
            |
            |     - No participa
            |     - No suma porcentaje
            |     - No cuenta como pendiente
            |
            */

                if (!$despiece->aplica) {

                    continue;
                }


                /*
            |--------------------------------------------------------------------------
            | PROCESO APLICABLE
            |--------------------------------------------------------------------------
            */

                $cantidadProcesosAplicables++;


                /*
            |--------------------------------------------------------------------------
            | CANTIDAD REALIZADA
            |--------------------------------------------------------------------------
            */

                $cantidadRealizada =
                    $despiece->cantidad_realizada;


                /*
            |--------------------------------------------------------------------------
            | SIN CANTIDAD CAPTURADA
            |--------------------------------------------------------------------------
            |
            | Aunque exista el registro, si la cantidad es NULL,
            | todavía está pendiente.
            |
            */

                if ($cantidadRealizada === null) {

                    $hayProcesoPendiente = true;

                    continue;
                }


                /*
            |--------------------------------------------------------------------------
            | PORCENTAJE
            |--------------------------------------------------------------------------
            */

                $porcentaje =
                    (float) $despiece->porcentaje;


                /*
            |--------------------------------------------------------------------------
            | LIMITAR PORCENTAJE
            |--------------------------------------------------------------------------
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
            |--------------------------------------------------------------------------
            | ACUMULAR
            |--------------------------------------------------------------------------
            */

                $sumaPorcentajes += $porcentaje;
            }


            /*
        |--------------------------------------------------------------------------
        | AVANCE DE LA PARTIDA
        |--------------------------------------------------------------------------
        */

            if ($cantidadProcesosAplicables > 0) {

                $avancePartida =
                    $sumaPorcentajes /
                    $cantidadProcesosAplicables;
            } else {

                /*
            | Todos los procesos son N/A
            */

                $avancePartida = 0;
            }


            /*
        |--------------------------------------------------------------------------
        | EVITAR 100% SI EXISTE UN PROCESO PENDIENTE
        |--------------------------------------------------------------------------
        |
        | Si tenemos:
        |
        |     Corte       = 100%
        |     Soldadura   = NULL
        |     Pintura     = NULL
        |
        | El resultado será:
        |
        |     (100 + 0 + 0) / 3
        |     = 33.33%
        |
        | Por lo tanto normalmente aquí ya no será 100%.
        |
        | Este bloque funciona como protección adicional.
        |
        */

            if (
                $hayProcesoPendiente &&
                $avancePartida >= 1
            ) {

                $avancePartida = 0.9999;
            }


            /*
        |--------------------------------------------------------------------------
        | LIMITAR AVANCE DE PARTIDA
        |--------------------------------------------------------------------------
        */

            $avancePartida =
                max(
                    0,
                    min(
                        1,
                        $avancePartida
                    )
                );


            /*
        |--------------------------------------------------------------------------
        | ACUMULAR AVANCE PONDERADO
        |--------------------------------------------------------------------------
        */

            $sumaAvancePonderado +=
                $avancePartida *
                $cantidadPartida;


            /*
        |--------------------------------------------------------------------------
        | ACUMULAR CANTIDAD
        |--------------------------------------------------------------------------
        */

            $sumaCantidad +=
                $cantidadPartida;
        }


        /*
        |--------------------------------------------------------------------------
        | SIN CANTIDADES VÁLIDAS
        |--------------------------------------------------------------------------
        */

        if ($sumaCantidad <= 0) {
            return null;
        }


        /*
        |--------------------------------------------------------------------------
        | AVANCE FINAL DEL PEDIDO
        |--------------------------------------------------------------------------
        */

        $avancePedido =
            $sumaAvancePonderado /
            $sumaCantidad;


        /*
        |--------------------------------------------------------------------------
        | LIMITAR AVANCE FINAL
        |--------------------------------------------------------------------------
        */

        return max(
            0,
            min(
                1,
                $avancePedido
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
        |--------------------------------------------------------------------------
        | VALIDACIÓN
        |--------------------------------------------------------------------------
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
        |--------------------------------------------------------------------------
        | PARTIDA
        |--------------------------------------------------------------------------
        */

        $partida =
            Partida::findOrFail(
                $datos['partida_id']
            );

        /*
        |--------------------------------------------------------------------------
        | PROCESO
        |--------------------------------------------------------------------------
        */

        $proceso =
            Proceso::findOrFail(
                $datos['proceso_id']
            );

        /*
        |--------------------------------------------------------------------------
        | VALIDAR PROCESO ACTIVO
        |--------------------------------------------------------------------------
        */

        if (!$proceso->activo) {

            return response()->json([

                'ok' => false,

                'mensaje' =>
                'El proceso seleccionado no está activo.',

            ], 422);
        }

        /*
        |--------------------------------------------------------------------------
        | CANTIDAD DE PARTIDA
        |--------------------------------------------------------------------------
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
        |--------------------------------------------------------------------------
        | PROCESO N/A
        |--------------------------------------------------------------------------
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
    |--------------------------------------------------------------------------
    | AVANCE DE PARTIDA
    |--------------------------------------------------------------------------
    */

            $avance =
                $this->calcularAvancePartida(
                    $partida->id
                );

            /*
    |--------------------------------------------------------------------------
    | PEDIDO
    |--------------------------------------------------------------------------
    */

            $pedido =
                $partida
                ->pedido()
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

                if ($pedidoTerminado) {

                    /*
            |--------------------------------------------------------------------------
            | FECHA DE TERMINADO
            |--------------------------------------------------------------------------
            */

                    if ($pedido->fecha_terminado === null) {

                        $pedido->fecha_terminado =
                            now()->toDateString();

                        $pedido->save();
                    }

                    /*
            |--------------------------------------------------------------------------
            | ESTADO OPERATIVO
            |--------------------------------------------------------------------------
            */

                    ControlOperativo::updateOrCreate(
                        [
                            'pedido_id' =>
                            $pedido->id,
                        ],
                        [
                            'estado_operativo' =>
                            'Terminado',
                        ]
                    );
                }
            }

            /*
    |--------------------------------------------------------------------------
    | RESPUESTA
    |--------------------------------------------------------------------------
    */

            return response()->json([

                'ok' => true,

                'mensaje' =>
                $pedidoTerminado
                    ? 'Proceso marcado como N/A. El pedido quedó terminado.'
                    : 'Proceso marcado como N/A.',

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

                'pedido_terminado' =>
                $pedidoTerminado,

                'fecha_terminado' =>
                $pedido &&
                    $pedido->fecha_terminado
                    ? $pedido->fecha_terminado->format('Y-m-d')
                    : null,

            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | PROCESO NORMAL
        |--------------------------------------------------------------------------
        */

        $cantidadRealizada =
            $datos['cantidad_realizada'] ?? 0;

        $cantidadRealizada =
            (float) $cantidadRealizada;

        /*
        |--------------------------------------------------------------------------
        | NO SUPERAR CANTIDAD
        |--------------------------------------------------------------------------
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
        |--------------------------------------------------------------------------
        | PORCENTAJE
        |--------------------------------------------------------------------------
        */

        $porcentaje =
            $cantidadRealizada /
            $cantidadPartida;

        $porcentaje =
            max(
                0,
                min(
                    1,
                    $porcentaje
                )
            );

        /*
        |--------------------------------------------------------------------------
        | GUARDAR
        |--------------------------------------------------------------------------
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
        |--------------------------------------------------------------------------
        | AVANCE DE PARTIDA
        |--------------------------------------------------------------------------
        */

        $avance =
            $this->calcularAvancePartida(
                $partida->id
            );

        /*
        |--------------------------------------------------------------------------
        | PEDIDO
        |--------------------------------------------------------------------------
        */

        $pedido =
            $partida
            ->pedido()
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

            if ($pedidoTerminado) {

                /*
                |--------------------------------------------------------------------------
                | FECHA DE TERMINADO
                |--------------------------------------------------------------------------
                */

                if ($pedido->fecha_terminado === null) {

                    $pedido->fecha_terminado =
                        now()->toDateString();

                    $pedido->save();
                }

                /*
                |--------------------------------------------------------------------------
                | ESTADO OPERATIVO
                |--------------------------------------------------------------------------
                */

                ControlOperativo::updateOrCreate(
                    [
                        'pedido_id' =>
                        $pedido->id,
                    ],
                    [
                        'estado_operativo' =>
                        'Terminado',
                    ]
                );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | RESPUESTA
        |--------------------------------------------------------------------------
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
        |--------------------------------------------------------------------------
        | PARTIDA
        |--------------------------------------------------------------------------
        */

        $partida =
            Partida::with(
                'despieceProcesos'
            )
            ->findOrFail(
                $partidaId
            );

        /*
        |--------------------------------------------------------------------------
        | PROCESOS ACTIVOS
        |--------------------------------------------------------------------------
        */

        $procesosActivos =
            Proceso::query()
            ->select([
                'id',
                'orden',
                'activo',
            ])
            ->where('activo', true)
            ->orderBy('orden')
            ->get();

        if (
            $procesosActivos->isEmpty()
        ) {

            return 0;
        }

        /*
        |--------------------------------------------------------------------------
        | INDEXAR
        |--------------------------------------------------------------------------
        */

        $despieces =
            $partida
            ->despieceProcesos
            ->keyBy('proceso_id');

        $sumaPorcentajes = 0;

        $cantidadProcesosAplicables = 0;



        /*
        |--------------------------------------------------------------------------
        | RECORRER PROCESOS
        |--------------------------------------------------------------------------
        */

        foreach (
            $procesosActivos as $proceso
        ) {

            $despiece =
                $despieces->get(
                    $proceso->id
                );

            /*
            |--------------------------------------------------------------------------
            | NO CAPTURADO
            |--------------------------------------------------------------------------
            |
            | El proceso está activo pero todavía no tiene registro.
            | Cuenta como 0% de avance.
            |
            */

            if (!$despiece) {

                $cantidadProcesosAplicables++;

                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | N/A
            |--------------------------------------------------------------------------
            |
            | El proceso existe pero no aplica para esta partida.
            | NO cuenta dentro del porcentaje.
            |
            */

            if (!$despiece->aplica) {

                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | PORCENTAJE
            |--------------------------------------------------------------------------
            */

            $porcentaje =
                (float)
                $despiece->porcentaje;

            $porcentaje =
                max(
                    0,
                    min(
                        1,
                        $porcentaje
                    )
                );

            /*
            |--------------------------------------------------------------------------
            | ACUMULAR
            |--------------------------------------------------------------------------
            */

            $sumaPorcentajes +=
                $porcentaje;

            $cantidadProcesosAplicables++;
        }

        /*
        |--------------------------------------------------------------------------
        | SIN PROCESOS APLICABLES
        |--------------------------------------------------------------------------
        */

        if (
            $cantidadProcesosAplicables === 0
        ) {

            return 0;
        }

        /*
        |--------------------------------------------------------------------------
        | PROMEDIO
        |--------------------------------------------------------------------------
        */

        $avance =
            $sumaPorcentajes /
            $cantidadProcesosAplicables;

        /*
        |--------------------------------------------------------------------------
        | LIMITAR
        |--------------------------------------------------------------------------
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
     */

    private function pedidoEstaTerminado(
        Pedido $pedido
    ): bool {

        /*
        |--------------------------------------------------------------------------
        | PROCESOS ACTIVOS
        |--------------------------------------------------------------------------
        */

        $procesosActivos =
            Proceso::query()
            ->select([
                'id',
                'nombre',
                'orden',
                'activo',
            ])
            ->where('activo', true)
            ->orderBy('orden')
            ->get();

        if (
            $procesosActivos->isEmpty()
        ) {

            return false;
        }

        /*
        |--------------------------------------------------------------------------
        | ÚLTIMO PROCESO = TERMINADO
        |--------------------------------------------------------------------------
        |
        | El último proceso activo siempre representa TERMINADO.
        |
        */

        $procesoTerminado =
            $procesosActivos->last();

        /*
        |--------------------------------------------------------------------------
        | PARTIDAS
        |--------------------------------------------------------------------------
        */

        $partidas =
            $pedido->partidas;

        if (
            $partidas->isEmpty()
        ) {

            return false;
        }

        /*
        |--------------------------------------------------------------------------
        | REVISAR TODAS LAS PARTIDAS
        |--------------------------------------------------------------------------
        */

        foreach ($partidas as $partida) {

            /*
            |--------------------------------------------------------------------------
            | CANTIDAD INVÁLIDA
            |--------------------------------------------------------------------------
            */

            if (
                (float) $partida->cantidad <= 0
            ) {

                return false;
            }

            /*
            |--------------------------------------------------------------------------
            | INDEXAR DESPIECES
            |--------------------------------------------------------------------------
            */

            $despieces =
                $partida
                ->despieceProcesos
                ->keyBy('proceso_id');

            /*
            |--------------------------------------------------------------------------
            | BUSCAR PROCESO TERMINADO
            |--------------------------------------------------------------------------
            */

            $despieceTerminado =
                $despieces->get(
                    $procesoTerminado->id
                );

            /*
            |--------------------------------------------------------------------------
            | NO EXISTE REGISTRO DE TERMINADO
            |--------------------------------------------------------------------------
            */

            if (!$despieceTerminado) {

                return false;
            }

            /*
            |--------------------------------------------------------------------------
            | TERMINADO NO PUEDE SER N/A
            |--------------------------------------------------------------------------
            */

            if (!$despieceTerminado->aplica) {

                return false;
            }

            /*
            |--------------------------------------------------------------------------
            | TERMINADO DEBE ESTAR AL 100%
            |--------------------------------------------------------------------------
            */

            $porcentaje =
                (float)
                $despieceTerminado->porcentaje;

            if (
                $porcentaje < 1
            ) {

                return false;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | TODAS LAS PARTIDAS TERMINADAS
        |--------------------------------------------------------------------------
        */

        return true;
    }
}
