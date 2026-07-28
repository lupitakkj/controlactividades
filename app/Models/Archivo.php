<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Archivo extends Model
{
    protected $table = 'archivos';

    protected $fillable = [
        'actividad_id',
        'user_id',
        'archivo',
        'nombre_original'
    ];

    /*
    |--------------------------------------------------------------------------
    | ACTIVIDAD
    |--------------------------------------------------------------------------
    */

    public function actividad()
    {
        return $this->belongsTo(Actividad::class);
    }

    /*
    |--------------------------------------------------------------------------
    | USUARIO QUE SUBIÓ EL ARCHIVO
    |--------------------------------------------------------------------------
    */

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}