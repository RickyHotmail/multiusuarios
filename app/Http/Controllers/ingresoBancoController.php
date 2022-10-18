<?php

namespace App\Http\Controllers;

use App\Models\Banco;
use App\Models\Cuenta_Bancaria;
use App\Models\Deposito;
use App\Models\Detalle_Diario;
use App\Models\Diario;
use App\Models\Ingreso_Banco;
use App\Models\Punto_Emision;
use App\Models\Rango_Documento;
use App\Models\Tipo_Movimiento_Banco;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ingresoBancoController extends Controller
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
            $rangoDocumento = Rango_Documento::Rango($request->get('rango_id'))->first();
            $general = new generalController();
            $ingresoBanco = new Ingreso_Banco();
            $cierre = $general->cierre($request->get('idFecha'),$rangoDocumento->puntoEmision->sucursal_id);          
            if($cierre){
                return redirect('/ingresoBanco/new/'.$request->get('punto_id'))->with('error2','No puede realizar la operacion por que pertenece a un mes bloqueado');
            }
            $ingresoBanco->ingreso_numero = $request->get('ingreso_serie').substr(str_repeat(0, 9).$request->get('ingreso_numero'), - 9);
            $ingresoBanco->ingreso_serie = $request->get('ingreso_serie');
            $ingresoBanco->ingreso_secuencial = $request->get('ingreso_numero');
            $ingresoBanco->ingreso_fecha = $request->get('idFecha');
            $ingresoBanco->ingreso_valor = $request->get('idValor');
            $ingresoBanco->ingreso_descripcion = $request->get('idMensaje');  
            $ingresoBanco->ingreso_beneficiario = $request->get('idBeneficiario');
            $ingresoBanco->rango_id = $request->get('rango_id');
            $ingresoBanco->cuenta_bancaria_id= $request->get('cuenta_id');
            $ingresoBanco->ingreso_estado = 1; 
            $ingresoBanco->tipo_id = $request->get('tipo_movimiento');

            if($request->get('idTipo') == 'DEPOSITO'){
                $banco = Banco::Banco($request->get('banco_id'))->first();               
                $deposito = new Deposito();
                $deposito->deposito_descripcion = 'Deposito '.' - '.$request->get('idMensaje');  
                $deposito->deposito_fecha = $request->get('idFecha');
                $deposito->deposito_tipo = 'DEPOSITO';
                $deposito->deposito_numero = $request->get('idNumD');
                $deposito->deposito_valor = $request->get('idValor');
                $deposito->cuenta_bancaria_id = $request->get('cuenta_id');
                $deposito->deposito_estado = '1';
                $deposito->empresa_id = Auth::user()->empresa->empresa_id;
                $deposito->save();
                $ingresoBanco->deposito()->associate($deposito);
                $general->registrarAuditoria('Registro de deposito del banco',$banco->banco_lista_nombre,'Registro de depostio por un valor de '.$request->get('idValor')); 
            }
            if($request->get('idTipo') == 'TRANSFERENCIA'){
                $banco = Banco::Banco($request->get('banco_id'))->first();               
                $deposito = new Deposito();
                $deposito->deposito_descripcion = 'Transferencia '.' - '.$request->get('idMensaje');  
                $deposito->deposito_fecha = $request->get('idFecha');
                $deposito->deposito_tipo = 'TRANSFERENCIA';
                $deposito->deposito_numero = $request->get('idNumD');
                $deposito->deposito_valor = $request->get('idValor');
                $deposito->cuenta_bancaria_id = $request->get('cuenta_id');
                $deposito->deposito_estado = '1';
                $deposito->empresa_id = Auth::user()->empresa->empresa_id;
                $deposito->save();
                $ingresoBanco->deposito()->associate($deposito);
                $general->registrarAuditoria('Registro de Transferencia del banco',$banco->banco_lista_nombre,'Registro de Transferencia por un valor de '.$request->get('idValor')); 
            }
            /**********************asiento diario****************************/
            $general = new generalController();
            $diario = new Diario();
            $diario->diario_codigo = $general->generarCodigoDiario($request->get('idFecha'),'CIBA');
            $diario->diario_fecha = $request->get('idFecha');
            $diario->diario_referencia = 'COMPROBANTE DE INGRESO DE BANCO';
            $diario->diario_tipo_documento = 'INGRESO DE BANCO';
            $diario->diario_numero_documento = $request->get('ingreso_serie').substr(str_repeat(0, 9).$request->get('ingreso_numero'), - 9);
            $diario->diario_beneficiario = $request->get('idBeneficiario');
            $diario->diario_tipo = 'CIBA';
            $diario->diario_secuencial = substr($diario->diario_codigo, 8);
            $diario->diario_mes = DateTime::createFromFormat('Y-m-d', $request->get('idFecha'))->format('m');
            $diario->diario_ano = DateTime::createFromFormat('Y-m-d', $request->get('idFecha'))->format('Y');
            $diario->diario_comentario = 'COMPROBANTE DE INGRESO DE BANCO: '.$request->get('idBeneficiario');
            $diario->diario_cierre = '0';
            $diario->diario_estado = '1';
            $diario->empresa_id = Auth::user()->empresa_id;
            $diario->sucursal_id = $rangoDocumento->puntoEmision->sucursal_id;
            $diario->save();
            $ingresoBanco->diario()->associate($diario);            
            
            $general->registrarAuditoria('Registro de Diario de Diario codigo: -> '.$diario->diario_codigo, $diario->diario_codigo,'Tipo de Diario -> '.$diario->diario_referencia.'');
            /********************detalle de diario de venta********************/
            $detalleDiario = new Detalle_Diario();
            $detalleDiario->detalle_debe = 0.00;
            $detalleDiario->detalle_haber = $request->get('idValor');
            $detalleDiario->detalle_comentario = 'P/R '.$request->get('idMensaje');
            $detalleDiario->detalle_tipo_documento = 'INGRESO DE BANCO';
            $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
            $detalleDiario->detalle_conciliacion = '0';
            $detalleDiario->detalle_estado = '1';           
            $tipo = Tipo_Movimiento_Banco::findOrFail($request->get('tipo_movimiento'));
            $detalleDiario->cuenta_id = $tipo->cuenta_id;
            $diario->detalles()->save($detalleDiario);
            $general->registrarAuditoria('Registro de Detalle de Diario codigo: -> '.$diario->diario_codigo, $diario->diario_codigo,'En la cuenta del debe -> '.$tipo->cuenta->cuenta_numero.' con el valor de: -> '.$request->get('idValor'));
            
            $detalleDiario = new Detalle_Diario();
            $detalleDiario->detalle_debe = $request->get('idValor');
            $detalleDiario->detalle_haber = 0.00;
            $detalleDiario->detalle_comentario = 'P/R CUENTA DE INGRESO DE BANCO '.$ingresoBanco->ingreso_motivo;
            $detalleDiario->detalle_tipo_documento = 'INGRESO DE BANCO';
            $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
            $detalleDiario->detalle_conciliacion = '0';
            $detalleDiario->detalle_estado = '1'; 
            if($request->get('idTipo') == 'DEPOSITO'){
                $detalleDiario->deposito()->associate($deposito);
            }
            if($request->get('idTipo') == 'TRANSFERENCIA'){
                $detalleDiario->deposito()->associate($deposito);
            }
            $cuentaB = Cuenta_Bancaria::findOrFail($request->get('cuenta_id'));
            $detalleDiario->cuenta_id = $cuentaB->cuenta_id;       
            $diario->detalles()->save($detalleDiario);
            $general->registrarAuditoria('Registro de Detalle de Diario codigo: -> '.$diario->diario_codigo, $diario->diario_codigo,'En la cuenta del Haber -> '.$cuentaB->cuenta->cuenta_id.' con el valor de: -> '.$request->get('idValor'));               
            
            $ingresoBanco->save();            
            /*Inicio de registro de auditoria */
            $auditoria = new generalController();
            $auditoria->registrarAuditoria('Registro de Ingreso de Banco -> ',$diario->diario_codigo,'Con motivo:'.$request->get('idMensaje'));
            /*Fin de registro de auditoria */
            $url = $general->pdfDiario($diario);
            DB::commit();
            return redirect('/ingresoBanco/new/'.$request->get('punto_id'))->with('success','Datos guardados exitosamente')->with('diario',$url);
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('/ingresoBanco/new/'.$request->get('punto_id'))->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
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
    public function nuevo($id){
        try{             
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $rangoDocumento=Rango_Documento::PuntoRango($id, 'Ingreso de Banco')->first();        
            $sucursalp=Punto_Emision::punto($id)->first();
            $secuencial=1;        
            if($rangoDocumento){
                $secuencial=$rangoDocumento->rango_inicio;
                $secuencialAux=Ingreso_Banco::secuencial($rangoDocumento->rango_id)->max('ingreso_secuencial');
                if($secuencialAux){
                    $secuencial=$secuencialAux+1;
                }
                return view('admin.bancos.ingresoBanco.nuevo',
                    ['movimientos'=>Tipo_Movimiento_Banco::TipoMovimientos()->where('sucursal_id','=',$sucursalp->sucursal_id)->get(), 
                    'bancos'=>Banco::bancos()->get(),
                    'secuencial'=>substr(str_repeat(0, 9).$secuencial, - 9),                
                    'PE'=>Punto_Emision::puntos()->get(),
                    'rangoDocumento'=>$rangoDocumento,
                    'sucursalp'=>$sucursalp->sucursal_id,
                    'gruposPermiso'=>$gruposPermiso, 
                    'permisosAdmin'=>$permisosAdmin]
                );
            }else{
                return redirect('inicio')->with('error','No tiene configurado, un punto de emisión o un rango de documentos para emitir ingresos de banco, configueros y vuelva a intentar');
            } 
        }
        catch(\Exception $ex){      
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }       
    }
}
