<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Arqueo_Caja;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Ingreso_Caja;
use App\Models\Detalle_Diario;
use App\Models\Cuenta;
use App\Models\Banco;
use App\Models\Caja;
use App\Models\Diario;
use App\Models\Movimiento_Caja;
use App\Models\Punto_Emision;
use App\Models\Rango_Documento;
use App\Models\Sucursal;
use App\Models\Tipo_Movimiento_Caja;
use DateTime;

class ingresoCajaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return redirect('/denegado');
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
            $cajasxusuario=Arqueo_Caja::arqueoCajaxuser(Auth::user()->user_id)->first();                   
            $cuentacaja=Caja::caja($request->get('idCaja'))->first();
            $ingresoCaja = new Ingreso_Caja();
            $cierre = $general->cierre($request->get('idFecha'),$cuentacaja->sucursal_id);          
            if($cierre){
                return redirect('listaIngresoCaja')->with('error2','No puede realizar la operacion por que pertenece a un mes bloqueado');
            }
            
            $TipoMovimientoCaja=Tipo_Movimiento_Caja::tipoMovimiento($request->get('tipoId'))->first(); 
            $ingresoCaja->ingreso_numero = $request->get('ingreso_serie').substr(str_repeat(0, 9).$request->get('ingreso_numero'), - 9);
            $ingresoCaja->ingreso_serie = $request->get('ingreso_serie');
            $ingresoCaja->ingreso_secuencial = $request->get('ingreso_numero');
            $ingresoCaja->ingreso_fecha = $request->get('idFecha');
            $ingresoCaja->ingreso_tipo = 'EFECTIVO';            
            $ingresoCaja->ingreso_valor = $request->get('idValor');
            $ingresoCaja->ingreso_descripcion = $request->get('idMensaje');  
            $ingresoCaja->ingreso_beneficiario = $request->get('idBeneficiario');
            $ingresoCaja->arqueo_id = $cajasxusuario->arqueo_id;
            $ingresoCaja->rango_id = $request->get('rango_id');
            $ingresoCaja->tipo_id = $request->get('tipoId');
            $ingresoCaja->ingreso_estado = 1;  
            /**********************movimiento caja****************************/
            $movimientoCaja = new Movimiento_Caja();          
            $movimientoCaja->movimiento_fecha= $request->get('idFecha');
            $movimientoCaja->movimiento_hora=date("H:i:s");
            $movimientoCaja->movimiento_tipo="ENTRADA";
            $movimientoCaja->movimiento_descripcion= $request->get('idMensaje');
            $movimientoCaja->movimiento_valor= $request->get('idValor');
            $movimientoCaja->movimiento_documento="INGRESO DE CAJA";
            $movimientoCaja->movimiento_numero_documento= $request->get('ingreso_serie').substr(str_repeat(0, 9).$request->get('ingreso_numero'), - 9);
            $movimientoCaja->movimiento_estado = 1;
            $movimientoCaja->arqueo_id = $cajasxusuario->arqueo_id;     
            if(Auth::user()->empresa->empresa_contabilidad == '1'){
                /**********************asiento diario****************************/
                $diario = new Diario();
                $diario->diario_codigo = $general->generarCodigoDiario($request->get('idFecha'),'CICA');
                $diario->diario_fecha = $request->get('idFecha');
                $diario->diario_referencia = 'COMPROBANTE DE INGRESO DE CAJA';
                $diario->diario_tipo_documento = 'INGRESO DE CAJA';
                $diario->diario_numero_documento = $request->get('ingreso_serie').substr(str_repeat(0, 9).$request->get('ingreso_numero'), - 9);
                $diario->diario_beneficiario = $request->get('idBeneficiario');
                $diario->diario_tipo = 'CICA';
                $diario->diario_secuencial = substr($diario->diario_codigo, 8);
                $diario->diario_mes = DateTime::createFromFormat('Y-m-d', $request->get('idFecha'))->format('m');
                $diario->diario_ano = DateTime::createFromFormat('Y-m-d', $request->get('idFecha'))->format('Y');
                $diario->diario_comentario = 'COMPROBANTE DE INGRESO A CAJA: '.' '.$request->get('idMensaje');
                $diario->diario_cierre = '0';
                $diario->diario_estado = '1';
                $diario->empresa_id = Auth::user()->empresa_id;
                $diario->sucursal_id = $cuentacaja->sucursal_id;
                $diario->save();
                $ingresoCaja->diario()->associate($diario);
                $movimientoCaja->diario()->associate($diario);            
                $general->registrarAuditoria('Registro de Diario con codigo: -> '.$diario->diario_codigo, $diario->diario_codigo,'Tipo de Diario -> '.$diario->diario_referencia.'');
            
                /********************detalle de diario de venta********************/
                $detalleDiario = new Detalle_Diario();
                $detalleDiario->detalle_debe = $request->get('idValor');
                $detalleDiario->detalle_haber = 0.00 ;
                $detalleDiario->detalle_comentario = 'CUENTA DE INGRESO CAJA';
                $detalleDiario->detalle_tipo_documento = 'INGRESO CAJA';
                $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                $detalleDiario->detalle_conciliacion = '0';
                $detalleDiario->detalle_estado = '1';           
                $detalleDiario->cuenta_id = $cuentacaja->cuenta_id;
                $diario->detalles()->save($detalleDiario);
                $general->registrarAuditoria('Registro de Detalle de Diario codigo: -> '.$diario->diario_codigo, $diario->diario_codigo,'En la cuenta del debe -> '.$request->get('idCuentaContable').' con el valor de: -> '.$request->get('idValor'));
                
                $detalleDiario = new Detalle_Diario();
                $detalleDiario->detalle_debe = 0.00;
                $detalleDiario->detalle_haber = $request->get('idValor');
                $detalleDiario->detalle_comentario = 'CUENTA DE INGRESO CAJA ';
                $detalleDiario->detalle_tipo_documento = 'INGRESO DE CAJA';
                $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                $detalleDiario->detalle_conciliacion = '0';
                $detalleDiario->detalle_estado = '1';
                $detalleDiario->cuenta_id = $TipoMovimientoCaja->cuenta_id;
                $diario->detalles()->save($detalleDiario);
                $general->registrarAuditoria('Registro de Detalle de Diario codigo: -> '.$diario->diario_codigo, $diario->diario_codigo,'En la cuenta del Haber -> '.$request->get('idCuentaContable').' con el valor de: -> '.$request->get('idValor'));
            }             
            $ingresoCaja->save();
            $movimientoCaja->save();
            $url = '';
            if(Auth::user()->empresa->empresa_contabilidad == '1'){
                $general->registrarAuditoria('Registro de Ingreso de Caja -> '.' al beneficiario: '.$request->get('idBeneficiario'),$diario->diario_codigo,'Con motivo:'.$request->get('idMensaje'));
                $url = $general->pdfDiario($diario);
            }else{
                $general->registrarAuditoria('Registro de Ingreso de Caja -> '.' al beneficiario: '.$request->get('idBeneficiario'),'0','Con motivo:'.$request->get('idMensaje'));
            }
            DB::commit();            
            return redirect('listaIngresoCaja')->with('success','Datos guardados exitosamente')->with('diario',$url);
       }catch(\Exception $ex){
            DB::rollBack();
            return redirect('listaIngresoCaja')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
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
        try{    
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $cajasxusuario=Arqueo_Caja::arqueoCajaxuser(Auth::user()->user_id)->first();    
            $cajas = Caja::cajas()->get();
            $rangoDocumento=Rango_Documento::PuntoRango($id, 'Ingreso de Caja')->first();
            $arqueoCaja=Arqueo_Caja::arqueoCaja(Auth::user()->user_id)->first();
            if(empty($arqueoCaja)){            
                $cuentacaja = null;                   
            }else{
                $cuentacaja=Caja::caja($arqueoCaja->caja_id)->first();  
            }
            $sucursalp=Punto_Emision::punto($id)->first();
            $secuencial=1;        
            if($rangoDocumento){
                $secuencial=$rangoDocumento->rango_inicio;
                $secuencialAux=Ingreso_Caja::secuencial($rangoDocumento->rango_id)->max('ingreso_secuencial');
                if($secuencialAux){
                    $secuencial=$secuencialAux+1;
                }
                return view('admin.caja.ingresoCaja.nuevo',
                    ['sucursales'=>Sucursal::sucursales()->get(),
                    'TipoMovimientoCaja'=>Tipo_Movimiento_Caja::tipoMovimientos()->where('sucursal_id','=',$sucursalp->sucursal_id)->get(),
                    'cajasxusuario'=>$cajasxusuario, 
                    'cajas'=>$cajas, 
                    'bancos'=>Banco::bancos()->get(),
                    'secuencial'=>substr(str_repeat(0, 9).$secuencial, - 9),                
                    'PE'=>Punto_Emision::puntos()->get(),
                    'rangoDocumento'=>$rangoDocumento,
                    'cuentas'=>Cuenta::CuentasMovimiento()->get(),
                    'cuentacaja'=>$cuentacaja,
                    'sucursalp'=>$sucursalp->sucursal_id,
                    'tipoPermiso'=>$tipoPermiso, 
                    'gruposPermiso'=>$gruposPermiso, 
                    'permisosAdmin'=>$permisosAdmin]
                );
            }else{
                return redirect('inicio')->with('error','No tiene configurado, un punto de emisión o un rango de documentos para emitir facturas de venta, configueros y vuelva a intentar');
            }     
        }
        catch(\Exception $ex){      
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
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
    public function destroy($id)
    {
        return redirect('/denegado');
    }
}
