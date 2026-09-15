<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pieza extends Model
{
    protected $table = 'piezas';

    protected $fillable = [
        'largo',
        'ancho',
        'cantidad',
        'material_id',
        'etiqueta',
        'permitir_rotacion',
        'direccion_grano',
        'activo',
    ];

    protected $casts = [
        'largo' => 'decimal:2',
        'ancho' => 'decimal:2',
        'cantidad' => 'integer',
        'permitir_rotacion' => 'boolean',
        'activo' => 'boolean',
    ];

    public function material(): BelongsTo
    {
        return $this->belongsTo(Materia::class);
    }
}