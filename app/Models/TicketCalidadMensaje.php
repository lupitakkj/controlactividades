<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketCalidadMensaje extends Model
{
    protected $table = 'ticket_calidad_mensajes';

    protected $fillable = [
        'ticket_id',
        'autor',
        'mensaje',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELACIONES
    |--------------------------------------------------------------------------
    */

    /**
     * Ticket de calidad al que pertenece el mensaje.
     */
    public function ticket(): BelongsTo
    {
        return $this->belongsTo(
            TicketCalidad::class,
            'ticket_id'
        );
    }
}