<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Partida extends Model
{
    protected $table = 'partidas';

    protected $fillable = [
        'pedido_id',
        'clave',
        'linea',
        'descripcion',
        'cantidad',
        'precio_unitario',
        'importe_total',
    ];

    public function pedido(): BelongsTo
    {
        return $this->belongsTo(Pedido::class, 'pedido_id');
    }

    /**
     * Procesos de producción de esta partida.
     */
    public function despieceProcesos(): HasMany
    {
        return $this->hasMany(
            DespieceProceso::class,
            'partida_id'
        );
    }
}
