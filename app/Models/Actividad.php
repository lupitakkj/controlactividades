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
        'prioridad',
        'complejidad',
        'estado',
        'tiempo_estimado',
        'fecha_inicio',
        'fecha_limite'
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

    public function comentarios()
    {
        return $this->hasMany(Comentario::class)
            ->latest();
    }
    public function archivos()
    {
        return $this->hasMany(Archivo::class);
    }
    public function getDiferenciaTiempoAttribute()
    {
        // tiempo real en horas

        $real = $this->tiempo_total / 60;

        // diferencia

        return round(
            $real - $this->tiempo_estimado,
            2
        );
    }
}
