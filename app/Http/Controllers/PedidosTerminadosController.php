<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use Illuminate\Http\Request;

class PedidosTerminadosController extends Controller
{
    /**
     * =========================================================================
     * PEDIDOS TERMINADOS
     * =========================================================================
     *
     * Muestra los pedidos cuyo avance calculado es 100%.
     *
     * También muestra:
     *
     * - Fecha de solicitud
     * - Fecha de terminado
     * - Días transcurridos entre ambas fechas
     *
     */
    public function index(Request $request)
    {
        /*
         * ================================================================
         * CARGAR PEDIDOS
         * ================================================================
         */
        $pedidos = Pedido::with([
            'partidas.despieceProcesos.proceso'
        ])
            ->orderBy(
                'fecha_entrega',
                'desc'
            )
            ->orderBy(
                'pedido_no',
                'desc'
            )
            ->get();


        /*
         * ================================================================
         * FILTRAR PEDIDOS TERMINADOS
         * ================================================================
         *
         * Un pedido terminado es aquel cuyo avance calculado
         * es 100% o mayor.
         */
        $pedidosTerminados =
            $pedidos->filter(function ($pedido) {

                return $pedido->avance !== null
                    && $pedido->avance >= 1;
            })
            ->values();


        /*
         * ================================================================
         * CALCULAR DÍAS DE DIFERENCIA
         * ================================================================
         *
         * Fecha de terminado - Fecha de solicitud.
         *
         * Si alguna de las dos fechas no existe,
         * dejamos el valor en NULL.
         */
        $pedidosTerminados->each(function ($pedido) {

            if (
                $pedido->fecha_solicitud
                && $pedido->fecha_terminado
            ) {

                $pedido->dias_diferencia =
                    $pedido->fecha_solicitud
                        ->diffInDays(
                            $pedido->fecha_terminado
                        );

            } else {

                $pedido->dias_diferencia = null;
            }
        });


        /*
         * ================================================================
         * VISTA
         * ================================================================
         */
        return view(
            'pedidos-terminados.index',
            compact('pedidosTerminados')
        );
    }
}