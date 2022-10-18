<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Laboratorio_Camaronera extends Model
{
    use HasFactory;
    protected $table='laboratorio_camaronera';
    protected $primaryKey = 'laboratorio_id';
    public $timestamps = true;
    protected $fillable = [     
        'laboratorio_nombre',   
        'laboratorio_Ubicacion',
        'laboratorio_estado',
        'empresa_id',
    ];
    protected $guarded =[
    ];
    public function scopeLaboratorios($query)
    {
        return $query->where('empresa_id', '=', Auth::user()->empresa_id)->where('laboratorio_estado', '=', '1');
    }
    public function scopeLaboratorio($query,$id)
    {
        return $query->where('empresa_id', '=', Auth::user()->empresa_id)->where('laboratorio_id', '=', $id);
    }
}
