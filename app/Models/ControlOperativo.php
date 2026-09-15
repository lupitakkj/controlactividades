<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ControlOperativo extends Model
{
    protected $table = 'control_operativo';

    protected $fillable = [
        'pedido_id',
        'prioridad',
        'fecha_produccion',
        'estado_operativo',
        'comentario',
        'responsable_id',
    ];

    protected $casts = [
        'fecha_produccion' => 'date',
    ];

    /**
     * Pedido al que pertenece este control operativo.
     */
    public function pedido(): BelongsTo
    {
        return $this->belongsTo(
            Pedido::class,
            'pedido_id'
        );
    }

    /**
     * Responsable del pedido.
     *
     * Se relaciona con la tabla users.
     */
    public function responsable(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'responsable_id'
        );
    }
}