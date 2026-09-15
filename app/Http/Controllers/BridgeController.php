<?php

namespace App\Http\Controllers;

use App\Models\ConsultaExistencia;
use Illuminate\Http\Request;

class BridgeController extends Controller
{
    public function pendiente(Request $request)
    {
        $this->validarToken($request);
        $consulta = ConsultaExistencia::where('estado', 'pendiente')
            ->orderBy('id', 'asc')
            ->first();

        if (!$consulta) {
            return response()->json([
                'hay_consulta' => false
            ]);
        }

        $consulta->update([
            'estado' => 'procesando'
        ]);

        return response()->json([
            'hay_consulta' => true,
            'id' => $consulta->id,
            'uuid' => $consulta->uuid,
            'clave' => $consulta->clave,
        ]);
    }

    public function resultado(Request $request)
    {
        $this->validarToken($request);
        $request->validate([
            'uuid' => ['required', 'uuid'],
            'estado' => ['required', 'in:completado,error'],
            'descripcion' => ['nullable', 'string'],
            'existencia' => ['nullable', 'numeric'],
            'error' => ['nullable', 'string'],
        ]);

        $consulta = ConsultaExistencia::where(
            'uuid',
            $request->uuid
        )->first();

        if (!$consulta) {
            return response()->json([
                'ok' => false,
                'mensaje' => 'Consulta no encontrada.'
            ], 404);
        }

        $consulta->update([
            'estado' => $request->estado,
            'descripcion' => $request->descripcion,
            'existencia' => $request->existencia,
            'error' => $request->error,
        ]);

        return response()->json([
            'ok' => true
        ]);
    }

    private function validarToken(Request $request): void
    {
        $token = $request->bearerToken();

        $esperado = env('INVENTARIO_BRIDGE_TOKEN');

        if (
            !$token ||
            !$esperado ||
            !hash_equals($esperado, $token)
        ) {
            abort(response()->json([
                'ok' => false,
                'mensaje' => 'No autorizado.'
            ], 401));
        }
    }
}
