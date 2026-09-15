<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Importacion extends Model
{
    protected $table = 'importaciones';

    protected $fillable = [
        'nombre_archivo',
        'fecha_importacion',
        'usuario_id',
        'cantidad_pedidos',
        'cantidad_partidas',
        'resultado',
        'observaciones',
    ];

    protected $casts = [
        'fecha_importacion' => 'datetime',
    ];

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}