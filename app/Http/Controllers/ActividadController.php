<?php

namespace App\Http\Controllers;

use App\Models\Actividad;
use Illuminate\Http\Request;
use App\Models\SesionTiempo;
use App\Models\Comentario;
use App\Models\Archivo;
use Illuminate\Support\Facades\Storage;
use App\Models\Bitacora;
use Illuminate\Support\Facades\Auth;

class ActividadController extends Controller
{
    public function store(Request $request)
    {

        $request->validate([

            'titulo' => 'required',

            'prioridad' => 'required',

            'complejidad' => 'required|integer|between:1,3',

            'fecha_limite' => 'nullable|date'
        ]);

        /*
            |--------------------------------------------------------------------------
            | SI ES ADMIN O SUPERVISOR
            |--------------------------------------------------------------------------
            */

        if (auth()->user()->hasAnyRole([
            'Administrador',
            'Supervisor'
        ])) {

            $userId = $request->user_id;
        }

        /*
        |--------------------------------------------------------------------------
        | SI ES DISEÑADOR
        |--------------------------------------------------------------------------
        */ else {

            $userId = auth()->user()->id;
        }

        switch ($request->complejidad) {

            case 1:
                $tiempoEstimado = 4;
                break;

            case 2:
                $tiempoEstimado = 16;
                break;

            case 3:
                $tiempoEstimado = 48;
                break;

            default:
                $tiempoEstimado = 4;
        }
        $ultimoOrden = Actividad::where('estado', 'pendiente')
            ->max('orden');
        $actividad = Actividad::create([

            'titulo' => $request->titulo,

            'descripcion' => $request->descripcion,

            'cliente' => $request->cliente,

            'user_id' => $userId,

            'estado' => 'pendiente',

            'prioridad' => $request->prioridad,

            'complejidad' => $request->complejidad,

            'tiempo_estimado' => $tiempoEstimado,

            'fecha_limite' => $request->fecha_limite,

            'orden' => $ultimoOrden + 1,
        ]);

        if ($request->hasFile('archivos')) {

            foreach ($request->file('archivos') as $file) {

                $ruta = $file->store(
                    'archivos',
                    'public'
                );

                Archivo::create([

                    'actividad_id' => $actividad->id,

                    'user_id' => auth()->id(),

                    'archivo' => $ruta,

                    'nombre_original' => $file->getClientOriginalName()

                ]);
            }
        }
        $this->registrarBitacora(
            $actividad->id,
            'CREAR_ACTIVIDAD',
            'Se creó la actividad "' . $actividad->titulo . '"'
        );
        return back();
    }

    public function iniciar($id)
    {
        $actividad = Actividad::findOrFail($id);

        // Pausar cualquier actividad activa del usuario

        $activas = Actividad::where('user_id', auth()->user()->id)
            ->where('estado', 'en_proceso')
            ->get();

        foreach ($activas as $activa) {

            $sesion = SesionTiempo::where('actividad_id', $activa->id)
                ->whereNull('fin')
                ->first();

            if ($sesion) {

                $sesion->fin = now();

                $sesion->minutos =
                    $sesion->inicio->diffInMinutes($sesion->fin);

                $sesion->save();
            }

            $activa->estado = 'pausada';

            $activa->save();
        }

        // Guardar la fecha de inicio solamente la primera vez

        if (is_null($actividad->fecha_inicio)) {

            $actividad->fecha_inicio = now();
        }

        // Crear nueva sesión

        SesionTiempo::create([

            'actividad_id' => $actividad->id,

            'inicio' => now()

        ]);

        $actividad->estado = 'en_proceso';

        $actividad->save();

        return back();
    }

    public function pausar($id)
    {
        $actividad = Actividad::findOrFail($id);

        $sesion = SesionTiempo::where('actividad_id', $id)
            ->whereNull('fin')
            ->first();

        if ($sesion) {

            $sesion->fin = now();

            $sesion->minutos =
                $sesion->inicio->diffInMinutes($sesion->fin);

            $sesion->save();
        }

        $actividad->estado = 'pausada';

        $actividad->save();

        return back();
    }

    public function terminar($id)
    {
        $actividad = Actividad::findOrFail($id);

        $sesion = SesionTiempo::where('actividad_id', $id)
            ->whereNull('fin')
            ->first();

        if ($sesion) {

            $sesion->fin = now();

            $sesion->minutos =
                $sesion->inicio->diffInMinutes($sesion->fin);

            $sesion->save();
        }

        $actividad->estado = 'terminada';

        $actividad->save();

        return back();
    }
    public function mover(Request $request, $id)
    {
        $actividad = Actividad::findOrFail($id);

        $nuevoEstado = $request->estado;

        /*
        |--------------------------------------------------------------------------
        | SI SE MUEVE A EN PROCESO
        |--------------------------------------------------------------------------
        */

        if ($nuevoEstado == 'en_proceso') {

            // Pausar otras actividades activas

            $activas = Actividad::where('user_id', auth()->user()->id)
                ->where('estado', 'en_proceso')
                ->where('id', '!=', $actividad->id)
                ->get();

            foreach ($activas as $activa) {

                $sesion = SesionTiempo::where('actividad_id', $activa->id)
                    ->whereNull('fin')
                    ->first();

                if ($sesion) {

                    $sesion->fin = now();

                    $sesion->minutos =
                        $sesion->inicio->diffInMinutes($sesion->fin);

                    $sesion->save();
                }

                $activa->estado = 'pausada';

                $activa->save();
            }

            // Crear nueva sesión si no existe activa

            $sesionActiva = SesionTiempo::where('actividad_id', $actividad->id)
                ->whereNull('fin')
                ->first();

            if (!$sesionActiva) {

                SesionTiempo::create([

                    'actividad_id' => $actividad->id,

                    'inicio' => now()
                ]);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | SI SE MUEVE A PAUSADA O TERMINADA
        |--------------------------------------------------------------------------
        */

        if (
            $nuevoEstado == 'pausada' ||
            $nuevoEstado == 'terminada'
        ) {

            $sesion = SesionTiempo::where('actividad_id', $actividad->id)
                ->whereNull('fin')
                ->first();

            if ($sesion) {

                $sesion->fin = now();

                $sesion->minutos =
                    $sesion->inicio->diffInMinutes($sesion->fin);

                $sesion->save();
            }
        }

        // Actualizar estado

        $actividad->estado = $nuevoEstado;

        $actividad->save();

        return response()->json([
            'success' => true
        ]);
    }
    public function comentar(Request $request, $id)
    {
        Comentario::create([

            'actividad_id' => $id,

            'user_id' => auth()->user()->id,

            'comentario' => $request->comentario
        ]);

        return back();
    }
    public function subirArchivo(Request $request, $id)
    {
        $request->validate([
            'archivo' => 'required|file|max:20480'
        ]);

        $file = $request->file('archivo');

        $ruta = $file->store('actividades', 'public');

        Archivo::create([

            'actividad_id' => $id,

            'user_id' => auth()->id(),

            'archivo' => $ruta,

            'nombre_original' => $file->getClientOriginalName()

        ]);

        return back();
    }
    public function update(Request $request, Actividad $actividad)
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'prioridad' => 'required'
        ]);

        $actividad->update([
            'titulo' => $request->titulo,
            'descripcion' => $request->descripcion,
            'prioridad' => $request->prioridad,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Actividad actualizada correctamente.'
        ]);
    }

    public function reasignar(Request $request, $id)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);

        $actividad = Actividad::findOrFail($id);

        $actividad->user_id = $request->user_id;

        $actividad->save();

        return back()->with(
            'success',
            'Actividad reasignada correctamente.'
        );
    }

    public function descargarArchivo($id)
    {
        $archivo = Archivo::findOrFail($id);

        if (!Storage::disk('public')->exists($archivo->archivo)) {
            abort(404, 'El archivo no existe.');
        }

        return Storage::disk('public')->download(
            $archivo->archivo,
            $archivo->nombre_original
        );
    }

    private function registrarBitacora($actividadId, $accion, $descripcion = null)
    {
        Bitacora::create([
            'actividad_id' => $actividadId,
            'user_id'       => Auth::id(),
            'accion'        => $accion,
            'descripcion'   => $descripcion,
        ]);
    }

    public function guardarOrden(Request $request)
    {
        foreach ($request->columnas as $columna) {

            foreach ($columna['tarjetas'] as $tarjeta) {

                Actividad::where('id', $tarjeta['id'])
                    ->update([
                        'estado' => $columna['estado'],
                        'orden'  => $tarjeta['orden']
                    ]);
            }
        }

        return response()->json([
            'success' => true
        ]);
    }
}
