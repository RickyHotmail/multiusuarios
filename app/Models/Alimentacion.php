<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Alimentacion extends Model
{
    use HasFactory;
    protected $table ='alimentacion';
    protected $primaryKey = 'alimentacion_id';
    public $timestamps = true;
    protected $fillable = [        
        'alimentacion_fecha',
        'alimentacion_valor', 
        'alimentacion_estado',
        'empleado_id',
        'cabecera_rol_id',
        'cabecera_rol_cm_id',
        'transaccion_id'
    ];
    protected $guarded =[
    ];
    public function scopealimentacion($query, $id){
        return $query->join('empleado','empleado.empleado_id','=','alimentacion.empleado_id')->join('empleado_cargo','empleado_cargo.empleado_cargo_id','=','empleado.cargo_id')->where('empleado_cargo.empresa_id','=',Auth::user()->empresa_id)->where('alimentacion_id','=',$id);
    }
    public function scopeFactura($query, $id){
        return $query->join('empleado','empleado.empleado_id','=','alimentacion.empleado_id')->join('empleado_cargo','empleado_cargo.empleado_cargo_id','=','empleado.cargo_id')->where('empleado_cargo.empresa_id','=',Auth::user()->empresa_id)->where('transaccion_id','=',$id);
    }
    public function scopebuscarEmpleado($query, $id){
        return $query->join('empleado','empleado.empleado_id','=','alimentacion.empleado_id')->join('empleado_cargo','empleado_cargo.empleado_cargo_id','=','empleado.cargo_id')->where('empleado_cargo.empresa_id','=',Auth::user()->empresa_id)->where('alimentacion_estado','=','1')->where('empleado.empleado_id','=',$id);
    }
    public function scoperoloperativo($query, $id){
        return $query->join('transaccion_compra','transaccion_compra.transaccion_id','=','alimentacion.transaccion_id')->join('empleado','empleado.empleado_id','=','alimentacion.empleado_id')->join('empleado_cargo','empleado_cargo.empleado_cargo_id','=','empleado.cargo_id')->where('empleado_cargo.empresa_id','=',Auth::user()->empresa_id)->where('alimentacion_estado','=','1')->where('empleado.empleado_id','=',$id);
    }
    public function scopealimentaciones($query){
        return $query->join('empleado','empleado.empleado_id','=','alimentacion.empleado_id')->join('empleado_cargo','empleado_cargo.empleado_cargo_id','=','empleado.cargo_id')->where('empleado_cargo.empresa_id','=',Auth::user()->empresa_id)->where('alimentacion_estado','=','1');
    }
    public function empleado()
    {
        return $this->belongsTo(Empleado::class, 'empleado_id', 'empleado_id');
    }
    public function rol()
    {
        return $this->belongsTo(Rol_Consolidado::class, 'cabecera_rol_id', 'cabecera_rol_id');
    }
    public function rolcm()
    {
        return $this->belongsTo(Cabecera_Rol_CM::class, 'cabecera_rol_cm_id', 'cabecera_rol_id');
    }
    public function transaccion()
    {
        return $this->belongsTo(Transaccion_Compra::class, 'transaccion_id', 'transaccion_id');
    }
}
