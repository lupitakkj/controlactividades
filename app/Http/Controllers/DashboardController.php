<?php

namespace App\Http\Controllers;

use App\Models\Actividad;
use App\Models\User;
use App\Models\SesionTiempo;
use App\Models\Bitacora;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Comprobar el horario al consultar el tablero.
        // Lunes a viernes: 19:00 en adelante. Sábado: 14:30 en adelante.
        // Domingo: no se ejecuta la pausa automática.
        $ahora = now();
        $diaSemana = (int) $ahora->dayOfWeekIso; // 1 lunes ... 7 domingo
        $horaActual = $ahora->format('H:i');

        $fueraDeHorario = (
            ($diaSemana >= 1 && $diaSemana <= 5 && $horaActual >= '19:00') ||
            ($diaSemana === 6 && $horaActual >= '14:30')
        );

        if ($fueraDeHorario) {
            $actividadesActivas = Actividad::where('estado', 'en_proceso')->get();

            foreach ($actividadesActivas as $actividadActiva) {
                DB::transaction(function () use ($actividadActiva) {
                    $actividad = Actividad::whereKey($actividadActiva->id)
                        ->lockForUpdate()
                        ->first();

                    // Evita volver a pausar una actividad que cambió de estado.
                    if (!$actividad || $actividad->estado !== 'en_proceso') {
                        return;
                    }

                    $momentoPausa = now();

                    $sesionesAbiertas = SesionTiempo::where('actividad_id', $actividad->id)
                        ->whereNull('fin')
                        ->lockForUpdate()
                        ->get();

                    foreach ($sesionesAbiertas as $sesion) {
                        $sesion->fin = $momentoPausa;
                        $sesion->minutos = max(
                            0,
                            (int) $sesion->inicio->diffInMinutes($momentoPausa)
                        );
                        $sesion->save();
                    }

                    $actividad->estado = 'pausada';
                    $actividad->save();

                    Bitacora::create([
                        'actividad_id' => $actividad->id,
                        'user_id' => $actividad->user_id,
                        'accion' => 'PAUSAR_ACTIVIDAD',
                        'descripcion' => 'Motivo de pausa: Pausa automática por cierre de jornada laboral.',
                    ]);
                });
            }
        }

        $query = Actividad::with([
            'comentarios.user',
            'archivos.user',
            'user'
        ]);

        // Por defecto, mostrar las actividades creadas durante el mes actual.
        // El parámetro mes permite consultar cualquier mes anterior.
        $mesSeleccionado = $request->input('mes', now()->format('Y-m'));

        if (!is_string($mesSeleccionado) ||
            !preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $mesSeleccionado)) {
            $mesSeleccionado = now()->format('Y-m');
        }

        try {
            $inicioMes = \Carbon\Carbon::createFromFormat('!Y-m', $mesSeleccionado)->startOfMonth();
        } catch (\Throwable $e) {
            $mesSeleccionado = now()->format('Y-m');
            $inicioMes = now()->copy()->startOfMonth();
        }

        $finMes = $inicioMes->copy()->endOfMonth();

        // Mostrar las actividades creadas en el mes seleccionado,
        // además de TODAS las actividades que sigan activas de meses anteriores.
        // Así, una actividad pendiente/en proceso/pausada no desaparece al cambiar de mes.
        $query->where(function ($q) use ($inicioMes, $finMes) {
            $q->whereBetween('created_at', [
                $inicioMes->toDateTimeString(),
                $finMes->toDateTimeString()
            ])->orWhereIn('estado', [
                'pendiente',
                'en_proceso',
                'pausada'
            ]);
        });

        // SOLO SUS ACTIVIDADES
        if (auth()->user()->hasRole('Diseñador')) {

            $query->where(
                'user_id',
                auth()->id()
            );
        }

        // ==========================
        // BUSCADOR
        // ==========================

        if ($request->filled('buscar')) {

            $buscar = trim($request->buscar);

            $query->where(function ($q) use ($buscar) {

                $q->where('titulo', 'like', "%{$buscar}%")
                    ->orWhere('descripcion', 'like', "%{$buscar}%")
                    ->orWhere('cliente', 'like', "%{$buscar}%");
            });
        }

        // ==========================
        // FILTRO CLIENTE
        // ==========================

        if ($request->filled('cliente')) {

            $query->where('cliente', $request->cliente);
        }
        // FILTRO DISEÑADOR
        // FILTRO DISEÑADOR


        if ($request->filled('disenador')) {

            $query->where(
                'user_id',
                $request->disenador
            );
        }
        // ==========================
        // FILTRO PRIORIDAD
        // ==========================

        if ($request->filled('prioridad')) {

            $query->where('prioridad', $request->prioridad);
        }


        $pendientes = (clone $query)
            ->where('estado', 'pendiente')
            ->get();

        $proceso = (clone $query)
            ->where('estado', 'en_proceso')
            ->get();

        $pausadas = (clone $query)
            ->where('estado', 'pausada')
            ->get();

        $terminadas = (clone $query)
            ->where('estado', 'terminada')
            ->get();

        $disenadores = User::role('Diseñador')
            ->get();

        $clientes = Actividad::whereNotNull('cliente')
            ->where('cliente', '<>', '')
            ->select('cliente')
            ->distinct()
            ->orderBy('cliente')
            ->pluck('cliente');

        return view('dashboard', compact(
            'pendientes',
            'proceso',
            'pausadas',
            'terminadas',
            'disenadores',
            'clientes',
            'mesSeleccionado'
        ));
    }
}
