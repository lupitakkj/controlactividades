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
     * IMPORTANTE:
     *
     * - No elimina información.
     * - No modifica partidas.
     * - No modifica procesos.
     * - No depende todavía de Control Operativo.
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
         * es exactamente 100%.
         */
        $pedidosTerminados =
            $pedidos->filter(function ($pedido) {

                return $pedido->avance !== null
                    && $pedido->avance >= 1;
            })
            ->values();


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