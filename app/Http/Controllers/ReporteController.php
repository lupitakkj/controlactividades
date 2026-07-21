<?php

namespace App\Http\Controllers;

use App\Models\Actividad;
use App\Models\Cliente;
use App\Models\User;
use Illuminate\Http\Request;

class ReporteController extends Controller
{
    public function index(Request $request)
    {
        // SOLO ADMIN Y SUPERVISOR

        if (!auth()->user()->hasAnyRole([
            'Administrador',
            'Supervisor'
        ])) {
            abort(403);
        }

        /*
        |------------------------------------------------------------------
        | FILTROS
        |------------------------------------------------------------------
        */

        $tipo = $request->tipo ?? 'general';

        $userId = $request->user_id;

        $actividadId = $request->actividad_id;

        $clienteId = $request->cliente_id;

        $fechaInicio = $request->fecha_inicio;

        $fechaFin = $request->fecha_fin;

        /*
        |------------------------------------------------------------------
        | QUERY BASE
        |------------------------------------------------------------------
        */

        $query = Actividad::with([
            'user',
            'cliente',
            'sesiones',
            'comentarios',
            'archivos'
        ]);

        // FILTRO DISEÑADOR

        if ($userId) {
            $query->where('user_id', $userId);
        }

        // FILTRO ACTIVIDAD

        if ($actividadId) {
            $query->where('id', $actividadId);
        }

        // FILTRO CLIENTE

        if ($clienteId) {
            $query->where('cliente_id', $clienteId);
        }

        // FILTRO FECHA

        if ($fechaInicio) {
            $query->whereDate(
                'created_at',
                '>=',
                $fechaInicio
            );
        }

        if ($fechaFin) {
            $query->whereDate(
                'created_at',
                '<=',
                $fechaFin
            );
        }
        $actividades = $query
            ->latest()
            ->get();


        /*
        |------------------------------------------------------------------
        | ESTADÍSTICAS
        |------------------------------------------------------------------
        */

        $totalActividades =
            $actividades->count();

        $totalHoras =
            round(
                $actividades->sum(function ($actividad) {
                    return $actividad->tiempo_total;
                }) / 60,
                2
            );

        $terminadas =
            $actividades
            ->where('estado', 'terminada')
            ->count();

        $pendientes =
            $actividades
            ->where('estado', '!=', 'terminada')
            ->count();

        $estimadoTotal =
            $actividades->sum('tiempo_estimado');

        $realTotal =
            round(
                $actividades->sum(function ($actividad) {
                    return $actividad->tiempo_total;
                }) / 60,
                2
            );

        $diferencia =
            round(
                $realTotal - $estimadoTotal,
                2
            );

        /*
        |------------------------------------------------------------------
        | DATOS FILTROS
        |------------------------------------------------------------------
        */

        $usuarios = User::role('disenador')
            ->get();

        $clientes = Cliente::all();

        $todasActividades = Actividad::all();

        /*
|--------------------------------------------------------------------------
| HORAS POR DISEÑADOR
|--------------------------------------------------------------------------
*/

        $horasPorDisenador = [];

        foreach ($usuarios as $usuario) {

            $horas =
                round(
                    $usuario->actividades
                        ->sum(function ($actividad) {

                            return $actividad->tiempo_total;
                        }) / 60,
                    2
                );

            $horasPorDisenador[] = [

                'nombre' => $usuario->name,

                'horas' => $horas
            ];
        }
        $labelsHoras =
            collect($horasPorDisenador)
            ->pluck('nombre');

        $datosHoras =
            collect($horasPorDisenador)
            ->pluck('horas');
        /*
|--------------------------------------------------------------------------
| ESTADOS
|--------------------------------------------------------------------------
*/

        $estados = [

            'pendiente' =>
            $actividades->where('estado', 'pendiente')->count(),

            'en_proceso' =>
            $actividades->where('estado', 'en_proceso')->count(),

            'pausada' =>
            $actividades->where('estado', 'pausada')->count(),

            'terminada' =>
            $actividades->where('estado', 'terminada')->count(),
        ];

        $estadoData = [

            $estados['pendiente'],
            $estados['en_proceso'],
            $estados['pausada'],
            $estados['terminada']

        ];

        $estimadoData = [

            $estimadoTotal,
            $realTotal

        ];

        $terminadasData = [

            $terminadas,
            $pendientes

        ];

        return view('reportes.index', compact(
            'actividades',
            'usuarios',
            'clientes',
            'todasActividades',
            'totalActividades',
            'totalHoras',
            'terminadas',
            'pendientes',
            'estimadoTotal',
            'realTotal',
            'diferencia',
            'tipo',
            'horasPorDisenador',
            'estados',
            'labelsHoras',
            'datosHoras',
            'estadoData',
            'estimadoData',
            'terminadasData',
        ));
    }
}
