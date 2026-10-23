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

        $periodo = in_array($request->input('periodo', 'mensual'), ['semanal', 'mensual'], true)
            ? $request->input('periodo', 'mensual')
            : 'mensual';
        $fechaReferencia = $request->input('periodo_fecha', now()->format('Y-m-d'));
        // En vista mensual el selector envía YYYY-MM; convertirlo al primer día del mes.
        if ($periodo === 'mensual' && is_string($fechaReferencia) && preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $fechaReferencia)) {
            $fechaReferencia .= '-01';
        }
        try {
            $fechaBase = \Carbon\Carbon::parse($fechaReferencia);
        } catch (\Throwable $e) {
            $fechaBase = now();
            $fechaReferencia = $fechaBase->format('Y-m-d');
        }

        if ($periodo === 'semanal') {
            $inicioPeriodo = $fechaBase->copy()->startOfWeek(\Carbon\Carbon::MONDAY)->startOfDay();
            $finPeriodo = $fechaBase->copy()->endOfWeek(\Carbon\Carbon::SUNDAY)->endOfDay();
            $etiquetaPeriodo = 'Semana del ' . $inicioPeriodo->format('d/m/Y') . ' al ' . $finPeriodo->format('d/m/Y');
        } else {
            $inicioPeriodo = $fechaBase->copy()->startOfMonth()->startOfDay();
            $finPeriodo = $fechaBase->copy()->endOfMonth()->endOfDay();
            $etiquetaPeriodo = $fechaBase->translatedFormat('F Y');
        }

        // Los filtros manuales de fecha tienen prioridad si se especifican.
        $fechaInicio = $request->filled('fecha_inicio') ? $request->fecha_inicio : $inicioPeriodo->toDateString();
        $fechaFin = $request->filled('fecha_fin') ? $request->fecha_fin : $finPeriodo->toDateString();
        $inicioPeriodoAnterior = $periodo === 'semanal'
            ? $fechaBase->copy()->subWeek()->format('Y-m-d')
            : $fechaBase->copy()->subMonth()->format('Y-m-d');
        $inicioPeriodoSiguiente = $periodo === 'semanal'
            ? $fechaBase->copy()->addWeek()->format('Y-m-d')
            : $fechaBase->copy()->addMonth()->format('Y-m-d');

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

        // En vistas semanal/mensual, mantener visibles todas las actividades activas
        // aunque hayan sido creadas antes del periodo seleccionado.
        if (!$request->filled('fecha_inicio') && !$request->filled('fecha_fin')) {
            $query->where(function ($q) use ($inicioPeriodo, $finPeriodo) {
                $q->whereBetween('created_at', [$inicioPeriodo, $finPeriodo])
                  ->orWhereIn('estado', ['pendiente', 'en_proceso', 'pausada']);
            });
        } else {
            if ($fechaInicio) {
                $query->whereDate('created_at', '>=', $fechaInicio);
            }
            if ($fechaFin) {
                $query->whereDate('created_at', '<=', $fechaFin);
            }
        }

        $actividades = $query->latest()->get();


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

        $pendientes = $actividades->where('estado', 'pendiente')->count();
        $actividadesEnProceso = $actividades->where('estado', 'en_proceso')->count();
        $actividadesPausadas = $actividades->where('estado', 'pausada')->count();

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

        // HORAS POR DISEÑADOR: respetar los mismos filtros del reporte.
        $horasPorDisenador = [];

        foreach ($usuarios as $usuario) {
            $actividadesUsuario = $actividades->where('user_id', $usuario->id);

            $horas = round(
                $actividadesUsuario->sum(function ($actividad) {
                    return (float) ($actividad->tiempo_total ?? 0);
                }) / 60,
                2
            );

            // Mostrar únicamente diseñadores con actividades dentro del reporte.
            if ($actividadesUsuario->isNotEmpty()) {
                $horasPorDisenador[] = [
                    'nombre' => $usuario->name,
                    'horas' => $horas,
                ];
            }
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

        // ACTIVIDADES TERMINADAS POR SEMANA: últimas 8 semanas.
        // Se usa fecha_terminado si existe; si no, updated_at como aproximación.
        $inicioSemanaActual = now()->startOfWeek(\Carbon\Carbon::MONDAY)->startOfDay();
        $semanas = collect();

        for ($i = 7; $i >= 0; $i--) {
            $inicio = $inicioSemanaActual->copy()->subWeeks($i);
            $fin = $inicio->copy()->endOfWeek(\Carbon\Carbon::SUNDAY)->endOfDay();

            $semanas->put($inicio->format('Y-m-d'), [
                'label' => $inicio->format('d/m') . ' - ' . $fin->format('d/m'),
                'total' => 0,
            ]);
        }

        foreach ($actividades->where('estado', 'terminada') as $actividadTerminada) {
            $fechaCierre = $actividadTerminada->getAttribute('fecha_terminado')
                ?: $actividadTerminada->updated_at;

            if (!$fechaCierre) {
                continue;
            }

            $fechaCierre = \Carbon\Carbon::parse($fechaCierre);
            $claveSemana = $fechaCierre->copy()->startOfWeek(\Carbon\Carbon::MONDAY)->format('Y-m-d');

            if ($semanas->has($claveSemana)) {
                $semana = $semanas->get($claveSemana);
                $semana['total']++;
                $semanas->put($claveSemana, $semana);
            }
        }

        $labelsSemanas = $semanas->pluck('label')->values();
        $datosTerminadasSemana = $semanas->pluck('total')->values();

        $porcentajeTerminadas = $totalActividades > 0
            ? round(($terminadas / $totalActividades) * 100, 1)
            : 0;

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
            'labelsSemanas',
            'datosTerminadasSemana',
            'actividadesEnProceso',
            'actividadesPausadas',
            'porcentajeTerminadas',
            'periodo',
            'fechaReferencia',
            'fechaInicio',
            'fechaFin',
            'etiquetaPeriodo',
            'inicioPeriodoAnterior',
            'inicioPeriodoSiguiente',
        ));
    }
}
