<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class SemanaOperativa extends Model
{
    protected $table = 'semanas_operativas';

    protected $fillable = [
        'anio',
        'semana',
        'fecha_inicio',
        'fecha_fin',
        'estado',
    ];

    protected $casts = [
        'anio' => 'integer',
        'semana' => 'integer',
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
    ];

    public static function actual(Carbon $ahora): ?self
    {
        $fecha = $ahora->toDateString();
        $candidata = static::whereDate('fecha_inicio', '<=', $fecha)
            ->latest('fecha_inicio')
            ->first();

        if ($candidata && $ahora->lt($candidata->fecha_inicio->copy()->setTime(18, 30))) {
            $candidata = static::where('fecha_inicio', '<', $candidata->fecha_inicio)
                ->latest('fecha_inicio')
                ->first();
        }

        return $candidata ?? static::where('anio', $ahora->year)
            ->whereDate('fecha_inicio', '<=', $fecha)
            ->whereDate('fecha_fin', '>=', $fecha)
            ->first();
    }
}
