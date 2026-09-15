<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Calendario;

class AgendaController extends Controller
{
    public function index()
    {
        return view('agenda.index');
    }

    public function eventos()
    {
        $eventos = Calendario::all();

        return response()->json(
            $eventos->map(function ($evento) {

                return [
                    'id' => $evento->id,

                    'title' => $evento->titulo,

                    'start' => $evento->fecha_inicio,

                    'end' => $evento->fecha_fin,

                    'extendedProps' => [

                        'descripcion' => $evento->descripcion,

                        'tipo' => $evento->tipo,

                        'recurso_id' => $evento->recurso_id,

                        'personas' => $evento->personas,

                        'organizador_id' => $evento->organizador_id,

                        'empresa_visitante' => $evento->empresa_visitante,

                        'contacto_visitante' => $evento->contacto_visitante,

                        'observaciones' => $evento->observaciones,

                        'color' => $evento->color,

                    ],
                ];
            })
        );
    }
    public function store(Request $request)
    {
        try {

            $request->validate([
                'titulo' => 'required|string|max:255',
                'tipo' => 'required|string|max:100',
                'recurso_id' => 'required|integer',
                'fecha' => 'required|date',
                'hora_inicio' => 'required',
                'hora_fin' => 'required',
                'personas' => 'nullable|string|max:255',
                'descripcion' => 'nullable|string',
            ]);

            $fechaInicio = $request->fecha . ' ' . $request->hora_inicio;
            $fechaFin = $request->fecha . ' ' . $request->hora_fin;

            Calendario::create([
                'titulo' => $request->titulo,
                'descripcion' => $request->descripcion,
                'tipo' => $request->tipo,
                'recurso_id' => $request->recurso_id,
                'fecha_inicio' => $fechaInicio,
                'fecha_fin' => $fechaFin,

                'organizador_id' => auth()->id(),

                'creado_por' => auth()->id(),
            ]);

            return redirect()
                ->route('agenda.index')
                ->with('success', 'Reservación creada correctamente.');
        } catch (\Throwable $e) {

            dd([
                'error' => $e->getMessage(),
                'archivo' => $e->getFile(),
                'linea' => $e->getLine(),
            ]);
        }
    }
}
