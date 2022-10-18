<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Cabecera_Rol_CM extends Model
{
    use HasFactory;
    protected $table='cabecera_rol_cm';
    protected $primaryKey = 'cabecera_rol_id';
    public $timestamps=true;
    protected $fillable = [
        'cabecera_rol_fecha',
        'cabecera_rol_tipo',
        'cabecera_rol_total_dias',       
        'cabecera_rol_total_ingresos',         
        'cabecera_rol_total_egresos',
        'cabecera_rol_comisariato',
        'cabecera_rol_anticipos',
        'cabecera_rol_quincena',
        'cabecera_rol_sueldo',
        'cabecera_rol_pago',
        'cabecera_rol_fr_acumula',
        'cabecera_rol_fondo_reserva',
        'cabecera_rol_decimotercero',
        'cabecera_rol_decimocuarto',
        'cabecera_rol_decimotercero_acumula',
        'cabecera_rol_decimocuarto_acumula',
        'cabecera_rol_viaticos',
        'cabecera_rol_iece_secap',
        'cabecera_rol_aporte_patronal',
        'cabecera_rol_vacaciones',
        'cabecera_rol_estado',
        'empleado_id',
        'diario_contabilizacion_id',
        'diario_pago_id',
    ];
    protected $guarded =[
    ];
    public function scopeRoles($query){
        return $query->join('empleado','empleado.empleado_id','=','cabecera_rol_cm.empleado_id')->join('empleado_cargo','empleado_cargo.empleado_cargo_id','=','empleado.cargo_id')->where('empleado_cargo.empresa_id','=',Auth::user()->empresa_id)->where('cabecera_rol_estado','=','1')->orderBy('cabecera_rol_fecha','desc');
    }
    public function scopeRol($query,$id){
        return $query->join('empleado','empleado.empleado_id','=','cabecera_rol_cm.empleado_id')->join('empleado_cargo','empleado_cargo.empleado_cargo_id','=','empleado.cargo_id')->where('empleado_cargo.empresa_id','=',Auth::user()->empresa_id)->where('cabecera_rol_estado','=','1')->where('cabecera_rol_id','=',$id)->orderBy('empleado.empleado_nombre','asc');
    }
    public function scopeMovimientoRolesByCC($query,$consumo,$fechadesde,$fechahasta){
        $query->join('detalle_rol_cm','detalle_rol_cm.cabecera_rol_id','=','cabecera_rol_cm.cabecera_rol_id')
            ->join('rubro','rubro.rubro_id','=','detalle_rol_cm.rubro_id')
            ->join('empleado','empleado.empleado_id','=','cabecera_rol_cm.empleado_id')
            ->join('tipo_empleado','tipo_empleado.tipo_id','=','empleado.tipo_id')
            ->join('tipo_empleado_parametrizacion', function($join){
                $join->on('tipo_empleado_parametrizacion.tipo_id', '=', 'tipo_empleado.tipo_id')
                ->on('tipo_empleado_parametrizacion.rubro_id', '=', 'rubro.rubro_id');
            })->join('categoria_rol','categoria_rol.categoria_id','=','tipo_empleado_parametrizacion.categoria_id')
            ->join('centro_consumo','centro_consumo.centro_consumo_id','=','categoria_rol.centro_consumo_id')
            ->where('rubro.empresa_id','=',Auth::user()->empresa_id)
            ->where('rubro_estado','=','1')
            ->where('cabecera_rol_estado','=','1')
            ->where('cabecera_rol_fecha', '>=', $fechadesde)
            ->where('cabecera_rol_fecha', '<=', $fechahasta);
        if($consumo != '0'){
            $query->where('categoria_rol.centro_consumo_id','=',$consumo);
        }
        return $query;
    }
    public function scopeBuscar($query, $fechaI, $fechaF, $empleado, $sucursal){
        $query->join('empleado','empleado.empleado_id','=','cabecera_rol_cm.empleado_id')
        ->join('empleado_cargo','empleado_cargo.empleado_cargo_id','=','empleado.cargo_id')
        ->join('empresa_departamento', 'empresa_departamento.departamento_id','=','empleado.departamento_id')
        ->join('sucursal', 'sucursal.sucursal_id','=','empresa_departamento.sucursal_id')
        ->where('empleado_cargo.empresa_id','=',Auth::user()->empresa_id)->orderBy('empleado.empleado_nombre','asc');
        if($empleado != '0'){
            $query->where('empleado.empleado_id','=',$empleado);
        }  
        if($sucursal != '0'){
            $query->where('sucursal.sucursal_id','=',$sucursal);
        }   
        $query->where('cabecera_rol_fecha','>=',$fechaI)->where('cabecera_rol_fecha','<=',$fechaF);
        return $query;
    }
    public function scopeEmpleadosRol($query){
        return $query->join('empleado','empleado.empleado_id','=','cabecera_rol_cm.empleado_id')
        ->join('empleado_cargo', 'empleado_cargo.empleado_cargo_id','=','empleado.cargo_id')
        ->join('empresa','empresa.empresa_id','=','empleado_cargo.empresa_id')
        ->where('cabecera_rol_estado','=','1')
        ->where('empresa.empresa_id','=',Auth::user()->empresa_id)->orderBy('empleado_nombre','asc');
    }
    public function scopeEmpleadosSucursal($query){
        return $query->join('empleado','empleado.empleado_id','=','cabecera_rol_cm.empleado_id')
        ->join('empleado_cargo', 'empleado_cargo.empleado_cargo_id','=','empleado.cargo_id')
        ->join('empresa_departamento', 'empresa_departamento.departamento_id','=','empleado.departamento_id')
        ->join('sucursal', 'sucursal.sucursal_id','=','empresa_departamento.sucursal_id')
        ->join('empresa','empresa.empresa_id','=','empleado_cargo.empresa_id')
        ->where('cabecera_rol_estado','=','1')
        ->where('empresa.empresa_id','=',Auth::user()->empresa_id)->orderBy('sucursal_nombre','asc');
    }
    public function scopeRolesValidar($query,$fecha,$empleado_id){
        return $query->join('detalle_rol_cm','detalle_rol_cm.cabecera_rol_id','=','cabecera_rol_cm.cabecera_rol_id')->join('empleado','empleado.empleado_id','=','cabecera_rol_cm.empleado_id')->join('empleado_cargo','empleado_cargo.empleado_cargo_id','=','empleado.cargo_id')->where('empleado_cargo.empresa_id','=',Auth::user()->empresa_id)
        ->where('cabecera_rol_fecha', '=', $fecha)
        ->where('cabecera_rol_cm.empleado_id', '=', $empleado_id)
        ->orderBy('cabecera_rol_fecha','desc');
    }
    public function scopeRolesBuscar($query,$fechadesde,$fechahasta,$empleado_id){
        return $query->join('detalle_rol_cm','detalle_rol_cm.cabecera_rol_id','=','cabecera_rol_cm.cabecera_rol_id')->join('empleado','empleado.empleado_id','=','cabecera_rol_cm.empleado_id')->join('empleado_cargo','empleado_cargo.empleado_cargo_id','=','empleado.cargo_id')->where('empleado_cargo.empresa_id','=',Auth::user()->empresa_id)
        ->where('detalle_rol_fecha_inicio', '>=', $fechadesde)
        ->where('detalle_rol_fecha_fin', '<=', $fechahasta)
        ->where('cabecera_rol_cm.empleado_id', '=', $empleado_id)
        ->orderBy('cabecera_rol_fecha','desc');
    }
    public function scopeRolBusquedaEmpleado($query,$empleado_id){
        return $query->join('detalle_rol_cm','detalle_rol_cm.cabecera_rol_id','=','cabecera_rol_cm.cabecera_rol_id')->join('empleado','empleado.empleado_id','=','cabecera_rol_cm.empleado_id')->join('empleado_cargo','empleado_cargo.empleado_cargo_id','=','empleado.cargo_id')->where('empleado_cargo.empresa_id','=',Auth::user()->empresa_id)
        ->where('cabecera_rol_estado','=','1')
        ->where('cabecera_rol_cm.empleado_id', '=', $empleado_id)
        ->orderBy('cabecera_rol_fecha','desc');
    }
    public function scopeRolBusquedaFecha($query,$fechadesde,$fechahasta){
        return $query->join('detalle_rol_cm','detalle_rol_cm.cabecera_rol_id','=','cabecera_rol_cm.cabecera_rol_id')->join('empleado','empleado.empleado_id','=','cabecera_rol_cm.empleado_id')->join('empleado_cargo','empleado_cargo.empleado_cargo_id','=','empleado.cargo_id')->where('empleado_cargo.empresa_id','=',Auth::user()->empresa_id)
        ->where('cabecera_rol_estado','=','1')
        ->where('detalle_rol_fecha_inicio', '>=', $fechadesde)
        ->where('detalle_rol_fecha_fin', '<=', $fechahasta)
        ->orderBy('cabecera_rol_fecha','desc');
    }
    public function detalles(){
        return $this->hasMany(Detalle_Rol_CM::class, 'cabecera_rol_id', 'cabecera_rol_id');
    }
    public function RolMovimientos(){
        return $this->hasMany(Rol_Movimiento::class, 'cabecera_rol_cm_id', 'cabecera_rol_id');
    }
    public function alimentacion(){
        return $this->hasMany(Alimentacion::class, 'cabecera_rol_id', 'cabecera_rol_id');
    }
    public function anticipos(){
        return $this->hasMany(Descuento_Anticipo_Empleado::class, 'cabecera_rol_id', 'cabecera_rol_id');
    }
    public function quincena(){
        return $this->hasMany(Descuento_Quincena::class, 'cabecera_rol_cm_id', 'cabecera_rol_id');
    }
    public function empleado(){
        return $this->belongsTo(Empleado::class, 'empleado_id', 'empleado_id');
    }
    public function diariopago(){
        return $this->belongsTo(Diario::class, 'diario_pago_id', 'diario_id');
    }
    public function diariocontabilizacion(){
        return $this->belongsTo(Diario::class, 'diario_contabilizacion_id', 'diario_id');
    }
    public function control(){
        return $this->belongsTo(Control_Dia::class, 'cabecera_rol_id', 'cabecera_rol_id');
    }
    public function vacaciones(){
        return $this->belongsTo(Vacacion::class, 'cabecera_rol_id', 'cabecera_rol_id');
    }
    public function anticiposcm(){
        return $this->hasMany(Descuento_Anticipo_Empleado::class, 'cabecera_rol_cm_id', 'cabecera_rol_id');
    }
    public function quincenacm(){
        return $this->hasMany(Descuento_Quincena::class, 'cabecera_rol_cm_id', 'cabecera_rol_id');
    }
    public function controlcm(){
        return $this->belongsTo(Control_Dia::class, 'cabecera_rol_id', 'cabecera_rol_cm_id');
    }
    public function alimentacioncm(){
        return $this->hasMany(Alimentacion::class, 'cabecera_rol_cm_id', 'cabecera_rol_id');
    }
}
