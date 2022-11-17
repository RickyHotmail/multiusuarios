<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Banco_Lista;
use App\Models\Empresa;
use App\Models\Pago;
use App\Models\Parametrizacion_Grupo_Permiso;
use App\Models\Parametrizacion_Permiso;
use App\Models\Plan;
use App\Models\Rol_Permiso;
use App\Models\Suscripcion;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class administracionGeneralController extends Controller{
    public function index(){
        $empresas=Empresa::empresas()->get();

        return view('admin.administracion.empresa.index',[
            'empresas'=>$empresas
        ]);
    }

    public function verUsuarios(Request $request, $id){
        $usuarios=User::findByEmpresa($id)->get();

        return view('admin.administracion.empresa.usuariosclave',[
            'usuarios'=>$usuarios
        ]);
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

    public function restablecerClaveUsuario($id){
        try{
            $usuario=User::findOrFail($id);
            if(!$usuario) redirect('administracion')->with('error2', 'No se encontró al Usuario');

            DB::beginTransaction();
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

                $generalController->enviarCorreo($usuario->user_correo, $usuario->user_nombre, 'Restablecer Password', $html, '', []);
            });
            
            
            /*Inicio de registro de auditoria */
            $auditoria = new generalController();
            $auditoria->registrarAuditoria('Restablecer contraseña de usuario -> '.$usuario->user_username,'0','');
            /*Fin de registro de auditoria */
            DB::commit();

            return redirect('administracion')->with('success','Contraseña restablecida, revisa tu correo electrónico');
        } catch(\Exception $ex){
            DB::rollBack();
            return redirect('administracion')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }

    public function verPagos($id){
        $empresa=Empresa::findOrFail($id);
        $suscripcion=$empresa->suscripcion;

        $planes=Plan::planes()->get();
        $bancos=Banco_Lista::bancoListas()->get();
        $hoy = date("Y-m-d");

        $caducado=false;
        $dias=0;

        if(!$suscripcion) return 'Tu sistema no trabaja con suscripción';

        if($suscripcion->suscripcion_fecha_finalizacion < $hoy){
            $caducado=true;

            $dias = (strtotime($hoy)-strtotime($suscripcion->suscripcion_fecha_finalizacion))/86400;
            $dias = abs($dias); $dias = floor($dias);
        }
        else{
            $dias = (strtotime($suscripcion->suscripcion_fecha_finalizacion)-strtotime($hoy))/86400;
            $dias = abs($dias); $dias = floor($dias);
        }

        return view('admin.suscripcion.verificar', [
            'suscripcion'=>$suscripcion,
            'planes'=>$planes,
            'bancos'=>$bancos,
            'caducado'=>$caducado,
            'dias'=>$dias
        ]);
    }

    public function activarPago(Request $request){
        try{
            DB::beginTransaction();
            $pagoActual=Pago::findOrFail($request->idPago);

            //return $pagoActual;

            if(!$pagoActual) return back()->with('warning', 'No se encontró el pago buscado');
            $pago=Pago::findByBancoNumero($pagoActual->pago_banco_nombre, $pagoActual->pago_banco_numero)->first();
            if(!$pago){
                if($pago->pago_id!=$pagoActual && $pago->pago_banco_nombre==$pagoActual->pago_banco_nombre && $pago->pago_banco_numero==$pagoActual->pago_banco_numero)
                    return back()->with('warning', 'Se encontró el pago, ya fué registrado');
            }

            $pagoActual->pago_estado=1;
            $pagoActual->pago_fecha_validacion=date("Y-m-d");
            $pagoActual->save();

            $suscripcion=Suscripcion::findOrFail($pagoActual->suscripcion_id);
            $plan=$suscripcion->plan;
            $suscripcion->plan_id=$pagoActual->plan_id;
            $suscripcion->suscripcion_fecha_inicio=date("Y-m-d");
            $suscripcion->suscripcion_fecha_finalizacion= date("Y-m-d",strtotime(date("d-m-Y").'+ '.$plan->plan_tiempo.' days'));
            $suscripcion->suscripcion_cantidad_generado=0;
            $suscripcion->save();

            DB::commit();
            return redirect('administracion')->with('success','El Pago fué activado con éxito');
        } catch(\Exception $ex){
            DB::rollBack();
            return redirect('administracion')->with('error2','Ocurrió un error al validar. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    
    public function verPermisos(Request $request, $id){
        $empresa=Empresa::findOrFail($id);
        $grupos=Parametrizacion_Grupo_Permiso::grupos()->get();

        return view('admin.suscripcion.permiso',[
            'empresa'=>$empresa,
            'grupos'=>$grupos
        ]);
    }

    public function actualizarPermisosAdministrador(Request $request, $id){
        try{
            DB::beginTransaction();
            $permisosGrupo=Parametrizacion_Permiso::parametrizacionesPermiso($request->permiso_general)->get();
            $rol=DB::select(DB::raw("select * from rol where rol_nombre='Administrador' and empresa_id=$id"));
            
            DB::select(DB::raw("delete from rol_permiso where rol_id=".$rol[0]->rol_id));

            
            foreach($permisosGrupo as $param){
                $rolPermiso=new Rol_Permiso();
                $rolPermiso->permiso_id=$param->permiso_id;
                $rolPermiso->rol_id=$rol[0]->rol_id;
                $rolPermiso->save();
            }

            DB::commit();
            return redirect('administracion')->with('success','Los permisos fueron cambiados correctamente');
        } catch(\Exception $ex){
            DB::rollBack();
            return redirect('administracion')->with('error2','Ocurrió un error al cambiar los permisos. Vuelva a intentar, error: ('.$ex->getMessage().')');
        }
    }
}
