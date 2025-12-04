<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Codigo extends Model
{
    use HasFactory;

    protected $table = 'codigos';

    protected $fillable = [
        'titulo',
        'lenguaje',
        'fecha',
        'codigo',
        'user_id'
    ];

    public function usuario()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }
}
