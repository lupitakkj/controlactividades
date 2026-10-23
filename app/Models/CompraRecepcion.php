<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CompraRecepcion extends Model
{
    protected $table = 'compras_recepciones';

    protected $fillable = [
        'orden_id', 'almacen_id', 'fecha_recepcion', 'recibido_por', 'observaciones',
        'evidencia_path', 'evidencia_nombre_original', 'evidencia_mime', 'evidencia_bytes',
    ];

    protected $casts = ['fecha_recepcion' => 'datetime'];

    public function orden(): BelongsTo
    {
        return $this->belongsTo(CompraOrden::class, 'orden_id');
    }

    public function almacen(): BelongsTo
    {
        return $this->belongsTo(Almacen::class, 'almacen_id');
    }

    public function recibidoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recibido_por');
    }

    public function detalles(): HasMany
    {
        return $this->hasMany(CompraRecepcionDetalle::class, 'recepcion_id');
    }
}
