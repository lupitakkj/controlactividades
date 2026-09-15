<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DespieceProceso extends Model
{
    protected $table = 'despiece_procesos';

    protected $fillable = [
        'partida_id',
        'proceso_id',
        'aplica',
        'cantidad_realizada',
        'porcentaje',
    ];

    protected $casts = [
        'aplica' => 'boolean',
        'cantidad_realizada' => 'decimal:4',
        'porcentaje' => 'decimal:4',
    ];

    public function partida(): BelongsTo
    {
        return $this->belongsTo(Partida::class, 'partida_id');
    }

    public function proceso(): BelongsTo
    {
        return $this->belongsTo(Proceso::class, 'proceso_id');
    }
}