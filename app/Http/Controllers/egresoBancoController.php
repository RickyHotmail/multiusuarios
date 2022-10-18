<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Banco;
use App\Models\Banco_Lista;
use App\Models\Cheque;
use App\Models\Cheque_Impresion;
use App\Models\Cuenta;
use App\Models\Cuenta_Bancaria;
use App\Models\Detalle_Diario;
use App\Models\Diario;
use App\Models\Egreso_Banco;
use App\Models\Punto_Emision;
use App\Models\Rango_Documento;
use App\Models\Sucursal;
use App\Models\Tipo_Movimiento_Banco;
use App\Models\Transferencia;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Luecano\NumeroALetras\NumeroALetras;

class egresoBancoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return redirect('/listaEgresoBanco');
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
            $urlcheque = '';
            $rangoDocumento = Rango_Documento::Rango($request->get('rango_id'))->first();
            $egresoBanco = new Egreso_Banco();
            $general = new generalController();
            $cierre = $general->cierre($request->get('idFecha'),$rangoDocumento->puntoEmision->sucursal_id);          
            if($cierre){
                return redirect('/egresoBanco/new/'.$request->get('punto_id'))->with('error2','No puede realizar la operacion por que pertenece a un mes bloqueado');
            }
            $egresoBanco->egreso_numero = $request->get('egreso_serie').substr(str_repeat(0, 9).$request->get('egreso_numero'), - 9);
            $egresoBanco->egreso_serie = $request->get('egreso_serie');
            $egresoBanco->egreso_secuencial = $request->get('egreso_numero');
            $egresoBanco->egreso_fecha = $request->get('idFecha');
            $egresoBanco->egreso_valor = $request->get('idValor');
            $egresoBanco->egreso_descripcion = $request->get('idMensaje');  
            $egresoBanco->egreso_beneficiario = $request->get('idBeneficiario');
            $egresoBanco->rango_id = $request->get('rango_id');
            $egresoBanco->cuenta_bancaria_id= $request->get('cuenta_id');
            $egresoBanco->egreso_estado = 1; 
            $egresoBanco->tipo_id = $request->get('tipo_movimiento');
            /*REGISTRO DE DECHQUE*/ 
            if($request->get('idTipo') == 'CHEQUE'){
                $formatter = new NumeroALetras();
                $cheque = new Cheque();
                $cheque->cheque_numero = $request->get('idNcheque');
                $cheque->cheque_descripcion = $request->get('idMensaje');
                $cheque->cheque_beneficiario = $request->get('idBeneficiariocheque');
                $cheque->cheque_fecha_emision = $request->get('idFecha');
                $cheque->cheque_fecha_pago = $request->get('idFechaCheque');
                $cheque->cheque_valor = $request->get('idValor');
                $cheque->cheque_valor_letras = $formatter->toInvoice($cheque->cheque_valor, 2, 'Dolares');
                $cheque->cuenta_bancaria_id = $request->get('cuenta_id');      
                $cheque->cheque_estado = '1';
                $cheque->empresa_id = Auth::user()->empresa->empresa_id;
                $cheque->save();
                $egresoBanco->cheque()->associate($cheque);
                $general->registrarAuditoria('Registro de Cheque de egreso de banco numero : -> '.$request->get('idNcheque'),'0','Por motivo de: -> '.$request->get('idMensaje').' con el valor de: -> '.$request->get('idValor'));
                $urlcheque = $general->pdfImprimeCheque($request->get('cuenta_id'),$cheque);
            }
            /*REGISTRO DATOS DE TRASNFERENCIA*/                
            if($request->get('idTipo') == 'TRANSFERENCIA'){
                $banco = Banco::Banco($request->get('banco_id'))->first();               
                $transferencia = new Transferencia();
                $transferencia->transferencia_descripcion = 'Transferencia '.' - '.$request->get('idMensaje');  
                $transferencia->transferencia_beneficiario = $request->get('idBeneficiario');
                $transferencia->transferencia_fecha = $request->get('idFecha');
                $transferencia->transferencia_valor = $request->get('idValor');
                $transferencia->cuenta_bancaria_id = $request->get('cuenta_id');
                $transferencia->transferencia_estado = '1';
                $transferencia->empresa_id = Auth::user()->empresa->empresa_id;
                $transferencia->save();
                $egresoBanco->transferencia()->associate($transferencia);
                $general->registrarAuditoria('Registro de transferencia de egreso de banco del banco',$banco->banco_lista_nombre,'Registro de transferencia. '.' por un valor de '.$request->get('idValor')); 
               
            }
            /**********************asiento diario****************************/
           

            $general = new generalController();
            $diario = new Diario();
            $diario->diario_codigo = $general->generarCodigoDiario($request->get('idFecha'),'CEBA');
            $diario->diario_fecha = $request->get('idFecha');
            $diario->diario_referencia = 'COMPROBANTE DE EGRESO DE BANCO';
            $diario->diario_tipo_documento = 'EGRESO DE BANCO';
            $diario->diario_numero_documento = $request->get('egreso_serie').substr(str_repeat(0, 9).$request->get('egreso_numero'), - 9);
            $diario->diario_beneficiario = $request->get('idBeneficiario');
            $diario->diario_tipo = 'CEBA';
            $diario->diario_secuencial = substr($diario->diario_codigo, 8);
            $diario->diario_mes = DateTime::createFromFormat('Y-m-d', $request->get('idFecha'))->format('m');
            $diario->diario_ano = DateTime::createFromFormat('Y-m-d', $request->get('idFecha'))->format('Y');
            $diario->diario_comentario = 'COMPROBANTE DE EGRESO DE BANCO: '.$request->get('idBeneficiario');
            if($request->get('idTipo') == 'CHEQUE'){
                $diario->diario_comentario = 'COMPROBANTE DE EGRESO DE BANCO: '.$request->get('idBeneficiario').' CHEQUE : '.$cheque->cheque_numero;
            }
            if($request->get('idTipo') == 'TRANSFERENCIA'){
                $diario->diario_comentario = 'COMPROBANTE DE EGRESO DE BANCO: '.$request->get('idBeneficiario').' TRANSFERENCIA ';
            }
            $diario->diario_cierre = '0';
            $diario->diario_estado = '1';
            $diario->empresa_id = Auth::user()->empresa_id;
            $diario->sucursal_id = $rangoDocumento->puntoEmision->sucursal_id;
            $diario->save();
            $egresoBanco->diario()->associate($diario);            
            
            $general->registrarAuditoria('Registro de Diario de Diario codigo: -> '.$diario->diario_codigo, $diario->diario_codigo,'Tipo de Diario -> '.$diario->diario_referencia.'');
            /********************detalle de diario de venta********************/
            $detalleDiario = new Detalle_Diario();
            $detalleDiario->detalle_debe = $request->get('idValor');
            $detalleDiario->detalle_haber = 0.00 ;
            $detalleDiario->detalle_comentario = 'P/R '.$request->get('idMensaje');
            $detalleDiario->detalle_tipo_documento = 'EGRESO DE BANCO';
            $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
            $detalleDiario->detalle_conciliacion = '0';
            $detalleDiario->detalle_estado = '1';     
            $tipo = Tipo_Movimiento_Banco::findOrFail($request->get('tipo_movimiento'));
            $detalleDiario->cuenta_id = $tipo->cuenta_id;
            $diario->detalles()->save($detalleDiario);
            $general->registrarAuditoria('Registro de Detalle de Diario codigo: -> '.$diario->diario_codigo, $diario->diario_codigo,'En la cuenta del debe -> '.$tipo->cuenta->cuenta_numero.' con el valor de: -> '.$request->get('idValor'));
            
            $detalleDiario = new Detalle_Diario();
            $detalleDiario->detalle_debe = 0.00;
            $detalleDiario->detalle_haber = $request->get('idValor');
            $detalleDiario->detalle_comentario = 'P/R CUENTA DE EGRESO DE BANCO '.$egresoBanco->egreso_motivo;
            $detalleDiario->detalle_tipo_documento = 'EGRESO DE BANCO';
            $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
            $detalleDiario->detalle_conciliacion = '0';
            $detalleDiario->detalle_estado = '1'; 
            if ($request->get('idTipo') == 'CHEQUE'){      
                $detalleDiario->cheque()->associate($cheque);
            }
            if ($request->get('idTipo') == 'TRANSFERENCIA'){      
                $detalleDiario->transferencia()->associate($transferencia);
            }
            $cuentaB = Cuenta_Bancaria::findOrFail($request->get('cuenta_id'));
            $detalleDiario->cuenta_id = $cuentaB->cuenta_id;            
            $diario->detalles()->save($detalleDiario);
            $general->registrarAuditoria('Registro de Detalle de Diario codigo: -> '.$diario->diario_codigo, $diario->diario_codigo,'En la cuenta del Haber -> '.$cuentaB->cuenta->cuenta_id.' con el valor de: -> '.$request->get('idValor'));
            $egresoBanco->save();            
            /*Inicio de registro de auditoria */
            $auditoria = new generalController();
            $auditoria->registrarAuditoria('Registro de Egreso de Banco -> '.$request->get('idBeneficiario'),$diario->diario_codigo,'Con motivo:'.$request->get('idMensaje'));
            /*Fin de registro de auditoria */
            $url = $general->pdfDiario($diario);
            if ($request->get('idTipo') == 'CHEQUE') {
                DB::commit();
                return redirect('/egresoBanco/new/'.$request->get('punto_id'))->with('success','Datos guardados exitosamente')->with('diario',$url)->with('cheque',$urlcheque);
            }            
            DB::commit();
            return redirect('/egresoBanco/new/'.$request->get('punto_id'))->with('success','Datos guardados exitosamente')->with('diario',$url);
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('/egresoBanco/new/'.$request->get('punto_id'))->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
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
        $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
        $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
        $rangoDocumento=Rango_Documento::PuntoRango($id, 'Egreso de Banco')->first();        
        $sucursalp=Punto_Emision::punto($id)->first();
        $secuencial=1;        
        if($rangoDocumento){
            $secuencial=$rangoDocumento->rango_inicio;
            $secuencialAux=Egreso_Banco::secuencial($rangoDocumento->rango_id)->max('egreso_secuencial');
            if($secuencialAux){
                $secuencial=$secuencialAux+1;
            }
            return view('admin.bancos.egresoBanco.nuevo',
                ['movimientos'=>Tipo_Movimiento_Banco::TipoMovimientos()->where('sucursal_id','=',$sucursalp->sucursal_id)->get(), 
                'bancos'=>Banco::bancos()->get(),
                'secuencial'=>substr(str_repeat(0, 9).$secuencial, - 9),                
                'PE'=>Punto_Emision::puntos()->get(),
                'rangoDocumento'=>$rangoDocumento,
                'tipoPermiso'=>$tipoPermiso,
                'sucursalp'=>$sucursalp->sucursal_id,
                'gruposPermiso'=>$gruposPermiso, 
                'permisosAdmin'=>$permisosAdmin]
            );
        }else{
            return redirect('inicio')->with('error','No tiene configurado, un punto de emisión o un rango de documentos para emitir egresos de banco, configueros y vuelva a intentar');
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
