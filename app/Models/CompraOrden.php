<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CompraOrden extends Model
{
    protected $table = 'compras_ordenes';

    protected $fillable = [
        'folio','proveedor_id','fecha_orden','moneda','subtotal','iva','total','estado',
        'pdf_path','observaciones','creado_por','actualizado_por',
        'fecha_cierre_faltantes','cierre_faltantes_por','motivo_cierre_faltantes',
    ];

    protected $casts = [
        'fecha_orden' => 'date',
        'fecha_cierre_faltantes' => 'datetime',
        'subtotal' => 'decimal:2',
        'iva' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function proveedor(): BelongsTo { return $this->belongsTo(Proveedor::class, 'proveedor_id'); }
    public function detalles(): HasMany { return $this->hasMany(CompraOrdenDetalle::class, 'orden_id')->orderBy('renglon'); }
    public function creador(): BelongsTo { return $this->belongsTo(User::class, 'creado_por'); }
    public function recepciones(): HasMany { return $this->hasMany(CompraRecepcion::class, 'orden_id')->latest('fecha_recepcion'); }
    public function autorizadorCierre(): BelongsTo { return $this->belongsTo(User::class, 'cierre_faltantes_por'); }
}
