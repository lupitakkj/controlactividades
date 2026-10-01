<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FamiliaMaterial extends Model
{
    protected $table = 'familias_materiales';

    protected $fillable = [
        'nombre',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    /**
     * Materiales pertenecientes a esta familia.
     */
    public function materiales(): HasMany
    {
        return $this->hasMany(
            CatalogoMaterial::class,
            'familia_material_id'
        );
    }
}