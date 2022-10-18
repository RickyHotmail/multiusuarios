<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Rol_Movimiento extends Model
{
    use HasFactory;
    protected $table='rol_movimiento';
    protected $primaryKey = 'rol_movimiento_id';
    public $timestamps = true;
    protected $fillable = [     
        'rol_movimiento_mes',
        'rol_movimiento_anio',   
        'rol_movimiento_valor',
        'rol_movimiento_tipo',         
        'rol_movimiento_estado', 
        'empleado_id', 
        'rubro_id', 
        'cabecera_rol_cm_id', 
    ];
    protected $guarded =[
    ];

    public function scopeMovimientos($query){
        return $query->join('rubro','rubro.rubro_id','=','rol_movimiento.rubro_id')->where('rubro.empresa_id','=',Auth::user()->empresa_id)->where('rol_movimiento_estado','=','1');
    }
    public function scopeMovimiento($query, $id){
        return $query->join('rubro','rubro.rubro_id','=','rol_movimiento.rubro_id')->where('rubro.empresa_id','=',Auth::user()->empresa_id)
        ->where('rol_movimiento_estado','=','1')
        ->where('rol_movimiento_id','=',$id);
    }
    public function scopeMovimientoRubro($query,$id,$mes,$anio){
        return $query->join('empleado','empleado.empleado_id','=','rol_movimiento.empleado_id')->join('rubro','rubro.rubro_id','=','rol_movimiento.rubro_id')
        ->where('rubro.empresa_id','=',Auth::user()->empresa_id)
        ->where('rol_movimiento_mes','=',$mes)
        ->where('rol_movimiento_anio','=',$anio)
        ->where('rubro.rubro_id','=',$id);
    }
    public function scopeMovimientoEmpleado($query,$id,$mes,$anio,$tipo){
        return $query->join('empleado','empleado.empleado_id','=','rol_movimiento.empleado_id')->join('rubro','rubro.rubro_id','=','rol_movimiento.rubro_id')
        ->where('rubro.empresa_id','=',Auth::user()->empresa_id)
        ->where('rol_movimiento_estado','=','1')
        ->where('rol_movimiento_mes','=',$mes)
        ->where('rol_movimiento_tipo','=',$tipo)
        ->where('rol_movimiento_anio','=',$anio)
        ->where('empleado.empleado_id','=',$id);
    }
    public function empleado(){
        return $this->belongsTo(Empleado::class, 'empleado_id', 'empleado_id');
    }
    public function rubro(){
        return $this->belongsTo(Rubro::class, 'rubro_id', 'rubro_id');
    }
    public function rolcm(){
        return $this->belongsTo(Cabecera_Rol_CM::class, 'cabecera_rol_cm_id', 'cabecera_rol_id');
    }
}
