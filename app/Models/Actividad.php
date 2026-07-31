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
    public function getDiferenciaTiempoTextoAttribute()
    {
        $minutos = $this->tiempo_total - ($this->tiempo_estimado * 60);

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
