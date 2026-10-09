<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Actividad extends Model
{
    protected $table = 'actividades';

    protected $fillable = [
        'cliente_id',
        'user_id',
        'titulo',
        'descripcion',
        'cliente',
        'prioridad',
        'complejidad',
        'estado',
        'tiempo_estimado',
        'fecha_inicio',
        'fecha_limite',
        'orden'
    ];

    protected $appends = [
        'tiempo_total',
        'tiempo_activo',
        'diferencia_tiempo_texto',
        'avance_subtareas',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function sesiones()
    {
        return $this->hasMany(SesionTiempo::class);
    }

    public function getTiempoTotalAttribute()
    {
        return $this->sesiones()->sum('minutos');
    }

    public function getTiempoActivoAttribute()
    {
        $minutos = $this->sesiones()->sum('minutos');

        $sesionActiva = $this->sesiones()
            ->whereNull('fin')
            ->first();

        if ($sesionActiva) {

            $minutos +=
                $sesionActiva->inicio->diffInMinutes(now());
        }

        return $minutos;
    }

    /**
     * Subtareas que pertenecen a esta actividad.
     */
    public function subtareas()
    {
        return $this->hasMany(Subtarea::class, 'actividad_id');
    }

    public function comentarios()
    {
        return $this->hasMany(Comentario::class)
            ->latest();
    }
    public function archivos()
    {
        return $this->hasMany(Archivo::class);
    }
    /**
     * Calcula el avance que se muestra para la actividad.
     *
     * - Si tiene subtareas: promedio ponderado por tiempo estimado.
     * - Si no tiene subtareas: tiempo activo trabajado / tiempo estimado.
     * - El porcentaje nunca supera el 100%.
     *
     * Nota: el avance por tiempo representa consumo de tiempo, no confirma
     * por sí solo que el trabajo esté terminado.
     */
    public function getAvanceSubtareasAttribute()
    {
        $subtareas = $this->relationLoaded('subtareas')
            ? $this->getRelation('subtareas')
            : $this->subtareas()->get();

        if ($subtareas->isNotEmpty()) {
            $horasTotales = (float) $subtareas->sum('tiempo_estimado');

            if ($horasTotales <= 0) {
                return 0;
            }

            $sumaPonderada = $subtareas->sum(function ($subtarea) {
                return (float) $subtarea->tiempo_estimado
                    * (int) $subtarea->avance;
            });

            return round(min(100, max(0, $sumaPonderada / $horasTotales)), 2);
        }

        // Sin subtareas, el avance se calcula con el tiempo de la actividad.
        $horasEstimadas = (float) $this->tiempo_estimado;

        if ($horasEstimadas <= 0) {
            return 0;
        }

        $minutosTrabajados = (float) $this->tiempo_activo;
        $minutosEstimados = $horasEstimadas * 60;
        $porcentaje = ($minutosTrabajados / $minutosEstimados) * 100;

        return round(min(100, max(0, $porcentaje)), 2);
    }

    public function getDiferenciaTiempoTextoAttribute()
    {
        // Incluye la sesión que sigue activa para mostrar el exceso en tiempo real.
        $minutos = $this->tiempo_activo - ((float) $this->tiempo_estimado * 60);

        $abs = abs($minutos);

        $horas = floor($abs / 60);

        $mins = $abs % 60;

        if ($minutos > 0) {
            return "🔴 {$horas} h {$mins} min de retraso";
        }

        if ($minutos < 0) {
            return "🟢 {$horas} h {$mins} min antes";
        }

        return "🟡 En tiempo";
    }

    public function bitacoras()
    {
        return $this->hasMany(Bitacora::class)
            ->latest();
    }
}