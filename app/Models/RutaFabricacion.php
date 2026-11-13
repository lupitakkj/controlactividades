<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RutaFabricacion extends Model
{
    protected $table = 'rutas_fabricacion';

    protected $fillable = ['nombre', 'descripcion', 'activo'];

    protected $casts = ['activo' => 'boolean'];

    public function etapas(): HasMany
    {
        return $this->hasMany(RutaFabricacionEtapa::class, 'ruta_id')->orderBy('orden');
    }
}
