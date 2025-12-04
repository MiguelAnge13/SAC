<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProyectoImagen extends Model
{
    use HasFactory;

    protected $table = 'proyecto_imagenes';

    protected $fillable = ['proyecto_id','ruta','nombre_original'];

    public function proyecto()
    {
        return $this->belongsTo(Proyecto::class, 'proyecto_id');
    }
}
