<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditoriaEvento extends Model
{
    protected $table = 'auditoria_eventos';

    public $timestamps = false;

    protected $guarded = ['*'];

    protected function casts(): array
    {
        return ['antes' => 'array', 'despues' => 'array', 'created_at' => 'datetime'];
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}
