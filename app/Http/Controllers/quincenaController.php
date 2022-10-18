<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Banco;
use App\Models\Cheque;
use App\Models\Cierre_Mes_Contable;
use App\Models\Cuenta;
use App\Models\Detalle_Diario;
use App\Models\Diario;
use App\Models\Empleado;
use App\Models\Punto_Emision;
use App\Models\Quincena;
use App\Models\Rango_Documento;
use App\Models\Sucursal;
use App\Models\Transferencia;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Luecano\NumeroALetras\NumeroALetras;

class quincenaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        
    }
    public function mision()
    {
        try{

            
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $quincena=Quincena::QuincenaSinPuntoEmision()->get();
            return view('admin.recursosHumanos.quincena.editemeision',['quincena'=>$quincena,'sucursales'=>Sucursal::sucursales()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        }
        catch(\Exception $ex){      
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        } 

        
    }
    public function asignarmision(Request $request)
    {
        try{
            $quincenas=$request->get('checkbox');
            for ($i = 0; $i < count($quincenas); ++$i) {
               
                $rangoDocumento=Rango_Documento::PuntoRango($request->get('puntomision'), 'Quincena')->first();
                $secuencial=1;
                if ($rangoDocumento) {
                    $secuencial=$rangoDocumento->rango_inicio;
                    $secuencialAux=Quincena::secuencial($rangoDocumento->rango_id)->max('quincena_secuencial');
                    if ($secuencialAux) {
                        $secuencial=$secuencialAux+1;
                    }
                    $quincena=Quincena::findOrFail($quincenas[$i]); 
                    $quincena->rango_id=$rangoDocumento->rango_id;
                    $quincena->quincena_numero=$rangoDocumento->puntoEmision->sucursal->sucursal_codigo.$rangoDocumento->puntoEmision->punto_serie.substr(str_repeat(0, 9).$secuencial, - 9);
                    $quincena->quincena_serie=$rangoDocumento->puntoEmision->sucursal->sucursal_codigo.$rangoDocumento->puntoEmision->punto_serie;
                    $quincena->quincena_secuencial=$secuencial;
                    $quincena->save();
                    return redirect('quincenapuntoemision')->with('success','Pago realizado exitosamente');
                } else {
                    return redirect('inicio')->with('error', 'No tiene configurado, un punto de emisión o un rango de documentos para emitir quincenas, configueros y vuelva a intentar');
                }
            }
        }
        catch(\Exception $ex){      
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        } 

        
    }
    public function nuevo($id){
        try{
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
           
            $rangoDocumento=Rango_Documento::PuntoRango($id, 'Quincena')->first();   
            $secuencial=1;
            if($rangoDocumento){
                $secuencial=$rangoDocumento->rango_inicio;
                $secuencialAux=Quincena::secuencial($rangoDocumento->rango_id)->max('quincena_secuencial');
                if($secuencialAux){$secuencial=$secuencialAux+1;}
                return view('admin.recursosHumanos.quincena.index',['empleados'=>Empleado::EmpleadosRolSucursal($rangoDocumento->puntoEmision->sucursal_id)->get(),'sucursales'=>Sucursal::sucursales()->get(),'cuentas'=>Cuenta::CuentasMovimiento()->get(),'bancos'=>Banco::bancos()->get(),'secuencial'=>substr(str_repeat(0, 9).$secuencial, - 9),  'rangoDocumento'=>$rangoDocumento,'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
            }else{
                return redirect('inicio')->with('error','No tiene configurado, un punto de emisión o un rango de documentos para emitir facturas de venta, configueros y vuelva a intentar');
            }
        }
        catch(\Exception $ex){      
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
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
            $general = new generalController();
            $cierre = $general->cierre($request->get('idFecha'),Rango_Documento::rango($request->get('rango_id'))->first()->puntoEmision->sucursal_id);
            $urlcheque = '';
            if($cierre){
                return redirect('pquincena/new/'.$request->get('punto_id'))->with('error2','No puede realizar la operacion por que pertenece a un mes bloqueado');
            }
            $fecha=strtotime($request->get('idFecha'));
            $anio = date("Y", $fecha);
            $mes = date("m",$fecha);
            $fechainicio=($anio.'-'.$mes.-'01');
            $fechafin=(date("Y-m-t", strtotime($fechainicio)));
          
            if(count(Quincena::buscarquincena($request->get('idEmpleado'),$fechainicio,$fechafin)->get())>0){
                return redirect('pquincena/new/'.$request->get('punto_id'))->with('error2','Ya existe la quincena del empleado');
            }
            $empleado = Empleado::EmpleadoById($request->get('idEmpleado'))->first();
            
            $quincena = new Quincena();
            $quincena->quincena_numero = $request->get('quincena_serie').substr(str_repeat(0, 9).$request->get('quincena_numero'), - 9);;
            $quincena->quincena_serie = $request->get('quincena_serie');
            $quincena->quincena_secuencial = $request->get('quincena_numero');

            $quincena->quincena_fecha = $request->get('idFecha');
            $quincena->quincena_tipo = $request->get('idTipo');            
            $quincena->quincena_valor = $request->get('idValor');
            $quincena->quincena_saldo = $request->get('idValor');
            $quincena->rango_id = $request->get('rango_id');
            if ( $request->get('idMensaje')) {
                $quincena->quincena_descripcion =  $request->get('idMensaje');
            }
            else{
                $quincena->quincena_descripcion = ' ';
            }
            $quincena->empleado_id = $request->get('idEmpleado');        
            $quincena->quincena_estado = 1;   
            /*REGISTRO DE CHEQUE*/            
            if ($request->get('idTipo') == 'Cheque'){      
                $formatter = new NumeroALetras();
                $cheque = new Cheque();
                $cheque->cheque_numero = $request->get('idNcheque');
                $cheque->cheque_descripcion =  $request->get('descripcion');
                $cheque->cheque_beneficiario = $request->get('idBeneficiario');
                $cheque->cheque_fecha_emision = $request->get('idFecha');
                $cheque->cheque_fecha_pago = $request->get('idFechaCheque');
                $cheque->cheque_valor = $request->get('idValor');
                $cheque->cheque_valor_letras = $formatter->toInvoice($cheque->cheque_valor, 2, 'Dolares');
                $cheque->cuenta_bancaria_id = $request->get('cuenta_id');      
                $cheque->cheque_estado = '1';
                $cheque->empresa_id = Auth::user()->empresa->empresa_id;
                $cheque->save();
                $urlcheque = $general->pdfImprimeCheque($request->get('cuenta_id'),$cheque);
                $general->registrarAuditoria('Registro de Cheque de quincena numero: -> '.$request->get('idNcheque'),'0','Por motivo de: -> '. $quincena->quincena_descripcion.' con el valor de: -> '.$request->get('idValor'));
            } 
            /*REGISTRO DE TRANSFERENCIA*/            
            if ($request->get('idTipo') == 'Transferencia'){       
                $transferencia = new Transferencia();
                $transferencia->transferencia_descripcion =  $request->get('descripcion');
                $transferencia->transferencia_beneficiario = $request->get('nempleado');
                $transferencia->transferencia_fecha = $request->get('idFecha');
                $transferencia->transferencia_valor = $request->get('idValor');
                $transferencia->cuenta_bancaria_id = $request->get('cuenta_id');      
                $transferencia->transferencia_estado = '1';
                $transferencia->empresa_id = Auth::user()->empresa->empresa_id;
                $transferencia->save();
                $general->registrarAuditoria('Registro de Transferencia de quincena numero: -> '.$request->get('ncuenta'),'0','Por motivo de: -> '. $quincena->quincena_descripcion.' con el valor de: -> '.$request->get('idValor'));
            }        
           
            $tipo=Empleado::EmpleadoBusquedaCuenta($request->get('idEmpleado'),'quincena')->first();
              /**********************asiento diario****************************/
            $general = new generalController();
            $diario = new Diario();
            $diario->diario_codigo = $general->generarCodigoDiario($request->get('idFecha'),'CEQE');
            $diario->diario_fecha = $request->get('idFecha');
            $diario->diario_referencia = 'COMPROBANTE DE EMISION DE QUINCENA DE EMPLEADO';
            $diario->diario_numero_documento = 0;
            if ($request->get('idTipo') == 'Transferencia'){      
                $diario->diario_tipo_documento = 'TRANSFERENCIA BANCARIA'; 
            }
            if ($request->get('idTipo') == 'Cheque'){      
                $diario->diario_tipo_documento = 'CHEQUE'; 
                $diario->diario_numero_documento = $cheque->cheque_numero;
            }
            $diario->diario_beneficiario = $empleado->empleado_nombre;
            $diario->diario_tipo = 'CEQE';
            $diario->diario_secuencial = substr($diario->diario_codigo, 8);
            $diario->diario_mes = DateTime::createFromFormat('Y-m-d', $request->get('idFecha'))->format('m');
            $diario->diario_ano = DateTime::createFromFormat('Y-m-d', $request->get('idFecha'))->format('Y');
            $diario->diario_comentario = 'COMPROBANTE DE EMISION DE QUINCENA DE EMPLEADO: '.$empleado->empleado_nombre;
            $diario->diario_cierre = '0';
            $diario->diario_estado = '1';
            $diario->empresa_id = Auth::user()->empresa_id;
            $diario->sucursal_id =  $empleado->departamento->sucursal_id;
            $diario->save();
            $quincena->diario()->associate($diario);
            
            $general->registrarAuditoria('Registro de Diario de Diario codigo: -> '.$diario->diario_codigo,'0','Tipo de Diario -> '.$diario->diario_referencia.'');
            /********************detalle de diario de venta********************/
            $detalleDiario = new Detalle_Diario();
            $detalleDiario->detalle_debe =  0.00 ;
            $detalleDiario->detalle_haber =$request->get('idValor');
            $detalleDiario->detalle_comentario = 'P/R EL PAGO DE QUINCENA DEL EMPLEADO';
            $detalleDiario->detalle_tipo_documento = 'QUINCENA';
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
            $detalleDiario->detalle_debe =$request->get('idValor');
            $detalleDiario->detalle_haber =  0.00;
            setlocale(LC_TIME, "es");
            $detalleDiario->detalle_comentario = 'P/R ANTICIPO DE QUINCENA DE '.strtoupper(strftime("%B", strtotime($request->get('idFecha')))).' '.DateTime::createFromFormat('Y-m-d', $request->get('idFecha'))->format('Y');
            $detalleDiario->detalle_tipo_documento = 'QUINCENA';
            $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
            $detalleDiario->detalle_conciliacion = '0';
            $detalleDiario->detalle_estado = '1';
            $detalleDiario->cuenta_id = $tipo->cuenta_haber;   
            $detalleDiario->empleado_id = $request->get('idEmpleado');         
            $diario->detalles()->save($detalleDiario);
            $general->registrarAuditoria('Registro de Detalle de Diario codigo: -> '.$diario->diario_codigo,'0','En la cuenta del debe -> '.$tipo->cuenta_debe.' con el valor de: -> '.$request->get('idValor'));
                
            $quincena->save();
            /*Inicio de registro de auditoria */
            $auditoria = new generalController();
            $auditoria->registrarAuditoria('Registro de Quincena de Empleado -> '.$request->get('idEmpleado'),'0','Con motivo:'. $quincena->quincena_descripcion);
            /*Fin de registro de auditoria */
           
            $url = $general->pdfDiarioEgreso($diario);
            if ($request->get('idTipo') == 'Cheque') {
                DB::commit();
                return redirect('pquincena/new/'.$request->get('punto_id'))->with('success','Pago realizado exitosamente')->with('diario',$url)->with('cheque',$urlcheque);
            }
            DB::commit();
            return redirect('pquincena/new/'.$request->get('punto_id'))->with('success','Datos guardados exitosamente')->with('diario',$url);
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('pquincena/new/'.$request->get('punto_id'))->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
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
        return redirect('/denegado');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return redirect('/denegado');
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
        return redirect('/denegado');
    }
    public function imprimir($id)
    {
        try{
            $quincena=Quincena::Quincena($id)->get()->first();
            $general = new generalController();
            $url = $general->pdfDiarioEgresourl($quincena->diario);
            return $url;
        }
        catch(\Exception $ex){      
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
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
        return redirect('/denegado');  
    }
    public function buscarBy(){
        return Quincena::Quincenas()->get();
    }
    public function buscarByEmpleado($ide){
        return Quincena::QuincenaEmpleado($ide)->get();
    }
}
