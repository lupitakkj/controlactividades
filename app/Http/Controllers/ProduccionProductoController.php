<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProduccionProductoController extends Controller
{
    /**
     * Mostrar producción actual por producto.
     *
     * El buscador permite consultar por:
     * - Prefijo de clave
     * - Clave completa o parcial
     * - Descripción
     * - Número de pedido
     */
    public function index(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | BÚSQUEDA
        |--------------------------------------------------------------------------
        */

        $buscar = trim(
            (string) $request->get('buscar', '')
        );


        /*
        |--------------------------------------------------------------------------
        | PROCESOS ACTIVOS
        |--------------------------------------------------------------------------
        |
        | Obtenemos una sola vez los procesos activos.
        |
        */

        $procesosActivos = DB::table('procesos')
            ->where('activo', 1)
            ->select([
                'id',
                'nombre',
                'orden',
            ])
            ->orderBy('orden')
            ->get();


        /*
        |--------------------------------------------------------------------------
        | MISMA POBLACIÓN QUE EL DASHBOARD DE DIRECCIÓN
        |--------------------------------------------------------------------------
        |
        | El Dashboard considera activo un pedido cuando:
        | - Su avance es menor al 100%.
        | - Su estado operativo es EN PROCESO o POR INICIAR.
        |
        | Para que los importes coincidan, aquí se incluyen TODAS las partidas
        | de esos pedidos activos, aunque una partida individual ya tenga todos
        | sus procesos terminados. El avance y proceso actual se siguen mostrando
        | por partida; ya no se usa el proceso pendiente como filtro de inclusión.
        |
        */

        $pedidosActivos = Pedido::with([
            'controlOperativo',
            'partidas.despieceProcesos.proceso',
        ])->get()->filter(function ($pedido) {
            $estado = strtoupper(trim(
                $pedido->controlOperativo?->estado_operativo ?? ''
            ));

            return $pedido->avance < 1
                && in_array($estado, ['EN PROCESO', 'POR INICIAR'], true);
        })->values();

        $pedidosActivosIds = $pedidosActivos->pluck('id')->all();

        $query = DB::table('partidas as p')
            ->join('pedidos as pe', 'pe.id', '=', 'p.pedido_id')
            ->whereIn('pe.id', $pedidosActivosIds);

        /*
        |--------------------------------------------------------------------------
        | FILTRO DE PRODUCTO
        |--------------------------------------------------------------------------
        |
        | El buscador puede recibir:
        |
        | TPOS
        | TPOS0701110
        | POSTE
        | POSTE DE 221
        | 463
        |
        */

        if ($buscar !== '') {

            $query->where(
                function ($q) use ($buscar) {

                    $q->where(
                        'p.clave',
                        'LIKE',
                        '%' . $buscar . '%'
                    )

                    ->orWhere(
                        'p.descripcion',
                        'LIKE',
                        '%' . $buscar . '%'
                    )

                    ->orWhere(
                        'pe.pedido_no',
                        'LIKE',
                        '%' . $buscar . '%'
                    );
                }
            );
        }


        /*
        |--------------------------------------------------------------------------
        | OBTENER PARTIDAS
        |--------------------------------------------------------------------------
        */

        $partidasEnProduccion = $query

            ->select([
                'p.id as partida_id',
                'pe.id as pedido_id',
                'pe.pedido_no',
                'p.clave',
                'p.descripcion',
                'p.cantidad',
                'p.precio_unitario',
                'p.importe_total',
            ])

            ->orderBy(
                'pe.pedido_no'
            )

            ->orderBy(
                'p.clave'
            )

            ->get();


        /*
        |--------------------------------------------------------------------------
        | SI NO HAY PARTIDAS
        |--------------------------------------------------------------------------
        */

        if ($partidasEnProduccion->isEmpty()) {

            return view(
                'produccion-producto.index',
                [
                    'postesEnProduccion' => collect(),

                    'cantidadPostes' => 0,

                    'importePostes' => 0,

                    'partidasPostes' => 0,

                    'pedidosPostes' => 0,

                    'partidasEnProduccion' => collect(),

                    'cantidadProducto' => 0,

                    'importeProducto' => 0,

                    'partidasProducto' => 0,

                    'pedidosProducto' => 0,

                    'buscar' => $buscar,
                ]
            );
        }


        /*
        |--------------------------------------------------------------------------
        | IDS DE LAS PARTIDAS
        |--------------------------------------------------------------------------
        */

        $partidaIds = $partidasEnProduccion
            ->pluck('partida_id')
            ->map(function ($id) {
                return (int) $id;
            })
            ->values()
            ->all();


        /*
        |--------------------------------------------------------------------------
        | IDS DE LOS PROCESOS
        |--------------------------------------------------------------------------
        */

        $procesoIds = $procesosActivos
            ->pluck('id')
            ->map(function ($id) {
                return (int) $id;
            })
            ->values()
            ->all();


        /*
        |--------------------------------------------------------------------------
        | OBTENER DESPIECE DE TODAS LAS PARTIDAS
        |--------------------------------------------------------------------------
        |
        | En lugar de hacer una consulta por cada proceso de cada partida,
        | obtenemos todos los registros de una sola vez.
        |
        */

        $despieces = DB::table('despiece_procesos')

            ->whereIn(
                'partida_id',
                $partidaIds
            )

            ->whereIn(
                'proceso_id',
                $procesoIds
            )

            ->select([
                'partida_id',
                'proceso_id',
                'aplica',
                'porcentaje',
            ])

            ->get();


        /*
        |--------------------------------------------------------------------------
        | INDEXAR DESPIECES
        |--------------------------------------------------------------------------
        |
        | La clave será:
        |
        | partida_id + proceso_id
        |
        */

        $despiecesIndexados = [];


        foreach ($despieces as $despiece) {

            $clave =

                (int) $despiece->partida_id
                . '_'
                . (int) $despiece->proceso_id;


            $despiecesIndexados[$clave] = $despiece;
        }


        /*
        |--------------------------------------------------------------------------
        | CALCULAR AVANCE DE CADA PARTIDA
        |--------------------------------------------------------------------------
        */

        $partidasEnProduccion = $partidasEnProduccion
            ->map(function ($partida) use (
                $procesosActivos,
                $despiecesIndexados
            ) {

                /*
                |--------------------------------------------------------------------------
                | Convertimos la fila en objeto simple
                |--------------------------------------------------------------------------
                */

                $partidaId = (int) $partida->partida_id;


                /*
                |--------------------------------------------------------------------------
                | SUMA DE PORCENTAJES
                |--------------------------------------------------------------------------
                */

                $suma = 0;

                $procesosAplicables = 0;

                // Primer proceso aplicable que todavía no está al 100%.
                // Sin registro = pendiente; N/A = no cuenta.
                $partida->proceso_actual = 'Terminado';


                /*
                |--------------------------------------------------------------------------
                | RECORRER PROCESOS ACTIVOS
                |--------------------------------------------------------------------------
                */

                foreach ($procesosActivos as $proceso) {

                    $procesoId = (int) $proceso->id;


                    $clave =

                        $partidaId
                        . '_'
                        . $procesoId;


                    /*
                    |--------------------------------------------------------------------------
                    | BUSCAR REGISTRO
                    |--------------------------------------------------------------------------
                    */

                    $despiece =
                        $despiecesIndexados[$clave]
                        ?? null;


                    /*
                    |--------------------------------------------------------------------------
                    | SIN REGISTRO
                    |--------------------------------------------------------------------------
                    |
                    | Se considera:
                    |
                    | 0%
                    | Aplicable
                    |
                    */

                    if ($despiece === null) {

                        $procesosAplicables++;

                        if (
                            $partida->proceso_actual === 'Terminado'
                        ) {

                            $partida->proceso_actual =
                                $proceso->nombre;
                        }

                        continue;
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | N/A
                    |--------------------------------------------------------------------------
                    |
                    | aplica = 0
                    |
                    | NO cuenta para el promedio.
                    |
                    */

                    if (
                        (int) $despiece->aplica === 0
                    ) {

                        continue;
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | PROCESO APLICABLE
                    |--------------------------------------------------------------------------
                    */

                    $procesosAplicables++;

                    $porcentaje = max(
                        0,
                        min(
                            1,
                            (float) $despiece->porcentaje
                        )
                    );

                    $suma += $porcentaje;

                    // El primero menor a 100% es el proceso actual.
                    if (
                        $porcentaje < 1 &&
                        $partida->proceso_actual === 'Terminado'
                    ) {

                        $partida->proceso_actual =
                            $proceso->nombre;
                    }
                }


                /*
                |--------------------------------------------------------------------------
                | AVANCE
                |--------------------------------------------------------------------------
                */

                $partida->avance =

                    $procesosAplicables > 0

                    ? $suma / $procesosAplicables

                    : 0;


                /*
                |--------------------------------------------------------------------------
                | LIMITAR ENTRE 0 Y 1
                |--------------------------------------------------------------------------
                */

                $partida->avance = max(
                    0,
                    min(
                        1,
                        $partida->avance
                    )
                );


                return $partida;
            });


        /*
        |--------------------------------------------------------------------------
        | TOTALES
        |--------------------------------------------------------------------------
        */

        $cantidadProducto =

            $partidasEnProduccion->sum(
                function ($partida) {

                    return (float)
                        $partida->cantidad;
                }
            );


        // El dashboard suma importe_total una sola vez por pedido activo.
        // Aquí hacemos lo mismo con los pedidos representados en las partidas
        // resultantes (y respetamos el filtro de búsqueda, si existe).
        $pedidoIdsProducto = $partidasEnProduccion
            ->pluck('pedido_id')
            ->unique()
            ->values();

        $importeProducto = $pedidosActivos
            ->whereIn('id', $pedidoIdsProducto)
            ->sum(function ($pedido) {
                return (float) $pedido->importe_total;
            });


        $partidasProducto =

            $partidasEnProduccion->count();


        $pedidosProducto =

            $partidasEnProduccion
                ->pluck('pedido_id')
                ->unique()
                ->count();


        /*
        |--------------------------------------------------------------------------
        | COMPATIBILIDAD CON EL BLADE ACTUAL
        |--------------------------------------------------------------------------
        |
        | Tu Blade todavía utiliza nombres relacionados con "postes".
        |
        | Los mantenemos temporalmente para que no tengas otro error
        | mientras adaptamos la vista.
        |
        */

        $postesEnProduccion =
            $partidasEnProduccion;


        $cantidadPostes =
            $cantidadProducto;


        $importePostes =
            $importeProducto;


        $partidasPostes =
            $partidasProducto;


        $pedidosPostes =
            $pedidosProducto;


        /*
        |--------------------------------------------------------------------------
        | VISTA
        |--------------------------------------------------------------------------
        */

        return view(
            'produccion-producto.index',
            compact(
                'partidasEnProduccion',
                'cantidadProducto',
                'importeProducto',
                'partidasProducto',
                'pedidosProducto',

                'postesEnProduccion',
                'cantidadPostes',
                'importePostes',
                'partidasPostes',
                'pedidosPostes',

                'buscar'
            )
        );
    }
}