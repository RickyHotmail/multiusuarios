<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Control_Dia extends Model
{
    use HasFactory;
    protected $table ='control_dias';
    protected $primaryKey = 'control_id';
    public $timestamps = true;
    protected $fillable = [       
        'control_serie',
        'control_numero',
        'control_secuencial',
        'control_mes',   
        'control_normal',
        'control_decanso',
        'control_vacaciones',
        'control_permiso',
        'control_cosecha',
        'control_extra',
        'control_ausente',
        'control_ano', 
        'control_estado',
        'control_fecha',
        'empleado_id',
        'rango_id',
        'cabecera_rol_id',
        'cabecera_rol_cm_id',
        
    ];
    protected $guarded =[
    ];
    public function scopeControldias($query){
        return $query->join('empleado', 'control_dias.empleado_id', '=', 'empleado.empleado_id')->join('empleado_cargo', 'empleado_cargo.empleado_cargo_id','=','empleado.cargo_id')
        ->where('empleado_cargo.empresa_id','=',Auth::user()->empresa_id)
        ->where('control_estado','=','1')->orderBy('empleado_nombre','asc');
    }
    public function scopeControldiasbuscar($query, $nomempleado,$mes,$anio){
         $query->join('empleado', 'control_dias.empleado_id', '=', 'empleado.empleado_id')->join('empleado_cargo', 'empleado_cargo.empleado_cargo_id','=','empleado.cargo_id')
        ->where('empleado_cargo.empresa_id','=',Auth::user()->empresa_id);
        if($nomempleado != '0'){
            $query->where('control_dias.empleado_id','=','425');
        }   
        $query->where('control_mes','=',$mes)->where('control_ano','<=',$anio);
        return $query;
    }
    public function scopeControldia($query, $id){
        return $query->join('empleado', 'control_dias.empleado_id', '=', 'empleado.empleado_id')->join('empleado_cargo', 'empleado_cargo.empleado_cargo_id','=','empleado.cargo_id')
        ->where('empleado_cargo.empresa_id','=',Auth::user()->empresa_id)
        ->where('empleado_estado','=','1')
        ->where('control_id','=', $id)->orderBy('empleado_nombre','asc');
    }
    public function scopeControldiaExisteRol($query, $id,$fecha){
        return $query->join('cabecera_rol', 'cabecera_rol.cabecera_rol_id', '=', 'control_dias.cabecera_rol_id')->join('empleado', 'control_dias.empleado_id', '=', 'empleado.empleado_id')->join('empleado_cargo', 'empleado_cargo.empleado_cargo_id','=','empleado.cargo_id')
        ->where('empleado_cargo.empresa_id','=',Auth::user()->empresa_id)
        ->where('empleado_estado','=','1')
        ->where('empleado.empleado_id','=', $id)->where('control_dias.control_fecha','=', $fecha);
    }
    public function scopeControldiaExiste($query, $id,$fecha){
        return $query->join('empleado', 'control_dias.empleado_id', '=', 'empleado.empleado_id')->join('empleado_cargo', 'empleado_cargo.empleado_cargo_id','=','empleado.cargo_id')
        ->where('empleado_cargo.empresa_id','=',Auth::user()->empresa_id)
        ->where('empleado_estado','=','1')->where('control_estado','=','1')
        ->where('empleado.empleado_id','=', $id)->where('control_dias.control_fecha','=', $fecha);
    }
    public function scopeControldiaDetalle($query, $id,$mes,$anio){
        return $query->join('detalle_control_dias', 'control_dias.control_id', '=', 'detalle_control_dias.control_id')->join('empleado', 'control_dias.empleado_id', '=', 'empleado.empleado_id')->join('empleado_cargo', 'empleado_cargo.empleado_cargo_id','=','empleado.cargo_id')
        ->where('empleado_cargo.empresa_id','=',Auth::user()->empresa_id)
        ->where('empleado_estado','=','1')
        ->where('control_estado','=','1')
        ->where('empleado.empleado_id','=', $id)
        ->where('control_mes','=', $mes)
        ->where('control_ano','=', $anio);
    }
    public function scopeControldiaDetalleRol($query, $id,$mes,$anio){
        return $query->join('detalle_control_dias', 'control_dias.control_id', '=', 'detalle_control_dias.control_id')->join('empleado', 'control_dias.empleado_id', '=', 'empleado.empleado_id')->join('empleado_cargo', 'empleado_cargo.empleado_cargo_id','=','empleado.cargo_id')
        ->where('empleado_cargo.empresa_id','=',Auth::user()->empresa_id)
        ->where('empleado_estado','=','1')
        ->where('control_estado','=','2')
        ->where('empleado.empleado_id','=', $id)
        ->where('control_mes','=', $mes)
        ->where('control_ano','=', $anio);
    }
    public function scopebuscarEmpleado($query, $id){
        return $query->join('empleado', 'control_dias.empleado_id', '=', 'empleado.empleado_id')->join('empleado_cargo', 'empleado_cargo.empleado_cargo_id','=','empleado.cargo_id')
        ->where('empleado_cargo.empresa_id','=',Auth::user()->empresa_id)
        ->where('control_estado','=','1')
        ->where('control_dias.empleado_id','=', $id);
    }
    public function scopePresentarEmpleado($query, $id){
        return $query->join('empleado', 'control_dias.empleado_id', '=', 'empleado.empleado_id')->join('empleado_cargo', 'empleado_cargo.empleado_cargo_id','=','empleado.cargo_id')
        ->where('empleado_cargo.empresa_id','=',Auth::user()->empresa_id)
        ->where('control_dias.control_id','=', $id);
    }
    public function scopeEmpleados($query){
        return $query->join('empleado', 'control_dias.empleado_id', '=', 'empleado.empleado_id')->join('empleado_cargo', 'empleado_cargo.empleado_cargo_id','=','empleado.cargo_id')
        ->where('empleado_cargo.empresa_id','=',Auth::user()->empresa_id)
        ->where('control_estado','=','1')
        ->orderBy('empleado_nombre','asc');
    }
    public function scopeEmpleadosSucursal($query, $id){
        return $query->join('empleado', 'control_dias.empleado_id', '=', 'empleado.empleado_id')
        ->join('empleado_cargo', 'empleado_cargo.empleado_cargo_id','=','empleado.cargo_id')
        ->join('empresa_departamento','empresa_departamento.departamento_id','=','empleado.departamento_id')
        ->where('empleado_cargo.empresa_id','=',Auth::user()->empresa_id)
        ->where('empresa_departamento.sucursal_id','=',$id)
        ->where('empleado_estado','=','1')
        ->where('control_estado','=','1')
        ->orderBy('empleado_nombre','asc');
    }
    public function scopeSecuencial($query, $id){
        return $query->join('empleado', 'control_dias.empleado_id', '=', 'empleado.empleado_id')->join('empleado_cargo', 'empleado_cargo.empleado_cargo_id','=','empleado.cargo_id')
        ->where('empleado_cargo.empresa_id','=',Auth::user()->empresa_id)->where('control_dias.rango_id','=',$id);
    }
    public function rol()
    {
        return $this->belongsTo(Cabecera_Rol::class, 'cabecera_rol_id', 'cabecera_rol_id');
    }
    public function rolcm()
    {
        return $this->belongsTo(Cabecera_Rol_CM::class, 'cabecera_rol_cm_id', 'cabecera_rol_id');
    }
    public function empleado()
    {
        return $this->belongsTo(Empleado::class, 'empleado_id', 'empleado_id');
    }
    public function detalles(){
        return $this->hasMany(Detalle_Control_Dias::class, 'control_id', 'control_id');
    }
}
