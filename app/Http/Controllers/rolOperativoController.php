<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Alimentacion;
use App\Models\Anticipo_Empleado;
use App\Models\Banco;
use App\Models\Cabecera_Rol_CM;
use App\Models\Categoria_Producto;
use App\Models\Centro_Consumo;
use App\Models\Cheque;
use App\Models\Control_Dia;
use App\Models\Cuenta_Bancaria;
use App\Models\Descuento_Anticipo_Empleado;
use App\Models\Detalle_Diario;
use App\Models\Detalle_Rol;
use App\Models\Diario;
use App\Models\Empleado;
use App\Models\Empresa;
use App\Models\Punto_Emision;
use App\Models\Quincena;
use App\Models\Rango_Documento;
use App\Models\Rol_Consolidado;
use App\Models\Transferencia;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Luecano\NumeroALetras\NumeroALetras;
use PDF;
class rolOperativoController extends Controller
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
            $empleados=Control_Dia::Empleados()->get();
            return view('admin.recursosHumanos.rolOperativo.index',['consumo'=>Centro_Consumo::CentroConsumos()->get(),'categoria'=>Categoria_Producto::Categorias()->get(),'empleados'=>$empleados,'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
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
            $rangoDocumento=Rango_Documento::PuntoRango($id, 'Rol')->first();
            if($rangoDocumento){
                $empleados=Control_Dia::EmpleadosSucursal($rangoDocumento->puntoEmision->sucursal_id)->get();
                return view('admin.recursosHumanos.rolOperativo.index',['rangoDocumento'=>$rangoDocumento,'consumo'=>Centro_Consumo::CentroConsumos()->get(),'categoria'=>Categoria_Producto::Categorias()->get(),'empleados'=>$empleados,'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
 
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
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function cambiocheque(Request $request)
    {

        try {
            DB::beginTransaction();
            $urlcheque = '';
            $iddetalle=$request->get('iddetalle');
            $idcheque = floatval($request->get('idcheque'));
            $cheque=Cheque::findOrFail($idcheque);
            $detalle=Detalle_Diario::findOrFail($iddetalle);
            $general = new generalController();
           
            $cierre = $general->cierre($cheque->cheque_fecha_emision,$detalle->diario->sucursal_id);
            if ($cierre) {
                return redirect('listaroles')->with('error2', 'No puede realizar la operacion por que pertenece a un mes bloqueado');
            }
           
           
            
            $detalle->cheque_id=null;
            $detalle->save();

            $cheque->cheque_estado = '2';
            $cheque->save();
            
            $chequenew = new Cheque();
            $chequenew->cheque_numero = $request->get('idNewcheque');
            $chequenew->cheque_descripcion = $cheque->cheque_descripcion;
            $chequenew->cheque_beneficiario = $cheque->cheque_beneficiario;
            $chequenew->cheque_fecha_emision = $cheque->cheque_fecha_emision;
            $chequenew->cheque_fecha_pago =$cheque->cheque_fecha_pago;
            $chequenew->cheque_valor = $cheque->cheque_valor;
            $chequenew->cheque_valor_letras = $cheque->cheque_valor_letras;
            $chequenew->cuenta_bancaria_id = $cheque->cuenta_bancaria_id;
            $chequenew->cheque_estado = '1';
            $chequenew->empresa_id = Auth::user()->empresa->empresa_id;
            $chequenew->save();
            $general->registrarAuditoria('Registro de Cheque numero: -> '.$request->get('idNewcheque'), '0', 'Por motivo de: -> '.$request->get('descripcion').' con el valor de: -> '.$chequenew->cheque_valor);
            $detalle->cheque()->associate($chequenew);
        
            $detalle->save();

            $diario=Diario::findOrFail($detalle->diario_id);
            $diario->diario_numero_documento=$request->get('idNewcheque');
            $diario->save();
            
            $urlcheque = $general->pdfImprimeCheque($cheque->cuenta_bancaria_id,$chequenew);
            
            DB::commit();
            return redirect('/listaroles')->with('success','Datos guardados exitosamente')->with('cheque',$urlcheque);
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('/listaroles')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function store(Request $request)
    {
        try{
            DB::beginTransaction();
            $urlcheque = '';
            $idadelantos=$request->get('check');
            $quincena = floatval($request->get('vquincena'));
            $iddescuento=($request->get('IDE'));
            $valorchek=($request->get('checkali'));
            
            $idalimentacion=($request->get('IDEali'));
    
            $idempleado=$request->get('idempleado');
            $descontar=($request->get('TDescont'));
            $Dtercero = $request->get('TTercero');
            $Dcuarto = $request->get('TCuarto');
            $total=$request->get('Liquidacion');

         

            $fondoreserva = $request->get('TFondo'); 
            $tdias = $request->get('Totaldias');
            $sueldo =$request->get('VEmpelado');
            $vdia =$request->get('VDia');
    
            $cosecha =$request->get('VTCosecha');

         
            $vacacion =$request->get('vvacacion');
            $IECE=$request->get('IECE');
            $Dcuartoacu = $request->get('Cuarto');
            $Dterceroacu = $request->get('Tercero');
    
            $Dingreso = $request->get('TIngresos');
            $transporte = $request->get('Viaticos');
            $Degreso = $request->get('TEgresos');
            $Iesspatronal=$request->get('Patronal');
            $Iess=$request->get('Iess');
            $Iessasu=$request->get('Tasumido');
            $Dotrosegre=$request->get('Otros_Eg');
            $permiso=$request->get('VPermiso');
            $dpermiso=$request->get('Permiso'); 
            $ppermiso=$request->get('idPerpor'); 
    
            $Dppqq=$request->get('Pre_Qui');
            $Dalimentacion=$request->get('alimentacion');
            $anticipos=$request->get('adelanto');
            $Dotrosin=$request->get('Otr_In');
            $Dextras=$request->get('TExtras');
            $ext_Sal=$request->get('Contr_Sol');
            $Imp_Rent=$request->get('TRentaV');

            $horasextras=floatval($cosecha)+floatval($Dextras)+floatval($Dotrosin);

            $cuentabanca=0;
            $banco=0;
            if ($request->get('tipo') == 'Cheque') {
                $cuenta_id=$request->get('ncuenta_cheque');
                $cuentabanca=$request->get('cuenta_id_cheque');
                $banco=Banco::findOrFail($request->get('banco_id_cheque'));
            }
            if ($request->get('tipo') == 'Transferencia') {
                $cuenta_id=$request->get('ncuenta_transfer');
                $cuentabanca=$request->get('cuenta_id_transfer');
                $banco=Banco::findOrFail($request->get('banco_id_transfer'));
            }
            $cuentabancaria=Cuenta_Bancaria::findOrFail($cuentabanca);
            $general = new generalController();
           
            $tipo=Empleado::EmpleadoBusquedaCuenta($idempleado, 'sueldos')->first();
           // return((floatval($request->get('DiaN'))+floatval($horasextras)+floatval($fondoreserva))-(floatval($Dtercero)+floatval($Dcuarto)+floatval($Dppqq)+floatval($anticipos)+floatval($Iess)));
          
            $cierre = $general->cierre($request->get('fechafinal'),$tipo->sucursal_id);          
            if($cierre){
                return redirect('roloperativo')->with('error2','No puede realizar la operacion por que pertenece a un mes bloqueado');
            }
            $cierre = $general->cierre($request->get('fecha'),$tipo->sucursal_id);          
            if($cierre){
                return redirect('roloperativo')->with('error2','No puede realizar la operacion por que pertenece a un mes bloqueado');
            }
           
                    $cabecera_rol = new Rol_Consolidado();
                    $cabecera_rol->cabecera_rol_fecha = $request->get('fechafinal');
                    $cabecera_rol->cabecera_rol_total_dias = $request->get('Totaldias');
                    $cabecera_rol->cabecera_rol_total_ingresos = $request->get('TIngresos');
                    $cabecera_rol->cabecera_rol_total_anticipos =$request->get('adelanto');
                    $cabecera_rol->cabecera_rol_total_egresos =$request->get('TEgresos');
                    $cabecera_rol->cabecera_rol_sueldo = $request->get('DiaN');
                    $cabecera_rol->cabecera_rol_pago = $request->get('Liquidacion');
                    $cabecera_rol->cabecera_rol_fr_acumula = $request->get('Fondo');
                    $cabecera_rol->cabecera_rol_iesspersonal = $request->get('Personal');
                    $cabecera_rol->cabecera_rol_iesspatronal = $request->get('Patronal');
                    $cabecera_rol->cabecera_rol_tipo = 'OPERATIVO';
                    $cabecera_rol->empleado_id =$request->get('idempleado');
                    $cabecera_rol->cabecera_rol_estado = 1;
                    
    
                    
                    
                    $empleado=Empleado::Empleado($idempleado)->get()->first();
    
                   
    
                    $general = new generalController();
                    $diario = new Diario();
                    $diario->diario_codigo = $general->generarCodigoDiario($request->get('fechafinal'), 'CPRP');
                    $diario->diario_fecha = $request->get('fechafinal');
                    $diario->diario_referencia = 'COMPROBANTE DE PAGO DE ROL DE EMPLEADO';
                    $diario->diario_tipo_documento = 'ROL OPERATIVO';
                    $diario->diario_tipo = 'CPRP';
                    $diario->diario_secuencial = substr($diario->diario_codigo, 8);
                    $diario->diario_mes = DateTime::createFromFormat('Y-m-d', $request->get('fechafinal'))->format('m');
                    $diario->diario_ano = DateTime::createFromFormat('Y-m-d', $request->get('fechafinal'))->format('Y');
                    $diario->diario_comentario = 'COMPROBANTE DE PAGO DE ROL DE EMPLEADO: '.$empleado->empleado_nombre.' Con el sueldo de: '.$cabecera_rol->cabecera_rol_sueldo;
                    if ($request->get('tipo') == 'Transferencia') {
                        $diario->diario_tipo_documento = 'TRANSFERENCIA';
                        $diario->diario_numero_documento =0;
                    }
                    if ($request->get('tipo') == 'Cheque') {
                        $diario->diario_tipo_documento = 'CHEQUE';
                        $diario->diario_numero_documento =$request->get('idNcheque');
                    }
                    $diario->diario_beneficiario =$empleado->empleado_nombre;
                    $diario->diario_cierre = '0';
                    $diario->diario_estado = '1';
                    
                    $diario->empresa_id = Auth::user()->empresa_id;
                    $diario->sucursal_id =  $tipo->sucursal_id;
                    $diario->save();
                    $general->registrarAuditoria('Registro de diario de rol Operativo de Empleado -> '.$idempleado, '0', 'Con motivo:'. $cabecera_rol->cabecera_rol_descripcion);
                    $cabecera_rol->diariopago()->associate($diario); 
                    
                    
                    $diariocontabilizado = new Diario();
                    $diariocontabilizado->diario_codigo = $general->generarCodigoDiario($request->get('fechafinal'), 'CCMR');
                    $diariocontabilizado->diario_fecha = $request->get('fechafinal');
                    $diariocontabilizado->diario_referencia = 'COMPROBANTE DE CONTABILIZACION MENSUAL DE ROLES';
                    $diariocontabilizado->diario_tipo_documento = 'CONTABILIZACION MENSUAL';
                    $diariocontabilizado->diario_tipo = 'CCMR';
                    $diariocontabilizado->diario_secuencial = substr($diario->diario_codigo, 8);
                    $diariocontabilizado->diario_mes = DateTime::createFromFormat('Y-m-d', $request->get('fechafinal'))->format('m');
                    $diariocontabilizado->diario_ano = DateTime::createFromFormat('Y-m-d', $request->get('fechafinal'))->format('Y');
                    $diariocontabilizado->diario_comentario = 'COMPROBANTE DE CONTABILIZACION MENSUAL DE ROLES: '.$empleado->empleado_nombre.' Con el sueldo de: '.$cabecera_rol->cabecera_rol_sueldo;
                    $diariocontabilizado->diario_numero_documento = 0;
                    $diariocontabilizado->diario_beneficiario =$empleado->empleado_nombre;
                    $diariocontabilizado->diario_cierre = '0';
                    $diariocontabilizado->diario_estado = '1';
                    
                    $diariocontabilizado->empresa_id = Auth::user()->empresa_id;
                    $diariocontabilizado->sucursal_id =  $tipo->sucursal_id;
                    $diariocontabilizado->save();
                    $general->registrarAuditoria('Registro de diario de rol consolidado de Empleado -> '.$request->get('idEmpleado'), '0', 'Con motivo:'. $cabecera_rol->cabecera_rol_descripcion);
                    $cabecera_rol->diariocontabilizacion()->associate($diariocontabilizado); 
    
    
                    
                    
                    
                    
                    $cabecera_rol->save();
                    $general->registrarAuditoria('Registro de Rol Operativo de Empleado -> '.$empleado->empleado_nombre, '0', 'Con motivo:'. $cabecera_rol->cabecera_rol_descripcion);
    
                    $controldia=Control_Dia::findOrFail($request->get('idControldia'));
                    $controldia->control_estado='2';
                    $controldia->rol()->associate($cabecera_rol);
                    $controldia->save();
                    $auditoria = new generalController();
                    $auditoria->registrarAuditoria('Actualizacion de Control dia de estado a 2 con empleado -> '.$empleado->empleado_nombre, '0', 'Por el pago de rol');
                    
                    if ($request->get('tipo') == 'Cheque') {
                        $formatter = new NumeroALetras();
                        $cheque = new Cheque();
                        $cheque->cheque_numero = $request->get('idNcheque');
                        $cheque->cheque_descripcion =  'PAGO ROL DE EMPLEADO '.$empleado->empleado_nombre.' CON MES Y AÑO '.date('Y', strtotime($request->get('fechafinal'))).'/'.date('F', strtotime($request->get('fechafinal')));
                        $cheque->cheque_beneficiario = $empleado->empleado_nombre;
                        $cheque->cheque_fecha_emision = $request->get('fechafinal');
                        $cheque->cheque_fecha_pago = $request->get('idFechaCheque');
                        $cheque->cheque_valor = $cabecera_rol->cabecera_rol_pago;
                        $cheque->cheque_valor_letras = $formatter->toInvoice($cheque->cheque_valor, 2, 'Dolares');
                        $cheque->cuenta_bancaria_id = $request->get('cuenta_id_cheque');
                        $cheque->cheque_estado = '1';
                        $cheque->empresa_id = Auth::user()->empresa->empresa_id;
                        $cheque->save();
                        $urlcheque = $general->pdfImprimeCheque($request->get('cuenta_id_cheque'),$cheque);
                        $general->registrarAuditoria('Registro de Cheque pago de rol  numero: -> '.$request->get('idNcheque'), '0', 'Por motivo de: -> '. $cabecera_rol->cabecera_rol_descripcion.' con el valor de: -> '.$total);
                    }
                    
                    if ($request->get('tipo') == 'Transferencia') {
                        $transferencia = new Transferencia();
                        $transferencia->transferencia_descripcion = 'PAGO ROL DE EMPLEADO '.$empleado->empleado_nombre.' CON MES Y AÑO '.date('Y', strtotime($request->get('fechafinal'))).'/'.date('F', strtotime($request->get('fechafinal')));
                        $transferencia->transferencia_beneficiario = $empleado->empleado_nombre;
                        $transferencia->transferencia_fecha = $request->get('fechafinal');
                        $transferencia->transferencia_valor = $cabecera_rol->cabecera_rol_pago;
                        $transferencia->cuenta_bancaria_id = $request->get('cuenta_id_transfer');
                        $transferencia->transferencia_estado = '1';
                        $transferencia->empresa_id = Auth::user()->empresa->empresa_id;
                        $transferencia->save();
                        $general->registrarAuditoria('Registro de Transferencia pago de rol  numero: -> '.$request->get('ncuenta'), '0', 'Por motivo de: -> '. $cabecera_rol->cabecera_rol_descripcion.' con el valor de: -> '.$total);
                    
                    }
                   
                    $detalleDiario = new Detalle_Diario();
                    $detalleDiario->detalle_debe = 0.00;
                    $detalleDiario->detalle_haber = (floatval($request->get('DiaN'))+floatval($horasextras)+floatval($fondoreserva)+floatval($transporte)+floatval($Dalimentacion))-(floatval($Dtercero)+floatval($Dcuarto)+floatval($Dppqq)+floatval($anticipos)+floatval($quincena)+floatval($Iess));
                    if ($request->get('tipo') == 'Cheque') {
                        $detalleDiario->detalle_comentario =  $banco->bancoLista->banco_lista_nombre.' Con Cuenta # '.$cuentabancaria->cuenta_bancaria_numero.' Con Cheque #'.$request->get('idNcheque');
                    }
                    if ($request->get('tipo') == 'Transferencia') {
                        $detalleDiario->detalle_comentario = 'Transferencia a la cuenta bancaria N° '.$request->get('ncuenta');
                    }
                    $detalleDiario->detalle_tipo_documento = 'ROL OPERATIVO';
                    $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                    $detalleDiario->detalle_conciliacion = '0';
                    $detalleDiario->detalle_estado = '1';
                    $detalleDiario->cuenta_id =  $cuenta_id;
                    if ($request->get('tipo') == 'Cheque'){      
                        $detalleDiario->cheque()->associate($cheque);
                    }
                    if ($request->get('tipo') == 'Transferencia'){      
                        $detalleDiario->transferencia()->associate($transferencia);
                    }
                    $diario->detalles()->save($detalleDiario);
                    $general->registrarAuditoria('Registro de Detalle de Diario codigo: -> '.$diario->diario_codigo, '0', 'En la cuenta del Haber -> '.$request->get('idCuentaContable').' con el valor de: -> '.$total);
                
                
                    
                    $tipo=Empleado::EmpleadoBusquedaCuenta($idempleado, 'sueldos')->first();
                    $detalleDiario = new Detalle_Diario();
                    $detalleDiario->detalle_debe =  (floatval($request->get('DiaN'))+floatval($horasextras)+floatval($fondoreserva)+floatval($transporte)+floatval($Dalimentacion))-(floatval($Dtercero)+floatval($Dcuarto)+floatval($Dppqq)+floatval($anticipos)+floatval($quincena)+floatval($Iess));
                    $detalleDiario->detalle_haber =0.00;
                    $detalleDiario->detalle_comentario = 'Pago del Rol del '.DateTime::createFromFormat('Y-m-d',$request->get('fecha'))->format('d-m-Y').' al '.DateTime::createFromFormat('Y-m-d',$request->get('fechafinal'))->format('d-m-Y');
                    $detalleDiario->detalle_tipo_documento = 'ROL OPERATIVO';
                    $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                    $detalleDiario->detalle_conciliacion = '0';
                    $detalleDiario->detalle_estado = '1';
                    $detalleDiario->cuenta_id = $tipo->cuenta_debe;
                    $detalleDiario->empleado_id = $idempleado;
                    $diario->detalles()->save($detalleDiario);
                    $general->registrarAuditoria('Registro de Detalle de Diario codigo: -> '.$diario->diario_codigo, '0', 'En la cuenta del Haber -> '.$request->get('idCuentaContable').' con el valor de: -> '.$total);
                    
                       
                     
                    
    
                        if (isset($valorchek)){
                            for ($j = 0; $j < count($valorchek); ++$j) {
                            $alimentar=Alimentacion::findOrFail($idalimentacion[$valorchek[$j]]);
                            $alimentar->alimentacion_estado='2';
                            $alimentar->rol()->associate($cabecera_rol);
                            $alimentar->save();
                            $auditoria = new generalController();
                            $auditoria->registrarAuditoria('Actualizacion de Alimentacion de estado a 2 con empleado -> '.$empleado->empleado_nombre, '0', 'Por el pago de rol');
                        
                            }
                        }
                        if ($quincena>0) {
                            $quincenas_rol=Quincena::findOrFail($request->get('idquincena'));
                            $quincenas_rol->quincena_estado='2';
                            $quincenas_rol->rol()->associate($cabecera_rol);
                            $quincenas_rol->save();
                            $auditoria = new generalController();
                            $auditoria->registrarAuditoria('Actualizacion de Quincena de estado a 2 con empleado -> '.$empleado->empleado_nombre, '0', 'Por el pago de rol');
                        }
                        if (isset($idadelantos)) {
                            for ($j = 0; $j < count($idadelantos); ++$j) {
                                $anticipo=Anticipo_Empleado::findOrFail($iddescuento[$idadelantos[$j]]);
                    
                                $anticipodescuento=new Descuento_Anticipo_Empleado();
                                $anticipodescuento->descuento_fecha=$request->get('fechafinal');
                                $anticipodescuento->descuento_descripcion='Descuento de anticipo en Rol';
                                $anticipodescuento->descuento_valor=$descontar[$idadelantos[$j]];
                                $anticipodescuento->descuento_estado='1';
                                $anticipodescuento->rol()->associate($cabecera_rol);
                                $anticipodescuento->anticipo()->associate($anticipo);
                                $anticipodescuento->diario()->associate($diario);
                                $anticipodescuento->save();
                    
                                $auditoria = new generalController();
                                $auditoria->registrarAuditoria('Resgitro del descuento del Anticipo de empleado-> '.$request->get('idEmpleado'), '0', 'Por el pago de rol');
                                $anticipo->anticipo_saldo=$anticipo->anticipo_valor-(Descuento_Anticipo_Empleado::Anticipos($iddescuento[$idadelantos[$j]])->sum('descuento_valor'));
                                if ($anticipo->anticipo_saldo=='0') {
                                    $anticipo->anticipo_estado='2';
                                }
                                $anticipo->update();
                                $auditoria = new generalController();
                                $auditoria->registrarAuditoria('Actualizacion del Anticipo de estado a 2 con empleado-> '.$empleado->empleado_nombre, '0', 'Por el pago de rol');
                            }
                        }
                        
                       
                        
                        $detalle= new Detalle_Rol();
                        $detalle->detalle_rol_fecha_inicio = $request->get('fecha');
                        $detalle->detalle_rol_fecha_fin = $request->get('fechafinal');
                        $detalle->detalle_rol_sueldo = floatval($sueldo);
                        
                        $detalle->detalle_rol_dias = $tdias;
                        $detalle->detalle_rol_valor_dia = floatval($vdia);
                        $detalle->detalle_rol_total_dias = floatval($request->get('DiaN'));
                        $detalle->detalle_rol_horas_extras = 0;
                        $detalle->detalle_rol_valor_he = $Dextras;
                        $detalle->detalle_rol_bonificacion_dias = 0;
                        $detalle->detalle_rol_horas_suplementarias = 0;
                        $detalle->detalle_rol_otra_bonificacion = 0;
                        $detalle->detalle_rol_otros_ingresos = $Dotrosin;
                        $detalle->detalle_rol_sueldo_rembolsable = 0;
                        $detalle->detalle_rol_fondo_reserva = floatval($fondoreserva);
                        $detalle->detalle_rol_iess = floatval($Iess);
                        $detalle->detalle_rol_multa = 0;
                        $detalle->detalle_rol_quincena = $quincena;
                        $detalle->detalle_rol_total_anticipo = $anticipos;
                        $detalle->detalle_rol_total_comisariato = $Dalimentacion;
                      
                        $detalle->detalle_rol_prestamo_quirografario = $Dppqq;
                        $detalle->detalle_rol_prestamo_hipotecario = 0;
                        $detalle->detalle_rol_prestamo = 0;
                        $detalle->detalle_rol_transporte = $transporte;
                        $detalle->detalle_rol_ext_salud =$ext_Sal;
                        $detalle->detalle_rol_impuesto_renta = $Imp_Rent;
                        $detalle->detalle_rol_ley_sol =0;
                        $detalle->detalle_rol_total_permiso = 0;
                        $detalle->detalle_rol_permiso_no_rem = 0;
                        $detalle->detalle_rol_porcentaje = 100;
                        $detalle->detalle_rol_otros_egresos = $Dotrosegre;
                        $detalle->detalle_rol_liquido_pagar = floatval($total)-floatval($permiso) ;
                        $detalle->detalle_rol_contabilizado = 1;
                        $detalle->detalle_rol_cosecha=$cosecha;
                        $detalle->detalle_rol_iess_asumido = floatval($Iessasu);
                        $detalle->detalle_rol_aporte_patronal = $Iesspatronal;
                        $detalle->detalle_rol_aporte_iecesecap = $IECE;
                        $detalle->detalle_rol_vacaciones = 0;
                        $detalle->detalle_rol_vacaciones_anticipadas=$vacacion;
                        $detalle->detalle_rol_decimo_cuarto = floatval($Dcuarto);
                        $detalle->detalle_rol_decimo_tercero = floatval($Dtercero);
                        $detalle->detalle_rol_decimo_cuartoacum = floatval($Dcuartoacu);
                        $detalle->detalle_rol_decimo_terceroacum = floatval($Dterceroacu);
                        $detalle->detalle_rol_total_egreso = floatval($Degreso);
                        $detalle->detalle_rol_total_ingreso = floatval($Dingreso)-floatval($permiso) ;
                        $detalle->detalle_rol_estado = 1;
                        $cabecera_rol->detalles()->save($detalle);
                        $general->registrarAuditoria('Registro de Detalle de Detalle Rol DE EMPLEADO -> '.$empleado->empleado_nombre, '0', ' con el valor de: -> '.$total);
                        
                             ///////////////////////////Ingresos///////////////////////////////
                             if ((floatval($request->get('DiaN')))>0) {
                                $detalleDiario = new Detalle_Diario();
                                $detalleDiario->detalle_debe = (floatval($request->get('DiaN')));
                                $detalleDiario->detalle_haber =0.00;
                                $detalleDiario->detalle_comentario = 'Pago del Rol del '.DateTime::createFromFormat('Y-m-d',$request->get('fecha'))->format('d-m-Y').' al '.DateTime::createFromFormat('Y-m-d',$request->get('fechafinal'))->format('d-m-Y');
                                $detalleDiario->detalle_tipo_documento = 'CONTABILIZACION MENSUAL';
                                $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                                $detalleDiario->detalle_conciliacion = '0';
                                $detalleDiario->detalle_estado = '1';
                                $tipo=Empleado::EmpleadoBusquedaCuenta($idempleado, 'sueldos')->first();
                                $detalleDiario->cuenta_id = $tipo->cuenta_debe;
                                $detalleDiario->empleado_id =  $idempleado;
                                $diariocontabilizado->detalles()->save($detalleDiario);
                                $general->registrarAuditoria('Registro de Detalle de Diario codigo: -> '.$diario->diario_codigo, '0', 'En la cuenta del Debe '.$tipo->cuenta_debe.' con el valor de: '.(floatval($request->get('DiaN'))+floatval($permiso)));
                            }
                            if ($horasextras>0) {
                                $detalleDiario = new Detalle_Diario();
                                $detalleDiario->detalle_debe = floatval($horasextras) ;
                                $detalleDiario->detalle_haber =0.00;
                                $detalleDiario->detalle_comentario = 'Pago del Rol del '.DateTime::createFromFormat('Y-m-d',$request->get('fecha'))->format('d-m-Y').' al '.DateTime::createFromFormat('Y-m-d',$request->get('fechafinal'))->format('d-m-Y');
                                $detalleDiario->detalle_tipo_documento = 'CONTABILIZACION MENSUAL';
                                $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                                $detalleDiario->detalle_conciliacion = '0';
                                $detalleDiario->detalle_estado = '1';
                                $tipo=Empleado::EmpleadoBusquedaCuenta($idempleado, 'horasExtras')->first();
                                $detalleDiario->cuenta_id = $tipo->cuenta_debe;
                                $detalleDiario->empleado_id =  $idempleado;
                                $diariocontabilizado->detalles()->save($detalleDiario);
                                $general->registrarAuditoria('Registro de Detalle de Diario codigo: -> '.$diario->diario_codigo, '0', 'En la cuenta del Debe '.$tipo->cuenta_debe.' con el valor de: '.$Dotrosin);
                            }
                            if (floatval($Dalimentacion)>0) {
                                $detalleDiario = new Detalle_Diario();
                                $detalleDiario->detalle_debe = floatval($Dalimentacion);
                                $detalleDiario->detalle_haber = 0.00;
                                $detalleDiario->detalle_comentario =  'Pago del Rol del '.DateTime::createFromFormat('Y-m-d',$request->get('fecha'))->format('d-m-Y').' al '.DateTime::createFromFormat('Y-m-d',$request->get('fechafinal'))->format('d-m-Y');
                                $detalleDiario->detalle_tipo_documento = 'CONTABILIZACION MENSUAL';
                                $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                                $detalleDiario->detalle_conciliacion = '0';
                                $detalleDiario->detalle_estado = '1';
                                $tipo=Empleado::EmpleadoBusquedaCuenta($idempleado, 'comisariato')->first();
                                $detalleDiario->cuenta_id = $tipo->cuenta_debe;
                                $detalleDiario->empleado_id = $idempleado;
                                $diariocontabilizado->detalles()->save($detalleDiario);
                                $general->registrarAuditoria('Registro de Detalle de Diario codigo: -> '.$diario->diario_codigo, '0', 'En la cuenta del Haber -> '.$tipo->cuenta_haber.' con el valor de: -> '.$Dalimentacion);
                            }
                          

                            if (floatval($transporte)>0) {
                                $detalleDiario = new Detalle_Diario();
                                $detalleDiario->detalle_debe = floatval($transporte) ;
                                $detalleDiario->detalle_haber =0.00;
                                $detalleDiario->detalle_comentario =  'Pago del Rol del '.DateTime::createFromFormat('Y-m-d',$request->get('fecha'))->format('d-m-Y').' al '.DateTime::createFromFormat('Y-m-d',$request->get('fechafinal'))->format('d-m-Y');
                                $detalleDiario->detalle_tipo_documento = 'CONTABILIZACION MENSUAL';
                                $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                                $detalleDiario->detalle_conciliacion = '0';
                                $detalleDiario->detalle_estado = '1';
                                $tipo=Empleado::EmpleadoBusquedaCuenta($idempleado, 'viaticos')->first();
                                $detalleDiario->cuenta_id = $tipo->cuenta_debe;
                                $detalleDiario->empleado_id =  $idempleado;
                                $diariocontabilizado->detalles()->save($detalleDiario);
                                $general->registrarAuditoria('Registro de Detalle de Diario codigo: -> '.$diario->diario_codigo, '0', 'En la cuenta del Debe '.$tipo->cuenta_debe.' con el valor de: '.$transporte);
                            }
                            
                            if (floatval($Dtercero)>0) {
                                $detalleDiario = new Detalle_Diario();
                                $detalleDiario->detalle_debe = floatval($Dtercero) ;
                                $detalleDiario->detalle_haber =0.00;
                                $detalleDiario->detalle_comentario =  'Pago del Rol del '.DateTime::createFromFormat('Y-m-d',$request->get('fecha'))->format('d-m-Y').' al '.DateTime::createFromFormat('Y-m-d',$request->get('fechafinal'))->format('d-m-Y');
                                $detalleDiario->detalle_tipo_documento = 'CONTABILIZACION MENSUAL';
                                $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                                $detalleDiario->detalle_conciliacion = '0';
                                $detalleDiario->detalle_estado = '1';
                                $tipo=Empleado::EmpleadoBusquedaCuenta($idempleado, 'decimoTercero')->first();
                                $detalleDiario->cuenta_id = $tipo->cuenta_debe;
                                $detalleDiario->empleado_id =  $idempleado;
                                $diariocontabilizado->detalles()->save($detalleDiario);
                                $general->registrarAuditoria('Registro de Detalle de Diario codigo: -> '.$diario->diario_codigo, '0', 'En la cuenta del Debe '.$tipo->cuenta_debe.' con el valor de: '.$transporte);

                            }
                            if (floatval($Dcuarto)>0) {
                                $detalleDiario = new Detalle_Diario();
                                $detalleDiario->detalle_debe = floatval($Dcuarto) ;
                                $detalleDiario->detalle_haber =0.00;
                                $detalleDiario->detalle_comentario =  'Pago del Rol del '.DateTime::createFromFormat('Y-m-d',$request->get('fecha'))->format('d-m-Y').' al '.DateTime::createFromFormat('Y-m-d',$request->get('fechafinal'))->format('d-m-Y');
                                $detalleDiario->detalle_tipo_documento = 'CONTABILIZACION MENSUAL';
                                $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                                $detalleDiario->detalle_conciliacion = '0';
                                $detalleDiario->detalle_estado = '1';
                                $tipo=Empleado::EmpleadoBusquedaCuenta($idempleado, 'decimoCuarto')->first();
                                $detalleDiario->cuenta_id = $tipo->cuenta_debe;
                                $detalleDiario->empleado_id =  $idempleado;
                                $diariocontabilizado->detalles()->save($detalleDiario);
                                $general->registrarAuditoria('Registro de Detalle de Diario codigo: -> '.$diario->diario_codigo, '0', 'En la cuenta del Debe '.$tipo->cuenta_debe.' con el valor de: '.$transporte);

                            }
                            if (floatval($fondoreserva)>0) {
                                $detalleDiario = new Detalle_Diario();
                                $detalleDiario->detalle_debe =  floatval($fondoreserva);
                                $detalleDiario->detalle_haber =0.00;
                                $detalleDiario->detalle_comentario =  'Pago del Rol del '.DateTime::createFromFormat('Y-m-d',$request->get('fecha'))->format('d-m-Y').' al '.DateTime::createFromFormat('Y-m-d',$request->get('fechafinal'))->format('d-m-Y');
                                $detalleDiario->detalle_tipo_documento = 'CONTABILIZACION MENSUAL';
                                $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                                $detalleDiario->detalle_conciliacion = '0';
                                $detalleDiario->detalle_estado = '1';
                                $tipo=Empleado::EmpleadoBusquedaCuenta($idempleado, 'fondoReserva')->first();
                                $detalleDiario->cuenta_id = $tipo->cuenta_debe;
                                $detalleDiario->empleado_id = $idempleado;
                                $diariocontabilizado->detalles()->save($detalleDiario);
                                $general->registrarAuditoria('Registro de Detalle de Diario codigo: -> '.$diario->diario_codigo, '0', 'En la cuenta del Debe -> '.$tipo->cuenta_debe.' con el valor de: -> '.$cabecera_rol->cabecera_rol_fr_acumula);
                            }
                            if((floatval($request->get('DiaN'))+floatval($horasextras)+floatval($fondoreserva)+floatval($transporte)+floatval($Dalimentacion))-(floatval($Dtercero)+floatval($Dcuarto)+floatval($Dppqq)+floatval($anticipos)+floatval($quincena)+floatval($Iess))>0){
        
                                $detalleDiario = new Detalle_Diario();
                                $detalleDiario->detalle_debe = 0.00;
                                $detalleDiario->detalle_haber = (floatval($request->get('DiaN'))+floatval($horasextras)+floatval($fondoreserva)+floatval($transporte)+floatval($Dalimentacion))-(floatval($Dtercero)+floatval($Dcuarto)+floatval($Dppqq)+floatval($anticipos)+floatval($quincena)+floatval($Iess));
                                $detalleDiario->detalle_comentario =  'Pago del Rol del '.DateTime::createFromFormat('Y-m-d',$request->get('fecha'))->format('d-m-Y').' al '.DateTime::createFromFormat('Y-m-d',$request->get('fechafinal'))->format('d-m-Y');
                               
                                $detalleDiario->detalle_tipo_documento = 'CONTABILIZACION MENSUAL';
                                $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                                $detalleDiario->detalle_conciliacion = '0';
                                $detalleDiario->detalle_estado = '1';
                                $tipo=Empleado::EmpleadoBusquedaCuenta($idempleado, 'sueldos')->first();
                                $detalleDiario->cuenta_id = $tipo->cuenta_haber;
                                $detalleDiario->empleado_id = $idempleado;
                                $diariocontabilizado->detalles()->save($detalleDiario);
                                $general->registrarAuditoria('Registro de Detalle de Diario codigo: -> '.$diario->diario_codigo, '0', 'En la cuenta del Haber -> '.$tipo->cuenta_haber.' con el valor de: -> '.$detalleDiario->detalle_haber);
        
        
                            }
                            ///////////////////////////Egresos///////////////////////////////
                           
                            if (floatval($ext_Sal)>0) {
                                $detalleDiario = new Detalle_Diario();
                                $detalleDiario->detalle_debe = floatval($ext_Sal);
                                $detalleDiario->detalle_haber = 0.00;
                                $detalleDiario->detalle_comentario =  'Pago del Rol del '.DateTime::createFromFormat('Y-m-d',$request->get('fecha'))->format('d-m-Y').' al '.DateTime::createFromFormat('Y-m-d',$request->get('fechafinal'))->format('d-m-Y');
                                $detalleDiario->detalle_tipo_documento = 'CONTABILIZACION MENSUAL';
                                $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                                $detalleDiario->detalle_conciliacion = '0';
                                $detalleDiario->detalle_estado = '1';
                                $tipo=Empleado::EmpleadoBusquedaCuenta($idempleado, 'anticipos')->first();
                                $detalleDiario->cuenta_id = $tipo->cuenta_debe;
                                $detalleDiario->empleado_id = $idempleado;
                                $diariocontabilizado->detalles()->save($detalleDiario);
                                $general->registrarAuditoria('Registro de Detalle de Diario codigo: -> '.$diario->diario_codigo, '0', 'En la cuenta del Haber -> '.$tipo->cuenta_haber.' con el valor de: -> '.$anticipos);
                   

                                $detalleDiario = new Detalle_Diario();
                                $detalleDiario->detalle_debe = floatval($ext_Sal);
                                $detalleDiario->detalle_haber = 0.00;
                                $detalleDiario->detalle_comentario =  'Pago del Rol del '.DateTime::createFromFormat('Y-m-d',$request->get('fecha'))->format('d-m-Y').' al '.DateTime::createFromFormat('Y-m-d',$request->get('fechafinal'))->format('d-m-Y');
                                $detalleDiario->detalle_tipo_documento = 'CONTABILIZACION MENSUAL';
                                $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                                $detalleDiario->detalle_conciliacion = '0';
                                $detalleDiario->detalle_estado = '1';
                                $tipo=Empleado::EmpleadoBusquedaCuenta($idempleado, 'iessPagar')->first();
                                $detalleDiario->cuenta_id = $tipo->cuenta_haber;
                                $detalleDiario->empleado_id = $idempleado;
                                $diariocontabilizado->detalles()->save($detalleDiario);
                                $general->registrarAuditoria('Registro de Detalle de Diario codigo: -> '.$diario->diario_codigo, '0', 'En la cuenta del Haber -> '.$tipo->cuenta_haber.' con el valor de: -> '.$anticipos);
                            
                            }
                            if (floatval($Dppqq)>0) {
                                $detalleDiario = new Detalle_Diario();
                                $detalleDiario->detalle_debe = 0.00;
                                $detalleDiario->detalle_haber = floatval($Dppqq);
                                $detalleDiario->detalle_comentario = 'Pago del Rol del '.DateTime::createFromFormat('Y-m-d',$request->get('fecha'))->format('d-m-Y').' al '.DateTime::createFromFormat('Y-m-d',$request->get('fechafinal'))->format('d-m-Y');
                                $detalleDiario->detalle_tipo_documento = 'CONTABILIZACION MENSUAL';
                                $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                                $detalleDiario->detalle_conciliacion = '0';
                                $detalleDiario->detalle_estado = '1';
                                $tipo=Empleado::EmpleadoBusquedaCuenta($idempleado, 'prestamosQuirografarios')->first();
                                $detalleDiario->cuenta_id = $tipo->cuenta_haber;
                                $detalleDiario->empleado_id = $idempleado;
                                $diariocontabilizado->detalles()->save($detalleDiario);
                                $general->registrarAuditoria('Registro de Detalle de Diario codigo: -> '.$diario->diario_codigo, '0', 'En la cuenta del Haber -> '.$tipo->cuenta_haber.' con el valor de: -> '.$Dppqq);
                            }
                         
                          
                            
                          
                            if (floatval($Iessasu)>0) {
                                $detalleDiario = new Detalle_Diario();
                                $detalleDiario->detalle_debe = 0.00;
                                $detalleDiario->detalle_haber = floatval($Iessasu);
                                $detalleDiario->detalle_comentario =  'Pago del Rol del '.DateTime::createFromFormat('Y-m-d',$request->get('fecha'))->format('d-m-Y').' al '.DateTime::createFromFormat('Y-m-d',$request->get('fechafinal'))->format('d-m-Y');
                                $detalleDiario->detalle_tipo_documento = 'CONTABILIZACION MENSUAL';
                                $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                                $detalleDiario->detalle_conciliacion = '0';
                                $detalleDiario->detalle_estado = '1';
                                $tipo=Empleado::EmpleadoBusquedaCuenta($idempleado, 'iessAsumido')->first();
                                $detalleDiario->cuenta_id = $tipo->cuenta_haber;
                                $detalleDiario->empleado_id = $idempleado;
                                $diariocontabilizado->detalles()->save($detalleDiario);
                                $general->registrarAuditoria('Registro de Detalle de Diario codigo: -> '.$diario->diario_codigo, '0', 'En la cuenta del Haber -> '.$tipo->cuenta_haber.' con el valor de: -> '.$Iessasu);
                            }
                            if ((floatval($anticipos)+floatval($quincena))>0) {
                                $detalleDiario = new Detalle_Diario();
                                $detalleDiario->detalle_debe = 0.00;
                                $detalleDiario->detalle_haber = (floatval($anticipos)+floatval($quincena));
                                $detalleDiario->detalle_comentario =  'Pago del Rol del '.DateTime::createFromFormat('Y-m-d',$request->get('fecha'))->format('d-m-Y').' al '.DateTime::createFromFormat('Y-m-d',$request->get('fechafinal'))->format('d-m-Y');
                                $detalleDiario->detalle_tipo_documento = 'CONTABILIZACION MENSUAL';
                                $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                                $detalleDiario->detalle_conciliacion = '0';
                                $detalleDiario->detalle_estado = '1';
                                $tipo=Empleado::EmpleadoBusquedaCuenta($idempleado, 'anticipos')->first();
                                $detalleDiario->cuenta_id = $tipo->cuenta_haber;
                                $detalleDiario->empleado_id = $idempleado;
                                $diariocontabilizado->detalles()->save($detalleDiario);
                                $general->registrarAuditoria('Registro de Detalle de Diario codigo: -> '.$diario->diario_codigo, '0', 'En la cuenta del Haber -> '.$tipo->cuenta_haber.' con el valor de: -> '.$anticipos);
                            }
                            if (floatval($Imp_Rent)>0) {
                                $detalleDiario = new Detalle_Diario();
                                $detalleDiario->detalle_debe = 0.00;
                                $detalleDiario->detalle_haber = floatval($Imp_Rent);
                                $detalleDiario->detalle_comentario =  'Pago del Rol del '.DateTime::createFromFormat('Y-m-d',$request->get('fecha'))->format('d-m-Y').' al '.DateTime::createFromFormat('Y-m-d',$request->get('fechafinal'))->format('d-m-Y');
                                $detalleDiario->detalle_tipo_documento = 'CONTABILIZACION MENSUAL';
                                $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                                $detalleDiario->detalle_conciliacion = '0';
                                $detalleDiario->detalle_estado = '1';
                                $tipo=Empleado::EmpleadoBusquedaCuenta($idempleado, 'impuestoRenta')->first();
                                $detalleDiario->cuenta_id = $tipo->cuenta_haber;
                                $detalleDiario->empleado_id = $idempleado;
                                $diariocontabilizado->detalles()->save($detalleDiario);
                                $general->registrarAuditoria('Registro de Detalle de Diario codigo: -> '.$diario->diario_codigo, '0', 'En la cuenta del Haber -> '.$tipo->cuenta_haber.' con el valor de: -> '.$Imp_Rent);
                            }
                            if (floatval($Dotrosegre)>0) {
                                $detalleDiario = new Detalle_Diario();
                                $detalleDiario->detalle_debe = 0.00;
                                $detalleDiario->detalle_haber = floatval($Dotrosegre);
                                $detalleDiario->detalle_comentario =  'Pago del Rol del '.DateTime::createFromFormat('Y-m-d',$request->get('fecha'))->format('d-m-Y').' al '.DateTime::createFromFormat('Y-m-d',$request->get('fechafinal'))->format('d-m-Y');
                                $detalleDiario->detalle_tipo_documento = 'CONTABILIZACION MENSUAL';
                                $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                                $detalleDiario->detalle_conciliacion = '0';
                                $detalleDiario->detalle_estado = '1';
                                $tipo=Empleado::EmpleadoBusquedaCuenta($idempleado, 'otrosEgresos')->first();
                                $detalleDiario->cuenta_id = $tipo->cuenta_haber;
                                $detalleDiario->empleado_id = $idempleado;
                                $diariocontabilizado->detalles()->save($detalleDiario);
                                $general->registrarAuditoria('Registro de Detalle de Diario codigo: -> '.$diario->diario_codigo, '0', 'En la cuenta del Haber -> '.$tipo->cuenta_haber.' con el valor de: -> '.$Dotrosegre);
                            }
                            if ($cabecera_rol->cabecera_rol_iesspersonal>0) {    
                                $detalleDiario = new Detalle_Diario();
                                $detalleDiario->detalle_debe = 0.00;
                                $detalleDiario->detalle_haber = $cabecera_rol->cabecera_rol_iesspersonal;
                                $detalleDiario->detalle_comentario =  'Pago del Rol del '.DateTime::createFromFormat('Y-m-d',$request->get('fecha'))->format('d-m-Y').' al '.DateTime::createFromFormat('Y-m-d',$request->get('fechafinal'))->format('d-m-Y');
                                $detalleDiario->detalle_tipo_documento = 'CONTABILIZACION MENSUAL';
                                $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                                $detalleDiario->detalle_conciliacion = '0';
                                $detalleDiario->detalle_estado = '1';
                                $tipo=Empleado::EmpleadoBusquedaCuenta($idempleado, 'aportePersonal')->first();
                                $detalleDiario->cuenta_id = $tipo->cuenta_haber;
                                $detalleDiario->empleado_id = $idempleado;
                                $diariocontabilizado->detalles()->save($detalleDiario);
                                $general->registrarAuditoria('Registro de Detalle de Diario codigo: -> '.$diario->diario_codigo, '0', 'En la cuenta del Haber -> '.$tipo->cuenta_haber.' con el valor de: -> '.$cabecera_rol->cabecera_rol_iesspatronal);
                            }
        
                           
        
        
        
                            ///////////////////////////Provisiones///////////////////////////////
                            if ($cabecera_rol->cabecera_rol_iesspatronal>0) {
                                $detalleDiario = new Detalle_Diario();
                                $detalleDiario->detalle_debe = $cabecera_rol->cabecera_rol_iesspatronal;
                                $detalleDiario->detalle_haber = 0.00;
                                $detalleDiario->detalle_comentario =  'Pago del Rol del '.DateTime::createFromFormat('Y-m-d',$request->get('fecha'))->format('d-m-Y').' al '.DateTime::createFromFormat('Y-m-d',$request->get('fechafinal'))->format('d-m-Y');
                                $detalleDiario->detalle_tipo_documento = 'CONTABILIZACION MENSUAL';
                                $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                                $detalleDiario->detalle_conciliacion = '0';
                                $detalleDiario->detalle_estado = '1';
                                $tipo=Empleado::EmpleadoBusquedaCuenta($idempleado, 'aportePatronal')->first();
                                $detalleDiario->cuenta_id = $tipo->cuenta_debe;
                                $detalleDiario->empleado_id = $idempleado;
                                $diariocontabilizado->detalles()->save($detalleDiario);
                                $general->registrarAuditoria('Registro de Detalle de Diario codigo: -> '.$diario->diario_codigo, '0', 'En la cuenta del Debe -> '.$tipo->cuenta_debe.' con el valor de: -> '.$cabecera_rol->cabecera_rol_iesspatronal);
               
        
                                $detalleDiario = new Detalle_Diario();
                                $detalleDiario->detalle_debe = 0.00;
                                $detalleDiario->detalle_haber = $cabecera_rol->cabecera_rol_iesspatronal+$IECE;
                                $detalleDiario->detalle_comentario =  'Pago del Rol del '.DateTime::createFromFormat('Y-m-d',$request->get('fecha'))->format('d-m-Y').' al '.DateTime::createFromFormat('Y-m-d',$request->get('fechafinal'))->format('d-m-Y');
                                $detalleDiario->detalle_tipo_documento = 'CONTABILIZACION MENSUAL';
                                $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                                $detalleDiario->detalle_conciliacion = '0';
                                $detalleDiario->detalle_estado = '1';
                                $tipo=Empleado::EmpleadoBusquedaCuenta($idempleado, 'aportePatronal')->first();
                                $detalleDiario->cuenta_id = $tipo->cuenta_haber;
                                $detalleDiario->empleado_id = $idempleado;
                                $diariocontabilizado->detalles()->save($detalleDiario);
                                $general->registrarAuditoria('Registro de Detalle de Diario codigo: -> '.$diario->diario_codigo, '0', 'En la cuenta del Haber -> '.$tipo->cuenta_haber.' con el valor de: -> '.$cabecera_rol->cabecera_rol_iesspatronal);
                            }
                            if ($vacacion>0) {
                                $detalleDiario = new Detalle_Diario();
                                $detalleDiario->detalle_debe = $vacacion;
                                $detalleDiario->detalle_haber = 0.00;
                                $detalleDiario->detalle_comentario =  'Pago del Rol del '.DateTime::createFromFormat('Y-m-d',$request->get('fecha'))->format('d-m-Y').' al '.DateTime::createFromFormat('Y-m-d',$request->get('fechafinal'))->format('d-m-Y');
                                $detalleDiario->detalle_tipo_documento = 'CONTABILIZACION MENSUAL';
                                $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                                $detalleDiario->detalle_conciliacion = '0';
                                $detalleDiario->detalle_estado = '1';
                                $tipo=Empleado::EmpleadoBusquedaCuenta($idempleado, 'vacacion')->first();
                                $detalleDiario->cuenta_id = $tipo->cuenta_debe;
                                $detalleDiario->empleado_id = $idempleado;
                                $diariocontabilizado->detalles()->save($detalleDiario);
                                $general->registrarAuditoria('Registro de Detalle de Diario codigo: -> '.$diario->diario_codigo, '0', 'En la cuenta del Debe -> '.$tipo->cuenta_debe.' con el valor de: -> '.$vacacion);
                 
        
                                $detalleDiario = new Detalle_Diario();
                                $detalleDiario->detalle_debe = 0.00;
                                $detalleDiario->detalle_haber = $vacacion;
                                $detalleDiario->detalle_comentario =  'Pago del Rol del '.DateTime::createFromFormat('Y-m-d',$request->get('fecha'))->format('d-m-Y').' al '.DateTime::createFromFormat('Y-m-d',$request->get('fechafinal'))->format('d-m-Y');
                                $detalleDiario->detalle_tipo_documento = 'CONTABILIZACION MENSUAL';
                                $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                                $detalleDiario->detalle_conciliacion = '0';
                                $detalleDiario->detalle_estado = '1';
                                $tipo=Empleado::EmpleadoBusquedaCuenta($idempleado, 'vacacion')->first();
                                $detalleDiario->cuenta_id = $tipo->cuenta_haber;
                                $detalleDiario->empleado_id = $idempleado;
                                $diariocontabilizado->detalles()->save($detalleDiario);
                                $general->registrarAuditoria('Registro de Detalle de Diario codigo: -> '.$diario->diario_codigo, '0', 'En la cuenta del Haber -> '.$tipo->cuenta_haber.' con el valor de: -> '.$vacacion);
                            }
                            
                            if (floatval($Dterceroacu)>0) {
                                $detalleDiario = new Detalle_Diario();
                                $detalleDiario->detalle_debe = floatval($Dterceroacu);
                                $detalleDiario->detalle_haber = 0.00;
                                $detalleDiario->detalle_comentario =  'Pago del Rol del '.DateTime::createFromFormat('Y-m-d',$request->get('fecha'))->format('d-m-Y').' al '.DateTime::createFromFormat('Y-m-d',$request->get('fechafinal'))->format('d-m-Y');
                                $detalleDiario->detalle_tipo_documento = 'CONTABILIZACION MENSUAL';
                                $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                                $detalleDiario->detalle_conciliacion = '0';
                                $detalleDiario->detalle_estado = '1';
                                $tipo=Empleado::EmpleadoBusquedaCuenta($idempleado, 'decimoTercero')->first();
                                $detalleDiario->cuenta_id = $tipo->cuenta_debe;
                                $detalleDiario->empleado_id = $idempleado;
                                $diariocontabilizado->detalles()->save($detalleDiario);
                                $general->registrarAuditoria('Registro de Detalle de Diario codigo: -> '.$diario->diario_codigo, '0', 'En la cuenta del Debe -> '.$tipo->cuenta_debe.' con el valor de: -> '.$Dtercero);
             
        
                                $detalleDiario = new Detalle_Diario();
                                $detalleDiario->detalle_debe = 0.00;
                                $detalleDiario->detalle_haber = floatval($Dterceroacu);
                                $detalleDiario->detalle_comentario =  'Pago del Rol del '.DateTime::createFromFormat('Y-m-d',$request->get('fecha'))->format('d-m-Y').' al '.DateTime::createFromFormat('Y-m-d',$request->get('fechafinal'))->format('d-m-Y');
                                $detalleDiario->detalle_tipo_documento = 'CONTABILIZACION MENSUAL';
                                $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                                $detalleDiario->detalle_conciliacion = '0';
                                $detalleDiario->detalle_estado = '1';
                                $tipo=Empleado::EmpleadoBusquedaCuenta($idempleado, 'decimoTercero')->first();
                                $detalleDiario->cuenta_id = $tipo->cuenta_haber;
                                $detalleDiario->empleado_id = $idempleado;
                                $diariocontabilizado->detalles()->save($detalleDiario);
                                $general->registrarAuditoria('Registro de Detalle de Diario codigo: -> '.$diario->diario_codigo, '0', 'En la cuenta del Haber -> '.$tipo->cuenta_haber.' con el valor de: -> '.$Dtercero);
                            }
                            if (floatval($Dcuartoacu)>0) {
                                $detalleDiario = new Detalle_Diario();
                                $detalleDiario->detalle_debe = floatval($Dcuartoacu);
                                $detalleDiario->detalle_haber = 0.00;
                                $detalleDiario->detalle_comentario =  'Pago del Rol del '.DateTime::createFromFormat('Y-m-d',$request->get('fecha'))->format('d-m-Y').' al '.DateTime::createFromFormat('Y-m-d',$request->get('fechafinal'))->format('d-m-Y');
                                $detalleDiario->detalle_tipo_documento = 'CONTABILIZACION MENSUAL';
                                $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                                $detalleDiario->detalle_conciliacion = '0';
                                $detalleDiario->detalle_estado = '1';
                                $tipo=Empleado::EmpleadoBusquedaCuenta($idempleado, 'decimoCuarto')->first();
                                $detalleDiario->cuenta_id = $tipo->cuenta_debe;
                                $detalleDiario->empleado_id = $idempleado;
                                $diariocontabilizado->detalles()->save($detalleDiario);
                                $general->registrarAuditoria('Registro de Detalle de Diario codigo: -> '.$diario->diario_codigo, '0', 'En la cuenta del Debe -> '.$tipo->cuenta_debe.' con el valor de: -> '.$Dcuarto);
                         
                                $detalleDiario = new Detalle_Diario();
                                $detalleDiario->detalle_debe = 0.00;
                                $detalleDiario->detalle_haber = floatval($Dcuartoacu);
                                $detalleDiario->detalle_comentario =  'Pago del Rol del '.DateTime::createFromFormat('Y-m-d',$request->get('fecha'))->format('d-m-Y').' al '.DateTime::createFromFormat('Y-m-d',$request->get('fechafinal'))->format('d-m-Y');
                                $detalleDiario->detalle_tipo_documento = 'CONTABILIZACION MENSUAL';
                                $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                                $detalleDiario->detalle_conciliacion = '0';
                                $detalleDiario->detalle_estado = '1';
                                $tipo=Empleado::EmpleadoBusquedaCuenta($idempleado, 'decimoCuarto')->first();
                                $detalleDiario->cuenta_id = $tipo->cuenta_haber;
                                $detalleDiario->empleado_id = $idempleado;
                                $diariocontabilizado->detalles()->save($detalleDiario);
                                $general->registrarAuditoria('Registro de Detalle de Diario codigo: -> '.$diario->diario_codigo, '0', 'En la cuenta del Haber -> '.$tipo->cuenta_haber.' con el valor de: -> '.$Dcuarto);
                            }
                           
                            if ($cabecera_rol->cabecera_rol_fr_acumula>0) {
                                $detalleDiario = new Detalle_Diario();
                                $detalleDiario->detalle_debe =  $cabecera_rol->cabecera_rol_fr_acumula;
                                $detalleDiario->detalle_haber =0.00;
                                $detalleDiario->detalle_comentario =  'Pago del Rol del '.DateTime::createFromFormat('Y-m-d',$request->get('fecha'))->format('d-m-Y').' al '.DateTime::createFromFormat('Y-m-d',$request->get('fechafinal'))->format('d-m-Y');
                                $detalleDiario->detalle_tipo_documento = 'CONTABILIZACION MENSUAL';
                                $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                                $detalleDiario->detalle_conciliacion = '0';
                                $detalleDiario->detalle_estado = '1';
                                $tipo=Empleado::EmpleadoBusquedaCuenta($idempleado, 'fondoReservaAcumulada')->first();
                                $detalleDiario->cuenta_id = $tipo->cuenta_debe;
                                $detalleDiario->empleado_id = $idempleado;
                                $diariocontabilizado->detalles()->save($detalleDiario);
                                $general->registrarAuditoria('Registro de Detalle de Diario codigo: -> '.$diario->diario_codigo, '0', 'En la cuenta del Debe -> '.$tipo->cuenta_debe.' con el valor de: -> '.$cabecera_rol->cabecera_rol_fr_acumula);
        
                                
                                $detalleDiario = new Detalle_Diario();
                                $detalleDiario->detalle_debe = 0.00;
                                $detalleDiario->detalle_haber = $cabecera_rol->cabecera_rol_fr_acumula;
                                $detalleDiario->detalle_comentario =  'Pago del Rol del '.DateTime::createFromFormat('Y-m-d',$request->get('fecha'))->format('d-m-Y').' al '.DateTime::createFromFormat('Y-m-d',$request->get('fechafinal'))->format('d-m-Y');
                                $detalleDiario->detalle_tipo_documento = 'CONTABILIZACION MENSUAL';
                                $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                                $detalleDiario->detalle_conciliacion = '0';
                                $detalleDiario->detalle_estado = '1';
                                $tipo=Empleado::EmpleadoBusquedaCuenta($idempleado, 'fondoReservaAcumulada')->first();
                                $detalleDiario->cuenta_id = $tipo->cuenta_haber;
                                $detalleDiario->empleado_id = $idempleado;
                                $diariocontabilizado->detalles()->save($detalleDiario);
                                $general->registrarAuditoria('Registro de Detalle de Diario codigo: -> '.$diario->diario_codigo, '0', 'En la cuenta del Haber -> '.$tipo->cuenta_haber.' con el valor de: -> '.$cabecera_rol->cabecera_rol_fr_acumula);
                            }
                            if (floatval($IECE)>0) {
                                $detalleDiario = new Detalle_Diario();
                                $detalleDiario->detalle_debe = floatval($IECE);
                                $detalleDiario->detalle_haber = 0.00;
                                $detalleDiario->detalle_comentario =  'Pago del Rol del '.DateTime::createFromFormat('Y-m-d',$request->get('fecha'))->format('d-m-Y').' al '.DateTime::createFromFormat('Y-m-d',$request->get('fechafinal'))->format('d-m-Y');
                                $detalleDiario->detalle_tipo_documento = 'CONTABILIZACION MENSUAL';
                                $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                                $detalleDiario->detalle_conciliacion = '0';
                                $detalleDiario->detalle_estado = '1';
                                $tipo=Empleado::EmpleadoBusquedaCuenta($idempleado, 'iece')->first();
                                $detalleDiario->cuenta_id = $tipo->cuenta_debe;
                                $detalleDiario->empleado_id = $idempleado;
                                $diariocontabilizado->detalles()->save($detalleDiario);
                                $general->registrarAuditoria('Registro de Detalle de Diario codigo: -> '.$diario->diario_codigo, '0', 'En la cuenta del Debe -> '.$tipo->cuenta_debe.' con el valor de: -> '.$IECE);
          
        
                           }
        
    
    
                        if(floatval($permiso)>0){
                        $detalle= new Detalle_Rol();
                        $detalle->detalle_rol_fecha_inicio = $request->get('fecha');
                        $detalle->detalle_rol_fecha_fin = $request->get('fechafinal');
                        $detalle->detalle_rol_sueldo = floatval($sueldo);
                        $detalle->detalle_rol_dias = $dpermiso;
                        $detalle->detalle_rol_valor_dia = floatval($vdia);
                        $detalle->detalle_rol_total_dias =floatval($permiso);
                        $detalle->detalle_rol_horas_extras = 0;
                        $detalle->detalle_rol_valor_he = $Dextras;
                        $detalle->detalle_rol_bonificacion_dias = 0;
                        $detalle->detalle_rol_bonificacion_valor = 0;
                        $detalle->detalle_rol_otra_bonificacion = 0;
                        $detalle->detalle_rol_otros_ingresos = 0;
                        $detalle->detalle_rol_sueldo_rembolsable = 0;
                        $detalle->detalle_rol_fondo_reserva = 0;
                        $detalle->detalle_rol_iess = 0;
                        $detalle->detalle_rol_multa = 0;
                        $detalle->detalle_rol_quincena = 0;
                        $detalle->detalle_rol_total_anticipo =0;
                        $detalle->detalle_rol_total_comisariato = 0;
                        $detalle->detalle_rol_prestamo_quirografario = 0;
                        $detalle->detalle_rol_prestamo_hipotecario = 0;
                        $detalle->detalle_rol_prestamo = 0;
                        $detalle->detalle_rol_transporte = 0;
                        $detalle->detalle_rol_ext_salud = 0;
                        $detalle->detalle_rol_impuesto_renta = 0;
                        $detalle->detalle_rol_ley_sol = 0;
                        $detalle->detalle_rol_total_permiso = 0;
                        $detalle->detalle_rol_permiso_no_rem = 0;
                        $detalle->detalle_rol_porcentaje = $ppermiso;
                        $detalle->detalle_rol_otros_egresos = 0;
                        $detalle->detalle_rol_liquido_pagar = floatval($permiso);
                        $detalle->detalle_rol_contabilizado = 1;
                        $detalle->detalle_rol_cosecha=0;
                        $detalle->detalle_rol_iess_asumido = 0;
                        $detalle->detalle_rol_aporte_patronal = 0;
                        $detalle->detalle_rol_aporte_iecesecap = 0;
                        $detalle->detalle_rol_vacaciones = 0;
                        $detalle->detalle_rol_vacaciones_anticipadas=0;
                        $detalle->detalle_rol_decimo_cuarto = 0;
                        $detalle->detalle_rol_decimo_tercero = 0;
                        $detalle->detalle_rol_decimo_cuartoacum = 0;
                        $detalle->detalle_rol_decimo_terceroacum = 0;
                        $detalle->detalle_rol_total_egreso = 0;
                        $detalle->detalle_rol_total_ingreso = floatval($permiso);
                        $detalle->detalle_rol_estado = 1;
                        $cabecera_rol->detalles()->save($detalle);
                        $general->registrarAuditoria('Registro de Detalle de Detalle Rol DE EMPLEADO -> '.$empleado->empleado_nombre, '0', ' con el valor de: -> '.$total);
      
                    }
                
                $cabecera=Rol_Consolidado::Rol($cabecera_rol->cabecera_rol_id)->get()->first();
                $url2 = $general->pdfRolOperativo($cabecera,$cuentabancaria);
                $url = $general->pdfDiario($diario);
                $url3 = $general->pdfDiario($diariocontabilizado);
                if ($request->get('tipo') == 'CHEQUE') {
                    DB::commit();
                    return redirect('/roloperativo/new/'.$request->get('punto_id'))->with('success','Datos guardados exitosament')->with('pdf',$url2)->with('diario',$url)->with('pdf2',$url3)->with('cheque',$urlcheque);
                }
                DB::commit();
                return redirect('/roloperativo/new/'.$request->get('punto_id'))->with('success','Datos guardados exitosamente')->with('pdf',$url2)->with('diario',$url)->with('pdf2',$url3);      
            }catch(\Exception $ex){
                DB::rollBack();
                return redirect('/roloperativo/new/'.$request->get('punto_id'))->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
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
        //
    }
}
