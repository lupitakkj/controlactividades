<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ConsultaExistencia;
use Illuminate\Support\Str;

class ExistenciasController extends Controller
{
    public function consulta(string $clave)
    {
        $clave = trim($clave);

        if ($clave === '') {
            abort(404);
        }

        return view('existencias.consulta', [
            'clave' => $clave,
        ]);
    }

    public function estado(string $uuid)
    {
        $consulta = ConsultaExistencia::where('uuid', $uuid)
            ->firstOrFail();

        return response()->json([
            'estado' => $consulta->estado,
            'clave' => $consulta->clave,
            'descripcion' => $consulta->descripcion,
            'existencia' => $consulta->existencia,
            'error' => $consulta->error,
        ]);
    } 
}
