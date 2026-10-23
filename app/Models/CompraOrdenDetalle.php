<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompraOrdenDetalle extends Model
{
    protected $table = 'compras_ordenes_detalle';
    protected $fillable = ['orden_id','renglon','clave_material','descripcion','unidad','cantidad_solicitada','precio_unitario','importe','observaciones'];
    protected $casts = ['cantidad_solicitada'=>'decimal:3','precio_unitario'=>'decimal:4','importe'=>'decimal:2'];

    public function orden(): BelongsTo { return $this->belongsTo(CompraOrden::class, 'orden_id'); }
}
