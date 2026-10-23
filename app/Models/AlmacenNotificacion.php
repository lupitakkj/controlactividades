<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AlmacenNotificacion extends Model
{
    protected $table = 'almacen_notificaciones';
    protected $fillable = ['user_id', 'tipo', 'titulo', 'mensaje', 'url', 'leida_en'];
    protected $casts = ['leida_en' => 'datetime'];

    public function usuario(): BelongsTo { return $this->belongsTo(User::class, 'user_id'); }
}
