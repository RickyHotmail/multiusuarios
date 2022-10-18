<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Arqueo_Caja;
use App\Models\Caja;
use App\Models\Cuenta;
use App\Models\Detalle_Diario;
use App\Models\Diario;
use App\Models\Movimiento_Caja;
use App\Models\Parametrizacion_Contable;
use App\Models\Punto_Emision;
use App\Models\Rango_Documento;
use App\Models\Sobrante_Caja;
use App\Models\Sucursal;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class sobranteCajaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
        try{           
            DB::beginTransaction();
            $sucursalp=Punto_Emision::punto($request->get('punto_id'))->first();
            $parametrizacionc=Parametrizacion_Contable::parametrizacionByNombre($sucursalp->sucursal_id,'SOBRANTE DE CAJA')->first();
            $arqueoCaja=Arqueo_Caja::arqueoCaja(Auth::user()->user_id)->first();           
            $cuentacaja=Caja::caja($request->get('idCaja'))->first();

            $general = new generalController();
            $cierre = $general->cierre($request->get('idFecha'),$sucursalp->sucursal_id);          
            if($cierre){
                return redirect('listaSobranteCaja')->with('error2','No puede realizar la operacion por que pertenece a un mes bloqueado');
            }
           
            $sobranteCaja = new Sobrante_Caja();
            $sobranteCaja->sobrante_numero = $request->get('sobrante_serie').substr(str_repeat(0, 9).$request->get('sobrante_numero'), - 9);
            $sobranteCaja->sobrante_serie = $request->get('sobrante_serie');
            $sobranteCaja->sobrante_secuencial = $request->get('sobrante_numero');
            $sobranteCaja->sobrante_fecha = $request->get('idFecha');
            $sobranteCaja->sobrante_observacion = $request->get('idMensaje');
            $sobranteCaja->sobrante_monto = $request->get('idValor');  
            $sobranteCaja->arqueo_id = $arqueoCaja->arqueo_id;
            $sobranteCaja->rango_id = $request->get('rango_id');        
            $sobranteCaja->sobrante_estado = 1;
            
            /**********************movimiento caja****************************/
            $movimientoCaja = new Movimiento_Caja();          
            $movimientoCaja->movimiento_fecha=date("Y")."-".date("m")."-".date("d");
            $movimientoCaja->movimiento_hora=date("H:i:s");
            $movimientoCaja->movimiento_tipo="ENTRADA";
            $movimientoCaja->movimiento_descripcion= $request->get('idMensaje');
            $movimientoCaja->movimiento_valor= $request->get('idValor');
            $movimientoCaja->movimiento_documento="SOBRANTE DE CAJA";
            $movimientoCaja->movimiento_numero_documento= $request->get('sobrante_serie').substr(str_repeat(0, 9).$request->get('sobrante_numero'), - 9);
            $movimientoCaja->movimiento_estado = 1;
            $movimientoCaja->arqueo_id = $arqueoCaja->arqueo_id;
            
            if(Auth::user()->empresa->empresa_contabilidad == '1'){
                /**********************asiento diario****************************/
                $diario = new Diario();
                $diario->diario_codigo = $general->generarCodigoDiario($request->get('idFecha'),'CDSC');
                $diario->diario_fecha = $request->get('idFecha');
                $diario->diario_referencia = 'SOBRANTE DE CAJA';
                $diario->diario_tipo_documento = 'POR SOBRANTE DE CAJA';
                $diario->diario_numero_documento = $request->get('sobrante_serie').substr(str_repeat(0, 9).$request->get('sobrante_numero'), - 9);
                $diario->diario_beneficiario = "SIN BENEFICIARIO";
                $diario->diario_tipo = 'CDSC';
                $diario->diario_secuencial = substr($diario->diario_codigo, 8);
                $diario->diario_mes = DateTime::createFromFormat('Y-m-d', $request->get('idFecha'))->format('m');
                $diario->diario_ano = DateTime::createFromFormat('Y-m-d', $request->get('idFecha'))->format('Y');
                $diario->diario_comentario = 'COMPROBANTE DE SOBRANTE DE CAJA';
                $diario->diario_cierre = '0';
                $diario->diario_estado = '1';
                $diario->empresa_id = Auth::user()->empresa_id;
                $diario->sucursal_id = $sucursalp->sucursal_id;
                $diario->save();
                $sobranteCaja->diario()->associate($diario);
                $movimientoCaja->diario()->associate($diario);
                
                $general->registrarAuditoria('Registro de Diario por sobrate de caja con codigo: -> '.$diario->diario_codigo, $diario->diario_codigo,'Tipo de Diario -> '.$diario->diario_referencia.'');
                $general2 = new generalController();
                $general2->registrarAuditoria('Registro de Movimiento por Sobrante de caja: -> '.$request->get('sobrante_serie').substr(str_repeat(0, 9).$request->get('sobrante_numero'), - 9), $diario->diario_codigo,' con el valor de: -> '.$request->get('idValor'));
                
                /********************detalle de diario de venta********************/
                $detalleDiario = new Detalle_Diario();
                $detalleDiario->detalle_debe =$request->get('idValor');
                $detalleDiario->detalle_haber = 0.00;
                $detalleDiario->detalle_comentario = 'CUENTA DE EFECTIVO CAJA';
                $detalleDiario->detalle_tipo_documento = 'EFECTIVO';
                $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                $detalleDiario->detalle_conciliacion = '0';
                $detalleDiario->detalle_estado = '1';           
                $detalleDiario->cuenta_id = $cuentacaja->cuenta_id;
                $diario->detalles()->save($detalleDiario);
                $general->registrarAuditoria('Registro de Detalle de Diario codigo: -> '.$diario->diario_codigo, $diario->diario_codigo,'En la cuenta del Haber -> '.$cuentacaja->cuenta_id.' con el valor de: -> '.$request->get('idValor'));
                
                $detalleDiario = new Detalle_Diario();
                $detalleDiario->detalle_debe = 0.00;
                $detalleDiario->detalle_haber =  $request->get('idValor');
                $detalleDiario->detalle_comentario = 'CUENTA DE SOBRANTE DE CAJA ';
                $detalleDiario->detalle_tipo_documento = 'SOBRANTE DE CAJA';
                $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                $detalleDiario->detalle_conciliacion = '0';
                $detalleDiario->detalle_estado = '1';
                $detalleDiario->cuenta_id = $parametrizacionc->cuenta_id;  
                $diario->detalles()->save($detalleDiario);
                $general->registrarAuditoria('Registro de Detalle de Diario codigo: -> '.$diario->diario_codigo, $diario->diario_codigo,'En la cuenta del debe -> '.$request->get('idCuentaHaber').' con el valor de: -> '.$request->get('idValor'));
            }
            $sobranteCaja->save();
            $movimientoCaja->save();
            $url = '';
            if(Auth::user()->empresa->empresa_contabilidad == '1'){
                $general->registrarAuditoria('Registro de Sobrante de Caja -> '.' con numero: '.$request->get('sobrante_serie').substr(str_repeat(0, 9).$request->get('sobrante_numero'), - 9),$diario->diario_codigo,'Con motivo:'.$request->get('idMensaje'));
                $url = $general->pdfDiario($diario);
            }else{
                $general->registrarAuditoria('Registro de Sobrante de Caja -> '.' con numero: '.$request->get('sobrante_serie').substr(str_repeat(0, 9).$request->get('sobrante_numero'), - 9),'','Con motivo:'.$request->get('idMensaje'));
            }
            DB::commit();            
            return redirect('listaSobranteCaja')->with('success','Datos guardados exitosamente')->with('diario',$url);
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('inicio')->with('error2','Oucrrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
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
    public function nuevo($id){
        try{   
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $rangoDocumento=Rango_Documento::PuntoRango($id, 'Sobrante de Caja')->first();
            $cajasxusuario=Arqueo_Caja::arqueoCajaxuser(Auth::user()->user_id)->first();    
            $cajas = Caja::cajas()->get();
            $arqueoCaja=Arqueo_Caja::arqueoCaja(Auth::user()->user_id)->first();                      
            if($arqueoCaja){        
                $secuencial=1;        
                if($rangoDocumento){
                    $secuencial=$rangoDocumento->rango_inicio;
                    $secuencialAux=Sobrante_Caja::secuencial($rangoDocumento->rango_id)->max('sobrante_secuencial');
                    if($secuencialAux){
                        $secuencial=$secuencialAux+1;
                    }
                    return view('admin.caja.sobranteCaja.nuevo',
                        ['secuencial'=>substr(str_repeat(0, 9).$secuencial, - 9),                
                        'PE'=>Punto_Emision::puntos()->get(),
                        'rangoDocumento'=>$rangoDocumento,
                        'cajasxusuario'=>$cajasxusuario,
                        'cajas'=>$cajas,
                        'gruposPermiso'=>$gruposPermiso, 
                        'permisosAdmin'=>$permisosAdmin]
                    );
                }else{
                    return redirect('inicio')->with('error','No tiene configurado, un punto de emisión o un rango de documentos para emitir facturas de venta, configueros y vuelva a intentar');
                }
            }else{
                return redirect('inicio')->with('error','No tiene una apertura de caja, Aperture una caja y vuelva a intentar');            
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
