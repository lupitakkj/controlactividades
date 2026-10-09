<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subtarea extends Model
{
    protected $table = 'subtareas';

    protected $fillable = [
        'actividad_id',
        'titulo',
        'descripcion',
        'user_id',
        'tiempo_estimado',
        'avance',
    ];

    protected $casts = [
        'actividad_id' => 'integer',
        'user_id' => 'integer',
        'tiempo_estimado' => 'decimal:2',
        'avance' => 'integer',
    ];

    /**
     * Actividad principal a la que pertenece.
     */
    public function actividad()
    {
        return $this->belongsTo(Actividad::class, 'actividad_id');
    }

    /**
     * Usuario responsable de esta subtarea.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
