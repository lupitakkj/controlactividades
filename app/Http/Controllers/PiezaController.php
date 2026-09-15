<?php

namespace App\Http\Controllers;

use App\Models\Pieza;
use Illuminate\Http\Request;

class PiezaController extends Controller
{
    public function index()
    {
        $piezas = Pieza::with('material')
            ->where('activo', 1)
            ->orderBy('id')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $piezas
        ]);
    }

    public function show($id)
    {
        $pieza = Pieza::with('material')->find($id);

        if (!$pieza) {
            return response()->json([
                'success' => false,
                'message' => 'Pieza no encontrada'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $pieza
        ]);
    }

    public function store(Request $request)
    {
        $datos = $request->validate([
            'largo' => 'required|numeric|min:0.01',
            'ancho' => 'required|numeric|min:0.01',
            'cantidad' => 'required|integer|min:1',
            'material_id' => 'required|exists:materiales,id',
            'etiqueta' => 'nullable|string|max:150',
            'permitir_rotacion' => 'boolean',
            'direccion_grano' => 'required|in:horizontal,vertical,indiferente',
        ]);

        $pieza = Pieza::create($datos);

        $pieza->load('material');

        return response()->json([
            'success' => true,
            'message' => 'Pieza creada correctamente',
            'data' => $pieza
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $pieza = Pieza::find($id);

        if (!$pieza) {
            return response()->json([
                'success' => false,
                'message' => 'Pieza no encontrada'
            ], 404);
        }

        $datos = $request->validate([
            'largo' => 'required|numeric|min:0.01',
            'ancho' => 'required|numeric|min:0.01',
            'cantidad' => 'required|integer|min:1',
            'material_id' => 'required|exists:materiales,id',
            'etiqueta' => 'nullable|string|max:150',
            'permitir_rotacion' => 'boolean',
            'direccion_grano' => 'required|in:horizontal,vertical,indiferente',
        ]);

        $pieza->update($datos);

        $pieza->load('material');

        return response()->json([
            'success' => true,
            'message' => 'Pieza actualizada correctamente',
            'data' => $pieza
        ]);
    }

    public function destroy($id)
    {
        $pieza = Pieza::find($id);

        if (!$pieza) {
            return response()->json([
                'success' => false,
                'message' => 'Pieza no encontrada'
            ], 404);
        }

        $pieza->update([
            'activo' => 0
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pieza eliminada correctamente'
        ]);
    }
}
