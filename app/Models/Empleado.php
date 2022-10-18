<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Empleado extends Model
{
    use HasFactory;
    protected $table = 'empleado';
    protected $primaryKey = 'empleado_id';
    public $timestamps = true;
    protected $fillable = [
        'empleado_cedula',
        'empleado_nombre',
        'empleado_telefono',
        'empleado_celular',
        'empleado_direccion',
        'empleado_sexo',
        'empleado_estatura',
        'empleado_grupo_sanguineo',
        'empleado_lugar_nacimiento',
        'empleado_fecha_nacimiento',
        'empleado_edad',
        'empleado_nacionalidad',
        'empleado_estado_civil',
        'empleado_correo',
        'empleado_jornada',
        'empleado_cosecha',
        'empleado_carga_familiar',
        'empleado_contacto_nombre',
        'empleado_contacto_telefono',
        'empleado_contacto_celular',
        'empleado_contacto_direccion',
        'empleado_observacion',
        'empleado_sueldo',
        'empleado_quincena',
        'empleado_fecha_ingreso',
        'empleado_fecha_salida',
        'empleado_horas_extra',
        'empleado_afiliado',
        'empleado_iess_asumido',
        'empleado_fondos_reserva',
        'empleado_fecha_afiliacion',
        'empleado_fecha_inicioFR',
        'empleado_impuesto_renta',
        'empleado_decimo_tercero',
        'empleado_decimo_cuarto',
        'empleado_estado',
        'empleado_cuenta_tipo',
        'empleado_cuenta_numero',
        'empleado_iess_gerente',
        'cargo_id',
        'departamento_id',
        'empleado_cuenta_anticipo',
        'empleado_cuenta_prestamo',
        'tipo_id',
        'banco_lista_id',
    ];
    protected $guarded = [
    ];
    

    public function scopeEmpleadosAnticipos($query){
        return $query->join('anticipo_empleado','anticipo_empleado.empleado_id','=','empleado.empleado_id')
        ->join('empleado_cargo', 'empleado_cargo.empleado_cargo_id','=','empleado.cargo_id')
        ->where('empleado_cargo.empresa_id','=',Auth::user()->empresa_id)->where('empleado_estado','=','1')
        ->select('empleado.empleado_id','empleado_nombre')->distinct()->orderBy('empleado_nombre','asc')->orderBy('empleado.empleado_id','asc');
    }
    public function scopeEmpleadoSimple($query){
        return $query->join('empleado_cargo', 'empleado_cargo.empleado_cargo_id','=','empleado.cargo_id')
        ->where('empleado_cargo.empresa_id','=',Auth::user()->empresa_id)
        ->where('empleado_estado','=','1')->orderBy('empleado_nombre','asc');
    }
    public function scopeEmpleadoById($query, $id){
        return $query->join('empleado_cargo', 'empleado_cargo.empleado_cargo_id','=','empleado.cargo_id')
        ->where('empleado_cargo.empresa_id','=',Auth::user()->empresa_id)->where('empleado_id','=',$id);
    }
    public function scopeEmpleadoByCed($query, $cedu){
        return $query->join('empleado_cargo', 'empleado_cargo.empleado_cargo_id','=','empleado.cargo_id')
        ->where('empleado_cargo.empresa_id','=',Auth::user()->empresa_id)->where('empleado_cedula','=',$cedu);
    }
    public function scopeEmpleadosByDepartamento($query, $id){
        return $query->join('empleado_cargo', 'empleado_cargo.empleado_cargo_id','=','empleado.cargo_id')
        ->where('empleado_cargo.empresa_id','=',Auth::user()->empresa_id)->where('departamento_id','=',$id);
    }

    public function scopeEmpleados($query){
        return $query->join('empleado_cargo', 'empleado_cargo.empleado_cargo_id','=','empleado.cargo_id')
        ->where('empleado_cargo.empresa_id','=',Auth::user()->empresa_id)->where('empleado_estado','=','1')->orderBy('empleado_nombre','asc');
    } 
    public function scopeEmpleadosEstado($query){
        return $query->join('empleado_cargo', 'empleado_cargo.empleado_cargo_id','=','empleado.cargo_id')
        ->where('empleado_cargo.empresa_id','=',Auth::user()->empresa_id)->orderBy('empleado_estado','asc');
    }   
    public function scopeEmpleadosControlDias($query, $sucursal){
        return $query->join('empleado_cargo', 'empleado_cargo.empleado_cargo_id','=','empleado.cargo_id')
        ->join('tipo_empleado', 'tipo_empleado.tipo_id','=','empleado.tipo_id')
        ->where('empleado_cargo.empresa_id','=',Auth::user()->empresa_id)->where('empleado_estado','=','1')
        ->where('tipo_empleado.sucursal_id','=',$sucursal)
        ->where('tipo_empleado.tipo_categoria','=','OPERATIVO CONTROL DIAS')
        ->orderBy('empleado_nombre','asc');
    }
    public function scopeEmpleadosBySucursal($query, $sucursal){
        return $query->join('empleado_cargo', 'empleado_cargo.empleado_cargo_id','=','empleado.cargo_id')
        ->join('empresa_departamento','empresa_departamento.departamento_id','=','empleado.departamento_id')
        ->where('empleado_cargo.empresa_id','=',Auth::user()->empresa_id)->where('empresa_departamento.sucursal_id','=',$sucursal)
        ->where('empleado_estado','=','1')->orderBy('empleado_nombre','asc');
    }
    public function scopeEmpleadosDecimoCuarto($query, $sucursal){
        return $query->join('empleado_cargo', 'empleado_cargo.empleado_cargo_id','=','empleado.cargo_id')
        ->join('empresa_departamento','empresa_departamento.departamento_id','=','empleado.departamento_id')
        ->where('empleado_cargo.empresa_id','=',Auth::user()->empresa_id)->where('empresa_departamento.sucursal_id','=',$sucursal)
        ->where('empleado_estado','=','1')->where('empleado_decimo_cuarto','=','0')->orderBy('empleado_nombre','asc');
    }
    public function scopeEmpleadosSucursal($query, $sucursal){
        return $query->join('empleado_cargo', 'empleado_cargo.empleado_cargo_id','=','empleado.cargo_id')
        ->join('empresa_departamento','empresa_departamento.departamento_id','=','empleado.departamento_id')
        ->where('empleado_cargo.empresa_id','=',Auth::user()->empresa_id)->where('empresa_departamento.sucursal_id','=',$sucursal)->orderBy('empleado_nombre','asc');
    }
    public function scopeEmpleadosbuscar($query, $sucursal, $estado){
         $query->join('empleado_cargo', 'empleado_cargo.empleado_cargo_id','=','empleado.cargo_id')
        ->join('empresa_departamento','empresa_departamento.departamento_id','=','empleado.departamento_id')
        ->where('empleado_cargo.empresa_id','=',Auth::user()->empresa_id)->where('empresa_departamento.sucursal_id','=',$sucursal)->orderBy('empleado_nombre','asc');
        if($estado != 'TODOS'){
            $query->where('empleado_estado', '=', $estado);
        }
        return  $query;  
    }
    public function scopeEmpleadosBySucursalAdministrativo($query, $sucursal){
        return $query->join('tipo_empleado', 'tipo_empleado.tipo_id','=','empleado.tipo_id')
        ->where('tipo_empleado.empresa_id','=',Auth::user()->empresa_id)->where('empleado_estado','=','1')
        ->where('tipo_empleado.sucursal_id','=',$sucursal)
        ->where('tipo_empleado.tipo_categoria','=','ADMINISTRATIVO')
        ->orwhere('tipo_empleado.tipo_categoria','=','OPERATIVO')
        ->orderBy('empleado_nombre','asc');
       
    }
    public function scopeEmpleado($query, $id){
        return $query->join('empleado_cargo', 'empleado_cargo.empleado_cargo_id','=','empleado.cargo_id')
        ->where('empleado_cargo.empresa_id','=',Auth::user()->empresa_id)->where('empleado_estado','=','1')->where('empleado_id','=',$id);
    }
    public function scopeEmpleadoEstado($query, $id){
        return $query->join('empleado_cargo', 'empleado_cargo.empleado_cargo_id','=','empleado.cargo_id')
        ->where('empleado_cargo.empresa_id','=',Auth::user()->empresa_id)->where('empleado_id','=',$id);
    } 
    public function scopeEmpleadoTipo($query, $id){
        return $query->join('empleado_cargo', 'empleado_cargo.empleado_cargo_id','=','empleado.cargo_id'
                    )->join('empresa_departamento', 'empresa_departamento.departamento_id','=','empleado.departamento_id'
                    )->join('sucursal', 'sucursal.sucursal_id','=','empresa_departamento.sucursal_id'
                    )->join('tipo_empleado_parametrizacion', 'tipo_empleado_parametrizacion.tipo_id','=','empleado.tipo_id'
                    )->join('cuenta', 'cuenta.cuenta_id','=','tipo_empleado_parametrizacion.cuenta_haber'
                    )->where('empleado_cargo.empresa_id','=',Auth::user()->empresa_id)->where('empleado_estado','=','1')->where('empleado_id','=',$id);
    } 
    
    public function scopeEmpleadoBusquedaCuenta($query, $id,$rubro){
        return $query->join('empleado_cargo', 'empleado_cargo.empleado_cargo_id','=','empleado.cargo_id'
                    )->join('empresa_departamento', 'empresa_departamento.departamento_id','=','empleado.departamento_id'
                    )->join('sucursal', 'sucursal.sucursal_id','=','empresa_departamento.sucursal_id'
                    )->join('tipo_empleado_parametrizacion', 'tipo_empleado_parametrizacion.tipo_id','=','empleado.tipo_id'
                    )->join('rubro', 'rubro.rubro_id','=','tipo_empleado_parametrizacion.rubro_id'
                    )->where('empleado_cargo.empresa_id','=',Auth::user()->empresa_id)->where('empleado_estado','=','1')
                    ->where('empleado.empleado_id','=',$id)
                    ->where('rubro.rubro_nombre','=',$rubro);
    }

    public function scopeBuscarEmpleadoByNombreCedula($query, $params){
        return $query->where(DB::raw('lower(empleado.empleado_nombre)'),'like', "%".strtolower($params)."%")
                     ->orwhere(DB::raw('lower(empleado.empleado_cedula)'),'like', "%".strtolower($params)."%")
                     ->where('empleado_estado','=','1');
    }
    
    public function scopeEmpleadoME($query){
        return $query->join('empleado_cargo', 'empleado_cargo.empleado_cargo_id','=','empleado.cargo_id'                    
                    )->join('medico', 'medico.empleado_id','=','empleado.empleado_id'
                    )->where('empleado_cargo.empresa_id','=',Auth::user()->empresa_id)->where('empleado_estado','=','1')->orderBy('empleado_nombre','asc');
    } 
    public function scopeEmpleadosRolSucursal($query,$sucursal){
        return $query->join('empleado_cargo', 'empleado_cargo.empleado_cargo_id','=','empleado.cargo_id')
        ->join('empresa_departamento','empresa_departamento.departamento_id','=','empleado.departamento_id')
        ->join('parametrizar_rol','parametrizar_rol.empresa_id','=','empleado_cargo.empresa_id')
        ->where('empleado_cargo.empresa_id','=',Auth::user()->empresa_id)
        ->where('empresa_departamento.sucursal_id','=',$sucursal)
        ->where('empleado_estado','=','1')->orderBy('empleado_nombre','asc');
    }
    public function scopeEmpleadosRol($query){
        return $query->join('empleado_cargo', 'empleado_cargo.empleado_cargo_id','=','empleado.cargo_id')

        ->join('empresa','empresa.empresa_id','=','empleado_cargo.empresa_id')
        ->where('empleado_estado','=','1')
        ->where('empresa.empresa_id','=',Auth::user()->empresa_id)->orderBy('empleado_nombre','asc');
    }
    public function scopeEmpleadosRolId($query, $id){
        return $query->join('empleado_cargo', 'empleado_cargo.empleado_cargo_id','=','empleado.cargo_id')
        ->join('empresa','empresa.empresa_id','=','empleado_cargo.empresa_id')
        ->join('parametrizar_rol','parametrizar_rol.empresa_id','=','empresa.empresa_id')
        ->where('empleado_estado','=','1')
        ->where('empresa.empresa_id','=',Auth::user()->empresa_id)->where('empleado_id','=',$id);
    }
    public function scopeEmpleadosRolIdBanco($query, $id){
        return $query->join('empleado_cargo', 'empleado_cargo.empleado_cargo_id','=','empleado.cargo_id')
        ->join('empresa','empresa.empresa_id','=','empleado_cargo.empresa_id')
        ->join('banco_lista','banco_lista.banco_lista_id','=','empleado.banco_lista_id')
        ->join('parametrizar_rol','parametrizar_rol.empresa_id','=','empresa.empresa_id')
        ->where('empleado_estado','=','1')
        ->where('empresa.empresa_id','=',Auth::user()->empresa_id)->where('empleado_id','=',$id);
    }
    
    public function scopeEmpleadosRolfecha($query, $fecha_hasta){
        return $query->join('empleado_cargo', 'empleado_cargo.empleado_cargo_id','=','empleado.cargo_id')
        ->join('empresa','empresa.empresa_id','=','empleado_cargo.empresa_id')
        ->join('parametrizar_rol','parametrizar_rol.empresa_id','=','empresa.empresa_id')
        ->where('empleado_fecha_salida', '<=', $fecha_hasta)
        ->where('empleado_estado','=','1')
        ->where('empresa.empresa_id','=',Auth::user()->empresa_id)->orderBy('empleado_nombre','asc');
    }
    public function scopeEmpleadoAnticipos($query){
        return $query->join('anticipo_empleado','anticipo_empleado.empleado_id','=','empleado.empleado_id')
        ->join('empleado_cargo', 'empleado_cargo.empleado_cargo_id','=','empleado.cargo_id')
        ->where('empleado_cargo.empresa_id','=',Auth::user()->empresa_id)->where('empleado_estado','=','1')
        ->select('empleado.empleado_id','empleado.empleado_nombre')->distinct()->orderBy('empleado_nombre','asc')
        ->orderBy('empleado.empleado_id','asc');
    }
    public function cargo(){
        return $this->belongsTo(Empleado_Cargo::class, 'cargo_id', 'empleado_cargo_id');
    }
    public function tipo(){
        return $this->belongsTo(Tipo_Empleado::class, 'tipo_id', 'tipo_id');
    }
    public function banco(){
        return $this->belongsTo(Banco_Lista::class, 'banco_lista_id', 'banco_lista_id');
    }   
    public function departamento(){
        return $this->belongsTo(Empresa_Departamento::class, 'departamento_id', 'departamento_id');
    } 
    public function cuentaPrestamo(){
        return $this->belongsTo(Cuenta::class, 'cuenta_id', 'empleado_cuenta_prestamo');
    }
    public function cuentaAnticipo(){
        return $this->belongsTo(Cuenta::class, 'cuenta_id', 'empleado_cuenta_anticipo');
    }


}
