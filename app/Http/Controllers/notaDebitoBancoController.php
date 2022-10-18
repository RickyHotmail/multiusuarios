<?php

namespace App\Http\Controllers;

use App\Models\Banco;
use App\Models\Cuenta;
use App\Models\Cuenta_Bancaria;
use App\Models\Detalle_Diario;
use App\Models\Diario;
use App\Models\Movimiento_Nota_Debito;
use App\Models\Nota_Debito_banco;
use App\Models\Punto_Emision;
use App\Models\Rango_Documento;
use App\Models\Tipo_Movimiento_Banco;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class notaDebitoBancoController extends Controller
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
        try {
            DB::beginTransaction();
            $rangoDocumento = Rango_Documento::Rango($request->get('rango_id'))->first();
            $nota = new Nota_Debito_banco();
            $general = new generalController();
            $cierre = $general->cierre($request->get('idFecha'),$rangoDocumento->puntoEmision->sucursal_id);          
            if($cierre){
                return redirect('/notaCreditoBanco/new/'.$request->get('punto_id'))->with('error2','No puede realizar la operacion por que pertenece a un mes bloqueado');
            }
            $nota->nota_numero = $request->get('nota_serie').substr(str_repeat(0, 9).$request->get('nota_numero'), - 9);
            $nota->nota_serie = $request->get('nota_serie');
            $nota->nota_secuencial = $request->get('nota_numero');
            $nota->nota_fecha = $request->get('idFecha');
            $nota->nota_valor = $request->get('IdTOTAL');
            $nota->nota_descripcion = $request->get('idMensaje');  
            $nota->nota_beneficiario = $request->get('idBeneficiario');
            $nota->cuenta_bancaria_id= $request->get('cuentaB_id');
            $nota->rango_id = $request->get('rango_id');
            $nota->nota_estado = 1; 
            /**********************asiento diario****************************/
            $general = new generalController();
            $diario = new Diario();
            $diario->diario_codigo = $general->generarCodigoDiario($request->get('idFecha'),'CNDB');
            $diario->diario_fecha = $request->get('idFecha');
            $diario->diario_referencia = 'COMPROBANTE DE NOTA DE DEBITO BANCARIA';
            $diario->diario_tipo_documento = 'NOTA DE DEBITO DE BANCO';
            $diario->diario_numero_documento = $request->get('nota_serie').substr(str_repeat(0, 9).$request->get('nota_numero'), - 9);
            $diario->diario_beneficiario = $request->get('idBeneficiario');
            $diario->diario_tipo = 'CNDB';
            $diario->diario_secuencial = substr($diario->diario_codigo, 8);
            $diario->diario_mes = DateTime::createFromFormat('Y-m-d', $request->get('idFecha'))->format('m');
            $diario->diario_ano = DateTime::createFromFormat('Y-m-d', $request->get('idFecha'))->format('Y');
            $diario->diario_comentario = 'COMPROBANTE DE NOTA DE DEBITO BANCARIA: '.$request->get('idMensaje');
            $diario->diario_cierre = '0';
            $diario->diario_estado = '1';
            $diario->empresa_id = Auth::user()->empresa_id;
            $diario->sucursal_id = $rangoDocumento->puntoEmision->sucursal_id;
            $diario->save();
            $nota->diario()->associate($diario);            
            $general->registrarAuditoria('Registro de Diario de Diario codigo: -> '.$diario->diario_codigo, $diario->diario_codigo,'Tipo de Diario -> '.$diario->diario_referencia.'');
              
            $nota->save();
            $general->registrarAuditoria('Registro de Nota de Debito de Banco -> '.$request->get('idBeneficiario'),$diario->diario_codigo,'Con motivo:'.$request->get('idMensaje'));
            
             //DETALLE DIARIOS DEBE
             $cuentaId = $request->get('DidCuenta');    
             $tipo = $request->get('Dtipo');       
             $debe = $request->get('Ddebe');
             //$haber = $request->get('Dhaber');
             $descripcion = $request->get('Ddescripcion');
             for ($i = 2; $i < count($cuentaId); ++$i){                
                 if($cuentaId[$i] <> ''){
                     $idCuentaContable = Tipo_Movimiento_Banco::TipoMovimiento($cuentaId[$i])->first();                
                     $detalleDiario = new Detalle_Diario();
                     if($tipo[$i]=='CREDITO'){
                        $detalleDiario->detalle_debe = 0;
                        $detalleDiario->detalle_haber = $debe[$i];
                     }
                     if($tipo[$i]=='DEBITO'){
                        $detalleDiario->detalle_debe = $debe[$i];
                        $detalleDiario->detalle_haber = 0;
                     }
                   
                     $detalleDiario->detalle_comentario = $descripcion[$i];
                     $detalleDiario->detalle_tipo_documento = 'NOTA DE DEBITO DE BANCO';
                     $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                     $detalleDiario->detalle_conciliacion = '0';
                     $detalleDiario->detalle_estado = '1';           
                     $detalleDiario->cuenta_id = $idCuentaContable->cuenta_id;
                     $diario->detalles()->save($detalleDiario);
                     $general->registrarAuditoria('Registro de Detalle de Diario codigo: -> '.$diario->diario_codigo, $diario->diario_codigo,'');
                     //guarda muchos tipos de movimiento
                     $movimientoNotaDebito = new Movimiento_Nota_Debito();
                     $movimientoNotaDebito->movimientond_tipo = $tipo[$i];
                     $movimientoNotaDebito->movimientond_valor = $debe[$i];
                     $movimientoNotaDebito->movimientond_descripcion = $descripcion[$i];
                     $movimientoNotaDebito->notaDebitoBanco()->associate($nota);
                     $movimientoNotaDebito->tipo_id = $cuentaId[$i];
                     $movimientoNotaDebito->save();
                 }
             }
              //DETALLE DIARIOS HABER
              $detalleDiario = new Detalle_Diario();
              $detalleDiario->detalle_debe = 0.00;
              $detalleDiario->detalle_haber = $request->get('IdTOTAL');
              $detalleDiario->detalle_comentario = 'P/R '.$request->get('idMensaje');
              $detalleDiario->detalle_tipo_documento = 'NOTA DE DEBITO DE BANCO';
              $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
              $detalleDiario->detalle_conciliacion = '0';
              $detalleDiario->detalle_estado = '1'; 
              $cuentaID = Cuenta_Bancaria::CuentaBancaria($request->get('cuentaB_id'))->first();                
              $detalleDiario->cuenta_id = $cuentaID->cuenta_id;
              $diario->detalles()->save($detalleDiario);
              $general->registrarAuditoria('Registro de Detalle de NOTA DE DEBITO DE BANCO: -> '.$diario->diario_codigo, $diario->diario_codigo,'En la cuenta del Haber -> '.$cuentaID->cuenta_numero.' con el valor de: -> '.$request->get('IdTOTAL'));
                         
            $url = $general->pdfDiario($diario);
            DB::commit();
            return redirect('/notaDebitoBanco/new/'.$request->get('punto_id'))->with('success','Datos guardados exitosamente')->with('diario',$url);;
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('/notaDebitoBanco/new/'.$request->get('punto_id'))->with('error2','Oucrrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
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
            $rangoDocumento=Rango_Documento::PuntoRango($id, 'Nota de Débito de Banco')->first();        
            $sucursalp=Punto_Emision::punto($id)->first();
            $secuencial=1;        
            if($rangoDocumento){
                $secuencial=$rangoDocumento->rango_inicio;
                $secuencialAux=Nota_Debito_banco::secuencial($rangoDocumento->rango_id)->max('nota_secuencial');
                if($secuencialAux){
                    $secuencial=$secuencialAux+1;
                }
                return view('admin.bancos.notaDebitoBancaria.nuevo',
                    ['movimientos'=>Tipo_Movimiento_Banco::TipoMovimientos()->where('sucursal_id','=',$sucursalp->sucursal_id)->get(),
                    'bancos'=>Banco::bancos()->get(),
                    'secuencial'=>substr(str_repeat(0, 9).$secuencial, - 9),                
                    'PE'=>Punto_Emision::puntos()->get(),
                    'rangoDocumento'=>$rangoDocumento,
                    'gruposPermiso'=>$gruposPermiso, 
                    'cuentas'=>Cuenta::CuentasMovimiento()->get(),
                    'permisosAdmin'=>$permisosAdmin]
                );
            }else{
                return redirect('inicio')->with('error','No tiene configurado, un punto de emisión o un rango de documentos para emitir nota de debito de banco, configueros y vuelva a intentar');
            }   
        }
        catch(\Exception $ex){      
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }      
    }    
}
