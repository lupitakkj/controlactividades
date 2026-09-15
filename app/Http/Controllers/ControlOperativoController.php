<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use Illuminate\Http\Request;

class ControlOperativoController extends Controller
{
    /**
     * Mostrar Control Operativo.
     */
    public function index(Request $request)
    {
        $pedidos = Pedido::with('controlOperativo')
            ->orderBy('fecha_entrega', 'asc')
            ->orderBy('pedido_no', 'asc')
            ->get();

        return view(
            'control-operativo.index',
            compact('pedidos')
        );
    }
}