<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Microcontrolador extends Model
{
    protected $table = 'microcontroladores';

    protected $fillable = [
        'serial',
        'vendor_id',
        'product_id',
        'port',
        'modelo',
        'primera_conexion_at',
        'ultima_conexion_at',
        'conectado',
        'notas',
    ];

    protected $casts = [
        'conectado' => 'boolean',
        'primera_conexion_at' => 'datetime',
        'ultima_conexion_at' => 'datetime',
    ];
}
