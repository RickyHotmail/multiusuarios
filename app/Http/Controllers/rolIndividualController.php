<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Alimentacion;
use App\Models\Anticipo_Empleado;
use App\Models\Banco;
use App\Models\Categoria_Producto;
use App\Models\Centro_Consumo;
use App\Models\Cheque;
use App\Models\Cuenta_Bancaria;
use App\Models\Descuento_Anticipo_Empleado;
use App\Models\Detalle_Diario;
use App\Models\Detalle_Rol;
use App\Models\Diario;
use App\Models\Empleado;
use App\Models\Parametrizacion_Contable;
use App\Models\Punto_Emision;
use App\Models\Quincena;
use App\Models\Rango_Documento;
use App\Models\Rol_Consolidado;
use App\Models\Transferencia;
use App\Models\Vacacion;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Luecano\NumeroALetras\NumeroALetras;
use Maatwebsite\Excel\Exceptions\LaravelExcelException;

class rolIndividualController extends Controller
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
            return view('admin.recursosHumanos.rolIndividual.index',['consumo'=>Centro_Consumo::CentroConsumos()->get(),'categoria'=>Categoria_Producto::Categorias()->get(),'empleados'=>$empleados,'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
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
                $empleados=Empleado::EmpleadosBySucursal($rangoDocumento->puntoEmision->sucursal_id)->get();
            return view('admin.recursosHumanos.rolIndividual.index',['rangoDocumento'=>$rangoDocumento,'consumo'=>Centro_Consumo::CentroConsumos()->get(),'categoria'=>Categoria_Producto::Categorias()->get(),'empleados'=>$empleados,'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
 
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
    public function store(Request $request)
    {
        try {
            setlocale(LC_TIME, 'spanish');
            
            
            
            $urlcheque = '';
            $idempleado=($request->get('idempleado'));
            $iddescuento=($request->get('IDE'));
            $descontar=($request->get('TDescont'));
            $saldo=($request->get('TSaldo'));
            $idadelantos=$request->get('check');
           
            $Cdias = $request->get('Tdias');
            
            $Dsueldo = $request->get('TCSueldo');
            $Dfechaini = $request->get('Tdesde');
            $Dfechafin = $request->get('Thasta');
            $Dextras = $request->get('Textras');
            $Dhoras_suplementarias = $request->get('Thoras_suplementarias');
            $Dotrosbon = $request->get('Totrosbon');
            $Dotrosin = $request->get('Totrosin');
            $Dmultas = $request->get('Tmultas');
            $Dalimentacion = $request->get('alimentacion');
           
            $Dppqq = $request->get('Tppqq');
            $Dhipotecarios = $request->get('Thipotecarios');
            $Dprestamos = $request->get('Tprestamos');
            $Dtransporte = $request->get('Ttransporte');
            $Dsalud = $request->get('Tsalud');
            $Dleysol = $request->get('Tley_salud');
            $Dporcentaje = $request->get('porcentaje');
            $Dotrosegre = $request->get('Totrosegre');
            $Dvacaciones = $request->get('Tvacaciones');
            $total = 0;

            $anticipos = floatval($request->get('adelanto'));
            $Iesspatronal = $request->get('Totalpatronal');
            $fondoreserva = $request->get('TFondo');
            $Iessasu = $request->get('Tasumido');
            $Iess =$request->get('Tiess');
            $IECESECAP = $request->get('TotalIESCAP');
            $Dtercero = $request->get('TTercero');
            $Dcuarto = $request->get('TCuarto');
            $Dinpuesto = $request->get('Trenta');

            $Dterceroacu = $request->get('TTerceroacu');
            $Dcuartoacu = $request->get('TCuartoacu');
            $fondoacu = $request->get('TFondoacu');

            $Dingreso = $request->get('TTingresos');
            $Degreso = $request->get('totalegre');

            $quincena = floatval($request->get('vquincena'));
            $Totalvacacion = floatval($request->get('Vac_pagadas'));

            $idvacacion = ($request->get('idvacacion'));
           

            if ($request->get('tipo') == 'Cheque') {
                $cuenta_id=$request->get('idCuentaContable_cheque');
                $cuentabanca=$request->get('cuenta_id_cheque');
                $banco=Banco::findOrFail($request->get('banco_id_cheque'));
            }
            if ($request->get('tipo') == 'Transferencia') {
                $cuenta_id=$request->get('idCuentaContable_transfer');
                $cuentabanca=$request->get('cuenta_id_transfer');
                $banco=Banco::findOrFail($request->get('banco_id_transfer'));
            }
            $cuentabancaria=Cuenta_Bancaria::findOrFail($cuentabanca);
            $general = new generalController();
            $tipo=Empleado::EmpleadoBusquedaCuenta($idempleado, 'sueldos')->first();
            $cierre = $general->cierre($request->get('fechafinal'),$tipo->sucursal_id);
            if ($cierre) {
                return redirect('rolindividual')->with('error2', 'No puede realizar la operacion por que pertenece a un mes bloqueado');
            }
           
            /////////////////////cabecera roi /////////////////////////////////////////
            $cabecera_rol = new Rol_Consolidado();
            $cabecera_rol->cabecera_rol_fecha = $request->get('fechafinal');
            $cabecera_rol->cabecera_rol_total_dias = $request->get('Totaldias');
            $cabecera_rol->cabecera_rol_total_ingresos = $request->get('TIngresos');
            $cabecera_rol->cabecera_rol_total_anticipos =$request->get('adelanto');
            $cabecera_rol->cabecera_rol_total_egresos =floatval($request->get('TEgresos'))+floatval($Dalimentacion)+floatval($Iess)+floatval($Iessasu)+floatval($anticipos)+floatval($Dinpuesto);
            $cabecera_rol->cabecera_rol_sueldo = $request->get('Totalsueldo');
            $cabecera_rol->cabecera_rol_pago = $request->get('Liquidacion');
            $cabecera_rol->cabecera_rol_fr_acumula = $fondoacu;
            $cabecera_rol->cabecera_rol_iesspersonal = $request->get('Totalpersonal');
            $cabecera_rol->cabecera_rol_iesspatronal = $request->get('Totalpatronal');
            $cabecera_rol->cabecera_rol_tipo = 'INDIVIDUAL';
            $cabecera_rol->empleado_id =$request->get('idempleado');
            $cabecera_rol->cabecera_rol_estado = 1;

            
                
            $empleado=Empleado::Empleado($idempleado)->get()->first();

               
            $diario = new Diario();
            $diario->diario_codigo = $general->generarCodigoDiario($request->get('fechafinal'), 'CPRE');
            $diario->diario_fecha = $request->get('fechafinal');
            $diario->diario_referencia = 'COMPROBANTE DE PAGO DE ROL DE EMPLEADO';
            if ($request->get('tipo') == 'Cheque') {
                $diario->diario_tipo_documento = 'CHEQUE';
                $diario->diario_numero_documento = $request->get('idNcheque');
            }
            if ($request->get('tipo') == 'Transferencia') {
                $diario->diario_tipo_documento = 'TRANSFERENCIA';
                $diario->diario_numero_documento = 0;
            }
            $diario->diario_tipo = 'CPRE';
            $diario->diario_secuencial = substr($diario->diario_codigo, 8);
            $diario->diario_mes = DateTime::createFromFormat('Y-m-d', $request->get('fechafinal'))->format('m');
            $diario->diario_ano = DateTime::createFromFormat('Y-m-d', $request->get('fechafinal'))->format('Y');
            $diario->diario_comentario = 'COMPROBANTE DE PAGO DE ROL DE EMPLEADO: '.$empleado->empleado_nombre.' Con el sueldo de: '.$cabecera_rol->cabecera_rol_sueldo;
            
            $diario->diario_beneficiario =$empleado->empleado_nombre;
            $diario->diario_cierre = '0';
            $diario->diario_estado = '1';
                
            $diario->empresa_id = Auth::user()->empresa_id;
            $diario->sucursal_id =  $tipo->sucursal_id;
            $diario->save();
            $general->registrarAuditoria('Registro de diario de rol individual de Empleado -> '.$idempleado, '0', 'Con motivo:'. $cabecera_rol->cabecera_rol_descripcion);
            $cabecera_rol->diariopago()->associate($diario);

            //$diariocontabilizado = new Diario();
            /*
            $diariocontabilizado->diario_codigo = $general->generarCodigoDiario($request->get('fechafinal'), 'CCMR');
            $diariocontabilizado->diario_fecha = $request->get('fechafinal');
            $diariocontabilizado->diario_referencia = 'COMPROBANTE DE CONTABILIZACION MENSUAL DE ROLES';
            $diariocontabilizado->diario_tipo_documento = 'CONTABILIZACION MENSUAL';
            $diariocontabilizado->diario_tipo = 'CCMR';
            $diariocontabilizado->diario_secuencial = substr($diario->diario_codigo, 8);
            $diariocontabilizado->diario_mes = DateTime::createFromFormat('Y-m-d', $request->get('fechafinal'))->format('m');
            $diariocontabilizado->diario_ano = DateTime::createFromFormat('Y-m-d', $request->get('fechafinal'))->format('Y');
            $diariocontabilizado->diario_comentario = 'COMPROBANTE DE PAGO DE ROL DE EMPLEADO: '.$empleado->empleado_nombre.' Con el sueldo de: '.$cabecera_rol->cabecera_rol_sueldo;
            $diariocontabilizado->diario_numero_documento = 0;
            $diariocontabilizado->diario_beneficiario =$empleado->empleado_nombre;
            $diariocontabilizado->diario_cierre = '0';
            $diariocontabilizado->diario_estado = '1';

            $diariocontabilizado->empresa_id = Auth::user()->empresa_id;
            $diariocontabilizado->sucursal_id =  $tipo->sucursal_id;
            $diariocontabilizado->save();
            $general->registrarAuditoria('Registro de diario de rol consolidado de Empleado -> '.$request->get('idEmpleado'), '0', 'Con motivo:'. $cabecera_rol->cabecera_rol_descripcion);
            */
            // $cabecera_rol->diariocontabilizacion()->associate($diariocontabilizado);

            $cabecera_rol->save();
            $general->registrarAuditoria('Registro de Rol individual de Empleado -> '.$empleado->empleado_nombre, '0', 'Con motivo:'. $cabecera_rol->cabecera_rol_descripcion);
                
           
            if ($Dalimentacion>0) {
                $alimentar=Alimentacion::findOrFail($request->get('idalimentacion'));
                $alimentar->alimentacion_estado='2';
                $alimentar->rol()->associate($cabecera_rol);
                $alimentar->save();
                $auditoria = new generalController();
                $auditoria->registrarAuditoria('Actualizacion de Alimentacion de estado a 2 con empleado -> '.$empleado->empleado_nombre, '0', 'Por el pago de rol');
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
                    $anticipodescuento->descuento_fecha=$request->get('fecha_hasta');
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

            if ($request->get('tipo') == 'Cheque') {
                $formatter = new NumeroALetras();
                $cheque = new Cheque();
                $cheque->cheque_numero = $request->get('idNcheque');
                $cheque->cheque_descripcion =  'PAGO ROL DE EMPLEADO '.$empleado->empleado_nombre.' de '.strftime("%B de %Y", strtotime($request->get('fechafinal')));
                $cheque->cheque_beneficiario = $empleado->empleado_nombre;
                $cheque->cheque_fecha_emision = $request->get('fechafinal');
                $cheque->cheque_fecha_pago = $request->get('idFechaCheque');
                $cheque->cheque_valor = $cabecera_rol->cabecera_rol_pago;
                $cheque->cheque_valor_letras = $formatter->toInvoice($cheque->cheque_valor, 2, 'Dolares');
                $cheque->cuenta_bancaria_id = $request->get('cuenta_id_cheque');
                $cheque->cheque_estado = '1';
                $cheque->empresa_id = Auth::user()->empresa->empresa_id;
                $cheque->save();
                $urlcheque = $general->pdfImprimeCheque($request->get('cuenta_id_cheque'), $cheque);
                $general->registrarAuditoria('Registro de Cheque pago de rol numero: -> '.$request->get('idNcheque'), '0', 'Por motivo de: -> '. $cabecera_rol->cabecera_rol_descripcion.' con el valor de: -> '.$cabecera_rol->cabecera_rol_pago);
            }
                    
            if ($request->get('tipo') == 'Transferencia') {
                $transferencia = new Transferencia();
                $transferencia->transferencia_descripcion = 'PAGO ROL DE EMPLEADO '.$empleado->empleado_nombre.' de '.strftime("%B de %Y", strtotime($request->get('fechafinal')));
                $transferencia->transferencia_beneficiario = $empleado->empleado_nombre;
                $transferencia->transferencia_fecha = $request->get('fechafinal');
                $transferencia->transferencia_valor = $cabecera_rol->cabecera_rol_pago;
                $transferencia->cuenta_bancaria_id = $request->get('cuenta_id_transfer');
                $transferencia->transferencia_estado = '1';
                $transferencia->empresa_id = Auth::user()->empresa->empresa_id;
                $transferencia->save();
                $general->registrarAuditoria('Registro de Transferencia  pago de rol numero: -> '.$request->get('ncuenta'), '0', 'Por motivo de: -> '. $cabecera_rol->cabecera_rol_descripcion.' con el valor de: -> '.$total);
            }
                    
            $detalleDiario = new Detalle_Diario();
            $detalleDiario->detalle_debe = 0.00;
            $detalleDiario->detalle_haber =  floatval($request->get('Liquidacion'));
            if ($request->get('tipo') == 'Cheque') {
                $detalleDiario->detalle_comentario =  $banco->bancoLista->banco_lista_nombre.' Con Cuenta # '.$cuentabancaria->cuenta_bancaria_numero.' Con Cheque #'.$request->get('idNcheque');
            }
            if ($request->get('tipo') == 'Transferencia') {
                $detalleDiario->detalle_comentario = 'Transferencia a la cuenta bancaria N° '.$request->get('ncuenta');
            }
            $detalleDiario->detalle_tipo_documento = 'ROL INDIVIDUAL';
            $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
            $detalleDiario->detalle_conciliacion = '0';
            $detalleDiario->detalle_estado = '1';
            $detalleDiario->cuenta_id = $cuenta_id;
            if ($request->get('tipo') == 'Cheque') {
                $detalleDiario->cheque()->associate($cheque);
            }
            if ($request->get('tipo') == 'Transferencia') {
                $detalleDiario->transferencia()->associate($transferencia);
            }
                    
            $diario->detalles()->save($detalleDiario);
            $general->registrarAuditoria('Registro de Detalle de Diario codigo: -> '.$diario->diario_codigo, '0', 'En la cuenta del Haber -> '.$request->get('idCuentaContable').' con el valor de: -> '.$detalleDiario->detalle_haber);

            if ($fondoreserva>0) {
                $detalleDiario = new Detalle_Diario();
                $detalleDiario->detalle_debe =  $fondoreserva;
                $detalleDiario->detalle_haber =0.00;
                $detalleDiario->detalle_comentario = 'COMPROBANTE DE PAGO DE ROL DE EMPLEADO  '.$empleado->empleado_nombre.' de '.strftime("%B de %Y", strtotime($request->get('fechafinal')));
                $detalleDiario->detalle_tipo_documento = 'ROL INDIVIDUAL';
                $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                $detalleDiario->detalle_conciliacion = '0';
                $detalleDiario->detalle_estado = '1';
                $tipo=Empleado::EmpleadoBusquedaCuenta($idempleado, 'fondoReserva')->first();
                $detalleDiario->cuenta_id = $tipo->cuenta_haber;
                $detalleDiario->empleado_id = $idempleado;
                $diario->detalles()->save($detalleDiario);
                $general->registrarAuditoria('Registro de Detalle de Diario codigo: -> '.$diario->diario_codigo, '0', 'En la cuenta del Debe -> '.$tipo->cuenta_haber.' con el valor de: -> '. $detalleDiario->detalle_debe);
            }
            if ($Dtercero>0) {
                $detalleDiario = new Detalle_Diario();
                $detalleDiario->detalle_debe = floatval($Dtercero) ;
                $detalleDiario->detalle_haber =0.00;
                $detalleDiario->detalle_comentario = 'COMPROBANTE DE PAGO DE ROL DE EMPLEADO   '.$empleado->empleado_nombre.' de '.strftime("%B de %Y", strtotime($request->get('fechafinal')));
                $detalleDiario->detalle_tipo_documento = 'ROL INDIVIDUAL';
                $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                $detalleDiario->detalle_conciliacion = '0';
                $detalleDiario->detalle_estado = '1';
                $tipo=Empleado::EmpleadoBusquedaCuenta($idempleado, 'decimoTercero')->first();
                $detalleDiario->cuenta_id = $tipo->cuenta_haber;
                $detalleDiario->empleado_id = $idempleado;
                $diario->detalles()->save($detalleDiario);
                $general->registrarAuditoria('Registro de Detalle de Diario codigo: -> '.$diario->diario_codigo, '0', 'En la cuenta del Debe -> '.$tipo->cuenta_haber.' con el valor de: -> '. $detalleDiario->detalle_debe);
            }
            if ($Dcuarto>0) {
                $detalleDiario = new Detalle_Diario();
                $detalleDiario->detalle_debe =  floatval($Dcuarto);
                $detalleDiario->detalle_haber =0.00;
                $detalleDiario->detalle_comentario = 'COMPROBANTE DE PAGO DE ROL DE EMPLEADO  '.$empleado->empleado_nombre.' de '.strftime("%B de %Y", strtotime($request->get('fechafinal')));
                $detalleDiario->detalle_tipo_documento = 'ROL INDIVIDUAL';
                $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                $detalleDiario->detalle_conciliacion = '0';
                $detalleDiario->detalle_estado = '1';
                $tipo=Empleado::EmpleadoBusquedaCuenta($idempleado, 'decimoCuarto')->first();
                $detalleDiario->cuenta_id = $tipo->cuenta_haber;
                $detalleDiario->empleado_id = $idempleado;
                $diario->detalles()->save($detalleDiario);
                $general->registrarAuditoria('Registro de Detalle de Diario codigo:  '.$diario->diario_codigo, '0', 'En la cuenta del Debe -> '.$tipo->cuenta_haber.' con el valor de: -> '. $detalleDiario->detalle_debe);
            }
            $tipo=Empleado::EmpleadoBusquedaCuenta($idempleado, 'sueldos')->first();
            $detalleDiario = new Detalle_Diario();
            $detalleDiario->detalle_debe =  floatval($request->get('Liquidacion'))-($Dcuarto+$Dtercero+ $fondoreserva);
            $detalleDiario->detalle_haber =0.00;
            $detalleDiario->detalle_comentario = 'COMPROBANTE DE PAGO DE ROL DE EMPLEADO  '.$empleado->empleado_nombre.' de '.strftime("%B de %Y", strtotime($request->get('fechafinal')));
            $detalleDiario->detalle_tipo_documento = 'ROL INDIVIDUAL';
            $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
            $detalleDiario->detalle_conciliacion = '0';
            $detalleDiario->detalle_estado = '1';
            $detalleDiario->cuenta_id = $tipo->cuenta_haber;
            $detalleDiario->empleado_id = $idempleado;
            $diario->detalles()->save($detalleDiario);
            $general->registrarAuditoria('Registro de Detalle de Diario codigo: -> '.$diario->diario_codigo, '0', 'En la cuenta del Debe  '.$request->get('idCuentaContable').' con el valor de: -> '. $detalleDiario->detalle_debe);
                       
                    
                       
                    
                
               
            ///////////////////////  detalle rol //////////////////////////////////////////
            for ($i = 1; $i < count($Cdias); ++$i) {
                $totaldetalle=0;
                if ($cabecera_rol->cabecera_rol_pago!=0) {
                    $totaldetalle=(($Dingreso[$i]-$Degreso[$i])+$Dtercero+$Dcuarto+$fondoreserva)-($Iessasu+$Iess+$anticipos+$quincena+$Dinpuesto);
                } else {
                    $totaldetalle=($Dingreso[$i]-$Degreso[$i]);
                }
                $detalle= new Detalle_Rol();
                $time = strtotime($Dfechaini[$i]);
                $inicio = date('Y-m-d', $time);
                $time = strtotime($Dfechafin[$i]);
                $fin = date('Y-m-d', $time);
                $detalle->detalle_rol_fecha_inicio = $inicio;
                $detalle->detalle_rol_fecha_fin = $fin;
                $detalle->detalle_rol_sueldo = floatval($Dsueldo[$i]);
                $detalle->detalle_rol_dias =floatval($Cdias[$i]);
                $detalle->detalle_rol_valor_dia = round(floatval($Dsueldo[$i])/30, 2);
                $detalle->detalle_rol_total_dias = floatval($Dsueldo[$i]);
                $detalle->detalle_rol_horas_extras = 0;
                $detalle->detalle_rol_valor_he = floatval($Dextras[$i]);
                $detalle->detalle_rol_bonificacion_dias = 0;
                $detalle->detalle_rol_horas_suplementarias = 0;
                $detalle->detalle_rol_otra_bonificacion = floatval($Dotrosbon[$i]);
                $detalle->detalle_rol_otros_ingresos = floatval($Dotrosin[$i]);
                $detalle->detalle_rol_sueldo_rembolsable = 0;
                $detalle->detalle_rol_fondo_reserva = $fondoreserva;
                $detalle->detalle_rol_iess = $Iess;
                $detalle->detalle_rol_multa = floatval($Dmultas[$i]);
                $detalle->detalle_rol_quincena = $quincena;
                $detalle->detalle_rol_total_anticipo =  $anticipos;
                $detalle->detalle_rol_total_comisariato = floatval($Dalimentacion);
                $detalle->detalle_rol_prestamo_quirografario = floatval($Dppqq[$i]);
                $detalle->detalle_rol_prestamo_hipotecario = floatval($Dhipotecarios[$i]);
                $detalle->detalle_rol_prestamo =floatval($Dprestamos[$i]);
                $detalle->detalle_rol_transporte = floatval($Dtransporte[$i]);
                $detalle->detalle_rol_ext_salud =floatval($Dsalud[$i]);
                $detalle->detalle_rol_impuesto_renta =  $Dinpuesto;
                $detalle->detalle_rol_ley_sol = floatval($Dleysol[$i]);
                $detalle->detalle_rol_total_permiso = 0;
                $detalle->detalle_rol_permiso_no_rem = 0;
                $detalle->detalle_rol_cosecha = 0;
                $detalle->detalle_rol_porcentaje = floatval($Dporcentaje[$i]);
                $detalle->detalle_rol_otros_egresos = floatval($Dotrosegre[$i]);
                $detalle->detalle_rol_liquido_pagar = $totaldetalle-floatval($Dalimentacion);
                
                $detalle->detalle_rol_contabilizado = 1;
                $detalle->detalle_rol_iess_asumido = $Iessasu;
                $detalle->detalle_rol_aporte_patronal =  $Iesspatronal;
                $detalle->detalle_rol_aporte_iecesecap = $IECESECAP;
                $detalle->detalle_rol_vacaciones = floatval($Dvacaciones[$i]);
                if ($Dvacaciones[$i]>0) {
                    $vacio =new Vacacion();
                    $vacio->vacacion_fecha = $inicio;
                    $vacio->vacacion_tipo = $request->get('tipo');
                    $vacio->vacacion_valor = floatval($Dvacaciones[$i]);
                    $vacio->vacacion_descripcion = 'Pago de Vacaciones por rol '.date("m-Y", strtotime($inicio));
                    $vacio->empleado_id = $idempleado;
                    $vacio->vacacion_estado='2';
                    $vacio->rol()->associate($cabecera_rol);
                    $vacio->diario()->associate($diario);
                    $vacio->save();
                }
                $detalle->detalle_rol_vacaciones_anticipadas=$Totalvacacion;
                if ($Totalvacacion>0) {
                    $vac=Vacacion::findOrFail($request->get('idvacacion'));
                    $vac->vacacion_estado='2';
                    $vac->rol()->associate($cabecera_rol);
                    $vac->save();
                }
                    
                $detalle->detalle_rol_decimo_tercero = $Dtercero;
                $detalle->detalle_rol_decimo_cuarto =  $Dcuarto;
                    
                $detalle->detalle_rol_total_egreso = floatval($Degreso[$i])+floatval($Dalimentacion)+floatval($Iess)+floatval($Iessasu)+floatval($anticipos)+floatval($Dinpuesto);
                $detalle->detalle_rol_total_ingreso = floatval($Dingreso[$i]);
                $detalle->detalle_rol_decimo_terceroacum = floatval($Dcuartoacu);
                $detalle->detalle_rol_decimo_cuartoacum = floatval($Dterceroacu);
                $detalle->detalle_rol_estado = 1;
                if ($cabecera_rol->cabecera_rol_pago==0) {
                   
                /*  
                    if (floatval($Dsueldo[$i])>0) {
                        $detalleDiario = new Detalle_Diario();
                        $detalleDiario->detalle_debe = floatval($Dsueldo[$i]) ;
                        $detalleDiario->detalle_haber =0.00;
                        $detalleDiario->detalle_comentario = 'COMPROBANTE DE CONTABILIZACION MENSUAL DE ROLES  '.$empleado->empleado_nombre.' de '.strftime("%B de %Y", strtotime($request->get('fechafinal')));
                        $detalleDiario->detalle_tipo_documento = 'CONTABILIZACION MENSUAL';
                        $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                        $detalleDiario->detalle_conciliacion = '0';
                        $detalleDiario->detalle_estado = '1';
                        $tipo=Empleado::EmpleadoBusquedaCuenta($idempleado, 'sueldos')->first();
                        $detalleDiario->cuenta_id = $tipo->cuenta_debe;
                        $detalleDiario->empleado_id =  $idempleado;
                        $diariocontabilizado->detalles()->save($detalleDiario);
                        $general->registrarAuditoria('Registro de Detalle de Diario codigo: -> '.$diario->diario_codigo, '0', 'En la cuenta del Debe '.$tipo->cuenta_debe.' con el valor de: '.$Dsueldo[$i]);
                    }
                 
                    if (floatval($Dhoras_suplementarias[$i])>0) {
                        $detalleDiario = new Detalle_Diario();
                        $detalleDiario->detalle_debe = floatval($Dhoras_suplementarias[$i]) ;
                        $detalleDiario->detalle_haber =0.00;
                        $detalleDiario->detalle_comentario = 'COMPROBANTE DE CONTABILIZACION MENSUAL DE ROLES  '.$empleado->empleado_nombre.' de '.strftime("%B de %Y", strtotime($request->get('fechafinal')));
                        $detalleDiario->detalle_tipo_documento = 'CONTABILIZACION MENSUAL';
                        $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                        $detalleDiario->detalle_conciliacion = '0';
                        $detalleDiario->detalle_estado = '1';
                        $tipo=Empleado::EmpleadoBusquedaCuenta($idempleado, 'horas_suplementarias')->first();
                        $detalleDiario->cuenta_id = $tipo->cuenta_debe;
                        $detalleDiario->empleado_id =  $idempleado;
                        $diariocontabilizado->detalles()->save($detalleDiario);
                        $general->registrarAuditoria('Registro de Detalle de Diario codigo: -> '.$diario->diario_codigo, '0', 'En la cuenta del Debe '.$tipo->cuenta_debe.' con el valor de: '.$Dhoras_suplementarias[$i]);
                    }
                    if (floatval($Dtransporte[$i])>0) {
                        $detalleDiario = new Detalle_Diario();
                        $detalleDiario->detalle_debe = floatval($Dtransporte[$i]) ;
                        $detalleDiario->detalle_haber =0.00;
                        $detalleDiario->detalle_comentario = 'COMPROBANTE DE CONTABILIZACION MENSUAL DE ROLES  '.$empleado->empleado_nombre.' de '.strftime("%B de %Y", strtotime($request->get('fechafinal')));
                        $detalleDiario->detalle_tipo_documento = 'CONTABILIZACION MENSUAL';
                        $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                        $detalleDiario->detalle_conciliacion = '0';
                        $detalleDiario->detalle_estado = '1';
                        $tipo=Empleado::EmpleadoBusquedaCuenta($idempleado, 'viaticos')->first();
                        $detalleDiario->cuenta_id = $tipo->cuenta_debe;
                        $detalleDiario->empleado_id =  $idempleado;
                        $diariocontabilizado->detalles()->save($detalleDiario);
                        $general->registrarAuditoria('Registro de Detalle de Diario codigo: -> '.$diario->diario_codigo, '0', 'En la cuenta del Debe '.$tipo->cuenta_debe.' con el valor de: '.$Dtransporte[$i]);
                    }
                    if (floatval($Dotrosbon[$i])>0) {
                        $detalleDiario = new Detalle_Diario();
                        $detalleDiario->detalle_debe = floatval($Dotrosbon[$i]) ;
                        $detalleDiario->detalle_haber =0.00;
                        $detalleDiario->detalle_comentario = 'COMPROBANTE DE CONTABILIZACION MENSUAL DE ROLES  '.$empleado->empleado_nombre.' de '.strftime("%B de %Y", strtotime($request->get('fechafinal')));
                        $detalleDiario->detalle_tipo_documento = 'CONTABILIZACION MENSUAL';
                        $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                        $detalleDiario->detalle_conciliacion = '0';
                        $detalleDiario->detalle_estado = '1';
                        $tipo=Empleado::EmpleadoBusquedaCuenta($idempleado, 'otrosBonificaciones')->first();
                        $detalleDiario->cuenta_id = $tipo->cuenta_debe;
                        $detalleDiario->empleado_id =  $idempleado;
                        $diariocontabilizado->detalles()->save($detalleDiario);
                        $general->registrarAuditoria('Registro de Detalle de Diario codigo: -> '.$diario->diario_codigo, '0', 'En la cuenta del Debe '.$tipo->cuenta_debe.' con el valor de: '.$Dotrosbon[$i]);
                    }
                    if (floatval($Dotrosin[$i])>0) {
                        $detalleDiario = new Detalle_Diario();
                        $detalleDiario->detalle_debe = floatval($Dotrosin[$i]) ;
                        $detalleDiario->detalle_haber =0.00;
                        $detalleDiario->detalle_comentario = 'COMPROBANTE DE CONTABILIZACION MENSUAL DE ROLES  '.$empleado->empleado_nombre.' de '.strftime("%B de %Y", strtotime($request->get('fechafinal')));
                        $detalleDiario->detalle_tipo_documento = 'CONTABILIZACION MENSUAL';
                        $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                        $detalleDiario->detalle_conciliacion = '0';
                        $detalleDiario->detalle_estado = '1';
                        $tipo=Empleado::EmpleadoBusquedaCuenta($idempleado, 'otrosIngresos')->first();
                        $detalleDiario->cuenta_id = $tipo->cuenta_debe;
                        $detalleDiario->empleado_id =  $idempleado;
                        $diariocontabilizado->detalles()->save($detalleDiario);
                        $general->registrarAuditoria('Registro de Detalle de Diario codigo: -> '.$diario->diario_codigo, '0', 'En la cuenta del Debe '.$tipo->cuenta_debe.' con el valor de: '.$Dotrosin[$i]);
                    }

                   
                    
                    if (floatval($Dsalud[$i])>0) {
                        $detalleDiario = new Detalle_Diario();
                        $detalleDiario->detalle_debe = 0.00;
                        $detalleDiario->detalle_haber = floatval($Dsalud[$i]);
                        $detalleDiario->detalle_comentario = 'COMPROBANTE DE CONTABILIZACION MENSUAL DE ROLES '.$empleado->empleado_nombre.' de '.strftime("%B de %Y", strtotime($request->get('fechafinal')));
                        $detalleDiario->detalle_tipo_documento = 'CONTABILIZACION MENSUAL';
                        $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                        $detalleDiario->detalle_conciliacion = '0';
                        $detalleDiario->detalle_estado = '1';
                        $tipo=Empleado::EmpleadoBusquedaCuenta($idempleado, 'extSalud')->first();
                        $detalleDiario->cuenta_id = $tipo->cuenta_haber;
                        $detalleDiario->empleado_id = $idempleado;
                        $diariocontabilizado->detalles()->save($detalleDiario);
                        $general->registrarAuditoria('Registro de Detalle de Diario codigo: -> '.$diario->diario_codigo, '0', 'En la cuenta del Haber -> '.$tipo->cuenta_haber.' con el valor de: -> '.$Dsalud[$i]);
                    }
                    if (floatval($Dleysol[$i])>0) {
                        $detalleDiario = new Detalle_Diario();
                        $detalleDiario->detalle_debe = 0.00;
                        $detalleDiario->detalle_haber = floatval($Dleysol[$i]);
                        $detalleDiario->detalle_comentario = 'COMPROBANTE DE CONTABILIZACION MENSUAL DE ROLES '.$empleado->empleado_nombre.' de '.strftime("%B de %Y", strtotime($request->get('fechafinal')));
                        $detalleDiario->detalle_tipo_documento = 'CONTABILIZACION MENSUAL';
                        $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                        $detalleDiario->detalle_conciliacion = '0';
                        $detalleDiario->detalle_estado = '1';
                        $tipo=Empleado::EmpleadoBusquedaCuenta($idempleado, 'leysalud')->first();
                        $detalleDiario->cuenta_id = $tipo->cuenta_haber;
                        $detalleDiario->empleado_id = $idempleado;
                        $diariocontabilizado->detalles()->save($detalleDiario);
                        $general->registrarAuditoria('Registro de Detalle de Diario codigo: -> '.$diario->diario_codigo, '0', 'En la cuenta del Haber -> '.$tipo->cuenta_haber.' con el valor de: -> '.$Dleysol[$i]);
                    }
                    if (floatval($Dppqq[$i])>0) {
                        $detalleDiario = new Detalle_Diario();
                        $detalleDiario->detalle_debe = 0.00;
                        $detalleDiario->detalle_haber = floatval($Dppqq[$i]);
                        $detalleDiario->detalle_comentario = 'COMPROBANTE DE CONTABILIZACION MENSUAL DE ROLES '.$empleado->empleado_nombre.' de '.strftime("%B de %Y", strtotime($request->get('fechafinal')));
                        $detalleDiario->detalle_tipo_documento = 'CONTABILIZACION MENSUAL';
                        $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                        $detalleDiario->detalle_conciliacion = '0';
                        $detalleDiario->detalle_estado = '1';
                        $tipo=Empleado::EmpleadoBusquedaCuenta($idempleado, 'prestamosQuirografarios')->first();
                        $detalleDiario->cuenta_id = $tipo->cuenta_haber;
                        $detalleDiario->empleado_id = $idempleado;
                        $diariocontabilizado->detalles()->save($detalleDiario);
                        $general->registrarAuditoria('Registro de Detalle de Diario codigo: -> '.$diario->diario_codigo, '0', 'En la cuenta del Haber -> '.$tipo->cuenta_haber.' con el valor de: -> '.$Dppqq[$i]);
                    }
                    if (floatval($Dhipotecarios[$i])>0) {
                        $detalleDiario = new Detalle_Diario();
                        $detalleDiario->detalle_debe = 0.00;
                        $detalleDiario->detalle_haber = floatval($Dhipotecarios[$i]);
                        $detalleDiario->detalle_comentario = 'COMPROBANTE DE CONTABILIZACION MENSUAL DE ROLES '.$empleado->empleado_nombre.' de '.strftime("%B de %Y", strtotime($request->get('fechafinal')));
                        $detalleDiario->detalle_tipo_documento = 'CONTABILIZACION MENSUAL';
                        $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                        $detalleDiario->detalle_conciliacion = '0';
                        $detalleDiario->detalle_estado = '1';
                        $tipo=Empleado::EmpleadoBusquedaCuenta($idempleado, 'prestamosHipotecarios')->first();
                        $detalleDiario->cuenta_id = $tipo->cuenta_haber;
                        $detalleDiario->empleado_id = $idempleado;
                        $diariocontabilizado->detalles()->save($detalleDiario);
                        $general->registrarAuditoria('Registro de Detalle de Diario codigo: -> '.$diario->diario_codigo, '0', 'En la cuenta del Haber -> '.$tipo->cuenta_haber.' con el valor de: -> '.$Dhipotecarios[$i]);
                    }
                    if (floatval($Dalimentacion)>0) {
                        $detalleDiario = new Detalle_Diario();
                        $detalleDiario->detalle_debe = 0.00;
                        $detalleDiario->detalle_haber = floatval($Dalimentacion);
                        $detalleDiario->detalle_comentario = 'COMPROBANTE DE CONTABILIZACION MENSUAL DE ROLES '.$empleado->empleado_nombre.' de '.strftime("%B de %Y", strtotime($request->get('fechafinal')));
                        $detalleDiario->detalle_tipo_documento = 'CONTABILIZACION MENSUAL';
                        $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                        $detalleDiario->detalle_conciliacion = '0';
                        $detalleDiario->detalle_estado = '1';
                        $tipo=Empleado::EmpleadoBusquedaCuenta($idempleado, 'comisariato')->first();
                        $detalleDiario->cuenta_id = $tipo->cuenta_haber;
                        $detalleDiario->empleado_id = $idempleado;
                        $diariocontabilizado->detalles()->save($detalleDiario);
                        $general->registrarAuditoria('Registro de Detalle de Diario codigo: -> '.$diario->diario_codigo, '0', 'En la cuenta del Haber -> '.$tipo->cuenta_haber.' con el valor de: -> '.$Dalimentacion);
                    }
                    if (floatval($Dprestamos[$i])>0) {
                        $detalleDiario = new Detalle_Diario();
                        $detalleDiario->detalle_debe = 0.00;
                        $detalleDiario->detalle_haber = floatval($Dprestamos[$i]);
                        $detalleDiario->detalle_comentario = 'COMPROBANTE DE CONTABILIZACION MENSUAL DE ROLES '.$empleado->empleado_nombre.' de '.strftime("%B de %Y", strtotime($request->get('fechafinal')));
                        $detalleDiario->detalle_tipo_documento = 'CONTABILIZACION MENSUAL';
                        $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                        $detalleDiario->detalle_conciliacion = '0';
                        $detalleDiario->detalle_estado = '1';
                        $tipo=Empleado::EmpleadoBusquedaCuenta($idempleado, 'prestamos')->first();
                        $detalleDiario->cuenta_id = $tipo->cuenta_haber;
                        $detalleDiario->empleado_id = $idempleado;
                        $diariocontabilizado->detalles()->save($detalleDiario);
                        $general->registrarAuditoria('Registro de Detalle de Diario codigo: -> '.$diario->diario_codigo, '0', 'En la cuenta del Haber -> '.$tipo->cuenta_haber.' con el valor de: -> '.$Dprestamos[$i]);
                    }
                    if (floatval($Dmultas[$i])>0) {
                        $detalleDiario = new Detalle_Diario();
                        $detalleDiario->detalle_debe = 0.00;
                        $detalleDiario->detalle_haber = floatval($Dmultas[$i]);
                        $detalleDiario->detalle_comentario = 'COMPROBANTE DE CONTABILIZACION MENSUAL DE ROLES '.$empleado->empleado_nombre.' de '.strftime("%B de %Y", strtotime($request->get('fechafinal')));
                        $detalleDiario->detalle_tipo_documento = 'CONTABILIZACION MENSUAL';
                        $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                        $detalleDiario->detalle_conciliacion = '0';
                        $detalleDiario->detalle_estado = '1';
                        $tipo=Empleado::EmpleadoBusquedaCuenta($idempleado, 'multas')->first();
                        $detalleDiario->cuenta_id = $tipo->cuenta_haber;
                        $detalleDiario->empleado_id = $idempleado;
                        $diariocontabilizado->detalles()->save($detalleDiario);
                        $general->registrarAuditoria('Registro de Detalle de Diario codigo: -> '.$diario->diario_codigo, '0', 'En la cuenta del Haber -> '.$tipo->cuenta_haber.' con el valor de: -> '.$Dmultas[$i]);
                    }
                    if (floatval($Iessasu)>0) {
                        $detalleDiario = new Detalle_Diario();
                        $detalleDiario->detalle_debe = 0.00;
                        $detalleDiario->detalle_haber = floatval($Iessasu);
                        $detalleDiario->detalle_comentario = 'COMPROBANTE DE CONTABILIZACION MENSUAL DE ROLES '.$empleado->empleado_nombre.' de '.strftime("%B de %Y", strtotime($request->get('fechafinal')));
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
                    if (floatval($anticipos)>0) {
                        $detalleDiario = new Detalle_Diario();
                        $detalleDiario->detalle_debe = 0.00;
                        $detalleDiario->detalle_haber = floatval($anticipos);
                        $detalleDiario->detalle_comentario = 'COMPROBANTE DE CONTABILIZACION MENSUAL DE ROLES '.$empleado->empleado_nombre.' de '.strftime("%B de %Y", strtotime($request->get('fechafinal')));
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
                    if (floatval($Dinpuesto)>0) {
                        $detalleDiario = new Detalle_Diario();
                        $detalleDiario->detalle_debe = 0.00;
                        $detalleDiario->detalle_haber = floatval($Dinpuesto);
                        $detalleDiario->detalle_comentario = 'COMPROBANTE DE CONTABILIZACION MENSUAL DE ROLES '.$empleado->empleado_nombre.' de '.strftime("%B de %Y", strtotime($request->get('fechafinal')));
                        $detalleDiario->detalle_tipo_documento = 'CONTABILIZACION MENSUAL';
                        $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                        $detalleDiario->detalle_conciliacion = '0';
                        $detalleDiario->detalle_estado = '1';
                        $tipo=Empleado::EmpleadoBusquedaCuenta($idempleado, 'impuestoRenta')->first();
                        $detalleDiario->cuenta_id = $tipo->cuenta_haber;
                        $detalleDiario->empleado_id = $idempleado;
                        $diariocontabilizado->detalles()->save($detalleDiario);
                        $general->registrarAuditoria('Registro de Detalle de Diario codigo: -> '.$diario->diario_codigo, '0', 'En la cuenta del Haber -> '.$tipo->cuenta_haber.' con el valor de: -> '.$Dinpuesto);
                    }
                    if (floatval($Dotrosegre[$i])>0) {
                        $detalleDiario = new Detalle_Diario();
                        $detalleDiario->detalle_debe = 0.00;
                        $detalleDiario->detalle_haber = floatval($Dotrosegre[$i]);
                        $detalleDiario->detalle_comentario = 'COMPROBANTE DE CONTABILIZACION MENSUAL DE ROLES '.$empleado->empleado_nombre.' de '.strftime("%B de %Y", strtotime($request->get('fechafinal')));
                        $detalleDiario->detalle_tipo_documento = 'CONTABILIZACION MENSUAL';
                        $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                        $detalleDiario->detalle_conciliacion = '0';
                        $detalleDiario->detalle_estado = '1';
                        $tipo=Empleado::EmpleadoBusquedaCuenta($idempleado, 'otrosEgresos')->first();
                        $detalleDiario->cuenta_id = $tipo->cuenta_haber;
                        $detalleDiario->empleado_id = $idempleado;
                        $diariocontabilizado->detalles()->save($detalleDiario);
                        $general->registrarAuditoria('Registro de Detalle de Diario codigo: -> '.$diario->diario_codigo, '0', 'En la cuenta del Haber -> '.$tipo->cuenta_haber.' con el valor de: -> '.$Dotrosegre[$i]);
                    }
                    if ($cabecera_rol->cabecera_rol_iesspersonal>0) {
                        $detalleDiario = new Detalle_Diario();
                        $detalleDiario->detalle_debe = 0.00;
                        $detalleDiario->detalle_haber = $cabecera_rol->cabecera_rol_iesspersonal;
                        $detalleDiario->detalle_comentario = 'COMPROBANTE DE CONTABILIZACION MENSUAL DE ROLES '.$empleado->empleado_nombre.' de '.strftime("%B de %Y", strtotime($request->get('fechafinal')));
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

                    if (((floatval($Dsueldo[$i])+floatval($Dotrosin[$i])+floatval($Dhoras_suplementarias[$i])+floatval($Dtransporte[$i])+floatval($Dotrosbon[$i])+floatval($Dotrosin[$i]))-(floatval($Dsalud[$i])+floatval($Dleysol[$i])+floatval($Dppqq[$i])+floatval($Dhipotecarios[$i])+floatval($Dalimentacion)+floatval($Dprestamos[$i])+floatval($Dmultas[$i])+floatval($Iessasu)+floatval($anticipos)+floatval($Dinpuesto)+floatval($Dotrosegre[$i])+$cabecera_rol->cabecera_rol_iesspersonal))>0) {
                        $detalleDiario = new Detalle_Diario();
                        $detalleDiario->detalle_debe = 0.00;
                        $detalleDiario->detalle_haber = ((floatval($Dsueldo[$i])+floatval($Dotrosin[$i])+floatval($Dhoras_suplementarias[$i])+floatval($Dtransporte[$i])+floatval($Dotrosbon[$i])+floatval($Dotrosin[$i]))-(floatval($Dsalud[$i])+floatval($Dleysol[$i])+floatval($Dppqq[$i])+floatval($Dhipotecarios[$i])+floatval($Dalimentacion)+floatval($Dprestamos[$i])+floatval($Dmultas[$i])+floatval($Iessasu)+floatval($anticipos)+floatval($Dinpuesto)+floatval($Dotrosegre[$i])+$cabecera_rol->cabecera_rol_iesspersonal));
                        $detalleDiario->detalle_comentario = 'COMPROBANTE DE CONTABILIZACION MENSUAL DE ROLES '.$empleado->empleado_nombre.' de '.strftime("%B de %Y", strtotime($request->get('fechafinal')));
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

                    ///////////////////////////Provisiones///////////////////////////////
                    if ($cabecera_rol->cabecera_rol_iesspatronal>0) {
                        $detalleDiario = new Detalle_Diario();
                        $detalleDiario->detalle_debe = $cabecera_rol->cabecera_rol_iesspatronal;
                        $detalleDiario->detalle_haber = 0.00;
                        $detalleDiario->detalle_comentario = 'COMPROBANTE DE CONTABILIZACION MENSUAL DE ROLES '.$empleado->empleado_nombre.' de '.strftime("%B de %Y", strtotime($request->get('fechafinal')));
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
                        $detalleDiario->detalle_haber = $cabecera_rol->cabecera_rol_iesspatronal;
                        $detalleDiario->detalle_comentario = 'COMPROBANTE DE CONTABILIZACION MENSUAL DE ROLES '.$empleado->empleado_nombre.' de '.strftime("%B de %Y", strtotime($request->get('fechafinal')));
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
                    if (floatval($Dvacaciones[$i])>0) {
                        $detalleDiario = new Detalle_Diario();
                        $detalleDiario->detalle_debe = $Totalvacacion;
                        $detalleDiario->detalle_haber = 0.00;
                        $detalleDiario->detalle_comentario = 'COMPROBANTE DE CONTABILIZACION MENSUAL DE ROLES '.$empleado->empleado_nombre.' de '.strftime("%B de %Y", strtotime($request->get('fechafinal')));
                        $detalleDiario->detalle_tipo_documento = 'CONTABILIZACION MENSUAL';
                        $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                        $detalleDiario->detalle_conciliacion = '0';
                        $detalleDiario->detalle_estado = '1';
                        $tipo=Empleado::EmpleadoBusquedaCuenta($idempleado, 'vacacion')->first();
                        $detalleDiario->cuenta_id = $tipo->cuenta_debe;
                        $detalleDiario->empleado_id = $idempleado;
                        $diariocontabilizado->detalles()->save($detalleDiario);
                        $general->registrarAuditoria('Registro de Detalle de Diario codigo: -> '.$diario->diario_codigo, '0', 'En la cuenta del Debe -> '.$tipo->cuenta_debe.' con el valor de: -> '.$Totalvacacion);
         

                        $detalleDiario = new Detalle_Diario();
                        $detalleDiario->detalle_debe = 0.00;
                        $detalleDiario->detalle_haber = $Totalvacacion;
                        $detalleDiario->detalle_comentario = 'COMPROBANTE DE CONTABILIZACION MENSUAL DE ROLES '.$empleado->empleado_nombre.' de '.strftime("%B de %Y", strtotime($request->get('fechafinal')));
                        $detalleDiario->detalle_tipo_documento = 'CONTABILIZACION MENSUAL';
                        $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                        $detalleDiario->detalle_conciliacion = '0';
                        $detalleDiario->detalle_estado = '1';
                        $tipo=Empleado::EmpleadoBusquedaCuenta($idempleado, 'vacacion')->first();
                        $detalleDiario->cuenta_id = $tipo->cuenta_haber;
                        $detalleDiario->empleado_id = $idempleado;
                        $diariocontabilizado->detalles()->save($detalleDiario);
                        $general->registrarAuditoria('Registro de Detalle de Diario codigo: -> '.$diario->diario_codigo, '0', 'En la cuenta del Haber -> '.$tipo->cuenta_haber.' con el valor de: -> '.$Totalvacacion);
                    }
                    if (floatval($Dtercero)>0) {
                        $detalleDiario = new Detalle_Diario();
                        $detalleDiario->detalle_debe = floatval($Dtercero);
                        $detalleDiario->detalle_haber = 0.00;
                        $detalleDiario->detalle_comentario = 'COMPROBANTE DE CONTABILIZACION MENSUAL DE ROLES '.$empleado->empleado_nombre.' de '.strftime("%B de %Y", strtotime($request->get('fechafinal')));
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
                        $detalleDiario->detalle_haber = floatval($Dtercero);
                        $detalleDiario->detalle_comentario = 'COMPROBANTE DE CONTABILIZACION MENSUAL DE ROLES '.$empleado->empleado_nombre.' de '.strftime("%B de %Y", strtotime($request->get('fechafinal')));
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
                    if (floatval($Dcuarto)>0) {
                        $detalleDiario = new Detalle_Diario();
                        $detalleDiario->detalle_debe = floatval($Dcuarto);
                        $detalleDiario->detalle_haber = 0.00;
                        $detalleDiario->detalle_comentario = 'COMPROBANTE DE CONTABILIZACION MENSUAL DE ROLES '.$empleado->empleado_nombre.' de '.strftime("%B de %Y", strtotime($request->get('fechafinal')));
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
                        $detalleDiario->detalle_haber = floatval($Dcuarto);
                        $detalleDiario->detalle_comentario = 'COMPROBANTE DE CONTABILIZACION MENSUAL DE ROLES '.$empleado->empleado_nombre.' de '.strftime("%B de %Y", strtotime($request->get('fechafinal')));
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
                    */
                    
                    /*
                    if (floatval($fondoreserva)>0) {
                        $detalleDiario = new Detalle_Diario();
                        $detalleDiario->detalle_debe = floatval($fondoreserva);
                        $detalleDiario->detalle_haber = 0.00;
                        $detalleDiario->detalle_comentario = 'COMPROBANTE DE CONTABILIZACION MENSUAL DE ROLES '.$empleado->empleado_nombre.' de '.strftime("%B de %Y", strtotime($request->get('fechafinal')));
                        $detalleDiario->detalle_tipo_documento = 'CONTABILIZACION MENSUAL';
                        $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                        $detalleDiario->detalle_conciliacion = '0';
                        $detalleDiario->detalle_estado = '1';
                        $tipo=Empleado::EmpleadoBusquedaCuenta($idempleado, 'fondoReserva')->first();
                        $detalleDiario->cuenta_id = $tipo->cuenta_debe;
                        $detalleDiario->empleado_id = $idempleado;
                        $diariocontabilizado->detalles()->save($detalleDiario);
                        $general->registrarAuditoria('Registro de Detalle de Diario codigo: -> '.$diario->diario_codigo, '0', 'En la cuenta del Debe -> '.$tipo->cuenta_debe.' con el valor de: -> '.$fondoreserva);


                        $detalleDiario = new Detalle_Diario();
                        $detalleDiario->detalle_debe = 0.00;
                        $detalleDiario->detalle_haber = floatval($fondoreserva);
                        $detalleDiario->detalle_comentario = 'COMPROBANTE DE CONTABILIZACION MENSUAL DE ROLES '.$empleado->empleado_nombre.' de '.strftime("%B de %Y", strtotime($request->get('fechafinal')));
                        $detalleDiario->detalle_tipo_documento = 'CONTABILIZACION MENSUAL';
                        $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                        $detalleDiario->detalle_conciliacion = '0';
                        $detalleDiario->detalle_estado = '1';
                        $tipo=Empleado::EmpleadoBusquedaCuenta($idempleado, 'fondoReserva')->first();
                        $detalleDiario->cuenta_id = $tipo->cuenta_haber;
                        $detalleDiario->empleado_id = $idempleado;
                        $diariocontabilizado->detalles()->save($detalleDiario);
                        $general->registrarAuditoria('Registro de Detalle de Diario codigo: -> '.$diario->diario_codigo, '0', 'En la cuenta del Haber -> '.$tipo->cuenta_haber.' con el valor de: -> '.$fondoreserva);
                    }
                    if ($cabecera_rol->cabecera_rol_fr_acumula>0) {
                        $detalleDiario = new Detalle_Diario();
                        $detalleDiario->detalle_debe =  $cabecera_rol->cabecera_rol_fr_acumula;
                        $detalleDiario->detalle_haber =0.00;
                        $detalleDiario->detalle_comentario = 'COMPROBANTE DE CONTABILIZACION MENSUAL DE ROLES '.$empleado->empleado_nombre.' de '.strftime("%B de %Y", strtotime($request->get('fechafinal')));
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
                        $detalleDiario->detalle_comentario = 'COMPROBANTE DE CONTABILIZACION MENSUAL DE ROLES '.$empleado->empleado_nombre.' de '.strftime("%B de %Y", strtotime($request->get('fechafinal')));
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
                    if (floatval($IECESECAP)>0) {
                        $detalleDiario = new Detalle_Diario();
                        $detalleDiario->detalle_debe = floatval($IECESECAP);
                        $detalleDiario->detalle_haber = 0.00;
                        $detalleDiario->detalle_comentario = 'COMPROBANTE DE CONTABILIZACION MENSUAL DE ROLES '.$empleado->empleado_nombre.' de '.strftime("%B de %Y", strtotime($request->get('fechafinal')));
                        $detalleDiario->detalle_tipo_documento = 'CONTABILIZACION MENSUAL';
                        $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                        $detalleDiario->detalle_conciliacion = '0';
                        $detalleDiario->detalle_estado = '1';
                        $tipo=Empleado::EmpleadoBusquedaCuenta($idempleado, 'iece')->first();
                        $detalleDiario->cuenta_id = $tipo->cuenta_debe;
                        $detalleDiario->empleado_id = $idempleado;
                        $diariocontabilizado->detalles()->save($detalleDiario);
                        $general->registrarAuditoria('Registro de Detalle de Diario codigo: -> '.$diario->diario_codigo, '0', 'En la cuenta del Debe -> '.$tipo->cuenta_debe.' con el valor de: -> '.$IECESECAP);


                        $detalleDiario = new Detalle_Diario();
                        $detalleDiario->detalle_debe = 0.00;
                        $detalleDiario->detalle_haber = floatval($IECESECAP);
                        $detalleDiario->detalle_comentario = 'COMPROBANTE DE CONTABILIZACION MENSUAL DE ROLES '.$empleado->empleado_nombre.' de '.strftime("%B de %Y", strtotime($request->get('fechafinal')));
                        $detalleDiario->detalle_tipo_documento = 'CONTABILIZACION MENSUAL';
                        $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                        $detalleDiario->detalle_conciliacion = '0';
                        $detalleDiario->detalle_estado = '1';
                        $tipo=Empleado::EmpleadoBusquedaCuenta($idempleado, 'iece')->first();
                        $detalleDiario->cuenta_id = $tipo->cuenta_haber;
                        $detalleDiario->empleado_id = $idempleado;
                        $diariocontabilizado->detalles()->save($detalleDiario);
                        $general->registrarAuditoria('Registro de Detalle de Diario codigo: -> '.$diario->diario_codigo, '0', 'En la cuenta del Haber -> '.$tipo->cuenta_haber.' con el valor de: -> '.$IECESECAP);
                    }
                    */
                }

                $cabecera_rol->detalles()->save($detalle);
                $general->registrarAuditoria('Registro de Detalle de Detalle Rol individual DE EMPLEADO -> '.$empleado->empleado_nombre, '0', ' con el valor de: -> '.$totaldetalle);
                $cabecera_rol->cabecera_rol_pago=0;
                $quincena=0;
                $anticipos=0;
                $fondoreserva=0;
                $Dinpuesto=0;
                $Iessasu=0;
                $Iess=0;
                $Iesspatronal=0;
                $IECESECAP=0;
                $Dtercero=0;
                $Dcuarto=0;
                $Dterceroacu=0;
                $Dalimentacion=0;
                $Dcuartoacu=0;
                $Totalvacacion=0;
            }
            $cabecera=Rol_Consolidado::Rol($cabecera_rol->cabecera_rol_id)->get()->first();
            $url2 = $general->pdfRolDetalle($cabecera);
            $url = $general->pdfDiario($diario);
            // $url3 = $general->pdfDiario($diariocontabilizado);
               
            if ($request->get('tipo') == 'Cheque') {
                DB::commit();
                return redirect('rolindividual/new/'.$request->get('punto_id'))->with('success', 'Pago realizado exitosamente')->with('pdf', $url2)->with('diario', $url)->with('cheque', $urlcheque);
            }
            DB::commit();
            return redirect('/rolindividual/new/'.$request->get('punto_id'))->with('success', 'Datos guardados exitosamente')->with('pdf', $url2)->with('diario', $url);
        }
        catch(\Exception $ex){
            DB::rollBack();
            return redirect('/rolindividual/new/'.$request->get('punto_id'))->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
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
