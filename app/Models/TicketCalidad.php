<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TicketCalidad extends Model
{
    protected $table = 'tickets_calidad';

    protected $fillable = [
        'usuario_id',
        'ticket_codigo',
        'pedido_id',
        'partida_id',
        'nombre',
        'correo',
        'prioridad',
        'tipo',
        'subtipo',
        'problema',
        'cantidad_afectada',
        'estado',
        'fecha_cierre',
    ];

    protected $casts = [
        'cantidad_afectada' => 'decimal:4',
        'fecha_cierre' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELACIONES
    |--------------------------------------------------------------------------
    */

    /**
     * Usuario que creó el ticket.
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'usuario_id'
        );
    }

    /**
     * Pedido al que pertenece el ticket.
     */
    public function pedido(): BelongsTo
    {
        return $this->belongsTo(
            Pedido::class,
            'pedido_id'
        );
    }

    /**
     * Partida a la que pertenece el ticket.
     *
     * Se mantiene por compatibilidad con la estructura
     * anterior. Para múltiples partidas utilizaremos
     * partidasAfectadas().
     */
    public function partida(): BelongsTo
    {
        return $this->belongsTo(
            Partida::class,
            'partida_id'
        );
    }

    /**
     * Partidas afectadas por el ticket.
     */
    public function partidasAfectadas(): HasMany
    {
        return $this->hasMany(
            TicketCalidadPartida::class,
            'ticket_id'
        );
    }

    /**
     * Archivos/evidencias del ticket.
     */
    public function archivos(): HasMany
    {
        return $this->hasMany(
            TicketCalidadArchivo::class,
            'ticket_id'
        );
    }

    /**
     * Mensajes del ticket.
     */
    public function mensajes(): HasMany
    {
        return $this->hasMany(
            TicketCalidadMensaje::class,
            'ticket_id'
        );
    }
}