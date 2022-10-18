<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Cliente;
use App\Models\Proveedor;
use App\Models\Empresa;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class envioCorreosController extends Controller
{
    public function index(){
        $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
        $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();

        $clientes=Cliente::clientes()->get();

        ///return $clientes;
        return view('admin.envioCorreos.index', ["tipo"=>1, "clientes"=>$clientes,'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
    }

    public function buscar(Request $request){
        $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
        $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();

        if($request->get("tipo_correo")==1){
            $clientes=Cliente::clientes()->get();
            return view('admin.envioCorreos.index', ["tipo"=>1, "clientes"=>$clientes,'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        }
        else{
            $proveedores=Proveedor::proveedores()->get();

            return view('admin.envioCorreos.index', ["tipo"=>2, "proveedores"=>$proveedores,'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        }
    }

    public function enviarCorreo(Request $request){
        //return $request;
        /*
        $correos="";

        if(count($request->check)>0){
            foreach($request->check as $g){
                echo $request->correo[$g-1].'<br>';

                $correos.=$request->correo[$g-1].",";
            }
        }

        return "sdssassasa count "+count($request->check);
        */


        $empresa=Empresa::Empresa()->first();
        require base_path("vendor/autoload.php");
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP(); // tell to use smtp
            $mail->CharSet = 'utf-8'; // set charset to utf8
            $mail->Host = trim('pagupasoft-com.correoseguro.dinaserver.com');
            $mail->SMTPAuth = true;
            $mail->SMTPSecure = 'tls';//$mail->SMTPSecure = false;
            $mail->SMTPAutoTLS = false;
            $mail->Port = trim('587'); // most likely something different for you. This is the mailtrap.io port i use for testing. 
            $mail->Username = trim('neopagupa@pagupasoft.com');
            $mail->Password = trim('PagupaServer2022');
            $mail->setFrom(trim('neopagupa@pagupasoft.com'), 'NEOPAGUPA SISTEMA CONTABLE');
            $mail->Subject = trim($request->get("asunto"));
            $mail->MsgHTML(trim($request->get("body")));

            
            $correos="";

            if(count($request->name)>0){
                foreach($request->name as $g){
                    //echo $request->correo[$g-1].' '.$request->nombre[$g-1].'<br>';
                    $split = explode(",",$g);

                    $correos.=$split[1]." nombre ".$split[0]."<br>";
                    $mail->addAddress(trim($split[1]), trim($split[0]));
                }
            }
            
            //$mail->addAddress("rick658658@gmail.com", "Ricardo");
            echo "enviado a <br>".$correos;

            $mail->SMTPOptions= array(
                'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
                )
            );
            $mail->send();

            return ".";
        } catch (Exception $ex) {
            $auditoria = new generalController();
            $auditoria->registrarAuditoria('Error al restablecer contraseña de usuario ','0',$ex);
            return($ex);
        }
    }
}
