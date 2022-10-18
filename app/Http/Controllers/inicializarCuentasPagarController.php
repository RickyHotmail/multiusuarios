<?php

namespace App\Http\Controllers;

use App\Models\Cuenta_Pagar;
use App\Models\Detalle_Diario;
use App\Models\Diario;
use App\Models\Empresa;
use App\Models\Parametrizacion_Contable;
use App\Models\Proveedor;
use App\Models\Punto_Emision;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class inicializarCuentasPagarController extends Controller
{
    public function nuevo()
    {
        try{
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            return view('admin.cuentasPagar.inicializar.index',['diarios'=>Diario::Diarios()->where('diario_tipo','=','CDCO')->get(),'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        }
        catch(\Exception $ex){      
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function consultar(Request $request)
    {   
        if (isset($_POST['buscar'])){
            return $this->buscar($request);
        }
        if (isset($_POST['guardar'])){
            return $this->guardar($request);
        }
    }
    public function buscar(Request $request){
        try{         
            DB::beginTransaction();  
            if($request->file('file_cuentas')->isValid()){
                $empresa = Empresa::empresa()->first();
                $name = $empresa->empresa_ruc. '.' .$request->file('file_cuentas')->getClientOriginalExtension();
                $path = $request->file('file_cuentas')->move(public_path().'\temp', $name); 
                $array = Excel::toArray(new Cuenta_Pagar(), $path); 
                $detalleDiarioAux =  Detalle_Diario::findOrFail('');
                $diario = $detalleDiarioAux->diario;
                $general = new generalController(); 
                for($i=1;$i < count($array[0]);$i++){
                    if($array[0][$i][0] != ''){
                        $proveedor = Proveedor::ProveedoresByRuc($array[0][$i][0])->first();
                        /********************cuenta por pagar***************************/
                        $cxp = new Cuenta_Pagar();
                        $cxp->cuenta_descripcion = $diario->diario_codigo.' COMPRA CON FACTURA No. '.$array[0][$i][1];
                        $cxp->cuenta_tipo ='CREDITO';
                        $cxp->cuenta_saldo =  $array[0][$i][5];
                        $cxp->cuenta_estado = '1';
                        $Excel_date = $array[0][$i][2]; 
                        $unix_date = ($Excel_date - 25569) * 86400;
                        $Excel_date = 25569 + ($unix_date / 86400);
                        $unix_date = ($Excel_date - 25569) * 86400;
                        $cxp->cuenta_fecha = gmdate("Y-m-d", $unix_date);
                        $cxp->cuenta_fecha_inicio = gmdate("Y-m-d", $unix_date);
                        $Excel_date = $array[0][$i][3]; 
                        $unix_date = ($Excel_date - 25569) * 86400;
                        $Excel_date = 25569 + ($unix_date / 86400);
                        $unix_date = ($Excel_date - 25569) * 86400;
                        $cxp->cuenta_fecha_fin = gmdate("Y-m-d", $unix_date);
                        $cxp->cuenta_monto =  $array[0][$i][4];
                        $cxp->cuenta_valor_factura = $array[0][$i][4];
                        $cxp->proveedor_id = $proveedor->proveedor_id;
                        $cxp->sucursal_id = $diario->sucursal_id;
                        $cxp->save();
                        $general->registrarAuditoria('Registro de inicializacion de cuenta por pagar de factura -> '.$array[0][$i][1],$array[0][$i][1],'Registro de inicializacion de cuenta por pagar de factura -> '.$array[0][$i][1].' con proveedor -> '.$proveedor->proveedor_nombre.' con un total de -> '.$array[0][$i][4].' con un saldo de -> '.$array[0][$i][5]);
                        /****************************************************************/
                        /********************detalle de diario***************************/
                        $detalleDiario = new Detalle_Diario();
                        if($detalleDiarioAux->detalle_debe > 0){
                            $detalleDiario->detalle_debe = $array[0][$i][5];
                        }else{
                            $detalleDiario->detalle_debe = 0.00;
                        }
                        if($detalleDiarioAux->detalle_haber > 0){
                            $detalleDiario->detalle_haber = $array[0][$i][5];
                        }else{
                            $detalleDiario->detalle_haber = 0.00;
                        }
                        $detalleDiario->detalle_tipo_documento = 'FACTURA';
                        $detalleDiario->detalle_numero_documento = $array[0][$i][1];
                        $detalleDiario->detalle_conciliacion = '0';
                        $detalleDiario->detalle_estado = '1';
                        $detalleDiario->detalle_comentario = 'P/R CUENTA POR PAGAR DE PROVEEDOR';
                        $parametrizacionContable=Parametrizacion_Contable::ParametrizacionByNombre($diario->sucursal_id, 'CUENTA POR PAGAR')->first();
                        if($parametrizacionContable->parametrizacion_cuenta_general == '1'){
                            $detalleDiario->cuenta_id = $parametrizacionContable->cuenta_id;
                        }else{
                            $parametrizacionContable = Proveedor::findOrFail($proveedor->proveedor_id);
                            $detalleDiario->cuenta_id = $parametrizacionContable->proveedor_cuenta_pagar;
                        }
                        $detalleDiario->proveedor_id = $proveedor->proveedor_id;
                        $diario->detalles()->save($detalleDiario);
                        $general->registrarAuditoria('Registro de detalle de diario con codigo -> '.$diario->diario_codigo,$array[0][$i][1],'Registro de detalle de diario con codigo -> '.$diario->diario_codigo.' por inicializacion de cuenta por pagar con cuenta contable -> '.$detalleDiario->cuenta->cuenta_numero.' en el haber por un valor de -> '.$array[0][$i][5]);
                        /****************************************************************/
                    }
                }
            }
            $detalleDiarioAux->delete();
            $url = $general->pdfDiario($diario);
            DB::commit();
            return redirect('inicializarCXP')->with('success','Datos guardados exitosamente')->with('diario',$url);
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('inicializarCXP')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function buscar2(Request $request){
        try{
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $datos = null;
            $count = 1;
            if($request->file('file_cuentas')->isValid()){
                $empresa = Empresa::empresa()->first();
                $name = $empresa->empresa_ruc. '.' .$request->file('file_cuentas')->getClientOriginalExtension();
                $path = $request->file('file_cuentas')->move(public_path().'\temp', $name); 
                $array = Excel::toArray(new Cuenta_Pagar(), $path); 
                for($i=1;$i < count($array[0]);$i++){
                    $proveedor = Proveedor::ProveedoresByRuc($array[0][$i][0])->first();
                    $datos[$count]['proId'] = $proveedor->proveedor_id;
                    $datos[$count]['proNombre'] = $proveedor->proveedor_nombre;
                    $datos[$count]['numero'] = $array[0][$i][1];

                    $Excel_date = $array[0][$i][2]; 
                    $unix_date = ($Excel_date - 25569) * 86400;
                    $Excel_date = 25569 + ($unix_date / 86400);
                    $unix_date = ($Excel_date - 25569) * 86400;
                    $datos[$count]['fecha'] = gmdate("Y-m-d", $unix_date);

                    $Excel_date = $array[0][$i][3]; 
                    $unix_date = ($Excel_date - 25569) * 86400;
                    $Excel_date = 25569 + ($unix_date / 86400);
                    $unix_date = ($Excel_date - 25569) * 86400;
                    $datos[$count]['vencimiento'] =gmdate("Y-m-d", $unix_date);
                    
                    $datos[$count]['valor'] = $array[0][$i][4];
                    $datos[$count]['saldo'] = $array[0][$i][5];
                    $count ++;
                }
            }
            return view('admin.cuentasPagar.inicializar.nuevo',['datos'=>$datos,'diarioC'=>Diario::findOrFail($request->get('diario_id')),'diarios'=>Diario::Diarios()->where('diario_tipo','=','CDCO')->get(),'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        }
        catch(\Exception $ex){      
            return redirect('inicializarCXP')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function guardar(Request $request){
        try{         
            DB::beginTransaction();  
            $seleccion = $request->get('checkbox1');
            $detalleDiarioAux =  Detalle_Diario::findOrFail($seleccion[0]);
            $diario = $detalleDiarioAux->diario;
            $general = new generalController(); 
            $cierre = $general->cierre($request->get('idfecha'),$diario->sucursal_id);          
            if($cierre){
                return redirect('inicializarCXP')->with('error2','No puede realizar la operacion por que pertenece a un mes bloqueado');
            }
           
            $proveedor = $request->get('Dproveedor');
            $numero = $request->get('Dnumero');
            $valor = $request->get('Dvalor');
            $saldo = $request->get('Dsaldo');
            $fecha = $request->get('Dfecha');
            $vence = $request->get('Dvence');
            for ($i = 1; $i < count($proveedor); ++$i){
                /********************cuenta por pagar***************************/
                $cxp = new Cuenta_Pagar();
                $cxp->cuenta_descripcion = $diario->diario_codigo.' COMPRA CON FACTURA No. '.$numero[$i];
                $cxp->cuenta_tipo ='CREDITO';
                $cxp->cuenta_saldo = $saldo[$i];
                $cxp->cuenta_estado = '1';
                $cxp->cuenta_fecha = $fecha[$i];
                $cxp->cuenta_fecha_inicio = $fecha[$i];
                $cxp->cuenta_fecha_fin = $vence[$i];
                $cxp->cuenta_monto = $valor[$i];
                $cxp->cuenta_valor_factura = $valor[$i];
                $cxp->proveedor_id = $proveedor[$i];
                $cxp->sucursal_id = $diario->sucursal_id;
                $cxp->save();
                $general->registrarAuditoria('Registro de inicializacion de cuenta por pagar de factura -> '.$numero[$i],$numero[$i],'Registro de inicializacion de cuenta por pagar de factura -> '.$numero[$i].' con proveedor -> '.$proveedor[$i].' con un total de -> '.$valor[$i].' con un saldo de -> '.$saldo[$i]);
                /****************************************************************/
                /********************detalle de diario***************************/
                $detalleDiario = new Detalle_Diario();
                if($detalleDiarioAux->detalle_debe > 0){
                    $detalleDiario->detalle_debe = $saldo[$i];
                }else{
                    $detalleDiario->detalle_debe = 0.00;
                }
                if($detalleDiarioAux->detalle_haber > 0){
                    $detalleDiario->detalle_haber = $saldo[$i];
                }else{
                    $detalleDiario->detalle_haber = 0.00;
                }
                $detalleDiario->detalle_tipo_documento = 'FACTURA';
                $detalleDiario->detalle_numero_documento = $numero[$i];
                $detalleDiario->detalle_conciliacion = '0';
                $detalleDiario->detalle_estado = '1';
                $detalleDiario->detalle_comentario = 'P/R CUENTA POR PAGAR DE PROVEEDOR';
                $parametrizacionContable=Parametrizacion_Contable::ParametrizacionByNombre($diario->sucursal_id, 'CUENTA POR PAGAR')->first();
                if($parametrizacionContable->parametrizacion_cuenta_general == '1'){
                    $detalleDiario->cuenta_id = $parametrizacionContable->cuenta_id;
                }else{
                    $parametrizacionContable = Proveedor::findOrFail($proveedor[$i]);
                    $detalleDiario->cuenta_id = $parametrizacionContable->proveedor_cuenta_pagar;
                }
                $detalleDiario->proveedor_id = $proveedor[$i];
                $diario->detalles()->save($detalleDiario);
                $general->registrarAuditoria('Registro de detalle de diario con codigo -> '.$diario->diario_codigo,$numero[$i],'Registro de detalle de diario con codigo -> '.$diario->diario_codigo.' por inicializacion de cuenta por pagar con cuenta contable -> '.$detalleDiario->cuenta->cuenta_numero.' en el haber por un valor de -> '.$saldo[$i]);
                /****************************************************************/
            }
            $detalleDiarioAux->delete();
            $url = $general->pdfDiario($diario);
            DB::commit();
            return redirect('inicializarCXP')->with('success','Datos guardados exitosamente')->with('diario',$url);
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('inicializarCXP')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
}
