<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Vacacion extends Model
{
    use HasFactory;
    protected $table ='vacacion';
    protected $primaryKey = 'vacacion_id';
    public $timestamps = true;
    protected $fillable = [    
        'vacacion_numero',
        'vacacion_serie',
        'vacacion_secuencial',   
        'vacacion_fecha',
        'vacacion_tipo', 
        'vacacion_valor',
        'vacacion_descripcion',
        'vacacion_estado',
        'diario_id',
        'cabecera_rol_id',
        'empleado_id',
        'rango_id'
    ];
    protected $guarded =[
    ];
    public function scopevacaciones($query){
        return $query->join('diario','diario.diario_id','=','vacacion.diario_id')->where('diario.empresa_id','=',Auth::user()->empresa_id)->where('vacacion_estado','=','1')->orderBy('vacacion_fecha','asc');
    }
    public function scopevacacion($query, $id){
        return $query->join('diario','diario.diario_id','=','vacacion.diario_id')->where('diario.empresa_id','=',Auth::user()->empresa_id)->where('vacacion_id','=',$id);
    }
    public function scopevacacionsucursal($query, $id){
        return $query->join('rango_documento','rango_documento.rango_id','=','vacacion.rango_id')
        ->join('punto_emision','punto_emision.punto_id','=','rango_documento.punto_id')
        ->join('sucursal','sucursal.sucursal_id','=','punto_emision.sucursal_id')
        ->where('sucursal.empresa_id','=',Auth::user()->empresa_id)->where('vacacion_estado','=','1')->where('sucursal.sucursal_id','=',$id);
    }
    public function scopeVacacionEmpleado($query, $id){
        return $query->join('diario','diario.diario_id','=','vacacion.diario_id')->where('diario.empresa_id','=',Auth::user()->empresa_id)->where('empleado_id','=',$id);
    }
    public function scopeSecuencial($query, $id){
        return $query->join('diario','diario.diario_id','=','vacacion.diario_id')->where('diario.empresa_id','=',Auth::user()->empresa_id)->where('vacacion.rango_id','=',$id);
    }
    public function scopeEstados($query){
        return $query->join('empleado','empleado.empleado_id','=','vacacion.empleado_id')->join('diario','diario.diario_id','=','vacacion.diario_id')->where('diario.empresa_id','=',Auth::user()->empresa_id);
    }
    public function scopevacacionesDiferente($query,$fechadesde,$fechahasta,$empleado_id,$estado){
        return $query->join('diario','diario.diario_id','=','vacacion.diario_id')->where('diario.empresa_id','=',Auth::user()->empresa_id)
        ->where('vacacion_fecha', '>=', $fechadesde)
        ->where('vacacion_fecha', '<=', $fechahasta)
        ->where('empleado_id', '=', $empleado_id)
        ->where('vacacion_estado','=',$estado)
        ->orderBy('vacacion_fecha','asc');
    }
    public function scopevacacionesbuscarEmpleado($query,$empleado_id){
        return $query->join('diario','diario.diario_id','=','vacacion.diario_id')->where('diario.empresa_id','=',Auth::user()->empresa_id)
        ->where('empleado_id', '=', $empleado_id)
        ->where('vacacion_estado','=','1')
        ->orderBy('vacacion_fecha','asc');
    }
    public function scopevacacionesEmpleado($query,$empleado_id){
        return $query->join('diario','diario.diario_id','=','vacacion.diario_id')->where('diario.empresa_id','=',Auth::user()->empresa_id)
        ->where('empleado_id', '=', $empleado_id)
        ->orderBy('vacacion_fecha','asc');
    }
    public function scopevacacionesfecha($query,$fechadesde,$fechahasta){
        return $query->join('diario','diario.diario_id','=','vacacion.diario_id')->where('diario.empresa_id','=',Auth::user()->empresa_id)
        ->where('vacacion_fecha', '>=', $fechadesde)
        ->where('vacacion_fecha', '<=', $fechahasta)
        ->orderBy('vacacion_fecha','asc');
    }
    public function scopevacacionesestado($query,$estado){
        return $query->join('diario','diario.diario_id','=','vacacion.diario_id')->where('diario.empresa_id','=',Auth::user()->empresa_id)
        ->where('vacacion_estado','=',$estado)
        ->orderBy('vacacion_fecha','asc');
    } 
    public function rangoDocumento(){
        return $this->belongsTo(Rango_Documento::class, 'rango_id', 'rango_id');
    }
    public function diario()
    {
        return $this->belongsTo(Diario::class, 'diario_id', 'diario_id');
    }
    public function rol()
    {
        return $this->belongsTo(Rol_Consolidado::class, 'cabecera_rol_id', 'cabecera_rol_id');
    }
    public function empleado()
    {
        return $this->belongsTo(Empleado::class, 'empleado_id', 'empleado_id');
    }
}
