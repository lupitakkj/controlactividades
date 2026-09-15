<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConsultaExistencia extends Model
{
    protected $table = 'consultas_existencias';

    protected $fillable = [
        'uuid',
        'clave',
        'estado',
        'descripcion',
        'existencia',
        'error',
    ];

    protected $casts = [
        'existencia' => 'decimal:4',
    ];
}
