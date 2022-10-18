<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Rol_Consolidado extends Model
{
    use HasFactory;
    protected $table='cabecera_rol';
    protected $primaryKey = 'cabecera_rol_id';
    public $timestamps=true;
    protected $fillable = [
        'cabecera_rol_fecha',
        'cabecera_rol_tipo',
        'cabecera_rol_total_dias',       
        'cabecera_rol_total_ingresos',        
        'cabecera_rol_total_anticipos', 
        'cabecera_rol_total_egresos',
        'cabecera_rol_sueldo',
        'cabecera_rol_pago',
        'cabecera_rol_fr_acumula',
        'cabecera_rol_iesspersonal',
        'cabecera_rol_iesspatronal',
        'cabecera_rol_estado',
        'empleado_id',
        'diario_contabilizacion_id',
        'diario_pago_id',
        'diario_contabilizacion_beneficios_id'
    ];
    protected $guarded =[
    ];
   
    public function scopeRoles($query){
        return $query->join('empleado','empleado.empleado_id','=','cabecera_rol.empleado_id')->join('empleado_cargo','empleado_cargo.empleado_cargo_id','=','empleado.cargo_id')->where('empleado_cargo.empresa_id','=',Auth::user()->empresa_id)->where('cabecera_rol_estado','=','1')->orderBy('cabecera_rol_fecha','desc');
    }
    public function scopeRol($query,$id){
        return $query->join('empleado','empleado.empleado_id','=','cabecera_rol.empleado_id')->join('empleado_cargo','empleado_cargo.empleado_cargo_id','=','empleado.cargo_id')->where('empleado_cargo.empresa_id','=',Auth::user()->empresa_id)->where('cabecera_rol_estado','=','1')->where('cabecera_rol_id','=',$id)->orderBy('empleado.empleado_nombre','asc');
    }
    public function scopeExtraerDecimoTercero($query,$fechadesde,$fechahasta){
        return $query->join('empleado','empleado.empleado_id','=','cabecera_rol.empleado_id')->join('detalle_rol','detalle_rol.cabecera_rol_id','=','cabecera_rol.cabecera_rol_id')->join('empleado_cargo','empleado_cargo.empleado_cargo_id','=','empleado.cargo_id')->where('empleado_cargo.empresa_id','=',Auth::user()->empresa_id)
        ->where('empleado_decimo_tercero','=','0')
        ->where('cabecera_rol_estado','=','1')
        ->where('detalle_rol_fecha_fin', '>=', $fechadesde)
        ->where('detalle_rol_fecha_fin', '<=', $fechahasta)
        ->orderBy('empleado.empleado_nombre','asc');
    }
    public function scopeExtraerDecimoCuarto($query,$fechadesde,$fechahasta){
        return $query->join('empleado','empleado.empleado_id','=','cabecera_rol.empleado_id')->join('detalle_rol','detalle_rol.cabecera_rol_id','=','cabecera_rol.cabecera_rol_id')->join('empleado_cargo','empleado_cargo.empleado_cargo_id','=','empleado.cargo_id')->where('empleado_cargo.empresa_id','=',Auth::user()->empresa_id)
        ->where('empleado_decimo_cuarto','=','0')
        ->where('cabecera_rol_estado','=','1')
        ->where('detalle_rol_fecha_fin', '>=', $fechadesde)
        ->where('detalle_rol_fecha_fin', '<=', $fechahasta)
        ->orderBy('empleado.empleado_nombre','asc');
    }
    public function scopeEmpleadosRol($query){
        return $query->join('empleado','empleado.empleado_id','=','cabecera_rol.empleado_id')
        ->join('empleado_cargo', 'empleado_cargo.empleado_cargo_id','=','empleado.cargo_id')
        ->join('empresa','empresa.empresa_id','=','empleado_cargo.empresa_id')
        ->where('cabecera_rol_estado','=','1')
        ->where('empresa.empresa_id','=',Auth::user()->empresa_id)->orderBy('empleado_nombre','asc');
    }
    public function scopeRolesFecha($query,$fechadesde){
        return $query->join('empleado','empleado.empleado_id','=','cabecera_rol.empleado_id')->join('empleado_cargo','empleado_cargo.empleado_cargo_id','=','empleado.cargo_id')->where('empleado_cargo.empresa_id','=',Auth::user()->empresa_id)->where('cabecera_rol_estado','=','1')
        ->where('cabecera_rol_fecha', '=', $fechadesde);
    }
    public function scopeRolFecha($query,$fechadesde){
        return $query->join('detalle_rol','detalle_rol.cabecera_rol_id','=','cabecera_rol.cabecera_rol_id')->join('empleado','empleado.empleado_id','=','cabecera_rol.empleado_id')->join('empleado_cargo','empleado_cargo.empleado_cargo_id','=','empleado.cargo_id')->where('empleado_cargo.empresa_id','=',Auth::user()->empresa_id)->where('cabecera_rol_estado','=','1')
        ->where('cabecera_rol_fecha', '=', $fechadesde);
    }
    public function scopeRolesBusqueda($query,$fechadesde,$fechahasta,$empleado_id){
        return $query->join('diario','diario.diario_id','=','quincena.diario_id')->where('diario.empresa_id','=',Auth::user()->empresa_id)
        ->where('detalle_rol_fecha_inicio', '>=', $fechadesde)
        ->where('detalle_rol_fecha_fin', '<=', $fechahasta)
        ->where('cabecera_rol.empleado_id', '=', $empleado_id)
        ->orderBy('cabecera_rol_fecha','desc');
    }
    public function scopeRolesBuscar($query,$fechadesde,$fechahasta,$empleado_id){
        return $query->join('detalle_rol','detalle_rol.cabecera_rol_id','=','cabecera_rol.cabecera_rol_id')->join('empleado','empleado.empleado_id','=','cabecera_rol.empleado_id')->join('empleado_cargo','empleado_cargo.empleado_cargo_id','=','empleado.cargo_id')->where('empleado_cargo.empresa_id','=',Auth::user()->empresa_id)
        ->where('detalle_rol_fecha_inicio', '>=', $fechadesde)
        ->where('detalle_rol_fecha_fin', '<=', $fechahasta)
        ->where('cabecera_rol.empleado_id', '=', $empleado_id)
        ->orderBy('cabecera_rol_fecha','desc');
    }
    public function scopeRolBusquedaEmpleado($query,$empleado_id){
        return $query->join('detalle_rol','detalle_rol.cabecera_rol_id','=','cabecera_rol.cabecera_rol_id')->join('empleado','empleado.empleado_id','=','cabecera_rol.empleado_id')->join('empleado_cargo','empleado_cargo.empleado_cargo_id','=','empleado.cargo_id')->where('empleado_cargo.empresa_id','=',Auth::user()->empresa_id)
        ->where('cabecera_rol_estado','=','1')
        ->where('cabecera_rol.empleado_id', '=', $empleado_id)
        ->orderBy('cabecera_rol_fecha','desc');
    }
    public function scopeRolBusquedaFecha($query,$fechadesde,$fechahasta){
        return $query->join('detalle_rol','detalle_rol.cabecera_rol_id','=','cabecera_rol.cabecera_rol_id')->join('empleado','empleado.empleado_id','=','cabecera_rol.empleado_id')->join('empleado_cargo','empleado_cargo.empleado_cargo_id','=','empleado.cargo_id')->where('empleado_cargo.empresa_id','=',Auth::user()->empresa_id)
        ->where('cabecera_rol_estado','=','1')
        ->where('detalle_rol_fecha_inicio', '>=', $fechadesde)
        ->where('detalle_rol_fecha_fin', '<=', $fechahasta)
        ->orderBy('cabecera_rol_fecha','desc');
    }
    public function scopebuscarrolContabilisado($query,$fechadesde,$fechahasta){
        return $query->join('detalle_rol','detalle_rol.cabecera_rol_id','=','cabecera_rol.cabecera_rol_id')->join('empleado','empleado.empleado_id','=','cabecera_rol.empleado_id') ->join('tipo_empleado','tipo_empleado.tipo_id','=','empleado.tipo_id')->join('empleado_cargo','empleado_cargo.empleado_cargo_id','=','empleado.cargo_id')->where('empleado_cargo.empresa_id','=',Auth::user()->empresa_id)
        ->where('cabecera_rol_estado','=','1')
        ->where('detalle_rol_fecha_inicio', '>=', $fechadesde)
        ->where('detalle_rol_fecha_fin', '<=', $fechahasta)
        ->where('empleado_afiliado', '=', '1')
        ->orderBy('empleado.empleado_nombre','asc');
    }

    public function scopebuscarrolContabilisadotipo($query,$fechadesde,$fechahasta){
        return $query->join('detalle_rol','detalle_rol.cabecera_rol_id','=','cabecera_rol.cabecera_rol_id')->join('empleado','empleado.empleado_id','=','cabecera_rol.empleado_id') ->join('tipo_empleado','tipo_empleado.tipo_id','=','empleado.tipo_id')->where('tipo_empleado.empresa_id','=',Auth::user()->empresa_id)
        ->where('cabecera_rol_estado','=','1')
        ->where('detalle_rol_fecha_inicio', '>=', $fechadesde)
        ->where('detalle_rol_fecha_fin', '<=', $fechahasta)
        ->where('empleado_afiliado', '=', '1')
        ->orderBy('tipo_empleado.tipo_descripcion','asc');
    }
    public function scopebuscarrolContabilisadodiario($query,$diario){
        return $query->join('detalle_rol','detalle_rol.cabecera_rol_id','=','cabecera_rol.cabecera_rol_id')->join('empleado','empleado.empleado_id','=','cabecera_rol.empleado_id') ->join('tipo_empleado','tipo_empleado.tipo_id','=','empleado.tipo_id')->join('empleado_cargo','empleado_cargo.empleado_cargo_id','=','empleado.cargo_id')->where('empleado_cargo.empresa_id','=',Auth::user()->empresa_id)
        ->where('cabecera_rol_estado','=','1')
        ->where('diario_contabilizacion_id', '=', $diario)
        ->where('empleado_afiliado', '=', '1')
        ->orderBy('empleado.empleado_nombre','asc');
    }

    public function scopebuscarrolContabilisadotipodiario($query,$diario){
        return $query->join('detalle_rol','detalle_rol.cabecera_rol_id','=','cabecera_rol.cabecera_rol_id')->join('empleado','empleado.empleado_id','=','cabecera_rol.empleado_id') ->join('tipo_empleado','tipo_empleado.tipo_id','=','empleado.tipo_id')->where('tipo_empleado.empresa_id','=',Auth::user()->empresa_id)
        ->where('cabecera_rol_estado','=','1')
        ->where('diario_contabilizacion_id', '=', $diario)
        ->where('empleado_afiliado', '=', '1')
        ->orderBy('tipo_empleado.tipo_descripcion','asc');
    }
    public function scopebuscardiario($query,$diario){
        return $query->join('empleado','empleado.empleado_id','=','cabecera_rol.empleado_id')->join('empleado_cargo','empleado_cargo.empleado_cargo_id','=','empleado.cargo_id')->where('empleado_cargo.empresa_id','=',Auth::user()->empresa_id)
        ->where('cabecera_rol_estado','=','1')
        ->where('diario_contabilizacion_id', '=', $diario)
        ->where('empleado_afiliado', '=', '1')
        ->orderBy('empleado.empleado_nombre','asc');
    }


    public function detalles(){
        return $this->hasMany(Detalle_Rol::class, 'cabecera_rol_id', 'cabecera_rol_id');
    }
    public function RolMovimientos(){
        return $this->hasMany(Rol_Movimiento::class, 'cabecera_rol_id', 'cabecera_rol_id');
    }
    public function alimentacion(){
        return $this->hasMany(Alimentacion::class, 'cabecera_rol_id', 'cabecera_rol_id');
    }
    public function anticipos(){
        return $this->hasMany(Descuento_Anticipo_Empleado::class, 'cabecera_rol_id', 'cabecera_rol_id');
    }
    public function quincena(){
        return $this->belongsTo(Quincena::class, 'cabecera_rol_id', 'cabecera_rol_id');
    }
    public function empleado(){
        return $this->belongsTo(Empleado::class, 'empleado_id', 'empleado_id');
    }
    public function diario(){
        return $this->belongsTo(Diario::class, 'diario_pago_id', 'diario_id');
    }
    public function diariopago(){
        return $this->belongsTo(Diario::class, 'diario_pago_id', 'diario_id');
    }
    public function diariocontabilizacion(){
        return $this->belongsTo(Diario::class, 'diario_contabilizacion_id', 'diario_id');
    }
    public function diariocontabilizacionbeneficios(){
        return $this->belongsTo(Diario::class, 'diario_contabilizacion_beneficios_id', 'diario_id');
    }
    public function control(){
        return $this->belongsTo(Control_Dia::class, 'cabecera_rol_id', 'cabecera_rol_id');
    }
    public function vacaciones(){
        return $this->belongsTo(Vacacion::class, 'cabecera_rol_id', 'cabecera_rol_id');
    }
}
