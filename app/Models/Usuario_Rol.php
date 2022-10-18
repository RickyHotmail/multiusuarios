<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Usuario_Rol extends Model
{
    use HasFactory;
    protected $table='usuario_rol';
    protected $primaryKey = 'usuario_rol_id';
    public $timestamps=true;
    protected $fillable = [
        'user_id', 
        'rol_id', 
    ];
    protected $guarded = [
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
    public function rol()
    {
        return $this->belongsTo(Rol::class, 'rol_id', 'rol_id');
    }
}
