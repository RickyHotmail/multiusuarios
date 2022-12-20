<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Analisis_Laboratorio;
use App\Models\Detalle_Analisis;
use App\Models\Detalles_Analisis_Referenciales;
use App\Models\Detalles_Analisis_Valores;
use App\Models\Email_Empresa;
use App\Models\Empresa;
use App\Models\Examen;
use App\Models\Orden_Examen;
use App\Models\Paciente;
use App\Models\Producto;
use App\Models\Punto_Emision;
use App\Models\User;
use App\Models\Valor_Laboratorio;
use App\Models\Valor_Referencial;
use DateTime;
use Exception;
use PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PHPMailer\PHPMailer\PHPMailer;

class analisis_LaboratorioController extends Controller{
    public function index(){
        try{  
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
            $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();        
            $analisis=Analisis_Laboratorio::analisisatender()->get();
            $analisisuser=Analisis_Laboratorio::analisisUSERS()->get();
            return view('admin.laboratorio.ordenesExamen.analisis',['analisisuser'=>$analisisuser,'analisis'=>$analisis,'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        }catch(\Exception $ex){
            return redirect('inicio')->with('error','Ocurrio un error vuelva a intentarlo');
        }
    }

    public function cargarDatosExamenes(){
        $auditoria = new generalController();
        $cantidad=0;
        $total=0;
        $noenviadas=0;

        //try{
            DB::beginTransaction();
            $analisis_lista = Analisis_Laboratorio::analisisatender()->get();

            foreach($analisis_lista as $analisis){
                $orden = $analisis->orden;

                if($analisis->analisis_estado==2){
                    return 3;
                    $total++;

                    if($orden->orden_numero_referencia!=null){
                        $ordenes_externo=(Object)json_decode($this->getOrdenes($orden->orden_numero_referencia));

                        return $ordenes_externo;

                        if(isset($ordenes_externo)){
                            foreach($ordenes_externo->data as $orden){
                                $orden_laboratorio= (Object) $orden;
                                
                                if($orden_laboratorio->estado=='V'  || $orden_laboratorio->estado=='R'){
                                    foreach($orden_laboratorio->examenes as $detalle_array){
                                        $detalleRequest = (Object) $detalle_array;
                                        
                                        //actualizar el detalle de analisis
                                        $detalle_analisis=Detalle_Analisis::detalleExamen($analisis->analisis_laboratorio_id, $detalleRequest->id_externo)->first();
                                        $detalle_analisis->tecnica="$detalleRequest->tecnica";
                                
                                        if(isset($detalleRequest->fecha_recepcion_muestra)){
                                            $detalle_analisis->fecha_recepcion_muestra="$detalleRequest->fecha_recepcion_muestra";
                                        }

                                        $detalle_analisis->fecha_reporte="$detalleRequest->fecha_reporte";
                                        $detalle_analisis->fecha_validacion="$detalleRequest->fecha_validacion";
                                        $detalle_analisis->usuario_validacion=json_encode($detalleRequest->usuario_validacion);
                                        $detalle_analisis->estado=$detalleRequest->estado;
                                        $detalle_analisis->save();

                                        //borrar resultados en caso que haya algún detalle guardado (el API devuelve todos otra vez)
                                        foreach($analisis->detalles as $detalle){
                                            foreach($detalle->detalles as $fila){
                                                $newfila=Detalles_Analisis_Valores::findOrFail($fila->detalle_valores_id);
                                                $newfila->delete();
                                            }
                                        }

                                        //actualizar resultados de cada analisis
                                        foreach($detalleRequest->resultados as $resultado_array){
                                            $resultadoObject=(Object) $resultado_array;
                                            $valores = new Detalles_Analisis_Valores();

                                            $valores->detalle_id=$detalle_analisis->detalle_id;
                                            $valores->id_externo_parametro=$resultadoObject->id_externo_parametro;
                                            $valores->nombre_parametro=$resultadoObject->nombre_parametro;
                                            $valores->resultado=$resultadoObject->resultado;
                                            $valores->unidad_medida=$resultadoObject->unidad_medida;

                                            $valores->valor_minimo=$resultadoObject->valor_minimo;
                                            $valores->valor_maximo=$resultadoObject->valor_maximo;
                                            $valores->valor_normal=$resultadoObject->valor_normal;

                                            $valores->interpretacion=$resultadoObject->interpretacion;
                                            $valores->comentario=$resultadoObject->comentario;

                                            $valores->save();
                                        }
                                    }
                                    $cantidad++;
                                    $analisis->analisis_estado=3;
                                    $analisis->save();

                                    $auditoria->registrarAuditoria('Se ha actualizado correctamente un analisis desde el exterior, orden examen: '.$orden->orden_id,'0','');
                                }
                            }
                        }
                    }
                }
                else
                    $noenviadas++;
            }

            DB::commit();
            return redirect('/analisisLaboratorio')->with("success","Se han actualizado $cantidad/$total orden(es) desde módulo externo, $noenviadas no enviadas al laboratorio");
        /* }
        catch(\Exception $ex){
            DB::rollBack();
            return redirect('/analisisLaboratorio')->with("error2","Hubo un error al actualizar ordendes, error ".$ex);
        } */
    }

    public function imprimiranalisis($id)
    { 
        //try{
            
            $orden = Orden_Examen::findOrFail($id);
            $analisis = Analisis_Laboratorio::findOrFail( $orden->analisis->analisis_laboratorio_id);

            $empresa =  Empresa::empresa()->first();
            $view =  \View::make('admin.formatosPDF.ordendeexamen', ['analisis'=>$analisis,'orden'=>$orden,'empresa'=>$empresa]);
            $ruta = public_path().'/ordenesExamenes/'.$empresa->empresa_ruc.'/'.DateTime::createFromFormat('Y-m-d', $orden->analisis->analisis_fecha)->format('d-m-Y');
            if (!is_dir($ruta)) {
                mkdir($ruta, 0777, true);
            }
            $nombreArchivo = 'AL-'.$orden->analisis->analisis_numero;
            PDF::loadHTML($view)->save($ruta.'/'.$nombreArchivo.'.pdf');
            return PDF::loadHTML($view)->save($ruta.'/'.$nombreArchivo.'.pdf')->stream('reporte');
       //}
        //catch(\Exception $ex){      
        //    return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        //}
    }
    public function enviar($id)
    { 
        try{
            $analisis = Analisis_Laboratorio::findOrFail($id);
            $response_pdf_text=$this->showExamenResults($analisis->orden->orden_id_referencia);
            
            /* guardar temporalmente la orden de examenes*/
            $data = $response_pdf_text;
            $destination = '../public/PDF/temp_examenes'.$id.'.pdf';
            $file = fopen($destination, "w+");
            fputs($file, $data);
            fclose($file);


            /*$tipo= Orden_Examen::Ordenanalisis($analisis->orden_id)->select('tipo_examen.tipo_id','tipo_examen.tipo_nombre')->distinct()->get();
            $empresa =  Empresa::empresa()->first();
            $view =  \View::make('admin.formatosPDF.resultadoExamen', ['analisis'=>$analisis,'empresa'=>$empresa]);
            $ruta = public_path().'/rusultadosExamenes/'.$empresa->empresa_ruc.'/'.DateTime::createFromFormat('Y-m-d', $analisis->analisis_fecha)->format('d-m-Y');
            if (!is_dir($ruta)) {
                mkdir($ruta, 0777, true);
            }
            $nombreArchivo = 'RE-'.$analisis->analisis_numero;
            PDF::loadHTML($view)->save($ruta.'/'.$nombreArchivo.'.pdf');
            */

            //$analisis->analisis_estado='4';
            //$analisis->save();
            
            //$email=Email_Empresa::Email()->first();

            require base_path("vendor/autoload.php");
            $mail = new PHPMailer(true);
            $mail->isSMTP(); // tell to use smtp
            $mail->CharSet = 'utf-8'; // set charset to utf8
            $mail->Host =  'mail.pagupasoft.com';  //$mail->Host = trim($email->email_servidor);
            $mail->SMTPAuth = true;
            $mail->SMTPSecure = 'tls';//$mail->SMTPSecure = false;
            $mail->SMTPAutoTLS = false;
            $mail->Port = trim('587'); // most likely something different for you. This is the mailtrap.io port i use for testing. 

            $mail->Username = trim('neopagupa@pagupasoft.com');  //$mail->Username = trim($email->email_usuario);
            $mail->Password = trim('PagupaServer07@');//$mail->Password = trim($email->email_pass);

            $mail->setFrom(trim('neopagupa@pagupasoft.com'), 'NEOPAGUPA SISTEMA CONTABLE');
            $mail->Subject = 'NEOPAGUPA-Sistema Contable';
            $mail->MsgHTML('RESULTADOS DE ANALISIS DEL PACIENTE ');
            $mail->addAddress(trim($analisis->orden->expediente->ordenatencion->paciente->paciente_email), 'SDS');
            //$mail->AddAttachment($ruta.'/'.$nombreArchivo.'.pdf', 'ResultadosAnalisis.pdf');
            $mail->AddAttachment($destination, 'Resultado de examenes');
            $mail->SMTPOptions= array(
                'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
                )
            );
            $mail->send();

            /* borrar el archivo pdf luego de enviarlo */
            unlink($destination);

            return redirect('analisisLaboratorio')->with('success', 'Se envio la orden de examen exitosamente');
        }
        catch(\Exception $ex){      
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
   
   
    public function resultados($id)
    {
        try{
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $analisis = Analisis_Laboratorio::findOrFail($id);

            $response=$this->showExamenResults($analisis->orden->orden_id_referencia);
            header("content-type: application/pdf");
            echo $response;


            //return view('admin.laboratorio.ordenesExamen.atender',['analisis'=>$analisis,'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        }catch(\Exception $ex){
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }

    /* get PDF exam results from Oreon API */
    public function showExamenResults($orden_id_referencia)
    {
        $headers = array();
        $headers[] = 'Accept: application/pdf';
        $headers[] ='Authorization: Bearer SUHeKxqVgrz8Pu97U3nQJEPTHGO43Ym4ip7FQa6D1DldHic3Deij4r09R9b7';
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://demo.orion-labs.com/api/v1/ordenes/'.$orden_id_referencia.'/resultados/pdf');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }

        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        
        if($httpcode==200){
            return $result;
        }
        else
            return null;
    }


    /* get all pending orders from ORION API  */
    private function getOrdenes($orden_numero_externo = null, $fecha1=null, $fecha2=null, $identificacion=null, $estado=null)
    {
        $filtro='';

        if(isset($orden_numero_externo)) $filtro='&filtrar[numero_orden]='.$orden_numero_externo;

        if(isset($fecha1)){
            if(strlen($filtro)>0)
                $filtro.='&filtrar[fecha_orden_desde]='.$fecha1;
            else
                $filtro='filtrar[fecha_orden_desde]='.$fecha1;
        }

        if(isset($fecha2)){
            if(strlen($filtro)>0)
                $filtro.='&filtrar[fecha_orden_hasta]='.$fecha2;
            else
                $filtro='filtrar[fecha_orden_hasta]='.$fecha2;
        }

        if(isset($identificacion)){
            if(strlen($filtro)>0)
                $filtro.='&filtrar[paciente.numero_identificacion]='.$identificacion;
            else
                $filtro='filtrar[paciente.numero_identificacion]='.$identificacion;
        }

        if(isset($estado)){
            if(strlen($filtro)>0)
                $filtro.='&filtrar[examenes.estado]='.$estado;
            else
                $filtro='filtrar[examenes.estado]='.$estado;
        }

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://demo.orion-labs.com/api/v1/ordenes?incluir=paciente,examenes.resultados'.$filtro);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');

        $headers = array();
        $headers[] = 'Accept: application/json';
        $headers[] = 'Content-Type: application/json';
        $headers[] ='Authorization: Bearer SUHeKxqVgrz8Pu97U3nQJEPTHGO43Ym4ip7FQa6D1DldHic3Deij4r09R9b7';


        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);


        return $result;
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
    public function consultar(Request $request){
       
        
    }
    public function seleccionar(Request $request){
        try{
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $analisis = Analisis_Laboratorio::findOrFail($request->get('idanalisis'));
            $analisisdetalle = Examen::BuscarProductoslaboratorio($request->get('radioempleado'))->get();
            $count=1;
            $count2=1;
            
            foreach($analisisdetalle as $analisisdetalles){
                $datos[$count]['item1']=$analisisdetalles->detalle_nombre;
                foreach($analisis->detalles as $analisisdeta){
                    foreach($analisisdeta->detalles as $analisisde){
                        if($analisisdetalles->detalle_nombre==$analisisde->detalle_descripcion){
                            $datos[$count]['item3']=$analisisde->detalle_valor;
                        }
                    }
                }
                $datos[$count]['item2']=$analisisdetalles->detalle_id;
                $datos[$count]['abreviatura']=$analisisdetalles->detalle_abreviatura;
                $datos[$count]['Medida']=$analisisdetalles->detalle_medida;
                $datos[$count]['Min']=$analisisdetalles->detalle_minimo;
                $datos[$count]['Max']=$analisisdetalles->detalle_maximo;
                $analisisvalores =Valor_Laboratorio::ValorLaboratorioexamen($analisisdetalles->detalle_id)->get();
                if (count($analisisvalores)<=0) {
                        $datos2[$count2]['id']=0;
                        $datos2[$count2]['text']='1';
                        $datos2[$count2]['nombre']=0;
                        $datos2[$count2]['valor']=0;
                        $datos2[$count2]['detalle']=$analisisdetalles->detalle_id;   
                    $count2++;
                }
                else{
                   
                    foreach ($analisisvalores as $valores){
                        $datos2[$count2]['id']=$valores->valor_id;
                        $datos2[$count2]['text']='0';
                        $datos2[$count2]['nombre']=$valores->valor_nombre;
                        $datos2[$count2]['valor']=0;
                        $datos2[$count2]['detalle']=$analisisdetalles->detalle_id;
                       
                        $count2++;
                    }  
                }
                $count++;
            }
          
            return view('admin.laboratorio.ordenesExamen.atender',['idchek'=>$request->get('radioempleado'),'datos2'=>$datos2,'datos'=>$datos,'analisis'=>$analisis,'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        }catch(\Exception $ex){
            return redirect('analisisLaboratorio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function guardar(Request $request){
        $dvalores=$request->get('valores');
        $dunidad=$request->get('unidad');
        if (count($dvalores)>0) {
            $idanalisis=$request->get('radioempleado');
            $iddetalle=$request->get('detalle_Valorid');
            $danalisis=$request->get('detalle_Valor');
            $analisis=Analisis_Laboratorio::findOrFail($request->get('idanalisis'));      
            $detalle=Detalle_Analisis::detalleid($request->get('idanalisis'), $request->get('radioempleado'))->get();
            foreach ($detalle as $detalles) {
                if ($detalles->producto_id==$idanalisis) {
                    for ($i = 0; $i < count($danalisis); ++$i) {
                        $detalleana=Detalles_Analisis_Valores::analisis( $detalles->detalle_id,$danalisis[$i])->first();   
                        if(isset($detalleana)){
                            $detallevalores=Detalles_Analisis_Valores::findOrFail($detalleana->detalle_valores_id);
                            $detallevalores->detalle_descripcion=$danalisis[$i];
                            $detallevalores->detalle_valor=$dvalores[$i];
                            $detallevalores->detalle_unidad=$dunidad[$i];
                            $detallevalores->save();

                            $aux=Detalle_Analisis::findOrFail($detalles->detalle_id);
                            $aux->detalle_estado='2';
                            $aux->save();
                            $referencia=Detalles_Analisis_Referenciales::Referencialdetalle($detallevalores->detalle_valores_id)->get();  
                            foreach($referencia as $referencial){
                                $aux=$referencial;
                                $referencial->delete();
                                $auditoria = new generalController();
                                $auditoria->registrarAuditoria('Eliminar de valor referencial de Laboratorio con nombre->'.$referencial->detalle_Columna1,'0','');
                            } 
                            $idreferencia=Valor_Referencial::Referencialdetalle($iddetalle[$i])->get();       
                            if (count($idreferencia)>0) {
                                foreach ($idreferencia as $detallesref) {
                                    $detallereferencia=new Detalles_Analisis_Referenciales();
                                    $detallereferencia->detalle_Columna1=$detallesref->valor_Columna1;
                                    $detallereferencia->detalle_Columna2=$detallesref->valor_Columna2;
                                    $detallereferencia->detalle_estado='1';
                                    $detallereferencia->detalle_valores_id=$detallevalores->detalle_valores_id;
                                    $detallereferencia->save();
                                }
                            }
                        }else{
                            $detallevalores=new Detalles_Analisis_Valores();
                            $detallevalores->detalle_descripcion=$danalisis[$i];
                            $detallevalores->detalle_valor=$dvalores[$i];
                            $detallevalores->detalle_unidad=$dunidad[$i];
                            $detallevalores->detalle_estado='1';
                            $detallevalores->detalle_id=$detalles->detalle_id;
                            $detallevalores->save();
                            $aux=Detalle_Analisis::findOrFail($detalles->detalle_id);
                            $aux->detalle_estado='2';
                            $aux->save();
                            $idreferencia=Valor_Referencial::Referencialdetalle($iddetalle[$i])->get();
                            if (count($idreferencia)>0) {
                                foreach ($idreferencia as $detallesref) {
                                    $detallereferencia=new Detalles_Analisis_Referenciales();
                                    $detallereferencia->detalle_Columna1=$detallesref->valor_Columna1;
                                    $detallereferencia->detalle_Columna2=$detallesref->valor_Columna2;
                                    $detallereferencia->detalle_estado='1';
                                    $detallereferencia->detalle_valores_id=$detallevalores->detalle_valores_id;
                                    $detallereferencia->save();
                                }
                            }
                        }
                    }
                }
            }
            $analisis->analisis_estado='3';
            $analisis->user_id=Auth::user()->user_id;
            $analisis->save();
            return redirect('analisisLaboratorio/'.$request->get('idanalisis').'/resultados')->with('success', 'Datos guardados exitosamente');
        }
        else{
            return redirect('analisisLaboratorio/'.$request->get('idanalisis').'/resultados')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function store(Request $request)
    {
        if (isset($_POST['guardar'])){
            return $this->guardar($request);
        }
        if (isset($_POST['radioempleado'])){
            return $this->seleccionar($request);
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
