<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SemanaOperativa extends Model
{
    protected $table = 'semanas_operativas';

    protected $fillable = [
        'anio',
        'semana',
        'fecha_inicio',
        'fecha_fin',
        'estado',
    ];

    protected $casts = [
        'anio' => 'integer',
        'semana' => 'integer',
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
    ];
}