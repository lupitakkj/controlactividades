<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Almacen extends Model
{
    protected $table = 'almacenes';

    protected $fillable = [
        'clave', 'nombre', 'ubicacion', 'responsable', 'telefono',
        'observaciones', 'activo', 'creado_por', 'actualizado_por',
    ];

    protected $casts = ['activo' => 'boolean'];

    public function recepciones(): HasMany
    {
        return $this->hasMany(CompraRecepcion::class, 'almacen_id');
    }

    public function creador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creado_por');
    }

    public function actualizador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actualizado_por');
    }
}
