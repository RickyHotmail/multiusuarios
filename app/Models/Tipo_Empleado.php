<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Tipo_Empleado extends Model
{
    use HasFactory;
    protected $table='tipo_empleado';
    protected $primaryKey = 'tipo_id';
    public $timestamps = true;
    protected $fillable = [        
        'tipo_descripcion', 
        'tipo_categoria',
        'tipo_estado',              
        'empresa_id',  
        'sucursal_id',        
    ];
    protected $guarded =[
    ];   
    public function scopeTipos($query){
        return $query->where('empresa_id','=',Auth::user()->empresa_id)->where('tipo_estado','=','1')->orderBy('tipo_descripcion','asc');
    }
    public function scopeTipoEmpleado($query, $id){
        return $query->where('empresa_id','=',Auth::user()->empresa_id)->where('tipo_id','=',$id);
    }
    public function scopeTipoEmpleadoBusquedaCuenta($query, $id,$rubro){
        return $query->join('tipo_empleado_parametrizacion', 'tipo_empleado_parametrizacion.tipo_id','=','tipo_empleado.tipo_id'
                    )->join('rubro', 'rubro.rubro_id','=','tipo_empleado_parametrizacion.rubro_id'
                    )->where('tipo_empleado.empresa_id','=',Auth::user()->empresa_id)->where('tipo_estado','=','1')
                    ->where('tipo_empleado.tipo_id','=',$id)
                    ->where('rubro.rubro_nombre','=',$rubro);
    } 
    public function detalles(){
        return $this->hasMany(Tipo_Empleado_Parametrizacion::class, 'tipo_id', 'tipo_id');
    }
    public function empresa(){
        return $this->belongsTo(Empresa::class, 'empresa_id', 'empresa_id');
    }
    public function sucursal(){
        return $this->belongsTo(Sucursal::class, 'sucursal_id', 'sucursal_id');
    }
}
