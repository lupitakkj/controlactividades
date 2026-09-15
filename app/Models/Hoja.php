<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Hoja extends Model
{
    protected $table = 'hojas';

    protected $fillable = [
        'material_id',
        'largo',
        'ancho',
        'cantidad',
        'etiqueta',
    ];

    protected $casts = [
        'largo' => 'decimal:2',
        'ancho' => 'decimal:2',
        'cantidad' => 'integer',
    ];

    public function material(): BelongsTo
    {
        return $this->belongsTo(Materia::class);
    }
}