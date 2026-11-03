<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PartidaRuta extends Model
{
    protected $table = 'partida_rutas';

    protected $fillable = [
        'partida_id',
        'ruta_fabricacion_id',
        'ruta_nombre_snapshot',
        'asignada_por',
        'origen_asignacion',
    ];

    public function partida(): BelongsTo
    {
        return $this->belongsTo(Partida::class, 'partida_id');
    }

    public function ruta(): BelongsTo
    {
        return $this->belongsTo(RutaFabricacion::class, 'ruta_fabricacion_id');
    }

    public function etapas(): HasMany
    {
        return $this->hasMany(PartidaRutaEtapa::class, 'partida_ruta_id')->orderBy('orden');
    }
}
