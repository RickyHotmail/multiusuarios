<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use App\Models\Punto_Emision;
use App\Models\Rol;
use App\Models\User;
use App\Models\Usuario_PuntoE;
use App\Models\Usuario_Rol;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use App\Libraries\verifyEmail;
use App\Models\Servidor_Correo;
use PHPMailer\PHPMailer\SMTP;

class usuarioController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
            $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $usuarios=User::usuarios()->get();
            return view('admin.seguridad.usuario.index',['usuarios'=>$usuarios, 'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
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
            $usuario = new User();
            $usuario->user_username = $request->get('idUsername');
            $usuario->user_cedula = $request->get('idCedula');  
            $usuario->user_nombre = $request->get('idNombre');  
            $usuario->user_correo = $request->get('idCorreo');            
            $usuario->user_tipo  = 1;
            $usuario->user_estado  = 1;
            $password=$this->generarPass();
            $usuario->password  = bcrypt($password);
            $usuario->empresa_id = Auth::user()->empresa_id;
            $usuario->save();
            
            DB::afterCommit(function () use($usuario, $password){
                $generalController=new generalController();

                $html  = "<p style='text-align: justify'>";
                $html .= "Bienvenido nuevo usuario <strong>$usuario->user_nombre</strong>,<br><br>";
                $html .= "Tu clave temportal para el ingreso al Sistema NeoPagupa es: <br><strong>$password</strong><br>La puedes cambiar más adelante cuando quieras.<br><br><br>";
                $html .= "Te deseamos el mejor desempeño en tus tareas.<br><br><br>";
                $html .= "Atentamente,<br><br><br>";
                $html .= "__________________________________________<br>";
                $html .= "<strong>PAGUPA SOFT</strong>";
                $html .= "</p>";


                $textoPlano = "Bienvenido nuevo usuario $usuario->user_nombre,\n\n";
                $textoPlano .= "Tu clave temporal para el ingreso al Sistema NeoPagupa es: $password, la puedes cambiar más adelante cuando quieras.\n\n\n";
                $textoPlano .= "Te deseamos el mejor desempeño en tus tareas,\n\n\n";
                $textoPlano .= "Atentamente,\n\n\n";
                $textoPlano .= "__________________________________________\n";
                $textoPlano .= "PAGUPA SOFT";

                $generalController->enviarCorreo($usuario->user_correo, $usuario->user_nombre, 'Registro de Nuevo Usuario', $html, $textoPlano, []);
            });
            
            
            /*Inicio de registro de auditoria */
            $auditoria = new generalController();
            $auditoria->registrarAuditoria('Registro de usuario -> '.$request->get('idUsername'),'0','');
            /*Fin de registro de auditoria */
            DB::commit();
            return redirect('usuario')->with('success','Datos guardados exitosamente');
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('usuario')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }

    public function restablecePass($id){
        $servidor=Servidor_Correo::servidorCorreo()->first();

        try{
            $usuario=User::usuario($id)->first();
            if(!$usuario){
                return redirect('usuario');
            }
            DB::beginTransaction();
            $usuario= User::findOrFail($id);
            $password=$this->generarPass();
            $usuario->user_cambio_clave=1;
            $usuario->password  = bcrypt($password);
            $usuario->save();
            
            DB::afterCommit(function () use($usuario, $password){
                $generalController=new generalController();

                $html  = "<p style='text-align: justify'>";
                $html .= "Estimado <strong>$usuario->user_nombre</strong>,<br><br>";
                $html .= "Tu clave de ingreso al Sistema NeoPagupa fué restablecida, la clave temporal es: <br><strong>$password</strong><br><br><br>";
                $html .= "Se te pedirá que la cambies en el siguiente inicio de sesión.<br><br><br>";
                $html .= "Atentamente,<br><br><br>";
                $html .= "__________________________________________<br>";
                $html .= "<strong>PAGUPA SOFT</strong>";
                $html .= "</p>";


                $textoPlano = "Estimado $usuario->user_nombre,\n\n";
                $textoPlano .= "Tu clave de ingreso al Sistema NeoPagupa fué restablecida, la clave temporal es:<br> $password \n\n\n";
                $textoPlano .= "Se te pedirá que la cambies en el siguiente inicio de sesión,\n\n\n";
                $textoPlano .= "Atentamente,\n\n\n";
                $textoPlano .= "__________________________________________\n";
                $textoPlano .= "PAGUPA SOFT";

                $generalController->enviarCorreo($usuario->user_correo, $usuario->user_nombre, 'Restablecer Password', $html, $textoPlano, []);
            });
            
            
            /*Inicio de registro de auditoria */
            $auditoria = new generalController();
            $auditoria->registrarAuditoria('Restablecer contraseña de usuario -> '.$usuario->user_username,'0','');
            /*Fin de registro de auditoria */
             DB::commit();
            return redirect('usuario')->with('success','Contraseña restablecida, revisa tu correo electrónico');
        } catch(\Exception $ex){
            DB::rollBack();
            return redirect('usuario')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }

    public function enviarNuevaClave(Request $request){
        try{
            $usuarios=DB::select(DB::raw("
                select users.* 
                from users join empresa on users.empresa_id=empresa.empresa_id
                where users.user_correo='$request->idCorreo' and empresa.empresa_ruc='$request->idRuc'
            "));

            

            if(!$usuarios) return redirect('/recuperarClave')->with("error", "La información que ingreso es incorrecta");
            $usuario=$usuarios[0];

            DB::beginTransaction();
            $usuario= User::findOrFail($usuario->user_id);
            $password=$this->generarPass();
            $usuario->user_cambio_clave=1;
            $usuario->password  = bcrypt($password);
            $usuario->save();

            DB::afterCommit(function () use($usuario, $password){
                $generalController=new generalController();

                $html  = "<p style='text-align: justify'>";
                $html .= "Estimado <strong>$usuario->user_nombre</strong>,<br><br>";
                $html .= "Tu clave de ingreso al Sistema NeoPagupa fué restablecida, la clave temporal es: <br><strong>$password</strong><br><br><br>";
                $html .= "Se te pedirá que la cambies en el siguiente inicio de sesión.<br><br><br>";
                $html .= "Atentamente,<br><br><br>";
                $html .= "__________________________________________<br>";
                $html .= "<strong>PAGUPA SOFT</strong>";
                $html .= "</p>";


                $textoPlano = "Estimado $usuario->user_nombre,\n\n";
                $textoPlano .= "Tu clave de ingreso al Sistema NeoPagupa fué restablecida, la clave temporal es:<br> $password \n\n\n";
                $textoPlano .= "Se te pedirá que la cambies en el siguiente inicio de sesión,\n\n\n";
                $textoPlano .= "Atentamente,\n\n\n";
                $textoPlano .= "__________________________________________\n";
                $textoPlano .= "PAGUPA SOFT";

                $generalController->enviarCorreo($usuario->user_correo, $usuario->user_nombre, 'Restablecer Password', $html, $textoPlano, []);
            });
            
            
            /*Inicio de registro de auditoria */
            $auditoria = new generalController();
            $auditoria->registrarAuditoria('Restablecer contraseña de usuario -> '.$usuario->user_username,'0','');
            /*Fin de registro de auditoria */
            DB::commit();
            return redirect('login')->with('success','Contraseña restablecida, revisa tu correo electrónico');
        } catch(\Exception $ex){
            DB::rollBack();
            //return $ex->getMessage();
            return redirect('usuario')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }

    public function generarPass(){
        $caracteres='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        $longpalabra=8;
        $pass='';
        for($pass='', $n=strlen($caracteres)-1; strlen($pass) < $longpalabra ; ) {
            $x = rand(0,$n);
            $pass.= $caracteres[$x];
        }
        return $pass;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
            $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $usuario=User::usuario($id)->first();
            if($usuario){
                return view('admin.seguridad.usuario.ver',['usuario'=>$usuario, 'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
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
        try {
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
            $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $usuario=User::usuario($id)->first();
            if($usuario){
                return view('admin.seguridad.usuario.editar',['usuario'=>$usuario, 'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
            }else{
                return redirect('/denegado');
            }
        }
        catch(\Exception $ex){      
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }

    public function cambiarClave()
    {
        try {
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
            $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $usuario=User::findOrFail(Auth::user()->user_id);

            if($usuario){
                return view('admin.seguridad.usuario.cambiarClave',['user_id'=> $usuario->user_id,'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
            }else{
                return redirect('/denegado');
            }
        }
        catch(\Exception $ex){      
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }

    public function recuperarCuenta()
    {
        return view('admin.seguridad.usuario.recuperarClave');
    }

    public function updatePassword(Request $request)
    {
        try{
            DB::beginTransaction();
            $usuario=User::findOrFail($request->get("user_id"));

            if($request->get("user_id")==Auth::user()->user_id){
                $clave=$request->get("idClaveActual");
                if(\Hash::check($clave, $usuario->password)){
                    $password=$request->get("idClaveNueva");
                    $usuario->password  = bcrypt($password);
                    $usuario->user_cambio_clave=0;
                    $usuario->save();
                    /*Inicio de registro de auditoria */
                    $auditoria = new generalController();
                    $auditoria->registrarAuditoria('Actualizacion de clave -> '.$usuario->user_username,'0','Usuario con id -> '.$request->get("user_id"));
                    /*Fin de registro de auditoria */
                    DB::commit();
                    return redirect('cambiarClave')->with('success','Datos actualizados exitosamente');
                }
                else{
                    DB::rollBack();
                    return redirect('cambiarClave')->with('error2','Ocurrio un error al tratar de actualizar la Clave, las claves no coinciden.');
                }
            }
            else{
                DB::rollBack();
                return redirect('cambiarClave')->with('error2','Ocurrio un error al tratar de actualizar su Clave.');
            }
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('usuario')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
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
            $usuario=User::findOrFail($id);
            $usuario->user_username = $request->get('idUsername');
            $usuario->user_cedula = $request->get('idCedula');  
            $usuario->user_nombre = $request->get('idNombre');  
            $usuario->user_correo = $request->get('idCorreo');         
            if ($request->get('idEstado') == "on"){   
                $usuario->user_estado=1;
            }else{
                $usuario->user_estado=0;
            }
            $usuario->save();
            /*Inicio de registro de auditoria */
            $auditoria = new generalController();
            $auditoria->registrarAuditoria('Actualizacion de usuario -> '.$request->get('idUsername'),'0','Usuario con id -> '.$id);
            /*Fin de registro de auditoria */
            DB::commit();
            return redirect('usuario')->with('success','Datos actualizados exitosamente');
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('usuario')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
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
            $usuario = User::findOrFail($id);
            $usuario->delete();
            /*Inicio de registro de auditoria */
            $auditoria = new generalController();
            $auditoria->registrarAuditoria('Eliminacion de usuario -> '.$usuario->user_username,'0','Usuario con id -> '.$id);
            /*Fin de registro de auditoria */
            DB::commit();
            return redirect('usuario')->with('success','Datos eliminados exitosamente');
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('usuario')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }

    public function delete($id)
    {
        try {
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
            $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $usuario=User::usuario($id)->first();
            if($usuario){
                return view('admin.seguridad.usuario.eliminar',['usuario'=>$usuario, 'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
            }else{
                return redirect('/denegado');
            }
        }
        catch(\Exception $ex){      
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }

    public function roles($id)
    {
        try {
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
            $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $roles=Rol::roles()->where('rol_nombre','<>','SuperAdministrador')->get();
            $usuario=User::usuario($id)->first();
            if($usuario){
                return view('admin.seguridad.usuario.roles',['usuario'=>$usuario, 'PE'=>Punto_Emision::puntos()->get(),'roles'=>$roles, 'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
            }else{
                return redirect('/denegado');
            }
        }
        catch(\Exception $ex){      
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function guardarRoles(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            $rolesAsignados='';
            $usuario_rol=Usuario_Rol::where('user_id','=',$id)->delete();
            $roles=Rol::roles()->get();
            foreach ($roles as $rol) {  
                if($request->get($rol->rol_id) == "on"){
                    $usuario_rol= new Usuario_Rol;
                    $usuario_rol->rol_id=$rol->rol_id;
                    $usuario_rol->user_id=$id;
                    $usuario_rol->save();
                    $rolesAsignados=$rolesAsignados.'-'.$rol->rol_nombre;
                }
            }
            /*Inicio de registro de auditoria */
            $auditoria = new generalController();
            $auditoria->registrarAuditoria('Actualizacion de roles de usuario con id -> '.$id,'0','Los roles asignados fueron -> '.$rolesAsignados);
            /*Fin de registro de auditoria */
            DB::commit();
            return redirect('usuario')->with('success','Datos guardados exitosamente');
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('usuario')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function puntosEmisionPermiso($id)
    {
        try {
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
            $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $puntosE=Punto_Emision::Puntos()->get();
            $usuario=User::usuario($id)->first();
            if($usuario){
                return view('admin.seguridad.usuario.puntosE',['usuario'=>$usuario, 'PE'=>Punto_Emision::puntos()->get(),'puntosE'=>$puntosE, 'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
            }else{
                return redirect('/denegado');
            }
        }
        catch(\Exception $ex){      
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function guardarPuntos(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            $puntosAsignados='';
            $usuario_puntoE=Usuario_PuntoE::where('user_id','=',$id)->delete();
            $puntosE=Punto_Emision::Puntos()->get();
            foreach ($puntosE as $punto) {  
                if($request->get($punto->punto_id) == "on"){
                    $usuario_puntoE= new Usuario_PuntoE;
                    $usuario_puntoE->punto_id=$punto->punto_id;
                    $usuario_puntoE->user_id=$id;
                    $usuario_puntoE->usuarioP_estado=1;
                    $usuario_puntoE->save();
                    $puntosAsignados=$puntosAsignados.'-'.$punto->punto_serie.'->'.$punto->punto_descripcion;
                }
            }
            /*Inicio de registro de auditoria */
            $auditoria = new generalController();
            $auditoria->registrarAuditoria('Actualizacion de permisos a puntos de emision de usuario con id -> '.$id,'0','Los puntos de emision asignados fueron -> '.$puntosAsignados);
            /*Fin de registro de auditoria */
            DB::commit();
            return redirect('usuario')->with('success','Datos guardados exitosamente');
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('usuario')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
}
