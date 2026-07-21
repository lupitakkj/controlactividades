<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SesionTiempo extends Model
{
    protected $table = 'sesion_tiempos';

    protected $fillable = [
        'actividad_id',
        'inicio',
        'fin',
        'minutos'
    ];

    protected $casts = [
        'inicio' => 'datetime',
        'fin' => 'datetime'
    ];

    public function actividad()
    {
        return $this->belongsTo(Actividad::class);
    }
}
