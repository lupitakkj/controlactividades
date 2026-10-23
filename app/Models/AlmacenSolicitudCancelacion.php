<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AlmacenSolicitudCancelacion extends Model
{
    protected $table = 'almacen_solicitudes_cancelacion';
    protected $fillable = ['salida_id', 'solicitado_por', 'motivo', 'estado', 'resuelto_por', 'respuesta_compras', 'resuelto_en'];
    protected $casts = ['resuelto_en' => 'datetime'];

    public function salida(): BelongsTo { return $this->belongsTo(AlmacenSalida::class, 'salida_id'); }
    public function solicitante(): BelongsTo { return $this->belongsTo(User::class, 'solicitado_por'); }
    public function resolver(): BelongsTo { return $this->belongsTo(User::class, 'resuelto_por'); }
}
