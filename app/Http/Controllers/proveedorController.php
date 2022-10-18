<?php

namespace App\Http\Controllers;
use App\Models\Proveedor;
use App\Models\Punto_Emision;
use App\Models\Cuenta;
use App\Models\Ciudad;
use App\Models\Tipo_Identificacion;
use App\Models\Tipo_Sujeto;
use App\Models\Categoria_Proveedor;
use App\Http\Controllers\Controller;
use App\Models\Cuenta_Pagar;
use App\Models\Empresa;
use App\Models\Pais;
use App\Models\Parametrizacion_Contable;
use App\Models\Provincia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;


class proveedorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try{
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $cuentas=Cuenta::CuentasMovimiento()->get();
            $ciudades=Ciudad::ciudades()->get();
            $tipoIdentificaciones=Tipo_Identificacion::tipoIdentificaciones()->get();
            $tipoSujetos=Tipo_Sujeto::tipoSujetos()->get();       
            $categoriaProveedores=Categoria_Proveedor::categoriaproveedores()->get();      
            $proveedores=Proveedor::proveedores()->get();
            $parametrizacionContable=Parametrizacion_Contable::ParametrizacionByNombreFinanciero('CUENTA POR PAGAR')->first();
            $parametrizacionContableProveedor=Parametrizacion_Contable::ParametrizacionByNombreFinanciero('ANTICIPO DE PROVEEDOR')->first();
            return view('admin.compras.proveedor.index',['parametrizacionContableProveedor'=>$parametrizacionContableProveedor,'parametrizacionContable'=>$parametrizacionContable,'proveedores'=>$proveedores,'PE'=>Punto_Emision::puntos()->get(),'cuentas'=>$cuentas,'ciudades'=>$ciudades, 'tipoIdentificaciones'=>$tipoIdentificaciones,'tipoSujetos'=>$tipoSujetos,'categoriaProveedores'=>$categoriaProveedores,'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        }
        catch(\Exception $ex){      
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        try{
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $cuentas=Cuenta::CuentasMovimiento()->get();
            $ciudades=Ciudad::ciudades()->get();
            $tipoIdentificaciones=Tipo_Identificacion::tipoIdentificaciones()->get();
            $tipoSujetos=Tipo_Sujeto::tipoSujetos()->get();       
            $categoriaProveedores=Categoria_Proveedor::categoriaproveedores()->get();      
            $proveedores=Proveedor::proveedores()->get();
            $parametrizacionContable=Parametrizacion_Contable::ParametrizacionByNombreFinanciero('CUENTA POR PAGAR')->first();
            $parametrizacionContableProveedor=Parametrizacion_Contable::ParametrizacionByNombreFinanciero('ANTICIPO DE PROVEEDOR')->first();
            return view('admin.compras.proveedor.create',['parametrizacionContableProveedor'=>$parametrizacionContableProveedor,'parametrizacionContable'=>$parametrizacionContable,'proveedores'=>$proveedores,'PE'=>Punto_Emision::puntos()->get(),'cuentas'=>$cuentas,'ciudades'=>$ciudades, 'tipoIdentificaciones'=>$tipoIdentificaciones,'tipoSujetos'=>$tipoSujetos,'categoriaProveedores'=>$categoriaProveedores,'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        }
        catch(\Exception $ex){      
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
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
            $parametrizacionContable=Parametrizacion_Contable::ParametrizacionByNombreFinanciero('CUENTA POR PAGAR')->first();
            $parametrizacionContableProveedor=Parametrizacion_Contable::ParametrizacionByNombreFinanciero('ANTICIPO DE EMPLEADO')->first();
            $proveedor = new Proveedor();
            $proveedor->proveedor_ruc = $request->get('idRuc');
            $proveedor->proveedor_nombre = $request->get('idNombre');
            $proveedor->proveedor_nombre_comercial = $request->get('idNombreComercial');
            $proveedor->proveedor_gerente = $request->get('idGerente');
            $proveedor->proveedor_direccion = $request->get('idDireccion');
            $proveedor->proveedor_telefono = $request->get('idTelefono');
            $proveedor->proveedor_celular = $request->get('idCelular');
            $proveedor->proveedor_email = $request->get('idEmail');
            $proveedor->proveedor_actividad = $request->get('idActividad');
            $proveedor->proveedor_fecha_ingreso = $request->get('idFecha');   
            if(Auth::user()->empresa->empresa_contabilidad == '1'){
                $proveedor->proveedor_tipo =$request->get('idTipo');       
                if ($request->get('idLlevacontabilidad') == "on"){
                    $proveedor->proveedor_lleva_contabilidad ="1";
                }else{
                    $proveedor->proveedor_lleva_contabilidad ="0";
                }                     
                if ($request->get('idContribuyente') == "on"){
                    $proveedor->proveedor_contribuyente ="1";
                }else{
                    $proveedor->proveedor_contribuyente ="0";
                }
                $proveedor->proveedor_cuenta_pagar = $request->get('idCuentaxpagar');
                $proveedor->proveedor_cuenta_anticipo = $request->get('idCuentaAnticipo');
            }
            $proveedor->tipo_sujeto_id = $request->get('idSujeto');
            $proveedor->tipo_identificacion_id = $request->get('idTidentificacion');
            $proveedor->ciudad_id = $request->get('idCiudad');          
            $proveedor->categoria_proveedor_id = $request->get('idCategoria');
            $proveedor->proveedor_estado  = 1; 
            if ($parametrizacionContableProveedor->parametrizacion_cuenta_general == '0') {
                $cuentap=Cuenta::BuscarByCuenta('ANTICIPO DE PROVEEDOR')->first();
                if (!$cuentap) {
                    $cuentap=Cuenta::BuscarByCuenta('ANTICIPOS DE PROVEEDOR')->first();  
                }
                if (!$cuentap) {
                    $cuentap=Cuenta::BuscarByCuenta('ANTICIPO A PROVEEDOR')->first();  
                }
                if (!$cuentap) {
                    $cuentap=Cuenta::BuscarByCuenta('ANTICIPOS A PROVEEDOR')->first();  
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
                    $cuentaa->cuenta_nombre = 'ANTICIPO DE PROVEEDOR -'.$proveedor->proveedor_nombre;
                    $cuentaa->cuenta_secuencial = $sec;
                    $cuentaa->cuenta_nivel = $cuentap->cuenta_secuencial+1;
                    $cuentaa->cuenta_estado = 1;
                    $cuentaa->empresa_id = Auth::user()->empresa_id;
                    $cuentaa->save();
                    /*Inicio de registro de auditoria */
                    $auditoria = new generalController();
                    $auditoria->registrarAuditoria('Registro de cuenta -> ANTICIPO DE PROVEEDOR -'.$proveedor->proveedor_nombre, '0', 'Numero de la cuenta registrada es -> '.$numerocuenta);
                    $proveedor->proveedor_cuenta_anticipo=$cuentaa->cuenta_id;
                }
            }
            if ($parametrizacionContable->parametrizacion_cuenta_general == '0') {
                $cuentapr=Cuenta::BuscarByCuenta('CUENTA POR PAGAR')->first();
                if (!$cuentapr) {
                    $cuentapr=Cuenta::BuscarByCuenta('CUENTAS POR PAGAR')->first();  
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
                    $cuentap->cuenta_nombre = 'CUENTA POR PAGAR -'.$proveedor->proveedor_nombre;
                    $cuentap->cuenta_secuencial = $sec;
                    $cuentap->cuenta_nivel = $cuentapr->cuenta_secuencial+1;
                    $cuentap->cuenta_estado = 1;
                    $cuentap->empresa_id = Auth::user()->empresa_id;
                    $cuentap->save();
                    /*Inicio de registro de auditoria */
                    $auditoria = new generalController();
                    $auditoria->registrarAuditoria('Registro de cuenta -> CUENTA POR PAGAR -'.$proveedor->proveedor_nombre, '0', 'Numero de la cuenta registrada es -> '.$numerocuenta);
                    $proveedor->proveedor_cuenta_pagar=$cuentap->cuenta_id;
                }
            }
            $proveedor->save();
            /*Inicio de registro de auditoria */
            $auditoria = new generalController();
            $auditoria->registrarAuditoria('Registro de Proveedor -> '.$request->get('idNombre').' con Cedula -> '.$request->get('idRuc'),'0','con el tipo de Identificacion ->'.$request->get('idTidentificacion'));
            /*Fin de registro de auditoria */
            DB::commit();
            return redirect('proveedor')->with('success','Datos guardados exitosamente');
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('proveedor')->with('error2','Oucrrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
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
            $parametrizacionContable=Parametrizacion_Contable::ParametrizacionByNombreFinanciero('CUENTA POR PAGAR')->first();
            $proveedor=Proveedor::proveedor($id)->first();
            if($proveedor){
                return view('admin.compras.proveedor.ver',['parametrizacionContable'=>$parametrizacionContable,'proveedor'=>$proveedor, 'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
            }else{
                return redirect('/denegado');
            }
        }
        catch(\Exception $ex){      
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
            
            $parametrizacionContable=Parametrizacion_Contable::ParametrizacionByNombreFinanciero('CUENTA POR PAGAR')->first();
            $parametrizacionContableProveedor=Parametrizacion_Contable::ParametrizacionByNombreFinanciero('ANTICIPO DE PROVEEDOR')->first();
            $cuentas=Cuenta::CuentasMovimiento()->get();
            $ciudades=Ciudad::ciudades()->get();
            $tipoIdentificaciones=Tipo_Identificacion::tipoIdentificaciones()->get();
            $tipoSujetos=Tipo_Sujeto::tipoSujetos()->get();       
            $categoriaProveedores=Categoria_Proveedor::categoriaproveedores()->get();      
            $proveedor=Proveedor::proveedor($id)->first();   
            if($proveedor){
                return view('admin.compras.proveedor.editar',['parametrizacionContableProveedor'=>$parametrizacionContableProveedor,'parametrizacionContable'=>$parametrizacionContable,'proveedor'=>$proveedor, 'cuentas'=>$cuentas,'ciudades'=>$ciudades,'tipoIdentificaciones'=>$tipoIdentificaciones,'tipoSujetos'=>$tipoSujetos,'categoriaProveedores'=>$categoriaProveedores, 'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
            }else{
                return redirect('/denegado');
            }
        }
        catch(\Exception $ex){      
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
            $parametrizacionContable=Parametrizacion_Contable::ParametrizacionByNombreFinanciero('CUENTA POR PAGAR')->first();
            $parametrizacionContableProveedor=Parametrizacion_Contable::ParametrizacionByNombreFinanciero('ANTICIPO DE PROVEEDOR')->first();
            $proveedor = Proveedor::findOrFail($id);       
            $proveedor->proveedor_ruc = $request->get('idRuc');
            $proveedor->proveedor_nombre = $request->get('idNombre');
            $proveedor->proveedor_nombre_comercial = $request->get('idNombreComercial');
            $proveedor->proveedor_gerente = $request->get('idGerente');
            $proveedor->proveedor_direccion = $request->get('idDireccion');
            $proveedor->proveedor_telefono = $request->get('idTelefono');
            $proveedor->proveedor_celular = $request->get('idCelular');
            $proveedor->proveedor_email = $request->get('idEmail');
            $proveedor->proveedor_actividad = $request->get('idActividad');
            $proveedor->proveedor_fecha_ingreso = $request->get('idFecha');
            if(Auth::user()->empresa->empresa_contabilidad == '1'){
                $proveedor->proveedor_tipo =$request->get('idTipo');             
                if ($request->get('idLlevacontabilidad') == "on"){
                    $proveedor->proveedor_lleva_contabilidad ="1";
                }else{
                    $proveedor->proveedor_lleva_contabilidad ="0";
                }                     
                if ($request->get('idContribuyente') == "on"){
                    $proveedor->proveedor_contribuyente ="1";
                }else{
                    $proveedor->proveedor_contribuyente ="0";
                }
                $proveedor->proveedor_cuenta_pagar = $request->get('idCuentaxpagar');
                $proveedor->proveedor_cuenta_anticipo = $request->get('idCuentaAnticipo');
            }
            if ($parametrizacionContable->parametrizacion_cuenta_general == '0') {
                $proveedor->proveedor_cuenta_pagar = $request->get('idCuentaxpagar');
            }
            if ($parametrizacionContableProveedor->parametrizacion_cuenta_general == '0') {
                $proveedor->proveedor_cuenta_anticipo = $request->get('idCuentaAnticipo');
            }
            $proveedor->tipo_sujeto_id = $request->get('idSujeto');
            $proveedor->tipo_identificacion_id = $request->get('idTidentificacion');
            $proveedor->ciudad_id = $request->get('idCiudad');          
            $proveedor->categoria_proveedor_id = $request->get('idCategoria');
            $proveedor->proveedor_estado  = 1;            
            $proveedor->save();
            /*Inicio de registro de auditoria */
            $auditoria = new generalController();
            $auditoria->registrarAuditoria('Actualizacion de Proveedor -> '.$request->get('idNombre').' con Cedula -> '.$request->get('idRuc'),'0','con el tipo de Identificacion ->'.$request->get('idTidentificacion'));
            /*Fin de registro de auditoria */
            DB::commit();
            return redirect('proveedor')->with('success','Datos guardados exitosamente');
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('proveedor')->with('error2','Oucrrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
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
            $Proveedor = Proveedor::findOrFail($id);            
            $Proveedor->delete();
            /*Inicio de registro de auditoria */
            $auditoria = new generalController();
            $auditoria->registrarAuditoria('Eliminacion de Cliente -> '.$Proveedor->proveedor_nombre,'0','Con cedula-> '.$Proveedor->proveedor_ruc);
            /*Fin de registro de auditoria */
            DB::commit();
            return redirect('proveedor')->with('success','Datos eliminados exitosamente');
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('proveedor')->with('error','El registro no pudo ser borrado, tiene resgitros adjuntos.');
        }
    }

    public function delete($id)
    {
        try{
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $proveedor=Proveedor::proveedor($id)->first();
            $parametrizacionContable=Parametrizacion_Contable::ParametrizacionByNombreFinanciero('CUENTA POR PAGAR')->first();
            if($proveedor){
                return view('admin.compras.proveedor.eliminar',['parametrizacionContable'=>$parametrizacionContable,'proveedor'=>$proveedor, 'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
            }else{
                return redirect('/denegado');
            }
        }
        catch(\Exception $ex){      
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function excelProveedor(){
        try{
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            return view('admin.compras.proveedor.cargarExcel',['PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        }
        catch(\Exception $ex){      
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function CargarExcelProveedor(Request $request){
        if (isset($_POST['cargar'])){
            return $this->cargarguardar($request);
        }
    }
    public function cargar(Request $request){
        try{
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $datos = null;
            $count = 1;
            
            if($request->file('excelProv')->isValid()){
                $empresa = Empresa::empresa()->first();
                $name = $empresa->empresa_ruc. '.' .$request->file('excelProv')->getClientOriginalExtension();
                $path = $request->file('excelProv')->move(public_path().'\temp', $name); 
                $array = Excel::toArray(new Proveedor(), $path); 
                for($i=1;$i < count($array[0]);$i++){
                    $datos[$count]['ruc'] = $array[0][$i][0];
                    $datos[$count]['nom'] = $array[0][$i][1];
                    $datos[$count]['nombComercial'] = $array[0][$i][2];
                    $datos[$count]['gerente'] = $array[0][$i][3];
                    $datos[$count]['direccion'] = $array[0][$i][4];
                    $datos[$count]['telefono'] = $array[0][$i][5];
                    $datos[$count]['celular'] = $array[0][$i][6];
                    $datos[$count]['email'] = $array[0][$i][7];
                    $datos[$count]['actividad'] = $array[0][$i][8];
                    $datos[$count]['fecha'] = date('d-m-Y');
                    $datos[$count]['tipo'] = $array[0][$i][10];                    
                    $datos[$count]['llevaContabilidad'] = $array[0][$i][11];
                    $datos[$count]['contribuyente'] = $array[0][$i][12];
                    $datos[$count]['cuentaPagar'] = $array[0][$i][13];
                    $datos[$count]['cuentaAntitipo'] = $array[0][$i][14];
                    $datos[$count]['tipoSujeto'] = $array[0][$i][15];
                    $datos[$count]['tipoIdentificacion'] = $array[0][$i][16];
                    $datos[$count]['ciudad'] = $array[0][$i][17];
                    $datos[$count]['categoriaProveedor'] = $array[0][$i][18];
                    $count ++;
                }  

            }
            return view('admin.compras.proveedor.cargarExcel',['datos'=>$datos,'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        }
        catch(\Exception $ex){      
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function cargarguardar(Request $request){
        try {
            DB::beginTransaction();
            if ($request->file('excelProv')->isValid()) {
                $empresa = Empresa::empresa()->first();
                $name = $empresa->empresa_ruc. '.' .$request->file('excelProv')->getClientOriginalExtension();
                $path = $request->file('excelProv')->move(public_path().'\temp', $name);
                $array = Excel::toArray(new Proveedor(), $path);
                for ($i=1;$i < count($array[0]);$i++) {
                    $validar=trim($array[0][$i][0]);
                    $validacion=Proveedor::existe($validar)->get();
                    if (count($validacion)==0) {
                        $proveedor = new Proveedor();
                        $proveedor->proveedor_ruc = $validar;
                        $proveedor->proveedor_nombre = $array[0][$i][1];
                        $proveedor->proveedor_nombre_comercial = $array[0][$i][2];
                        $proveedor->proveedor_gerente = $array[0][$i][3];
                        if ($array[0][$i][4]) {
                            $proveedor->proveedor_direccion = $array[0][$i][4];
                        }
                        else{
                            $proveedor->proveedor_direccion = ' ';
                        }
                        if ($array[0][$i][5]) {
                            $proveedor->proveedor_telefono = $array[0][$i][5];
                        }
                        else{
                            $proveedor->proveedor_telefono = ' ';
                        }
                        if ($array[0][$i][6]) {
                            $proveedor->proveedor_celular = $array[0][$i][6];
                        }
                        else{
                            $proveedor->proveedor_celular = ' ';
                        }
                        if ($array[0][$i][7]) {
                            $proveedor->proveedor_email = $array[0][$i][7];
                        }
                        else{
                            $proveedor->proveedor_email = ' ';
                        }
                        if ($array[0][$i][8]) {
                            $proveedor->proveedor_actividad = $array[0][$i][8];
                        }
                        else{
                            $proveedor->proveedor_actividad = ' ';
                        }
                        
                      
                        $proveedor->proveedor_fecha_ingreso = date('d-m-Y');
                        
                        $proveedor->proveedor_tipo = $array[0][$i][10];
                        
                        $proveedor->proveedor_lleva_contabilidad = $array[0][$i][11];
                        
                        $proveedor->proveedor_contribuyente = $array[0][$i][12];
                        if (isset($array[0][$i][13])) {
                            $cuenta=Cuenta::buscarCuenta($array[0][$i][13])->first();
                            if (isset($cuenta->cuenta_id)) {
                                $proveedor->proveedor_cuenta_pagar = $cuenta->cuenta_id;
                            }
                        } 
                        if (isset($array[0][$i][14])) {
                            $cuenta=Cuenta::buscarCuenta($array[0][$i][14])->first();
                            if (isset($cuenta->cuenta_id)) {
                                $proveedor->proveedor_cuenta_anticipo = $cuenta->cuenta_id;
                            }
                        }
                        $tipoSujeto=Tipo_Sujeto::TipoSujetoNombre($array[0][$i][15])->first();
                        if (isset($tipoSujeto->tipo_sujeto_nombre)) {
                            $proveedor->tipo_sujeto_id = $tipoSujeto->tipo_sujeto_id;
                        } else {
                            $tipoSujeto = new Tipo_Sujeto();
                            $tipoSujeto->tipo_sujeto_codigo = $i.'CSMEXC';
                            $tipoSujeto->tipo_sujeto_nombre = strtoupper($array[0][$i][15]);
                            $tipoSujeto->tipo_sujeto_estado  = 1;
                            $tipoSujeto->empresa_id = Auth::user()->empresa_id;
                            $tipoSujeto->save();
                            /*Inicio de registro de auditoria */
                            $auditoria = new generalController();
                            $auditoria->registrarAuditoria('Registro de tipo de sujeto -> '.$array[0][$i][15], '0', '');
                            /*Fin de registro de auditoria */
                            $proveedor->tipoSujeto()->associate($tipoSujeto);
                        }
                    
                        $tipoIdentificacion=Tipo_Identificacion::TipoIdentificacionNombre($array[0][$i][16])->first();
                            
                        if (isset($tipoIdentificacion->tipo_identificacion_nombre)) {
                            $proveedor->tipo_identificacion_id = $tipoIdentificacion->tipo_identificacion_id;
                        } else {
                            $tipoIdentificacion = new Tipo_Identificacion();
                            $tipoIdentificacion->tipo_identificacion_nombre =strtoupper($array[0][$i][16]);
                            if ($array[0][$i][16] =='RUC') {
                                $tipoIdentificacion->tipo_identificacion_codigo = 'R';
                            }
                            if ($array[0][$i][16] =='Cédula') {
                                $tipoIdentificacion->tipo_identificacion_codigo = 'C';
                            }
                            if ($array[0][$i][16] =='CONSUMIDOR FINAL') {
                                $tipoIdentificacion->tipo_identificacion_codigo = 'F';
                            }
                            if ($array[0][$i][16] =='PASAPORTE / IDENTIFICACIÓN TRIBUTARIA DEL EXTERIOR') {
                                $tipoIdentificacion->tipo_identificacion_codigo = 'P';
                            }
                            $tipoIdentificacion->tipo_identificacion_estado  = 1;
                            $tipoIdentificacion->empresa_id = Auth::user()->empresa_id;
                            $tipoIdentificacion->save();
                            /*Inicio de registro de auditoria */
                            $auditoria = new generalController();
                            $auditoria->registrarAuditoria('Registro de tipo de Identificacion -> '.$array[0][$i][16], '0', '');
                            /*Fin de registro de auditoria */
                            $proveedor->tipoIdentificacion()->associate($tipoIdentificacion);
                        }
                        $ciudad=Ciudad::CiudadNombre($array[0][$i][17])->first();
                        if (isset($ciudad->ciudad_nombre)) {
                            $proveedor->ciudad_id = strtoupper($ciudad->ciudad_id);
                        } else {
                            $ciudad = new Ciudad();
                            $ciudad->ciudad_nombre = strtoupper($array[0][$i][17]);
                            $ciudad->ciudad_codigo = '999999';
                            $provincia=Provincia::ProvinciaNombre('SIN PROVINCIA')->first();
                            if (isset($provincia->provincia_nombre)) {
                                $ciudad->provincia_id = $provincia->provincia_id;
                            } else {
                                $provincia = new Provincia();
                                $provincia->provincia_nombre = 'SIN PROVINCIA';
                                $provincia->provincia_codigo = 'S/N';

                                $pais=Pais::PaisNombre('ECUADOR')->first();
                                if (isset($pais->pais_nombre)) {
                                    $provincia->pais_id = $pais->pais_id;
                                } else {
                                    $Pais = new Pais();
                                    $Pais->pais_nombre = 'ECUADOR';
                                    $Pais->pais_codigo = '+593';
                                    $Pais->pais_estado = 1;
                                    $Pais->empresa_id = Auth::user()->empresa_id;
                                    $Pais->save();
                                    /*Inicio de registro de auditoria */
                                    $auditoria = new generalController();
                                    $auditoria->registrarAuditoria('Registro de pais -> '.$Pais->pais_nombre, '0', '');
                                    $provincia->pais()->associate($Pais);
                                }
                                $provincia->provincia_estado = 1;
                                $provincia->save();
                                /*Inicio de registro de auditoria */
                                $auditoria = new generalController();
                                $auditoria->registrarAuditoria('Registro de provincia -> '.'SIN PROVINCIA', '0', 'Asignada al pais ECAUDOR -> ');
                                $ciudad->provincia()->associate($provincia);
                            }
                            /*Fin de registro de auditoria */
                            $ciudad->ciudad_estado = 1;
                            $ciudad->save();
                            /*Inicio de registro de auditoria */
                            $auditoria = new generalController();
                            $auditoria->registrarAuditoria('Registro de ciudad -> '.$array[0][$i][17], '0', '');
                            $proveedor->ciudad()->associate($ciudad);
                        }
                        $categoriaProv = Categoria_Proveedor::CategoriaProveedorNombre($array[0][$i][18])->first();
                        if (isset($categoriaProv->categoria_proveedor_nombre)) {
                            $proveedor->categoria_proveedor_id = $categoriaProv->categoria_proveedor_id;
                        } else {
                            $categoriaProv = new Categoria_Proveedor();
                            $categoriaProv->categoria_proveedor_nombre = strtoupper($array[0][$i][18]);
                            $categoriaProv->categoria_proveedor_descripcion = 'Desde excell';
                            $categoriaProv->empresa_id = Auth::user()->empresa_id;
                            $categoriaProv->categoria_proveedor_estado = 1;
                            $categoriaProv->save();
                            /*Inicio de registro de auditoria */
                            $auditoria = new generalController();
                            $auditoria->registrarAuditoria('Registro de categoria proveedor -> '.$array[0][$i][18].' con descripcion -> desde excell ', '0', '');
                            $proveedor->categoriaProveedor()->associate($categoriaProv);
                        }
                        $proveedor->proveedor_estado = 1;
                        $cuentap=Cuenta::BuscarByCuenta('ANTICIPO DE PROVEEDOR')->first();
                        if ($cuentap) {
                            $cuentaapdre=Cuenta::BuscarByCuenta($cuentap->cuenta_id)->max('cuenta_secuencial');
                            $sec=1;
                            if ($cuentaapdre) {
                                $sec=$sec+$cuentaapdre;
                            }
                            $numerocuenta=$cuentap->cuenta_numero.'.'.$sec;
                            $cuentaa = new Cuenta();
                            $cuentaa->cuenta_numero =$numerocuenta;
                            $cuentaa->cuenta_nombre = 'ANTICIPO DE PROVEEDOR -'.$proveedor->proveedor_nombre;
                            $cuentaa->cuenta_secuencial = $sec;
                            $cuentaa->cuenta_nivel = $cuentap->cuenta_secuencial+1;
                            $cuentaa->cuenta_estado = 1;
                            $cuentaa->empresa_id = Auth::user()->empresa_id;
                            $cuentaa->save();
                            /*Inicio de registro de auditoria */
                            $auditoria = new generalController();
                            $auditoria->registrarAuditoria('Registro de cuenta -> ANTICIPO DE PROVEEDOR -'.$proveedor->proveedor_nombre, '0', 'Numero de la cuenta registrada es -> '.$numerocuenta);
                            $proveedor->proveedor_cuenta_anticipo=$cuentaa->cuenta_id;
                        }
                        $cuentapr=Cuenta::BuscarByCuenta('CUENTA POR PAGAR')->first();
                        if ($cuentapr) {
                            $cuentaapdre=Cuenta::BuscarByCuenta($cuentapr->cuenta_id)->max('cuenta_secuencial');
                            $sec=1;
                            if ($cuentaapdre) {
                                $sec=$sec+$cuentaapdre;
                            }
                            $numerocuenta=$cuentapr->cuenta_numero.'.'.$sec;
                            $cuentap = new Cuenta();
                            $cuentap->cuenta_numero =$numerocuenta;
                            $cuentap->cuenta_nombre = 'CUENTA POR PAGAR -'.$proveedor->proveedor_nombre;
                            $cuentap->cuenta_secuencial = $sec;
                            $cuentap->cuenta_nivel = $cuentapr->cuenta_secuencial+1;
                            $cuentap->cuenta_estado = 1;
                            $cuentap->empresa_id = Auth::user()->empresa_id;
                            $cuentap->save();
                            /*Inicio de registro de auditoria */
                            $auditoria = new generalController();
                            $auditoria->registrarAuditoria('Registro de cuenta -> CUENTA POR PAGAR -'.$proveedor->proveedor_nombre, '0', 'Numero de la cuenta registrada es -> '.$numerocuenta);
                            $proveedor->proveedor_cuenta_pagar=$cuentap->cuenta_id;
                        }
                        $proveedor->save();
                        /*Inicio de registro de auditoria */
                        $auditoria = new generalController();
                        $auditoria->registrarAuditoria('Registro de Proveedores -> '.$array[0][$i][1].'con codigo->'.$array[0][$i][0].'Mediante archivo excell', '0', '');
                    }
                }
            }
            DB::commit();
            return redirect('proveedor')->with('success','Datos guardados exitosamente');
        }
        catch(\Exception $ex){
            DB::rollBack();
            return redirect('proveedor')->with('error2','Oucrrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function guardar(Request $request){
        try{
            $ruc = $request->get('idRuc');
            $nom = $request->get('idNom');
            $nomComercial = $request->get('idNombComercial');
            $gerente = $request->get('idGerente');
            $dir = $request->get('idDireccion');
            $tel = $request->get('idTelefono');
            $cel = $request->get('idCelular');
            $email = $request->get('idEmail');
            $actividad = $request->get('iActividad');
            $fecha = $request->get('idFecha');
            $tipo = $request->get('idTipo');
            $llevaConta = $request->get('idLlevaContabilidad');
            $contribuyente = $request->get('idContribuyente');
            $cuentap = $request->get('idCuentaPagar');
            $cuentaAnt = $request->get('idCuentaAntitipo');
            $tiposujeto = $request->get('idTipoSujeto');
            $tipoidentificacion = $request->get('idTipoIdentificacion');
            $ciu = $request->get('idCiudad');
            $categoriaproveedor = $request->get('idCategoriaProveedor');
            DB::beginTransaction();           
            if($ruc){
                for ($i = 0; $i < count($ruc); ++$i) {
                    $validar=trim($ruc[$i]);     
                    $validacion=Proveedor::existe($validar)->get();
                        if (count($validacion)==0) {
                            $proveedor = new Proveedor();
                            $proveedor->proveedor_ruc = $ruc[$i];
                            $proveedor->proveedor_nombre = strtoupper($nom[$i]);
                            $proveedor->proveedor_nombre_comercial = strtoupper($nomComercial[$i]);
                            $proveedor->proveedor_gerente = $gerente[$i];
                            $proveedor->proveedor_direccion = $dir[$i];
                            $proveedor->proveedor_telefono = $tel[$i];
                            $proveedor->proveedor_celular = $cel[$i];
                            $proveedor->proveedor_email = $email[$i];
                            $proveedor->proveedor_actividad = $actividad[$i];
                            $proveedor->proveedor_fecha_ingreso = $fecha[$i];
                            $proveedor->proveedor_tipo = $tipo[$i];
                            $proveedor->proveedor_lleva_contabilidad = $llevaConta[$i];
                            $proveedor->proveedor_contribuyente = $contribuyente[$i];
                            $tipoSujeto=Tipo_Sujeto::TipoSujetoNombre($tiposujeto[$i])->first();
                            if (isset($tipoSujeto->tipo_sujeto_nombre)) {
                                $proveedor->tipo_sujeto_id = $tipoSujeto->tipo_sujeto_id;
                            } else {
                                $tipoSujeto = new Tipo_Sujeto();
                                $tipoSujeto->tipo_sujeto_codigo = $i.'CSMEXC';
                                $tipoSujeto->tipo_sujeto_nombre = strtoupper($tiposujeto[$i]);
                                $tipoSujeto->tipo_sujeto_estado  = 1;
                                $tipoSujeto->empresa_id = Auth::user()->empresa_id;
                                $tipoSujeto->save();
                                /*Inicio de registro de auditoria */
                                $auditoria = new generalController();
                                $auditoria->registrarAuditoria('Registro de tipo de sujeto -> '.$tiposujeto[$i], '0', '');
                                /*Fin de registro de auditoria */
                                $proveedor->tipoSujeto()->associate($tipoSujeto);
                            }
                    
                            $tipoIdentificacion=Tipo_Identificacion::TipoIdentificacionNombre($tipoidentificacion[$i])->first();
                            
                            if (isset($tipoIdentificacion->tipo_identificacion_nombre)) {
                                $proveedor->tipo_identificacion_id = $tipoIdentificacion->tipo_identificacion_id;
                            } else {
                                $tipoIdentificacion = new Tipo_Identificacion();
                                $tipoIdentificacion->tipo_identificacion_nombre =strtoupper($tipoidentificacion[$i]);
                                if ($tipoidentificacion[$i] =='RUC') {
                                    $tipoIdentificacion->tipo_identificacion_codigo = 'R';
                                }
                                if ($tipoidentificacion[$i] =='CEDULA') {
                                    $tipoIdentificacion->tipo_identificacion_codigo = 'C';
                                }
                                if ($tipoidentificacion[$i] =='CONSUMIDOR FINAL') {
                                    $tipoIdentificacion->tipo_identificacion_codigo = 'F';
                                }
                                if ($tipoidentificacion[$i] =='PASAPORTE / IDENTIFICACIÓN TRIBUTARIA DEL EXTERIOR') {
                                    $tipoIdentificacion->tipo_identificacion_codigo = 'P';
                                }
                                $tipoIdentificacion->tipo_identificacion_estado  = 1;
                                $tipoIdentificacion->empresa_id = Auth::user()->empresa_id;
                                $tipoIdentificacion->save();
                                /*Inicio de registro de auditoria */
                                $auditoria = new generalController();
                                $auditoria->registrarAuditoria('Registro de tipo de Identificacion -> '.$tipoidentificacion[$i], '0', '');
                                /*Fin de registro de auditoria */
                                $proveedor->tipoIdentificacion()->associate($tipoIdentificacion);
                            }
                            $ciudad=Ciudad::CiudadNombre($ciu[$i])->first();
                            if (isset($ciudad->ciudad_nombre)) {
                                $proveedor->ciudad_id = strtoupper($ciudad->ciudad_id);
                            } else {
                                $ciudad = new Ciudad();
                                $ciudad->ciudad_nombre = strtoupper($ciu[$i]);
                                $ciudad->ciudad_codigo = '999999';
                                $provincia=Provincia::ProvinciaNombre('SIN PROVINCIA')->first();
                                if (isset($provincia->provincia_nombre)) {
                                    $ciudad->provincia_id = $provincia->provincia_id;
                                } else {
                                    $provincia = new Provincia();
                                    $provincia->provincia_nombre = 'SIN PROVINCIA';
                                    $provincia->provincia_codigo = 'S/N';

                                    $pais=Pais::PaisNombre('ECUADOR')->first();
                                    if (isset($pais->pais_nombre)) {
                                        $provincia->pais_id = $pais->pais_id;
                                    } else {
                                        $Pais = new Pais();
                                        $Pais->pais_nombre = 'ECUADOR';
                                        $Pais->pais_codigo = '+593';
                                        $Pais->pais_estado = 1;
                                        $Pais->empresa_id = Auth::user()->empresa_id;
                                        $Pais->save();
                                        /*Inicio de registro de auditoria */
                                        $auditoria = new generalController();
                                        $auditoria->registrarAuditoria('Registro de pais -> '.$request->get('idNombre'), '0', '');
                                        $provincia->pais()->associate($Pais);
                                    }
                                    $provincia->provincia_estado = 1;
                                    $provincia->save();
                                    /*Inicio de registro de auditoria */
                                    $auditoria = new generalController();
                                    $auditoria->registrarAuditoria('Registro de provincia -> '.'SIN PROVINCIA', '0', 'Asignada al pais ECAUDOR -> ');
                                    $ciudad->provincia()->associate($provincia);
                                }
                                /*Fin de registro de auditoria */
                                $ciudad->ciudad_estado = 1;
                                $ciudad->save();
                                /*Inicio de registro de auditoria */
                                $auditoria = new generalController();
                                $auditoria->registrarAuditoria('Registro de ciudad -> '.$ciu[$i], '0', '');
                                $proveedor->ciudad()->associate($ciudad);
                            }
                            $categoriaProv = Categoria_Proveedor::CategoriaProveedorNombre($categoriaproveedor[$i])->first();
                            if (isset($categoriaProv->categoria_proveedor_nombre)) {
                                $proveedor->categoria_proveedor_id = $categoriaProv->categoria_proveedor_id;
                            } else {
                                $categoriaProv = new Categoria_Proveedor();
                                $categoriaProv->categoria_proveedor_nombre = strtoupper($categoriaproveedor[$i]);
                                $categoriaProv->categoria_proveedor_descripcion = 'Desde excell';
                                $categoriaProv->empresa_id = Auth::user()->empresa_id;
                                $categoriaProv->categoria_proveedor_estado = 1;
                                $categoriaProv->save();
                                /*Inicio de registro de auditoria */
                                $auditoria = new generalController();
                                $auditoria->registrarAuditoria('Registro de categoria proveedor -> '.$categoriaproveedor[$i].' con descripcion -> desde excell ', '0', '');
                                $proveedor->categoriaProveedor()->associate($categoriaProv);
                            }
                            $proveedor->proveedor_estado = 1;
                            $proveedor->save();
                            /*Inicio de registro de auditoria */
                            $auditoria = new generalController();
                            $auditoria->registrarAuditoria('Registro de Proveedores -> '.$nom[$i].'con codigo->'.$ruc[$i].'Mediante archivo excell', '0', '');
                        }
                    }
                
            }
            DB::commit();
            return redirect('proveedor')->with('success','Datos guardados exitosamente');
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('proveedor')->with('error2','Oucrrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }

    public function buscarByNombre($buscar){
        return Proveedor::ProveedoresByNombre($buscar)->get();
    } 
    public function buscarByProveedor($buscar){
        return Proveedor::Proveedor($buscar)->get();
    }   
}
