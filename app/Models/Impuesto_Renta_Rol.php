<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Impuesto_Renta_Rol extends Model
{
    use HasFactory;
    protected $table='impuesto_renta_rol';
    protected $primaryKey = 'impuestos_id';
    public $timestamps=true;
    protected $fillable = [
        'impuesto_fraccion_basica',
        'impuesto_exceso_hasta',
        'impuesto_fraccion_excede', 
        'impuesto_sobre_fraccion',
        'impuesto_estado',
        'empresa_id',        
    ];
    protected $guarded =[
    ];
    public function scopeRoles($query){
        return $query->where('empresa_id','=',Auth::user()->empresa_id)->where('impuesto_estado','=','1');
    }
    public function scopeRol($query,$id){
        return $query->where('empresa_id','=',Auth::user()->empresa_id)->where('impuestos_id','=',$id);
    }
    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'empresa_id', 'empresa_id');
    }
}
