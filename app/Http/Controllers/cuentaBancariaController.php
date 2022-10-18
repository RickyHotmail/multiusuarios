<?php

namespace App\Http\Controllers;
use App\Models\Banco;
use App\Models\Cuenta_Bancaria;
use App\Models\Cuenta;
use App\Http\Controllers\Controller;
use App\Models\Cheque_Impresion;
use App\Models\Cuenta_Pagar;
use App\Models\Empresa;
use App\Models\Punto_Emision;
use Barryvdh\DomPDF\PDF as DomPDFPDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PDF;

use function PHPUnit\Framework\returnValue;

class cuentaBancariaController extends Controller
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
            $bancos=Banco::bancos()->get();
            $cuentas = Cuenta::CuentasMovimiento()->get();
            $cuentaBancarias = Cuenta_Bancaria::cuentaBancarias()->get();        
            return view('admin.bancos.cuentaBancaria.index',['cuentaBancarias'=>$cuentaBancarias,'bancos'=>$bancos, 'PE'=>Punto_Emision::puntos()->get(),'cuentas'=>$cuentas, 'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        }catch(\Exception $ex){
            return redirect('inicio')->with('error','Ocurrio un error vuelva a intentarlo('.$ex->getMessage().')');
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return redirect('/denegado');
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
            $cuentaBancaria = new Cuenta_Bancaria();
            $cuentaBancaria->cuenta_bancaria_numero = $request->get('idNumero');
            $cuentaBancaria->cuenta_bancaria_tipo = $request->get('idTipo');
            $cuentaBancaria->cuenta_bancaria_saldo_inicial = $request->get('idInicial');
            $cuentaBancaria->cuenta_bancaria_jefe = $request->get('idjefe');
            $cuentaBancaria->banco_id = $request->get('idBanco');
            $cuentaBancaria->cuenta_id = $request->get('idCuenta');
            $cuentaBancaria->cuenta_bancaria_estado = 1;
            $cuentaBancaria->save();
            /*Inicio de registro de auditoria */
            $auditoria = new generalController();
            $auditoria->registrarAuditoria('Registro de Cuenta Bancaria con # -> '.$request->get('idNumero'),'0','Con codigo de Banco:'.$request->get('idBanco'));
            /*Fin de registro de auditoria */
            DB::commit();
            return redirect('cuentaBancaria')->with('success','Datos guardados exitosamente');
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('cuentaBancaria')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
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
            $cuentaBancaria = Cuenta_Bancaria::cuentaBancaria($id)->first();
            if($cuentaBancaria){
                return view('admin.bancos.cuentaBancaria.ver',['cuentaBancaria'=>$cuentaBancaria, 'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
            }else{
                return redirect('/denegado');
            }
        }catch(\Exception $ex){
            return redirect('inicio')->with('error','Ocurrio un error vuelva a intentarlo('.$ex->getMessage().')');
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
            $bancos=Banco::bancos()->get();
            $cuentas = Cuenta::CuentasMovimiento()->get();
            $cuentaBancaria = Cuenta_Bancaria::cuentaBancaria($id)->first();
            if($cuentaBancaria){
                return view('admin.bancos.cuentaBancaria.editar', ['cuentaBancaria'=>$cuentaBancaria,'bancos'=>$bancos,'cuentas'=>$cuentas, 'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
            }else{
                return redirect('/denegado');
            }
        }catch(\Exception $ex){
            return redirect('inicio')->with('error','Ocurrio un error vuelva a intentarlo('.$ex->getMessage().')');
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
            $cuentaBancaria = Cuenta_Bancaria::findOrFail($id);
            $cuentaBancaria->cuenta_bancaria_numero = $request->get('idNumero');
            $cuentaBancaria->cuenta_bancaria_tipo = $request->get('idTipo');
            $cuentaBancaria->cuenta_bancaria_saldo_inicial = $request->get('idInicial');
            $cuentaBancaria->cuenta_bancaria_jefe = $request->get('idJefe');
            $cuentaBancaria->banco_id = $request->get('idBanco');
            $cuentaBancaria->cuenta_id = $request->get('idCuenta');                                
            if ($request->get('idEstado') == "on"){
                $cuentaBancaria->cuenta_bancaria_estado = 1;
            }else{
                $cuentaBancaria->cuenta_bancaria_estado = 0;
            }
            $cuentaBancaria->save();       
            /*Inicio de registro de auditoria */
            $auditoria = new generalController();
            $auditoria->registrarAuditoria('Actualizacion de Cuenta Bancaria -> '.$request->get('idNumero'),'0','');
            /*Fin de registro de auditoria */   
            DB::commit();
            return redirect('cuentaBancaria')->with('success','Datos actualizados exitosamente');
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('cuentaBancaria')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
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
            $cuentaBancaria = Cuenta_Bancaria::findOrFail($id);
            $cuentaBancaria->delete();
            /*Inicio de registro de auditoria */
            $auditoria = new generalController();
            $auditoria->registrarAuditoria('Eliminacion de banco lista -> '.$cuentaBancaria->banco_lista_nombre,'0','');
            /*Fin de registro de auditoria */
            DB::commit();
            return redirect('cuentaBancaria')->with('success','Datos eliminados exitosamente');
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('cuentaBancaria')->with('error','El registro no pudo ser borrado, tiene resgitros adjuntos.');
        }
    }

    public function delete($id)
    {
        try{
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $cuentaBancaria = Cuenta_Bancaria::cuentaBancaria($id)->first();
            if($cuentaBancaria){
                return view('admin.bancos.cuentaBancaria.eliminar',['cuentaBancaria'=>$cuentaBancaria, 'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
            }else{
                return redirect('/denegado');
            }
        }catch(\Exception $ex){
            return redirect('inicio')->with('error','Ocurrio un error vuelva a intentarlo('.$ex->getMessage().')');
        }
    }

    public function configurarCheque($id)
    {
        try {
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $cuentaBancaria = Cuenta_Bancaria::cuentaBancaria($id)->first();
            $chequeImpresion = Cheque_Impresion::chequeImpresionByUser($id)->first();

            if(!$chequeImpresion)
                $chequeImpresion = Cheque_Impresion::chequeImpresion($id)->first();
            
            if($cuentaBancaria){
                return view('admin.bancos.cuentaBancaria.chequeimprimir',['chequeImpresion'=>$chequeImpresion,'cuentaBancaria'=>$cuentaBancaria, 'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
            }else{
                return redirect('/denegado');
            }
        }
        catch(\Exception $ex){      
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function guardarConfCheque(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            $cuentaBancaria = Cuenta_Bancaria::cuentaBancaria($id)->first();
            $chequeImpresion = Cheque_Impresion::chequeImpresionByUser($id)->first();
            if(isset($chequeImpresion->chequei_id))
                $chequeImpresion->delete();
            
            $chequeImpresion= new Cheque_Impresion();
            $chequeImpresion->chequei_valorx = $request->get('idValorx');
            $chequeImpresion->chequei_valory = $request->get('idValory');
            $chequeImpresion->chequei_valorfont = $request->get('idValorfont');

            $chequeImpresion->chequei_beneficiariox = $request->get('idBeneficiariox');
            $chequeImpresion->chequei_beneficiarioy = $request->get('idBeneficiarioy');
            $chequeImpresion->chequei_beneficiariofont = $request->get('idBeneficiariofont');
            
            $chequeImpresion->chequei_letrasx = $request->get('idLetrasx');
            $chequeImpresion->chequei_letrasy = $request->get('idLetrasy');
            $chequeImpresion->chequei_letrasfont = $request->get('idLetrasfont');

            $chequeImpresion->chequei_fechax = $request->get('idFechax');
            $chequeImpresion->chequei_fechay = $request->get('idFechay');
            $chequeImpresion->chequei_fechafont = $request->get('idFechafont');

            $chequeImpresion->cuenta_bancaria_id = $id;
            $chequeImpresion->user_id=Auth::user()->user_id;
            $chequeImpresion->chequei_estado=1;
            $chequeImpresion->save();  
            
            /*Inicio de registro de auditoria */
            $auditoria = new generalController();
            $auditoria->registrarAuditoria('Configuracion de Impresion del Cheque perteneciente al -> '.$cuentaBancaria->banco->bancoLista->banco_lista_nombre.' -> '.$cuentaBancaria->cuenta_bancaria_numero,'0','');
            /*Fin de registro de auditoria */
            DB::commit();
            return redirect('cuentaBancaria')->with('success','Datos guardados exitosamente');
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function chequeImprima($id){       
        $empresa = Empresa::empresa()->first();
        $cuentaBancaria = Cuenta_Bancaria::cuentaBancaria($id)->first();
        $chequeImpresion = Cheque_Impresion::chequeImpresionByUser($id)->first();

        if(!$chequeImpresion)
            $chequeImpresion = Cheque_Impresion::chequeImpresion($id)->first();

        if(!$chequeImpresion)
            return ": ( <br><br><br>Usted no ha configurado la visualización, realice esta acción en configurar Cheque  <a href='".url('cuentaBancaria')."'>Regresar</a>";

        $ruta = public_path().'/chequesImpresosPDF/'.$empresa->empresa_ruc;
        echo "$ruta";
        if (!is_dir($ruta)) {
            mkdir($ruta, 0777, true);
        }
        $nombreArchivo = 'Cheque'.$cuentaBancaria->banco->bancoLista->banco_lista_nombre;
        $view =  \View::make('admin.formatosPDF.chequeImpresionPdf', ['chequeImpresion'=>$chequeImpresion,'empresa'=>$empresa]);
        //PDF::loadHTML($view)->save($ruta.'/'.$nombreArchivo)->download($nombreArchivo.'.pdf');
        return PDF::loadHTML($view)->save($ruta.'/'.$nombreArchivo)->stream('cheque.pdf');
    }

    public function buscarByBanco($buscar){
        return Cuenta_Bancaria::CuentaBancariasBanco($buscar)->get();
    }
    public function buscarByBancoCuenta($buscar){
        return Cuenta_Bancaria::CuentaBancoNumero($buscar)->get();
    }
    public function buscarByCuentaBanco($buscar){
        return Cuenta_Bancaria::CuentaBanco($buscar)->get();
    }
    public function buscarByCuentaBancaria($buscar){
        return Cuenta_Bancaria::CuentBancariaId($buscar)->get();
    }
}
