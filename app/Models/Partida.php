<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Services\AsignarRutaPorClave;

class Partida extends Model
{
    protected $table = 'partidas';

    /**
     * Al crear una partida importada o capturada, asignar la ruta predeterminada
     * si existe una regla para su clave. No modifica Despiece.
     */
    protected static function booted(): void
    {
        static::created(function (Partida $partida): void {
            app(AsignarRutaPorClave::class)->aplicarPredeterminada($partida);
        });
    }

    protected $fillable = [
        'pedido_id',
        'clave',
        'linea',
        'descripcion',
        'cantidad',
        'precio_unitario',
        'importe_total',
    ];

    public function pedido(): BelongsTo
    {
        return $this->belongsTo(Pedido::class, 'pedido_id');
    }

    /**
     * Procesos de producción de esta partida.
     */
    public function despieceProcesos(): HasMany
    {
        return $this->hasMany(
            DespieceProceso::class,
            'partida_id'
        );
    }

    public function rutaAsignada()
    {
        return $this->hasOne(PartidaRuta::class, 'partida_id');
    }

    public function reglaRutaClave()
    {
        return $this->belongsTo(RutaFabricacionClave::class, 'clave', 'clave');
    }

    /**
     * Tickets de calidad asociados a la partida.
     */
    public function ticketsCalidad(): HasMany
    {
        return $this->hasMany(
            TicketCalidadPartida::class,
            'partida_id'
        );
    }
}
