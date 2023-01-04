<?php

namespace App\Http\Controllers;
use App\Models\Cliente;

use App\Http\Controllers\Controller;
use App\Models\Categoria_Cliente;
use App\Models\Ciudad;
use App\Models\Credito;
use App\Models\Cuenta;
use App\Models\Empresa;
use App\Models\Lista_Precio;
use App\Models\Pais;
use App\Models\Parametrizacion_Contable;
use App\Models\Provincia;
use App\Models\Punto_Emision;
use App\Models\Tipo_Cliente;
use App\Models\Tipo_Identificacion;
use App\Models\Tipo_Sujeto;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class clienteController extends Controller
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
            $ciudad=Ciudad::ciudades()->get();
            $tipoIdentificacion=Tipo_Identificacion::tipoIdentificaciones()->get();
            $tipoCliente=Tipo_Cliente::tipoClientes()->get();
            $credito=Credito::creditos()->get();
            $parametrizacionContable=Parametrizacion_Contable::ParametrizacionByNombreFinanciero('CUENTA POR COBRAR')->first();
            $parametrizacionContableCliente=Parametrizacion_Contable::ParametrizacionByNombreFinanciero('ANTICIPO DE CLIENTE')->first();
            $categoriaCliente=Categoria_Cliente::categoriaClientes()->get();      
            $clientes=Cliente::clientes()->get();
            return view('admin.ventas.cliente.index',['parametrizacionContableCliente'=>$parametrizacionContableCliente,'parametrizacionContable'=>$parametrizacionContable,'precios'=>Lista_Precio::ListasPrecios()->get(), 'clientes'=>$clientes,'PE'=>Punto_Emision::puntos()->get(),'cuentas'=>$cuentas,'ciudad'=>$ciudad, 'tipoIdentificacion'=>$tipoIdentificacion,'tipoCliente'=>$tipoCliente, 'credito'=>$credito,'categoriaCliente'=>$categoriaCliente,'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        }catch(\Exception $ex){
           
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }

    public function create()
    {
        try{
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $cuentas=Cuenta::CuentasMovimiento()->get();
            $ciudad=Ciudad::ciudades()->get();
            $tipoIdentificacion=Tipo_Identificacion::tipoIdentificaciones()->get();
            $tipoCliente=Tipo_Cliente::tipoClientes()->get();  
            $credito=Credito::creditos()->get();
            $categoriaCliente=Categoria_Cliente::categoriaClientes()->get();   
            $parametrizacionContable=Parametrizacion_Contable::ParametrizacionByNombreFinanciero('CUENTA POR COBRAR')->first();
            $parametrizacionContableCliente=Parametrizacion_Contable::ParametrizacionByNombreFinanciero('ANTICIPO DE CLIENTE')->first();
            return view('admin.ventas.cliente.create',['parametrizacionContableCliente'=>$parametrizacionContableCliente,'parametrizacionContable'=>$parametrizacionContable,'precios'=>Lista_Precio::ListasPrecios()->get(),'PE'=>Punto_Emision::puntos()->get(),'cuentas'=>$cuentas,'ciudad'=>$ciudad, 'tipoIdentificacion'=>$tipoIdentificacion,'tipoCliente'=>$tipoCliente, 'credito'=>$credito,'categoriaCliente'=>$categoriaCliente,'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
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
            $parametrizacionContable=Parametrizacion_Contable::ParametrizacionByNombreFinanciero('CUENTA POR COBRAR')->first();
            $parametrizacionContableCliente=Parametrizacion_Contable::ParametrizacionByNombreFinanciero('ANTICIPO DE CLIENTE')->first();
            $cliente = new Cliente();
            $cliente->cliente_cedula = $request->get('idCedula');
            $cliente->cliente_nombre = $request->get('idNombre');
            $cliente->cliente_abreviatura = $request->get('idAbreviatura');
            $cliente->cliente_direccion = $request->get('idDireccion');
            $cliente->cliente_telefono = $request->get('idTelefono');
            $cliente->cliente_celular = $request->get('idCelular');
            $cliente->cliente_email = $request->get('idEmail');
            $cliente->cliente_fecha_ingreso = $request->get('idFecha');
            if ($request->get('idLlevacontabilidad') == "on"){
                $cliente->cliente_lleva_contabilidad ="1";
            }else{
                $cliente->cliente_lleva_contabilidad ="0";
            }
            if ($request->get('idTienecredito') == "on"){
                $cliente->cliente_tiene_credito ="1";
            }else{
                $cliente->cliente_tiene_credito ="0";
            }
            if(Auth::user()->empresa->empresa_contabilidad == '1'){
                $cliente->cliente_cuenta_cobrar = $request->get('idCuentaxcobrar');
                $cliente->cliente_cuenta_anticipo = $request->get('idCuentaAnticipo');
            }
            if ($parametrizacionContableCliente->parametrizacion_cuenta_general == '0') {
                
                    $cuentap=Cuenta::BuscarByCuenta('ANTICIPO DE CLIENTE')->first();
                    if (!$cuentap) {
                        $cuentap=Cuenta::BuscarByCuenta('ANTICIPOS DE EMPLEADO')->first();
                    }
                    if (!$cuentap) {
                        $cuentap=Cuenta::BuscarByCuenta('ANTICIPO A EMPLEADO')->first();
                    }
                    if (!$cuentap) {
                        $cuentap=Cuenta::BuscarByCuenta('ANTICIPOS A EMPLEADO')->first();
                    }
                    if ($cuentap) {
                        $sec=1;
                        $cuentaapdre=Cuenta::BuscarByCuenta($cuentap->cuenta_id)->max('cuenta_secuencial');
                        if ($cuentaapdre) {
                            $sec=$sec+$cuentaapdre;
                        }
                        $numerocuenta=$cuentap->cuenta_numero.'.'.$sec;
                        $cuentaa = new Cuenta();
                        $cuentaa->cuenta_numero =$numerocuenta;
                        $cuentaa->cuenta_nombre = 'ANTICIPO DE CLIENTE -'.$cliente->cliente_nombre;
                        $cuentaa->cuenta_secuencial = $sec;
                        $cuentaa->cuenta_nivel = $cuentap->cuenta_secuencial+1;
                        $cuentaa->cuenta_estado = 1;
                        $cuentaa->empresa_id = Auth::user()->empresa_id;
                        $cuentaa->save();
                        /*Inicio de registro de auditoria */
                        $auditoria = new generalController();
                        $auditoria->registrarAuditoria('Registro de cuenta -> ANTICIPO DE CLIENTE -'.$cliente->cliente_nombre, '0', 'Numero de la cuenta registrada es -> '.$numerocuenta);
                        $cliente->cliente_cuenta_anticipo=$cuentaa->cuenta_id;
                    }
                
            }
            if ($parametrizacionContable->parametrizacion_cuenta_general == '0') {
           
                    $cuentapr=Cuenta::BuscarByCuenta('CUENTAS POR COBRAR')->first();
                    if (!$cuentap) {
                        $cuentap=Cuenta::BuscarByCuenta('CUENTA POR COBRAR')->first();
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
                        $cuentap->cuenta_nombre = 'CUENTA POR COBRAR -'.$cliente->cliente_nombre;
                        $cuentap->cuenta_secuencial = $sec;
                        $cuentap->cuenta_nivel = $cuentapr->cuenta_secuencial+1;
                        $cuentap->cuenta_estado = 1;
                        $cuentap->empresa_id = Auth::user()->empresa_id;
                        $cuentap->save();
                        /*Inicio de registro de auditoria */
                        $auditoria = new generalController();
                        $auditoria->registrarAuditoria('Registro de cuenta -> CUENTA POR COBRAR -'.$cliente->cliente_nombre, '0', 'Numero de la cuenta registrada es -> '.$numerocuenta);
                        $cliente->cliente_cuenta_cobrar=$cuentap->cuenta_id;
                    }
                
            }
            
            $cliente->ciudad_id = $request->get('idCiudad');
            $cliente->tipo_identificacion_id = $request->get('idTidentificacion');
            $cliente->tipo_cliente_id = $request->get('idTipoCliente');
            $cliente->cliente_credito = $request->get('idCupoCredito');
            $cliente->categoria_cliente_id = $request->get('idCategoria');
            if($request->get('lista_id')){
                $cliente->lista_id  = $request->get('lista_id'); 
            }
            $cliente->cliente_estado  = 1;            
            $cliente->save();
            /*Inicio de registro de auditoria */
            $auditoria = new generalController();
            $auditoria->registrarAuditoria('Registro de Cliente -> '.$request->get('idNombre').' con Cedula -> '.$request->get('idCedula'),'0','con el tipo de Identificacion ->'.$request->get('idTidentificacion'));
            /*Fin de registro de auditoria */

            

            if(isset(Auth::user()->empresa->grupo)){
                foreach(Auth::user()->empresa->grupo->usuarios->empresas as $empresas){
                    if(Auth::user()->empresa->empresa_id != $empresas->empresa->empresa_id){
                        if (Auth::user()->empresa->grupo->grupo_duplicado == '1') {
                            $cliente = new Cliente();
                            $cliente->cliente_cedula = $request->get('idCedula');
                            $cliente->cliente_nombre = $request->get('idNombre');
                            $cliente->cliente_abreviatura = $request->get('idAbreviatura');
                            $cliente->cliente_direccion = $request->get('idDireccion');
                            $cliente->cliente_telefono = $request->get('idTelefono');
                            $cliente->cliente_celular = $request->get('idCelular');
                            $cliente->cliente_email = $request->get('idEmail');
                            $cliente->cliente_fecha_ingreso = $request->get('idFecha');
                            if ($request->get('idLlevacontabilidad') == "on"){
                                $cliente->cliente_lleva_contabilidad ="1";
                            }else{
                                $cliente->cliente_lleva_contabilidad ="0";
                            }
                            if ($request->get('idTienecredito') == "on"){
                                $cliente->cliente_tiene_credito ="1";
                            }else{
                                $cliente->cliente_tiene_credito ="0";
                            }
                            if(Auth::user()->empresa->empresa_contabilidad == '1'){
                                $cliente->cliente_cuenta_cobrar = $request->get('idCuentaxcobrar');
                                $cliente->cliente_cuenta_anticipo = $request->get('idCuentaAnticipo');
                            }
                            $cliente->ciudad_id = $request->get('idCiudad');

                            $cliente->tipo_identificacion_id = $request->get('idTidentificacion');

                            $tipo=Tipo_Cliente::findOrFail($request->get('idTipoCliente'));
                            $tipoaux=Tipo_Cliente::TipoClienteEmpresaNombre($tipo->tipo_cliente_nombre,$empresas->empresa->empresa_id)->first();
                            if($tipoaux){
                                $cliente->tipo_cliente_id = $tipoaux->tipo_cliente_id;
                            }else{
                                $tipoaux=Tipo_Cliente::TipoClienteEmpresa($empresas->empresa->empresa_id)->first();
                                $cliente->tipo_cliente_id = $tipoaux->tipo_cliente_id;
                            }
                            $cliente->cliente_credito = $request->get('idCupoCredito');
                            
                            $tipo=Categoria_Cliente::findOrFail($request->get('idCategoria'));
                            $tipoaux=Categoria_Cliente::CategoriaClienteEmpresaNombre($tipo->categoria_cliente_nombre,$empresas->empresa->empresa_id)->first();
                            if($tipoaux){
                                $cliente->categoria_cliente_id = $tipoaux->categoria_cliente_id;
                            }else{
                                $tipoaux=Categoria_Cliente::CategoriaClienteEmpresa($empresas->empresa->empresa_id)->first();
                                $cliente->categoria_cliente_id = $tipoaux->categoria_cliente_id;
                            }

                            $cliente->cliente_estado  = 1;            
                            $cliente->save();
                            /*Inicio de registro de auditoria */
                            $auditoria = new generalController();
                            $auditoria->registrarAuditoria('Registro de Cliente -> '.$request->get('idNombre').' con Cedula -> '.$request->get('idCedula').' Con empresa ruc '.$empresas->empresa->empresa_ruc.' Con razon social'.$empresas->empresa->empresa_razonSocial,$empresas->empresa->empresa_id,'con el tipo de Identificacion ->'.$request->get('idTidentificacion'));
                        }
                    }
                }
            }
            DB::commit();
            return redirect('cliente')->with('success','Datos guardados exitosamente');
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('cliente')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
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
            $parametrizacionContable=Parametrizacion_Contable::ParametrizacionByNombreFinanciero('CUENTA POR COBRAR')->first();
            $cliente=Cliente::cliente($id)->first();
            if($cliente){
                return view('admin.ventas.cliente.ver',['parametrizacionContable'=>$parametrizacionContable,'cliente'=>$cliente, 'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
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
            
            $parametrizacionContable=Parametrizacion_Contable::ParametrizacionByNombreFinanciero('CUENTA POR COBRAR')->first();
            $parametrizacionContableCliente=Parametrizacion_Contable::ParametrizacionByNombreFinanciero('ANTICIPO DE CLIENTE')->first();
            $cuentas=Cuenta::CuentasMovimiento()->get();
            $ciudades=Ciudad::ciudades()->get();
            $tipoIdentificacions=Tipo_Identificacion::tipoIdentificaciones()->get();
            $tipoClientes=Tipo_Cliente::tipoClientes()->get();
            $creditos=Credito::creditos()->get();
            $categoriaClientes=Categoria_Cliente::categoriaClientes()->get();
            $cliente=Cliente::cliente($id)->first();
            if($cliente){
                return view('admin.ventas.cliente.editar',['parametrizacionContableCliente'=>$parametrizacionContableCliente,'parametrizacionContable'=>$parametrizacionContable,'precios'=>Lista_Precio::ListasPrecios()->get(),'cliente'=>$cliente, 'cuentas'=>$cuentas,'ciudades'=>$ciudades,'tipoIdentificacions'=>$tipoIdentificacions,'tipoClientes'=>$tipoClientes,'creditos'=>$creditos,'categoriaClientes'=>$categoriaClientes, 'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
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
            $parametrizacionContable=Parametrizacion_Contable::ParametrizacionByNombreFinanciero('CUENTA POR COBRAR')->first();
            $parametrizacionContableCliente=Parametrizacion_Contable::ParametrizacionByNombreFinanciero('ANTICIPO DE CLIENTE')->first();
            $clienteaux=Cliente::findOrFail($id);
            $cliente = Cliente::findOrFail($id);
            $cliente->cliente_cedula = $request->get('idCedula');
            $cliente->cliente_nombre = $request->get('idNombre');
            $cliente->cliente_abreviatura = $request->get('idAbreviatura');
            $cliente->cliente_direccion = $request->get('idDireccion');
            $cliente->cliente_telefono = $request->get('idTelefono');
            $cliente->cliente_celular = $request->get('idCelular');
            $cliente->cliente_email = $request->get('idEmail');
            $cliente->cliente_fecha_ingreso = $request->get('idFecha');
            if ($request->get('idContabilidad') == "on"){
                $cliente->cliente_lleva_contabilidad = 1;
            }else{
                $cliente->cliente_lleva_contabilidad = 0;
            } 
            if ($request->get('idTienecredito') == "on"){
                $cliente->cliente_tiene_credito = 1;
            }else{
                $cliente->cliente_tiene_credito = 0;
            } 
            if(Auth::user()->empresa->empresa_contabilidad == '1'){
                $cliente->cliente_cuenta_cobrar = $request->get('idCobrar');
                $cliente->cliente_cuenta_anticipo = $request->get('idAnticipo');
            }
            $cliente->ciudad_id = $request->get('idCiudad');
            $cliente->tipo_identificacion_id = $request->get('idTipo');
            $cliente->tipo_cliente_id = $request->get('idTcliente');
            $cliente->cliente_credito = $request->get('idCupoCredito');
            $cliente->categoria_cliente_id = $request->get('idCategoria');          

            if ($request->get('idEstado') == "on"){
                $cliente->cliente_estado = 1;
            }else{
                $cliente->cliente_estado = 0;
            }
            if($request->get('lista_id')){
                $cliente->lista_id  = $request->get('lista_id'); 
            }else{
                $cliente->lista_id  = null; 
            }
            if ($parametrizacionContableCliente->parametrizacion_cuenta_general == '0') {
                $cliente->cliente_cuenta_anticipo = $request->get('idAnticipo');
            }
            if ($parametrizacionContable->parametrizacion_cuenta_general == '0') {
                $cliente->cliente_cuenta_cobrar = $request->get('idCobrar');
            }
            $cliente->save();
            /*Inicio de registro de auditoria */
            $auditoria = new generalController();
            $auditoria->registrarAuditoria('Actualizacion de Cliente -> '.$request->get('idNombre'),'0','Con cedula-> '.$request->get('idCedula').' y tipo de Identificacion -> '.$request->get('idTipo'));
           /*Fin de registro de auditoria */

           if(isset(Auth::user()->empresa->grupo)){
                foreach(Auth::user()->empresa->grupo->usuarios->empresas as $empresas){
                        if (Auth::user()->empresa->empresa_id != $empresas->empresa->empresa_id) {
                            if (Auth::user()->empresa->grupo->grupo_duplicado == '1') {
                                $clienteaux=Cliente::ClientesCedulaEmpresa($clienteaux->cliente_cedula,$empresas->empresa->empresa_id)->first();
                                $cliente = Cliente::findOrFail($clienteaux->cliente_id);
                                $cliente->cliente_cedula = $request->get('idCedula');
                                $cliente->cliente_nombre = $request->get('idNombre');
                                $cliente->cliente_abreviatura = $request->get('idAbreviatura');
                                $cliente->cliente_direccion = $request->get('idDireccion');
                                $cliente->cliente_telefono = $request->get('idTelefono');
                                $cliente->cliente_celular = $request->get('idCelular');
                                $cliente->cliente_email = $request->get('idEmail');
                                $cliente->cliente_fecha_ingreso = $request->get('idFecha');
                                if ($request->get('idContabilidad') == "on"){
                                    $cliente->cliente_lleva_contabilidad = 1;
                                }else{
                                    $cliente->cliente_lleva_contabilidad = 0;
                                } 
                                if ($request->get('idTienecredito') == "on"){
                                    $cliente->cliente_tiene_credito = 1;
                                }else{
                                    $cliente->cliente_tiene_credito = 0;
                                } 
                                if(Auth::user()->empresa->empresa_contabilidad == '1'){
                                    $cliente->cliente_cuenta_cobrar = $request->get('idCobrar');
                                    $cliente->cliente_cuenta_anticipo = $request->get('idAnticipo');
                                }
                                $cliente->ciudad_id = $request->get('idCiudad');
                                $cliente->tipo_identificacion_id = $request->get('idTipo');
                                $tipo=Tipo_Cliente::findOrFail($request->get('idTcliente'));
                                $tipoaux=Tipo_Cliente::TipoClienteEmpresaNombre($tipo->tipo_cliente_nombre,$empresas->empresa->empresa_id)->first();
                                if($tipoaux){
                                    $cliente->tipo_cliente_id = $tipoaux->tipo_cliente_id;
                                }else{
                                    $tipoaux=Tipo_Cliente::TipoClienteEmpresa($empresas->empresa->empresa_id)->first();
                                    $cliente->tipo_cliente_id = $tipoaux->tipo_cliente_id;
                                }
                                $cliente->cliente_credito = $request->get('idCupoCredito');
                                
                                $tipo=Categoria_Cliente::findOrFail($request->get('idCategoria'));
                                $tipoaux=Categoria_Cliente::CategoriaClienteEmpresaNombre($tipo->categoria_cliente_nombre,$empresas->empresa->empresa_id)->first();
                                if($tipoaux){
                                    $cliente->categoria_cliente_id = $tipoaux->categoria_cliente_id;
                                }else{
                                    $tipoaux=Categoria_Cliente::CategoriaClienteEmpresa($empresas->empresa->empresa_id)->first();
                                    $cliente->categoria_cliente_id = $tipoaux->categoria_cliente_id;
                                }
                                if ($request->get('idEstado') == "on"){
                                    $cliente->cliente_estado = 1;
                                }else{
                                    $cliente->cliente_estado = 0;
                                }
                                if ($parametrizacionContableCliente->parametrizacion_cuenta_general == '0') {
                                    $cliente->cliente_cuenta_anticipo = $request->get('idAnticipo');
                                }
                                if ($parametrizacionContable->parametrizacion_cuenta_general == '0') {
                                    $cliente->cliente_cuenta_cobrar = $request->get('idCobrar');
                                }
                                $cliente->save();
                            }
                    }
                }
            }


            DB::commit();
            return redirect('cliente')->with('success','Datos actualizados exitosamente');
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('cliente')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
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
            $cliente = Cliente::findOrFail($id); 
            $clienteaux=Cliente::findOrFail($id);           
            $cliente->delete();
            /*Inicio de registro de auditoria */
            $auditoria = new generalController();
            $auditoria->registrarAuditoria('Eliminacion de Cliente -> '.$cliente->cliente_nombre,'0','Con cedula-> '.$cliente->cliente_cedula);
            /*Fin de registro de auditoria */
            if (isset(Auth::user()->empresa->grupo)) {
                foreach (Auth::user()->empresa->grupo->usuarios->empresas as $empresas) {
                    if (Auth::user()->empresa->empresa_id != $empresas->empresa->empresa_id) {
                        if (Auth::user()->empresa->grupo->grupo_duplicado == '1') {
                            $clienteaux=Cliente::ClientesCedulaEmpresa($clienteaux->cliente_cedula,$empresas->empresa->empresa_id)->first();
                            $cliente = Cliente::findOrFail($clienteaux->cliente_id);
                            $cliente->delete();
                            $auditoria = new generalController();
                            $auditoria->registrarAuditoria('Eliminacion de Cliente -> '.$cliente->cliente_nombre.' Con empresa ruc '.$empresas->empresa->empresa_ruc.' Con razon social'.$empresas->empresa->empresa_razonSocial,$empresas->empresa->empresa_id,'0','Con cedula-> '.$cliente->cliente_cedula);
                        }
                    }
                }
            }
            DB::commit();
            return redirect('cliente')->with('success','Datos eliminados exitosamente');
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('cliente')->with('error','El registro no pudo ser borrado, tiene resgitros adjuntos.');
        }
    }

    public function delete($id)
    {
        try{
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $cliente=Cliente::cliente($id)->first();
            $parametrizacionContable=Parametrizacion_Contable::ParametrizacionByNombreFinanciero('CUENTA POR COBRAR')->first();
            if($cliente){
                return view('admin.ventas.cliente.eliminar',['parametrizacionContable'=>$parametrizacionContable,'cliente'=>$cliente, 'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
            }else{
                return redirect('/denegado');
            }
        }catch(\Exception $ex){
          
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function excelCliente(){
        try{
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            return view('admin.ventas.cliente.cargarExcel',['PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        }
        catch(\Exception $ex){      
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function CargarExcelCliente(Request $request){
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
            
            if($request->file('excelClient')->isValid()){
                $empresa = Empresa::empresa()->first();
                $name = $empresa->empresa_ruc. '.' .$request->file('excelClient')->getClientOriginalExtension();
                $path = $request->file('excelClient')->move(public_path().'\temp', $name); 
                $array = Excel::toArray(new Cliente(), $path); 
                for($i=1;$i < count($array[0]);$i++){
                    $datos[$count]['ced'] = $array[0][$i][0];
                    $datos[$count]['nom'] = $array[0][$i][1];                  
                    $datos[$count]['direccion'] = $array[0][$i][2];
                    $datos[$count]['telefono'] = $array[0][$i][3];
                    $datos[$count]['celular'] = $array[0][$i][4];
                    $datos[$count]['email'] = $array[0][$i][5];
                    $datos[$count]['fecha'] = date('Y-m-d');
                    $datos[$count]['llevaContabilidad'] = $array[0][$i][6];
                    $datos[$count]['tieneCredito'] = $array[0][$i][7];
                    $datos[$count]['ciudad'] = $array[0][$i][8];
                    $datos[$count]['tipoIdentificacion'] = $array[0][$i][9];
                    $datos[$count]['tipoCliente'] = $array[0][$i][10];
                    $datos[$count]['categoriaCliente'] = $array[0][$i][11];
                    $datos[$count]['credito'] = $array[0][$i][12];
                    $count ++;
                }

            }
            return view('admin.ventas.cliente.cargarExcel',['datos'=>$datos,'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        }
        catch(\Exception $ex){      
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function cargarguardar(Request $request){
        try {
            if ($request->file('excelClient')->isValid()) {
                $empresa = Empresa::empresa()->first();
                $name = $empresa->empresa_ruc. '.' .$request->file('excelClient')->getClientOriginalExtension();
                $path = $request->file('excelClient')->move(public_path().'\temp', $name);
                $array = Excel::toArray(new Cliente(), $path);
                DB::beginTransaction();    
                for ($i=1;$i < count($array[0]);$i++) {
                    $validar=trim($array[0][$i][0]);
                    $validacion=Cliente::existe($validar)->get();
                    if (count($validacion)==0) {
                        $cliente = new Cliente();
                        $cliente->cliente_cedula =  $validar;
                        $cliente->cliente_nombre = $array[0][$i][1];
                        if ($array[0][$i][2]) {
                            $cliente->cliente_direccion = $array[0][$i][2];
                        }
                        else{
                            $cliente->cliente_direccion = ' ';
                        }
                        if ($array[0][$i][3]) {
                            $cliente->cliente_telefono = $array[0][$i][3];
                        }
                        else{
                            $cliente->cliente_telefono = ' ';
                        }
                        if ($array[0][$i][4]) {
                            $cliente->cliente_celular = $array[0][$i][4];
                        }
                        else{
                            $cliente->cliente_celular = ' ';
                        }
                        if ($array[0][$i][5]) {
                            $cliente->cliente_email = $array[0][$i][5];
                        }
                        else{
                            $cliente->cliente_email = ' ';
                        }
                        $cliente->cliente_fecha_ingreso = date('Y-m-d');
                        $cliente->cliente_lleva_contabilidad = $array[0][$i][6];
                        $cliente->cliente_tiene_credito =  $array[0][$i][7];
                        
                        $tipoIdentificacion=Tipo_Identificacion::TipoIdentificacionNombre($array[0][$i][9])->first();
                        if (isset($tipoIdentificacion->tipo_identificacion_nombre)) {
                            $cliente->tipo_identificacion_id = $tipoIdentificacion->tipo_identificacion_id;
                        } else {
                            $tipoIdentificacion = new Tipo_Identificacion();
                            $tipoIdentificacion->tipo_identificacion_nombre = $array[0][$i][9];
                            if ($tipoIdentificacion->tipo_identificacion_nombre =='RUC') {
                                $tipoIdentificacion->tipo_identificacion_codigo = 'R';
                            }
                            if ($tipoIdentificacion->tipo_identificacion_nombre =='Cédula') {
                                $tipoIdentificacion->tipo_identificacion_codigo = 'C';
                            }
                            if ($tipoIdentificacion->tipo_identificacion_nombre =='CONSUMIDOR FINAL') {
                                $tipoIdentificacion->tipo_identificacion_codigo = 'F';
                            }
                            if ($tipoIdentificacion->tipo_identificacion_nombre =='PASAPORTE / IDENTIFICACIÓN TRIBUTARIA DEL EXTERIOR') {
                                $tipoIdentificacion->tipo_identificacion_codigo = 'P';
                            }
                           
                            $tipoIdentificacion->tipo_identificacion_estado  = 1;
                            $tipoIdentificacion->empresa_id = Auth::user()->empresa_id;
                            $tipoIdentificacion->save();
                            /*Inicio de registro de auditoria */
                            $auditoria = new generalController();
                            $auditoria->registrarAuditoria('Registro de tipo de Identificacion -> '.$tipoIdentificacion->tipo_identificacion_nombre, '0', '');
                            /*Fin de registro de auditoria */
                            $cliente->tipoIdentificacion()->associate($tipoIdentificacion);
                        }
                        $ciudad=Ciudad::CiudadNombre(($array[0][$i][8]))->first();
                        if (isset($ciudad->ciudad_nombre)) {
                            $cliente->ciudad_id = $ciudad->ciudad_id;
                        } else {
                            $ciudad = new Ciudad();
                            $ciudad->ciudad_nombre = mb_strtoupper($array[0][$i][8], 'UTF-8');
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
                            $auditoria->registrarAuditoria('Registro de ciudad -> '.$ciudad->ciudad_nombre, '0', '');
                            $cliente->ciudad()->associate($ciudad);
                        }
                        $categoriaClient = Categoria_Cliente::CategoriaClienteNombre($array[0][$i][11])->first();
                        if (isset($categoriaClient->categoria_cliente_nombre)) {
                            $cliente->categoria_cliente_id = $categoriaClient->categoria_cliente_id;
                        } else {
                            $categoriaClient = new Categoria_Cliente();
                            $categoriaClient->categoria_cliente_nombre = mb_strtoupper($array[0][$i][11], 'UTF-8');
                            $categoriaClient->categoria_cliente_descripcion = 'Desde excell';
                            $categoriaClient->empresa_id = Auth::user()->empresa_id;
                            $categoriaClient->categoria_cliente_estado = 1;
                            $categoriaClient->save();
                            /*Inicio de registro de auditoria */
                            $auditoria = new generalController();
                            $auditoria->registrarAuditoria('Registro de categoria cliente -> '.$categoriaClient->categoria_cliente_nombre.' con descripcion -> desde excell ', '0', '');
                            $cliente->categoriaCliente()->associate($categoriaClient);
                        }

                        $tipoCliente = Tipo_Cliente::TipoClienteNombre($array[0][$i][10])->first();
                        if (isset($tipoCliente->tipo_cliente_nombre)) {
                            $cliente->tipo_cliente_id = $tipoCliente->tipo_cliente_id;
                        } else {
                            $tipoCliente = new Tipo_Cliente();
                            $tipoCliente->tipo_cliente_nombre = mb_strtoupper($array[0][$i][10], 'UTF-8');
                            $tipoCliente->empresa_id = Auth::user()->empresa_id;
                            $tipoCliente->tipo_cliente_estado = 1;
                            $tipoCliente->save();
                            /*Inicio de registro de auditoria */
                            $auditoria = new generalController();
                            $auditoria->registrarAuditoria('Registro de tipo cliente -> '.$tipoCliente->tipo_cliente_nombre.' con descripcion -> desde excell ', '0', '');
                            $cliente->tipoCliente()->associate($tipoCliente);
                        }

                        $credito = Credito::CreditoNombre($array[0][$i][12])->first();
                        if (isset($credito->credito_nombre)) {
                            $cliente->credito_id = $credito->credito_id;
                        } else {
                            $credito = new Credito();
                            $credito->credito_nombre = mb_strtoupper($array[0][$i][12], 'UTF-8');
                            $credito->credito_descripcion = 'Creado Automatico con el cliente';
                            $credito->credito_monto = 0;
                            $credito->empresa_id = Auth::user()->empresa_id;
                            $credito->credito_estado = 1;
                            $credito->save();
                            /*Inicio de registro de auditoria */
                            $auditoria = new generalController();
                            $auditoria->registrarAuditoria('Registro de Credito -> '.$credito->credito_nombre.' con descripcion -> desde excell ', '0', '');
                            $cliente->credito()->associate($credito);
                        }
                        $cliente->cliente_estado = '1';
                        $cuentaa = Cuenta::buscarCuenta($array[0][$i][13])->first();
                        if (isset($cuentaa->cuenta_id)) {
                            $cliente->cliente_cuenta_anticipo=$cuentaa->cuenta_id;
                        }
                        $cuentaac = Cuenta::buscarCuenta($array[0][$i][14])->first();
                        if (isset($cuentaac->cuenta_id)) {
                            $cliente->cliente_cuenta_cobrar=$cuentaac->cuenta_id;
                        }
                        /*
                        $cuentap=Cuenta::BuscarByCuenta('ANTICIPO DE CLIENTE')->first();
           
                        if ($cuentap) {
                            $cuentaapdre=Cuenta::BuscarByCuenta($cuentap->cuenta_id)->max('cuenta_secuencial');
                            $sec=1;
                            if ($cuentaapdre) {
                                $sec=$sec+$cuentaapdre;
                            }
                            $numerocuenta=$cuentap->cuenta_numero.'.'.$sec;
                            $cuentaa = new Cuenta();
                            $cuentaa->cuenta_numero =$numerocuenta;
                            $cuentaa->cuenta_nombre = 'ANTICIPO DE CLIENTE -'.$cliente->cliente_nombre;
                            $cuentaa->cuenta_secuencial = $sec;
                            $cuentaa->cuenta_nivel = $cuentap->cuenta_secuencial+1;
                            $cuentaa->cuenta_estado = 1;
                            $cuentaa->empresa_id = Auth::user()->empresa_id;
                            $cuentaa->save();
                            
                            $auditoria = new generalController();
                            $auditoria->registrarAuditoria('Registro de cuenta -> ANTICIPO DE CLIENTE -'.$cliente->cliente_nombre, '0', 'Numero de la cuenta registrada es -> '.$numerocuenta);
                            $cliente->cliente_cuenta_anticipo=$cuentaa->cuenta_id;
                        }
                        $cuentapr=Cuenta::BuscarByCuenta('CUENTA POR COBRAR')->first();
                       
                        if ($cuentapr) {
                            $cuentaapdre=Cuenta::BuscarByCuenta($cuentapr->cuenta_id)->max('cuenta_secuencial');
                            $sec=1;
                            if ($cuentaapdre) {
                                $sec=$sec+$cuentaapdre;
                            }
                            $numerocuenta=$cuentapr->cuenta_numero.'.'.$sec;
                            $cuentap = new Cuenta();
                            $cuentap->cuenta_numero =$numerocuenta;
                            $cuentap->cuenta_nombre = 'CUENTA POR COBRAR -'.$cliente->cliente_nombre;
                            $cuentap->cuenta_secuencial = $sec;
                            $cuentap->cuenta_nivel = $cuentapr->cuenta_secuencial+1;
                            $cuentap->cuenta_estado = 1;
                            $cuentap->empresa_id = Auth::user()->empresa_id;
                            $cuentap->save();
                            
                            $auditoria = new generalController();
                            $auditoria->registrarAuditoria('Registro de cuenta -> CUENTA POR COBRAR -'.$cliente->cliente_nombre, '0', 'Numero de la cuenta registrada es -> '.$numerocuenta);
                            $cliente->cliente_cuenta_cobrar=$cuentap->cuenta_id;
                        }
                        */
                        $cliente->save();
                        /*Inicio de registro de auditoria */
                        $auditoria = new generalController();
                        $auditoria->registrarAuditoria('Registro de Clientes -> '.mb_strtoupper($array[0][$i][1], 'UTF-8').'con codigo->'.mb_strtoupper($array[0][$i][0], 'UTF-8').'Mediante archivo excell', '0', '');
                    }
                }
                DB::commit();
                return redirect('cliente')->with('success','Datos guardados exitosamente');
            }
        }
        catch(\Exception $ex){ 
            DB::rollBack();     
            return redirect('cliente')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function tildes($cadena) {
        $no_permitidas= array ("á","é","í","ó","ú","Á","É","Í","Ó","Ú","ñ","À","Ã","Ì","Ò","Ù","Ã™","Ã ","Ã¨","Ã¬","Ã²","Ã¹","ç","Ç","Ã¢","ê","Ã®","Ã´","Ã»","Ã‚","ÃŠ","ÃŽ","Ã”","Ã›","ü","Ã¶","Ã–","Ã¯","Ã¤","«","Ò","Ã","Ã„","Ã‹");
        $permitidas= array ("a","e","i","o","u","A","E","I","O","U","n","N","A","E","I","O","U","a","e","i","o","u","c","C","a","e","i","o","u","A","E","I","O","U","u","o","O","i","a","e","U","I","A","E");
        $texto = str_replace($no_permitidas, $permitidas ,$cadena);
        return $texto;
    }
    public function guardar(Request $request){
        try{
            $ced = $request->get('idCed');
            $nom = $request->get('idNom');            
            $dir = $request->get('idDireccion');
            $tel = $request->get('idTelefono');
            $cel = $request->get('idCelular');
            $email = $request->get('idEmail');
            $fecha = $request->get('idFecha');          
            $llevaConta = $request->get('idLlevaContabilidad');
            $tieneCredito = $request->get('idTieneCredito');
            $ciu = $request->get('idCiudad');
            $tipoidentificacion = $request->get('idTipoIdentificacion');
            $tipoCliente1 = $request->get('idTipoCliente');
            $categoriaCliente = $request->get('idCategoriaCliente');
            $credito = $request->get('idCredito');
                   
            if($ced){
                for ($i = 0; $i < count($ced); ++$i) {
                    $validar=trim($ced[$i]);
                    $validacion=Cliente::existe($validar)->get();
                    if (count($validacion)==0) {
                        $cliente = new Cliente();
                        $cliente->cliente_cedula = $ced[$i];
                        $cliente->cliente_nombre = strtoupper($nom[$i]);
                        $cliente->cliente_direccion = $dir[$i];
                        $cliente->cliente_telefono = $tel[$i];
                        $cliente->cliente_celular = $cel[$i];
                        $cliente->cliente_email = $email[$i];
                        $cliente->cliente_fecha_ingreso = $fecha[$i];
                        $cliente->cliente_lleva_contabilidad = $llevaConta[$i];
                        $cliente->cliente_tiene_credito = $tieneCredito[$i];

                        $tipoIdentificacion=Tipo_Identificacion::TipoIdentificacionNombre($tipoidentificacion[$i])->first();
                        if (isset($tipoIdentificacion->tipo_identificacion_nombre)) {
                            $cliente->tipo_identificacion_id = $tipoIdentificacion->tipo_identificacion_id;
                        } else {
                            $tipoIdentificacion = new Tipo_Identificacion();
                            $tipoIdentificacion->tipo_identificacion_nombre = strtoupper($tipoidentificacion[$i]);
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
                            $cliente->tipoIdentificacion()->associate($tipoIdentificacion);
                        }
                        $ciudad=Ciudad::CiudadNombre($ciu[$i])->first();
                        if (isset($ciudad->ciudad_nombre)) {
                            $cliente->ciudad_id = $ciudad->ciudad_id;
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
                            $cliente->ciudad()->associate($ciudad);
                        }
                        $categoriaClient = Categoria_Cliente::CategoriaClienteNombre($categoriaCliente[$i])->first();
                        if (isset($categoriaClient->categoria_proveedor_nombre)) {
                            $cliente->categoria_cliente_id = $categoriaClient->categoria_cliente_id;
                        } else {
                            $categoriaClient = new Categoria_Cliente();
                            $categoriaClient->categoria_cliente_nombre = strtoupper($categoriaCliente[$i]);
                            $categoriaClient->categoria_cliente_descripcion = 'Desde excell';
                            $categoriaClient->empresa_id = Auth::user()->empresa_id;
                            $categoriaClient->categoria_cliente_estado = 1;
                            $categoriaClient->save();
                            /*Inicio de registro de auditoria */
                            $auditoria = new generalController();
                            $auditoria->registrarAuditoria('Registro de categoria cliente -> '.$categoriaCliente[$i].' con descripcion -> desde excell ', '0', '');
                            $cliente->categoriaCliente()->associate($categoriaClient);
                        }

                        $tipoCliente = Tipo_Cliente::TipoClienteNombre($tipoCliente1[$i])->first();
                        if (isset($tipoCliente->tipo_cliente_nombre)) {
                            $cliente->tipo_cliente_id = $tipoCliente->tipo_cliente_id;
                        } else {
                            $tipoCliente = new Tipo_Cliente();
                            $tipoCliente->tipo_cliente_nombre = strtoupper($tipoCliente1[$i]);
                            $tipoCliente->empresa_id = Auth::user()->empresa_id;
                            $tipoCliente->tipo_cliente_estado = 1;
                            $tipoCliente->save();
                            /*Inicio de registro de auditoria */
                            $auditoria = new generalController();
                            $auditoria->registrarAuditoria('Registro de tipo cliente -> '.$tipoCliente1[$i].' con descripcion -> desde excell ', '0', '');
                            $cliente->tipoCliente()->associate($tipoCliente);
                        }

                        $credito = Credito::CreditoNombre($credito[$i])->first();
                        if (isset($credito->credito_nombre)) {
                            $cliente->credito_id = $credito->credito_id;
                        } else {
                            $credito = new Tipo_Cliente();
                            $credito->credito_nombre = strtoupper($credito[$i]);
                            $credito->credito_descripcion = 'Creado Automatico con el cliente';
                            $credito->credito_nombre = 10000;
                            $credito->empresa_id = Auth::user()->empresa_id;
                            $credito->credito_estado = 1;
                            $credito->save();
                            /*Inicio de registro de auditoria */
                            $auditoria = new generalController();
                            $auditoria->registrarAuditoria('Registro de Credito -> '.$credito[$i].' con descripcion -> desde excell ', '0', '');
                            $cliente->credito()->associate($credito);
                        }
                        $cliente->cliente_estado = '1';
                        $cliente->save();
                        /*Inicio de registro de auditoria */
                        $auditoria = new generalController();
                        $auditoria->registrarAuditoria('Registro de Clientes -> '.$nom[$i].'con codigo->'.$ced[$i].'Mediante archivo excell', '0', '');
                    }
                }
            }
            DB::commit();
            return redirect('cliente')->with('success','Datos guardados exitosamente');
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('cliente')->with('error2','Oucrrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }

    public function buscarByNombre($buscar){
        return Cliente::ClientesByNombre($buscar)->select("cliente.cliente_nombre","cliente.cliente_cedula","cliente.cliente_direccion",
        "cliente.cliente_telefono","cliente.cliente_id","tipo_cliente.tipo_cliente_nombre","cliente.cliente_credito","cliente.cliente_tiene_credito",
        DB::raw("((SELECT sum(cuenta_cobrar.cuenta_saldo) FROM cuenta_cobrar WHERE cuenta_cobrar.cliente_id = cliente.cliente_id) + 
        (SELECT sum(orden_despacho.orden_total) FROM orden_despacho WHERE orden_despacho.cliente_id = cliente.cliente_id and (orden_estado='1' or orden_estado='2'))) as saldo_pendiente"))->get();
    }
    public function buscarByNombreCedula($buscar){
        return Cliente::ClientesByCedulaRuc($buscar)->get();
    }

    public function buscarClientesByCedulaNombre(Request $request){
        //return $request;
        return Cliente::clientesByCedulaNombre($request->buscar)->get();
    }
}
