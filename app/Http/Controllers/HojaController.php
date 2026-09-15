<?php

namespace App\Http\Controllers;

use App\Models\Hoja;
use Illuminate\Http\Request;

class HojaController extends Controller
{
    public function index()
    {
        $hojas = Hoja::with('material')
            ->where('cantidad', '>', 0)
            ->orderBy('id')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $hojas
        ]);
    }

    public function show($id)
    {
        $hoja = Hoja::with('material')->find($id);

        if (!$hoja) {
            return response()->json([
                'success' => false,
                'message' => 'Hoja no encontrada'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $hoja
        ]);
    }

    public function store(Request $request)
    {
        $datos = $request->validate([
            'material_id' => 'required|exists:materiales,id',
            'largo' => 'required|numeric|min:0.01',
            'ancho' => 'required|numeric|min:0.01',
            'cantidad' => 'required|integer|min:1',
            'etiqueta' => 'nullable|string|max:150',
        ]);

        $hoja = Hoja::create($datos);

        $hoja->load('material');

        return response()->json([
            'success' => true,
            'message' => 'Hoja creada correctamente',
            'data' => $hoja
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $hoja = Hoja::find($id);

        if (!$hoja) {
            return response()->json([
                'success' => false,
                'message' => 'Hoja no encontrada'
            ], 404);
        }

        $datos = $request->validate([
            'material_id' => 'required|exists:materiales,id',
            'largo' => 'required|numeric|min:0.01',
            'ancho' => 'required|numeric|min:0.01',
            'cantidad' => 'required|integer|min:1',
            'etiqueta' => 'nullable|string|max:150',
        ]);

        $hoja->update($datos);

        $hoja->load('material');

        return response()->json([
            'success' => true,
            'message' => 'Hoja actualizada correctamente',
            'data' => $hoja
        ]);
    }

    public function destroy($id)
    {
        $hoja = Hoja::find($id);

        if (!$hoja) {
            return response()->json([
                'success' => false,
                'message' => 'Hoja no encontrada'
            ], 404);
        }

        $hoja->update([
            'cantidad' => 0
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Hoja eliminada correctamente'
        ]);
    }
}