<?php

namespace App\Http\Controllers;
use App\Models\Anticipo_Empleado;
use App\Models\Detalle_Diario;
use App\Models\Empleado;
use App\Models\Cheque;
use App\Models\Parametrizacion_Contable;
use App\Models\Banco;
use App\Models\Diario;
use App\Models\Punto_Emision;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Luecano\NumeroALetras\NumeroALetras;

use App\Http\Controllers\Controller;
use App\Models\Arqueo_Caja;
use App\Models\Caja;
use App\Models\Cuenta_Bancaria;
use App\Models\Descuento_Anticipo_Empleado;
use App\Models\Empresa;
use App\Models\Movimiento_Caja;
use App\Models\Rango_Documento;
use App\Models\Sucursal;
use App\Models\Transferencia;
use DateTime;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class anticipoEmpleadoController extends Controller
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
        return redirect('/denegado');
    }

    public function excelAnticipoEmpleado()
    {
        try{
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            return view('admin.recursosHumanos.listaAnticipo.cargarExcel',['PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function CargarExcel(Request $request)
    {
        try{
            DB::beginTransaction();
            if($request->file('excelEmpl')->isValid()){
                $empresa = Empresa::empresa()->first();
                $name = $empresa->empresa_ruc. '.' .$request->file('excelEmpl')->getClientOriginalExtension();
                $path = $request->file('excelEmpl')->move(public_path().'\temp', $name); 
                $array = Excel::toArray(new Anticipo_Empleado(), $path); 
                $detalleDiarioAux =  Detalle_Diario::findOrFail($request->get('idDiario'));
                $diario = $detalleDiarioAux->diario;
                $activador=false;
                $general = new generalController(); 
                for ($i=1;$i < count($array[0]);$i++) {
                    $validar=trim($array[0][$i][5]);
                    
                    if ($validar) {
                      
                        $empleado = Empleado::EmpleadoByCed($validar)->first();
                        if ($empleado) {
                          
                            /*extraer punto de emision y secuencial*/
                           
                            $puntoemeision = null;
                            $puntosEmision = Punto_Emision::PuntoxSucursal($diario->sucursal_id)->get();
                            foreach($puntosEmision as $punto){
                                $rangoDocumento=Rango_Documento::PuntoRango($punto->punto_id, 'Anticipo de Empleado')->first();
                                if($rangoDocumento){
                                    $puntoemeision = $punto;
                                    break;
                                }
                            }
                            if ($rangoDocumento) {
                                $secuencial=$rangoDocumento->rango_inicio;
                                $secuencialAux=Anticipo_Empleado::secuencial($rangoDocumento->rango_id)->max('anticipo_secuencial');
                                if ($secuencialAux) {
                                    $secuencial=$secuencialAux+1;
                                }
                            }
                            $anticipoEmpleado = new Anticipo_Empleado();
                            $anticipoEmpleado->anticipo_numero = $rangoDocumento->puntoEmision->sucursal->sucursal_codigo.$rangoDocumento->puntoEmision->punto_serie.substr(str_repeat(0, 9).$secuencial, - 9);
                            $anticipoEmpleado->anticipo_serie = $rangoDocumento->puntoEmision->sucursal->sucursal_codigo.$rangoDocumento->puntoEmision->punto_serie;
                            $anticipoEmpleado->anticipo_secuencial = $secuencial;
                            
                            $Excel_date2 = $array[0][$i][0]; 
                            $unix_date2 = ($Excel_date2 - 25569) * 86400;
                            $Excel_date2 = 25569 + ($unix_date2 / 86400);
                            $unix_date2 = ($Excel_date2 - 25569) * 86400;
                            $anticipoEmpleado->anticipo_fecha = gmdate("Y-m-d", $unix_date2);

                            $anticipoEmpleado->anticipo_tipo = ($array[0][$i][1]);
                            $anticipoEmpleado->anticipo_documento ='0';
                                  
                            $anticipoEmpleado->anticipo_motivo = ($array[0][$i][2]);
                            $anticipoEmpleado->anticipo_valor = ($array[0][$i][3]);  
                            $anticipoEmpleado->anticipo_saldo = ($array[0][$i][4]);   
                            $anticipoEmpleado->anticipo_saldom = ($array[0][$i][4]); 

                            $anticipoEmpleado->empleado_id = $empleado->empleado_id;
                            $anticipoEmpleado->rango_id =$rangoDocumento->rango_id;
                            $anticipoEmpleado->anticipo_documento = 0; 
                            $anticipoEmpleado->anticipo_estado = 1;

                            $detalleDiario = new Detalle_Diario();
                            $detalleDiario->detalle_debe =  ($array[0][$i][4]);
                            $detalleDiario->detalle_haber = 0.00 ;
                            $detalleDiario->detalle_comentario = 'P/R CUENTA DE ANTICIPO EMPLEADO';
                            $detalleDiario->detalle_tipo_documento = 'ANTICIPO DE EMPLEADO';
                            $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                            $detalleDiario->detalle_conciliacion = '0';
                            $detalleDiario->detalle_estado = '1';        
                            $detalleDiario->empleado_id = $empleado->empleado_id;
                            
                            $parametrizacionContable=Parametrizacion_Contable::ParametrizacionByNombre($diario->sucursal_id, 'ANTICIPO DE EMPLEADO')->first();
                            if($parametrizacionContable->parametrizacion_cuenta_general == '1'){
                                $detalleDiario->cuenta_id = $parametrizacionContable->cuenta_id;
                            }else{
                                $parametrizacionContable = Empleado::findOrFail($empleado->empleado_id);
                                $detalleDiario->cuenta_id = $parametrizacionContable->empleado_cuenta_anticipo;
                            }
                            $diario->detalles()->save($detalleDiario);
                            $general->registrarAuditoria('Registro de Detalle de Diario codigo: -> '.$diario->diario_codigo,'0','En la cuenta del Haber -> '.$detalleDiario->cuenta->cuenta_numero.' con el valor de: -> '.$array[0][$i][3]);
                            
                          
                            $anticipoEmpleado->diario_id= $diario->diario_id;
                            $anticipoEmpleado->save();
                            $activador=true;
                        }
                    }
                }
                /*
                if($activador==true){
                    $detalleDiarioAux->delete();
                }
                */
            }
            DB::commit();
            return redirect('excelAnticipoEmpleado')->with('success','Datos guardados exitosamente');
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('excelAnticipoEmpleado')->with('error2','Ocurrio un error vuelva a intentarlo('.$ex->getMessage().')');
        }
        
    }
    public function store(Request $request)
    {
        try{           
            DB::beginTransaction();       
            $rangoDocumento = Rango_Documento::Rango($request->get('rango_id'))->first();     
            $general = new generalController();
            $cierre = $general->cierre($request->get('idFecha'),$rangoDocumento->puntoEmision->sucursal_id);
            $urlcheque = '';
            if($cierre){
                return redirect('anticipoEmpleado/new/'.$rangoDocumento->punto_id)->with('error2','No puede realizar la operacion por que pertenece a un mes bloqueado');
            }
            $movimientoCaja = new Movimiento_Caja();
            $cajasxusuario=Arqueo_Caja::arqueoCajaxuser(Auth::user()->user_id)->first();  
            $cuentaBanco = Cuenta_Bancaria::CuentaBanco($request->get('cuenta_id'))->first();
            
            $cuentacaja=Caja::caja($request->get('idCaja'))->first();
            $empleado = Empleado::empleado($request->get('idEmpleado'))->first();
            $anticipoEmpleado = new Anticipo_Empleado();
            $anticipoEmpleado->anticipo_numero = $request->get('anticipo_serie').substr(str_repeat(0, 9).$request->get('anticipo_numero'), - 9);
            $anticipoEmpleado->anticipo_serie = $request->get('anticipo_serie');
            $anticipoEmpleado->anticipo_secuencial = $request->get('anticipo_numero');
            $anticipoEmpleado->anticipo_fecha = $request->get('idFecha');
            $anticipoEmpleado->anticipo_tipo = $request->get('idTipo');      
            if($request->get('idTipo') == "Efectivo"){
                $anticipoEmpleado->anticipo_documento = 0;
                $anticipoEmpleado->arqueo_id = $cajasxusuario->arqueo_id;
            } 
            if($request->get('idTipo') == "Cheque"){
                $anticipoEmpleado->anticipo_documento = $request->get('idNcheque');      
            }
            if($request->get('idTipo') == "Transferencia"){
                $anticipoEmpleado->anticipo_documento = 0;      
            }                
            $anticipoEmpleado->anticipo_motivo = $request->get('idMensaje');
            $anticipoEmpleado->anticipo_valor = $request->get('idValor');  
            $anticipoEmpleado->anticipo_saldo = $request->get('idValor');   
            $anticipoEmpleado->empleado_id = $request->get('idEmpleado');
            $anticipoEmpleado->rango_id = $request->get('rango_id'); 
            $anticipoEmpleado->anticipo_estado = 1;         
            /*REGISTRO DE CHEQUE*/            
            if($request->get('idTipo') == "Cheque"){
                $formatter = new NumeroALetras();
                $cheque = new Cheque();
                $cheque->cheque_numero = $request->get('idNcheque');
                $cheque->cheque_descripcion = $request->get('idMensaje');
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
                $general->registrarAuditoria('Registro de Cheque numero de anticipo de empleado: -> '.$request->get('idNcheque'),'0','Por motivo de: -> '.$request->get('idMensaje').' con el valor de: -> '.$request->get('idValor'));
            } 
            /**********************asiento diario****************************/
            $general = new generalController();
            $diario = new Diario();
            $diario->diario_codigo = $general->generarCodigoDiario($request->get('idFecha'),'CEAE');
            $diario->diario_fecha = $request->get('idFecha');
            $diario->diario_referencia = 'COMPROBANTE DE ANTICIPO DE EMPLEADO';
            $diario->diario_tipo_documento = 'ANTICIPO DE EMPLEADO';
            $diario->diario_numero_documento = $request->get('anticipo_serie').substr(str_repeat(0, 9).$request->get('anticipo_numero'), - 9);
            $diario->diario_beneficiario = $empleado->empleado_nombre;
            $diario->diario_tipo = 'CEAE';
            $diario->diario_secuencial = substr($diario->diario_codigo, 8);
            $diario->diario_mes = DateTime::createFromFormat('Y-m-d', $request->get('idFecha'))->format('m');
            $diario->diario_ano = DateTime::createFromFormat('Y-m-d', $request->get('idFecha'))->format('Y');
            $diario->diario_comentario = $request->get('idMensaje');
            if($request->get('idTipo') == "Cheque"){
                $diario->diario_tipo_documento = 'ANTICIPO DE EMPLEADO';
                $diario->diario_numero_documento = $cheque->cheque_numero;
            }
            if ($request->get('idTipo') == 'Transferencia'){      
                $diario->diario_tipo_documento = 'TRANSFERENCIA BANCARIA'; 
                $diario->diario_numero_documento = 0;
            } 
            $diario->diario_cierre = '0';
            $diario->diario_estado = '1';
            $diario->empresa_id = Auth::user()->empresa_id;
            $diario->sucursal_id = $rangoDocumento->puntoEmision->sucursal_id;          
            $diario->save();
            $anticipoEmpleado->diario()->associate($diario);
            if($request->get('idTipo') == "Efectivo"){
                $movimientoCaja->diario()->associate($diario);
            }
            
            $general->registrarAuditoria('Registro de Diario de Diario codigo: -> '.$diario->diario_codigo,'0','Tipo de Diario -> '.$diario->diario_referencia.'');
            if($request->get('idTipo') == "Efectivo"){               
                /**********************movimiento caja****************************/               
               $movimientoCaja->movimiento_fecha=date("Y")."-".date("m")."-".date("d");
               $movimientoCaja->movimiento_hora=date("H:i:s");
               $movimientoCaja->movimiento_tipo="SALIDA";
               $movimientoCaja->movimiento_descripcion= $request->get('idMensaje');
               $movimientoCaja->movimiento_valor= $request->get('idValor');
               $movimientoCaja->movimiento_documento="ANTICIPO DE EMPLEADO";
               $movimientoCaja->movimiento_numero_documento= $request->get('anticipo_serie').substr(str_repeat(0, 9).$request->get('anticipo_numero'), - 9);
               $movimientoCaja->movimiento_estado = 1;
               $movimientoCaja->arqueo_id = $cajasxusuario->arqueo_id;
               $movimientoCaja->save(); 
               $general->registrarAuditoria('Registro de Movimiento numero: -> '.$request->get('anticipo_serie').substr(str_repeat(0, 9).$request->get('anticipo_numero'), - 9),'0','Por motivo de: -> '.$request->get('idMensaje').' con el valor de: -> '.$request->get('idValor'));
  
           }          
            /*REGISTRO DE TRANSFERENCIA*/            
            if($request->get('idTipo') == "Transferencia"){
                $transferencia = new Transferencia();
                $transferencia->transferencia_descripcion = $request->get('idMensaje');
                $transferencia->transferencia_beneficiario = $empleado->empleado_nombre;
                $transferencia->transferencia_fecha = $request->get('idFecha');
                $transferencia->transferencia_valor = $request->get('idValor');
                $transferencia->cuenta_bancaria_id = $request->get('cuenta_id');
                $transferencia->transferencia_estado = '1';
                $transferencia->empresa_id = Auth::user()->empresa->empresa_id;
                $transferencia->save();
                $general->registrarAuditoria('Registro de transferencia por anticipo a proveedor','0','Por motivo de: -> '.$request->get('idMensaje').' con el valor de: -> '.$request->get('idValor')); 
            } 
            /********************detalle de diario de venta********************/
            $detalleDiario = new Detalle_Diario();
            $detalleDiario->detalle_debe = $request->get('idValor');
            $detalleDiario->detalle_haber = 0.00 ;
            $detalleDiario->detalle_comentario = 'P/R ANTICIPO EMPLEADO DEL '.DateTime::createFromFormat('Y-m-d', $request->get('idFecha'))->format('d/m/Y');
            $detalleDiario->detalle_tipo_documento = 'ANTICIPO DE EMPLEADO';
            $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
            $detalleDiario->detalle_conciliacion = '0';
            $detalleDiario->detalle_estado = '1';
            $detalleDiario->empleado_id = $request->get('idEmpleado');
            $parametrizacionContable=Parametrizacion_Contable::ParametrizacionByNombre($diario->sucursal_id, 'ANTICIPO DE PROVEEDOR')->first();
            $parametrizacionContable2=Empleado::EmpleadoBusquedaCuenta($request->get('idEmpleado'),'anticipos')->first();
            if($parametrizacionContable->parametrizacion_cuenta_general == '1'){
                $detalleDiario->cuenta_id = $parametrizacionContable2->cuenta_debe;
            }else{
                $parametrizacionContable = Empleado::findOrFail($request->get('idEmpleado'));
                $detalleDiario->cuenta_id = $parametrizacionContable->empleado_cuenta_anticipo;
            }
            $diario->detalles()->save($detalleDiario);
            $general->registrarAuditoria('Registro de Detalle de Diario codigo: -> '.$diario->diario_codigo,'0','En la cuenta del debe -> '.$detalleDiario->cuenta->cuenta_numero.' con el valor de: -> '.$request->get('idValor'));
            
            $detalleDiario = new Detalle_Diario();
            $detalleDiario->detalle_debe = 0.00;
            $detalleDiario->detalle_haber = $request->get('idValor');
            $detalleDiario->detalle_tipo_documento = 'ANTICIPO DE EMPLEADO';
            $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
            $detalleDiario->detalle_conciliacion = '0';
            $detalleDiario->detalle_estado = '1';
            if($request->get('idTipo') == "Efectivo"){
                $detalleDiario->detalle_comentario = 'CUENTA DE ANTICIPO DE CAJA';
                $detalleDiario->cuenta_id = $cuentacaja->cuenta_id;
            }
            if($request->get('idTipo') == "Cheque" ){
                $detalleDiario->detalle_comentario = 'CHEQUE No '.$cheque->cheque_numero;
                $detalleDiario->cuenta_id = $cuentaBanco->cuenta_id;
                $detalleDiario->cheque()->associate($cheque);
            }
            if($request->get('idTipo') == "Transferencia"){
                $detalleDiario->detalle_comentario = 'TRANSFERENCIA A CUENTA No '.$empleado->empleado_cuenta_numero;
                $detalleDiario->cuenta_id = $cuentaBanco->cuenta_id;
                $detalleDiario->transferencia()->associate($transferencia);
            }         
            $diario->detalles()->save($detalleDiario);
            $general->registrarAuditoria('Registro de Detalle de Diario codigo: -> '.$diario->diario_codigo,'0','En la cuenta del Haber -> '.$detalleDiario->cuenta->cuenta_numero.' con el valor de: -> '.$request->get('idValor'));
            
            
            $anticipoEmpleado->save();
            /*Inicio de registro de auditoria */
            $auditoria = new generalController();
            $auditoria->registrarAuditoria('Registro de Anticipo de Empleado -> '.$request->get('idEmpleado'),'0','Con motivo:'.$request->get('idMensaje'));
            /*Fin de registro de auditoria */
            $url = $general->pdfDiario($diario);
            if ($request->get('idTipo') == "Cheque") {
                DB::commit();
                return redirect('anticipoEmpleado/new/'.$rangoDocumento->punto_id)->with('success','Datos guardados exitosamente')->with('diario',$url)->with('cheque',$urlcheque);
            }
            DB::commit();
            return redirect('anticipoEmpleado/new/'.$rangoDocumento->punto_id)->with('success','Datos guardados exitosamente')->with('diario',$url);
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('anticipoEmpleado/new/'.$rangoDocumento->punto_id)->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
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
    public function nuevo($id){        
        $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
        $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
        $cajasxusuario=Arqueo_Caja::arqueoCajaxuser(Auth::user()->user_id)->first();    
        $cajas = Caja::cajas()->get();
        $rangoDocumento=Rango_Documento::PuntoRango($id, 'Anticipo de Empleado')->first();        
        $sucursalp=Punto_Emision::punto($id)->first();            
        $secuencial=1;        
        if($rangoDocumento){
            $secuencial=$rangoDocumento->rango_inicio;
            $secuencialAux=Anticipo_Empleado::secuencial($rangoDocumento->rango_id)->max('anticipo_secuencial');
            if($secuencialAux){
                $secuencial=$secuencialAux+1;
            }
            return view('admin.recursosHumanos.anticipoEmpleado.nuevo',
            ['empleados'=>Empleado::EmpleadosBySucursal($rangoDocumento->puntoEmision->sucursal_id)->get(),
            'bancos'=>Banco::bancos()->get(),
            'PE'=>Punto_Emision::puntos()->get(),
            'gruposPermiso'=>$gruposPermiso,
            'rangoDocumento'=>$rangoDocumento,
            'cajasxusuario'=>$cajasxusuario,
            'cajas'=>$cajas,
            'sucursalp'=>$sucursalp->sucursal_id,
            'secuencial'=>substr(str_repeat(0, 9).$secuencial, - 9),
            'permisosAdmin'=>$permisosAdmin]);

        }else{
            return redirect('inicio')->with('error','No tiene configurado, un punto de emisión o un rango de documentos, configueros y vuelva a intentar');
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

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function buscarByEmpleado($ide){
        return Anticipo_Empleado::AnticipoEmpleadobuscar($ide)->get();
    }
   
    public function destroy($id)
    {
        return redirect('/denegado');
    }
    public function nuevoE(){
        try{  
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();        
            return view('admin.recursosHumanos.eliminarAnticipo.index',['empleados'=>Empleado::EmpleadosAnticipos()->get(),'sucursales'=>Sucursal::sucursales()->get(),'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        }catch(\Exception $ex){
          
            return redirect('inicio')->with('error','Ocurrio un error vuelva a intentarlo');
        }
    }
    public function buscarEliminar(Request $request){
        if (isset($_POST['buscar'])){
            return $this->buscar($request);
        }
        if (isset($_POST['eliminar'])){
            return $this->eliminar($request);
        }
    } 
    public function buscar(Request $request){
        try{   
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $todo = 0;
            $count = 1;
            $datos = null;
            if ($request->get('fecha_todo') == "on"){
                $todo = 1; 
            }
            $empleado = Empleado::empleado($request->get('empleadoID'))->first();
            $datos[$count]['cod'] = '';
            $datos[$count]['ben'] = $empleado->empleado_nombre; 
            $datos[$count]['mon'] = '';
            $datos[$count]['pag'] = '';
            $datos[$count]['sal'] = ''; 
            $datos[$count]['fec'] = ''; 
            $datos[$count]['fep'] = ''; 
            $datos[$count]['dir'] = ''; 
            $datos[$count]['tip'] = ''; 
            $datos[$count]['fac'] = ''; 
            $datos[$count]['chk'] = '0'; 
            $datos[$count]['tot'] = '1'; 
            $datos[$count]['che'] = '0'; 
            $count ++;
            foreach(Anticipo_Empleado::AntProByFec($request->get('fecha_desde'),$request->get('fecha_hasta'),$empleado->empleado_id, $request->get('sucursal_id'),$todo)->get() as $anticipo){
                $datos[$count]['cod'] = $anticipo->anticipo_id;
                $datos[$count]['ben'] = ''; 
                $datos[$count]['mon'] = $anticipo->anticipo_valor; 
                $datos[$count]['pag'] = '';
                $datos[$count]['sal'] = $anticipo->anticipo_saldo;
                $datos[$count]['fec'] = $anticipo->anticipo_fecha; 
                $datos[$count]['fep'] = ''; 
                $datos[$count]['dir'] = $anticipo->diario->diario_codigo; 
                $datos[$count]['tip'] = $anticipo->anticipo_tipo.' - '.$anticipo->anticipo_documento; 
                $datos[$count]['fac'] = ''; 
                $datos[$count]['chk'] = '0'; 
                $datos[$count]['tot'] = '2'; 
                $datos[$count]['che'] = '0'; 
                foreach($anticipo->diario->detalles as $detalle){
                    if(isset($detalle->cheque->cheque_id)){
                        $datos[$count]['che'] = 'Cheque '.$detalle->cheque->cheque_numero.' del banco '.$detalle->cheque->cuentaBancaria->banco->bancoLista->banco_lista_nombre.' de la cuenta '.$detalle->cheque->cuentaBancaria->cuenta_bancaria_numero;
                    }
                }
                $count ++;
                foreach(Descuento_Anticipo_Empleado::DescuentosByAnticipo($anticipo->anticipo_id)->select('descuento_anticipo_empleado.descuento_id','descuento_valor','descuento_fecha','descuento_anticipo_empleado.diario_id','descuento_anticipo_empleado.cabecera_rol_cm_id','descuento_anticipo_empleado.cabecera_rol_id')->get() as $descuento){
                    $datos[$count]['cod'] = $descuento->descuento_id;
                    $datos[$count]['ben'] = ''; 
                    $datos[$count]['mon'] = ''; 
                    $datos[$count]['sal'] = ''; 
                    $datos[$count]['fec'] = ''; 
                    $datos[$count]['pag'] = $descuento->descuento_valor;
                    $datos[$count]['fep'] = $descuento->descuento_fecha; 
                    $datos[$count]['dir'] = $descuento->diario->diario_codigo; 
                    $datos[$count]['tip'] = ''; 
                    $datos[$count]['fac'] = ''; 
                    if($descuento->cabecera_rol_id!=null){
                        $datos[$count]['fac'] = $descuento->rol->cabecera_rol_fecha; 
                    }
                    if($descuento->cabecera_rol_cm_id!=null){
                        $datos[$count]['fac'] = $descuento->rolcm->cabecera_rol_fecha; 
                    }
                    
                    $datos[$count]['chk'] = '1'; 
                    $datos[$count]['tot'] = '3';
                    $datos[$count]['che'] = '0';  
                    $count ++;
                }
                if( $datos[$count-1]['tot'] == '2' ){
                    $datos[$count-1]['chk'] = '1'; 
                }
            }
            return view('admin.recursosHumanos.eliminarAnticipo.index',['sucurslaC'=>$request->get('sucursal_id'),'empleadoC'=>$request->get('empleadoID'),'fecI'=>$request->get('fecha_desde'),'fecF'=>$request->get('fecha_hasta'),'todo'=>$todo,'datos'=>$datos,'empleados'=>Empleado::EmpleadosAnticipos()->get(),'sucursales'=>Sucursal::sucursales()->get(),'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);      
        }catch(\Exception $ex){
            return redirect('eliminatAntEmp')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }   
    }/*aqui me quede en el eliminar */
    public function eliminar(Request $request){
        try {
            DB::beginTransaction();
            $auditoria = new generalController();  
            $noTienecaja =null;
            $jo=true;
            if($request->get('checkbox')){
                $seleccion = $request->get('checkbox');
                for ($i = 0; $i < count($seleccion); ++$i) {
                    $anticipo = Anticipo_Empleado::findOrFail($seleccion[$i]);
                    $cierre = $auditoria->cierre($anticipo->anticipo_fecha,$anticipo->diario->sucursal_id);
                    if ($cierre) {
                        return redirect('eliminatAntEmp')->with('error2','No puede realizar la operacion por que pertenece a un mes bloqueado');
                    }
                }
                for ($i = 0; $i < count($seleccion); ++$i) {
                    $anticipo = Anticipo_Empleado::findOrFail($seleccion[$i]);
                    $diario = null;
                    if(isset($anticipo->diario)){
                        $diario = $anticipo->diario;
                        if($anticipo->anticipo_tipo == 'Efectivo'){
                            $cajaAbierta=Arqueo_Caja::ArqueoCajaxid($anticipo->arqueo_id)->first();
                            if(isset($cajaAbierta->arqueo_id)){
                                $movimientoCaja = Movimiento_Caja::MovimientoCajaxarqueo($anticipo->arqueo_id, $anticipo->diario_id)->first();
                                $movimientoCaja->delete();
                                $jo=true;
                            }else{
                                $cajaAbierta=Arqueo_Caja::arqueoCajaxuser(Auth::user()->user_id)->first();
                                 if ($cajaAbierta){
                                    /**********************movimiento caja****************************/
                                    $movimientoCaja = new Movimiento_Caja();          
                                    $movimientoCaja->movimiento_fecha=date("Y")."-".date("m")."-".date("d");
                                    $movimientoCaja->movimiento_hora=date("H:i:s");
                                    $movimientoCaja->movimiento_tipo="ENTRADA";
                                    $movimientoCaja->movimiento_descripcion= 'P/R ELIMINACION DE ANTICIPO DE PROVEEDOR :'.$anticipo->anticipo_motivo;
                                    $movimientoCaja->movimiento_valor= $anticipo->anticipo_valor;
                                    $movimientoCaja->movimiento_documento="P/R ELIMINACION DE ANTICIPO DE PROVEEDOR";
                                    $movimientoCaja->movimiento_numero_documento= $anticipo->anticipo_numero;
                                    $movimientoCaja->movimiento_estado = 1;
                                    $movimientoCaja->arqueo_id = $cajaAbierta->arqueo_id;                                
                                    $movimientoCaja->save();
                                    
                                    $movimientoAnterior = Movimiento_Caja::MovimientoCajaxarqueo($anticipo->arqueo_id,$anticipo->diario_id)->first();
                                    $movimientoAnterior->diario_id = null;
                                    $movimientoAnterior->update();

                                    $jo=true;
                                     }else{
                                    $noTienecaja = 'Lo valores en Efectivo no pudieron ser eliminados, porque no dispone de CAJA ABIERTA';                               
                                }
                            }
                        }else{
                            $jo=true;
                        }

                    }
                    if($jo){
                        $auditoria->registrarAuditoria('Eliminacion de anticipo de empleado  '.$anticipo->empleado->empleado_nombre,'',''); 
                        $anticipo->delete();
                        if(!is_null($diario)){
                            foreach($diario->detalles as $detalle){
                                if(isset($detalle->cheque)){
                                    $auxC =$detalle->cheque;
                                }
                                if(isset($detalle->transferencia)){
                                    $auxT=$detalle->transferencia;
                                }
                                $detalle->delete();
                                $auditoria->registrarAuditoria('Eliminacion del detalle diario  N°'.$diario->diario_codigo,$diario->diario_codigo,'Eliminacion de detalle de diario por eliminacion de anticipo');  
                                if(isset($auxT)){
                                    $auxT->delete();
                                    $auditoria->registrarAuditoria('Eliminacion de transferencia a proveedor '.$auxT->transferencia_beneficiario,'','Eliminacion de transferencia a proveedor '.$auxT->transferencia_beneficiario.' por un valor de '.$auxT->transferencia_valor.' por eliminacion de anticipo de proveedor');  
                                }
                                if(isset($auxC)){
                                    if($request->get('anularChequeID') == 'no'){
                                        $auxC->delete();
                                        $auditoria->registrarAuditoria('Eliminacion de cheque','','Eliminacion de cheque numero '.$auxC->cheque_numero.' por un valor de '.$auxC->cheque_valor.' por eliminacion de anticipo de proveedor');
                                    }else{
                                        $auxC->cheque_estado = '2';
                                        $auxC->update();
                                        $auditoria->registrarAuditoria('Anulacion de cheque','','Anulacion de cheque numero '.$auxC->cheque_numero.' por un valor de '.$auxC->cheque_valor.' por eliminacion de anticipo de empleado');
                                    }                                      
                                }
                            }
                            $diario->delete();
                            $auditoria->registrarAuditoria('Eliminacion de diario  N°'.$diario->diario_codigo,$diario->diario_codigo,'Eliminacion de diario por eliminacion de anticipo');  
                        }
                    }
                }
            }
            if($request->get('checkbox2')){
                $seleccion2 = $request->get('checkbox2');
                for ($i = 0; $i < count($seleccion2); ++$i) {
                    $descuento =  Descuento_Anticipo_Empleado::findOrFail($seleccion2[$i]);
                    $anticipo = Anticipo_Empleado::findOrFail($descuento->anticipo_id);
                    $cierre = $auditoria->cierre($descuento->descuento_fecha,$descuento->diario->sucursal_id);                   
                    if($cierre){
                        return redirect('eliminatAntEmp')->with('error2','No puede realizar la operacion por que pertenece a un mes bloqueado');
                    }
                    $cierre = $auditoria->cierre($anticipo->anticipo_fecha,$anticipo->diario->sucursal_id);    
                    if($cierre){
                        return redirect('eliminatAntEmp')->with('error2','No puede realizar la operacion por que pertenece a un mes bloqueado');
                    } 
                }
                for ($i = 0; $i < count($seleccion2); ++$i) {
                    $descuento =  Descuento_Anticipo_Empleado::findOrFail($seleccion2[$i]);
                    $valorDescuento = $descuento->descuento_valor;
                    $anticipo = Anticipo_Empleado::findOrFail($descuento->anticipo_id);
                    
                  
                    $diario = null;
                    
                    
                    if(isset($descuento->diario->diario_id)){
                        $diario = $descuento->diario;
                    }
                    foreach($diario->detalles as $detalle){
                        $detalle->delete();
                        $auditoria->registrarAuditoria('Eliminacion del detalle diario  N°'.$diario->diario_codigo,$diario->diario_codigo,'Eliminacion de detalle de diario por eliminacion de cruce de anticipo con cuentas por pagar');  
                    }
                    $descuento->delete();
                    $auditoria->registrarAuditoria('Eliminacion de anticipo empleado','0','Empleado '.$descuento->anticipo->empleado->empleado_nombre.' con descripcion -> '.$descuento->descuento_descripcion);
                    $diario->delete();
                    $auditoria->registrarAuditoria('Eliminacion de dairio de anticipo empleado','0','Empleado '.$descuento->anticipo->empleado->empleado_nombre.' con diario -> '.$descuento->diario->diario_codigo);
                    $aux = Anticipo_Empleado::findOrFail($descuento->anticipo_id);
                    if(is_null($aux->anticipo_documento)){
                        $aux->anticipo_saldo = $anticipo->anticipo_saldo + $valorDescuento;
                    }else{
                        $aux->anticipo_saldo = $anticipo->anticipo_valor-round(Anticipo_Empleado::AnticipoEmpleadoDescuentos($aux->anticipo_id)->sum('descuento_valor'),2);
                    }
                    if($aux->anticipo_saldo == 0){
                        $aux->anticipo_estado = '2';
                    }else{
                        $aux->anticipo_estado = '1';
                    }
                    $anticipo->update();
                    $auditoria->registrarAuditoria('Actualizacion de anticipo empleado','0','Actualizacion de eliminacion de cruce de anticipos de empleado -> '.$descuento->anticipo->empleado->empleado_nombre.' con descripcion -> '.$descuento->descuento_descripcion);
                
                }
            }
            DB::commit();
            if(isset($noTienecaja)){
                return redirect('eliminatAntEmp')->with('success','Datos eliminados exitosamente')->with('error2',$noTienecaja);
            }else{
                return redirect('eliminatAntEmp')->with('success','Datos eliminados exitosamente');
            }
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('eliminatAntEmp')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
}
