<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Banco;
use App\Models\Beneficios_Sociales;
use App\Models\Cheque;
use App\Models\Cuenta;
use App\Models\Detalle_Diario;
use App\Models\Diario;
use App\Models\Empleado;
use App\Models\Punto_Emision;
use App\Models\Rango_Documento;
use App\Models\Tipo_Movimiento_Empleado;
use App\Models\Transferencia;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Luecano\NumeroALetras\NumeroALetras;

class beneficiosSocialesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }
    public function nuevo($id){
        try{ 
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $rangoDocumento=Rango_Documento::PuntoRango($id, 'Beneficios Sociales')->first();
            if($rangoDocumento){
                return view('admin.recursosHumanos.beneficiosSociales.view',['movimientos'=>Tipo_Movimiento_Empleado::TipoMovimientos()->get(),'empleados'=>Empleado::EmpleadosRolSucursal($rangoDocumento->puntoEmision->sucursal_id)->get(),'cuentas'=>Cuenta::CuentasMovimiento()->get(),'bancos'=>Banco::bancos()->get(),  'rangoDocumento'=>$rangoDocumento,'PE'=>Punto_Emision::puntos()->get(),'rangoDocumento'=>$rangoDocumento,'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
            }else{
                return redirect('inicio')->with('error','No tiene configurado, un punto de emisión o un rango de documentos para emitir facturas de venta, configueros y vuelva a intentar');
            }
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
            $urlcheque = '';
            $general = new generalController();
            $fechadesde=$request->get('fecha_desde')."-01";
           
            $fechahasta=$request->get('fecha_hasta')."-01";
            $dth = new DateTime($fechahasta);
            $fechahasta=($dth->format('Y-m-t'));
            $cierre = $general->cierre($request->get('idFechaemision'),$request->get('sucursal_id'));
            if ($cierre) {
                return redirect('beneficioSocial/new/'.$request->get('punto_id'))->with('error2', 'No puede realizar la operacion por que pertenece a un mes bloqueado');
            }
            $emplead=Beneficios_Sociales::validarbeneficio($fechadesde,$request->get('Tipo_id'),$request->get('sucursal_id'),$request->get('empleado_id'))->get();
         
            $empleado=Empleado::findOrFail($request->get('empleado_id'));
            if(count($emplead)>0){
                return redirect('beneficioSocial/new/'.$request->get('punto_id'))->with('error2', 'Ya existe el Beneficio del empleado');
            }
        
            $dt = new DateTime($fechadesde);
            $dt = new DateTime($fechadesde);
            $beneficio =new Beneficios_Sociales();
            $beneficio->beneficios_fecha_emision = $request->get('idFechaemision');
            $beneficio->beneficios_fecha = $dt->format('d/m/Y');
            $beneficio->beneficios_tipo = $request->get('idTipo');
            $beneficio->beneficios_periodo ='Periodo desde '.DateTime::createFromFormat('Y-m-d', ($dt->format('Y-m-d')))->format('m').' - '.DateTime::createFromFormat('Y-m-d', ($dt->format('Y-m-d')))->format('Y').' hasta '.DateTime::createFromFormat('Y-m-d', ($dth->format('Y-m-d')))->format('m').' - '.DateTime::createFromFormat('Y-m-d', ($dth->format('Y-m-d')))->format('Y');
            $beneficio->beneficios_valor = $request->get('idValor');
            $beneficio->beneficios_descripcion =   $request->get('idMensaje');
            $beneficio->empleado_id =  $request->get('empleado_id');
            $beneficio->beneficios_estado = 1;
            $beneficio->tipo_id = $request->get('Tipo_id');
            $tipo = Tipo_Movimiento_Empleado::findOrFail($request->get('Tipo_id'));

            if ($request->get('idTipo') == 'Cheque'){      
                $formatter = new NumeroALetras();
                $cheque = new Cheque();
                $cheque->cheque_numero = $request->get('idNcheque');
                $cheque->cheque_descripcion =  $request->get('descripcion');
                $cheque->cheque_beneficiario = $request->get('idBeneficiario');
                $cheque->cheque_fecha_emision = $request->get('idFechaemision');
                $cheque->cheque_fecha_pago = $request->get('idFechaCheque');
                $cheque->cheque_valor = $request->get('idValor');
                $cheque->cheque_valor_letras = $formatter->toInvoice($cheque->cheque_valor, 2, 'Dolares');
                $cheque->cuenta_bancaria_id = $request->get('cuenta_id');      
                $cheque->cheque_estado = '1';
                $cheque->empresa_id = Auth::user()->empresa->empresa_id;
                $cheque->save();
                $urlcheque = $general->pdfImprimeCheque($request->get('cuenta_id'),$cheque);
                $general->registrarAuditoria('Registro de Cheque numero de beneficios sociales: -> '.$request->get('idNcheque'),'0','Por motivo de: -> '. $beneficio->beneficios_descripcion.' con el valor de: -> '.$request->get('idValor'));
            } 
            /*REGISTRO DE TRANSFERENCIA*/            
            if ($request->get('idTipo') == 'Transferencia'){       
                $transferencia = new Transferencia();
                $transferencia->transferencia_descripcion =  $request->get('descripcion');
                $transferencia->transferencia_beneficiario = $empleado->empleado_nombre;
                $transferencia->transferencia_fecha = $request->get('idFechaCheque');
                $transferencia->transferencia_valor = $request->get('idValor');
                $transferencia->cuenta_bancaria_id = $request->get('cuenta_id');      
                $transferencia->transferencia_estado = '1';
                $transferencia->empresa_id = Auth::user()->empresa->empresa_id;
                $transferencia->save();
                $general->registrarAuditoria('Registro de Transferencia de beneficios sociales numero: -> '.$request->get('ncuenta'),'0','Por motivo de: -> '. $beneficio->beneficios_descripcion.' con el valor de: -> '.$request->get('idValor'));
            }  

            $diario = new Diario();
            $diario->diario_codigo = $general->generarCodigoDiario(($request->get('idFechaCheque')), 'CPUE');
            $diario->diario_fecha = $request->get('idFechaCheque');
            $diario->diario_referencia = 'COMPROBANTE DE PAGO DE UTILIDADES DE EMPLEADOS';
            $diario->diario_numero_documento = 0;
            if ($request->get('idTipo') == 'Transferencia'){      
                $diario->diario_tipo_documento = 'TRANSFERENCIA BANCARIA'; 
            }
            if ($request->get('idTipo') == 'Cheque'){      
                $diario->diario_tipo_documento = 'CHEQUE'; 
                $diario->diario_numero_documento = $request->get('idNcheque');
            }
            $diario->diario_beneficiario =  $empleado->empleado_nombre;
            $diario->diario_tipo = 'CPUE';
            $diario->diario_secuencial = substr($diario->diario_codigo, 8);
            $diario->diario_mes = DateTime::createFromFormat('Y-m-d', $request->get('idFechaCheque'))->format('m');
            $diario->diario_ano = DateTime::createFromFormat('Y-m-d', $request->get('idFechaCheque'))->format('Y');
            $diario->diario_comentario = 'COMPROBANTE DE PAGO DE UTILIADADES CORRESPONDIENTE AL AÑO '.DateTime::createFromFormat('Y-m-d', ($dt->format('Y-m-d')))->format('Y');

            $diario->diario_cierre = '0';
            $diario->diario_estado = '1';
            $diario->empresa_id = Auth::user()->empresa_id;
            $diario->sucursal_id =  $request->get('sucursal_id');
            $diario->save();

            $beneficio->diario()->associate($diario);
            $general->registrarAuditoria('Registro de Diario de Diario codigo: -> '.$diario->diario_codigo,'0','Tipo de Diario -> '.$diario->diario_referencia.'');

            $beneficio->save();
            $auditoria = new generalController();
            $auditoria->registrarAuditoria('Registro de Utilidades de Empleado -> '.$request->get('empleado_id'),'0','Con motivo:'. $beneficio->beneficios_descripcion);
            
            
            $detalleDiario = new Detalle_Diario();
            $detalleDiario->detalle_debe =  0.00 ;
            $detalleDiario->detalle_haber =$request->get('idValor');
            $detalleDiario->detalle_comentario = 'P/R EL PAGO DE UTILIDADES DEL EMPLEADO';
            $detalleDiario->detalle_tipo_documento = 'UTILIDADES';
            $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
            $detalleDiario->detalle_conciliacion = '0';
            $detalleDiario->detalle_estado = '1';           
            $detalleDiario->cuenta_id = $request->get('idCuentaContable');
            if ($request->get('idTipo') == 'Cheque'){      
                $detalleDiario->detalle_comentario = 'CHEQUE No '.$cheque->cheque_numero;
                $detalleDiario->cheque()->associate($cheque);
            }
            if ($request->get('idTipo') == 'Transferencia'){      
                $detalleDiario->detalle_comentario = 'TRANSFERENCIA A CUENTA No '.$empleado->empleado_cuenta_numero;
                $detalleDiario->transferencia()->associate($transferencia);
            }
            $diario->detalles()->save($detalleDiario);
            $general->registrarAuditoria('Registro de Detalle de Diario codigo: -> '.$diario->diario_codigo,'0','En la cuenta del Haber -> '.$request->get('idCuentaContable').' con el valor de: -> '.$request->get('idValor'));
            
            $detalleDiario = new Detalle_Diario();
            $detalleDiario->detalle_debe =  $request->get('idValor') ;
            $detalleDiario->detalle_haber =0.00;
            setlocale(LC_TIME, "es");
            $detalleDiario->detalle_comentario = 'P/R PAGO DEL UTILIDADES DESDE '.strtoupper(strftime("%B", strtotime($fechadesde))).' '.DateTime::createFromFormat('Y-m-d', $fechadesde)->format('Y').' HASTA '.strtoupper(strftime("%B", strtotime($fechahasta))).' '.DateTime::createFromFormat('Y-m-d', $fechahasta)->format('Y');
            $detalleDiario->detalle_tipo_documento = 'UTILIDADES';
            $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
            $detalleDiario->detalle_conciliacion = '0';
            $detalleDiario->detalle_estado = '1';           
            $detalleDiario->cuenta_id = $tipo->cuenta_id;   
            $detalleDiario->empleado_id = $request->get('empleado_id');     
            $diario->detalles()->save($detalleDiario);
            $general->registrarAuditoria('Registro de Detalle de Diario codigo: -> '.$diario->diario_codigo,'0','En la cuenta del Haber -> '.$request->get('idCuentaContable').' con el valor de: -> '.$request->get('idValor'));
            
            
            
            $url = $general->pdfDiario($diario);
            if ($request->get('idTipo') == 'Cheque') {
                DB::commit(); 
                return redirect('beneficioSocial/new/'.$request->get('punto_id'))->with('success', 'Datos guardados exitosamente')->with('pdf', $url)->with('cheque',$urlcheque);;
            }

            /*Inicio de registro de auditoria */
           
            /*Fin de registro de auditoria */
            DB::commit(); 
            return redirect('beneficioSocial/new/'.$request->get('punto_id'))->with('success', 'Datos guardados exitosamente')->with('pdf', $url);

        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('beneficioSocial/new/'.$request->get('punto_id'))->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function eliminar($id)
    {
        try{
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $beneficios=Beneficios_Sociales::findOrFail($id);
            $transferencia=null;
            $cheque=null;
            foreach ($beneficios->diario->detalles as $i) {
                   
                if (isset($i->cheque_id)) {
                    $cheque=Cheque::findOrFail($i->cheque_id);
                }
                if (isset($i->transferencia_id)) {
                    $transferencia=Transferencia::findOrFail($i->transferencia_id);
                }
            }
            
            return view('admin.recursosHumanos.beneficiosSociales.eliminar', ['transferencia'=>$transferencia,'cheque'=>$cheque,'beneficios'=>$beneficios,'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        }catch(\Exception $ex){
            return redirect('inicio')->with('error','Ocurrio un error vuelva a intentarlo('.$ex->getMessage().')');
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
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        //
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
            $beneficio=Beneficios_Sociales::findOrFail($id);
            $diario=Diario::findOrFail($beneficio->diario_id);
            $general = new generalController();
            $cierre = $general->cierre($beneficio->beneficios_fecha,$diario->sucursal_id);          
            if($cierre){
                return redirect('listabeneficios')->with('error2','No puede realizar la operacion por que pertenece a un mes bloqueado');
            }
            $diariocoun=Beneficios_Sociales::Validacion($beneficio->diario_id)->get();
            if (count($diariocoun)==1) {
                foreach ($beneficio->diario->detalles as $i) {
                
                    if (isset($i->cheque_id)) {
                       
                        $detalle=Detalle_Diario::findOrFail($i->detalle_id);
                      
                        $chequeAux=Cheque::findOrFail($i->cheque_id);
                       
                        $detalle->cheque_id=null;
                        $detalle->save();
                        
                        $chequeAux->delete();
                        $general->registrarAuditoria('Eliminacion de Cheque numero: -> '.$chequeAux->cheque_numero, $id, 'Con UTILIDADES  id -> '.$id.'Con valor de -> '.$chequeAux->cheque_valor);         
                    }          
                    if (isset($i->transferencia_id)) {
                        $detalle=Detalle_Diario::findOrFail($i->detalle_id);
                        
                        $transferenciaAux=Transferencia::findOrFail($i->transferencia_id);
    
                        $detalle->transferencia_id=null;
                        $detalle->save();
    
                        $transferenciaAux->delete();
                        $general->registrarAuditoria('Eliminacion de Transferencia numero: -> '.$transferenciaAux->transferencia_numero, $id, 'Con UTILIDADES  id -> '.$id.'Con valor de -> '.$transferenciaAux->transferencia_valor);  
                    }
                   
    
                    $i->delete();
                    $general = new generalController();
                    $general->registrarAuditoria('Eliminacion del detalle diario tipo documento numero: -> '.$i->detalle_tipo_documento.'con empleado '.$beneficio->empleado->emepleado_nombre, $id,'Con UTILIDADES  id -> '.$i.'con codigo de diario'.$beneficio->diario->diario_codigo);
                   
                }    
                $beneficio->delete();
                $general->registrarAuditoria('Eliminacion del Beneficio Social: -> '.$id.'con empleado '.$beneficio->empleado->emepleado_nombre, $id, 'Con UTILIDADES  id -> '.$id);     
    
                $diario->delete();
                $general->registrarAuditoria('Eliminacion de Dario: -> '.$id.'con empleado '.$beneficio->empleado->emepleado_nombre, $id, 'Con UTILIDADES  id -> '.$id);     
              
                
                DB::commit();
                return redirect('listabeneficios')->with('success','Datos Eliminados exitosamente');
            } 
            else{
                    $detalle=Detalle_Diario::Empleadodiario($beneficio->diario_id,$beneficio->empleado_id)->first();
                  
                    $detalleaux=Detalle_Diario::findOrFail($detalle->detalle_id);
                    $detalleaux->delete();

                    foreach ($beneficio->diario->detalles as $i) {
                        if (isset($i->transferencia_id)) {
                           
                            $transferenciaAux=Transferencia::findOrFail($i->transferencia_id);
                            $transferenciaAux->transferencia_valor=$transferenciaAux->transferencia_valor-$detalle->detalle_debe;
                            $transferenciaAux->save();
                            $detalleaux=Detalle_Diario::findOrFail($i->detalle_id);
                            $detalleaux->detalle_haber=$detalleaux->detalle_haber-$detalle->detalle_debe;
                            $detalleaux->save();
                           
                        }
                    }
                   
                    $beneficio->delete();
                    $general->registrarAuditoria('Eliminacion del Beneficio Social: -> '.$id.'con empleado '.$beneficio->empleado->emepleado_nombre, $id, 'Con Beneficio Social  id -> '.$id); 
                    $url = $general->pdfDiario($diario);       
                    DB::commit();
                    return redirect('listabeneficios')->with('success','Datos Eliminados exitosamente')->with('pdf',$url);
            }  
        }catch(\Exception $ex){
            return redirect('listabeneficios')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        } 
    }
    
   
    public function imprimirdiario($id)
    { 
        try{
            $beneficio=Beneficios_Sociales::beneficio($id)->get()->first();
            $general = new generalController();
            $url = $general->pdfDiariourl2($beneficio->diario);
            return $url;
        }catch(\Exception $ex){
            return redirect('inicio')->with('error','Ocurrio un error vuelva a intentarlo('.$ex->getMessage().')');
        }
    }
}
