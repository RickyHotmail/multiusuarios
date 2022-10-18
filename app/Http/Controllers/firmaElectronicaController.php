<?php

namespace App\Http\Controllers;

use App\Models\Firma_Electronica;
use App\Http\Controllers\Controller;
use App\Models\Empresa;
use App\Models\Punto_Emision;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class firmaElectronicaController extends Controller
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
            $firmaElectronica=Firma_Electronica::firma()->first();
            return view('admin.parametrizacion.firmaElectronica.index',['firmaElectronica'=>$firmaElectronica, 'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
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
            $info_cert=$this->guardarFirma($request);
            if($info_cert=='error'){
                return redirect('firmaElectronica')->with('error','Error en la carga de la firma; password incorrecta');
            }
            DB::beginTransaction();
            $firmaElectronica = new Firma_Electronica();
            $firmaElectronica->firma_ambiente = '';
            $firmaElectronica->firma_archivo = $request->file('firma_archivo')->getClientOriginalName();
            $firmaElectronica->firma_password = Crypt::encryptString($request->get('idPass'));
            $firmaElectronica->firma_pubKey =Crypt::encryptString($info_cert['cert']);
            $firmaElectronica->firma_privKey =Crypt::encryptString($info_cert['pkey']);
            $firmaElectronica->firma_fecha = $request->get('idFecha'); 
            $firmaElectronica->firma_disponibilidad = '';            
            $firmaElectronica->firma_estado  = 1;
            $firmaElectronica->empresa_id = Auth::user()->empresa_id;
            $firmaElectronica->save();
            /*Inicio de registro de auditoria */
            $auditoria = new generalController();
            $auditoria->registrarAuditoria('Registro de firma electronica','0','Archivo adjunto -> '.$request->get('idArchivo'));
            /*Fin de registro de auditoria */
            DB::commit();
            return redirect('firmaElectronica')->with('success','Datos guardados exitosamente');
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('firmaElectronica')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
       }
    
    }
    public function guardarFirma(Request $request){
        try{
            $empresa=Empresa::empresa()->first();
            $name = $request->file('firma_archivo')->getClientOriginalName();
            $this->borrar();      	
            mkdir(public_path().'/certificadosFirmasElectronicas'.'/'.$empresa->empresa_ruc, 0777);
            if (!is_dir(public_path().'/temp/'.$empresa->empresa_ruc)) {
                mkdir(public_path().'/temp/'.$empresa->empresa_ruc, 0777);
            }
            if (!is_dir(public_path().'/documentosElectronicos/'.$empresa->empresa_ruc)) {
                mkdir(public_path().'/documentosElectronicos/'.$empresa->empresa_ruc, 0777);
            }
            $path = $request->file('firma_archivo')->move(public_path().'/certificadosFirmasElectronicas'.'/'.$empresa->empresa_ruc, $name); 
            $almacén_cert = file_get_contents(public_path().'/certificadosFirmasElectronicas'.'/'.$empresa->empresa_ruc.'/'.$name);
            if (openssl_pkcs12_read($almacén_cert, $info_cert, $request->get('idPass'))) {
                return $info_cert;
            } else {
                $this->borrar();
                return 'error';
            }
        }
        catch(\Exception $ex){
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function borrar(){
        try{

            $firmaActual=Firma_Electronica::firma()->first();
            $empresa=Empresa::empresa()->first();
            if(!empty($firmaActual)){
                File::delete(public_path().'/certificadosFirmasElectronicas/'.$empresa->empresa_ruc.'/'.$firmaActual->firma_archivo);
                $firmaEliminar=Firma_Electronica::findOrFail($firmaActual->firma_id);
                $firmaEliminar->delete();
            } 
            if (is_dir(public_path().'/certificadosFirmasElectronicas/'.$empresa->empresa_ruc)) {
                $this->rmDir_rf(public_path().'/certificadosFirmasElectronicas/'.$empresa->empresa_ruc);
            }
            
        }
        catch(\Exception $ex){
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    } 
    public function rmDir_rf($carpeta)
    {
      foreach(glob($carpeta . "/*") as $archivos_carpeta){             
        if (is_dir($archivos_carpeta)){
          $this->rmDir_rf($archivos_carpeta);
        } else {
        unlink($archivos_carpeta);
        }
      }
      rmdir($carpeta);
     }
    public function update(Request $request, $id)
    {
        try{
            DB::beginTransaction();
            $firmaElectronica = Firma_Electronica::findOrFail($id);
            $firmaElectronica->firma_ambiente = $request->get('idAmbiente');
            $firmaElectronica->firma_disponibilidad = $request->get('idDisponibilidad');            
            $firmaElectronica->save();
            /*Inicio de registro de auditoria */
            $auditoria = new generalController();
            $auditoria->registrarAuditoria('Actualizacion de parametros electronicos ambiente -> '.$request->get('idAmbiente').' diponibilidad -> '.$request->get('idDisponibilidad'),'0','');
            /*Fin de registro de auditoria */
            DB::commit();
            return redirect('firmaElectronica')->with('success','Datos actualizados exitosamente');
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('firmaElectronica')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
}
