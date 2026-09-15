<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Calendario extends Model
{
    protected $table = 'calendario';

    protected $fillable = [
        'titulo',
        'descripcion',
        'tipo',
        'recurso_id',
        'fecha_inicio',
        'fecha_fin',
        'personas',
        'organizador_id',
        'empresa_visitante',
        'contacto_visitante',
        'observaciones',
        'color',
        'creado_por',
    ];

    protected $casts = [
        'fecha_inicio' => 'datetime',
        'fecha_fin' => 'datetime',
    ];
}