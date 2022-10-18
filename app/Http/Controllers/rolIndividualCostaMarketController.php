<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Alimentacion;
use App\Models\Anticipo_Empleado;
use App\Models\Arqueo_Caja;
use App\Models\Banco;
use App\Models\Cabecera_Rol_CM;
use App\Models\Caja;
use App\Models\Categoria_Producto;
use App\Models\Centro_Consumo;
use App\Models\Cheque;
use App\Models\Cuenta_Bancaria;
use App\Models\Descuento_Anticipo_Empleado;
use App\Models\Descuento_Quincena;
use App\Models\Detalle_Diario;
use App\Models\Detalle_Rol;
use App\Models\Detalle_Rol_CM;
use App\Models\Diario;
use App\Models\Empleado;
use App\Models\Movimiento_Caja;
use App\Models\Movimiento_Consumo_Rol;
use App\Models\Parametrizacion_Contable;
use App\Models\Punto_Emision;
use App\Models\Quincena;
use App\Models\Rango_Documento;
use App\Models\Rol_Movimiento;
use App\Models\Rubro;
use App\Models\Tipo_Movimiento_Empleado;
use App\Models\Transferencia;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Luecano\NumeroALetras\NumeroALetras;

class rolIndividualCostaMarketController extends Controller
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
            $empleados=Empleado::Empleados()->get();
            $cajaAbierta=Arqueo_Caja::arqueoCajaxuser(Auth::user()->user_id)->first();
            $movimientos=Tipo_Movimiento_Empleado::TipoMovimientos()->get();
            return view('admin.recursosHumanos.rolIndividual.index',['cajaAbierta'=>$cajaAbierta,'movimientos'=>$movimientos,'consumo'=>Centro_Consumo::CentroConsumos()->get(),'categoria'=>Categoria_Producto::Categorias()->get(),'empleados'=>$empleados,'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
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
    public function nuevo($id){
        try{ 
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $rangoDocumento=Rango_Documento::PuntoRango($id, 'Rol')->first();
            $cajaAbierta=Arqueo_Caja::arqueoCajaxuser(Auth::user()->user_id)->first();
            $movimientos=Tipo_Movimiento_Empleado::TipoMovimientos()->get();
            if($rangoDocumento){
                $empleados=Empleado::EmpleadosBySucursalAdministrativo($rangoDocumento->puntoEmision->sucursal_id)->get();
                $rubros=Rubro::RubrosRH()->get();
            return view('admin.RHCostaMarket.rolIndividual.index',['cajaAbierta'=>$cajaAbierta,'movimientos'=>$movimientos,'rubros'=>$rubros,'rangoDocumento'=>$rangoDocumento,'consumo'=>Centro_Consumo::CentroConsumos()->get(),'categoria'=>Categoria_Producto::Categorias()->get(),'empleados'=>$empleados,'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
              
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
        try {
            DB::beginTransaction();
            
            $roles=Cabecera_Rol_CM::RolesValidar($request->get('fechafinal'),$request->get('empleadoid'))->get();
           
            if(count($roles)>0){
                return redirect('/rolindividualCM/new/'.$request->get('punto_id'))->with('error2','Ya esta realizado el rol del empleado verifique por favor');
            }
           

            $general = new generalController();
            $urlcheque = '';
            $anticipos=$request->get('check');
           
            $idanticipos=($request->get('IDE'));
            $valoranticipos=($request->get('TDescontar'));

            $quincena=$request->get('Qcheck');
            $idquincena=($request->get('QIDE'));
            $valorquincena=($request->get('QTDescontar'));
            
            $valorali=($request->get('checkali'));
          
            $idalimentacion=($request->get('IDEali'));
    
            $idempleado=$request->get('empleadoid');
            

            $tipo=Empleado::EmpleadoBusquedaCuenta($idempleado, 'sueldos')->first();
            $cierre = $general->cierre($request->get('fechafinal'),$tipo->sucursal_id);  
            
            if($cierre){
                return redirect('/rolindividualCM/new/'.$request->get('punto_id'))->with('error2','No puede realizar la operacion por que pertenece a un mes bloqueado');
            }
     

            $Dtercero = $request->get('TTercero');
            $Dcuarto = $request->get('TCuarto');
            $fondoreserva = $request->get('TFondo'); 
            $total=$request->get('Liquidacion');

         

            
            $tdias = $request->get('Totaldias');
            $sueldo =$request->get('Vsueldos');
            
            
           
            $fechaini=$request->get('Tdesde');
            $fechafin = $request->get('Thasta');     
            $aniticos=$request->get('porcentaje');
            
            $idrubros=$request->get('idrubro'); 

    

         
           
            $Dingreso = $request->get('TIngresos');
            $viaticos = $request->get('Viaticos');
            $Degreso = $request->get('TEgresos');


            $Dterceroacu = $request->get('Tercero');
            $Dcuartoacu = $request->get('Cuarto');
            $Vacacioneacu = $request->get('VACACIONESP');
            $fondoreservaacu = $request->get('Fondo'); 
            $aportepatornal = $request->get('Patronal'); 
            $IECE=$request->get('IECE');

            $idmovimiento=$request->get('rolid'); 
            $rubros=$request->get('rubro');
            
            $tiporubros=$request->get('tiporubro'); 
            $valorrubros=$request->get('valor');
            $general = new generalController();
           
            

            $sueldototal=0;
            $totaladelanto=0;
            $totalquincena=0;
            $iess=0;
          

            $cabecera_rol = new Cabecera_Rol_CM();
            $cabecera_rol->cabecera_rol_sueldo =0;
            $cabecera_rol->cabecera_rol_anticipos =0;
            $cabecera_rol->cabecera_rol_quincena =0;
            $cabecera_rol->cabecera_rol_comisariato =0;
           for ($i = 1; $i < count($rubros); ++$i) {
               if($rubros[$i]=='quincena'){
                   $cabecera_rol->cabecera_rol_quincena = $valorrubros[$i];
               }
               if($rubros[$i]=='sueldos'){
                   $cabecera_rol->cabecera_rol_sueldo = $valorrubros[$i];
               }
               if($rubros[$i]=='anticipos'){
                   $cabecera_rol->cabecera_rol_anticipos = $valorrubros[$i];
               }
               if($rubros[$i]=='comisariato'){
                   $cabecera_rol->cabecera_rol_comisariato = $valorrubros[$i];
               }
           }
           $cabecera_rol->cabecera_rol_fecha = $request->get('fechafinal');
           $cabecera_rol->cabecera_rol_total_dias = $tdias;
           $cabecera_rol->cabecera_rol_total_ingresos = $Dingreso;

           $cabecera_rol->cabecera_rol_total_egresos =$Degreso;
           $cabecera_rol->cabecera_rol_pago = $total;
           $cabecera_rol->cabecera_rol_fr_acumula = $fondoreservaacu;
           $cabecera_rol->cabecera_rol_decimotercero_acumula = $Dterceroacu;
           $cabecera_rol->cabecera_rol_decimocuarto_acumula = $Dcuartoacu;
           $cabecera_rol->cabecera_rol_vacaciones = $Vacacioneacu;
           $cabecera_rol->cabecera_rol_fondo_reserva = $fondoreserva;
           $cabecera_rol->cabecera_rol_decimotercero = $Dtercero;
           $cabecera_rol->cabecera_rol_decimocuarto = $Dcuarto;
           $cabecera_rol->cabecera_rol_viaticos = $viaticos;
           $cabecera_rol->cabecera_rol_aporte_patronal = $aportepatornal;
           $cabecera_rol->cabecera_rol_iece_secap = $IECE;
        
           $cabecera_rol->cabecera_rol_tipo = 'INDIVIDUAL';
           $cabecera_rol->empleado_id =$idempleado;
           $cabecera_rol->cabecera_rol_estado = 1;
           
           

          
            if ($request->get('tipo') == 'Cheque') {
                $cuenta_id=$request->get('ncuenta_cheque');
                $cuentabanca=$request->get('cuenta_id_cheque');
                $banco=Banco::findOrFail($request->get('banco_id_cheque'));
                $cuentabancaria=Cuenta_Bancaria::findOrFail($cuentabanca);
            }
            if ($request->get('tipo') == 'Transferencia') {
                $cuenta_id=$request->get('ncuenta_transfer');
                $cuentabanca=$request->get('cuenta_id_transfer');
                $banco=Banco::findOrFail($request->get('banco_id_transfer'));
                 $cuentabancaria=Cuenta_Bancaria::findOrFail($cuentabanca);
            }
            if($request->get('tipo') == 'Efectivo'){
                $cuentacaja=Caja::caja($request->get('caja_id'))->first();
                $cuenta_id = $cuentacaja->cuenta_id;
                $tipoDoc = 'EFECTIVO';
            }
            if($request->get('tipo') == 'Otros'){
                $TipoMovimiento=Tipo_Movimiento_Empleado::findOrFail($request->get('idmovimiento'));
                $cuenta_id = $TipoMovimiento->cuenta_id;
                $tipoDoc = 'OTROS';
            }
           
           
           
            $empleado=Empleado::findOrFail($idempleado);
           
            
            if ($request->get('tipo') == 'Cheque') {
                $formatter = new NumeroALetras();
                $cheque = new Cheque();
                $cheque->cheque_numero = $request->get('idNcheque');
                $cheque->cheque_descripcion =  'PAGO ROL DE EMPLEADO '.$empleado->empleado_nombre.' CON MES Y AÑO '.date('Y', strtotime($request->get('fechafinal'))).'/'.date('F', strtotime($request->get('fechafinal')));
                $cheque->cheque_beneficiario = $empleado->empleado_nombre;
                $cheque->cheque_fecha_emision = $request->get('idFechaCheque');
                $cheque->cheque_fecha_pago = $request->get('idFechaCheque');
                $cheque->cheque_valor = $cabecera_rol->cabecera_rol_pago;
                $cheque->cheque_valor_letras = $formatter->toInvoice($cheque->cheque_valor, 2, 'Dolares');
                $cheque->cuenta_bancaria_id = $request->get('cuenta_id_cheque');
                $cheque->cheque_estado = '1';
                $cheque->empresa_id = Auth::user()->empresa->empresa_id;
                $cheque->save();
                $urlcheque = $general->pdfImprimeCheque($request->get('cuenta_id_cheque'),$cheque);
                $general->registrarAuditoria('Registro de Cheque numero: -> '.$request->get('idNcheque'), '0', 'Por motivo de: -> '. $cabecera_rol->cabecera_rol_descripcion.' con el valor de: -> '.$total);
            }
            
            if ($request->get('tipo') == 'Transferencia') {
                $transferencia = new Transferencia();
                $transferencia->transferencia_descripcion = 'PAGO ROL DE EMPLEADO '.$empleado->empleado_nombre.' CON MES Y AÑO '.date('Y', strtotime($request->get('fechafinal'))).'/'.date('F', strtotime($request->get('fechafinal')));
                $transferencia->transferencia_beneficiario = $empleado->empleado_nombre;
                $transferencia->transferencia_fecha = $request->get('idFechatrasnfer');
                $transferencia->transferencia_valor = $cabecera_rol->cabecera_rol_pago;
                $transferencia->cuenta_bancaria_id = $request->get('cuenta_id_transfer');
                $transferencia->transferencia_estado = '1';
                $transferencia->empresa_id = Auth::user()->empresa->empresa_id;
                $transferencia->save();
                $general->registrarAuditoria('Registro de Transferencia numero: -> '.$request->get('ncuenta'), '0', 'Por motivo de: -> '. $cabecera_rol->cabecera_rol_descripcion.' con el valor de: -> '.$total);
            
            }

           

            $diario = new Diario();
            if ($request->get('tipo') == 'Transferencia') {
                $diario->diario_codigo = $general->generarCodigoDiario($request->get('idFechatrasnfer'), 'CPRP');
                $diario->diario_fecha = $request->get('idFechatrasnfer');
                $diario->diario_mes = DateTime::createFromFormat('Y-m-d', $request->get('idFechatrasnfer'))->format('m');
                $diario->diario_ano = DateTime::createFromFormat('Y-m-d', $request->get('idFechatrasnfer'))->format('Y');
                $diario->diario_tipo_documento = 'TRANSFERENCIA BANCARIA';
                $diario->diario_numero_documento =0;
            }
            if ($request->get('tipo') == 'Cheque') {
                $diario->diario_codigo = $general->generarCodigoDiario($request->get('idFechaCheque'), 'CPRP');
                $diario->diario_fecha = $request->get('idFechaCheque');
                $diario->diario_mes = DateTime::createFromFormat('Y-m-d', $request->get('idFechaCheque'))->format('m');
                $diario->diario_ano = DateTime::createFromFormat('Y-m-d', $request->get('idFechaCheque'))->format('Y');
                $diario->diario_tipo_documento = 'CHEQUE';
                $diario->diario_numero_documento =$request->get('idNcheque');
            }
            if ($request->get('tipo') == 'Efectivo') {
                $diario->diario_codigo = $general->generarCodigoDiario($request->get('fechafinal'), 'CPRP');
                $diario->diario_fecha = $request->get('fechafinal');
                $diario->diario_mes = DateTime::createFromFormat('Y-m-d', $request->get('fechafinal'))->format('m');
                $diario->diario_ano = DateTime::createFromFormat('Y-m-d', $request->get('fechafinal'))->format('Y');
                $diario->diario_tipo_documento = 'EFECTIVO';
                $diario->diario_numero_documento =0;
            }
            if ($request->get('tipo') == 'Otros') {
                $diario->diario_codigo = $general->generarCodigoDiario($request->get('fechafinal'), 'CPRP');
                $diario->diario_fecha = $request->get('fechafinal');
                $diario->diario_mes = DateTime::createFromFormat('Y-m-d', $request->get('fechafinal'))->format('m');
                $diario->diario_ano = DateTime::createFromFormat('Y-m-d', $request->get('fechafinal'))->format('Y');
                $diario->diario_tipo_documento = 'OTROS';
                $diario->diario_numero_documento =0;
            }
         
            $diario->diario_referencia = 'COMPROBANTE DE PAGO DE ROL DE EMPLEADO';

            $diario->diario_tipo_documento = 'ROL INDIVIDUAL';

            $diario->diario_tipo = 'CPRP';

            $diario->diario_secuencial = substr($diario->diario_codigo, 8);
           
            $diario->diario_comentario = 'PAGO DEL ROL DEL '.DateTime::createFromFormat('Y-m-d',$fechaini[1])->format('d-m-Y').' AL '.DateTime::createFromFormat('Y-m-d',$request->get('fechafinal'))->format('d-m-Y');
           
            
            $diario->diario_beneficiario =$empleado->empleado_nombre;
            $diario->diario_cierre = '0';
            $diario->diario_estado = '1';
            
            $diario->empresa_id = Auth::user()->empresa_id;
            $diario->sucursal_id =  $tipo->sucursal_id;
            $diario->save();
            $general->registrarAuditoria('Registro de diario de rol INDIVIDUAL de Empleado -> '.$idempleado, '0', 'Con motivo:'. $cabecera_rol->cabecera_rol_descripcion);
            $cabecera_rol->diariopago()->associate($diario); 
           


            $diariocontabilizado = new Diario();
            $diariocontabilizado->diario_codigo = $general->generarCodigoDiario($request->get('fechafinal'), 'CCMR');
 
          
            $diariocontabilizado->diario_fecha = $request->get('fechafinal');
            
            $diariocontabilizado->diario_referencia = 'COMPROBANTE DE CONTABILIZACION MENSUAL DE ROLES';
            $diariocontabilizado->diario_tipo_documento = 'CONTABILIZACION MENSUAL';
            $diariocontabilizado->diario_tipo = 'CCMR';
            $diariocontabilizado->diario_secuencial = substr($diariocontabilizado->diario_codigo, 8);
            $diariocontabilizado->diario_mes = DateTime::createFromFormat('Y-m-d', $request->get('fechafinal'))->format('m');
            $diariocontabilizado->diario_ano = DateTime::createFromFormat('Y-m-d', $request->get('fechafinal'))->format('Y');
            $diariocontabilizado->diario_comentario = 'COMPROBANTE DE CONTABILIZACION MENSUAL DE ROLES: '.$empleado->empleado_nombre;
                    
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
            $general->registrarAuditoria('Registro de Rol INDIVIDUAL de Empleado -> '.$empleado->empleado_nombre, '0', 'Con motivo:'. $cabecera_rol->cabecera_rol_descripcion);
           
            $detalleDiario = new Detalle_Diario();
            $detalleDiario->detalle_debe = 0.00;
            $detalleDiario->detalle_haber = ($Dingreso-$Degreso)+$Dtercero+$Dcuarto+$fondoreserva+$viaticos;
            if ($request->get('tipo') == 'Cheque') {
                $detalleDiario->detalle_comentario =  'CHEQUE No '.$request->get('idNcheque');
            }
            if ($request->get('tipo') == 'Transferencia') {
                $detalleDiario->detalle_comentario = 'TRANSFERENCIA A CUENTA No '.$empleado->empleado_cuenta_numero;
            }
            if ($request->get('tipo') == 'Efectivo') {
                $detalleDiario->detalle_comentario = 'PAGO EN EFECTIVO ';
            }
            if ($request->get('tipo') == 'Otros') {
                
                $detalleDiario->detalle_comentario = 'PAGO POR '.$TipoMovimiento->tipo_nombre;
            }
            $detalleDiario->detalle_tipo_documento = 'ROL INDIVIDUAL';
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
            $detalleDiario->detalle_debe =  $Dingreso+$Dtercero+$Dcuarto+$fondoreserva+$viaticos-$Degreso;
            $detalleDiario->detalle_haber =0.00;
            
            $detalleDiario->detalle_comentario = 'Pago del Rol del '.DateTime::createFromFormat('Y-m-d',$fechaini[1])->format('d-m-Y').' al '.DateTime::createFromFormat('Y-m-d',$request->get('fechafinal'))->format('d-m-Y');
            
            $detalleDiario->detalle_tipo_documento = 'ROL INDIVIDUAL';
            
            $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
            $detalleDiario->detalle_conciliacion = '0';
            $detalleDiario->detalle_estado = '1';
            $detalleDiario->cuenta_id = $tipo->cuenta_haber;
            $detalleDiario->empleado_id = $idempleado;
            $diario->detalles()->save($detalleDiario);
            $general->registrarAuditoria('Registro de Detalle de Diario codigo: -> '.$diario->diario_codigo, '0', 'En la cuenta del Haber -> '.$request->get('idCuentaContable').' con el valor de: -> '.$total);
            
            if($request->get('tipo') == 'Efectivo'){
                $arqueoCaja=Arqueo_Caja::arqueoCaja(Auth::user()->user_id)->first();
                /**********************movimiento caja****************************/
                $movimientoCaja = new Movimiento_Caja();          
                $movimientoCaja->movimiento_fecha=date("Y")."-".date("m")."-".date("d");
                $movimientoCaja->movimiento_hora=date("H:i:s");
                $movimientoCaja->movimiento_tipo="SALIDA";
                $movimientoCaja->movimiento_descripcion= 'P/R PAGO A EMPLEADO :'.$empleado->empleado_nombreS;
                $movimientoCaja->movimiento_valor= $Dingreso+$Dtercero+$Dcuarto+$fondoreserva+$viaticos-$Degreso;
                $movimientoCaja->movimiento_documento="PAGO A EMPLEADO EN EFECTIVO";
                $movimientoCaja->movimiento_numero_documento= 0;
                $movimientoCaja->movimiento_estado = 1;
                $movimientoCaja->arqueo_id = $arqueoCaja->arqueo_id;
                if(Auth::user()->empresa->empresa_contabilidad == '1'){
                    $movimientoCaja->diario()->associate($diario);
                }
                $movimientoCaja->save();
                /*********************************************************************/
            }

            if (isset($valorali)){
                for ($j = 0; $j < count($valorali); ++$j) {
                $alimentar=Alimentacion::findOrFail($idalimentacion[$valorali[$j]]);
                $alimentar->alimentacion_estado='2';
                $alimentar->rolcm()->associate($cabecera_rol);
                $alimentar->save();
                $auditoria = new generalController();
                $auditoria->registrarAuditoria('Actualizacion de Alimentacion de estado a 2 con empleado -> '.$empleado->empleado_nombre, '0', 'Por el pago de rol');
            
                }
            }
            if (isset($quincena)){
                for ($j = 0; $j < count($quincena); ++$j) {
                    $quincenas=Quincena::findOrFail($idquincena[$quincena[$j]]);
                
                    $anticipoquincena=new Descuento_Quincena();
                    $anticipoquincena->descuento_fecha=$request->get('fechafinal');
                    $anticipoquincena->descuento_descripcion='Descuento de quincena en Rol';
                    $anticipoquincena->descuento_valor=$valorquincena[$j];
                    $anticipoquincena->descuento_estado='1';
                    $anticipoquincena->rolcm()->associate($cabecera_rol);
                    $anticipoquincena->quincena()->associate($quincenas);
                    $anticipoquincena->diario()->associate($diario);
                    $anticipoquincena->save();

                    $quincenas->quincena_saldo=$quincenas->quincena_valor-(Descuento_Quincena::Anticipos($quincenas->quincena_id)->sum('descuento_valor'));
                    if ($quincenas->quincena_saldo=='0') {
                        $quincenas->quincena_estado='2';
                    }
                    $quincenas->update();
               
                    $auditoria = new generalController();
                    $auditoria->registrarAuditoria('Actualizacion de Quincena de estado a 2 con empleado -> '.$empleado->empleado_nombre, '0', 'Por el pago de rol');
                }
            }
            if (isset($anticipos)) {
                for ($j = 0; $j < count($anticipos); ++$j) {
                    if (($valoranticipos[$j])>0) {
                        $anticipo=Anticipo_Empleado::findOrFail($idanticipos[$anticipos[$j]]);
                        $anticipodescuento=new Descuento_Anticipo_Empleado();
                        $anticipodescuento->descuento_fecha=$request->get('fechafinal');
                        $anticipodescuento->descuento_descripcion='Descuento de anticipo en Rol';
                        $anticipodescuento->descuento_valor=$valoranticipos[$j];
                        $anticipodescuento->descuento_estado='1';
                        $anticipodescuento->rolcm()->associate($cabecera_rol);
                        $anticipodescuento->anticipo()->associate($anticipo);
                        $anticipodescuento->diario()->associate($diario);
                        $anticipodescuento->save();
        
                        $auditoria = new generalController();
                        $auditoria->registrarAuditoria('Resgitro del descuento del Anticipo de empleado-> '.$request->get('idEmpleado'), '0', 'Por el pago de rol');
                        if ($anticipo->anticipo_saldom) {
                            $anticipo->anticipo_saldo=$anticipo->anticipo_saldom-(Descuento_Anticipo_Empleado::Anticipos($anticipo->anticipo_id)->sum('descuento_valor'));
                        } else {
                            $anticipo->anticipo_saldo=$anticipo->anticipo_valor-(Descuento_Anticipo_Empleado::Anticipos($anticipo->anticipo_id)->sum('descuento_valor'));
                        }
                        if ($anticipo->anticipo_saldo=='0') {
                            $anticipo->anticipo_estado='2';
                        }
                        $anticipo->update();
                        $auditoria = new generalController();
                        $auditoria->registrarAuditoria('Actualizacion del Anticipo de estado a 2 con empleado-> '.$empleado->empleado_nombre, '0', 'Por el pago de rol');
                    }
                }
            }
            $matriz=null;
            $activador=true;
            $count=1;

            for ($i = 0; $i < count($rubros); ++$i) {
                if ($idrubros[$i]!="{idrubro}") {
                    if ($valorrubros[$i]>0) {
                        $activadoranti=true;
                        $auxrubro=Rubro::findOrFail($idrubros[$i]);
                        $detalle= new Detalle_Rol_CM();
                        $detalle->detalle_rol_fecha_inicio = $fechaini[1];
                        $detalle->detalle_rol_fecha_fin =  $fechafin[1];
                        $detalle->detalle_rol_descripcion = $auxrubro->rubro_descripcion;
                        $detalle->detalle_rol_valor = $valorrubros[$i];
                        $detalle->detalle_rol_contabilizado = '1';
                        $detalle->detalle_rol_estado = '1';
                        if ($idmovimiento[$i]!='0') {
                            $movi=Rol_Movimiento::findOrFail($idmovimiento[$i]);
                            $movi->rolcm()->associate($cabecera_rol);
                            $movi->rol_movimiento_estado='2';
                            $movi->save();
                        }                   
                        $detalle->movimiento()->associate($auxrubro);
                        $cabecera_rol->detalles()->save($detalle);
                        $general->registrarAuditoria('Registro de Detalle de Detalle Rol DE EMPLEADO -> '.$empleado->empleado_nombre, '0', ' con el valor de: -> '.$total); 
                        if ($tiporubros[$i]=='2') {
                                $tipo=Empleado::EmpleadoBusquedaCuenta($idempleado, $rubros[$i])->first();
                                if($matriz==null){
                                    $matriz[$count]["idcuenta"]= $tipo->cuenta_debe;
                                    $matriz[$count]["debe"]= floatval($valorrubros[$i]);
                                    $matriz[$count]["tipo"]= 'DEBE';
                                    $matriz[$count]["haber"]=0;
                                    $count++;
                                }
                                else{
                                    $activador=true;
                                    for ($k = 1; $k <= count($matriz); ++$k) {
                                        if($matriz[$k]["idcuenta"]==$tipo->cuenta_debe && $matriz[$k]["debe"]>0){
                                            $matriz[$k]["debe"]=  $matriz[$k]["debe"]+floatval($valorrubros[$i]);
                                            $activador=false;
                                        }
                                    }
                                    if($activador==true){
                                        $matriz[$count]["idcuenta"]= $tipo->cuenta_debe;
                                        $matriz[$count]["debe"]= floatval($valorrubros[$i]);
                                        $matriz[$count]["tipo"]= 'DEBE';
                                        $matriz[$count]["haber"]=0;
                                        $count++;
                                    }
                                }                               
                       }
                        if ($tiporubros[$i]=='1') {
                            if (isset($valoranticipos)) {
                                $parametrizacionContable=Parametrizacion_Contable::ParametrizacionByNombreFinanciero('ANTICIPO DE EMPLEADO')->first();
                                $emplea=Empleado::findOrFail($idempleado);
                                if ($parametrizacionContable->parametrizacion_cuenta_general == '0') {
                                    $activadoranti=false;
                                    if ($matriz==null) {
                                        $matriz[$count]["idcuenta"]= $emplea->empleado_cuenta_anticipo;
                                        $matriz[$count]["debe"]= 0;
                                        $matriz[$count]["tipo"]= 'HABER';
                                        $matriz[$count]["haber"]=floatval($valorrubros[$i]);
                                        $count++;
                                    } else {
                                        $matriz[$count]["idcuenta"]= $emplea->empleado_cuenta_anticipo;
                                        $matriz[$count]["debe"]= 0;
                                        $matriz[$count]["tipo"]= 'HABER';
                                        $matriz[$count]["haber"]=floatval($valorrubros[$i]);
                                        $count++;
                                    }
                                }
                            }
                            if ($activadoranti==true) {
                                $tipo=Empleado::EmpleadoBusquedaCuenta($idempleado, $rubros[$i])->first();
                                if ($matriz==null) {
                                    $matriz[$count]["idcuenta"]= $tipo->cuenta_haber;
                                    $matriz[$count]["haber"]= floatval($valorrubros[$i]);
                                    $matriz[$count]["tipo"]= 'HABER';
                                    $matriz[$count]["debe"]=0;
                                    $count++;
                                } else {
                                    $activador=true;
                                    for ($k = 1; $k <= count($matriz); ++$k) {
                                        if ($matriz[$k]["idcuenta"]==$tipo->cuenta_haber && $matriz[$k]["haber"]>0) {
                                            $matriz[$k]["haber"]=  $matriz[$k]["haber"]+floatval($valorrubros[$i]);
                                            $activador=false;
                                        }
                                    }
                                    if ($activador==true) {
                                        $matriz[$count]["idcuenta"]= $tipo->cuenta_haber;
                                        $matriz[$count]["haber"]= floatval($valorrubros[$i]);
                                        $matriz[$count]["tipo"]= 'HABER';
                                        $matriz[$count]["debe"]=0;
                                        $count++;
                                    }
                                }
                            }
                                                     
                        }
                    }
                
                }
                
            } 
            if (floatval($fondoreserva)>0) {
                $detalleDiario = new Detalle_Diario();
                $detalleDiario->detalle_debe = $fondoreserva;
                $detalleDiario->detalle_haber = 0.00;
                $detalleDiario->detalle_comentario =  'Pago del Rol del '.DateTime::createFromFormat('Y-m-d',$fechaini[1])->format('d-m-Y').' al '.DateTime::createFromFormat('Y-m-d',$request->get('fechafinal'))->format('d-m-Y');
                $detalleDiario->detalle_tipo_documento = 'CONTABILIZACION MENSUAL';
                $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                $detalleDiario->detalle_conciliacion = '0';
                $detalleDiario->detalle_estado = '1';
                $tipo=Empleado::EmpleadoBusquedaCuenta($idempleado, 'fondoReserva')->first();
                $detalleDiario->cuenta_id = $tipo->cuenta_debe;
                $detalleDiario->empleado_id = $idempleado;
                $diariocontabilizado->detalles()->save($detalleDiario);
                $general->registrarAuditoria('Registro de Detalle de Diario codigo: -> '.$diario->diario_codigo, '0', 'En la cuenta del Debe -> '.$tipo->cuenta_haber.' con el valor de: -> '.$fondoreserva);
            } 
            if (floatval($Dtercero)>0) {
                $detalleDiario = new Detalle_Diario();
                $detalleDiario->detalle_debe = $Dtercero;
                $detalleDiario->detalle_haber = 0.00;
                $detalleDiario->detalle_comentario =  'Pago del Rol del '.DateTime::createFromFormat('Y-m-d',$fechaini[1])->format('d-m-Y').' al '.DateTime::createFromFormat('Y-m-d',$request->get('fechafinal'))->format('d-m-Y');
                $detalleDiario->detalle_tipo_documento = 'CONTABILIZACION MENSUAL';
                $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                $detalleDiario->detalle_conciliacion = '0';
                $detalleDiario->detalle_estado = '1';
                $tipo=Empleado::EmpleadoBusquedaCuenta($idempleado, 'decimoTercero')->first();
                $detalleDiario->cuenta_id = $tipo->cuenta_debe;
                $detalleDiario->empleado_id = $idempleado;
                $diariocontabilizado->detalles()->save($detalleDiario);
                $general->registrarAuditoria('Registro de Detalle de Diario codigo: -> '.$diario->diario_codigo, '0', 'En la cuenta del Debe -> '.$tipo->cuenta_haber.' con el valor de: -> '.$Dtercero);
            }
            if (floatval($Dcuarto)>0) {
                $detalleDiario = new Detalle_Diario();
                $detalleDiario->detalle_debe = $Dcuarto;
                $detalleDiario->detalle_haber = 0.00;
                $detalleDiario->detalle_comentario =  'Pago del Rol del '.DateTime::createFromFormat('Y-m-d',$fechaini[1])->format('d-m-Y').' al '.DateTime::createFromFormat('Y-m-d',$request->get('fechafinal'))->format('d-m-Y');
                $detalleDiario->detalle_tipo_documento = 'CONTABILIZACION MENSUAL';
                $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                $detalleDiario->detalle_conciliacion = '0';
                $detalleDiario->detalle_estado = '1';
                $tipo=Empleado::EmpleadoBusquedaCuenta($idempleado, 'decimoCuarto')->first();
                $detalleDiario->cuenta_id = $tipo->cuenta_debe;
                $detalleDiario->empleado_id = $idempleado;
                $diariocontabilizado->detalles()->save($detalleDiario);
                $general->registrarAuditoria('Registro de Detalle de Diario codigo: -> '.$diario->diario_codigo, '0', 'En la cuenta del Debe -> '.$tipo->cuenta_haber.' con el valor de: -> '.$Dcuarto);
            }
            if (floatval($cabecera_rol->cabecera_rol_aporte_patronal)>0) {
                $tipo=Empleado::EmpleadoBusquedaCuenta($idempleado, 'aportePatronal')->first();

                if($matriz==null){
                    $matriz[$count]["idcuenta"]= $tipo->cuenta_debe;
                    $matriz[$count]["debe"]=floatval($cabecera_rol->cabecera_rol_aporte_patronal);
                    $matriz[$count]["tipo"]= 'DEBE';
                    $matriz[$count]["haber"]=0;
                    $count++;
                }
                else{
                    $activador=true;
                    for ($k = 1; $k <= count($matriz); ++$k){ 
                        if($matriz[$k]["idcuenta"]==$tipo->cuenta_debe && $matriz[$k]["debe"]>0){
                            $matriz[$k]["debe"]=  $matriz[$k]["debe"]+floatval($cabecera_rol->cabecera_rol_aporte_patronal);
                            $activador=false;
                        }
                    }
                    if($activador==true){
                        $matriz[$count]["idcuenta"]= $tipo->cuenta_debe;
                        $matriz[$count]["debe"]= floatval($cabecera_rol->cabecera_rol_aporte_patronal);
                        $matriz[$count]["tipo"]= 'DEBE';
                        $matriz[$count]["haber"]=0;
                        $count++;
                    }
                    
                }
            }
            if (floatval($IECE)>0) {
                $tipo=Empleado::EmpleadoBusquedaCuenta($idempleado, 'iece')->first();

                if($matriz==null){
                    $matriz[$count]["idcuenta"]= $tipo->cuenta_debe;
                    $matriz[$count]["debe"]=floatval($IECE);
                    $matriz[$count]["tipo"]= 'DEBE';
                    $matriz[$count]["haber"]=0;
                    $count++;
                }
                else{
                    $activador=true;
                    for ($k = 1; $k <= count($matriz); ++$k){ 
                        if($matriz[$k]["idcuenta"]==$tipo->cuenta_debe && $matriz[$k]["debe"]>0){
                            $matriz[$k]["debe"]=  $matriz[$k]["debe"]+floatval($IECE);
                            $activador=false;
                        }
                    }
                    if($activador==true){
                        $matriz[$count]["idcuenta"]= $tipo->cuenta_debe;
                        $matriz[$count]["debe"]= floatval($IECE);
                        $matriz[$count]["tipo"]= 'DEBE';
                        $matriz[$count]["haber"]=0;
                        $count++;
                    }
                    
                }

               

            }
            if (floatval($viaticos)>0) {
                $tipo=Empleado::EmpleadoBusquedaCuenta($idempleado, 'viaticos')->first();
                if($matriz==null){
                    $matriz[$count]["idcuenta"]= $tipo->cuenta_debe;
                    $matriz[$count]["debe"]= floatval($viaticos);
                    $matriz[$count]["tipo"]= 'DEBE';
                    $matriz[$count]["haber"]=0;
                    $count++;
                }
                else{
                    $activador=true;
                    for ($k = 1; $k <= count($matriz); ++$k){
                        if($matriz[$k]["idcuenta"]==$tipo->cuenta_debe && $matriz[$k]["debe"]>0){
                            $matriz[$k]["debe"]=  $matriz[$k]["debe"]+floatval($viaticos);
                            $activador=false;
                        }
                    
                    }
                    if($activador==true){
                        $matriz[$count]["idcuenta"]= $tipo->cuenta_debe;
                        $matriz[$count]["debe"]= floatval($viaticos);
                        $matriz[$count]["tipo"]= 'DEBE';
                        $matriz[$count]["haber"]=0;
                        $count++;
                    }
                }
            }
            if ((floatval($aportepatornal)+floatval($IECE))>0) {
                $detalleDiario = new Detalle_Diario();
                $detalleDiario->detalle_debe = 0.00;
                $detalleDiario->detalle_haber = (floatval($aportepatornal)+floatval($IECE));
                $detalleDiario->detalle_comentario =  'Pago del Rol del '.DateTime::createFromFormat('Y-m-d', $fechaini[1])->format('d-m-Y').' al '.DateTime::createFromFormat('Y-m-d', $request->get('fechafinal'))->format('d-m-Y');
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
            if (floatval($Dterceroacu)>0) {
                $detalleDiario = new Detalle_Diario();
                $detalleDiario->detalle_debe = floatval($Dterceroacu);
                $detalleDiario->detalle_haber = 0.00;
                $detalleDiario->detalle_comentario =  'Pago del Rol del '.DateTime::createFromFormat('Y-m-d',$fechaini[1])->format('d-m-Y').' al '.DateTime::createFromFormat('Y-m-d',$request->get('fechafinal'))->format('d-m-Y');
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
                $detalleDiario->detalle_comentario =  'Pago del Rol del '.DateTime::createFromFormat('Y-m-d', $fechaini[1])->format('d-m-Y').' al '.DateTime::createFromFormat('Y-m-d',$request->get('fechafinal'))->format('d-m-Y');
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
            if (floatval($Vacacioneacu)>0) {
                $detalleDiario = new Detalle_Diario();
                $detalleDiario->detalle_debe = floatval($Vacacioneacu);
                $detalleDiario->detalle_haber = 0.00;
                $detalleDiario->detalle_comentario =  'Pago del Rol del '.DateTime::createFromFormat('Y-m-d', $fechaini[1])->format('d-m-Y').' al '.DateTime::createFromFormat('Y-m-d',$request->get('fechafinal'))->format('d-m-Y');
                $detalleDiario->detalle_tipo_documento = 'CONTABILIZACION MENSUAL';
                $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                $detalleDiario->detalle_conciliacion = '0';
                $detalleDiario->detalle_estado = '1';
                $tipo=Empleado::EmpleadoBusquedaCuenta($idempleado, 'vacacion')->first();
                $detalleDiario->cuenta_id = $tipo->cuenta_debe;
                $detalleDiario->empleado_id = $idempleado;
                $diariocontabilizado->detalles()->save($detalleDiario);
                $general->registrarAuditoria('Registro de Detalle de Diario codigo: -> '.$diario->diario_codigo, '0', 'En la cuenta del Debe -> '.$tipo->cuenta_debe.' con el valor de: -> '.$Dtercero);


                $detalleDiario = new Detalle_Diario();
                $detalleDiario->detalle_debe = 0.00;
                $detalleDiario->detalle_haber = floatval($Vacacioneacu);
                $detalleDiario->detalle_comentario =  'Pago del Rol del '.DateTime::createFromFormat('Y-m-d', $fechaini[1])->format('d-m-Y').' al '.DateTime::createFromFormat('Y-m-d',$request->get('fechafinal'))->format('d-m-Y');
                $detalleDiario->detalle_tipo_documento = 'CONTABILIZACION MENSUAL';
                $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                $detalleDiario->detalle_conciliacion = '0';
                $detalleDiario->detalle_estado = '1';
                $tipo=Empleado::EmpleadoBusquedaCuenta($idempleado, 'vacacion')->first();
                $detalleDiario->cuenta_id = $tipo->cuenta_haber;
                $detalleDiario->empleado_id = $idempleado;
                $diariocontabilizado->detalles()->save($detalleDiario);
                $general->registrarAuditoria('Registro de Detalle de Diario codigo: -> '.$diario->diario_codigo, '0', 'En la cuenta del Haber -> '.$tipo->cuenta_haber.' con el valor de: -> '.$Dtercero);
            }
            if (floatval($Dcuartoacu)>0) {
                $detalleDiario = new Detalle_Diario();
                $detalleDiario->detalle_debe = floatval($Dcuartoacu);
                $detalleDiario->detalle_haber = 0.00;
                $detalleDiario->detalle_comentario =  'Pago del Rol del '.DateTime::createFromFormat('Y-m-d', $fechaini[1])->format('d-m-Y').' al '.DateTime::createFromFormat('Y-m-d',$request->get('fechafinal'))->format('d-m-Y');
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
                $detalleDiario->detalle_comentario =  'Pago del Rol del '.DateTime::createFromFormat('Y-m-d',$fechaini[1])->format('d-m-Y').' al '.DateTime::createFromFormat('Y-m-d',$request->get('fechafinal'))->format('d-m-Y');
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
            if (floatval($fondoreservaacu)>0) {
                $detalleDiario = new Detalle_Diario();
                $detalleDiario->detalle_debe = floatval($fondoreservaacu);
                $detalleDiario->detalle_haber = 0.00;
                $detalleDiario->detalle_comentario =  'Pago del Rol del '.DateTime::createFromFormat('Y-m-d',$fechaini[1])->format('d-m-Y').' al '.DateTime::createFromFormat('Y-m-d',$request->get('fechafinal'))->format('d-m-Y');
                $detalleDiario->detalle_tipo_documento = 'CONTABILIZACION MENSUAL';
                $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                $detalleDiario->detalle_conciliacion = '0';
                $detalleDiario->detalle_estado = '1';
                $tipo=Empleado::EmpleadoBusquedaCuenta($idempleado, 'fondoReserva')->first();
                $detalleDiario->cuenta_id = $tipo->cuenta_debe;
                $detalleDiario->empleado_id = $idempleado;
                $diariocontabilizado->detalles()->save($detalleDiario);
                $general->registrarAuditoria('Registro de Detalle de Diario codigo: -> '.$diario->diario_codigo, '0', 'En la cuenta del Debe -> '.$tipo->cuenta_debe.' con el valor de: -> '.$Dcuarto);
         
                $detalleDiario = new Detalle_Diario();
                $detalleDiario->detalle_debe = 0.00;
                $detalleDiario->detalle_haber = floatval($fondoreservaacu);
                $detalleDiario->detalle_comentario =  'Pago del Rol del '.DateTime::createFromFormat('Y-m-d',$fechaini[1])->format('d-m-Y').' al '.DateTime::createFromFormat('Y-m-d',$request->get('fechafinal'))->format('d-m-Y');
                $detalleDiario->detalle_tipo_documento = 'CONTABILIZACION MENSUAL';
                $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                $detalleDiario->detalle_conciliacion = '0';
                $detalleDiario->detalle_estado = '1';
                $tipo=Empleado::EmpleadoBusquedaCuenta($idempleado, 'fondoReserva')->first();
                $detalleDiario->cuenta_id = $tipo->cuenta_haber;
                $detalleDiario->empleado_id = $idempleado;
                $diariocontabilizado->detalles()->save($detalleDiario);
                $general->registrarAuditoria('Registro de Detalle de Diario codigo: -> '.$diario->diario_codigo, '0', 'En la cuenta del Haber -> '.$tipo->cuenta_haber.' con el valor de: -> '.$Dcuarto);
            }
                $detalleDiario = new Detalle_Diario();
                $detalleDiario->detalle_debe = 0.00;
                $detalleDiario->detalle_haber = ($Dingreso-$Degreso)+$Dtercero+$Dcuarto+$fondoreserva+$viaticos;
                $detalleDiario->detalle_comentario =  'Pago del Rol del '.DateTime::createFromFormat('Y-m-d',$fechaini[1])->format('d-m-Y').' al '.DateTime::createFromFormat('Y-m-d',$request->get('fechafinal'))->format('d-m-Y');
                $detalleDiario->detalle_tipo_documento = 'CONTABILIZACION MENSUAL';
                $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                $detalleDiario->detalle_conciliacion = '0';
                $detalleDiario->detalle_estado = '1';
                $tipo=Empleado::EmpleadoBusquedaCuenta($idempleado, 'sueldos')->first();
                $detalleDiario->cuenta_id = $tipo->cuenta_haber;
                $detalleDiario->empleado_id = $idempleado;
                $diariocontabilizado->detalles()->save($detalleDiario);
                $general->registrarAuditoria('Registro de Detalle de Diario codigo: -> '.$diario->diario_codigo, '0', 'En la cuenta del Haber -> '.$tipo->cuenta_haber.' con el valor de: -> '.$Dcuarto);


                
                for ($k = 1; $k <= count($matriz); ++$k)  {
                    if($matriz[$k]["tipo"]=="DEBE"){
                        $detalleDiario = new Detalle_Diario();
                        $detalleDiario->detalle_debe =  $matriz[$k]["debe"];
                        $detalleDiario->detalle_haber = 0.00;
                        $detalleDiario->detalle_comentario =  'Pago del Rol del '.DateTime::createFromFormat('Y-m-d',$fechaini[1])->format('d-m-Y').' al '.DateTime::createFromFormat('Y-m-d',$request->get('fechafinal'))->format('d-m-Y');
                        $detalleDiario->detalle_tipo_documento = 'CONTABILIZACION MENSUAL';
                        $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                        $detalleDiario->detalle_conciliacion = '0';
                        $detalleDiario->detalle_estado = '1';          
                        $detalleDiario->cuenta_id = $matriz[$k]["idcuenta"];
                        $detalleDiario->empleado_id = $idempleado;
                        $diariocontabilizado->detalles()->save($detalleDiario);
                        $general->registrarAuditoria('Registro de Detalle de Diario codigo: -> '.$diario->diario_codigo, '0', 'En la cuenta del Debe -> '.$matriz[$k]["idcuenta"].' con el valor de: -> '. $matriz[$k]["debe"]);
        
                    }
                    if($matriz[$k]["tipo"]=="HABER"){
                        $detalleDiario = new Detalle_Diario();
                        $detalleDiario->detalle_debe =  0.00;
                        $detalleDiario->detalle_haber =  $matriz[$k]["haber"];
                        $detalleDiario->detalle_comentario =  'Pago del Rol del '.DateTime::createFromFormat('Y-m-d',$fechaini[1])->format('d-m-Y').' al '.DateTime::createFromFormat('Y-m-d',$request->get('fechafinal'))->format('d-m-Y');
                        $detalleDiario->detalle_tipo_documento = 'CONTABILIZACION MENSUAL';
                        $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                        $detalleDiario->detalle_conciliacion = '0';
                        $detalleDiario->detalle_estado = '1';          
                        $detalleDiario->cuenta_id = $matriz[$k]["idcuenta"];
                        $detalleDiario->empleado_id = $idempleado;
                        $diariocontabilizado->detalles()->save($detalleDiario);
                        $general->registrarAuditoria('Registro de Detalle de Diario codigo: -> '.$diario->diario_codigo, '0', 'En la cuenta del Haber -> '.$matriz[$k]["idcuenta"].' con el valor de: -> '. $matriz[$k]["haber"]);
        
                    }
                }
            $url = $general->pdfDiarioEgreso($diario);
            $url3 = $general->pdfDiario($diariocontabilizado);
            $cabecera=Cabecera_Rol_CM::findOrFail($cabecera_rol->cabecera_rol_id);
            $url2 = $general->pdfRolCm($cabecera);
            if ($request->get('tipo') == 'Cheque') {
                DB::commit();
                return redirect('/rolindividualCM/new/'.$request->get('punto_id'))->with('success','Datos guardados exitosament')->with('pdf',$url2)->with('diario',$url3)->with('pdf2',$url)->with('cheque',$urlcheque);
            }
            DB::commit();
            return redirect('/rolindividualCM/new/'.$request->get('punto_id'))->with('success','Datos guardados exitosamente')->with('pdf',$url2)->with('diario',$url3)->with('pdf2',$url);      
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('/rolindividualCM/new/'.$request->get('punto_id'))->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
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
        try {
            DB::beginTransaction();
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
            $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $tipopago=null;
            $datos=null;
            $detalles=null;
            $alimentacion=null;
            $anticipo=null;
            $quincenas=null;
            $rol2= Cabecera_Rol_CM::findOrFail($id);
            $count=1;
            $datos[$count]['rol_id']=$rol2->cabecera_rol_id;
            $datos[$count]['empleado']=$rol2->empleado_id;
            $datos[$count]['ingresos']=$rol2->cabecera_rol_total_ingresos;
            $datos[$count]['egresos']=$rol2->cabecera_rol_total_egresos;

            $datos[$count]['pago']=$rol2->cabecera_rol_pago;
            $datos[$count]['cuarto']=$rol2->cabecera_rol_decimocuarto;
        
            $datos[$count]['tercero']=$rol2->cabecera_rol_decimotercero;
            $datos[$count]['fondos']=$rol2->cabecera_rol_fondo_reserva;
            $datos[$count]['viaticos']=$rol2->cabecera_rol_viaticos;
            $datos[$count]['secap']=$rol2->cabecera_rol_aporte_patronal;
            $datos[$count]['patronal']=$rol2->cabecera_rol_aporte_patronal;
            $datos[$count]['terceroacu']=$rol2->cabecera_rol_decimotercero_acumula;
            $datos[$count]['cuartoacu']=$rol2->cabecera_rol_decimocuarto_acumula;
            $datos[$count]['fondosacu']=$rol2->cabecera_rol_fr_acumula;
            $datos[$count]['dias']=$rol2->cabecera_rol_total_dias;
            $datos[$count]['fondosacu']=$rol2->cabecera_rol_fr_acumula;
            $datos[$count]['vacaciones']=$rol2->cabecera_rol_vacaciones;
           

            
            $count=1;
            foreach ($rol2->alimentacioncm as $alimentaciones) {
                $alimentacion[$count]['fecha']=$alimentaciones->alimentacion_fecha;
                $alimentacion[$count]['valor']=$alimentaciones->alimentacion_valor;
                $alimentacion[$count]['factura']=$alimentaciones->transaccion->transaccion_numero;
                $count++;
            }
            $count=1;
            foreach ($rol2->anticiposcm as $anticipos) {
                $anticipo[$count]['descuento_fecha']=$anticipos->anticipo->anticipo_fecha;
                $anticipo[$count]['descuento_valor']=$anticipos->descuento_valor;
                $anticipo[$count]['Valor_Anticipó']=$anticipos->anticipo->anticipo_valor;
                $count++;
            }
            $count=1;
            foreach ($rol2->quincenacm as $quincena) {
                $quincenas[$count]['descuento_fecha']=$quincena->quincena->quincena_fecha;
                $quincenas[$count]['descuento_valor']=$quincena->descuento_valor;
                $quincenas[$count]['Valor_Anticipó']=$quincena->quincena->quincena_valor;
                $count++;
            }
            $count=1;
            $tipopago[$count]['tipo']='OTROS';
          
            if(isset($rol2->diariopago->movimientocaja)){
                $tipopago[$count]['tipo']='EFECTIVO';
                $tipopago[$count]['banco']=$rol2->diariopago->movimientocaja->arqueo->caja->caja_nombre;
            }
            foreach($rol2->diariopago->detalles as $detalle){
                if (isset($detalle->cheque)) {
                    $tipopago[$count]['iddetalle']=$detalle->detalle_id; 
                    $tipopago[$count]['tipo']="Cheque";
                    $tipopago[$count]['idcheque']=$detalle->cheque->cheque_id;
                    $tipopago[$count]['cheque']=$detalle->cheque->cheque_numero;
                    $tipopago[$count]['fecha']=$detalle->cheque->cheque_fecha_pago;
                    $tipopago[$count]['numero']=$detalle->cheque->cuentaBancaria->cuenta_bancaria_numero;
                    $tipopago[$count]['banco']=$detalle->cheque->cuentaBancaria->banco->bancoLista->banco_lista_nombre;
                
                }
                if (isset($detalle->transferencia)) {
                    $tipopago[$count]['iddetalle']=$detalle->detalle_id; 
                    $tipopago[$count]['tipo']="Transferencia";
                    $tipopago[$count]['numero']=$detalle->transferencia->cuentaBancaria->cuenta_bancaria_numero;
                    $tipopago[$count]['banco']=$detalle->transferencia->cuentaBancaria->banco->bancoLista->banco_lista_nombre;
                
                }
                
            }
            if($tipopago[$count]['tipo']=='OTROS'){
                foreach ($rol2->diariopago->detalles as $detalle) {
                    if($detalle->detalle_haber>0){
                        $cuentat=Tipo_Movimiento_Empleado::TipoCuenta($detalle->cuenta_id)->first();
                        $tipopago[$count]['banco']=$cuentat->tipo_nombre;
                    }
                
                }
            }
        
        $count=1;
        $rubros=Rubro::Rubros()->get();
        foreach ($rubros as $rubro) {
            $detalles[$count]['identificacion']=$rubro->rubro_nombre;
            $detalles[$count]['fechaincio']='';
            $detalles[$count]['fechafin']='';
            $detalles[$count]['Descripcion']=$rubro->rubro_descripcion;
            $detalles[$count]['Valor']='0.00';
            $detalles[$count]['Tipo']=$rubro->rubro_tipo;
            foreach ($rol2->detalles as $detalle) {
                if ($rubro->rubro_id==$detalle->rubro_id) {
                    $detalles[$count]['fechaincio']=$detalle->detalle_rol_fecha_inicio;
                    $detalles[$count]['fechafin']=$detalle->detalle_rol_fecha_fin;
                    $detalles[$count]['Descripcion']=$detalle->detalle_rol_descripcion;
                    $detalles[$count]['Valor']=$detalle->detalle_rol_valor;
                }
            }
            $count++;
        }
        DB::commit();
        return view('admin.RHCostaMarket.rolIndividual.ver',['quincenas'=>$quincenas,'detalles'=>$detalles,'tipopago'=>$tipopago,'anticipo'=>$anticipo,'alimentacion'=>$alimentacion,'datos'=>$datos,'rol'=>$rol2,'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('/listaRolCM')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function cambiocheque($id)
    {
        try {
            DB::beginTransaction();
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $tipopago=null;
            $datos=null;
            $detalles=null;
            $alimentacion=null;
            $anticipo=null;
            $quincenas=null;
            $rol2= Cabecera_Rol_CM::findOrFail($id);
            $count=1;
            $datos[$count]['rol_id']=$rol2->cabecera_rol_id;
            $datos[$count]['empleado']=$rol2->empleado_id;
            $datos[$count]['ingresos']=$rol2->cabecera_rol_total_ingresos;
            $datos[$count]['egresos']=$rol2->cabecera_rol_total_egresos;

            $datos[$count]['pago']=$rol2->cabecera_rol_pago;
            $datos[$count]['cuarto']=$rol2->cabecera_rol_decimocuarto;
        
            $datos[$count]['tercero']=$rol2->cabecera_rol_decimotercero;
            $datos[$count]['fondos']=$rol2->cabecera_rol_fondo_reserva;
            $datos[$count]['viaticos']=$rol2->cabecera_rol_viaticos;
            $datos[$count]['secap']=$rol2->cabecera_rol_aporte_patronal;
            $datos[$count]['patronal']=$rol2->cabecera_rol_aporte_patronal;
            $datos[$count]['terceroacu']=$rol2->cabecera_rol_decimotercero_acumula;
            $datos[$count]['cuartoacu']=$rol2->cabecera_rol_decimocuarto_acumula;
            $datos[$count]['fondosacu']=$rol2->cabecera_rol_fr_acumula;
            $datos[$count]['dias']=$rol2->cabecera_rol_total_dias;
            $datos[$count]['fondosacu']=$rol2->cabecera_rol_fr_acumula;
            $datos[$count]['vacaciones']=$rol2->cabecera_rol_vacaciones;
            $datos[$count]['descripcion']=$rol2->cabecera_rol_descripcion;

            
            $count=1;
            foreach ($rol2->alimentacioncm as $alimentaciones) {
                $alimentacion[$count]['fecha']=$alimentaciones->alimentacion_fecha;
                $alimentacion[$count]['valor']=$alimentaciones->alimentacion_valor;
                $alimentacion[$count]['factura']=$alimentaciones->transaccion->transaccion_numero;
                $count++;
            }
            $count=1;
            foreach ($rol2->anticiposcm as $anticipos) {
                $anticipo[$count]['descuento_fecha']=$anticipos->anticipo->anticipo_fecha;
                $anticipo[$count]['descuento_valor']=$anticipos->descuento_valor;
                $anticipo[$count]['Valor_Anticipó']=$anticipos->anticipo->anticipo_valor;
                $count++;
            }
            $count=1;
            foreach ($rol2->quincenacm as $quincena) {
                $quincenas[$count]['descuento_fecha']=$quincena->quincena->quincena_fecha;
                $quincenas[$count]['descuento_valor']=$quincena->descuento_valor;
                $quincenas[$count]['Valor_Anticipó']=$quincena->quincena->quincena_valor;
                $count++;
            }
            $count=1;
            
            $datos[$count]['tipo']="Efectivo";
            foreach($rol2->diariopago->detalles as $detalle){
                if ($detalle->detalle_haber>0) {
                    $datos[$count]['iddetalle']=$detalle->detalle_id;
                    if (isset($detalle->cheque)) {
                       
                        $datos[$count]['tipo']="Cheque";
                        $datos[$count]['idcheque']=$detalle->cheque->cheque_id;
                        $datos[$count]['cheque']=$detalle->cheque->cheque_numero;
                        $datos[$count]['fecha']=$detalle->cheque->cheque_fecha_pago;
                        $datos[$count]['numero']=$detalle->cheque->cuentaBancaria->cuenta_bancaria_numero;
                        $datos[$count]['banco']=$detalle->cheque->cuentaBancaria->banco->bancoLista->banco_lista_nombre;
                        $count++;
                    }
                    if (isset($detalle->transferencia)) {
                       
                        $datos[$count]['tipo']="Transferencia";
                        $datos[$count]['numero']=$detalle->transferencia->cuentaBancaria->cuenta_bancaria_numero;
                        $datos[$count]['banco']=$detalle->transferencia->cuentaBancaria->banco->bancoLista->banco_lista_nombre;
                
                        $count++;
                    }
                }
            }
        $count=1;
        $rubros=Rubro::Rubros()->get();
        foreach ($rubros as $rubro) {
            $detalles[$count]['identificacion']=$rubro->rubro_nombre;
            $detalles[$count]['fechaincio']='';
            $detalles[$count]['fechafin']='';
            $detalles[$count]['Descripcion']=$rubro->rubro_descripcion;
            $detalles[$count]['Valor']='0.00';
            $detalles[$count]['Tipo']=$rubro->rubro_tipo;
            foreach ($rol2->detalles as $detalle) {
                if ($rubro->rubro_id==$detalle->rubro_id) {
                    $detalles[$count]['fechaincio']=$detalle->detalle_rol_fecha_inicio;
                    $detalles[$count]['fechafin']=$detalle->detalle_rol_fecha_fin;
                    $detalles[$count]['Descripcion']=$detalle->detalle_rol_descripcion;
                    $detalles[$count]['Valor']=$detalle->detalle_rol_valor;
                }
            }
            $count++;
        }
        DB::commit();
        return view('admin.RHCostaMarket.rolIndividual.cambiocheque',['quincenas'=>$quincenas,'detalles'=>$detalles,'tipopago'=>$tipopago,'anticipo'=>$anticipo,'alimentacion'=>$alimentacion,'datos'=>$datos,'rol'=>$rol2,'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('/listaRolCM')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function eliminar($id){
        try {
            DB::beginTransaction();
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
            $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $tipopago=null;
            $datos=null;
            $detalles=null;
            $alimentacion=null;
            $anticipo=null;
            $quincenas=null;
            $rol2= Cabecera_Rol_CM::findOrFail($id);
            $count=1;
            $datos[$count]['rol_id']=$rol2->cabecera_rol_id;
            $datos[$count]['empleado']=$rol2->empleado_id;
            $datos[$count]['ingresos']=$rol2->cabecera_rol_total_ingresos;
            $datos[$count]['egresos']=$rol2->cabecera_rol_total_egresos;

            $datos[$count]['pago']=$rol2->cabecera_rol_pago;
            $datos[$count]['cuarto']=$rol2->cabecera_rol_decimocuarto;
        
            $datos[$count]['tercero']=$rol2->cabecera_rol_decimotercero;
            $datos[$count]['fondos']=$rol2->cabecera_rol_fondo_reserva;
            $datos[$count]['viaticos']=$rol2->cabecera_rol_viaticos;
            $datos[$count]['secap']=$rol2->cabecera_rol_aporte_patronal;
            $datos[$count]['patronal']=$rol2->cabecera_rol_aporte_patronal;
            $datos[$count]['terceroacu']=$rol2->cabecera_rol_decimotercero_acumula;
            $datos[$count]['cuartoacu']=$rol2->cabecera_rol_decimocuarto_acumula;
            $datos[$count]['fondosacu']=$rol2->cabecera_rol_fr_acumula;
            $datos[$count]['dias']=$rol2->cabecera_rol_total_dias;
            $datos[$count]['fondosacu']=$rol2->cabecera_rol_fr_acumula;
            $datos[$count]['vacaciones']=$rol2->cabecera_rol_vacaciones;
           

            
            $count=1;
            foreach ($rol2->alimentacioncm as $alimentaciones) {
                $alimentacion[$count]['fecha']=$alimentaciones->alimentacion_fecha;
                $alimentacion[$count]['valor']=$alimentaciones->alimentacion_valor;
                $alimentacion[$count]['factura']=$alimentaciones->transaccion->transaccion_numero;
                $count++;
            }
            $count=1;
            foreach ($rol2->anticiposcm as $anticipos) {
                $anticipo[$count]['descuento_fecha']=$anticipos->descuento_fecha;
                $anticipo[$count]['descuento_valor']=$anticipos->descuento_valor;
                $anticipo[$count]['Valor_Anticipó']=$anticipos->anticipo->anticipo_valor;
                $count++;
            }
            $count=1;
            foreach ($rol2->quincenacm as $quincena) {
                $quincenas[$count]['descuento_fecha']=$quincena->descuento_fecha;
                $quincenas[$count]['descuento_valor']=$quincena->descuento_valor;
                $quincenas[$count]['Valor_Anticipó']=$quincena->quincena->quincena_valor;
                $count++;
            }
            $count=1;
            $tipopago[$count]['tipo']='OTROS';
           
            if(isset($rol2->diariopago->movimientocaja)){
                $tipopago[$count]['tipo']='EFECTIVO';
                $tipopago[$count]['banco']=$rol2->diariopago->movimientocaja->arqueo->caja->caja_nombre;
            }
            foreach($rol2->diariopago->detalles as $detalle){
            if (isset($detalle->cheque)) {
                $tipopago[$count]['iddetalle']=$detalle->detalle_id; 
                $tipopago[$count]['tipo']="Cheque";
                $tipopago[$count]['idcheque']=$detalle->cheque->cheque_id;
                $tipopago[$count]['cheque']=$detalle->cheque->cheque_numero;
                $tipopago[$count]['fecha']=$detalle->cheque->cheque_fecha_pago;
                $tipopago[$count]['numero']=$detalle->cheque->cuentaBancaria->cuenta_bancaria_numero;
                $tipopago[$count]['banco']=$detalle->cheque->cuentaBancaria->banco->bancoLista->banco_lista_nombre;
              
            }
            if (isset($detalle->transferencia)) {
                $tipopago[$count]['iddetalle']=$detalle->detalle_id; 
                $tipopago[$count]['tipo']="Transferencia";
                $tipopago[$count]['numero']=$detalle->transferencia->cuentaBancaria->cuenta_bancaria_numero;
                $tipopago[$count]['banco']=$detalle->transferencia->cuentaBancaria->banco->bancoLista->banco_lista_nombre;
               
            }
            
        }
        if($tipopago[$count]['tipo']=='OTROS'){
            foreach ($rol2->diariopago->detalles as $detalle) {
                if($detalle->detalle_haber>0){
                    $cuentat=Tipo_Movimiento_Empleado::TipoCuenta($detalle->cuenta_id)->first();
                    $tipopago[$count]['banco']=$cuentat->tipo_nombre;
                }
               
            }
        }
        $count=1;
        $rubros=Rubro::Rubros()->get();
        foreach ($rubros as $rubro) {
            $detalles[$count]['identificacion']=$rubro->rubro_nombre;
            $detalles[$count]['fechaincio']='';
            $detalles[$count]['fechafin']='';
            $detalles[$count]['Descripcion']=$rubro->rubro_descripcion;
            $detalles[$count]['Valor']='0.00';
            $detalles[$count]['Tipo']=$rubro->rubro_tipo;
            foreach ($rol2->detalles as $detalle) {
                if ($rubro->rubro_id==$detalle->rubro_id) {
                    $detalles[$count]['fechaincio']=$detalle->detalle_rol_fecha_inicio;
                    $detalles[$count]['fechafin']=$detalle->detalle_rol_fecha_fin;
                    $detalles[$count]['Descripcion']=$detalle->detalle_rol_descripcion;
                    $detalles[$count]['Valor']=$detalle->detalle_rol_valor;
                }
            }
            $count++;
        }
        DB::commit();
        return view('admin.RHCostaMarket.rolIndividual.eliminar',['quincenas'=>$quincenas,'detalles'=>$detalles,'tipopago'=>$tipopago,'anticipo'=>$anticipo,'alimentacion'=>$alimentacion,'datos'=>$datos,'rol'=>$rol2,'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('/listaRolCM')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        } 

    }
    public function anular($id){
        try {
            DB::beginTransaction();
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
            $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $tipopago=null;
            $datos=null;
            $detalles=null;
            $alimentacion=null;
            $anticipo=null;
            $quincenas=null;
            $rol2= Cabecera_Rol_CM::findOrFail($id);
            $count=1;
            $datos[$count]['rol_id']=$rol2->cabecera_rol_id;
            $datos[$count]['empleado']=$rol2->empleado_id;
            $datos[$count]['ingresos']=$rol2->cabecera_rol_total_ingresos;
            $datos[$count]['egresos']=$rol2->cabecera_rol_total_egresos;

            $datos[$count]['pago']=$rol2->cabecera_rol_pago;
            $datos[$count]['cuarto']=$rol2->cabecera_rol_decimocuarto;
        
            $datos[$count]['tercero']=$rol2->cabecera_rol_decimotercero;
            $datos[$count]['fondos']=$rol2->cabecera_rol_fondo_reserva;
            $datos[$count]['viaticos']=$rol2->cabecera_rol_viaticos;
            $datos[$count]['secap']=$rol2->cabecera_rol_aporte_patronal;
            $datos[$count]['patronal']=$rol2->cabecera_rol_aporte_patronal;
            $datos[$count]['terceroacu']=$rol2->cabecera_rol_decimotercero_acumula;
            $datos[$count]['cuartoacu']=$rol2->cabecera_rol_decimocuarto_acumula;
            $datos[$count]['fondosacu']=$rol2->cabecera_rol_fr_acumula;
            $datos[$count]['dias']=$rol2->cabecera_rol_total_dias;
            $datos[$count]['fondosacu']=$rol2->cabecera_rol_fr_acumula;
            $datos[$count]['vacaciones']=$rol2->cabecera_rol_vacaciones;
           

            
            $count=1;
            foreach ($rol2->alimentacioncm as $alimentaciones) {
                $alimentacion[$count]['fecha']=$alimentaciones->alimentacion_fecha;
                $alimentacion[$count]['valor']=$alimentaciones->alimentacion_valor;
                $alimentacion[$count]['factura']=$alimentaciones->transaccion->transaccion_numero;
                $count++;
            }
            $count=1;
            foreach ($rol2->anticiposcm as $anticipos) {
                $anticipo[$count]['descuento_fecha']=$anticipos->descuento_fecha;
                $anticipo[$count]['descuento_valor']=$anticipos->descuento_valor;
                $anticipo[$count]['Valor_Anticipó']=$anticipos->anticipo->anticipo_valor;
                $count++;
            }
            $count=1;
            foreach ($rol2->quincenacm as $quincena) {
                $quincenas[$count]['descuento_fecha']=$quincena->descuento_fecha;
                $quincenas[$count]['descuento_valor']=$quincena->descuento_valor;
                $quincenas[$count]['Valor_Anticipó']=$quincena->quincena->quincena_valor;
                $count++;
            }
            $count=1;
            $tipopago[$count]['tipo']='OTROS';
           
            if(isset($rol2->diariopago->movimientocaja)){
                $tipopago[$count]['tipo']='EFECTIVO';
                $tipopago[$count]['banco']=$rol2->diariopago->movimientocaja->arqueo->caja->caja_nombre;
            }
            foreach($rol2->diariopago->detalles as $detalle){
            if (isset($detalle->cheque)) {
                $tipopago[$count]['iddetalle']=$detalle->detalle_id; 
                $tipopago[$count]['tipo']="Cheque";
                $tipopago[$count]['idcheque']=$detalle->cheque->cheque_id;
                $tipopago[$count]['cheque']=$detalle->cheque->cheque_numero;
                $tipopago[$count]['fecha']=$detalle->cheque->cheque_fecha_pago;
                $tipopago[$count]['numero']=$detalle->cheque->cuentaBancaria->cuenta_bancaria_numero;
                $tipopago[$count]['banco']=$detalle->cheque->cuentaBancaria->banco->bancoLista->banco_lista_nombre;
              
            }
            if (isset($detalle->transferencia)) {
                $tipopago[$count]['iddetalle']=$detalle->detalle_id; 
                $tipopago[$count]['tipo']="Transferencia";
                $tipopago[$count]['numero']=$detalle->transferencia->cuentaBancaria->cuenta_bancaria_numero;
                $tipopago[$count]['banco']=$detalle->transferencia->cuentaBancaria->banco->bancoLista->banco_lista_nombre;
               
            }
            
        }
        if($tipopago[$count]['tipo']=='OTROS'){
            foreach ($rol2->diariopago->detalles as $detalle) {
                if($detalle->detalle_haber>0){
                    $cuentat=Tipo_Movimiento_Empleado::TipoCuenta($detalle->cuenta_id)->first();
                    $tipopago[$count]['banco']=$cuentat->tipo_nombre;
                }
               
            }
        }
        $count=1;
        $rubros=Rubro::Rubros()->get();
        foreach ($rubros as $rubro) {
            $detalles[$count]['identificacion']=$rubro->rubro_nombre;
            $detalles[$count]['fechaincio']='';
            $detalles[$count]['fechafin']='';
            $detalles[$count]['Descripcion']=$rubro->rubro_descripcion;
            $detalles[$count]['Valor']='0.00';
            $detalles[$count]['Tipo']=$rubro->rubro_tipo;
            foreach ($rol2->detalles as $detalle) {
                if ($rubro->rubro_id==$detalle->rubro_id) {
                    $detalles[$count]['fechaincio']=$detalle->detalle_rol_fecha_inicio;
                    $detalles[$count]['fechafin']=$detalle->detalle_rol_fecha_fin;
                    $detalles[$count]['Descripcion']=$detalle->detalle_rol_descripcion;
                    $detalles[$count]['Valor']=$detalle->detalle_rol_valor;
                }
            }
            $count++;
        }
        DB::commit();
        return view('admin.RHCostaMarket.rolIndividual.anular',['quincenas'=>$quincenas,'detalles'=>$detalles,'tipopago'=>$tipopago,'anticipo'=>$anticipo,'alimentacion'=>$alimentacion,'datos'=>$datos,'rol'=>$rol2,'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('/listaRolCM')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
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
            $auditoria = new generalController();
            $rol = Cabecera_Rol_CM::findOrFail($id);
            $cierre = $auditoria->cierre($rol->cabecera_rol_fecha,$rol->diariopago->sucursal_id);          
            if($cierre){
                return redirect('listaroles')->with('error2','No puede realizar la operacion por que pertenece a un mes bloqueado');
            }
            $aux=Cabecera_Rol_CM::findOrFail($id);
            $aux->diario_pago_id=null;
            $aux->diario_contabilizacion_id=null;   
            $aux->save();
            $auditoria->registrarAuditoria('Actualziacion de Rol de diarios null para la eliminacion de diario para la eliminacion de rol  con valor '.$rol->cabecera_rol_pago ,'0','');
            
            if (isset($rol->RolMovimientos)) {
                foreach ($rol->RolMovimientos as $movimiento) {
                    $movi = Rol_Movimiento::findOrFail($movimiento->rol_movimiento_id);
                    $movi->cabecera_rol_cm_id=null;
                    $movi->rol_movimiento_estado='1';
                    $movi->save();
                }
            }
            if (isset($rol->anticiposcm)) {
                foreach ($rol->anticiposcm as $detalle) {
                    $anticipos = Descuento_Anticipo_Empleado::findOrFail($detalle->descuento_id);
                    $anticipo = Anticipo_Empleado::findOrFail($anticipos->anticipo_id);
                    $anticipoaux = Anticipo_Empleado::findOrFail($anticipos->anticipo_id);
                    $anticipos->delete();
                    $auditoria->registrarAuditoria('Eliminacion de Descuento de Anticipos para la eliminacion de rol con valor '.$detalle->descuento_valor ,'0','');
                    if($anticipoaux->anticipo_saldom){
                        $anticipo->anticipo_saldo=$anticipo->anticipo_saldom-(Descuento_Anticipo_Empleado::Anticipos($anticipo->anticipo_id)->sum('descuento_valor'));
                    }else{
                        $anticipo->anticipo_saldo=$anticipo->anticipo_valor-(Descuento_Anticipo_Empleado::Anticipos($anticipo->anticipo_id)->sum('descuento_valor'));
                    }
                    if( $anticipo->anticipo_saldo==0){
                        $anticipo->anticipo_estado='2';
                    }
                    else{
                        $anticipo->anticipo_estado='1';
                    }
                   
                    $anticipo->update();
                 
                    $auditoria->registrarAuditoria('Actualziacion de  Anticipos para la eliminacion de descuento de anticipo para la eliminacion de rol con valor '.$detalle->descuento_valor ,'0','');
    
                }
            }
            if (isset($rol->quincenacm)) {
                foreach ($rol->quincenacm as $detalle) {
                    $quincena = Descuento_Quincena::findOrFail($detalle->descuento_id);
                    $quincenas = Quincena::findOrFail($quincena->quincena_id);
                    $quincena->delete();
                    $auditoria->registrarAuditoria('Eliminacion de Descuento de Anticipos para la eliminacion de rol con valor '.$detalle->descuento_valor ,'0','');
                    
                    $quincenas->quincena_saldo=$quincenas->quincena_valor-(Descuento_Quincena::Anticipos($quincenas->quincena_id)->sum('descuento_valor'));
                    $quincenas->quincena_estado='1';
                    $quincenas->update();
                    $auditoria->registrarAuditoria('Actualziacion de  Anticipos para la eliminacion de descuento de anticipo para la eliminacion de rol con valor '.$detalle->descuento_valor ,'0','');
    
                    
                }
            }
            if (isset($rol->alimentacioncm)) {
                foreach ($rol->alimentacioncm as $alimentacion) {
                    $alimentaciones = Alimentacion::findOrFail($alimentacion->alimentacion_id);
                    $alimentaciones->cabecera_rol_cm_id=null;
                    $alimentaciones->alimentacion_estado='1';
                    $alimentaciones->save();
                    $auditoria->registrarAuditoria('Actualziacion de  Alimentacion a null para la eliminacion de rol ','0','');
                }
            }
            /*
            if (isset($rol->diariopago->movimientocaja)) {
                $movimiento = Movimiento_Caja::findOrFail($rol->diariopago->movimientocaja->movimiento_id);
                $movimiento->delete();
            }
            */
            if (isset($rol->diariopago->diario_id)) {
                foreach ($rol->diariopago->detalles as $detalle) {
                    if (isset($detalle->cheque->cheque_id)) {
                        $cheques = Cheque::findOrFail($detalle->cheque->cheque_id);
                    }
                    if (isset($detalle->transferencia->transferencia_id)) {
                        $transferencias = Transferencia::findOrFail($detalle->transferencia->transferencia_id);
                    }
                }
                foreach ($rol->diariopago->detalles as $detalle) {
                    $detalles = Detalle_Diario::findOrFail($detalle->detalle_id);
                    $detalles->delete();
                    $auditoria->registrarAuditoria('Eliminacion de Detalle diario de pago de Rol tipo-> '.$rol->cabecera_rol_tipo.' del empleado '.$rol->empleado->empleado_nombre.' para la eliminacion de rol con valor '.$rol->cabecera_rol_pago, '0', '');
                }
                if(isset($cheques)){
                    $cheques->delete();
                    $auditoria->registrarAuditoria('Eliminacion de Cheque -> '.$cheques->cheque_numero.' de pago de Rol tipo -> '.$rol->cabecera_rol_tipo.' del empleado '.$rol->empleado->empleado_nombre.' para la eliminacion de rol con valor '.$rol->cabecera_rol_pago, '0', '');
                }
                if(isset($transferencias)){
                    $transferencias->delete();
                    $auditoria->registrarAuditoria('Eliminacion de Tranferencia de pago de Rol tipo -> '.$rol->cabecera_rol_tipo.' del empleado '.$rol->empleado->empleado_nombre.' para la eliminacion de rol con valor '.$rol->cabecera_rol_pago, '0', '');
                }
                $rol->diariopago->delete();
                $auditoria->registrarAuditoria('Eliminacion de Diario de rol de pago tipo-> '.$rol->cabecera_rol_tipo.' del empleado '.$rol->empleado->empleado_nombre.' para la eliminacion de rol con valor '.$rol->cabecera_rol_pago ,'0','');
                
            }
            if (isset($rol->diariocontabilizacion)) {
               
                foreach ($rol->diariocontabilizacion->detalles as $detalle) {
                    $detalles = Detalle_Diario::findOrFail($detalle->detalle_id);
                    $detalles->delete();
                    $auditoria->registrarAuditoria('Eliminacion de Detalle diario contabilizado de pago de Rol tipo-> '.$rol->cabecera_rol_tipo.' del empleado '.$rol->empleado->empleado_nombre.' para la eliminacion de rol con valor '.$rol->cabecera_rol_pago, '0', '');
                }
                $rol->diariocontabilizacion->delete();
                $auditoria->registrarAuditoria('Eliminacion de Diario contabilizado de rol de pago tipo-> '.$rol->cabecera_rol_tipo.' del empleado '.$rol->empleado->empleado_nombre.' para la eliminacion de rol con valor '.$rol->cabecera_rol_pago, '0', '');


            }
            
            foreach ($rol->detalles as $detalle) {
                $detalles = Detalle_Rol_CM::findOrFail($detalle->detalle_rol_id);
                $detalles->delete();
                $auditoria->registrarAuditoria('Eliminacion de Detalle Rol de pago de Rol tipo-> '.$rol->cabecera_rol_tipo.' del empleado '.$rol->empleado->empleado_nombre.' para la eliminacion de rol con valor '.$rol->cabecera_rol_pago ,'0','');
            }
            
            
            $rol->delete();
            $auditoria->registrarAuditoria('Eliminacion de  de rol  tipo -> '.$rol->cabecera_rol_tipo.' del empleado '.$rol->empleado->empleado_nombre.' con valor '.$rol->cabecera_rol_pago ,'0','');
            /*Fin de registro de auditoria */
           
         
            DB::commit();
            return redirect('listaRolCM')->with('success','Datos eliminados exitosamente');
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('/listaRolCM')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }  
    }
    public function anularCheque($id)
    {
        try{
            DB::beginTransaction();
            $auditoria = new generalController();
            $rol = Cabecera_Rol_CM::findOrFail($id);
            $cierre = $auditoria->cierre($rol->cabecera_rol_fecha,$rol->diariopago->sucursal_id);          
            if($cierre){
                return redirect('listaroles')->with('error2','No puede realizar la operacion por que pertenece a un mes bloqueado');
            }
            $aux=Cabecera_Rol_CM::findOrFail($id);
            $aux->diario_pago_id=null;
            $aux->diario_contabilizacion_id=null;   
            $aux->save();
            $auditoria->registrarAuditoria('Actualziacion de Rol de diarios null para la eliminacion de diario para la eliminacion de rol  con valor '.$rol->cabecera_rol_pago ,'0','');
            
            if (isset($rol->RolMovimientos)) {
                foreach ($rol->RolMovimientos as $movimiento) {
                    $movi = Rol_Movimiento::findOrFail($movimiento->rol_movimiento_id);
                    $movi->cabecera_rol_cm_id=null;
                    $movi->rol_movimiento_estado='1';
                    $movi->save();
                }
            }
            if (isset($rol->anticiposcm)) {
                foreach ($rol->anticiposcm as $detalle) {
                    $anticipos = Descuento_Anticipo_Empleado::findOrFail($detalle->descuento_id);
                    $anticipo = Anticipo_Empleado::findOrFail($anticipos->anticipo_id);
                    $anticipoaux = Anticipo_Empleado::findOrFail($anticipos->anticipo_id);
                    $anticipos->delete();
                    $auditoria->registrarAuditoria('Eliminacion de Descuento de Anticipos para la eliminacion de rol con valor '.$detalle->descuento_valor ,'0','');
                    if($anticipoaux->anticipo_saldom){
                        $anticipo->anticipo_saldo=$anticipo->anticipo_saldom-(Descuento_Anticipo_Empleado::Anticipos($anticipo->anticipo_id)->sum('descuento_valor'));
                    }else{
                        $anticipo->anticipo_saldo=$anticipo->anticipo_valor-(Descuento_Anticipo_Empleado::Anticipos($anticipo->anticipo_id)->sum('descuento_valor'));
                    }
                    if( $anticipo->anticipo_saldo==0){
                        $anticipo->anticipo_estado='2';
                    }
                    else{
                        $anticipo->anticipo_estado='1';
                    }
                   
                    $anticipo->update();
                 
                    $auditoria->registrarAuditoria('Actualziacion de  Anticipos para la eliminacion de descuento de anticipo para la eliminacion de rol con valor '.$detalle->descuento_valor ,'0','');
    
                }
            }
            if (isset($rol->quincenacm)) {
                foreach ($rol->quincenacm as $detalle) {
                    $quincena = Descuento_Quincena::findOrFail($detalle->descuento_id);
                    $quincenas = Quincena::findOrFail($quincena->quincena_id);
                    $quincena->delete();
                    $auditoria->registrarAuditoria('Eliminacion de Descuento de Anticipos para la eliminacion de rol con valor '.$detalle->descuento_valor ,'0','');
                    
                    $quincenas->quincena_saldo=$quincenas->quincena_valor-(Descuento_Quincena::Anticipos($quincenas->quincena_id)->sum('descuento_valor'));
                    $quincenas->quincena_estado='1';
                    $quincenas->update();
                    $auditoria->registrarAuditoria('Actualziacion de  Anticipos para la eliminacion de descuento de anticipo para la eliminacion de rol con valor '.$detalle->descuento_valor ,'0','');
    
                    
                }
            }
            if (isset($rol->alimentacioncm)) {
                foreach ($rol->alimentacioncm as $alimentacion) {
                    $alimentaciones = Alimentacion::findOrFail($alimentacion->alimentacion_id);
                    $alimentaciones->cabecera_rol_cm_id=null;
                    $alimentaciones->alimentacion_estado='1';
                    $alimentaciones->save();
                    $auditoria->registrarAuditoria('Actualziacion de  Alimentacion a null para la eliminacion de rol ','0','');
                }
            }
            /*
            if (isset($rol->diariopago->movimientocaja)) {
                $movimiento = Movimiento_Caja::findOrFail($rol->diariopago->movimientocaja->movimiento_id);
                $movimiento->delete();
            }
            */
            if (isset($rol->diariopago->diario_id)) {
                foreach ($rol->diariopago->detalles as $detalle) {
                    if (isset($detalle->cheque->cheque_id)) {
                        $cheques = Cheque::findOrFail($detalle->cheque->cheque_id);
                        $cheques->cheque_estado = '2';
                        $cheques->save();
                        $auditoria->registrarAuditoria('Anulacion de Cheque numero: -> '.$cheques->cheque_numero.' Por el rol del empleado '.$rol->empleado->empleado_nombre,0, 'Con cheque  id -> '.$cheques->cheque_numero.'Con valor de -> '.$cheques->cheque_valor);                   
                    }
                   
                }
                foreach ($rol->diariopago->detalles as $detalle) {
                    $detalles = Detalle_Diario::findOrFail($detalle->detalle_id);
                    $detalles->delete();
                    $auditoria->registrarAuditoria('Eliminacion de Detalle diario de pago de Rol tipo-> '.$rol->cabecera_rol_tipo.' del empleado '.$rol->empleado->empleado_nombre.' para la eliminacion de rol con valor '.$rol->cabecera_rol_pago, '0', '');
                }
                
               
                $rol->diariopago->delete();
                $auditoria->registrarAuditoria('Eliminacion de Diario de rol de pago tipo-> '.$rol->cabecera_rol_tipo.' del empleado '.$rol->empleado->empleado_nombre.' para la eliminacion de rol con valor '.$rol->cabecera_rol_pago ,'0','');
                
            }
            if (isset($rol->diariocontabilizacion)) {
               
                foreach ($rol->diariocontabilizacion->detalles as $detalle) {
                    $detalles = Detalle_Diario::findOrFail($detalle->detalle_id);
                    $detalles->delete();
                    $auditoria->registrarAuditoria('Eliminacion de Detalle diario contabilizado de pago de Rol tipo-> '.$rol->cabecera_rol_tipo.' del empleado '.$rol->empleado->empleado_nombre.' para la eliminacion de rol con valor '.$rol->cabecera_rol_pago, '0', '');
                }
                $rol->diariocontabilizacion->delete();
                $auditoria->registrarAuditoria('Eliminacion de Diario contabilizado de rol de pago tipo-> '.$rol->cabecera_rol_tipo.' del empleado '.$rol->empleado->empleado_nombre.' para la eliminacion de rol con valor '.$rol->cabecera_rol_pago, '0', '');


            }
            
            foreach ($rol->detalles as $detalle) {
                $detalles = Detalle_Rol_CM::findOrFail($detalle->detalle_rol_id);
                $detalles->delete();
                $auditoria->registrarAuditoria('Eliminacion de Detalle Rol de pago de Rol tipo-> '.$rol->cabecera_rol_tipo.' del empleado '.$rol->empleado->empleado_nombre.' para la eliminacion de rol con valor '.$rol->cabecera_rol_pago ,'0','');
            }
            
            
            $rol->delete();
            $auditoria->registrarAuditoria('Eliminacion de  de rol  tipo -> '.$rol->cabecera_rol_tipo.' del empleado '.$rol->empleado->empleado_nombre.' con valor '.$rol->cabecera_rol_pago ,'0','');
            /*Fin de registro de auditoria */
           
         
            DB::commit();
            return redirect('listaRolCM')->with('success','Datos eliminados exitosamente');
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('/listaRolCM')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }  
    }
}
