<?php

namespace App\Http\Controllers;

use App\Models\Medico;
use App\Models\Medico_Especialidad;
use App\Models\Configuracion_Reporte;
use App\Models\Empleado;
use App\Models\Empresa;
use App\Models\Proveedor;
use App\Models\Signos_Vitales;
use App\Http\Controllers\Controller;
use App\Http\Controllers\examenController;
use App\Models\Aseguradora_Procedimiento;
use App\Models\Bodega;
use App\Models\Cliente;
use App\Models\Configuracion_Especialidad;
use App\Models\Detalle_Diagnostico;
use App\Models\Detalle_Examen;
use App\Models\Detalle_Expediente;
use App\Models\Detalle_Imagen;
use App\Models\Detalle_OFactura;
use App\Models\Diagnostico;
use App\Models\Enfermedad;
use App\Models\Especialidad;
use App\Models\Examen;
use App\Models\Imagen;
use App\Models\Medicamento;
use App\Models\Movimiento_Producto;
use App\Models\Orden_Atencion;
use App\Models\Orden_Examen;
use App\Models\Orden_Factura;
use App\Models\Orden_Imagen;
use App\Models\Paciente;
use App\Models\Prescripcion;
use App\Models\Prescripcion_Medicamento;
use App\Models\Procedimiento_Especialidad;
use App\Models\Producto;
use Illuminate\Http\Request;
use App\Models\Punto_Emision;
use App\Models\Sucursal;
use App\Models\Tipo_Examen;
use App\Models\Analisis_Laboratorio;
use App\Models\Detalle_Analisis;
use App\Models\Rango_Documento;
use App\Models\Tipo_Detalle_Consulta;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use PDF;
use Excel;
use App\NEOPAGUPA\ViewExcel;
use DateTime;
use Illuminate\Support\Facades\Storage;

class atencionCitasController extends Controller
{
    public function index()
    {
        try{
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
            $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $medicos = Medico::medicos()->get();
            $id = 0;

            foreach($medicos as $medico){
                if($medico->user_id == Auth::user()->user_id){
                    $id = $medico->medico_id;
                }
            }
            
            $medico = Medico::medico($id)->first();
            $mespecialidadM = Medico_Especialidad::mespecialidadM($id)->get();
            $ordenesAtencion = Orden_Atencion::OrdenesHoy()->get();
            return view('admin.citasMedicas.atencionCitas.index',['medico'=>$medico, 'mespecialidadM'=>$mespecialidadM,'ordenesAtencion'=>$ordenesAtencion, 'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        }catch(\Exception $ex){
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }

    public function create()
    {
        //
    }

    public function store(Request $request){
        //try {
            DB::beginTransaction();
            $auditoria = new generalController();
            $atencion=Orden_Atencion::findOrFail($request->get('orden_id'));

            $bodega=Bodega::SucursalBodega($atencion->sucursal_id)->first();
            /**************Diagnostico****************/

            $laboratorio = $request->get('laboratorio');
           
            $DenfermedadId = $request->get('DenfermedadId');
            $DobservacionEnfer = $request->get('DobservacionEnfer');  
            $DcasoN = $request->get('DcboxCasoN');        
            $Ddefinitivo = $request->get('DcboxDefinitivo');   
            $DcasoNEstado = $request->get('DcboxCasoNEstado');        
            $DdefinitivoEstado = $request->get('DcboxDefinitivoEstado');          
            /**************Prescripcion****************/
            $PmedicinaId = $request->get('PmedicinaId');
            $Pcantidad = $request->get('Pcantidad');  
            $Pindicaciones = $request->get('Pindicaciones');  
            $Pproducto = $request->get('Pproducto');  
            /**************Examenes****************/
            
            /**************Imagenes****************/
            $ImagenId = $request->get('ImagenId');
            $Iobservacion = $request->get('Iobservacion');
            /**************Facturacion****************/
            $FprocedimientoAId = $request->get('FprocedimientoAId');
            $FproductoId = $request->get('FproductoId');
            $Fobservacion = $request->get('Fobservacion');
            $Fcosto = $request->get('Fcosto');

            ///Signos Viatles
            $sig_valor = $request->get('svalor');
            $sig_id = $request->get('side');

            ///CEspecialidades
            $c_nombre = $request->get('nombre');
            $c_tipo = $request->get('tipo');
            $c_medida = $request->get('medida');
            $c_id = $request->get('ide');
            $c_valor = $request->get('valor');

            
            if(isset($c_nombre)){
                for ($i = 0; $i < count($c_nombre); ++$i) {
                    $detalle = new Detalle_Expediente();
                    $detalle->detallee_nombre=$c_nombre[$i];
                    $detalle->detallee_tipo=$c_tipo[$i];
                    $detalle->detallee_medida=$c_medida[$i];
                    $detalle->detallee_url=' ';
                    $detalle->detallee_multiple=' ';
                    $detalle->detallee_valor=$c_valor[$i];
                    $detalle->detallee_estado='1';
                    $detalle->expediente_id=$request->get('expediente_id');
                    
                    $detalle->save();
                    $auditoria->registrarAuditoria('Ingreso de Detalle de expediente -> ' .  $request->get('expediente_id'),$atencion->orden_id, 'Con Nombre '.$c_nombre[$i].' Con Valor'.$c_valor[$i] );
                }
            }

            if(isset($c_nsig_valorombre)){
                for ($i = 1; $i < count($sig_valor); ++$i) {
                    $signos=Signos_Vitales::findOrFail($sig_id[$i]);
                    $signos->signo_valor=$sig_valor[$i];
                    $signos->save();
                    
                }
            }
            
            if ($DenfermedadId) {
                if (count($DenfermedadId)>0) {
                    $diagnosticos = new Diagnostico();
                    $diagnosticos->expediente_id = $request->get('expediente_id');
                    $diagnosticos->diagnostico_estado ="1";
                    if ($request->get('diagnostico_observacion')) {
                        $diagnosticos->diagnostico_observacion = $request->get('diagnostico_observacion');
                    } else {
                        $diagnosticos->diagnostico_observacion =' ';
                    }
                    $diagnosticos->save();
                    $auditoria->registrarAuditoria('Ingreso de Diagnostico con expediente -> ' .  $request->get('expediente_id'), $atencion->orden_id, '');
                    for ($i = 0; $i < count($DenfermedadId); ++$i) {
                        $Detalle=new Detalle_Diagnostico();
                        $Detalle->detalled_estado = 1;
                        $Detalle->enfermedad_id = $DenfermedadId[$i];
                        $diagnosticos->detallediagnostico()->save($Detalle);
                        $auditoria->registrarAuditoria('Ingreso de Detalle de Diagnostico con expediente -> ' .  $request->get('expediente_id'), $atencion->orden_id, '');
                    }
                }
            }
            if(isset($PmedicinaId)){
                if (count($PmedicinaId)>1) {
                    $prescripcion = new Prescripcion();
                    if ($request->get('recomendacion_prescripcion')) {
                        $prescripcion->prescripcion_recomendacion = $request->get('recomendacion_prescripcion');
                    } 
                    if ($request->get('observacion_prescripcion')) {
                        $prescripcion->prescripcion_observacion = $request->get('observacion_prescripcion');
                    } 
                    $prescripcion->prescripcion_estado = 1;
                    $prescripcion->expediente_id = $request->get('expediente_id');
                    $prescripcion->save();
                    $auditoria->registrarAuditoria('Ingreso de Medicina con expediente -> ' .  $request->get('expediente_id'),$atencion->orden_id, '');
                    for ($i = 1; $i < count($PmedicinaId); ++$i) {
                        $prescripcionM = new Prescripcion_Medicamento();

                        /******************registro de movimiento de producto******************/
                        $movimientoProducto = new Movimiento_Producto();
                        $movimientoProducto->movimiento_fecha=date('Y-m-d H:i:s');;
                        $movimientoProducto->movimiento_cantidad=$Pcantidad[$i];
                        $movimientoProducto->movimiento_precio=0;
                        $movimientoProducto->movimiento_iva=0;
                        $movimientoProducto->movimiento_total=0;
                        $movimientoProducto->movimiento_stock_actual=0;
                        $movimientoProducto->movimiento_costo_promedio=0;
                        $movimientoProducto->movimiento_documento='FARMACIA';
                        $movimientoProducto->movimiento_motivo='PENDIENTE DE DESAPACHAR';
                        $movimientoProducto->movimiento_tipo='SALIDA';
                        $movimientoProducto->movimiento_descripcion='PENDIENTE DE DESAPACHAR EN FARMACIA POR LA ORDEN DE ATENCION No. '.$atencion->orden_numero;
                        $movimientoProducto->movimiento_estado='0';
                        $movimientoProducto->producto_id=$Pproducto[$i];
                        $movimientoProducto->bodega_id=$bodega->bodega_id;
                        $movimientoProducto->empresa_id=Auth::user()->empresa_id;
                        $movimientoProducto->save();
                        $auditoria->registrarAuditoria('Registro de movimiento de producto por pendiente de despachar en farmacion con la orden de atencion numero -> '.$atencion->orden_numero,$atencion->orden_numero,'Registro de movimiento de producto de farmacia por la orden de atencion -> '.$atencion->orden_numero.' producto id -> '.$Pproducto[$i].' con la cantidad de -> '.$Pcantidad[$i].' con un stock actual de -> '.$movimientoProducto->movimiento_stock_actual);
                        /*********************************************************************/
                        

                        $prescripcionM->prescripcionm_cantidad = $Pcantidad[$i];
                        $prescripcionM->prescripcionm_indicacion = $Pindicaciones[$i];
                        $prescripcionM->prescripcionm_estado = 1;
                        $prescripcionM->medicamento_id = $PmedicinaId[$i];
                        $prescripcionM->movimiento()->associate($movimientoProducto);
                        $prescripcionM->prescripcion()->associate($prescripcion);
                        $prescripcionM->save();

                    
                        $auditoria->registrarAuditoria('Ingreso de Detalle Medicina con expediente -> ' .  $request->get('expediente_id'),$atencion->orden_id, 'Con Medicina Id '.$PmedicinaId[$i].' Con Cantidad '.$Pcantidad[$i]);
                    }
                }
            }

            $OrdenExamenPdfDir = null;

            if (isset($laboratorio)) {
                $ordenExamen = new Orden_Examen();
                $ordenExamen->orden_id=$atencion->orden_id;
                $ordenExamen->expediente_id = $request->get('expediente_id'); 

                if($request->get('otros_examenes')){ 
                    $ordenExamen->orden_otros = $request->get('otros_examenes');
                }

                ///////////  si es iess pasa directo a subir examenes (estado 2)
                if($atencion->orden_iess == '0'){
                    $ordenExamen->orden_estado = 1;
                    $ordenExamen->save();
                }
                else{
                    $ordenExamen->orden_estado = 2;
                    $ordenExamen->save();

                    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                    ///////////////////////////////////////////////////////creo un examen de laboratorio que no lleva factura //////////////////////////////////////////////////////////////////////////////
                    //$puntoEmision = Punto_Emision::PuntoSucursalUser($atencion->expediente->ordenatencion->sucursal_id, Auth::user()->user_id)->first();
                    $puntoEmision = Punto_Emision::PuntoxSucursal($atencion->expediente->ordenatencion->sucursal_id)->first();
                    $rangoDocumento=Rango_Documento::PuntoRango($puntoEmision->punto_id, 'Analisis de Laboratorio')->first();
                    
                    $secuencial=1;
                    $analisis=new Analisis_Laboratorio();

                    if($rangoDocumento){
                        $secuencialAux=Analisis_Laboratorio::secuencial($rangoDocumento->rango_id)->max('analisis_secuencial');
                        if($secuencialAux){
                            $secuencial=$secuencialAux+1;  
                        }
                    
                        $analisis->analisis_numero=$atencion->orden_codigo.'-'.substr(str_repeat(0, 9).$secuencial, - 9);
                        $analisis->analisis_serie=$atencion->orden_codigo;
                        $analisis->analisis_secuencial=$secuencial;

                        $analisis->analisis_fecha=(new DateTime())->format('Y-m-d');
                        $analisis->analisis_otros=$request->get('otros_examenes');;
                        $analisis->analisis_observacion='';
                        $analisis->analisis_estado='1';
                        $analisis->sucursal_id=Rango_Documento::rango($rangoDocumento->rango_id)->first()->puntoEmision->sucursal_id;
                        $analisis->orden_id=$atencion->orden_id;
                        $analisis->factura_id=null;
                        $analisis->orden_particular_id=null;
                        $analisis->save();
                        
                        for ($i = 0; $i < count($laboratorio); ++$i){
                            $detalleanalisis=new Detalle_Analisis();
                            $detalleanalisis->detalle_estado='1';
                            $detalleanalisis->producto_id=$laboratorio[$i];

                            $producto = Producto::findOrFail($laboratorio[$i]);
                            $detalleanalisis->id_externo=$producto->producto_codigo;

                            $analisis->detalles()->save($detalleanalisis);
                            $detalleanalisis->save();
                        }

                    }else{
                        return redirect('inicio')->with('error','No tiene configurado, un punto de emisión o un rango de documentos para emitir facturas de venta, configueros y vuelva a intentar');
                    }


                    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                }
                $auditoria->registrarAuditoria('Ingreso de Examenes con expediente -> ' .  $request->get('expediente_id'),$atencion->orden_id, '');
            
                for ($i = 0; $i < count($laboratorio); ++$i) {
                    $detalleExamen = new Detalle_Examen();
                    $detalleExamen->detalle_estado="1";
                    $detalleExamen->examen_id=$laboratorio[$i];
                    $detalleExamen->orden_id=$ordenExamen->orden_id;
                    $detalleExamen->save();

                    $examen= $detalleExamen->examen($detalleExamen->examen_id)->first();
                    $auditoria->registrarAuditoria('Ingreso de Detalle Examenes con expediente -> ' .  $request->get('expediente_id'),$atencion->orden_id, 'Con examen Id '.$laboratorio[$i]);
                }

                if($atencion->orden_iess == '1'){
                    $ExamenController=new examenController();
                    $resultadoEnvio = $ExamenController->postCrearOrden($ordenExamen);

                    
                    if($resultadoEnvio->codigo==201){ //////éxito
                        $analisis->analisis_estado = '2';
                        $analisis->save();
                        $ordenExamen->orden_id_referencia=$resultadoEnvio->resultado['data']['id'];
                        $ordenExamen->orden_numero_referencia=$resultadoEnvio->resultado['data']['numero_orden'];
                        $ordenExamen->update();

                        //$this->sendMailNotifications($orden->orden_numero_referencia);
                    }
                }

                $OrdenExamenPdfDir=$this->crearOrdenExamenPdf($atencion, $ordenExamen);
            }

            
            $OrdenImagenPdfDir=null;

            if (count($ImagenId)>1) {
                $ordenImagen = new Orden_Imagen();

                ///////////  si es iess pasa directo a subir examenes (estado 2)
                if($atencion->orden_iess == '0')
                    $ordenImagen->orden_estado = 1; 
                else
                    $ordenImagen->orden_estado = 2;

                if ($request->get('otros_imagen')) {
                    $ordenImagen->orden_observacion=$request->get('otros_imagen');
                }

                $ordenImagen->expediente_id = $request->get('expediente_id');
                $ordenImagen->save();
                $auditoria->registrarAuditoria('Ingreso de Imagenes con expediente -> ' .  $request->get('expediente_id'),$atencion->orden_id, '');
                
               
                for ($i = 1; $i < count($ImagenId); ++$i) {
                    $detalleImagen = new Detalle_Imagen();
                    if ($Iobservacion[$i]) {
                        $detalleImagen->detalle_indicacion = $Iobservacion[$i];
                    }
                    $detalleImagen->detalle_estado = 1;
                    $detalleImagen->imagen_id = $ImagenId[$i];

                    $ordenImagen->detalleImagen()->save($detalleImagen);
                    $auditoria->registrarAuditoria('Ingreso de Detalle Imagenes con expediente -> ' .  $request->get('expediente_id'),$atencion->orden_id, 'Las imagenes asignadas fueron -> ' .$Iobservacion[$i]);
                }

                $OrdenImagenPdfDir=$this->crearOrdenImagenPdf($atencion, $ordenImagen, $request->file('imagefile'));
            }
            
            if($request->file('imagefile')!=null) 
                $AnexoPdfDir=$this->crearAnexoPdf($atencion, $request->file('imagefile'));
            
            $atencion->orden_frecuencia=$request->get('tipo_atencion');
            $atencion->orden_estado='4';
            $atencion->save();

            /*Inicio de registro de auditoria */
            $auditoria->registrarAuditoria('Actualizacion de Examen a estado Atendido Numero'.$atencion->orden_numero.' Con Expediente '.$request->get('expediente_id'),$atencion->orden_id, '');
            /*Fin de registro de auditoria */

            DB::commit();
            $redirect = redirect('atencionCitas')->with('success', 'Datos guardados exitosamente');
            
            if(isset($OrdenExamenPdfDir)) $redirect->with('pdf', $OrdenExamenPdfDir);
            if(isset($OrdenImagenPdfDir)) $redirect->with('pdf2', $OrdenImagenPdfDir);
            if(isset($AnexoPdfDir)) $redirect->with('diario', $AnexoPdfDir);

            return $redirect;
        /* } catch (\Exception $ex) {
            DB::rollBack();
            return redirect('atencionCitas')->with('error', 'Ocurrio un error en el procedimiento. Vuelva a intentar.('.$ex->getMessage().')');
        } */
    }

    public function informeHistoricoIndex(Request $request){
        try{
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
            $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $medicos = Medico::medicos()->get();
            $id = 0;

            foreach($medicos as $medico){
                if($medico->user_id == Auth::user()->user_id){
                    $id = $medico->medico_id;
                }
            }
            
            $medico = Medico::medico($id)->first();
            $mespecialidadM = Medico_Especialidad::mespecialidadM($id)->get();
            $prescripciones = Prescripcion::prescripcionesPaciente()->get();
            $pacientes = Paciente::pacientes()->get();


            $sucursales=Sucursal::sucursales()->get();
            

            //return $prescripciones;

            return view('admin.citasMedicas.atencionCitas.historicoPlano',['medico'=>$medico, 'sucursales'=>$sucursales, 'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        }catch(\Exception $ex){
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }

    public function informeHistoricoPlano(Request $request){
        try{   
            $sucursal=Sucursal::findOrFail($request->sucursal);
            $ordenes=Orden_Atencion::ordenesByFechaSuc($request->fecha_desde, $request->fecha_hasta, $sucursal->sucursal_id)->get();
            
            if($ordenes){
                foreach($ordenes as $orden){
                    $expediente=$orden->expediente;
                    $producto=$orden->producto;
                    $paciente=$orden->paciente;
                    $especialidad=$orden->especialidad;
                    $tipoSeguro=$orden->tipoSeguro;


                    $procedimientoEspecialidad=Procedimiento_Especialidad::procedimientoProductoEspecialidad($producto->producto_id, $especialidad->especialidad_id)->first();
                    $procedimientoAseguradora=Aseguradora_Procedimiento::procedimientosAsignados($procedimientoEspecialidad->procedimiento_id, $orden->cliente_id)->first();
                    $datos[$orden->orden_id][$producto->producto_id]=$procedimientoAseguradora;

                    if($paciente){
                        $dependencia=$paciente->tipoDependencia;
                    }

                    ///////////////diagnóstico///////////////////////////////
                    if($expediente){
                        $diagnostico=$expediente->diagnostico;

                        if($diagnostico){
                            $diagDetalle=$diagnostico->detallediagnostico;

                            foreach($diagDetalle as $detalle){
                                $detalle->enfermedad;
                            }
                        }

                        $ordenExamen=$expediente->ordenExamen;

                        if($ordenExamen){
                            $detalleExamen=$ordenExamen->detalle;

                            foreach($detalleExamen as $detalle){
                                $examen=$detalle->examen;

                                if($examen){
                                    $productoExamen=$examen->producto;
                                }
                            }
                        }
                    }
                }
            }

            $datos['ordenes']= $ordenes;
            return Excel::download(new ViewExcel('admin.formatosExcel.historicoplano', $datos), 'NEOPAGUPA  Sistema Contable.xls');
        }catch(\Exception $ex){
            return redirect('informehistoricoplano')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }

    public function informeIndividualIndex(Request $request){
        //try{
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
            $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $medicos = Medico::medicos()->get();
            $sucursales=Sucursal::sucursales()->get();

            $ordenes=null;
            $id = 0;

            foreach($medicos as $medico){
                if($medico->user_id == Auth::user()->user_id){
                    $id = $medico->medico_id;
                }
            }

            if($id>0)
                $medico = Medico::findOrFail($id);
            else
                $medico=null;

            if($request->fecha_desde!=null){
                $sucursal=Sucursal::findOrFail($request->sucursal);
                $ordenes=Orden_Atencion::ordenesByFechaSuc($request->fecha_desde, $request->fecha_hasta, $sucursal->sucursal_id)->get();

                foreach($ordenes as $orden){
                    $orden->paciente;
                }
            }
            else
                $sucursal=Sucursal::sucursales()->first();
            
            
            //$mespecialidadM = Medico_Especialidad::mespecialidadM($id)->get();
            //$prescripciones = Prescripcion::prescripcionesPaciente()->get();
            //$pacientes = Paciente::pacientes()->get();


            
            
            $data = [
                'medico'=>$medico,
                'sucursales'=>$sucursales,
                "ordenes"=>$ordenes,
                'PE'=>Punto_Emision::puntos()->get(),
                'gruposPermiso'=>$gruposPermiso,
                'permisosAdmin'=>$permisosAdmin,
                'fDesde'=>$request->fecha_desde,
                'fHasta'=>$request->fecha_hasta,
                'sucursal_id'=> $sucursal->sucursal_id
            ];

            //return $data;

            return view('admin.citasMedicas.atencionCitas.individualPlano', $data);
        //}catch(\Exception $ex){
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
       // }
    }

    public function informeIndividualPlano(Request $request){
        //return $request;
        //try{
            //$sucursal=Sucursal::findOrFail($request->sucursal_id);
            $ordenes=Orden_Atencion::ordenesByFechaSucPac($request->fechaA, $request->fechaB, $request->sucursal_id, $request->paciente_id)->get();
            
            if($ordenes){
                foreach($ordenes as $orden){
                    $expediente=$orden->expediente;
                    $producto=$orden->producto;
                    $paciente=$orden->paciente;
                    $especialidad=$orden->especialidad;
                    $tipoSeguro=$orden->tipoSeguro;


                    $procedimientoEspecialidad=Procedimiento_Especialidad::procedimientoProductoEspecialidad($producto->producto_id, $especialidad->especialidad_id)->first();
                    $procedimientoAseguradora=Aseguradora_Procedimiento::procedimientosAsignados($procedimientoEspecialidad->procedimiento_id, $orden->paciente->cliente_id)->first();
                    $datos[$orden->orden_id][$producto->producto_id]=$procedimientoAseguradora;

                    if($paciente){
                        $dependencia=$paciente->tipoDependencia;
                    }

                    ///////////////diagnóstico///////////////////////////////
                    if($expediente){
                        $diagnostico=$expediente->diagnostico;

                        if($diagnostico){
                            $diagDetalle=$diagnostico->detallediagnostico;

                            foreach($diagDetalle as $detalle){
                                $detalle->enfermedad;
                            }
                        }

                        $ordenExamen=$expediente->ordenExamen;
                        $ordenImagen=$expediente->ordenImagen;
                        $prescripcion=$expediente->prescripcion;

                        if($ordenExamen){
                            $detalleExamen=$ordenExamen->detalle;

                            foreach($detalleExamen as $detalle){
                                $examen=$detalle->examen;
                                
                                    
                                if($examen){
                                    $productoExamen=$examen->producto;
                                    $examen->tipoExamen;
                                    $procEspe=Procedimiento_Especialidad::procedimientoProductoEspecialidad($productoExamen->producto_id, $especialidad->especialidad_id)->first();

                                    if($procEspe){
                                        $procAseg=Aseguradora_Procedimiento::procedimientosAsignados($procEspe->procedimiento_id, $orden->paciente->cliente_id)->first();
                                        $datos['detalle_examen'][$detalle->detalle_id] = $procAseg;

                                        
                                    }
                                }
                            }
                        }
                        
                        
                        if($ordenImagen){
                            $detalleImagen=$ordenImagen->detalleImagen;
                            
                            foreach($detalleImagen as $detalle){
                                $imagen=$detalle->imagen;
                                
                                if($imagen){
                                    $productoImagen=$imagen->producto;
                                    $imagen->tipoImagen;

                                    $procEspe=Procedimiento_Especialidad::procedimientoProductoEspecialidad($productoImagen->producto_id, $especialidad->especialidad_id)->first();

                                    if($procEspe){
                                        $procAseg=Aseguradora_Procedimiento::procedimientosAsignados($procEspe->procedimiento_id, $orden->paciente->cliente_id)->first();
                                        $datos['detalle_imagen'][$detalle->detalle_id] = $procAseg;
                                    }
                                }
                            }
                        }

                        if($prescripcion){
                            //return 'si presc';
                            $detalleprescipcion=$prescripcion->presMedicamento;

                            foreach($detalleprescipcion as $detalle){
                                $medicamento=$detalle->medicamento;
                                
                                    
                                if($medicamento){
                                    $productoMedic=$medicamento->producto;
                                    $medicamento->tipoMedicamento;

                                    $procEspe=Procedimiento_Especialidad::procedimientoProductoEspecialidad($productoMedic->producto_id, $especialidad->especialidad_id)->first();

                                    if($procEspe){
                                        $procAseg=Aseguradora_Procedimiento::procedimientosAsignados($procEspe->procedimiento_id, $orden->paciente->cliente_id)->first();
                                        $datos['detalle_medicamento'][$detalle->prescripcionM_id] = $procAseg;
                                    }
                                }
                            }
                        }
                        //else
                            //return 'no presc';

                        //if($orden->orden_secuencial==78)
                        //    return $expediente;
                    }
                }
            }

            
            $datos['ordenes']= $ordenes;
            //return  $datos['ordenes'];


            return Excel::download(new ViewExcel('admin.formatosExcel.individualPlano', $datos), 'NEOPAGUPA  Sistema Contable.xls');
        //}catch(\Exception $ex){
            return redirect('informehistoricoplano')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        //}
    }


    public function informeCargaMasivaIndex(Request $request){
        try{
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
            $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $medicos = Medico::medicos()->get();
            $id = 0;

            foreach($medicos as $medico){
                if($medico->user_id == Auth::user()->user_id){
                    $id = $medico->medico_id;
                }
            }
            

            $configuracion=Configuracion_Reporte::getConfiguracionReporteMasivo()->first();

            if(!$configuracion){
                $detalle=explode("-", "1-1-1-1-1-1-1-1-1-1-1-1-1-1-1-1-1-1-1-1-1-1-1-1-1-1-1-1-1-1-1-1-1-1-1-1-1-1-1-1");
                $nombre='REPORTE MASIVO';
            }
            else{
                $detalle=explode("-", $configuracion->configuracion_detalle);
                $nombre=$configuracion->configuracion_nombre;
            }


                
            $medico = Medico::medico($id)->first();
            $mespecialidadM = Medico_Especialidad::mespecialidadM($id)->get();
            $prescripciones = Prescripcion::prescripcionesPaciente()->get();
            $pacientes = Paciente::pacientes()->get();


            $sucursales=Sucursal::sucursales()->get();
            
            $pass=[
                'medico'=>$medico, 
                'sucursales'=>$sucursales, 
                'PE'=>Punto_Emision::puntos()->get(),
                'gruposPermiso'=>$gruposPermiso, 
                'permisosAdmin'=>$permisosAdmin,
                'config'=>(Object)array(
                    "nombre"=>'REPORTE MASIVO',
                    "valor"=> $detalle)
            ];

            //return json_encode($pass);

            return view('admin.citasMedicas.atencionCitas.cargamasiva', $pass);
        }catch(\Exception $ex){
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }

    public function informeCargaMasiva(Request $request){
        //try{   
            $detalleConfig="";
            for($i=0; $i<40; $i++){
                if(isset($_POST['chk'.($i+1)]))
                    $detalleConfig.="1-";
                else 
                    $detalleConfig.="0-";
            }

            $configuracion=Configuracion_Reporte::getConfiguracionReporteMasivo()->first();

            if(!$configuracion){
                $configuracion=new Configuracion_Reporte();
                $configuracion->configuracion_nombre="REPORTE MASIVO";
                $configuracion->configuracion_detalle=$detalleConfig;
            }
            else{
                $configuracion->configuracion_detalle=$detalleConfig;
            }

            $configuracion->save();

            $sucursal=Sucursal::findOrFail($request->sucursal);
            $ordenes=Orden_Atencion::ordenesByFechaSucNoIess($request->fecha_desde, $request->fecha_hasta, $sucursal->sucursal_id)->get();
            
            if($ordenes){
                foreach($ordenes as $orden){

                    $expediente=$orden->expediente;
                    $producto=$orden->producto;
                    $paciente=$orden->paciente;
                    $cliente=$orden->cliente;
                    $especialidad=$orden->especialidad;
                    $medico=$orden->medico->empleado;
                    $tipoSeguro=$orden->tipoSeguro;
                    $sucursal_nombre=$orden->sucursal->sucursal_nombre;
                    $factura=$orden->factura;

                    if($factura)
                        $detalleFV=$factura->detalles;


                    if($producto){
                        $procedimientoEspecialidad=Procedimiento_Especialidad::procedimientoProductoEspecialidad($producto->producto_id, $especialidad->especialidad_id)->first();
                        $procedimientoAseguradora=Aseguradora_Procedimiento::procedimientosAsignados($procedimientoEspecialidad->procedimiento_id, $orden->cliente_id)->first();
                        $datos[$orden->orden_id][$producto->producto_id]=$procedimientoAseguradora;

                        if($paciente){
                            $dependencia=$paciente->tipoDependencia;
                        }

                        ///////////////diagnóstico///////////////////////////////
                        if($expediente){
                            $diagnostico=$expediente->diagnostico;

                            if($diagnostico){
                                $diagDetalle=$diagnostico->detallediagnostico;

                                foreach($diagDetalle as $detalle){
                                    $detalle->enfermedad;
                                }
                            }

                            $ordenExamen=$expediente->ordenExamen;

                            if($ordenExamen){
                                $detalleExamen=$ordenExamen->detalle;

                                foreach($detalleExamen as $detalle){
                                    $examen=$detalle->examen;

                                    if($examen){
                                        $productoExamen=$examen->producto;
                                    }
                                }
                            }
                        }
                    }
                }
            }

            $datos['ordenes']= $ordenes;
            $datos['config'] = explode("-",$detalleConfig);
            //$datos['sucursal_nombre']='$sucursal->sucursal_nombre';

            //return $datos;

            return Excel::download(new ViewExcel('admin.formatosExcel.cargamasiva', $datos), 'NEOPAGUPA-Sistema Contable Informe.xls');
        /* }catch(\Exception $ex){
            return redirect('informecargamasiva')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        } */
    }








    private function crearOrdenExamenPdf($atencion, $ordenExamen){
        $empresa = Empresa::empresa()->first();
        $fecha = (new DateTime("$atencion->orden_fecha"))->format('d-m-Y');

        $view =  \View::make('admin.formatosPDF.ordenesAtenciones.ordenExamenMedico', ['orden'=>$atencion, 'ordenExamen'=> $ordenExamen, 'empresa'=>$empresa]);
        $ruta = 'DocumentosOrdenAtencion/'.$empresa->empresa_ruc.'/'.$fecha.'/'.$atencion->orden_numero.'/Documentos/Laboratorio';
        if (!is_dir(public_path().'/'.$ruta)) {
            mkdir(public_path().'/'.$ruta, 0777, true);
        }
        $nombreArchivo = 'ORDEN_EXAMENES';
        PDF::loadHTML($view)->save(public_path().'/'.$ruta.'/'.$nombreArchivo.'.pdf');

        return $ruta.'/'.$nombreArchivo.'.pdf';
    }

    private function crearOrdenImagenPdf($atencion, $ordenImagen, $imagefile){
        $empresa = Empresa::empresa()->first();
        $fecha = (new DateTime("$atencion->orden_fecha"))->format('d-m-Y');

        //$detalleImagen = Detalle_Imagen::DetalleImagen($ordenImagen->orden_id);

        $view =  \View::make('admin.formatosPDF.ordenesAtenciones.ordenExamenImagen', ['orden'=>$atencion, 'ordenImagen'=> $ordenImagen, 'empresa'=>$empresa]);
        $ruta = 'DocumentosOrdenAtencion/'.$empresa->empresa_ruc.'/'.$fecha.'/'.$atencion->orden_numero.'/Documentos/Imagenes';
        if (!is_dir(public_path().'/'.$ruta)) {
            mkdir(public_path().'/'.$ruta, 0777, true);
        }
        $nombreArchivo = 'ORDEN_IMAGENES';
        PDF::loadHTML($view)->save(public_path().'/'.$ruta.'/'.$nombreArchivo.'.pdf');

        return $ruta.'/'.$nombreArchivo.'.pdf';
    }

    private function crearAnexoPdf($atencion, $imagefile){
        $imagenes=[];
        $empresa = Empresa::empresa()->first();
        $fecha = (new DateTime("$atencion->orden_fecha"))->format('d-m-Y');

        $ruta = 'DocumentosOrdenAtencion/'.$empresa->empresa_ruc.'/'.$fecha.'/'.$atencion->orden_numero.'/Documentos/Anexos';

        if ($imagefile) {
            if (!is_dir(public_path().'/'.$ruta)) {
                mkdir(public_path().'/'.$ruta, 0777, true);
            }
            
            $c=0;
            foreach($imagefile as $file){
                $c++;

                if($c>0){
                    $name = 'imagen_temporal_'.$c.'.jpeg';

                    $path = $file->move(public_path().'/'.$ruta, $name);
                    $temp = [
                        'num'=>$c,
                        'ruta'=>$ruta,
                        'nombre'=>$name,
                        'path'=>$path
                    ];

                    $imagenes[] = $temp;
                }
            }
        }

        
        $view =  \View::make('admin.formatosPDF.ordenesAtenciones.ordenAtencionCitaPdf', ['ordenAtencion'=>$atencion, 'imagenes'=>$imagenes, 'empresa'=>$empresa]);
        $ruta = 'DocumentosOrdenAtencion/'.$empresa->empresa_ruc.'/'.$fecha.'/'.$atencion->orden_numero.'/Documentos';
        if (!is_dir(public_path().'/'.$ruta)) {
            mkdir(public_path().'/'.$ruta, 0777, true);
        }
        $nombreArchivo = 'ANEXOS_ATENCION';
        PDF::loadHTML($view)->save(public_path().'/'.$ruta.'/'.$nombreArchivo.'.pdf');

        //borrar las imagenes creadas arriba
        foreach($imagenes as $foto){
            $result = Storage::delete(public_path().'/'.$foto['ruta'].'/'.$foto['nombre']);
        }

        return $ruta.'/'.$nombreArchivo.'.pdf';
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id){
        try{
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
            $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $medico = Medico::medico($id)->first();
            $mespecialidadM = Medico_Especialidad::mespecialidadM($id)->get();
            $ordenesAtencion = Orden_Atencion::Ordenes()->get();
            if($medico){
                return view('admin.citasMedicas.atencionCitas.ver',['medico'=>$medico,'ordenesAtencion'=>$ordenesAtencion,'mespecialidadM'=>$mespecialidadM,'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
            }else{
                return redirect('/denegado');
            }
        }catch(\Exception $ex){
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }

    public function atender($id){
        try{
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
            $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();

            $diagnosticos = Diagnostico::Diagnosticos()->get();
            $medicamentos = Medicamento::Medicamentos()->get();
            $enfermedades = Enfermedad::Enfermedades()->get();
           
            $imagenes = Imagen::Imagenes()->get();
            $sucursales = Sucursal::Sucursales()->get();
            $especialidades = Especialidad::Especialidades()->get();
            //$examenes = Examen::Examenes()->get();
            
            $tipoExamenes = Tipo_Examen::TipoExamenes()->get();
            $productos = Producto::Productos()->get();
            
            //return $productos;

            //$medicos = Medico::medicos()->get();
            $ordenAtencion = Orden_Atencion::findOrFail($id);
            
            $medico = $ordenAtencion->medico;
            $cespecialidad=Configuracion_Especialidad::configuracionEspecialidades()->get();
            $tiposDetalles=Tipo_Detalle_Consulta::tiposDetalles()->get();
            
            $signoVital=Signos_Vitales::SignoVitalOrdenId($ordenAtencion->orden_id)->get();

            $espLaboratorio=Especialidad::especialidadBuscar('laboratorio')->first();
            $examenes= Examen::buscarProductosProcedimiento($ordenAtencion->paciente->paciente_id, $espLaboratorio->especialidad_id
                                )->select('examen.examen_id', 'producto.producto_codigo', 'producto.producto_nombre'
                                )->get();

            //return $examenes;
            
            $data=[
                'cespecialidad'=>$cespecialidad,
                'medico'=>$medico,
                'examenes'=>$examenes,
                'tipoExamenes'=>$tipoExamenes,
                'especialidades'=>$especialidades,
                'productos'=>$productos,
                'imagenes'=>$imagenes,
                'sucursales'=>$sucursales,
                'enfermedades'=>$enfermedades,
                'medicamentos'=>$medicamentos,
                'diagnosticos'=>$diagnosticos,
                'signoVital'=>$signoVital,
                'ordenAtencion'=>$ordenAtencion,
                'PE'=>Punto_Emision::puntos()->get(),
                'tipoPermiso'=>$tipoPermiso,
                'gruposPermiso'=>$gruposPermiso,
                'permisosAdmin'=>$permisosAdmin,
                'tiposDetalles'=>$tiposDetalles
            ];

            return view('admin.citasMedicas.atencionCitas.atender', $data);
        }catch(\Exception $ex){
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
        
    }

    public function editarDiagnostico($id){
        try{
            DB::beginTransaction();
            $ordenAtencion=Orden_Atencion::findOrFail($id);
            $enfermedades = Enfermedad::Enfermedades()->get();
            $especialidades = Especialidad::especialidades()->get();

            $expediente=$ordenAtencion->expediente;
            $diagnostico=$expediente->diagnostico;

            if($diagnostico!=null){
                foreach($diagnostico->detallediagnostico as $detalle){
                    $det = $detalle->enfermedad;
                }
            }

            $data=[
                "especialidades" => $especialidades,
                "diagnostico" => $diagnostico? $diagnostico->detallediagnostico : [],
                "ordenAtencion"=>$ordenAtencion,
                "enfermedades"=>$enfermedades,
                "medico"=>$ordenAtencion->medico,
                "observacion"=>$diagnostico? $diagnostico->diagnostico_observacion: ""
            ];

            $auditoria = new generalController();
            $auditoria->registrarAuditoria('Modificación de Examenes con expediente -> ' .   $expediente->expediente_id, $id, 'Con examenes Ids '.json_encode($enfermedades));

            DB::commit();
            return view('admin.citasMedicas.atencionCitas.editarDiagnostico', $data);
        }
        catch(\Exception $ex){

        }

    }

    public function actualizarDiagnosticoOrdenAtencion(Request $request, $id){
        try {
            DB::beginTransaction();
            $auditoria = new generalController();

            $ordenAtencion = Orden_Atencion::findOrFail($id);
            $expediente=$ordenAtencion->expediente;
            $diagnostico=$expediente->diagnostico;

            if($diagnostico){
                $diagnosticoDetalle=$diagnostico->detalleDiagnostico;
                

                $borrados="";

                if(count($diagnosticoDetalle)>0){
                    foreach($diagnosticoDetalle as $diag){
                        $borrados.=$diag->enfermedad_id.',';
                        $diag->delete();
                    }
                    
                    $auditoria->registrarAuditoria('Eliminación de Detalle Diagnostico por modificación con expediente -> ' .   $expediente->expediente_id, $id, 'Con diagnostico eliminados Ids: '.$borrados);
                }
            }
            else{
                $diagnostico=New Diagnostico();
                $diagnostico->expediente_id=$expediente->expediente_id;
                $diagnostico->diagnostico_observacion=$request->diagnostico_observacion;
                $diagnostico->diagnostico_estado=1;
                $diagnostico->save();
            }


            $enfermedades = $request->get('DenfermedadId');

            for ($i = 0; $i < count($enfermedades); ++$i) {
                $detDiagnostico = new Detalle_Diagnostico();
                $detDiagnostico->detalled_estado="1";
                $detDiagnostico->enfermedad_id=$enfermedades[$i];
                $detDiagnostico->diagnostico_id=$diagnostico->diagnostico_id;
                $detDiagnostico->save();
            }
            

            $auditoria->registrarAuditoria('Modificación de Examenes con expediente -> ' .   $expediente->expediente_id, $id, 'Con examenes Ids '.json_encode($enfermedades));

            DB::commit();

            return $redirect = redirect('ordenAtencionEditar')->with('success', 'Datos guardados exitosamente');
        }
        catch(\Exception $e){
            DB::rollBack();

            return $redirect = redirect('ordenAtencionEditar')->with('error', 'Ocurrió un error al actualizar: '.$e->getMessage());
        }
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

    //api orion
    public function pruebaOrden(){
        $fecha = date('Y-m-d h:i:s');
        $paciente = (object) array(
            'tipo' =>'CED',
            'numero_identificacion'=>0705052470,
            'nombres'=>'Cristhian Manuel',
            'apellidos'=>'Armijos Jimenez',
            'fecha_nacimiento'=>'1988-06-15',
            'sexo'=>'M',
            'correo'=>'armijos@gmail.com',
            'telefono'=>'0984092819'
        );

        $medico = (object) array(
            "id_externo"=> '27',
            "numero_identificacion"=> '0728394859',
            "nombres"=> 'WILSON ALEXANDER',
            "apellidos"=> 'BELDUMA MORA'
        );

        $examenes = array(
            (object) array(
                "id_externo"=> 'COL',
                "muestra_pendiente"=> 0,
                //"precio"=> 0
            ),
            (object) array(
                "id_externo"=> 'TRI',
                "muestra_pendiente"=> 0,
                //"precio"=> 0
            ),
        );

        //return json_encode($examenes);

        $result = $this->postCrearOrden(234, $fecha, $paciente, $medico, $examenes);

        

        echo json_encode($result,JSON_PRETTY_PRINT);
    }

    public function pruebaGetOrdenes(){
        $fecha = date('Y-m-d');

        //$result = $this->getOrdenes(234, $fecha);
        $result = $this->getOrdenes(234);

        echo '<pre>';
        echo json_encode($result,JSON_PRETTY_PRINT);
        echo '</pre>';
    }

    public function pruebaGetOrden(){
        $result = $this->getOrden(5929);

        echo '<pre>';
        echo json_encode($result,JSON_PRETTY_PRINT);
        echo '</pre>';
    }

    public function pruebaGetOrdenPdf(){
        $result = $this->getOrdenPdf(5929);

        //echo '<pre>';
        //echo json_encode($result,JSON_PRETTY_PRINT);
        //echo '</pre>';

        if($result->codigo="200"){ //mostrar pdf en una ventana
            return $result->resultado;
        }
    }
}
