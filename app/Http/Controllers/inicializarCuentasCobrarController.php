<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Cuenta_Cobrar;
use App\Models\Detalle_Diario;
use App\Models\Detalle_Lista;
use App\Models\Diario;
use App\Models\Empresa;
use App\Models\Parametrizacion_Contable;
use App\Models\Punto_Emision;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class inicializarCuentasCobrarController extends Controller
{
    public function nuevo()
    {
        try{
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            return view('admin.cuentasCobrar.inicializar.index',['diarios'=>Diario::Diarios()->where('diario_tipo','=','CDCO')->get(),'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
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
                $array = Excel::toArray(new Cuenta_Cobrar(), $path); 
                $detalleDiarioAux =  Detalle_Diario::findOrFail(11202);
                $diario = $detalleDiarioAux->diario;
                $general = new generalController(); 
                for($i=1;$i < count($array[0]);$i++){
                    $cliente = Cliente::ClientesByCedula($array[0][$i][0])->first();               
                    /********************cuenta por cobrar***************************/
                    $cxc = new Cuenta_Cobrar();
                    $cxc->cuenta_descripcion = $diario->diario_codigo.' VENTA CON FACTURA No. '.$array[0][$i][1];
                    $cxc->cuenta_tipo ='CREDITO';
                    $cxc->cuenta_saldo = $array[0][$i][5];
                    $cxc->cuenta_estado = '1';
                    $Excel_date = $array[0][$i][2]; 
                    $unix_date = ($Excel_date - 25569) * 86400;
                    $Excel_date = 25569 + ($unix_date / 86400);
                    $unix_date = ($Excel_date - 25569) * 86400;
                    $cxc->cuenta_fecha = gmdate("Y-m-d", $unix_date);
                    $cxc->cuenta_fecha_inicio = gmdate("Y-m-d", $unix_date);
                    $Excel_date = $array[0][$i][3]; 
                    $unix_date = ($Excel_date - 25569) * 86400;
                    $Excel_date = 25569 + ($unix_date / 86400);
                    $unix_date = ($Excel_date - 25569) * 86400;
                    $cxc->cuenta_fecha_fin = gmdate("Y-m-d", $unix_date);
                    $cxc->cuenta_monto = $array[0][$i][4];
                    $cxc->cuenta_valor_factura = $array[0][$i][4];
                    $cxc->cliente_id = $cliente->cliente_id;
                    $cxc->sucursal_id = $diario->sucursal_id;
                    $cxc->save();
                    $general->registrarAuditoria('Registro de inicializacion de cuenta por cobrar de factura -> '.$array[0][$i][1],$array[0][$i][1],'Registro de inicializacion de cuenta por cobrar de factura -> '.$array[0][$i][1].' con cliente -> '.$cliente->cliente_nombre.' con un total de -> '.$array[0][$i][4].' con un saldo de -> '.$array[0][$i][5]);
                    /****************************************************************/
                    /********************detalle de diario***************************/
                    $detalleDiario = new Detalle_Diario();
                    if($detalleDiarioAux->detalle_debe > 0){
                        $detalleDiario->detalle_debe =  $cxc->cuenta_saldo;
                    }else{
                        $detalleDiario->detalle_debe = 0.00;
                    }
                    if($detalleDiarioAux->detalle_haber > 0){
                        $detalleDiario->detalle_haber =  $cxc->cuenta_saldo;
                    }else{
                        $detalleDiario->detalle_haber = 0.00;
                    }
                    $detalleDiario->detalle_tipo_documento = 'FACTURA';
                    $detalleDiario->detalle_numero_documento = $array[0][$i][1];
                    $detalleDiario->detalle_conciliacion = '0';
                    $detalleDiario->detalle_estado = '1';
                    $detalleDiario->detalle_comentario = 'P/R CUENTA POR COBRAR DE CLIENTE';
                    $parametrizacionContable=Parametrizacion_Contable::ParametrizacionByNombre($diario->sucursal_id, 'CUENTA POR COBRAR')->first();
                    if($parametrizacionContable->parametrizacion_cuenta_general == '1'){
                        $detalleDiario->cuenta_id = $parametrizacionContable->cuenta_id;
                    }else{
                        $parametrizacionContable = Cliente::findOrFail($cliente->cliente_id);
                        $detalleDiario->cuenta_id = $parametrizacionContable->cliente_cuenta_cobrar;
                    }
                    $detalleDiario->cliente_id = $cliente->cliente_id;
                    $diario->detalles()->save($detalleDiario);
                    $general->registrarAuditoria('Registro de detalle de diario con codigo -> '.$diario->diario_codigo,$array[0][$i][1],'Registro de detalle de diario con codigo -> '.$diario->diario_codigo.' por inicializacion de cuenta por cobrar con cuenta contable -> '.$detalleDiario->cuenta->cuenta_numero.' en el haber por un valor de -> '.$cxc->cuenta_saldo);
                    /****************************************************************/
                }
                $detalleDiarioAux->delete();
                //$url = $general->pdfDiario($diario);
                $url='';
            }
            DB::commit();
            return redirect('inicializarCXC')->with('success','Datos guardados exitosamente')->with('diario',$url);
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('inicializarCXC')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
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
                $array = Excel::toArray(new Cuenta_Cobrar(), $path); 
                for($i=1;$i < count($array[0]);$i++){
                    $cliente = Cliente::ClientesByCedula($array[0][$i][0])->first();
                    $datos[$count]['cliId'] = $cliente->cliente_id;
                    $datos[$count]['cliNombre'] = $cliente->cliente_nombre;
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
            return view('admin.cuentasCobrar.inicializar.nuevo',['datos'=>$datos,'diarioC'=>Diario::findOrFail($request->get('diario_id')),'diarios'=>Diario::Diarios()->where('diario_tipo','=','CDCO')->get(),'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        }
        catch(\Exception $ex){      
            return redirect('inicializarCXC')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
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
                return redirect('inicializarCXC')->with('error2','No puede realizar la operacion por que pertenece a un mes bloqueado');
            }
            
            $cliente = $request->get('Dcliente');
            $numero = $request->get('Dnumero');
            $valor = $request->get('Dvalor');
            $saldo = $request->get('Dsaldo');
            $fecha = $request->get('Dfecha');
            $vence = $request->get('Dvence');
     
            for ($i = 1; $i < count($cliente); ++$i){
                /********************cuenta por cobrar***************************/
                $cxc = new Cuenta_Cobrar();
                $cxc->cuenta_descripcion = $diario->diario_codigo.' VENTA CON FACTURA No. '.$numero[$i];
                $cxc->cuenta_tipo ='CREDITO';
                $cxc->cuenta_saldo = $saldo[$i];
                $cxc->cuenta_estado = '1';
                $cxc->cuenta_fecha = $fecha[$i];
                $cxc->cuenta_fecha_inicio = $fecha[$i];
                $cxc->cuenta_fecha_fin = $vence[$i];
                $cxc->cuenta_monto = $valor[$i];
                $cxc->cuenta_valor_factura = $valor[$i];
                $cxc->cliente_id = $cliente[$i];
                $cxc->sucursal_id = $diario->sucursal_id;
                $cxc->save();
                $general->registrarAuditoria('Registro de inicializacion de cuenta por cobrar de factura -> '.$numero[$i],$numero[$i],'Registro de inicializacion de cuenta por cobrar de factura -> '.$numero[$i].' con cliente -> '.$cliente[$i].' con un total de -> '.$valor[$i].' con un saldo de -> '.$saldo[$i]);
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
                $detalleDiario->detalle_comentario = 'P/R CUENTA POR COBRAR DE CLIENTE';
                $parametrizacionContable=Parametrizacion_Contable::ParametrizacionByNombre($diario->sucursal_id, 'CUENTA POR COBRAR')->first();
                if($parametrizacionContable->parametrizacion_cuenta_general == '1'){
                    $detalleDiario->cuenta_id = $parametrizacionContable->cuenta_id;
                }else{
                    $parametrizacionContable = Cliente::findOrFail($cliente[$i]);
                    $detalleDiario->cuenta_id = $parametrizacionContable->cliente_cuenta_cobrar;
                }
                $detalleDiario->cliente_id = $cliente[$i];
                $diario->detalles()->save($detalleDiario);
                $general->registrarAuditoria('Registro de detalle de diario con codigo -> '.$diario->diario_codigo,$numero[$i],'Registro de detalle de diario con codigo -> '.$diario->diario_codigo.' por inicializacion de cuenta por cobrar con cuenta contable -> '.$detalleDiario->cuenta->cuenta_numero.' en el haber por un valor de -> '.$saldo[$i]);
                /****************************************************************/
            }
            return(count($valor));
            $detalleDiarioAux->delete();
            $url = $general->pdfDiario($diario);
            DB::commit();
            return redirect('inicializarCXC')->with('success','Datos guardados exitosamente')->with('diario',$url);
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('inicializarCXC')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
}
