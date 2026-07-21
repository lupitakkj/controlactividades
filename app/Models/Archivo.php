<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Archivo extends Model
{
    protected $table = 'archivos';

    protected $fillable = [
        'actividad_id',
        'archivo',
        'nombre_original'
    ];

    public function actividad()
    {
        return $this->belongsTo(Actividad::class);
    }
}
