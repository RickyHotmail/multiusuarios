<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Categoria_Rol extends Model
{
    use HasFactory;
    protected $table='categoria_rol';
    protected $primaryKey = 'categoria_id';
    public $timestamps = true;
    protected $fillable = [        
        'categoria_nombre',           
        'centro_consumo_id',
        'categoria_estado',         
    ];
    protected $guarded =[
    ];
    public function scopeCategorias($query){
        return $query->join('centro_consumo','centro_consumo.centro_consumo_id','=','categoria_rol.centro_consumo_id')->where('empresa_id','=',Auth::user()->empresa_id);
    }   
    public function scopeCategoria($query, $id){
        return $query->join('centro_consumo','centro_consumo.centro_consumo_id','=','categoria_rol.centro_consumo_id')->where('empresa_id','=',Auth::user()->empresa_id)->where('categoria_id ','=',$id);
    }
    public function centroconsumo(){
        return $this->belongsTo(Centro_Consumo::class, 'centro_consumo_id', 'centro_consumo_id');
    }
}
