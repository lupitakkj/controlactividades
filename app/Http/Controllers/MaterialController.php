<?php

namespace App\Http\Controllers;

use App\Models\Materia;

class MaterialController extends Controller
{
    public function index()
    {
        $materiales = Materia::where('activo', 1)
            ->orderBy('nombre')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $materiales
        ]);
    }

    public function show($id)
    {
        $material = Materia::find($id);

        if (!$material) {
            return response()->json([
                'success' => false,
                'message' => 'Material no encontrado'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $material
        ]);
    }
}