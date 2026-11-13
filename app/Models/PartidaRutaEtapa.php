<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PartidaRutaEtapa extends Model
{
    protected $table = 'partida_ruta_etapas';

    protected $fillable = [
        'partida_ruta_id',
        'proceso_id',
        'nombre_proceso_snapshot',
        'orden',
        'obligatorio',
    ];

    public function asignacion(): BelongsTo
    {
        return $this->belongsTo(PartidaRuta::class, 'partida_ruta_id');
    }

    public function proceso(): BelongsTo
    {
        return $this->belongsTo(Proceso::class, 'proceso_id');
    }
}
