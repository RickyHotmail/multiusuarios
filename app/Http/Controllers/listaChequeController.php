<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Banco;
use App\Models\Banco_Lista;
use App\Models\Cheque;
use App\Models\Cuenta_Bancaria;
use App\Models\Empresa;
use App\Models\Punto_Emision;
use App\Models\Rubro;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Luecano\NumeroALetras\NumeroALetras;
use Maatwebsite\Excel\Facades\Excel;

class listaChequeController extends Controller
{
    public function vista()
    {
        try{ 
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            return view('admin.bancos.listaCheque.index',['bancos'=>Banco::bancos()->get(),'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        }
        catch(\Exception $ex){      
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function listarCheques(Request $request)
    {   
        try{
            if ($request->get('idTodos') == '0'){
                //todos
                $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
                $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
                $listadoCheques=Cheque::listadoCheques()           
                ->where('banco_lista.empresa_id','=',Auth::user()->empresa_id)
                ->where('cheque_fecha_emision','>=',$request->get('idDesde'))
                ->where('cheque_fecha_emision','<=',$request->get('idHasta'))
                ->where('cuenta_bancaria.cuenta_bancaria_id','=',$request->get('cuenta_id'))->orderBy('cheque_numero','asc')->get();   
                return view('admin.bancos.listaCheque.index',['estadC'=>$request->get('idTodos'),'fechaI'=>$request->get('idDesde'),'fechaF'=>$request->get('idHasta'),'bancoC'=>$request->get('banco_id'),'cuentaBancaria'=>Cuenta_Bancaria::CuentaBancaria($request->get('cuenta_id'))->first(),'bancos'=>Banco::bancos()->get(),'listadoCheques'=>$listadoCheques, 'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
            }
            if ($request->get('idTodos') == '1'){
                //activos
                $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
                $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
                $listadoCheques=Cheque::listadoCheques()
                ->where('banco_lista.empresa_id','=',Auth::user()->empresa_id)
                ->where('cheque_fecha_emision','>=',$request->get('idDesde'))
                ->where('cheque_fecha_emision','<=',$request->get('idHasta'))
                ->where('cuenta_bancaria.cuenta_bancaria_id','=',$request->get('cuenta_id'))
                ->where('cheque_estado','=','1')->orderBy('cheque_numero','asc')->get();     
                return view('admin.bancos.listaCheque.index',['estadC'=>$request->get('idTodos'),'fechaI'=>$request->get('idDesde'),'fechaF'=>$request->get('idHasta'),'bancoC'=>$request->get('banco_id'),'cuentaBancaria'=>Cuenta_Bancaria::CuentaBancaria($request->get('cuenta_id'))->first(),'bancos'=>Banco::bancos()->get(),'listadoCheques'=>$listadoCheques, 'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
            }
            if ($request->get('idTodos') == '2'){
                //anulados
                $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
                $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
                $listadoCheques=Cheque::listadoCheques()
                ->where('banco_lista.empresa_id','=',Auth::user()->empresa_id)
                ->where('cheque_fecha_emision','>=',$request->get('idDesde'))
                ->where('cheque_fecha_emision','<=',$request->get('idHasta'))
                ->where('cuenta_bancaria.cuenta_bancaria_id','=',$request->get('cuenta_id'))
                ->where('cheque_estado','=','2')->orderBy('cheque_numero','asc')->get();            
                return view('admin.bancos.listaCheque.index',['estadC'=>$request->get('idTodos'),'fechaI'=>$request->get('idDesde'),'fechaF'=>$request->get('idHasta'),'bancoC'=>$request->get('banco_id'),'cuentaBancaria'=>Cuenta_Bancaria::CuentaBancaria($request->get('cuenta_id'))->first(),'bancos'=>Banco::bancos()->get(),'listadoCheques'=>$listadoCheques, 'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
            }
        }
        catch(\Exception $ex){      
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }      
    }

    public function imprimirCheque($id){
        $general = new generalController();
        return $general->pdfImprimeCheque2(Cheque::findOrFail($id)->cuenta_bancaria_id ,Cheque::findOrFail($id));
    }
    public function excelCheque(){
        try{
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            return view('admin.bancos.listaCheque.cargarExcel',['PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        }
        catch(\Exception $ex){      
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function CargarExcelCheque(Request $request){
        if (isset($_POST['cargar'])){
            return $this->cargarguardar($request);
        }
    }

    public function cargarguardar(Request $request){
        try {
            if ($request->file('excelClient')->isValid()) {
                $empresa = Empresa::empresa()->first();
                $name = $empresa->empresa_ruc. '.' .$request->file('excelClient')->getClientOriginalExtension();
                $path = $request->file('excelClient')->move(public_path().'\temp', $name);
                $array = Excel::toArray(new Cheque(), $path);
                DB::beginTransaction();    
                for ($i=1;$i < count($array[0]);$i++){  
                    $formatter = new NumeroALetras();                  
                    $cheque = new Cheque();
                    $cheque->cheque_numero = $array[0][$i][0];
                    $cheque->cheque_descripcion =  $array[0][$i][1];
                    $cheque->cheque_beneficiario = $array[0][$i][2];
                    $Excel_date2 = $array[0][$i][3]; 
                    $unix_date2 = ($Excel_date2 - 25569) * 86400;
                    $Excel_date2 = 25569 + ($unix_date2 / 86400);
                    $unix_date2 = ($Excel_date2 - 25569) * 86400;
                    $cheque->cheque_fecha_emision = gmdate("Y-m-d", $unix_date2);
                    
                    $Excel_date3 = $array[0][$i][4];
                    $unix_date3 = ($Excel_date3 - 25569) * 86400;
                    $Excel_date3 = 25569 + ($unix_date3 / 86400);
                    $unix_date3 = ($Excel_date3 - 25569) * 86400;                    
                    $cheque->cheque_fecha_pago = gmdate("Y-m-d", $unix_date3);
                    $cheque->cheque_valor =  $array[0][$i][5];
                    $cheque->cheque_valor_letras =  $formatter->toInvoice($array[0][$i][5], 2, 'Dolares');
                    
                    $bancoLista = Banco_Lista::BancoListaByNom($array[0][$i][7])->first();
                    if(isset($bancoLista->banco_lista_id)){
                        $banco = Banco::BancoXbancolista($bancoLista->banco_lista_id)->first();         
                    }else{
                        return('# cheque -'.$array[0][$i][0]);
                    }          
                    $cuentaBancarias = Cuenta_Bancaria::CuentaBancariasBanco($banco->banco_id)->get();                    
                    foreach($cuentaBancarias as $cuentaBancaria){                        
                        if($cuentaBancaria->cuenta_bancaria_numero == $array[0][$i][6]){
                            //return($cuentaBancaria->cuenta_bancaria_id);
                            $cheque->cuenta_bancaria_id =  $cuentaBancaria->cuenta_bancaria_id;
                        } 
                    }                                      
                    $cheque->cheque_estado = '1';
                    $cheque->empresa_id = Auth::user()->empresa->empresa_id;
                    $cheque->save();

                    /*Inicio de registro de auditoria */
                    $auditoria = new generalController();
                    $auditoria->registrarAuditoria('Registro de Cheques-> '.$array[0][$i][0], 'UTF-8'.'con codigo->'.mb_strtoupper($array[0][$i][0], 'UTF-8').'Mediante archivo excell', '0', '');
                
                }
                DB::commit();
                return redirect('excelCheque')->with('success','Datos guardados exitosamente');
            }
        }
        catch(\Exception $ex){ 
            DB::rollBack();     
            return redirect('excelCheque')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }

}
