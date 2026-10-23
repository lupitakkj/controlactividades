<?php

namespace App\Support;

use App\Models\Actividad;
use App\Models\SesionTiempo;
use App\Models\Bitacora;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PausarActividadesFueraHorario
{
    public static function ejecutar(): int
    {
        $ahora = now('America/Mexico_City');
        $dia = $ahora->dayOfWeekIso;
        $hora = $ahora->format('H:i');

        // Domingo: no realizar pausas automáticas.
        if ($dia === 7) {
            return 0;
        }

        // Lunes a viernes: 19:00.
        if ($dia >= 1 && $dia <= 5 && $hora < '19:00') {
            return 0;
        }

        // Sábado: 14:30.
        if ($dia === 6 && $hora < '14:30') {
            return 0;
        }

        $pausadas = 0;

        DB::transaction(function () use ($ahora, &$pausadas) {
            $actividades = Actividad::where(
                'estado',
                'en_proceso'
            )->lockForUpdate()->get();

            foreach ($actividades as $actividad) {
                $sesion = SesionTiempo::where(
                    'actividad_id',
                    $actividad->id
                )
                    ->whereNull('fin')
                    ->lockForUpdate()
                    ->first();

                // No cambiar una actividad si no tiene
                // una sesión de tiempo abierta.
                if (!$sesion) {
                    continue;
                }

                $sesion->fin = $ahora;
                $sesion->minutos = max(
                    0,
                    (int) $sesion->inicio->diffInMinutes($ahora)
                );
                $sesion->save();

                $actividad->estado = 'pausada';
                $actividad->save();

                Bitacora::create([
                    'actividad_id' => $actividad->id,
                    'user_id' => Auth::id()
                        ?? $actividad->user_id,
                    'accion' => 'PAUSAR_ACTIVIDAD',
                    'descripcion' =>
                        'Motivo de pausa: cierre automático de jornada.',
                ]);

                $pausadas++;
            }
        });

        return $pausadas;
    }
}
