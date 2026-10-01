<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CatalogoMaterial extends Model
{
    protected $table = 'catalogo_materiales';

    protected $fillable = [
        'clave',
        'descripcion',
        'familia_material_id',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    /**
     * Familia a la que pertenece este material.
     */
    public function familia(): BelongsTo
    {
        return $this->belongsTo(
            FamiliaMaterial::class,
            'familia_material_id'
        );
    }
}