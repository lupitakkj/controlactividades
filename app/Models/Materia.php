<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Materia extends Model
{
    protected $table = 'materiales';

    protected $fillable = [
        'nombre',
        'espesor',
        'activo',
    ];

    protected $casts = [
        'espesor' => 'decimal:2',
        'activo' => 'boolean',
    ];

    public function hojas(): HasMany
    {
        return $this->hasMany(Hoja::class);
    }

    public function piezas(): HasMany
    {
        return $this->hasMany(Pieza::class);
    }
}