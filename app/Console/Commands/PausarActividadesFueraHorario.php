<?php

namespace App\Console\Commands;

use App\Models\Actividad;
use App\Models\Bitacora;
use App\Models\SesionTiempo;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class PausarActividadesFueraHorario extends Command
{
    /**
     * Revisar cada minuto y pausar actividades que sigan activas
     * después del horario de salida.
     */
    protected $signature = 'actividades:pausar-fuera-horario';

    protected $description = 'Pausa actividades activas después del horario laboral';

    public function handle(): int
    {
        $ahora = now();
        $dia = (int) $ahora->dayOfWeekIso; // 1 lunes ... 7 domingo
        $hora = $ahora->format('H:i');

        $debePausar = (
            ($dia >= 1 && $dia <= 5 && $hora >= '19:00') ||
            ($dia === 6 && $hora >= '14:30')
        );

        if (!$debePausar) {
            return self::SUCCESS;
        }

        $actividades = Actividad::where('estado', 'en_proceso')->get();
        $pausadas = 0;

        foreach ($actividades as $actividad) {
            DB::transaction(function () use ($actividad, &$pausadas) {
                // Volver a verificar dentro de la transacción para evitar
                // pausar una actividad que ya cambió de estado.
                $actual = Actividad::whereKey($actividad->id)
                    ->lockForUpdate()
                    ->first();

                if (!$actual || $actual->estado !== 'en_proceso') {
                    return;
                }

                $sesionesAbiertas = SesionTiempo::where('actividad_id', $actual->id)
                    ->whereNull('fin')
                    ->lockForUpdate()
                    ->get();

                $momentoPausa = now();

                foreach ($sesionesAbiertas as $sesion) {
                    $sesion->fin = $momentoPausa;
                    $sesion->minutos = max(
                        0,
                        (int) $sesion->inicio->diffInMinutes($momentoPausa)
                    );
                    $sesion->save();
                }

                $actual->estado = 'pausada';
                $actual->save();

                // La bitácora utiliza el usuario asignado a la actividad,
                // porque el comando se ejecuta sin una sesión de usuario.
                Bitacora::create([
                    'actividad_id' => $actual->id,
                    'user_id' => $actual->user_id,
                    'accion' => 'PAUSAR_ACTIVIDAD',
                    'descripcion' => 'Motivo de pausa: Pausa automática por cierre de jornada laboral.',
                ]);

                $pausadas++;
            });
        }

        if ($pausadas > 0) {
            $this->info("Actividades pausadas automáticamente: {$pausadas}");
        }

        return self::SUCCESS;
    }
}
