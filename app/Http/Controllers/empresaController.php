<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use App\Models\GrupoPer;
use App\Models\Permiso;
use App\Models\Punto_Emision;
use App\Models\Rol;
use App\Models\Rol_Permiso;
use App\Models\User;
use App\Models\Usuario_Rol;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class empresaController extends Controller
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
            $empresas=Empresa::empresas()->get();
            return view('admin.configuracion.empresa.index',['empresas'=>$empresas, 'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        }catch(\Exception $ex){
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }

    public function indexDatosEmpresa()
    {
        try{
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $empresa=Empresa::empresa()->first();
            return view('admin.configuracion.empresa.datosEmpresa',['empresa'=>$empresa, 'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        }catch(\Exception $ex){
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
            $empresa = new Empresa();
            $empresa->empresa_ruc =$request->get('Ruc');
            $empresa->empresa_nombreComercial =$request->get('idNombre');
            $empresa->empresa_razonSocial =$request->get('idRazon');
            $empresa->empresa_direccion =$request->get('idDireccion');
            $empresa->empresa_telefono =$request->get('idTelefono');
            $empresa->empresa_celular =$request->get('idCelular');
            $empresa->empresa_ciudad =$request->get('idCiudad');
            $empresa->empresa_logo="0";
            $empresa->empresa_cedula_representante=$request->get('idcedulaRepresentante');
            $empresa->empresa_representante =$request->get('idRepresentante');     
            $empresa->empresa_cedula_contador =$request->get('idcedulacontador');
            $empresa->empresa_contador =$request->get('idcontador');
            $empresa->empresa_fecha_ingreso =$request->get('idFecha');
            $empresa->empresa_email =$request->get('idEmail');
            if ($request->get('idContabilidad') == "on"){
                $empresa->empresa_llevaContabilidad ="1";
            }else{
                $empresa->empresa_llevaContabilidad ="0";
            }
            if ($request->get('idContabilidad2') == "on"){
                $empresa->empresa_contabilidad ="1";
            }else{
                $empresa->empresa_contabilidad ="0";
            }
            if ($request->get('idElectronica') == "on"){
                $empresa->empresa_electronica ="1";
            }else{
                $empresa->empresa_electronica ="0";
            }
            if ($request->get('idNomina') == "on"){
                $empresa->empresa_nomina ="1";
            }else{
                $empresa->empresa_nomina ="0";
            }
            if ($request->get('idMedico') == "on"){
                $empresa->empresa_medico ="1";
            }else{
                $empresa->empresa_medico ="0";
            }
            if ($request->get('idPrecios') == "on"){
                $empresa->empresa_estado_cambiar_precio ="1";
            }else{
                $empresa->empresa_estado_cambiar_precio ="0";
            }
            $empresa->empresa_tipo =$request->get('idTipo');
            $empresa->empresa_contribuyenteEspecial =$request->get('idContribuyente');
            $empresa->empresa_estado=1;
            $empresa->save();

            $rol = $this->permisos($empresa);           

            $usuarioControlador = new usuarioController();
            $usuario = new User();
            $usuario->user_username = 'SuperAdministrador';
            $usuario->user_cedula = '9999999999';  
            $usuario->user_nombre = 'SuperAdministrador';  
            $usuario->user_correo = 'pagupa_soft@hotmail.com';            
            $usuario->user_tipo  = 1; 
            $usuario->user_estado  = 1;
            $password=$usuarioControlador->generarPass();
            $usuario->password  = bcrypt($password);
            $usuario->empresa()->associate($empresa);
            $usuario->save();
            DB::afterCommit(function () use($usuario,$password,$usuarioControlador){
                $usuarioControlador->enviarCorreoUsuario($usuario->user_correo,$usuario->user_nombre,$usuario->user_username,$password);
            });
            
            $usuario_rol= new Usuario_Rol();
            $usuario_rol->rol()->associate($rol);
            $usuario_rol->usuario()->associate($usuario);
            $usuario_rol->save();

            /*Inicio de registro de auditoria */
            $auditoria = new generalController();
            $auditoria->registrarAuditoria('Registro de empresa -> '.$request->get('idNombre'),'0','');
            /*Fin de registro de auditoria */
            DB::commit();
            return redirect('empresa')->with('success','Datos guardados exitosamente');
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('empresa')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
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
            return view('admin.configuracion.empresa.ver',['empresa'=>Empresa::findOrFail($id), 'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
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
            return view('admin.configuracion.empresa.editar',['empresa'=>Empresa::findOrFail($id), 'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
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
            $empresa = Empresa::findOrFail($id);
            $empresa->empresa_ruc =$request->get('Ruc');
            $empresa->empresa_nombreComercial =$request->get('idNombre');
            $empresa->empresa_razonSocial =$request->get('idRazon');
            $empresa->empresa_direccion =$request->get('idDireccion');
            $empresa->empresa_telefono =$request->get('idTelefono');
            $empresa->empresa_celular =$request->get('idCelular');
            $empresa->empresa_ciudad =$request->get('idCiudad');
            $empresa->empresa_logo="";
            $empresa->empresa_representante =$request->get('idRepresentante');
            $empresa->empresa_cedula_representante=$request->get('idcedulaRepresentante');
            $empresa->empresa_fecha_ingreso =$request->get('idFecha');
            $empresa->empresa_cedula_contador =$request->get('idcedulacontador');
            $empresa->empresa_contador =$request->get('idcontador');
            $empresa->empresa_email =$request->get('idEmail');
            if ($request->get('idContabilidad') == "on"){
                $empresa->empresa_llevaContabilidad ="1";
            }else{
                $empresa->empresa_llevaContabilidad ="0";
            }
            if ($request->get('idContabilidad2') == "on"){
                $empresa->empresa_contabilidad ="1";
            }else{
                $empresa->empresa_contabilidad ="0";
            }
            if ($request->get('idElectronica') == "on"){
                $empresa->empresa_electronica ="1";
            }else{
                $empresa->empresa_electronica ="0";
            }
            if ($request->get('idNomina') == "on"){
                $empresa->empresa_nomina ="1";
            }else{
                $empresa->empresa_nomina ="0";
            }
            if ($request->get('idMedico') == "on"){
                $empresa->empresa_medico ="1";
            }else{
                $empresa->empresa_medico ="0";
            }
            if ($request->get('idPrecios') == "on"){
                $empresa->empresa_estado_cambiar_precio ="1";
            }else{
                $empresa->empresa_estado_cambiar_precio ="0";
            }
            $empresa->empresa_tipo =$request->get('idTipo');
            $empresa->empresa_contribuyenteEspecial =$request->get('idContribuyente');
            if ($request->get('idEstado') == "on"){
                $empresa->empresa_estado ="1";
            }else{
                $empresa->empresa_estado ="0";
            }
            $empresa->save();
            /*Inicio de registro de auditoria */
            $auditoria = new generalController();
            $auditoria->registrarAuditoria('Actualizacion de empresa -> '.$request->get('idNombre'),'0','');
            /*Fin de registro de auditoria */
            DB::commit();
            return redirect('empresa')->with('success','Datos actualizados exitosamente');
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('empresa')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }

    public function updateDatosEpresa(Request $request, $id)
    {
        try{
            DB::beginTransaction();
            $empresa = Empresa::findOrFail($id);
            $empresa->empresa_ruc =$request->get('Ruc');
            $empresa->empresa_nombreComercial =$request->get('idNombre');
            $empresa->empresa_razonSocial =$request->get('idRazon');
            $empresa->empresa_direccion =$request->get('idDireccion');
            $empresa->empresa_telefono =$request->get('idTelefono');
            $empresa->empresa_celular =$request->get('idCelular');
            $empresa->empresa_ciudad =$request->get('idCiudad');
            $empresa->empresa_representante =$request->get('idRepresentante');
            $empresa->empresa_cedula_representante=$request->get('idcedulaRepresentante');
            $empresa->empresa_fecha_ingreso =$request->get('idFecha');
            $empresa->empresa_cedula_contador =$request->get('idcedulacontador');
            $empresa->empresa_contador =$request->get('idcontador');
            $empresa->empresa_email =$request->get('idEmail');
            if ($request->get('idContabilidad') == "on"){
                $empresa->empresa_llevaContabilidad ="1";
            }else{
                $empresa->empresa_llevaContabilidad ="0";
            }
            $empresa->empresa_tipo =$request->get('idTipo');
            $empresa->empresa_contribuyenteEspecial =$request->get('idContribuyente');
            if ($request->get('idEstado') == "on"){
                $empresa->empresa_estado ="1";
            }else{
                $empresa->empresa_estado ="0";
            }
            if ($request->get('idPrecios') == "on"){
                $empresa->empresa_estado_cambiar_precio ="1";
            }else{
                $empresa->empresa_estado_cambiar_precio ="0";
            }
            if($request->file('file-es')){
                if($request->file('file-es')->isValid()){
                    $name = $empresa->empresa_ruc. '.' .$request->file('file-es')->getClientOriginalExtension();
                    $empresa->empresa_logo = $name;
                    $path = $request->file('file-es')->move(public_path().'\logos', $name); 
                }
            }
            $empresa->save();
            /*Inicio de registro de auditoria */
            $auditoria = new generalController();
            $auditoria->registrarAuditoria('Actualizacion de empresa -> '.$request->get('idNombre'),'0','');
            /*Fin de registro de auditoria */
             DB::commit();
            return redirect('datosEmpresa')->with('success','Datos actualizados exitosamente');
        }catch(\Exception $ex){
            DB::rollBack();;
            return redirect('datosEmpresa')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
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
        try {
            DB::beginTransaction();
            $empresa = Empresa::findOrFail($id);
            $empresa->delete();
            /*Inicio de registro de auditoria */
            $auditoria = new generalController();
            $auditoria->registrarAuditoria('Eliminacion de empresa -> '.$empresa->empresa_nombre,'0','');
            /*Fin de registro de auditoria */
            DB::commit();
            return redirect('empresa')->with('success','Datos eliminados exitosamente');
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('empresa')->with('error','El registro no pudo ser borrado, tiene resgitros adjuntos.');
        }
    }

    public function delete($id)
    {
        try{
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            return view('admin.configuracion.empresa.eliminar',['empresa'=>Empresa::findOrFail($id), 'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        }catch(\Exception $ex){
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }

    public function permisos($empresa){
        try{
            $rol = new Rol();
            $rol->rol_nombre='SuperAdministrador';
            $rol->rol_tipo='1';
            $rol->empresa()->associate($empresa);
            $rol->rol_estado=1;
            $rol->save();

            $grupoPer = new GrupoPer();
            $grupoPer->grupo_nombre = 'Seguridad';
            $grupoPer->grupo_icono = 'fa fa-unlock-alt';
            $grupoPer->grupo_orden = '1';
            $grupoPer->grupo_estado  = 1;
            $grupoPer->empresa()->associate($empresa);
            $grupoPer->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Grupos de Permisos';
            $permiso->permiso_ruta = 'grupo';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '1';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save();     

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Permisos';
            $permiso->permiso_ruta = 'permiso';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '2';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save();     

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Roles';
            $permiso->permiso_ruta = 'rol';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '3';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save();     

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Usuarios';
            $permiso->permiso_ruta = 'usuario';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '4';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save();     

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Perfil de Usuario';
            $permiso->permiso_ruta = 'perfil';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '5';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save();     

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Auditoria';
            $permiso->permiso_ruta = 'auditoria';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '6';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save();     

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $grupoPer = new GrupoPer();
            $grupoPer->grupo_nombre = 'Parámetros Generales	';
            $grupoPer->grupo_icono = 'fas fa-sliders-h';
            $grupoPer->grupo_orden = '2';
            $grupoPer->grupo_estado  = 1;
            $grupoPer->empresa()->associate($empresa);
            $grupoPer->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Empresas';
            $permiso->permiso_ruta = 'empresa';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '1';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save();     

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Datos de Empresa';
            $permiso->permiso_ruta = 'datosEmpresa';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '2';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save();     

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Email de la Empresa';
            $permiso->permiso_ruta = 'emailEmpresa';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '3';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save();     

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Firma Electronica';
            $permiso->permiso_ruta = 'firmaElectronica';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '4';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save();     

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Paises';
            $permiso->permiso_ruta = 'pais';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '5';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save();     

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Provincia';
            $permiso->permiso_ruta = 'provincia';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '6';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save();     

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Ciudad';
            $permiso->permiso_ruta = 'ciudad';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '7';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save();     

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Zona';
            $permiso->permiso_ruta = 'zona';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '8';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save();     

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Tarifa Iva';
            $permiso->permiso_ruta = 'tarifaIva';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '9';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save();     

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Sucursales';
            $permiso->permiso_ruta = 'sucursal';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '10';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save();     

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Punto de Emision';
            $permiso->permiso_ruta = 'puntoEmision';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '11';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save();     

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Rango de Documentos';
            $permiso->permiso_ruta = 'rangoDocumento';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '12';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save();     

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Vendedor';
            $permiso->permiso_ruta = 'vendedor';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '13';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save(); 

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Parametrización Contable';
            $permiso->permiso_ruta = 'parametrizacionContable';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '14';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save();     

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Parametrizacion Rol';
            $permiso->permiso_ruta = 'parametrizacionRol';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '15';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save();     

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Impuesto Renta Rol';
            $permiso->permiso_ruta = 'impuestoRentaRol';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '16';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save();     

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();  

            $grupoPer = new GrupoPer();
            $grupoPer->grupo_nombre = 'Contabilidad';
            $grupoPer->grupo_icono = 'fas fa-chart-pie';
            $grupoPer->grupo_orden = '3';
            $grupoPer->grupo_estado  = 1;
            $grupoPer->empresa()->associate($empresa);
            $grupoPer->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Plan cuenta';
            $permiso->permiso_ruta = 'cuenta';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '1';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save();     

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Asiento Diario de Ajuste';
            $permiso->permiso_ruta = 'asientoDiario/asientoAjuste';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '2';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save();     

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Asientos Descuadrados';
            $permiso->permiso_ruta = 'asientoDiario/descuadrados';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '3';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save();     

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Asiento Diario';
            $permiso->permiso_ruta = 'asientoDiario';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '4';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save();     

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Estado de Sit. Financiera';
            $permiso->permiso_ruta = 'estadoFinanciero';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '5';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save();     

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Estado de Resultados';
            $permiso->permiso_ruta = 'estadoResultados';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '6';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save();     

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Balance de comprobacion';
            $permiso->permiso_ruta = 'balanceComprobacion';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '7';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save();     

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Mayor Auxiliar';
            $permiso->permiso_ruta = 'mayorAuxiliar';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '8';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save();     

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Mayor de proveedores';
            $permiso->permiso_ruta = 'mayorProveedores';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '9';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save();     

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Mayor de clientes';
            $permiso->permiso_ruta = 'mayorClientes';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '10';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save();     

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Movimiento Cuenta';
            $permiso->permiso_ruta = 'movimientoCuenta';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '11';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save(); 

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Actualizar Costos';
            $permiso->permiso_ruta = 'actualizarCostos';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '12';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save(); 

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Cierre de Mes Contable';
            $permiso->permiso_ruta = 'cierreMes';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '13';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save(); 

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Cierre Periodo contable';
            $permiso->permiso_ruta = 'inicio';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '14';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save(); 

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $grupoPer = new GrupoPer();
            $grupoPer->grupo_nombre = 'Bancos';
            $grupoPer->grupo_icono = 'fas fa-university';
            $grupoPer->grupo_orden = '4';
            $grupoPer->grupo_estado  = 1;
            $grupoPer->empresa()->associate($empresa);
            $grupoPer->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Lista de Bancos';
            $permiso->permiso_ruta = 'bancoLista';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '1';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save(); 

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Banco';
            $permiso->permiso_ruta = 'banco';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '2';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save(); 

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();
            
            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Cuenta Bancaria';
            $permiso->permiso_ruta = 'cuentaBancaria';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '3';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save(); 

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();
            
            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Rango de Cheque';
            $permiso->permiso_ruta = 'rangoCheque';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '4';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save(); 

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Tipo Movimiento de Banco';
            $permiso->permiso_ruta = 'tipoMovimientoBanco';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '5';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save(); 

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Egreso de Banco';
            $permiso->permiso_ruta = 'D/EegresoBanco';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '6';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save(); 

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();
            
            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Ingreso de Banco';
            $permiso->permiso_ruta = 'D/EingresoBanco';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '7';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save(); 

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Nota de Credito Banco';
            $permiso->permiso_ruta = 'D/EnotaCreditoBanco';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '8';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save(); 

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Nota de Debito Banco';
            $permiso->permiso_ruta = 'D/EnotaDebitoBanco';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '9';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save(); 

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Lista de Egresos de Banco';
            $permiso->permiso_ruta = 'listaEgresoBanco';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '10';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save(); 

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Lista de Ingreso de Banco';
            $permiso->permiso_ruta = 'listaIngresoBanco';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '11';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save(); 

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Lista de N. Credito Bancaria';
            $permiso->permiso_ruta = 'listanotaCreditoBancario';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '12';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save(); 

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Lista de N. Debito Bancaria';
            $permiso->permiso_ruta = 'listanotaDebitoBancario	';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '13';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save(); 

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Reporte de Cheques';
            $permiso->permiso_ruta = 'listaCheque';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '14';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save(); 

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Conciliación Bancaria';
            $permiso->permiso_ruta = 'conciliacionBancaria';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '15';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save(); 

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $grupoPer = new GrupoPer();
            $grupoPer->grupo_nombre = 'Inventario';
            $grupoPer->grupo_icono = 'fas fa-boxes';
            $grupoPer->grupo_orden = '5';
            $grupoPer->grupo_estado  = 1;
            $grupoPer->empresa()->associate($empresa);
            $grupoPer->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Bodega';
            $permiso->permiso_ruta = 'bodega';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '1';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save(); 

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Bodeguero';
            $permiso->permiso_ruta = 'bodeguero';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '2';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save(); 

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Transportista';
            $permiso->permiso_ruta = 'transportista';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '3';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save(); 

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Categoría del Producto';
            $permiso->permiso_ruta = 'categoriaProducto';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '4';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save(); 

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Tamaño Producto';
            $permiso->permiso_ruta = 'tamanoProducto';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '5';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save(); 

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Unidad Medida';
            $permiso->permiso_ruta = 'unidadMedida';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '6';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save(); 

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Grupo de Producto';
            $permiso->permiso_ruta = 'grupoProducto';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '7';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save(); 

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Marca del Producto';
            $permiso->permiso_ruta = 'marcaProducto';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '8';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save(); 

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Producto';
            $permiso->permiso_ruta = 'producto';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '9';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save(); 

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Tipo de Movimiento';
            $permiso->permiso_ruta = 'tipoMovimiento';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '10';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save(); 

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Egreso de Bodega';
            $permiso->permiso_ruta = 'D/EegresoBodega';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '11';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save(); 

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Ingreso de Bodega';
            $permiso->permiso_ruta = 'D/EingresoBodega';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '12';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save(); 

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Kardex';
            $permiso->permiso_ruta = 'kardex';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '13';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save(); 

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Kardex Costo';
            $permiso->permiso_ruta = 'kardexCosto';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '14';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save(); 

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Lista de Egresos de Bodega';
            $permiso->permiso_ruta = 'egresoBodega';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '15';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save(); 

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Lista de Ingresos de Bodega';
            $permiso->permiso_ruta = 'ingresoBodega';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '16';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save(); 

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $grupoPer = new GrupoPer();
            $grupoPer->grupo_nombre = 'Compras';
            $grupoPer->grupo_icono = 'fas fa-shopping-basket';
            $grupoPer->grupo_orden = '6';
            $grupoPer->grupo_estado  = 1;
            $grupoPer->empresa()->associate($empresa);
            $grupoPer->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Centro Consumo';
            $permiso->permiso_ruta = 'centroConsumo';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '1';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save(); 

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();
            
            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Transaccion Compra';
            $permiso->permiso_ruta = 'D/EtransaccionCompra';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '2';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save(); 

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Liquidación de compra';
            $permiso->permiso_ruta = 'D/EliquidacionCompra';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '3';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save(); 

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Lista de Compras';
            $permiso->permiso_ruta = 'listaCompras';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '4';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save(); 

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Reporte de C. Consumo';
            $permiso->permiso_ruta = 'listaCc';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '5';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save(); 

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Lista de Docs. de Compras';
            $permiso->permiso_ruta = 'listatransaccionCompra';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '6';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save(); 

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Lista de Liqui. de Compras';
            $permiso->permiso_ruta = 'listaliquidacionCompra';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '7';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save(); 

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Configurar Categoría Costo';
            $permiso->permiso_ruta = 'categoriaCosto';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '8';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save(); 

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $grupoPer = new GrupoPer();
            $grupoPer->grupo_nombre = 'Ventas';
            $grupoPer->grupo_icono = 'fas fa-shopping-cart';
            $grupoPer->grupo_orden = '7';
            $grupoPer->grupo_estado  = 1;
            $grupoPer->empresa()->associate($empresa);
            $grupoPer->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Factura';
            $permiso->permiso_ruta = 'D/Efactura';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '1';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save(); 

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Factura Coberturas';
            $permiso->permiso_ruta = 'D/Einicio';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '2';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save(); 

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Nota de Entrega';
            $permiso->permiso_ruta = 'D/Enotaentrega';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '3';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save(); 

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Proforma';
            $permiso->permiso_ruta = 'D/Eproforma';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '4';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save(); 

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Nota de Credito';
            $permiso->permiso_ruta = 'D/EnotaCredito';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '5';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save(); 

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Nota de Debito';
            $permiso->permiso_ruta = 'D/EnotaDebito';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '6';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save(); 

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Orden de Despacho';
            $permiso->permiso_ruta = 'D/EordenDespacho';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '7';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save(); 

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Guía de Remisión';
            $permiso->permiso_ruta = 'D/EguiaRemision';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '8';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save(); 

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Anular Documento';
            $permiso->permiso_ruta = 'anularDocumento';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '9';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save(); 

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Retención Recibida';
            $permiso->permiso_ruta = 'retencionVenta';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '10';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save(); 

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Lista de Facturas';
            $permiso->permiso_ruta = 'listaFactura';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '11';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save(); 

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Lista de Nota de Entrega';
            $permiso->permiso_ruta = 'notaentrega';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '12';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save(); 

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Lista de Proformas';
            $permiso->permiso_ruta = 'listaProforma';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '13';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save(); 

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Lista de Notas de Crédito';
            $permiso->permiso_ruta = 'listanotaCredito';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '14';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save(); 

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Lista de Notas de Débito';
            $permiso->permiso_ruta = 'listanotaDebito';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '15';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save(); 

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Lista Ordenes Despacho';
            $permiso->permiso_ruta = 'listaOrdenes';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '16';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save(); 

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Lista Guías de Remisiones';
            $permiso->permiso_ruta = 'listaGuias';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '17';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save(); 

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Lista Guías - Ordenes';
            $permiso->permiso_ruta = 'listaGuiasOrdenes';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '18';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save(); 

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Lista de Ventas';
            $permiso->permiso_ruta = 'listaVentas';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '19';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save();

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Reporte Ordenes de Despacho';
            $permiso->permiso_ruta = 'ordenDespacho/reporte';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '20';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save(); 

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $grupoPer = new GrupoPer();
            $grupoPer->grupo_nombre = 'SRI';
            $grupoPer->grupo_icono = 'fas fa-dollar-sign';
            $grupoPer->grupo_orden = '8';
            $grupoPer->grupo_estado  = 1;
            $grupoPer->empresa()->associate($empresa);
            $grupoPer->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Tipo de Identificación';
            $permiso->permiso_ruta = 'tipoIdentificacion';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '1';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save(); 

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Tipo Transaccion';
            $permiso->permiso_ruta = 'tipoTransaccion';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '2';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save(); 

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Transaccion Identificacion';
            $permiso->permiso_ruta = 'transaccionIdentificacion';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '3';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save(); 

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Tipo de Comprobante';
            $permiso->permiso_ruta = 'tipoComprobante';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '4';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save(); 

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Sustento Tributario';
            $permiso->permiso_ruta = 'sustentoTributario';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '5';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save(); 

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Tipo de Sujeto';
            $permiso->permiso_ruta = 'tipoSujeto';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '6';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save(); 

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Forma de Pago';
            $permiso->permiso_ruta = 'formaPago';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '7';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save(); 

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Conceptos de Retención';
            $permiso->permiso_ruta = 'conceptoRetencion';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '8';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save(); 

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Anexo Transaccional';
            $permiso->permiso_ruta = 'atsSRI';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '9';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save(); 

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Reporte de Compras';
            $permiso->permiso_ruta = 'reporteCompras';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '10';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save(); 

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Reporte de Ventas';
            $permiso->permiso_ruta = 'reporteVentas';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '11';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save(); 

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Reporte de Doc. Anulados';
            $permiso->permiso_ruta = 'reporteDocsAnulados';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '12';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save(); 

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Lista de Ret. Emitidas';
            $permiso->permiso_ruta = 'listaRetencion';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '13';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save(); 

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Lista de Ret. Recibidas';
            $permiso->permiso_ruta = 'listaRetencionRecibida';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '14';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save(); 

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Documentos Electrónicos';
            $permiso->permiso_ruta = 'docsElectronicos';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '15';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save(); 

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Eliminación de Comprobantes	';
            $permiso->permiso_ruta = 'eliminacionComprantes';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '16';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save(); 

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();            

            $grupoPer = new GrupoPer();
            $grupoPer->grupo_nombre = 'Cuentas por Cobrar';
            $grupoPer->grupo_icono = 'fas fa-file-invoice-dollar';
            $grupoPer->grupo_orden = '9';
            $grupoPer->grupo_estado  = 1;
            $grupoPer->empresa()->associate($empresa);
            $grupoPer->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Categoría de Cliente';
            $permiso->permiso_ruta = 'categoriaCliente';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '1';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save(); 

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Tipo de Cliente';
            $permiso->permiso_ruta = 'tipoCliente';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '2';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save(); 

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Crédito';
            $permiso->permiso_ruta = 'credito';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '3';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save(); 

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Cliente';
            $permiso->permiso_ruta = 'cliente';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '4';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save(); 

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Pagos Cuentas Cobrar';
            $permiso->permiso_ruta = 'pagosCXC';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '5';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save(); 

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();
            
            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Anticipo Cliente';
            $permiso->permiso_ruta = 'D/EanticipoCliente';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '6';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save(); 

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Descontar Anticipo Cliente';
            $permiso->permiso_ruta = 'descontarAntCli';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '7';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save(); 

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Lista de Anticipos';
            $permiso->permiso_ruta = 'listaAnticipoCliente';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '8';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save(); 

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Lista de Cartera';
            $permiso->permiso_ruta = 'listaCartera';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '9';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save(); 

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Eliminar pagos CXC';
            $permiso->permiso_ruta = 'eliminarPagoCXC';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '10';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save(); 

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Eliminar anticipos Clien';
            $permiso->permiso_ruta = 'eliminatAntCli';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '11';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save(); 

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Estado de Cuenta CXC';
            $permiso->permiso_ruta = 'cxc';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '12';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save(); 

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();        

            $grupoPer = new GrupoPer();
            $grupoPer->grupo_nombre = 'Cuentas por Pagar';
            $grupoPer->grupo_icono = 'fas fa-hand-holding-usd';
            $grupoPer->grupo_orden = '10';
            $grupoPer->grupo_estado  = 1;
            $grupoPer->empresa()->associate($empresa);
            $grupoPer->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Categoría de Proveedor';
            $permiso->permiso_ruta = 'categoriaProveedor';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '1';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save(); 

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Proveedor';
            $permiso->permiso_ruta = 'proveedor';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '2';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save(); 

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Pagos Cuentas Pagar';
            $permiso->permiso_ruta = 'pagosCXP';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '3';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save(); 

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Anticipo Proveedor';
            $permiso->permiso_ruta = 'D/EanticipoProveedor';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '4';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save(); 

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Descontar Anticipo Proveedor';
            $permiso->permiso_ruta = 'descontarAntPro';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '5';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save(); 

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Lista de Anticipos';
            $permiso->permiso_ruta = 'listaAnticipoProveedor';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '6';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save();

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();
        
            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Lista de Deuda';
            $permiso->permiso_ruta = 'listaDeudas';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '7';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save(); 

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Eliminar pagos CXP';
            $permiso->permiso_ruta = 'eliminarPagoCXP';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '8';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save(); 

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Eliminar anticipos Prov';
            $permiso->permiso_ruta = 'eliminatAntPro';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '9';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save(); 

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Estado de Cuenta CXP';
            $permiso->permiso_ruta = 'cxp';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '10';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save();  

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $grupoPer = new GrupoPer();
            $grupoPer->grupo_nombre = 'Caja';
            $grupoPer->grupo_icono = 'fas fa-cash-register';
            $grupoPer->grupo_orden = '11';
            $grupoPer->grupo_estado  = 1;
            $grupoPer->empresa()->associate($empresa);
            $grupoPer->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Tipo Movimeinto Caja';
            $permiso->permiso_ruta = 'tipoMovimientoCaja';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '1';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save(); 

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Cajas';
            $permiso->permiso_ruta = 'caja';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '2';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save(); 

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Apertura de Caja';
            $permiso->permiso_ruta = 'arqueoCaja';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '3';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save(); 

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Depositos';
            $permiso->permiso_ruta = 'depositoCaja';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '4';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save(); 

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Egreso de Caja';
            $permiso->permiso_ruta = 'D/EegresoCaja';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '5';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save(); 

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Ingreso de Caja';
            $permiso->permiso_ruta = 'D/EingresoCaja';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '6';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save(); 

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Faltante de Caja';
            $permiso->permiso_ruta = 'D/EfaltanteCaja';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '7';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save(); 

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Sobrante de Caja';
            $permiso->permiso_ruta = 'D/EsobranteCaja';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '8';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save(); 

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Cierre de Caja';
            $permiso->permiso_ruta = 'cierreCaja';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '9';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save(); 

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();
            
            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Cuadre de Caja';
            $permiso->permiso_ruta = 'cuadreCajaAbierta';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '10';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save(); 

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Lista de Cierre de Caja';
            $permiso->permiso_ruta = 'listaCierreCaja';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '11';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save(); 

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Lista de Egreso de Caja';
            $permiso->permiso_ruta = 'listaEgresoCaja';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '12';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save(); 

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Lista de Ingresos de Caja';
            $permiso->permiso_ruta = 'listaIngresoCaja';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '13';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save(); 

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Lista de Faltantes de Caja';
            $permiso->permiso_ruta = 'listaFaltanteCaja';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '14';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save(); 

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Lista de Sobrantes de Caja';
            $permiso->permiso_ruta = 'listaSobranteCaja';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '15';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save(); 

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $grupoPer = new GrupoPer();
            $grupoPer->grupo_nombre = 'Activos Fijos';
            $grupoPer->grupo_icono = 'fas fa-laptop-house';
            $grupoPer->grupo_orden = '12';
            $grupoPer->grupo_estado  = 1;
            $grupoPer->empresa()->associate($empresa);
            $grupoPer->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Grupo de Activo';
            $permiso->permiso_ruta = 'grupoActivo';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '1';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save(); 

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Activo Fijo';
            $permiso->permiso_ruta = 'activoFijo';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '2';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save(); 

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Depreciación Mensual';
            $permiso->permiso_ruta = 'depreciacionMensual';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '3';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save(); 

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Reporte de Depreciación';
            $permiso->permiso_ruta = 'reporteDepreciacion';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '4';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save(); 

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Venta de Activo Fijo';
            $permiso->permiso_ruta = 'ventaActivo';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '5';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save(); 

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();
         
            $grupoPer = new GrupoPer();
            $grupoPer->grupo_nombre = 'Recursos Humanos';
            $grupoPer->grupo_icono = 'fas fa-user-tie';
            $grupoPer->grupo_orden = '13';
            $grupoPer->grupo_estado  = 1;
            $grupoPer->empresa()->associate($empresa);
            $grupoPer->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Rubro';
            $permiso->permiso_ruta = 'rubro';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '1';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save(); 

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Departamento';
            $permiso->permiso_ruta = 'departamento';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '2';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save(); 

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Cargo Empleado';
            $permiso->permiso_ruta = 'empleadoCargo';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '3';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save(); 

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Tipo Empleado';
            $permiso->permiso_ruta = 'tipoEmpleado';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '4';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save(); 

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Empleado';
            $permiso->permiso_ruta = 'empleado';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '5';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save(); 

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Anticipo Empleado';
            $permiso->permiso_ruta = 'D/EanticipoEmpleado';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '6';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save(); 

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Descontar Anticipo Empleado';
            $permiso->permiso_ruta = 'descontarAntEmp';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '7';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save(); 

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Rol Individual';
            $permiso->permiso_ruta = 'rolindividual';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '8';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save(); 

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Rol Consolidado';
            $permiso->permiso_ruta = 'rolConsolidado';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '9';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save(); 

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Quincena Individual';
            $permiso->permiso_ruta = 'pquincena';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '10';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save(); 

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Quincena Consolidada';
            $permiso->permiso_ruta = 'quincenaConsolidada';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '11';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save(); 

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Vacaciones';
            $permiso->permiso_ruta = 'vacacion';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '12';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save(); 

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Control de Dias';
            $permiso->permiso_ruta = 'controldiario';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '13';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save(); 

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Rol Operativo';
            $permiso->permiso_ruta = 'roloperativo';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '14';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save(); 

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Decimo Tercero';
            $permiso->permiso_ruta = 'decimoT';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '15';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save(); 

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Decimo Cuarto';
            $permiso->permiso_ruta = 'decimoC';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '16';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save(); 

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Alimentación';
            $permiso->permiso_ruta = 'alimentacion';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '17';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save(); 

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Lista de Anticipos';
            $permiso->permiso_ruta = 'listaAnticipoEmpleado';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '18';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save(); 

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Lista de Vacaciones';
            $permiso->permiso_ruta = 'lvacaciones';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '19';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save(); 

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Listas de Quincena';
            $permiso->permiso_ruta = 'lquincena';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '20';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save(); 

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Lista de Roles';
            $permiso->permiso_ruta = 'listaroles';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '21';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save(); 

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Contabilización Mensual';
            $permiso->permiso_ruta = 'contabilizacionMensual';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '22';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save(); 

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $grupoPer = new GrupoPer();
            $grupoPer->grupo_nombre = 'Citas Medicas';
            $grupoPer->grupo_icono = 'fas fa-calendar-alt';
            $grupoPer->grupo_orden = '14';
            $grupoPer->grupo_estado  = 1;
            $grupoPer->empresa()->associate($empresa);
            $grupoPer->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Paciente';
            $permiso->permiso_ruta = 'paciente';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '1';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save(); 

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Orden de Atencion';
            $permiso->permiso_ruta = 'ordenAtencion';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '2';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save(); 

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Signos Vitales';
            $permiso->permiso_ruta = 'signosVitales';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '3';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save(); 

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Atención de Citas';
            $permiso->permiso_ruta = 'atencionCitas';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '4';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save(); 

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Historial Clínico';
            $permiso->permiso_ruta = 'historialClinico';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '5';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save(); 

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $grupoPer = new GrupoPer();
            $grupoPer->grupo_nombre = 'Configurar Centro Salud';
            $grupoPer->grupo_icono = 'fas fa-hospital';
            $grupoPer->grupo_orden = '15';
            $grupoPer->grupo_estado  = 1;
            $grupoPer->empresa()->associate($empresa);
            $grupoPer->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Tipo de Seguro';
            $permiso->permiso_ruta = 'tipoSeguro';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '1';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save(); 

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Tipo Dependencia';
            $permiso->permiso_ruta = 'tipoDependencia';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '2';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save(); 

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Tipo Medicamento';
            $permiso->permiso_ruta = 'tipoMedicamento';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '3';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save(); 

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Tipo Imagen';
            $permiso->permiso_ruta = 'tipoImagen';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '4';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save(); 

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Empresa';
            $permiso->permiso_ruta = 'entidad';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '5';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save(); 

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Especialidad';
            $permiso->permiso_ruta = 'especialidad';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '6';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save(); 

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Medico';
            $permiso->permiso_ruta = 'medico';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '7';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save(); 

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Procedimiento Especialidad';
            $permiso->permiso_ruta = 'procedimientoEspecialidad';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '8';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save(); 

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();
                
            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Aseguradora Procedimiento';
            $permiso->permiso_ruta = 'aseguradoraProcedimiento';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '9';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save(); 

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Empresa Procedimiento';
            $permiso->permiso_ruta = 'entidadProcedimiento';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '10';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save(); 

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Documento Orden Atencion';
            $permiso->permiso_ruta = 'documentoOrdenAtencion';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '11';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save(); 

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();    

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Medicamento';
            $permiso->permiso_ruta = 'medicamento';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '12';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save(); 

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Imagen';
            $permiso->permiso_ruta = 'imagen';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '13';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save(); 

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Enfermedad';
            $permiso->permiso_ruta = 'enfermedad';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '14';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save(); 

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $grupoPer = new GrupoPer();
            $grupoPer->grupo_nombre = 'Laboratorio';
            $grupoPer->grupo_icono = 'fas fa-flask';
            $grupoPer->grupo_orden = '16';
            $grupoPer->grupo_estado  = 1;
            $grupoPer->empresa()->associate($empresa);
            $grupoPer->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Tipo de Muestra';
            $permiso->permiso_ruta = 'tipoMuestra';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '1';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save(); 

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Tipo Recipiente';
            $permiso->permiso_ruta = 'tipoRecipiente';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '2';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save(); 

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Tipo Examen';
            $permiso->permiso_ruta = 'tipoExamen';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '3';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save(); 

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Examen';
            $permiso->permiso_ruta = 'examen';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '4';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save(); 

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Orden de Examen';
            $permiso->permiso_ruta = 'ordenesExamen';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '5';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save(); 

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Analisis de Laboratorio';
            $permiso->permiso_ruta = 'analisisLaboratorio';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '6';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save(); 

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();            

            $grupoPer = new GrupoPer();
            $grupoPer->grupo_nombre = 'Farmacia';
            $grupoPer->grupo_icono = 'fas fa-notes-medical';
            $grupoPer->grupo_orden = '17';
            $grupoPer->grupo_estado  = 1;
            $grupoPer->empresa()->associate($empresa);
            $grupoPer->save();
            
            $permiso = new Permiso();
            $permiso->permiso_nombre = 'Recetas';
            $permiso->permiso_ruta = 'receta';
            $permiso->permiso_tipo = '1';
            $permiso->permiso_icono = 'far fa-circle';
            $permiso->permiso_orden = '1';
            $permiso->permiso_estado = 1;
            $permiso->empresa()->associate($empresa);
            $permiso->grupo()->associate($grupoPer);
            $permiso->save(); 

            $rol_permiso= new Rol_Permiso();
            $rol_permiso->permiso()->associate($permiso);
            $rol_permiso->rol()->associate($rol);
            $rol_permiso->save();

            return $rol;
        }catch(\Exception $ex){
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
}