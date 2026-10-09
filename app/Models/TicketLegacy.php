<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketLegacy extends Model
{
    protected $connection = 'ticketsapp';

    protected $table = 'tickets';

    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $casts = [
        'fecha' => 'datetime',
        'primera_respuesta' => 'datetime',
    ];
}