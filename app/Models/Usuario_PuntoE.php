<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Usuario_PuntoE extends Model
{
    use HasFactory;
    protected $table='usuario_puntoe';
    protected $primaryKey = 'usuarioP_id';
    public $timestamps=true;
    protected $fillable = [
        'usuarioP_estado',
        'user_id', 
        'punto_id', 
    ];
    protected $guarded = [
    ];
}
