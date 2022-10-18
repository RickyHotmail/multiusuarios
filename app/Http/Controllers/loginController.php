<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Empresa;
use App\Models\Parametrizar_Empresa;

class loginController extends Controller
{
    /**
     * Handle an authentication attempt.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
    public function index()
    {
        try{
            $paramEmpresa= Parametrizar_Empresa::buscarConfiguracion("REQUERIR RUC")->first();
            $REQUERIR_RUC=1;

            if($paramEmpresa){
                if($paramEmpresa->parametrizar_valor==0) $REQUERIR_RUC=0;
            }

            return view('admin.seguridad.auth.login', ["requerirRuc"=>$REQUERIR_RUC]);
        }
        catch(\Exception $ex){      
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function authenticate(Request $request)
    {
        try{
            $userdata = array(
                'user_username' => $request->get('idUsername'),
                'password' => $request->get('idPassword'),
                'user_estado' => 1
            );
            
            $REQUERIR_RUC=1;
            $paramEmpresa= Parametrizar_Empresa::buscarConfiguracion("REQUERIR RUC")->first();
            if($paramEmpresa){
                if($paramEmpresa->parametrizar_valor==0) $REQUERIR_RUC=0;
            }
            
            if($REQUERIR_RUC==1){
                $empresa = Empresa::where('empresa_ruc', $request->get('idRuc'))->first();
                $userdata['empresa_id']=$empresa->empresa_id;
            }

            if (Auth::attempt($userdata, true)) {
                $request->session()->regenerate();
                $usuario=User::findOrFail(Auth::user()->user_id);
                Auth::login($usuario);
                $auditoria = new generalController();
                $auditoria->registrarAuditoria('Inicio de sesion usuario->'.$request->get('idUsername').' Con Id ->'.Auth::user()->user_id,Auth::user()->user_id,'');

                if($usuario->user_cambio_clave==1)
                    return redirect()->to('cambiarClave');
                else
                    return redirect()->intended('principal');
            }
            return back()->withErrors([
                'user_username' => 'Las credenciales proporcionadas no coinciden con nuestros registros.',
            ]);
        }
        catch(\Exception $ex){      
            return redirect('principal')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/login');
    }
}