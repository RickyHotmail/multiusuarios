<?php

namespace App\Http\Controllers;

use App\Models\Empleado;
use App\Models\Empleado_Cargo;
use App\Models\Empresa_Departamento;
use App\Models\Cuenta;
use App\Models\Tipo_Empleado;
use App\Models\Banco_Lista;
use App\Models\Punto_Emision;
use App\Http\Controllers\Controller;
use App\Models\Alimentacion;
use App\Models\Asignacion_Rol;
use App\Models\Empresa;
use App\Models\Parametrizacion_Contable;
use App\Models\Rol_Movimiento;
use App\Models\Rubro;

use App\Models\Sucursal;
use App\Models\Transaccion_Compra;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use PDF;
use Maatwebsite\Excel\Facades\Excel;

class empleadoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try{ 
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $cargos=Empleado_Cargo::EmpleadoCargos()->get();
            $departamentos=Empresa_Departamento::Departamentos()->get();
            $cuentas=Cuenta::CuentasMovimiento()->get(); 
            $tipo=Tipo_Empleado::Tipos()->get(); 
            $banco=Banco_Lista::BancoListas()->get();
            $estados=Empleado::EmpleadosEstado()->select('empleado.empleado_estado')->distinct()->get();
           
            $empleados=Empleado::Empleados()->get();
            $parametrizacionContable=Parametrizacion_Contable::ParametrizacionByNombreFinanciero('ANTICIPO DE EMPLEADO')->first();
            return view('admin.recursosHumanos.empleado.index',['estados'=>$estados,'sucursales'=>Sucursal::sucursales()->get(),'sucursalC'=>$request->get('sucursal_id'),'parametrizacionContable'=>$parametrizacionContable,'PE'=>Punto_Emision::puntos()->get(),'cargos'=>$cargos,'departamentos'=>$departamentos,'cuentas'=>$cuentas, 'tipos'=>$tipo, 'banco'=>$banco,'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        }catch(\Exception $ex){
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function consultar(Request $request)
    {
        try{
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
            $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $cargos=Empleado_Cargo::EmpleadoCargos()->get();
            $departamentos=Empresa_Departamento::Departamentos()->get();
            $cuentas=Cuenta::CuentasMovimiento()->get(); 
            $tipo=Tipo_Empleado::Tipos()->get(); 
            $banco=Banco_Lista::BancoListas()->get();
            $empleados=Empleado::Empleadosbuscar($request->get('sucursal_id'),$request->get('Estado_id'))->get();
            $estados=Empleado::EmpleadosEstado()->select('empleado.empleado_estado')->distinct()->get();

            $parametrizacionContable=Parametrizacion_Contable::ParametrizacionByNombreFinanciero('ANTICIPO DE EMPLEADO')->first();
            return view('admin.recursosHumanos.empleado.index',['estados'=>$estados,'Estado_id'=>$request->get('Estado_id'),'sucursales'=>Sucursal::sucursales()->get(),'sucursalC'=>$request->get('sucursal_id'),'parametrizacionContable'=>$parametrizacionContable,'empleados'=>$empleados,'PE'=>Punto_Emision::puntos()->get(),'cargos'=>$cargos,'departamentos'=>$departamentos,'cuentas'=>$cuentas, 'tipos'=>$tipo, 'banco'=>$banco,'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
     
        }catch(\Exception $ex){
            return redirect('inicio')->with('error2','Oucrrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function buscarByEmpleado($ide){
        return Empleado::EmpleadosRolId($ide)->get();
    }
    public function buscarByEmpleadoBanco($ide){
        return Empleado::EmpleadosRolIdBanco($ide)->get();
    }
    public function presentarEmpleados($id){
        $compra=null;
        $alimentaciones=Alimentacion::Factura($id)->get();
        
        $empleados=Empleado::Empleados()->get();
        $i=0;
        foreach($empleados as $empleado){
            $compra[$i]["idalim"]=0;
            $compra[$i]["ide"]=$empleado->empleado_id;
            $compra[$i]["cedula"]=$empleado->empleado_cedula;
            $compra[$i]["nombre"]=$empleado->empleado_nombre;
            $compra[$i]["valor"]=0.00;
            $compra[$i]["rol"]=null;
            $compra[$i]["rolcm"]=null;
            
           if (count($alimentaciones)>0) {
               foreach ($alimentaciones as $alimentacion) {
                   if ($alimentacion->empleado_id==$empleado->empleado_id) {
                        $compra[$i]["rol"]=$alimentacion->cabecera_rol_id;
                        $compra[$i]["rolcm"]=$alimentacion->cabecera_rol_cm_id;
                        $compra[$i]["valor"]=$alimentacion->alimentacion_valor;
                        $compra[$i]["idalim"]=$alimentacion->alimentacion_id;
                   }
               }
           }
            $i++;
        }
        return $compra;
    }
    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try{
            DB::beginTransaction();
            $parametrizacionContable=Parametrizacion_Contable::ParametrizacionByNombreFinanciero('ANTICIPO DE EMPLEADO')->first();
            $empleado = new Empleado();
            $empleado->empleado_cedula = $request->get('idCedula');
            $empleado->empleado_nombre = $request->get('idNombre');
            $empleado->empleado_telefono = $request->get('idTelefono');
            $empleado->empleado_celular = $request->get('idCelular');
            $empleado->empleado_direccion = $request->get('idDireccion');
            $empleado->empleado_sexo = $request->get('idSexo');
            $empleado->empleado_estatura= $request->get('idEstatura');;
            $empleado->empleado_grupo_sanguineo = $request->get('idGrupoS');
            $empleado->empleado_lugar_nacimiento = $request->get('idLugarNac');
            $empleado->empleado_fecha_nacimiento = $request->get('idFechaNac');
            $empleado->empleado_edad = $request->get('idEdad');
            $empleado->empleado_nacionalidad = $request->get('idNacionalidad');
            $empleado->empleado_estado_civil = $request->get('idEstadoCivil');
            $empleado->empleado_correo = $request->get('idCorreo');
            $empleado->empleado_jornada = $request->get('idJornada');
            $empleado->empleado_cosecha = $request->get('idCosecha');
            $empleado->empleado_carga_familiar = $request->get('idCargaF');
            $empleado->empleado_contacto_nombre = $request->get('idContactoNombre');
            $empleado->empleado_contacto_telefono = $request->get('idContactoTelefono');
            $empleado->empleado_contacto_celular = $request->get('idContactoCelular');
            $empleado->empleado_contacto_direccion = $request->get('idContactoDireccion');
            $empleado->empleado_observacion = $request->get('idObservacion');
            $empleado->empleado_sueldo = $request->get('idSueldo');
            $empleado->empleado_fecha_ingreso = $request->get('idFechaIng');
            $empleado->empleado_fecha_salida = $request->get('idFechaSal');  
            $empleado->empleado_quincena = $request->get('idQuincena');          
            if ($request->get('idHorasEx') == "on"){
                $empleado->empleado_horas_extra ="1";
            }else{
                $empleado->empleado_horas_extra ="0";
            }
            if ($request->get('idAfiliado') == "on"){
                $empleado->empleado_afiliado ="1";
            }else{
                $empleado->empleado_afiliado ="0";
            }
            if ($request->get('idIessA') == "on"){
                $empleado->empleado_iess_asumido ="1";
            }else{
                $empleado->empleado_iess_asumido ="0";
            }
            if ($request->get('idFondosRes') == "on"){
                $empleado->empleado_fondos_reserva ="1";
            }else{
                $empleado->empleado_fondos_reserva ="0";
            }
            if ($request->get('idImpuestoR') == "on"){
                $empleado->empleado_impuesto_renta ="1";
            }else{
                $empleado->empleado_impuesto_renta ="0";
            }
            if ($request->get('idDecimoTer') == "on"){
                $empleado->empleado_decimo_tercero ="1";
            }else{
                $empleado->empleado_decimo_tercero ="0";
            }
            if ($request->get('idDecimoCua') == "on"){
                $empleado->empleado_decimo_cuarto ="1";
            }else{
                $empleado->empleado_decimo_cuarto ="0";
            }
            if ($request->get('idGerente') == "on"){
                $empleado->empleado_iess_gerente ="1";
            }else{
                $empleado->empleado_iess_gerente ="0";
            }
            if($request->get('idObservacion')){
                $empleado->empleado_observacion = $request->get('idObservacion');
            }else{
                $empleado->empleado_observacion = '';
            }
            $empleado->empleado_fecha_afiliacion = $request->get('idFechaAfi');
            $empleado->empleado_fecha_inicioFR = $request->get('idFechaIni');            
            $empleado->empleado_estado=1;
            $empleado->empleado_cuenta_tipo = $request->get('idCuantaTipo');
            $empleado->empleado_cuenta_numero = $request->get('idCuenta');
            $empleado->cargo_id = $request->get('idCargo');
            $empleado->departamento_id = $request->get('idDepartamento');
            $empleado->empleado_cuenta_anticipo=null;
            $empleado->empleado_cuenta_prestamo=null;
            $empleado->tipo_id = $request->get('idTipo');
            $empleado->banco_lista_id = $request->get('idBanco'); 
            if ($parametrizacionContable->parametrizacion_cuenta_general == '0') {
                $cuentap=Cuenta::BuscarByCuenta('ANTICIPOS A EMPLEADOS')->first();
                if (!$cuentap) {
                    $cuentap=Cuenta::BuscarByCuenta('ANTICIPO A EMPLEADO')->first();
                }
                if (!$cuentap) {
                    $cuentap=Cuenta::BuscarByCuenta('ANTICIPOS DE EMPLEADO')->first();
                }
                if (!$cuentap) {
                    $cuentap=Cuenta::BuscarByCuenta('ANTICIPO DE EMPLEADO')->first();
                }
                if ($cuentap) {
                    $cuentaapdre=Cuenta::BuscarByCuenta($cuentap->cuenta_id)->max('cuenta_secuencial');
                    $sec=1;
                    if ($cuentaapdre) {
                        $sec=$sec+$cuentaapdre;
                    }
                    $numerocuenta=$cuentap->cuenta_numero.'.'.$sec;
                    $cuentaa = new Cuenta();
                    $cuentaa->cuenta_numero =$numerocuenta;
                    $cuentaa->cuenta_nombre = 'ANTICIPOS A EMPLEADOS -'.$empleado->empleado_nombre;
                    $cuentaa->cuenta_secuencial = $sec;
                    $cuentaa->cuenta_nivel = $cuentap->cuenta_secuencial+1;
                    $cuentaa->cuenta_estado = 1;
                    $cuentaa->empresa_id = Auth::user()->empresa_id;
                    $cuentaa->save();
                    /*Inicio de registro de auditoria */
                    $auditoria = new generalController();
                    $auditoria->registrarAuditoria('Registro de cuenta -> ANTICIPOS A EMPLEADOS -'.$empleado->empleado_nombre, '0', 'Numero de la cuenta registrada es -> '.$numerocuenta);
                    $empleado->empleado_cuenta_anticipo=$cuentaa->cuenta_id;
                }
            }
            if ($parametrizacionContable->parametrizacion_cuenta_general == '0') {
                $cuentapr=Cuenta::BuscarByCuenta('PRESTAMOS A EMPLEADOS')->first();
                if (!$cuentap) {
                    $cuentap=Cuenta::BuscarByCuenta('PRESTAMO A EMPLEADO')->first();
                }
                if (!$cuentap) {
                    $cuentap=Cuenta::BuscarByCuenta('PRESTAMO DE EMPLEADO')->first();
                }
                if (!$cuentap) {
                    $cuentap=Cuenta::BuscarByCuenta('PRESTAMOS DE EMPLEADO')->first();
                }
                if ($cuentapr) {
                    $cuentaapdre=Cuenta::BuscarByCuenta($cuentapr->cuenta_id)->max('cuenta_secuencial');
                    $sec=1;
                    if ($cuentaapdre) {
                        $sec=$sec+$cuentaapdre;
                    }
                    $numerocuenta=$cuentapr->cuenta_numero.'.'.$sec;
                    $cuentap = new Cuenta();
                    $cuentap->cuenta_numero =$numerocuenta;
                    $cuentap->cuenta_nombre = 'PRESTAMOS A EMPLEADOS -'.$empleado->empleado_nombre;
                    $cuentap->cuenta_secuencial = $sec;
                    $cuentap->cuenta_nivel = $cuentapr->cuenta_secuencial+1;
                    $cuentap->cuenta_estado = 1;
                    $cuentap->empresa_id = Auth::user()->empresa_id;
                    $cuentap->save();
                    /*Inicio de registro de auditoria */
                    $auditoria = new generalController();
                    $auditoria->registrarAuditoria('Registro de cuenta -> PRESTAMOS A EMPLEADOS -'.$empleado->empleado_nombre, '0', 'Numero de la cuenta registrada es -> '.$numerocuenta);
                    $empleado->empleado_cuenta_prestamo=$cuentap->cuenta_id;
                }
            }
            $empleado->save();
            /*Inicio de registro de auditoria */
            $auditoria = new generalController();
            $auditoria->registrarAuditoria('Registro de empleado -> '.$request->get('idNombre'),'0','');
            /*Fin de registro de auditoria */
            DB::commit();
            return redirect('empleado')->with('success','Datos guardados exitosamente');
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('empleado')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try{
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $cargos=Empleado_Cargo::EmpleadoCargos()->get();
            $departamentos=Empresa_Departamento::Departamentos()->get();
            $cuentas=Cuenta::CuentasMovimiento()->get(); 
            $tipo=Tipo_Empleado::Tipos()->get();      
            $banco=Banco_Lista::BancoListas()->get();
            $empleado=Empleado::findOrFail($id);
            $parametrizacionContable=Parametrizacion_Contable::ParametrizacionByNombreFinanciero('ANTICIPO DE EMPLEADO')->first();    
            if($empleado){
                return view('admin.recursosHumanos.empleado.ver',['parametrizacionContable'=>$parametrizacionContable,'empleado'=>$empleado, 'PE'=>Punto_Emision::puntos()->get(),'cargos'=>$cargos,'departamentos'=>$departamentos,'cuentas'=>$cuentas, 'tipo'=>$tipo, 'banco'=>$banco,'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
            }else{
                return redirect('/denegado');
            }
        }catch(\Exception $ex){
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        try{
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $cargos=Empleado_Cargo::EmpleadoCargos()->get();
            $departamentos=Empresa_Departamento::Departamentos()->get();
            $cuentas=Cuenta::CuentasMovimiento()->get(); 
            $tipo=Tipo_Empleado::Tipos()->get(); 
            $banco=Banco_Lista::BancoListas()->get();
            $empleado=Empleado::findOrFail($id);
            $parametrizacionContable=Parametrizacion_Contable::ParametrizacionByNombreFinanciero('ANTICIPO DE EMPLEADO')->first();     
            if($empleado){
                return view('admin.recursosHumanos.empleado.editar',['parametrizacionContable'=>$parametrizacionContable,'empleado'=>$empleado,'PE'=>Punto_Emision::puntos()->get(),'cargos'=>$cargos,'departamentos'=>$departamentos,'cuentas'=>$cuentas, 'tipo'=>$tipo, 'banco'=>$banco, 'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
            }else{
                return redirect('/denegado');
            }
        }catch(\Exception $ex){
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try{
            DB::beginTransaction();
         
            $empleado = Empleado::findOrFail($id);
            $empleado->empleado_cedula = $request->get('idCedula');
            $empleado->empleado_nombre = $request->get('idNombre');
            $empleado->empleado_telefono = $request->get('idTelefono');
            $empleado->empleado_celular = $request->get('idCelular');
            $empleado->empleado_direccion = $request->get('idDireccion');
            $empleado->empleado_sexo = $request->get('idSexo');
            $empleado->empleado_estatura= $request->get('idEstatura');;
            $empleado->empleado_grupo_sanguineo = $request->get('idGrupoS');
            $empleado->empleado_lugar_nacimiento = $request->get('idLugarNac');
            $empleado->empleado_fecha_nacimiento = $request->get('idFechaNac');
            $empleado->empleado_edad = $request->get('idEdad');
            $empleado->empleado_nacionalidad = $request->get('idNacionalidad');
            $empleado->empleado_estado_civil = $request->get('idEstadoCivil');
            $empleado->empleado_correo = $request->get('idCorreo');
            $empleado->empleado_jornada = $request->get('idJornada');
            $empleado->empleado_cosecha = $request->get('idCosecha');
            $empleado->empleado_carga_familiar = $request->get('idCargaF');
            $empleado->empleado_contacto_nombre = $request->get('idContactoNombre');
            $empleado->empleado_contacto_telefono = $request->get('idContactoTelefono');
            $empleado->empleado_contacto_celular = $request->get('idContactoCelular');
            $empleado->empleado_contacto_direccion = $request->get('idContactoDireccion');
            $empleado->empleado_observacion = $request->get('idObservacion');
            $empleado->empleado_sueldo = $request->get('idSueldo');
            $empleado->empleado_fecha_ingreso = $request->get('idFechaIng');
            $empleado->empleado_fecha_salida = $request->get('idFechaSal');   
            $empleado->empleado_quincena = $request->get('idQuincena');   
            if ($request->get('idHorasEx') == "on"){
                $empleado->empleado_horas_extra ="1";
            }else{
                $empleado->empleado_horas_extra ="0";
            }
            if ($request->get('idAfiliado') == "on"){
                $empleado->empleado_afiliado ="1";
            }else{
                $empleado->empleado_afiliado ="0";
            }
            if ($request->get('idIessA') == "on"){
                $empleado->empleado_iess_asumido ="1";
            }else{
                $empleado->empleado_iess_asumido ="0";
            }
            if ($request->get('idFondosRes') == "on"){
                $empleado->empleado_fondos_reserva ="1";
            }else{
                $empleado->empleado_fondos_reserva ="0";
            }
            if ($request->get('idImpuestoR') == "on"){
                $empleado->empleado_impuesto_renta ="1";
            }else{
                $empleado->empleado_impuesto_renta ="0";
            }
            if ($request->get('idDecimoTer') == "on"){
                $empleado->empleado_decimo_tercero ="1";
            }else{
                $empleado->empleado_decimo_tercero ="0";
            }
            if ($request->get('idDecimoCua') == "on"){
                $empleado->empleado_decimo_cuarto ="1";
            }else{
                $empleado->empleado_decimo_cuarto ="0";
            }
            if ($request->get('idGerente') == "on"){
                $empleado->empleado_iess_gerente ="1";
            }else{
                $empleado->empleado_iess_gerente ="0";
            }
            if($request->get('idObservacion')){
                $empleado->empleado_observacion = $request->get('idObservacion');
            }else{
                $empleado->empleado_observacion = '';
            }
            $empleado->empleado_estado ="0";
            if($request->get('idEstado')== "on"){
                $empleado->empleado_estado ="1";
            }
            $empleado->empleado_fecha_afiliacion = $request->get('idFechaAfi');
            $empleado->empleado_fecha_inicioFR = $request->get('idFechaIni');            
            $empleado->empleado_cuenta_tipo = $request->get('idCuantaTipo');
            $empleado->empleado_cuenta_numero = $request->get('idCuenta');
            $empleado->cargo_id = $request->get('idCargo');
            $empleado->departamento_id = $request->get('idDepartamento');
            $empleado->empleado_cuenta_anticipo = $request->get('idCuentaAnti');
            $empleado->empleado_cuenta_prestamo = $request->get('idCuentaPres');
            $empleado->tipo_id = $request->get('idTipo');
            $empleado->banco_lista_id = $request->get('idBanco');                        
            $empleado->save();
            /*Inicio de registro de auditoria */
            $auditoria = new generalController();
            $auditoria->registrarAuditoria('Actualizacion empleado -> '.$request->get('idNombre'),'0','');
            /*Fin de registro de auditoria */
            DB::commit();
            return redirect('empleado')->with('success','Datos actualizados exitosamente');
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('empleado')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try{
            DB::beginTransaction();
            $empleado = Empleado::findOrFail($id);
            $empleado->delete();
            /*Inicio de registro de auditoria */
            $auditoria = new generalController();
            $auditoria->registrarAuditoria('Eliminacion de Empleado -> '.$empleado->empleado_nombre.' con numero de cedula -> '.$empleado->empleado_cedula,'0','');
            /*Fin de registro de auditoria */
            DB::commit();
            return redirect('empleado')->with('success','Datos eliminados exitosamente');
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('empleado')->with('error','El registro no pudo ser borrado, tiene resgitros adjuntos.');
        }
    }
    public function excelEmpleado(){
        try{
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            return view('admin.recursosHumanos.empleado.cargarExcel',['PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        }
        catch(\Exception $ex){      
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function CargarExcelEmpleado(Request $request){
        if (isset($_POST['cargar'])){
            return $this->cargarguardar($request);
        }
        if (isset($_POST['guardar'])){
            return $this->cargarguardar($request);
        }
    }
    public function cargarguardar(Request $request){
        try{
            $mensaje='';            
            DB::beginTransaction();
            $parametrizacionContable=Parametrizacion_Contable::ParametrizacionByNombreFinanciero('ANTICIPO DE EMPLEADO')->first();     
            if($request->file('excelEmpl')->isValid()){
                $empresa = Empresa::empresa()->first();
                $name = $empresa->empresa_ruc. '.' .$request->file('excelEmpl')->getClientOriginalExtension();
                $path = $request->file('excelEmpl')->move(public_path().'\temp', $name); 
                $array = Excel::toArray(new Empleado, $path);   
                for($i=1;$i < count($array[0]);$i++){
                    $empleadoCed = Empleado::EmpleadoByCed($array[0][$i][0])->first();
                    if(isset($empleadoCed->empleado_cedula)){                                                                  
                        $mensaje = $mensaje.' '.$array[0][$i][0];                        
                    }else{
                        $empleado = new Empleado();
                        $empleado->empleado_cedula = $array[0][$i][0];
                        $empleado->empleado_nombre = $array[0][$i][1];
                        $empleado->empleado_telefono = $array[0][$i][2];
                        $empleado->empleado_direccion = $array[0][$i][3];
                        $Excel_date2 = $array[0][$i][4]; 
                        $unix_date2 = ($Excel_date2 - 25569) * 86400;
                        $Excel_date2 = 25569 + ($unix_date2 / 86400);
                        $unix_date2 = ($Excel_date2 - 25569) * 86400;
                        $empleado->empleado_fecha_ingreso = gmdate("Y-m-d", $unix_date2);
                        $empleado->empleado_estado = $array[0][$i][5];
                        $empleado->empleado_sueldo = floatval($array[0][$i][7]);
                        $empleado->empleado_sexo = $array[0][$i][19];
                        $empleado->empleado_estatura = floatval($array[0][$i][20]);
                        $empleado->empleado_grupo_sanguineo = $array[0][$i][21];
                        $empleado->empleado_lugar_nacimiento = $array[0][$i][22];
                        $Excel_date = $array[0][$i][23]; 
                        $unix_date = ($Excel_date - 25569) * 86400;
                        $Excel_date = 25569 + ($unix_date / 86400);
                        $unix_date = ($Excel_date - 25569) * 86400;
                        $empleado->empleado_fecha_nacimiento = gmdate("Y-m-d", $unix_date);
                        $empleado->empleado_edad = $array[0][$i][24];
                        $empleado->empleado_nacionalidad = $array[0][$i][25];
                        $empleado->empleado_estado_civil = $array[0][$i][26];
                        if($array[0][$i][27]!= ''){
                            $empleado->empleado_celular = $array[0][$i][27];
                        }else{
                            $empleado->empleado_celular = '000000000';
                        }
                        if($array[0][$i][28]!= ''){
                            $empleado->empleado_correo = $array[0][$i][28];
                        }else{
                            $empleado->empleado_correo = 'S/N';
                        }
                        $empleado->empleado_carga_familiar = floatval($array[0][$i][29]);
                        $empleado->empleado_contacto_nombre = $array[0][$i][31];
                        if($array[0][$i][32] != ''){
                            $empleado->empleado_contacto_telefono = $array[0][$i][32];
                        }else{
                            $empleado->empleado_contacto_telefono = 'S/N';
                        }
                        if($array[0][$i][32] != ''){
                            $empleado->empleado_contacto_celular = $array[0][$i][32];
                        }else{
                            $empleado->empleado_contacto_celular = 'S/N';
                        }
                        if($array[0][$i][33] != ''){
                            $empleado->empleado_contacto_direccion = $array[0][$i][33];
                        }else{
                            $empleado->empleado_contacto_direccion = 'S/N';
                        }
                        if($array[0][$i][37] != ''){
                            $empleado->empleado_observacion = $array[0][$i][37];
                        }else{
                            $empleado->empleado_observacion = 'SIN OBSERVACION';
                        }
                        
                        //$empleado->empleado_fecha_salida = '';
                        $empleado->empleado_horas_extra = $array[0][$i][10];
                        $empleado->empleado_afiliado = $array[0][$i][14];
                        $empleado->empleado_iess_asumido = $array[0][$i][17];
                        $empleado->empleado_fondos_reserva = $array[0][$i][11];
                        $Excel_date3 = $array[0][$i][15]; 
                        $unix_date3 = ($Excel_date3 - 25569) * 86400;
                        $Excel_date3 = 25569 + ($unix_date3 / 86400);
                        $unix_date3 = ($Excel_date3 - 25569) * 86400;
                        $empleado->empleado_fecha_afiliacion = gmdate("Y-m-d", $unix_date3);
                        $Excel_date4 = $array[0][$i][41]; 
                        $unix_date4 = ($Excel_date4 - 25569) * 86400;
                        $Excel_date4 = 25569 + ($unix_date4 / 86400);
                        $unix_date4 = ($Excel_date4 - 25569) * 86400;
                        $empleado->empleado_fecha_inicioFR = gmdate("Y-m-d", $unix_date4);
                        $empleado->empleado_impuesto_renta = $array[0][$i][18];
                        $empleado->empleado_decimo_tercero = 0;
                        $empleado->empleado_decimo_cuarto = 0;
                        $empleado->empleado_cuenta_tipo = $array[0][$i][42];
                        $empleado->empleado_cuenta_numero = $array[0][$i][44];
                        $empleado->empleado_jornada = $array[0][$i][45];
                        $empleado->empleado_cosecha = $array[0][$i][46];

                        //llaves FORANEAS
                        $cargoEmpl = Empleado_Cargo::EmpleadoByNombre($array[0][$i][6])->first();
                        if(isset($cargoEmpl->empleado_cargo_id)){
                            $empleado->cargo_id = $cargoEmpl->empleado_cargo_id;
                        }else{
                            $emplCargo = new Empleado_Cargo();
                            $emplCargo->empleado_cargo_nombre = strtoupper($array[0][$i][6]);                                                    
                            $emplCargo->empleado_cargo_estado  = 1;
                            $emplCargo->empresa_id = Auth::user()->empresa_id;
                            $emplCargo->save();
                            /*Inicio de registro de auditoria */
                            $auditoria = new generalController();
                            $auditoria->registrarAuditoria('Registro de Cargo de Empleado -> '.$array[0][$i][14],'0','');
                            $empleado->cargo()->associate($emplCargo);
                        }
                        $departamento = Empresa_Departamento::DepartamentoByNomb($array[0][$i][12])->first();
                        if(isset($departamento->departamento_id)){
                            $empleado->departamento_id = $departamento->departamento_id;
                        }
                        if($array[0][$i][43]!= ''){
                            $banco = Banco_Lista::BancoListaByNom($array[0][$i][43])->first();                       
                            if(isset($banco->banco_lista_id)){
                                $empleado->banco_lista_id = $banco->banco_lista_id;                        
                            }else{
                                $nomBanco = new Banco_Lista();
                                $nomBanco->banco_lista_nombre = $array[0][$i][43];                                                    
                                $nomBanco->banco_lista_estado  = 1;
                                $nomBanco->empresa_id = Auth::user()->empresa_id;
                                $nomBanco->save();
                                /*Inicio de registro de auditoria */
                                $auditoria = new generalController();
                                $auditoria->registrarAuditoria('Registro de Banco Lista -> '.$array[0][$i][14],'0','');
                                $empleado->banco()->associate($nomBanco);
                            }   
                        }else{
                            $banco = Banco_Lista::BancoListaByNom('Banco de Pichincha')->first();
                            $empleado->banco_lista_id = $banco->banco_lista_id; 
                        }     
                        //$empleado->tipo_id = $array[0][$i][44];
                        //campos vacios                        
                        //$empleado->empleado_cuenta_anticipo = $array[0][$i][44];
                        //$empleado->empleado_cuenta_prestamo = $array[0][$i][44];
                        if ($parametrizacionContable->parametrizacion_cuenta_general == '0') {
                            $cuentap=Cuenta::BuscarByCuenta('ANTICIPOS A EMPLEADOS')->first();
                    
                            if ($cuentap) {
                                $cuentaapdre=Cuenta::BuscarByCuenta($cuentap->cuenta_id)->max('cuenta_secuencial');
                                $sec=1;
                                if ($cuentaapdre) {
                                    $sec=$sec+$cuentaapdre;
                                }
                                $numerocuenta=$cuentap->cuenta_numero.'.'.$sec;
                                $cuentaa = new Cuenta();
                                $cuentaa->cuenta_numero =$numerocuenta;
                                $cuentaa->cuenta_nombre = 'ANTICIPOS A EMPLEADOS -'.$empleado->empleado_nombre;
                                $cuentaa->cuenta_secuencial = $sec;
                                $cuentaa->cuenta_nivel = $cuentap->cuenta_secuencial+1;
                                $cuentaa->cuenta_estado = 1;
                                $cuentaa->empresa_id = Auth::user()->empresa_id;
                                $cuentaa->save();
                                /*Inicio de registro de auditoria */
                                $auditoria = new generalController();
                                $auditoria->registrarAuditoria('Registro de cuenta -> ANTICIPOS A EMPLEADOS -'.$empleado->empleado_nombre, '0', 'Numero de la cuenta registrada es -> '.$numerocuenta);
                                $empleado->empleado_cuenta_anticipo=$cuentaa->cuenta_id;
                            }
                        }
                        if ($parametrizacionContable->parametrizacion_cuenta_general == '0') {
                            $cuentapr=Cuenta::BuscarByCuenta('PRESTAMOS A EMPLEADOS')->first();
                    
                            if ($cuentapr) {
                                $cuentaapdre=Cuenta::BuscarByCuenta($cuentapr->cuenta_id)->max('cuenta_secuencial');
                                $sec=1;
                                if ($cuentaapdre) {
                                    $sec=$sec+$cuentaapdre;
                                }
                                $numerocuenta=$cuentapr->cuenta_numero.'.'.$sec;
                                $cuentap = new Cuenta();
                                $cuentap->cuenta_numero =$numerocuenta;
                                $cuentap->cuenta_nombre = 'PRESTAMOS A EMPLEADOS -'.$empleado->empleado_nombre;
                                $cuentap->cuenta_secuencial = $sec;
                                $cuentap->cuenta_nivel = $cuentapr->cuenta_secuencial+1;
                                $cuentap->cuenta_estado = 1;
                                $cuentap->empresa_id = Auth::user()->empresa_id;
                                $cuentap->save();
                                /*Inicio de registro de auditoria */
                                $auditoria = new generalController();
                                $auditoria->registrarAuditoria('Registro de cuenta -> PRESTAMOS A EMPLEADOS -'.$empleado->empleado_nombre, '0', 'Numero de la cuenta registrada es -> '.$numerocuenta);
                                $empleado->empleado_cuenta_prestamo=$cuentap->cuenta_id;
                            }
                        }
                        $empleado->save();
                        /*Inicio de registro de auditoria */
                        $auditoria = new generalController();
                        $auditoria->registrarAuditoria('Registro de EMPLEADO -> '.$array[0][$i][1].'con cedula de ->'.$array[0][$i][0].' mediante Excel.','0','');
                        /*Fin de registro de auditoria */
                  }  
                }
            }
           DB::commit();
            if($mensaje ==''){
                return redirect('empleado')->with('success','Datos guardados exitosamente');               
            }else{
                return redirect('empleado')->with('success','Datos guardados exitosamente')->with('error2','Algunos Datos no se registraron cedula repetida: '.' '.$mensaje);
            }
           
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('empleado')->with('error2','Oucrrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function excelEmpleadoUpdate(){
        try{
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            return view('admin.recursosHumanos.empleado.updateExcel',['PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        }
        catch(\Exception $ex){      
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function UpdateExcelEmpleado(Request $request){       
        if (isset($_POST['cargar1'])){
            return $this->cargarguardar1($request);
        }
        if (isset($_POST['guardar1'])){
            return $this->cargarguardar1($request);
        }
    }
    public function cargarguardar1(Request $request){        
        try{
            $contador=0;            
            DB::beginTransaction();
            if($request->file('excelEmpl')->isValid()){
                $empresa = Empresa::findOrFail(Auth::user()->empresa_id);                
                $name = $empresa->empresa_ruc. '.' .$request->file('excelEmpl')->getClientOriginalExtension();
                $path = $request->file('excelEmpl')->move(public_path().'\temp', $name); 
                $array = Excel::toArray(new Empleado, $path);   
                for($i=1;$i < count($array[0]);$i++){                        
                        $empleadoCed = Empleado::EmpleadoByCed($array[0][$i][0])->first();
                        if(isset($empleadoCed->empleado_cedula)){
                            $Excel_date2 = $array[0][$i][1]; 
                            $unix_date2 = ($Excel_date2 - 25569) * 86400;
                            $Excel_date2 = 25569 + ($unix_date2 / 86400);
                            $unix_date2 = ($Excel_date2 - 25569) * 86400;
                            $empleadoCed->empleado_fecha_ingreso = gmdate("Y-m-d", $unix_date2);
                            $empleadoCed->save();                        
                            /*Inicio de registro de auditoria */
                            $auditoria = new generalController();
                            $auditoria->registrarAuditoria('Registro de Actualizacion de EMPLEADO -> '.'con cedula de ->'.$array[0][$i][0].' mediante Excel.','0','');
                            /*Fin de registro de auditoria */
                            $contador = $contador+1;
                        }
                    
                }
            }
           DB::commit();            
            return redirect('empleado')->with('success','Datos guardados exitosamente')->with('success','Se actualizaron : '.' '.$contador.''.' '.'empleados');           
           
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('empleado')->with('error2','Oucrrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }

    public function delete($id)
    {
        try{
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $cargos=Empleado_Cargo::EmpleadoCargos()->get();
            $departamentos=Empresa_Departamento::Departamentos()->get();
            $cuentas=Cuenta::CuentasMovimiento()->get(); 
            $tipo=Tipo_Empleado::Tipos()->get();      
            $banco=Banco_Lista::BancoListas()->get();
            $empleado=Empleado::findOrFail($id);
            $parametrizacionContable=Parametrizacion_Contable::ParametrizacionByNombreFinanciero('ANTICIPO DE EMPLEADO')->first();    
            if($empleado){
                return view('admin.recursosHumanos.empleado.eliminar',['parametrizacionContable'=>$parametrizacionContable,'empleado'=>$empleado, 'PE'=>Punto_Emision::puntos()->get(),'cargos'=>$cargos,'departamentos'=>$departamentos,'cuentas'=>$cuentas, 'tipo'=>$tipo, 'banco'=>$banco,'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
            }else{
                return redirect('/denegado');
            }
        }catch(\Exception $ex){
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    
    public function fichaEmpleadoImprime($id){
        $empleado = Empleado::findOrFail($id);
        $empresa = Empresa::findOrFail(Auth::user()->empresa_id);      
        $ruta = public_path().'/fichasEmpleados/'.$empresa->empresa_ruc;
        echo "$ruta";
        if (!is_dir($ruta)) {
            mkdir($ruta, 0777, true);
        }
        $nombreArchivo = 'FE-'.$empleado->empleado_nombre;
        $view =  \View::make('admin.formatosPDF.fichaEmpleado', ['empleado'=>$empleado,'empresa'=>$empresa]);
        //PDF::loadHTML($view)->save($ruta.'/'.$nombreArchivo)->download($nombreArchivo.'.pdf');
        return PDF::loadHTML($view)->save($ruta.'/'.$nombreArchivo)->stream('ficha.pdf');
    }

    public function buscarEmpleadoByNombreCedula(Request $request){
        return Empleado::buscarEmpleadoByNombreCedula($request->buscar)->get();
    }
}
