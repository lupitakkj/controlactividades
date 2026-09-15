<?php

namespace App\Http\Controllers;

use App\Models\Pieza;
use App\Models\Hoja;
use Illuminate\Http\Request;
use App\Services\CutListOptimizer;

class CutListController extends Controller
{
    public function optimizar(
    Request $request,
    CutListOptimizer $optimizer
) {
    $datos = $request->validate([
        'kerf' => 'required|numeric|min:0',

        'separar_materiales' =>
            'required|boolean',

        'considerar_grano' =>
            'required|boolean',

        'permitir_rotacion' =>
            'required|boolean',
    ]);


    // ==========================================
    // OBTENER PIEZAS
    // ==========================================

    $piezas = Pieza::with('material')
        ->where('activo', 1)
        ->orderBy('id')
        ->get();


    // ==========================================
    // OBTENER HOJAS
    // ==========================================

    $hojas = Hoja::with('material')
        ->where('cantidad', '>', 0)
        ->orderBy('id')
        ->get();


    // ==========================================
    // EJECUTAR MOTOR
    // ==========================================

    $resultado = $optimizer->optimizar(
        $piezas,
        $hojas,
        $datos
    );


    return response()->json([
        'success' => true,

        'message' =>
            'Motor de optimización ejecutado.',

        'resultado' => $resultado,
    ]);
}
}