<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class AlmacenSalida extends Model
{
    protected $table = 'almacen_salidas';

    protected $fillable = [
        'folio', 'pedido_id', 'almacen_id', 'fecha_salida',
        'recibido_por', 'observaciones', 'estado', 'creado_por',
    ];

    protected $casts = ['fecha_salida' => 'datetime'];

    public function pedido(): BelongsTo { return $this->belongsTo(Pedido::class, 'pedido_id'); }
    public function pedidos(): BelongsToMany { return $this->belongsToMany(Pedido::class, 'almacen_salidas_pedidos', 'salida_id', 'pedido_id')->withTimestamps(); }
    public function almacen(): BelongsTo { return $this->belongsTo(Almacen::class, 'almacen_id'); }
    public function creador(): BelongsTo { return $this->belongsTo(User::class, 'creado_por'); }
    public function detalles(): HasMany { return $this->hasMany(AlmacenSalidaDetalle::class, 'salida_id'); }
    public function solicitudesCancelacion(): HasMany { return $this->hasMany(AlmacenSolicitudCancelacion::class, 'salida_id'); }
}
