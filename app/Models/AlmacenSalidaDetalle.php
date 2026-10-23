<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AlmacenSalidaDetalle extends Model
{
    protected $table = 'almacen_salidas_detalle';
    protected $fillable = ['salida_id', 'clave_material', 'descripcion', 'unidad', 'cantidad', 'observaciones'];
    protected $casts = ['cantidad' => 'decimal:3'];

    public function salida(): BelongsTo { return $this->belongsTo(AlmacenSalida::class, 'salida_id'); }
}
