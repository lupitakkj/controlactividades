<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RutaFabricacionClave extends Model
{
    protected $table = 'rutas_fabricacion_claves';

    protected $fillable = ['clave', 'ruta_fabricacion_id'];

    public function ruta(): BelongsTo
    {
        return $this->belongsTo(RutaFabricacion::class, 'ruta_fabricacion_id');
    }
}
