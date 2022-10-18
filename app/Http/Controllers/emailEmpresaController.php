<?php

namespace App\Http\Controllers;

use App\Models\Email_Empresa;
use App\Http\Controllers\Controller;
use App\Models\Punto_Emision;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class emailEmpresaController extends Controller
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
            $emailEmpresa=Email_Empresa::email()->first();
            return view('admin.parametrizacion.emailEmpresa.index',['emailEmpresa'=>$emailEmpresa, 'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
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
            $correoAux=DB::table('email_empresa')->where('empresa_id','=',Auth::user()->empresa_id)->first();
            if(!empty($correoAux)){
                $correoAux2=Email_Empresa::findOrFail($correoAux->email_id);
                $correoAux2->delete();
            }
            $emailEmpresa = new Email_Empresa();
            $emailEmpresa->email_servidor = $request->get('idServidor');
            $emailEmpresa->email_email = $request->get('idCorreo');
            $emailEmpresa->email_usuario = $request->get('idUsuario');
            $emailEmpresa->email_pass = $request->get('idPass'); 
            $emailEmpresa->email_puerto = $request->get('idPuerto');
            $emailEmpresa->email_mensaje = $request->get('idMensaje');
            $emailEmpresa->email_estado  = 1;
            $emailEmpresa->empresa_id = Auth::user()->empresa_id;
            $emailEmpresa->save();
            /*Inicio de registro de auditoria */
            $auditoria = new generalController();
            $auditoria->registrarAuditoria('Registro de emai de empresa -> '.$request->get('idCorreo'),'0','Servidor -> '.$request->get('idServidor').' y puerto -> '.$request->get('idPuerto'));
            /*Fin de registro de auditoria */
            DB::commit();
            return redirect('emailEmpresa')->with('success','Datos guardados exitosamente');
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('emailEmpresa')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
       }
    }
}
