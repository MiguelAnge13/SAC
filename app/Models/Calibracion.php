<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Calibracion extends Model
{
    protected $table = 'calibraciones';
    protected $fillable = ['user_id','session_id','servo_num','angulo','nota'];
}