<?php

namespace App\Http\Controllers;

use App\Models\Auditoria;
use App\Models\Banco;
use App\Models\Cabecera_Rol_CM;
use App\Models\Cheque;
use App\Models\Cheque_Impresion;
use App\Models\Cierre_Mes_Contable;
use App\Models\Cuenta_Bancaria;
use App\Models\Decimo_Cuarto;
use App\Models\Decimo_Tercero;
use App\Models\Diario;
use App\Models\Documento_Anulado;
use App\Models\Egreso_Bodega;
use App\Models\Empleado;
use App\Models\Empresa;
use App\Models\Factura_Venta;
use App\Models\Guia_Remision;
use App\Models\Ingreso_Bodega;
use App\Models\Movimiento_Prestamo_Producto;
use App\Models\Movimiento_Producto;
use App\Models\Nota_Entrega;
use App\Models\Orden_Despacho;
use App\Models\Orden_Examen;
use App\Models\Pais;
use App\Models\Prefactura_Venta;
use App\Models\Proforma;
use App\Models\Provincia;
use App\Models\Punto_Emision;
use App\Models\Rol_Consolidado;
use App\Models\Rubro;
use App\Models\Servidor_Correo;
use App\Models\Suscripcion;
use DateTime;
use PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Luecano\NumeroALetras\NumeroALetras;
use PHPMailer\PHPMailer\PHPMailer;
use PhpParser\Node\Stmt\Return_;

class generalController extends Controller
{
    public static function redondear2Dec(float $numero){
        $numero=str_replace(',','',$numero);

        if(count(explode(".", $numero))==1) return number_format($numero,2,".","");

        $decimal = explode(".", $numero)[1];

        if(substr($decimal, 2, 1)==5 && substr($decimal, 1, 1) % 2 ==0)  return bcdiv($numero, 1, 2);
        return number_format(round($numero, 2), 2,".","");
    }
    public function documentos(){
        $suscripcion=Suscripcion::suscripcion()->first();
        $fechaactual=date('Y-m-d');
        if($suscripcion->suscripcion_ilimitada == '1'){
            return false;
        }
        if($suscripcion->suscripcion_estado=='0'){
            return true;
        }
        if($suscripcion->suscripcion_fecha_finalizacion < $fechaactual){
            return true;
        }
        $saldos=DB::select(DB::raw("SELECT SUM(T.Cantidad) FROM 
        (
        select count(factura_id) as Cantidad  from factura_venta,bodega,sucursal where factura_venta.bodega_id=bodega.bodega_id and bodega.sucursal_id=sucursal.sucursal_id and sucursal.empresa_id=".Auth::user()->empresa_id." and factura_venta.factura_estado<>'2' and factura_fecha>='".$suscripcion->suscripcion_fecha_inicio."'
        UNION ALL 
        select count(retencion_id) as Cantidad from retencion_compra,rango_documento,punto_emision,sucursal where retencion_compra.rango_id=rango_documento.rango_id and punto_emision.punto_id=rango_documento.punto_id and punto_emision.sucursal_id=sucursal.sucursal_id and sucursal.empresa_id=".Auth::user()->empresa_id." and retencion_fecha>='".$suscripcion->suscripcion_fecha_inicio."'
        UNION ALL 
        select count(nc_id) as Cantidad from nota_credito,rango_documento,punto_emision,sucursal where nota_credito.rango_id=rango_documento.rango_id and punto_emision.punto_id=rango_documento.punto_id and punto_emision.sucursal_id=sucursal.sucursal_id and sucursal.empresa_id=".Auth::user()->empresa_id." and nc_fecha>='".$suscripcion->suscripcion_fecha_inicio."'
        UNION ALL 
            select count(nd_id) as Cantidad from nota_debito,rango_documento,punto_emision,sucursal where nota_debito.rango_id=rango_documento.rango_id and punto_emision.punto_id=rango_documento.punto_id and punto_emision.sucursal_id=sucursal.sucursal_id and sucursal.empresa_id=".Auth::user()->empresa_id." and nd_fecha>='".$suscripcion->suscripcion_fecha_inicio."'
        UNION ALL 
            select count(lc_id) as Cantidad from liquidacion_compra,rango_documento,punto_emision,sucursal where liquidacion_compra.rango_id=rango_documento.rango_id and punto_emision.punto_id=rango_documento.punto_id and punto_emision.sucursal_id=sucursal.sucursal_id and sucursal.empresa_id=".Auth::user()->empresa_id." and lc_fecha>='".$suscripcion->suscripcion_fecha_inicio."'
        )T"));
        foreach($saldos as $as2){
            if($as2->sum > $suscripcion->plan->plan_cantidad_documentos)
            return(true);
        }
        return false;
    }
    public function denegado()
    {
        try{
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            return view('errors.denegado',['PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        }
        catch(\Exception $ex){      
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function cierre($fecha,$sucursal){
        try{
            $fechaEntera = strtotime($fecha);
            $anio = date("Y", $fechaEntera);    
            $mes=date("m", $fechaEntera);    
            $cierre=Cierre_Mes_Contable::Cierre($anio,$sucursal)->get()->first();
            $cierre3='cierre_'.$mes;
            if ($cierre) {
                if ($cierre->$cierre3 =='1') {
                    return true;
                } else {
                    return false;
                }
            }
            else{
                return false;
            }
        }
        catch(\Exception $ex){      
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function registrarAuditoria($descripcion, $numeroDocumento, $adicional, $documentoB64=null){
        try{
            DB::beginTransaction();
            if(Auth::user()->user_username != 'SuperAdministrador'){
                $auditoria = new Auditoria();
                $auditoria->auditoria_fecha=date("Y")."-".date("m")."-".date("d");
                $auditoria->auditoria_hora=date("H:i:s");
                $auditoria->auditoria_maquina=gethostname();
                $auditoria->auditoria_adicional=$adicional;
                $auditoria->auditoria_descripcion=$descripcion;
                $auditoria->auditoria_numero_documento=$numeroDocumento;
                $auditoria->auditoria_estado='1';
                $auditoria->user_id=Auth::user()->user_id;
                if($documentoB64!=null)$auditoria->auditoria_documento_respaldo=$documentoB64;
                $auditoria->save();
            }
            DB::commit();
        }
        catch(\Exception $ex){    
            DB::rollBack();  
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function generarCodigoDiario($fecha, $tipo){
        $mes  = DateTime::createFromFormat('Y-m-d', $fecha)->format('m');
        $ano  = DateTime::createFromFormat('Y-m-d', $fecha)->format('y');
        $secuencialDiario = Diario::DiarioSecuencial($tipo,$mes,DateTime::createFromFormat('Y-m-d', $fecha)->format('Y'))->max('diario_secuencial');
        $sec = 1;
        if($secuencialDiario){
            $sec = $secuencialDiario +1;
        }
        $codigoDiario = $tipo.$mes.$ano.substr(str_repeat(0, 7).$sec, - 7);
        return $codigoDiario;
    }
    public function pdfDiario(Diario $diario){
        $empresa = Empresa::empresa()->first();
        $diario=Diario::findOrFail($diario->diario_id);
        $ruta = public_path().'/DIARIOS/'.$empresa->empresa_ruc.'/'.DateTime::createFromFormat('Y-m-d', $diario->diario_fecha)->format('d-m-Y');
        if (!is_dir($ruta)) {
            mkdir($ruta, 0777, true);
        }
        $nombreArchivo = $diario->diario_codigo. ".pdf";
        $view =  \View::make('admin.formatosPDF.diario', ['empresa'=> $empresa,'diario'=> $diario]);
        PDF::loadHTML($view)->save($ruta.'/'.$nombreArchivo)->download($nombreArchivo);
        return 'DIARIOS/'.$empresa->empresa_ruc.'/'.DateTime::createFromFormat('Y-m-d', $diario->diario_fecha)->format('d-m-Y').'/'.$nombreArchivo;
    }
    public function pdfProforma(Proforma $profor){
        $empresa = Empresa::empresa()->first();
        $ruta = public_path().'/PROFORMA/'.$empresa->empresa_ruc.'/'.DateTime::createFromFormat('Y-m-d', $profor->proforma_fecha)->format('d-m-Y');
        if (!is_dir($ruta)) {
            mkdir($ruta, 0777, true);
        }
        $nombreArchivo = $profor->proforma_numero. ".pdf";
        $view =  \View::make('admin.formatosPDF.pdfproforma', ['prof'=>$profor,'empresa'=>$empresa]);
        PDF::loadHTML($view)->save($ruta.'/'.$nombreArchivo)->download($nombreArchivo);
        return 'PROFORMA/'.$empresa->empresa_ruc.'/'.DateTime::createFromFormat('Y-m-d', $profor->proforma_fecha)->format('d-m-Y').'/'.$nombreArchivo;
    }
    public function pdfComprobanteEgresoBodega(Egreso_Bodega $egreso){
        $empresa = Empresa::empresa()->first();
        $diario=Diario::findOrFail($egreso->diario_id);
        $ruta = public_path().'/DIARIOS/'.$empresa->empresa_ruc.'/'.DateTime::createFromFormat('Y-m-d', $diario->diario_fecha)->format('d-m-Y');
        if (!is_dir($ruta)) {
            mkdir($ruta, 0777, true);
        }
        $nombreArchivo = $diario->diario_codigo. "_comprobante_egreso.pdf";
        $view =  \View::make('admin.formatosPDF.comprobanteEgresoBodega', ['empresa'=> $empresa,'egreso'=> $egreso]);
        PDF::loadHTML($view)->save($ruta.'/'.$nombreArchivo)->download($nombreArchivo);
        return 'DIARIOS/'.$empresa->empresa_ruc.'/'.DateTime::createFromFormat('Y-m-d', $diario->diario_fecha)->format('d-m-Y').'/'.$nombreArchivo;
    }
    public function pdfComprobanteIngresoBodega(Ingreso_Bodega $ingreso){
        $empresa = Empresa::empresa()->first();
        $diario=Diario::findOrFail($ingreso->diario_id);
        $ruta = public_path().'/DIARIOS/'.$empresa->empresa_ruc.'/'.DateTime::createFromFormat('Y-m-d', $diario->diario_fecha)->format('d-m-Y');
        if (!is_dir($ruta)) {
            mkdir($ruta, 0777, true);
        }
        $nombreArchivo = $diario->diario_codigo. "_comprobante_ingreso.pdf";
        $view =  \View::make('admin.formatosPDF.comprobanteIngresoBodega', ['empresa'=> $empresa,'ingreso'=> $ingreso]);
        PDF::loadHTML($view)->save($ruta.'/'.$nombreArchivo)->download($nombreArchivo);
        return 'DIARIOS/'.$empresa->empresa_ruc.'/'.DateTime::createFromFormat('Y-m-d', $diario->diario_fecha)->format('d-m-Y').'/'.$nombreArchivo;
    }
    public function anulados($id){
        $documento=Documento_Anulado::findOrFail($id);
        $empresa = Empresa::empresa()->first();
        $codigosecue='';
        $codigoini='';
        $tipo='';
        $fecha=date('d-m-Y');
        if($documento->facturaVenta){
            $tipo='FAC-';
            $codigoini=$documento->facturaVenta->factura_serie;
            $codigosecue=$documento->facturaVenta->factura_secuencial;
           
            $codigosecue=substr(str_repeat(0, 9).$documento->facturaVenta->factura_secuencial, - 9);
            $fecha=$documento->facturaVenta->factura_fecha;

        }
        if($documento->notaCredito){
            $tipo='NC-';
            $codigoini=$documento->notaCredito->nc_serie;
            $codigosecue=$documento->notaCredito->nc_secuencial;
            $codigosecue=substr(str_repeat(0, 9).$documento->notaCredito->nc_secuencial, - 9);
            $fecha=$documento->notaCredito->nc_fecha;

        }
        if($documento->notaDebito){
            $tipo='ND-';
            $codigoini=$documento->notaDebito->nd_serie;
            $codigosecue=$documento->notaDebito->nd_secuencial;
            $codigosecue=substr(str_repeat(0, 9).$documento->notaDebito->nd_secuencial, - 9);
            $fecha=$documento->notaDebito->nd_fecha;

        }
        if($documento->retencion){
            $tipo='RET-';
            $codigoini=$documento->retencion->retencion_serie;
            $codigosecue=$documento->retencion->retencion_secuencial;
            $codigosecue=substr(str_repeat(0, 9).$documento->retencion->retencion_secuencial, - 9);
            $fecha=$documento->retencion->retencion_fecha;

        }
        if($documento->liquidacion){
            $tipo='LC-';
            $codigoini=$documento->liquidacion->lc_serie;
            $codigosecue=$documento->liquidacion->lc_secuencial;
            $codigosecue=substr(str_repeat(0, 9).$documento->liquidacion->lc_secuencial, - 9);
            $fecha=$documento->liquidacion->lc_fecha;

        }
        $codigopunto=substr($codigoini, -3);     
        $codigoini=substr($codigoini, 0, -3);
        $nombreArchivo = $tipo.$codigoini.'-'.$codigopunto.'-'.$codigosecue.".pdf";
        return redirect('documentosElectronicos/'.$empresa->empresa_ruc.'/'.DateTime::createFromFormat('Y-m-d', $fecha)->format('d-m-Y').'/'.$nombreArchivo);
    }
    public function pdfVariosDiario($diarios, $fecha){
        $empresa = Empresa::empresa()->first();
        $nombre = '';
        for ($i = 0; $i < count($diarios); ++$i){
            $diario=Diario::findOrFail($diarios[$i]->diario_id);
            $nombre = $nombre.' - '.$diario->diario_codigo;
        }
        $ruta = public_path().'/DIARIOS/'.$empresa->empresa_ruc.'/'.DateTime::createFromFormat('Y-m-d', $fecha)->format('d-m-Y');
        if (!is_dir($ruta)) {
            mkdir($ruta, 0777, true);
        }
        $nombreArchivo = $nombre. ".pdf";
        $view =  \View::make('admin.formatosPDF.variosDiarios', ['empresa'=> $empresa,'diarios'=> $diarios]);
        PDF::loadHTML($view)->save($ruta.'/'.$nombreArchivo)->download($nombreArchivo);
        return 'DIARIOS/'.$empresa->empresa_ruc.'/'.DateTime::createFromFormat('Y-m-d', $fecha)->format('d-m-Y').'/'.$nombreArchivo;
    }
    public function pdfDiarioEgreso(Diario $diario){
        $empresa = Empresa::empresa()->first();
        $diario=Diario::findOrFail($diario->diario_id);
        $empleado=null;
        foreach($diario->detalles as $detalle){
            if(isset($detalle->empleado_id)){
                $empleado=Empleado::findOrFail($detalle->empleado_id);
            } 
        }
        $ruta = public_path().'/DIARIOS/'.$empresa->empresa_ruc.'/'.DateTime::createFromFormat('Y-m-d', $diario->diario_fecha)->format('d-m-Y');
        if (!is_dir($ruta)) {
            mkdir($ruta, 0777, true);
        }
        $nombreArchivo = $diario->diario_codigo. ".pdf";
        $view =  \View::make('admin.formatosPDF.diarioEgreso', ['empresa'=> $empresa,'diario'=> $diario,'empleado'=> $empleado]);
        PDF::loadHTML($view)->save($ruta.'/'.$nombreArchivo)->download($nombreArchivo);
        return 'DIARIOS/'.$empresa->empresa_ruc.'/'.DateTime::createFromFormat('Y-m-d', $diario->diario_fecha)->format('d-m-Y').'/'.$nombreArchivo;
    }
    
    public function pdfDiarioPago(Diario $diario){
        $empresa = Empresa::empresa()->first();
        $diario=Diario::findOrFail($diario->diario_id);
        $ruta = public_path().'/DIARIOS/'.$empresa->empresa_ruc.'/'.DateTime::createFromFormat('Y-m-d', $diario->diario_fecha)->format('d-m-Y');
        if (!is_dir($ruta)) {
            mkdir($ruta, 0777, true);
        }
        $nombreArchivo = $diario->diario_codigo. ".pdf";
        $view =  \View::make('admin.formatosPDF.diarioPago', ['empresa'=> $empresa,'diario'=> $diario]);
        PDF::loadHTML($view)->save($ruta.'/'.$nombreArchivo)->download($nombreArchivo);
        return 'DIARIOS/'.$empresa->empresa_ruc.'/'.DateTime::createFromFormat('Y-m-d', $diario->diario_fecha)->format('d-m-Y').'/'.$nombreArchivo;
    }
    public function pdfImprimeCheque($idCuenta, Cheque $cheque){
        $formatter = new NumeroALetras();
        $newValorLetras = $formatter->toInvoice($cheque->cheque_valor, 2, 'Dolares');
        $cheque->cheque_valor_letras = $newValorLetras;
        $cheque->update();
        $empresa = Empresa::empresa()->first();
        $cuentaBancaria = Cuenta_Bancaria::cuentaBancaria($idCuenta)->first();
        $chequeImpresion = Cheque_Impresion::chequeImpresionByUser($idCuenta)->first();

        if(!$chequeImpresion)
            $chequeImpresion = Cheque_Impresion::chequeImpresion($idCuenta)->first();

        if(!$chequeImpresion)
            return ": ( <br><br><br>Usted no ha configurado la visualización, realice esta acción en configurar Cheque  <a href='".url('cuentaBancaria')."'>Ir a Configurar</a>, &nbsp &nbsp <a onclick='window.history.back'>Regresar</a>";

        //return $chequeImpresion;


        $ruta = public_path().'/chequesImpresosPDF/'.$empresa->empresa_ruc.'/'.DateTime::createFromFormat('Y-m-d', $cheque->cheque_fecha_emision)->format('d-m-Y');
        echo "$ruta";
        if (!is_dir($ruta)) {
            mkdir($ruta, 0777, true);
        }
        $nombreArchivo = $cuentaBancaria->cuenta_bancaria_numero.'_'.$cheque->cheque_numero. ".pdf";
        $view =  \View::make('admin.formatosPDF.chequeImpresionPdf', ['cheque'=>$cheque,'chequeImpresion'=>$chequeImpresion,'empresa'=>$empresa]);
        PDF::loadHTML($view)->save($ruta.'/'.$nombreArchivo)->download($nombreArchivo);
        return 'chequesImpresosPDF/'.$empresa->empresa_ruc.'/'.DateTime::createFromFormat('Y-m-d', $cheque->cheque_fecha_emision)->format('d-m-Y').'/'.$nombreArchivo;
  
    }
    public function pdfImprimeCheque2($idCuenta, Cheque $cheque){
        $formatter = new NumeroALetras();
        $newValorLetras = $formatter->toInvoice($cheque->cheque_valor, 2, 'Dolares');
        $cheque->cheque_valor_letras = $newValorLetras;
        $cheque->update();
        $empresa = Empresa::empresa()->first();
        $cuentaBancaria = Cuenta_Bancaria::cuentaBancaria($idCuenta)->first();
        $chequeImpresion = Cheque_Impresion::chequeImpresionByUser($idCuenta)->first();

        if(!$chequeImpresion)
            $chequeImpresion = Cheque_Impresion::chequeImpresion($idCuenta)->first();

        if(!$chequeImpresion)
            return ": ( <br><br><br>Usted no ha configurado la visualización, realice esta acción en configurar Cheque  <a href='".url('cuentaBancaria')."'>Regresar</a>";

        //echo Auth::user()->user_id.'<br>';
        //return $chequeImpresion;

        $ruta = public_path().'/chequesImpresosPDF/'.$empresa->empresa_ruc.'/'.DateTime::createFromFormat('Y-m-d', $cheque->cheque_fecha_emision)->format('d-m-Y');
        echo "$ruta";
        if (!is_dir($ruta)) {
            mkdir($ruta, 0777, true);
        }
        $nombreArchivo = $cuentaBancaria->cuenta_bancaria_numero.'_'.$cheque->cheque_numero. ".pdf";
        $view =  \View::make('admin.formatosPDF.chequeImpresionPdf', ['cheque'=>$cheque,'chequeImpresion'=>$chequeImpresion,'empresa'=>$empresa]);
        return PDF::loadHTML($view)->save($ruta.'/'.$nombreArchivo)->stream($nombreArchivo);
    }
    public function NotaEntrega(Nota_Entrega $nota,$url){
        $nota=Nota_Entrega::findOrFail($nota->nt_id);
        $empresa = Empresa::empresa()->first();
        $ruta = public_path().'/notasEntrega/'.$empresa->empresa_ruc.'/'.DateTime::createFromFormat('Y-m-d', $nota->nt_fecha)->format('d-m-Y');
        if (!is_dir($ruta)) {
            mkdir($ruta, 0777, true);
        }
        $nombreArchivo = 'NT-'.$nota->nt_numero. ".pdf";
        $view =  \View::make('admin.formatosPDF.notaentrega', ['nt'=>$nota,'empresa'=>$empresa]);
        if($url == 0){
            return PDF::loadHTML($view)->save($ruta.'/'.$nombreArchivo)->stream('notaEntrega.pdf');
        }else{
            PDF::loadHTML($view)->save($ruta.'/'.$nombreArchivo)->download($nombreArchivo);
            return 'notasEntrega/'.$empresa->empresa_ruc.'/'.DateTime::createFromFormat('Y-m-d', $nota->nt_fecha)->format('d-m-Y').'/'.$nombreArchivo;
        }
    }
   
    public function NotaEntregaRecibo(Nota_Entrega $nota,$url){
        $empresa = Empresa::empresa()->first();
        $ruta = public_path().'/notasEntrega/'.$empresa->empresa_ruc.'/'.DateTime::createFromFormat('Y-m-d', $nota->nt_fecha)->format('d-m-Y');
        if (!is_dir($ruta)) {
            mkdir($ruta, 0777, true);
        }
        $nombreArchivo = 'NT-'.$nota->nt_numero. ".pdf";
        $view =  \View::make('admin.formatosPDF.reciboNotaEntrega', ['nt'=>$nota,'empresa'=>$empresa]);
        if($url == 0){
            return PDF::loadHTML($view)->setPaper(array(0,0,249.45,300.33 + (count($nota->detalle) * 15)), 'portrait')->save($ruta.'/'.$nombreArchivo)->stream('recibo.pdf');
        }else{
            PDF::loadHTML($view)->setPaper(array(0,0,249.45,300.33 + (count($nota->detalle) * 15)), 'portrait')->save($ruta.'/'.$nombreArchivo)->download($nombreArchivo);
            return 'notasEntrega/'.$empresa->empresa_ruc.'/'.DateTime::createFromFormat('Y-m-d', $nota->nt_fecha)->format('d-m-Y').'/'.$nombreArchivo;
        }
    }
    public function FacturaRecibo(Factura_Venta $factura,$url){
        $empresa = Empresa::empresa()->first();
        $ruta = public_path().'/documentos/reciboFactura/'.$empresa->empresa_ruc.'/'.DateTime::createFromFormat('Y-m-d', $factura->factura_fecha)->format('d-m-Y');
        if (!is_dir($ruta)) {
            mkdir($ruta, 0777, true);
        }
        $nombreArchivo = 'FAC-'.$factura->factura_numero. ".pdf";
        $view =  \View::make('admin.formatosPDF.reciboFactura', ['factura'=>$factura,'empresa'=>$empresa]);
        if($url == 0){
            return PDF::loadHTML($view)->setPaper(array(0,0,249.45,450.33 + (count($factura->detalles) * 15)), 'portrait')->save($ruta.'/'.$nombreArchivo)->stream('recibo.pdf');
        }else{
            PDF::loadHTML($view)->setPaper(array(0,0,249.45,450.33 + (count($factura->detalles) * 15)), 'portrait')->save($ruta.'/'.$nombreArchivo)->download($nombreArchivo);
            return 'documentos/reciboFactura/'.$empresa->empresa_ruc.'/'.DateTime::createFromFormat('Y-m-d', $factura->factura_fecha)->format('d-m-Y').'/'.$nombreArchivo;
        }
    }
    public function pdfDiariourl(Diario $diario){
        $empresa = Empresa::empresa()->first();
        $ruta = public_path().'/DIARIOS/'.$empresa->empresa_ruc.'/'.DateTime::createFromFormat('Y-m-d', $diario->diario_fecha)->format('d-m-Y');
        if (!is_dir($ruta)) {
            mkdir($ruta, 0777, true);
        }
        $nombreArchivo = $diario->diario_codigo. ".pdf";
        $view =  \View::make('admin.formatosPDF.diario', ['empresa'=> $empresa,'diario'=> $diario]);
        PDF::loadHTML($view)->save($ruta.'/'.$nombreArchivo)->download($nombreArchivo.'.pdf');
        return PDF::loadHTML($view)->save($ruta.'/'.$nombreArchivo)->stream('diario.pdf');
    }
    public function pdfDiariourl2(Diario $diario){
        $empresa = Empresa::empresa()->first();
        $ruta = public_path().'/DIARIOS/'.$empresa->empresa_ruc.'/'.DateTime::createFromFormat('Y-m-d', $diario->diario_fecha)->format('d-m-Y');
        if (!is_dir($ruta)) {
            mkdir($ruta, 0777, true);
        }
        $nombreArchivo = $diario->diario_codigo. ".pdf";
        $view =  \View::make('admin.formatosPDF.diariodecimo', ['empresa'=> $empresa,'diario'=> $diario]);
        PDF::loadHTML($view)->save($ruta.'/'.$nombreArchivo)->download($nombreArchivo.'.pdf');
        return PDF::loadHTML($view)->save($ruta.'/'.$nombreArchivo)->stream('diario.pdf');
    }
    public function pdfPrefactura(Prefactura_Venta $prefactura){
        
        $prefactura = Prefactura_Venta::findOrFail($prefactura->prefactura_id);
        $empresa =  Empresa::empresa()->first();
        $saco=0;
        $i=1;
        $movi=null;
        $datos=Prefactura_Venta::totales($prefactura->prefactura_id)->groupBy('producto.producto_id')->selectRaw('sum(detalle_cantidad) as sum, producto_nombre, producto.producto_id')->get();
        foreach($prefactura->detalles as $detalle){
            $movimientostotal=Movimiento_Prestamo_Producto::totalproducto($prefactura->cliente_id,$detalle->producto_id,$prefactura->prefactura_fecha)->where('movimiento_tipo','=','ENTRADA')->sum('movimiento_valor')-Movimiento_Prestamo_Producto::totalproducto($prefactura->cliente_id,$detalle->producto_id,$prefactura->prefactura_fecha)->where('movimiento_tipo','=','SALIDA')->sum('movimiento_valor');
            if($movimientostotal != 0){
                if($movi==null){
                    $movi[$i]['total']=$movimientostotal;
                    $movi[$i]['descripcion']=$detalle->producto->producto_nombre;
                    $i++;
                   
                }
                else{
                    for ($j = 1; $j <= count($movi); ++$j)  {
                        if($movi[$j]['descripcion']!=$detalle->producto->producto_nombre){
                            $movi[$i]['total']=$movimientostotal;
                            $movi[$i]['descripcion']=$detalle->producto->producto_nombre;
                            $i++;
                        }
                    }  
                }
               
                   
            }

        
        $guia=Guia_Remision::findOrFail($detalle->gr_id);
        
        foreach($guia->ordenes as $orden){

            foreach($orden->detalles as $detalleo){
                
                if($detalleo->movimientop){
                    $saco=$saco+$detalleo->movimientop->movimiento_valor;
                }
            }
        }
    }
        

       
       
        $ruta = public_path().'/prefacturacion/'.$empresa->empresa_ruc.'/'.DateTime::createFromFormat('Y-m-d', $prefactura->prefactura_fecha)->format('d-m-Y');
        if (!is_dir($ruta)) {
            mkdir($ruta, 0777, true);
        }
        $nombreArchivo = 'PFV-'.$prefactura->prefactura_numero.".pdf";
        if($prefactura->prefactura_tipo =='MES Y AÑO'){
            $view =  \View::make('admin.formatosPDF.prefacutracionmuelle', ['datos'=> $datos,'empresa'=> $empresa,'prefactura'=> $prefactura]);
        }else{
            $view =  \View::make('admin.formatosPDF.prefacturacion', ['movi'=> $movi,'saco'=> $saco,'empresa'=> $empresa,'prefactura'=> $prefactura]);
        }
        PDF::loadHTML($view)->save($ruta.'/'.$nombreArchivo)->download($nombreArchivo);
        return '/prefacturacion/'.$empresa->empresa_ruc.'/'.DateTime::createFromFormat('Y-m-d', $prefactura->prefactura_fecha)->format('d-m-Y').'/'.$nombreArchivo;
    }
    public function pdfDiarioEgresourl(Diario $diario){
        $empresa = Empresa::empresa()->first();
        $diario=Diario::findOrFail($diario->diario_id);
        $empleado=null;
        foreach($diario->detalles as $detalle){
            if(isset($detalle->empleado_id)){
                $empleado=Empleado::findOrFail($detalle->empleado_id);
            } 
        }
        $ruta = public_path().'/DIARIOS/'.$empresa->empresa_ruc.'/'.DateTime::createFromFormat('Y-m-d', $diario->diario_fecha)->format('d-m-Y');
        if (!is_dir($ruta)) {
            mkdir($ruta, 0777, true);
        }
        $nombreArchivo = $diario->diario_codigo. ".pdf";
        $view =  \View::make('admin.formatosPDF.diarioEgreso', ['empresa'=> $empresa,'diario'=> $diario,'empleado'=> $empleado]);
        PDF::loadHTML($view)->save($ruta.'/'.$nombreArchivo)->download($nombreArchivo.'.pdf');
        return PDF::loadHTML($view)->save($ruta.'/'.$nombreArchivo)->stream('diario.pdf');
    }
    public function pdfRolDetalle(Rol_Consolidado $rol){
        $empresa = Empresa::empresa()->first();
        $ruta = public_path().'/roles/'.$empresa->empresa_ruc.'/'.date("m-Y", strtotime($rol->cabecera_rol_fecha));
        echo "$ruta";
        if (!is_dir($ruta)) {
            mkdir($ruta, 0777, true);
        }
        $nombreArchivo = $rol->empleado->empleado_nombre.'-'.date("m-Y", strtotime($rol->cabecera_rol_fecha)). ".pdf";
        setlocale(LC_ALL, 'spanish');
        $mes=strftime('%B',strtotime($rol->cabecera_rol_fecha));
        $view =  \View::make('admin.formatosPDF.rolindividual', ['empresa'=> $empresa,'rol'=> $rol,'mes'=> $mes]);
        PDF::loadHTML($view)->save($ruta.'/'.$nombreArchivo)->download($nombreArchivo.'.pdf');
        return 'roles/'.$empresa->empresa_ruc.'/'.date("m-Y", strtotime($rol->cabecera_rol_fecha)).'/'.$nombreArchivo;

    }
    public function pdfRolCm(Cabecera_Rol_CM $rol){
        $cuenta=null;
        $tipo=null;
        $diario=Diario::findOrFail($rol->diario_pago_id);
        $tipo='OTROS';
        foreach($diario->detalles as $detalle){
            if(isset($detalle->cheque)){
                $cuenta=Cuenta_Bancaria::findOrFail($detalle->cheque->cuenta_bancaria_id);
                $tipo='CHEQUE';
            }
            if(isset($detalle->transferencia)){
                $cuenta=Cuenta_Bancaria::findOrFail($detalle->transferencia->cuenta_bancaria_id);
                $tipo='TRANSFERENCIA BANCARIA';
            }
            if(isset($diario->movimientocaja)){
                $tipo='EFECTIVO';
            }

        }
        $empresa = Empresa::empresa()->first();
        $rubro=Rubro::RubrosRH()->get();
        $ingresos=Rubro::Rubrotipoorder('2')->get();
        $egresos=Rubro::Rubrotipoorder('1')->get();
        $ruta = public_path().'/roles/'.$empresa->empresa_ruc.'/'.date("m-Y", strtotime($rol->cabecera_rol_fecha));
        echo "$ruta";
        if (!is_dir($ruta)) {
            mkdir($ruta, 0777, true);
        }
        $nombreArchivo = $rol->empleado->empleado_nombre.'-'.date("m-Y", strtotime($rol->cabecera_rol_fecha)). ".pdf";
        setlocale(LC_ALL, 'spanish');
        $mes=strftime('%B',strtotime($rol->cabecera_rol_fecha));
        $view =  \View::make('admin.formatosPDF.rolesCM.rolOperativo', ['empresa'=> $empresa,'rol'=> $rol,'mes'=> $mes,'tipo'=> $tipo,'bancaria'=> $cuenta,'egresos'=>$egresos,'ingresos'=>$ingresos,'rubros'=>$rubro]);
        PDF::loadHTML($view)->save($ruta.'/'.$nombreArchivo)->download($nombreArchivo);
        return 'roles/'.$empresa->empresa_ruc.'/'.date("m-Y", strtotime($rol->cabecera_rol_fecha)).'/'.$nombreArchivo;

    }
    public function imprimirRolCm(Cabecera_Rol_CM $rol){
        $cuenta=null;
        $tipo=null;
        $diario=Diario::findOrFail($rol->diario_pago_id);
        $tipo='OTROS';
        foreach($diario->detalles as $detalle){
            if(isset($detalle->cheque)){
                $cuenta=Cuenta_Bancaria::findOrFail($detalle->cheque->cuenta_bancaria_id);
                $tipo='CHEQUE';
            }
            if(isset($detalle->transferencia)){
                $cuenta=Cuenta_Bancaria::findOrFail($detalle->transferencia->cuenta_bancaria_id);
                $tipo='TRANSFERENCIA BANCARIA';
            }
            if(isset($diario->movimientocaja)){
                $tipo='EFECTIVO';
            }

        }

        $empresa = Empresa::empresa()->first();
        $rubro=Rubro::RubrosRH()->get();
        $ingresos=Rubro::Rubrotipoorder('2')->get();
        $egresos=Rubro::Rubrotipoorder('1')->get();
        $ruta = public_path().'/roles/'.$empresa->empresa_ruc.'/'.date("m-Y", strtotime($rol->cabecera_rol_fecha));
        echo "$ruta";
        if (!is_dir($ruta)) {
            mkdir($ruta, 0777, true);
        }
        $nombreArchivo = $rol->empleado->empleado_nombre.'-'.date("m-Y", strtotime($rol->cabecera_rol_fecha)). ".pdf";
        setlocale(LC_ALL, 'spanish');
        $mes=strftime('%B',strtotime($rol->cabecera_rol_fecha));
        $view =  \View::make('admin.formatosPDF.rolesCM.rolOperativo', ['empresa'=> $empresa,'rol'=> $rol,'mes'=> $mes,'tipo'=> $tipo,'bancaria'=> $cuenta,'egresos'=>$egresos,'ingresos'=>$ingresos,'rubros'=>$rubro]);
        PDF::loadHTML($view)->save($ruta.'/'.$nombreArchivo)->download($nombreArchivo.'.pdf');
        return PDF::loadHTML($view)->save($ruta.'/'.$nombreArchivo)->stream('rol.pdf');

    }
    public function pdfRolOperativo(Rol_Consolidado $rol,Cuenta_Bancaria $cuenta){
        $empresa = Empresa::empresa()->first();

        $ruta = public_path().'/roles/'.$empresa->empresa_ruc.'/'.date("m-Y", strtotime($rol->cabecera_rol_fecha));
        echo "$ruta";
        if (!is_dir($ruta)) {
            mkdir($ruta, 0777, true);
        }
        $nombreArchivo = $rol->empleado->empleado_nombre.'-'.date("m-Y", strtotime($rol->cabecera_rol_fecha)). ".pdf";
        setlocale(LC_ALL, 'spanish');
        $mes=strftime('%B',strtotime($rol->cabecera_rol_fecha));
        $view =  \View::make('admin.formatosPDF.roles.rolOperativo', ['empresa'=> $empresa,'rol'=> $rol,'bancaria'=> $cuenta,'mes'=> $mes]);
        PDF::loadHTML($view)->save($ruta.'/'.$nombreArchivo)->download($nombreArchivo.'.pdf');
        return 'roles/'.$empresa->empresa_ruc.'/'.date("m-Y", strtotime($rol->cabecera_rol_fecha)).'/'.$nombreArchivo;

    }
    public function pdfRol(Rol_Consolidado $rol){
        $empresa = Empresa::empresa()->first();
        $ruta = public_path().'/decimoCuarto/'.$empresa->empresa_ruc.'/'.date("m-Y", strtotime($rol->cabecera_rol_fecha));
        echo "$ruta";
        if (!is_dir($ruta)) {
            mkdir($ruta, 0777, true);
        }
        $nombreArchivo = $rol->empleado->empleado_nombre.'-'.date("m-Y", strtotime($rol->cabecera_rol_fecha)). ".pdf";
        setlocale(LC_ALL, 'spanish');
        $mes=strftime('%B',strtotime($rol->cabecera_rol_fecha));
        
        $view =  \View::make('admin.formatosPDF.rolindividual', ['empresa'=> $empresa,'rol'=> $rol,'mes'=> $mes]);
        PDF::loadHTML($view)->save($ruta.'/'.$nombreArchivo)->download($nombreArchivo.'.pdf');
        return PDF::loadHTML($view)->save($ruta.'/'.$nombreArchivo)->stream('rol.pdf');
    }

    public function pdfTercero(Decimo_Tercero $tercero){
        $empresa = Empresa::empresa()->first();
        $ruta = public_path().'/decimoTercero/'.$empresa->empresa_ruc.'/'.date("m-Y", strtotime($tercero->decimo_fecha));
        echo "$ruta";
        if (!is_dir($ruta)) {
            mkdir($ruta, 0777, true);
        }
        $nombreArchivo = $tercero->empleado->empleado_nombre.'-'.date("m-Y",strtotime($tercero->decimo_fecha)).".pdf";
        setlocale(LC_ALL, 'spanish');
        $mes=strftime('%B',strtotime($tercero->decimo_fecha));
        $fecha = strftime("%d de %B de %Y", strtotime($tercero->decimo_fecha));
        $view =  \View::make('admin.formatosPDF.tercero', ['empresa'=> $empresa,'tercero'=> $tercero,'fecha'=> $fecha,'mes'=> $mes]);
        PDF::loadHTML($view)->save($ruta.'/'.$nombreArchivo)->download($nombreArchivo.'.pdf');
        return PDF::loadHTML($view)->save($ruta.'/'.$nombreArchivo)->stream('tercero.pdf');
    }
    public function LaboratorioAnalisis(Orden_Examen $orden){
        $empresa = Empresa::empresa()->first();
        $ruta = public_path().'/ordenesExamenes/Analisis/'.$empresa->empresa_ruc.'/'.DateTime::createFromFormat('Y-m-d', $orden->expediente->ordenatencion->orden_fecha)->format('d-m-Y');
        echo "$ruta";
        if (!is_dir($ruta)) {
            mkdir($ruta, 0777, true);
        }
        $nombreArchivo = 'OD-'.$orden->expediente->ordenatencion->orden_numero;
        $view =  \View::make('admin.formatosPDF.ordendeexamen', ['orden'=>$orden,'empresa'=>$empresa]);
        PDF::loadHTML($view)->save($ruta.'/'.$nombreArchivo.'.pdf')->download($nombreArchivo.'.pdf');
        return PDF::loadHTML($view)->save($ruta.'/'.$nombreArchivo.'.pdf')->stream('analisis.pdf');
}
    public function ordendespacho(Orden_Despacho $orden){
            $empresa = Empresa::empresa()->first();
            $ruta = public_path().'/ordenDespacho/'.$empresa->empresa_ruc.'/'.DateTime::createFromFormat('Y-m-d', $orden->orden_fecha)->format('d-m-Y');
            echo "$ruta";
            if (!is_dir($ruta)) {
                mkdir($ruta, 0777, true);
            }
            $nombreArchivo = 'OD-'.$orden->orden_numero;
            $view =  \View::make('admin.formatosPDF.ordenDespacho', ['orden'=>$orden,'empresa'=>$empresa]);
            PDF::loadHTML($view)->save($ruta.'/'.$nombreArchivo)->download($nombreArchivo.'.pdf');
            return PDF::loadHTML($view)->save($ruta.'/'.$nombreArchivo)->stream('ordenDespacho.pdf');
    }
    
    public function pdfCuarto(Decimo_Cuarto $Cuarto){
        $empresa = Empresa::empresa()->first();
        $ruta = public_path().'/decimoCuarto/'.$empresa->empresa_ruc.'/'.date("m-Y", strtotime($Cuarto->decimo_fecha));

        echo "$ruta";
        if (!is_dir($ruta)) {
            mkdir($ruta, 0777, true);
        }
        $nombreArchivo = $Cuarto->empleado->empleado_nombre.'-'.date("m-Y", strtotime($Cuarto->decimo_fecha)). ".pdf";
        setlocale(LC_ALL, 'spanish');
        $mes=strftime('%B',strtotime($Cuarto->decimo_fecha_emision));
        $fecha = strftime("%d de %B de %Y", strtotime($Cuarto->decimo_fecha));
        $view =  \View::make('admin.formatosPDF.cuarto', ['empresa'=> $empresa,'cuarto'=> $Cuarto,'fecha'=> $fecha,'mes'=> $mes]);
        PDF::loadHTML($view)->save($ruta.'/'.$nombreArchivo)->download($nombreArchivo.'.pdf');
        return PDF::loadHTML($view)->save($ruta.'/'.$nombreArchivo)->stream('cuarto.pdf');
    }

    public function preciocosto($fechaInicio,$fechaFin,$producto_id){
        if($fechaInicio == ''){
            $fechaInicio = date("Y-m-d",strtotime($fechaFin."- 60 days"));
        }
        $movimientoAnterior = Movimiento_Producto::MovProductoByFechaCorte($producto_id,date("Y-m-d",strtotime($fechaInicio."- 1 days")))->orderBy('movimiento_fecha','desc')->orderBy('movimiento_id','desc')->first();
        $precioCosto=0;
        foreach(Movimiento_Producto::MovProductoByFecha($producto_id,$fechaInicio,$fechaFin)->get() as $movimiento){
            if($movimiento->movimiento_motivo == 'COMPRA'){
                if($movimientoAnterior){
                    if($movimiento->movimiento_stock_actual != 0){
                        $precioCosto = abs((($movimientoAnterior->movimiento_cantidad*$movimientoAnterior->movimiento_costo_promedio) + $movimiento->movimiento_total)/$movimiento->movimiento_stock_actual);
                    }
                }
            }
            $movimientoAnterior = $movimiento;
        }
        return $precioCosto;
    }

    public function validateUnbalancedJournal(Diario $diario){
        $totalDebe = 0;
        $totalHaber = 0;

        foreach ($diario->detalles as $detalle) {
            $totalDebe = floatval($totalDebe) + floatval($detalle->detalle_debe);
            $totalHaber =  floatval($totalHaber) + floatval($detalle->detalle_haber);
        }
        
        return round($totalDebe,2) == round($totalHaber,2) ? true : false;
    }

    public function enviarCorreo($correo, $nombre, $asunto, $html, $plain, $adjuntos=[]){
        try {
            date_default_timezone_set('Etc/UTC');

            //estilo del mensaje
            $cuerpo  = "<html><head>
                            <style>
                                a{
                                    text-decoration: none;
                                    color: white;
                                    letter-spacing: 2px;
                                }
                                .letra {
                                    padding: 10px 10px;
                                    background: #FDA30E;
                                    font-weight: 600;
                                    margin: 2px;
                                }
                                .fondo{
                                    background: linear-gradient(352deg, rgba(241,222,44,1) 0%, rgba(250,250,250,1) 100%) !important;
                                    padding: 10px;
                                }
                            </style>
                        </head>";
            $cuerpo .= "<body style='text-align: center'>";
            $cuerpo .= "<table style='min-width: 1000px>";
            $cuerpo .= "<tr style='text-align: center'>
                            <td width: '20%'>
                            <img src='cid:logo'  width='100px'>
                            </td>
                            <td width: '80%'>
                                <strong>NEOPAGUPA - TU SISTEMA CONTABLE</strong>
                            </td>
                        </tr>";
            $cuerpo .= "<tr>
                            <td class='fondo' colspan=2 style='height:400px; background: #e1e1e1'>
                                $html
                            </td>
                        </tr>";
            $cuerpo .= "<tr>
                            <td colspan=2 style='height:35px; background: black'>
                                <h2 class='text-center; color: white'>
                                    <a style='color: white' href='#'>
                                        <b>PAGUPA</b><span class='letra'>S</span><span class='letra'>O</span><span class='letra'>F</span><span class='letra'>T</span>
                                    </a> 
                                    &nbsp;&nbsp;&nbsp;&nbsp;
                                    <label style='color: white; font-size: 12px; font-weight: normal'>© 2022 PAGUPASOFT. DERECHOS RESERVADOS</label>
                                </h2>
                            </td>
                        </tr>";
            $cuerpo .= "</table>";
            $cuerpo .= "</body></html>";
                        

            require '../vendor/autoload.php';


            $servidor=Servidor_Correo::servidorCorreo()->first();

            $mail = new PHPMailer();
            $mail->isSMTP();
            $mail->SMTPAuth   = true;
            $mail->SMTPSecure = $servidor->servidor_secure;
            $mail->Port = $servidor->servidor_port;
            $mail->SMTPKeepAlive = true;
            
            $mail->Host = $servidor->servidor_host;
            $mail->Username = $servidor->servidor_username;
            $mail->Password = $servidor->servidor_password;

            $mail->setFrom($servidor->servidor_username, $servidor->servidor_from);
            $mail->AddEmbeddedImage(public_path().'/'.$servidor->servidor_embeddedImage, 'logo');
            $mail->Body    = utf8_decode($cuerpo);
            $mail->AltBody = utf8_decode($plain);
            $mail->addAddress(trim($correo), utf8_decode($nombre));
            $mail->addAddress($servidor->servidor_username, 'Yo');
            $mail->Subject = utf8_decode($asunto);
            $mail->WordWrap = 50;

            
            foreach($adjuntos as $adjunto){
                $mail->addAttachment($adjunto->path, $adjunto->nombre);
            }
            

            $mail->isHTML(true);

            $mail->SMTPOptions= array(
                'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
                )
            );

            return $mail->send();
        } catch (\Exception $ex) {
            return $ex->getMessage();
        }
    }
}
