<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Tipo_Empleado_Parametrizacion extends Model
{
    use HasFactory;
    protected $table='tipo_empleado_parametrizacion';
    protected $primaryKey = 'parametrizacion_id';
    public $timestamps = true;
    protected $fillable = [        
        'parametrizacion_estado',              
        'cuenta_debe',  
        'cuenta_haber',
        'tipo_id',  
        'rubro_id', 
        'categoria_id', 
    ];
    protected $guarded =[
    ];   
    public function scopeTipoEmpleadoBusquedaCuenta($query, $id,$rubro){
        return $query->join('tipo_empleado', 'tipo_empleado_parametrizacion.tipo_id','=','tipo_empleado.tipo_id'
                    )->join('rubro', 'rubro.rubro_id','=','tipo_empleado_parametrizacion.rubro_id'
                    )->where('rubro.empresa_id','=',Auth::user()->empresa_id)
                    ->where('tipo_empleado.tipo_id','=',$id)
                    ->where('rubro.rubro_nombre','=',$rubro);
    }
    public function cuentaDebe(){
        return $this->belongsTo(Cuenta::class, 'cuenta_debe', 'cuenta_id');
    }
    public function cuentaHaber(){
        return $this->belongsTo(Cuenta::class, 'cuenta_haber', 'cuenta_id');
    }
    public function rubro(){
        return $this->belongsTo(Rubro::class, 'rubro_id', 'rubro_id');
    }
    public function categoria(){
        return $this->belongsTo(Categoria_Rol::class, 'categoria_id', 'categoria_id');
    }
}
