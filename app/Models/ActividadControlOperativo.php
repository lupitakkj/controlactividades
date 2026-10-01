<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActividadControlOperativo extends Model
{
    protected $table = 'actividades_control_operativo';

    protected $fillable = [
        'pedido_id',
        'actividad',
        'completada',
        'activa',
        'responsable',
        'fecha_limite',
    ];

    protected $casts = [
        'completada' => 'boolean',
        'activa' => 'boolean',
        'fecha_limite' => 'date',
    ];

    public function pedido(): BelongsTo
    {
        return $this->belongsTo(Pedido::class, 'pedido_id');
    }
}
