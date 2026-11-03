<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RutaFabricacionEtapa extends Model
{
    protected $table = 'ruta_fabricacion_etapas';

    protected $fillable = ['ruta_id', 'proceso_id', 'orden', 'obligatorio'];

    protected $casts = ['orden' => 'integer', 'obligatorio' => 'boolean'];

    public function ruta(): BelongsTo
    {
        return $this->belongsTo(RutaFabricacion::class, 'ruta_id');
    }

    public function proceso(): BelongsTo
    {
        return $this->belongsTo(Proceso::class, 'proceso_id');
    }
}
