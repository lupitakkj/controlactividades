<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketCalidadArchivo extends Model
{
    protected $table = 'ticket_calidad_archivos';

    protected $fillable = [
        'ticket_id',
        'nombre_archivo',
        'ruta',
        'tipo',
        'tamano',
    ];

    protected $casts = [
        'tamano' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELACIONES
    |--------------------------------------------------------------------------
    */

    /**
     * Ticket de calidad al que pertenece el archivo.
     */
    public function ticket(): BelongsTo
    {
        return $this->belongsTo(
            TicketCalidad::class,
            'ticket_id'
        );
    }
}