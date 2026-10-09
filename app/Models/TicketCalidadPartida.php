<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketCalidadPartida extends Model
{
    protected $table = 'ticket_calidad_partidas';

    protected $fillable = [
        'ticket_id',
        'partida_id',
        'cantidad_afectada',
    ];

    protected $casts = [
        'cantidad_afectada' => 'decimal:4',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Ticket de calidad al que pertenece.
     */
    public function ticket(): BelongsTo
    {
        return $this->belongsTo(
            TicketCalidad::class,
            'ticket_id'
        );
    }

    /**
     * Partida afectada.
     */
    public function partida(): BelongsTo
    {
        return $this->belongsTo(
            Partida::class,
            'partida_id'
        );
    }
}