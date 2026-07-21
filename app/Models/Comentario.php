<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comentario extends Model
{
    protected $table = 'comentarios';

    protected $fillable = [
        'actividad_id',
        'user_id',
        'comentario'
    ];

    public function actividad()
    {
        return $this->belongsTo(Actividad::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
