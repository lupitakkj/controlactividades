<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Proveedor extends Model
{
    protected $table = 'proveedores';

    protected $fillable = [
        'clave', 'razon_social', 'rfc', 'contacto', 'telefono', 'correo',
        'direccion', 'observaciones', 'activo', 'creado_por', 'actualizado_por',
    ];

    protected $casts = ['activo' => 'boolean'];

    public function creador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creado_por');
    }

    public function actualizador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actualizado_por');
    }
}
