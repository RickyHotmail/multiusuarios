<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Empleado;
use App\Models\Cliente;
use App\Http\Controllers\Controller;
use App\Models\Detalle_Mantenimiento;
use App\Models\Detalle_Orden_Mantenimiento;
use App\Models\Orden_Mantenimiento;
use App\Models\Parametrizar_Orden;
use App\Models\Parametrizar_Prioridad;
use App\Models\Responsable_Mantenimiento;
use App\Models\Responsable_User_Mantenimiento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ordenMantenimientoController extends Controller{
    public function index(Request $request){
        if($request->orden_estado==5)
            $ordenes=Orden_Mantenimiento::ordenes()->get();
        else
            $ordenes=Orden_Mantenimiento::ordenesFilterByEstado($request->orden_estado)->get();

        $data=[
            'result'=>'OK',
            'cantidad' => count($ordenes),
            'data' => $ordenes
        ];

        return response()->json($data, 200);
    }

    public function listaOrdenes(){
        $ordenes=Orden_Mantenimiento::ordenes()->get();
        $clientes=Cliente::clientes()->get();
        $tecnicos=Responsable_User_Mantenimiento::tecnicos()->get();
        $parametrizacion=Parametrizar_Orden::parametrizacionOrdenes()->get();
        $prioridad=Parametrizar_Prioridad::prioridades()->get();

        foreach($ordenes as $orden){
            $orden->detalles;
            $orden->detallesOrden;
            $orden->responsables;
            $orden->tipo;

            foreach($orden->responsables as $resp){
                $resp->empleado;
            }
        }

        $data=[
            'result'=>'OK',
            'cantidad' => count($ordenes),
            'ordenes' => $ordenes,
            'clientes'=>$clientes,
            'tecnicos'=>$tecnicos,
            'parametrizacion'=>$parametrizacion,
            'escala'=>$prioridad
        ];

        //return $ordenes;

        return view('admin.mantenimiento.index', $data);
    }

    public function listaTecnicos(){
        $tecnicos=Responsable_User_Mantenimiento::tecnicos()->get();
        $usuarios=User::usuarios()->get();
        $empleados=Empleado::empleados()->get();
        
        $data=[
            'usuarios'=>$usuarios,
            'empleados'=>$empleados,
            'tecnicos'=>$tecnicos
        ];

        return view('admin.mantenimiento.tecnicos', $data);
    }

    public function agregarTecnicoMantenimiento(Request $request)
    {
        try{              
            DB::beginTransaction();
            $responsable_user = new Responsable_User_Mantenimiento();

            if($request->get('empleado_id')==null || $request->get('empleado_id')==""){
                $responsable_user->responsable_user_nombre=$request->get('nombre');
                $responsable_user->responsable_user_apellido=$request->get('apellido');
                $responsable_user->responsable_user_cedula=$request->get('cedula');
                $responsable_user->responsable_user_telefono=$request->get('telefono');
            }

            $responsable_user->empleado_id=$request->get('empleado_id');
            $responsable_user->user_id=$request->get('usuario_id');      
            $responsable_user->save();


            
            $auditoria = new generalController();
            $auditoria->registrarAuditoria('Registro de Tecnico Mantenimiento usuario: '.$responsable_user->user_id, $responsable_user->user_id,'');
            /*Fin de registro de auditoria */            
            DB::commit();
            return redirect('tecnicosMantenimiento')->with('success','Datos guardados exitosamente');
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('tecnicosMantenimiento')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }

    }

    public function comprobarStock($id){
        $orden=Orden_Mantenimiento::findOrFail($id);

        $orden->detallesOrden;
        
        foreach($orden->detallesOrden as $det){
            $det->producto;
        }
        

        $data=[
            'result'=>'OK',
            'orden' => $orden
        ];

        //return $orden;

        return view('admin.mantenimiento.stock', $data);
    }

    public function getOrden($id){
        try{
            $orden=Orden_Mantenimiento::findOrFail($id);

            $orden->detalles;
            $orden->detallesOrden;
            $orden->responsables;

            $orden->cliente;

            foreach($orden->responsables as $resp){
                $resp->tecnico->empleado;
            }

            foreach($orden->detallesOrden as $det){
                $det->producto;
            }

            $data=[
                'result'=>'OK',
                'data' => $orden
            ];


            return response()->json($data, 200);
        }
        catch(\Exception $e){
            $data=[
                'result'=>'FAIL',
                'data' => []
            ];
            return response()->json($data, 200);
        }
    }

    public function getOrdenWeb($id){
        $tecnicos=Responsable_User_Mantenimiento::tecnicos()->get();
        $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=', Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
        $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
        $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();

        try{
            $orden=Orden_Mantenimiento::findOrFail($id);

            //$orden->detalles;
            $orden->responsables;

            $orden->cliente;

            foreach($orden->responsables as $resp){
                $resp->tecnico;
            }

            foreach($orden->detallesOrden as $det){
                $det->producto;
            }

            $data=[
                'result'=>'OK',
                'orden' => $orden,
                'tecnicos'=>$tecnicos,
                'gruposPermiso'=>$gruposPermiso,
                'tipoPermiso'=>$tipoPermiso,
                'permisosAdmin'=>$permisosAdmin
            ];

            //return $orden;
            return view('admin.mantenimiento.ver', $data);
        }
        catch(\Exception $e){
            $data=[
                'result'=>'OK',
                'data' => []
            ];
            return redirect('listaMantenimiento')->with('error', 'hubo un error al cargar la orden '.$e->getMessage());
        }
    }

    public function create(){
        
    }
    public function store(Request $request){
        //return $request;
        try{
            DB::beginTransaction();
            $ordenes=Orden_Mantenimiento::ordenes()->get();

            $nuevaOrden=new Orden_Mantenimiento();
            $nuevaOrden->orden_numero=count($ordenes);
            $nuevaOrden->orden_serie="ASDDDD";
            $nuevaOrden->orden_secuencial=43;
            
            $nuevaOrden->orden_fecha_inicio=$request->get('fecha_inicio');
            $nuevaOrden->orden_prioridad=$request->prioridad;
            $nuevaOrden->orden_lugar=$request->lugar;
            
            $nuevaOrden->orden_descripcion="";
            $nuevaOrden->orden_logistica=$request->logistica;
            $nuevaOrden->orden_observacion="";
            $nuevaOrden->orden_recibido_por="";
            $nuevaOrden->orden_estado=1;
            $nuevaOrden->orden_resultado=1;

            $nuevaOrden->tipo_id=$request->tipo_id;
            $nuevaOrden->user_id=Auth::user()->user_id;
            $nuevaOrden->cliente_id=$request->cliente;
            $nuevaOrden->sucursal_id=1;
            $nuevaOrden->save();

            $listaANotificar[]="";

            if($request->responsable!=null){
                for ($i = 0; $i < count($request->responsable); ++$i){
                    $responsable_usuario=Responsable_User_Mantenimiento::searchByEmpleado($request->responsable[$i])->first();

                    if($responsable_usuario==null)
                        $responsable_usuario=Responsable_User_Mantenimiento::findOrFail($request->responsable[$i]);
                    
                    $responsable=new Responsable_Mantenimiento();
                    $responsable->responsable_user_id=$responsable_usuario->responsable_user_id;
                    $responsable->orden_id=$nuevaOrden->orden_id;
                    $responsable->responsable_estado=1;
                    $responsable->save();

                    if($responsable_usuario->user->user_fcm_token!="") $listaANotificar[]=$responsable_usuario->user->user_fcm_token;
                }
            }

            if($request->detalle!=null){
                for ($i = 0; $i < count($request->detalle); ++$i){
                    $detalle=new Detalle_Mantenimiento();
                    $detalle->detalle_fecha_inicio=date('d-m-Y');
                    $detalle->detalle_descripcion=$request->detalle[$i];
                    $detalle->orden_id=$nuevaOrden->orden_id;
                    $detalle->detalle_estado=1;
                    $detalle->save();
                }
            }

            $stock=true;
            
            if($request->id_detalle_orden!=null){
                for ($i = 0; $i < count($request->id_detalle_orden); ++$i){
                    $detOrden=new Detalle_Orden_Mantenimiento();
                    $detOrden->producto_id=$request->id_detalle_orden[$i];
                    $detOrden->detalle_orden_cantidad=$request->cantidad_detalle_orden[$i];
                    $detOrden->orden_id=$nuevaOrden->orden_id;
                    $detOrden->detalle_orden_estado=1;
                    $detOrden->save();

                    if($request->stock_detalle_orden[$i] <  $request->cantidad_detalle_orden[$i])  $stock=false;
                }
            }

            if($stock){
                $nuevaOrden->orden_estado=2;
                $nuevaOrden->save();
            }

            $auditoria = new generalController();
            $auditoria->registrarAuditoria('Registro de nueva orden numero '.$nuevaOrden->numero, $nuevaOrden->orden_id, '');

            DB::commit();

            $this->sendNotification("Orden Creada", "La orden °".$nuevaOrden->orden_id." ha sido creada", $listaANotificar, "ORDER DETAIL", $nuevaOrden->orden_id, "");

            if(isset($request->web))
                return redirect('listaMantenimiento')->with('success','Datos guardados exitosamente');
            else
                return response()->json(["result"=>"OK", "mensaje"=>"guardado correctamente ", "notificados"=>$listaANotificar], 200);
        }
        catch(\Exception $e){
            DB::rollBack();

            if(isset($request->web))
                return redirect('listaMantenimiento')->with('error','No se pudo guardar la Orden '.$e->getMessage());
            else
                return response()->json(["result"=>"FAIL", "mensaje"=>"no se pudo guardar".$e->getMessage()], 202);
        }
    }

    public function actualizarEstadoOrden(Request $request){
        try{
            DB::beginTransaction();

            $orden=Orden_Mantenimiento::findOrFail($request->orden_id);
            $orden->orden_estado=2;
            $orden->update();

            $auditoria = new generalController();
            $auditoria->registrarAuditoria('Se actualizó la orden '.$orden->numero.' a estado 2', $orden->orden_id, $orden->orden_id);

            DB::commit();
            return redirect('listaMantenimiento')->with('success', 'La Orden actualizada correctamente');;
        }
        catch(\Exception $ex){
            DB::rollBack();
            return redirect('listaMantenimiento')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }

    public function login(Request $request){
        try{
            $usuario=User::findByEmail($request->get('idUsername'))->get();

            $userdata = array(
                'user_username' => $usuario[0]->user_username,
                'password' => $request->get('idPassword'),
                'user_estado' => 1
            );
            if (Auth::attempt($userdata, true)) {
                $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=', Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
                $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
                $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();

                $request->session()->regenerate();

                $user=User::findOrFail(Auth::user()->user_id);
                Auth::login($user); 

                $auditoria = new generalController();
                $auditoria->registrarAuditoria('Inicio de sesion usuario->'.$request->get('idUsername').' Con Id ->'.$user->user_id, $user->user_id, '');

                $data=["result"=>"OK",
                    "user_id"=> $user->user_id,
                    "user_username"=>$user->user_username,
                    "user_usermail"=>$user->user_correo,
                    "rol"=>$user->roles[0]->rol_nombre,
                    "grupos"=>$gruposPermiso,
                    "permisosAdmin"=>$permisosAdmin
                ];

                return response()->json($data, 200);
            }
            return response()->json(["result"=>"FAIL", "mensaje"=>"credenciales Incorrectas "], 202);
        }
        catch(\Exception $ex){      
            return response()->json(["result"=>"FAIL", "mensaje"=>"credenciales Incorrectas "], 202);
        }
    }

    public function actualizarOrden(Request $request){
        try{
            DB::beginTransaction();
            $orden=Orden_Mantenimiento::findOrFail($request->orden_id);
            $orden->orden_observacion="".$request->observacion;
            $orden->orden_estatus=$request->estatus;
            $orden->orden_resultado=1;

            if($request->resultado=="Operativo") $orden->orden_resultado=2;

            if($request->estatus==2){
                $orden->orden_finalizacion=date('Y-m-d', time());  
                $orden->orden_estado=4;
            }
            
            if($request->imagen){
                $orden->orden_recibido_por=$request->recibido_por;
                $url = $this->subir_foto($request->imagen, $orden, $request->recibido_por);
            }

            $orden->save();
            $auditoria = new generalController();
            $auditoria->registrarAuditoria('Se actualizó la orden '.$orden->numero, $orden->orden_id, $orden->orden_id);


            DB::commit();
            //return redirect('listaMantenimiento')->with('success', 'La Orden actualizada correctamente');

            
            $usuarios[]="";

            foreach($orden->responsables as $resp){                
                $usuarios[]=$resp->tecnico->user->user_fcm_token;
            }
            

            $this->sendNotification("Orden Creada", "La orden °".$orden->orden_id." ha sido actualizada", $usuarios, "ORDER DETAIL", $orden->orden_id, "");


            return response()->json(["result"=>"OK", "mensaje"=>"Actualizada Correctamente", "data"=>$orden], 201);
        }
        catch(\Exception $ex){
            DB::rollBack();
            return response()->json(["result"=>"FAIL", "mensaje"=>"No se pudo actualizar la orden de Mantenimiento"], 410);
        }
    }

    public function anularOrden(Request $request){
        try{
            DB::beginTransaction();

            $orden=Orden_Mantenimiento::findOrFail($request->orden_id);
            $orden->orden_estado=0;
            $orden->save();
            $auditoria = new generalController();
            $auditoria->registrarAuditoria('Se anuló la orden '.$orden->numero, $orden->orden_id, $orden->orden_id);

            DB::commit();
            //$this->sendNotification("Orden Anulada", "La orden °".$orden->orden_id." ha sido creada");

            $usuarios="";

            foreach($orden->responsables as $responsable){                
                $usuarios[]=$responsable->empleado->empleado_nombre;
            }

            //$this->sendNotification("Orden Anulada", "La Orden N° ".$orden->orden_id." ha sido anulada",$usuarios);


            return response()->json(["result"=>"OK", "mensaje"=>"orden Anulada Correctamente"], 201);
        }
        catch(\Exception $ex){
            DB::rollBack();
            return response()->json(["result"=>"FAIL", "mensaje"=>"No se pudo actualizar la orden de Mantenimiento"], 410);
        }
    }

    public function guardarTokenUsuario(Request $request){
        try{
            DB::beginTransaction();

            $usuario=User::findOrFail($request->user_id);
            $usuario->user_fcm_token=$request->user_fcm_token;
            $usuario->save();

            $auditoria = new generalController();
            $auditoria->registrarAuditoria('Se agregó el token fcm al usuairio '.$usuario->user_username, $usuario->user_id, '');


            DB::commit();
            return response()->json(["result"=>"OK", "mensaje"=>"Token Actualizada Correctamente"], 201);
        }
        catch(\Exception $ex){
            DB::rollBack();
            return response()->json(["result"=>"FAIL", "mensaje"=>"No se pudo actualizar la orden de Mantenimiento ".$ex], 410);
        }
    }

    public function enviar(){
        //$listaUsuarios=array();
        $listaUsuarios[]="eUxN1EA3SUOwULL-ha4-mr:APA91bEgZFIqBKbQtznG9mNJCzDqXn0bLIPOg5lJztPz1El5p_5_udkfMsXw_jwYBBZxsjfc9rmMeiMPQMhKpL-Gnc2DY96UoNmSgL2eiau8WW6K05oDayIDhhPZuEh67QgBXRgegaHC";
        $listaUsuarios[]="c_FHf2aAQtyviKivjrrXXD:APA91bGy-oZlD-WlTWyexAzqybvkY60Z92LPqkgyA9pXyxoME29JD1faXZaD95jyG_0fXlrkB5bMsFJze3ouu4b0TUhXi6HNW0vMkbIATLbpXcdwBifvgHNOiX0NBrgCW-g6BGFV3j_6";
        $result=$this->sendNotification("Uso del Servicio", "Esta función es para comprobar el estado en Línea del Servicio", $listaUsuarios, "", "", "");

        return 'notificacion enviada '.$result;
    }

    private function sendNotification($title, $message, $listaUsuarios, $accion, $codigo=0, $img=null){
        //echo 'users '.json_encode($listaUsuarios).'<br><br>';
        $msg = urlencode($message);
        $data = array(
            'title'=>$title,
            'sound' => "default",
            'msg'=>$msg,
            //'data'=>$datapayload,
            'body'=>$message,
            'color' => "#79bc64"
        );
        if($img){
            $data["image"] = $img;
            $data["style"] = "picture";
            $data["picture"] = $img;
        }

        if($codigo){
            $data["codigo"]=$codigo;
            $data["accion"]=$accion;
        }

        $fields = array(
            'registration_ids'=>$listaUsuarios,
            //'to'=>json_encode($listaUsuarios),
            'notification'=>$data,
            'data'=>$data,
            "priority" => "high",
        );
        $headers = array(
            'Authorization: key=AAAAwCXuQ98:APA91bFfYo6_dJL3N36MRyILv-MP8ZSJbhnFiiD9epx2vPCpJ86-9VIytaKBiFgMFYbXe_fdyZwOv7rO6WIdGZBWRbkC1OmYFJbrmXK3d9H4LVRLwWmzK-82FNBVJgFkuan0zSRn0jFO',
            'Content-Type: application/json'
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        $result = curl_exec($ch);
        curl_close( $ch );
        return $result;
    }

    private function subir_foto($imagen, $orden, $recibido){
        $imageData = base64_decode($imagen); // <-- **Change is here for variable name only**
        $photo = imagecreatefromstring($imageData);


        $ruta = 'DocumentosMantenimiento/1/'.$orden->orden_id;
        $extension = "jpg";

        if ($imagen) {
            if (!is_dir(public_path().'/'.$ruta))  mkdir(public_path().'/'.$ruta, 0777, true);

            $name='firma_'.$recibido.'.'.$extension;
            //$path=$photo->move(public_path().'/'.$ruta, $name);
            imagejpeg($photo, public_path().'/'.$ruta.'/'.$name);
        
            return $ruta.'/'.$name;
        }
        else
            return null;
    }


    public function agregarResponsable(Request $request){
        try{
            DB::beginTransaction();

            $orden=Orden_Mantenimiento::findOrFail($request->orden_id);

            $responsable=new Responsable_Mantenimiento();
            $responsable->empleado_id=$request->empleado_id;
            $responsable->orden_id=$orden->orden_id;
            $responsable->responsable_estado=1;
            $responsable->save();

            $auditoria = new generalController();
            $auditoria->registrarAuditoria('Se agregó un responsable a la orden '.$orden->numero, $orden->orden_id, 'responsable con id '.$responsable->responsable_id.' empleado: '.$request->empleado_id);

            DB::commit();
            return response()->json(["result"=>"OK", "mensaje"=>"agregado correctamente"], 201);
        }
        catch(\Exception $e){
            DB::rollBack();
            return response()->json(["result"=>"FAIL", "mensaje"=>"no se pudo agregar al Responsable".$e->getMessage(),], 410);
        }
    }

    public function quitarResponsable(Request $request){
        try{
            DB::beginTransaction();

            $orden=Orden_Mantenimiento::findOrFail($request->orden_id);
            $responsable=Responsable_Mantenimiento::findOrFail($request->responsable_id);
            $responsable->delete();

            $auditoria = new generalController();
            $auditoria->registrarAuditoria('Se eliminó un responsable a la orden '.$orden->numero, $orden->orden_id, 'id :'.$request->responsable_id);

            DB::commit();
            return response()->json(["result"=>"OK", "mensaje"=>"responable eliminado correctamente"], 201);
        }
        catch(\Exception $e){
            DB::rollBack();
            return response()->json(["result"=>"FAIL", "mensaje"=>"no se pudo eliminar al Responsable".$e->getMessage(),], 410);
        }
    }

    public function agregarDetalle(Request $request){
        try{
            DB::beginTransaction();

            $orden=Orden_Mantenimiento::findOrFail($request->orden_id);

            $detalle=new Detalle_Mantenimiento();
            $detalle->detalle_fecha_inicio=date('d-m-Y');
            $detalle->detalle_descripcion=$request->descripcion;
            $detalle->orden_id=$request->orden_id;
            $detalle->detalle_estado=1;
            $detalle->save();

            $auditoria = new generalController();
            $auditoria->registrarAuditoria('Se agregó un detalle a la orden '.$orden->numero, $orden->orden_id, '');

            DB::commit();
            return response()->json(["result"=>"OK", "mensaje"=>"agregado correctamente"], 201);
        }
        catch(\Exception $e){
            DB::rollBack();
            return response()->json(["result"=>"FAIL", "mensaje"=>"no se pudo agregar al Responsable".$e->getMessage(),], 410);
        }
    }

    public function quitarDetalle(Request $request){
        try{
            DB::beginTransaction();

            $orden=Orden_Mantenimiento::findOrFail($request->orden_id);
            $detalle=Detalle_Mantenimiento::findOrFail($request->detalle_id);
            $detalle->delete();

            $auditoria = new generalController();
            $auditoria->registrarAuditoria('Se eliminó un detalle a la orden '.$orden->numero, $orden->orden_id, 'id :'.$request->detalle_id);

            DB::commit();
            return response()->json(["result"=>"OK", "mensaje"=>"detalle eliminado correctamente"], 201);
        }
        catch(\Exception $e){
            DB::rollBack();
            return response()->json(["result"=>"FAIL", "mensaje"=>"no se pudo eliminar detalle".$e->getMessage(),], 410);
        }
    }

    public function agregarDetalleMantenimiento(Request $request){
        try{
            DB::beginTransaction();

            $orden=Orden_Mantenimiento::findOrFail($request->orden_id);

            $detalle=new Detalle_Mantenimiento();
            $detalle->detalle_cantidad=$request->cantidad;
            $detalle->detalle_descripcion=$request->descripcion;
            $detalle->orden_id=$request->orden_id;
            $detalle->detalle_estado=1;
            $detalle->save();

            $auditoria = new generalController();
            $auditoria->registrarAuditoria('Se agregó un detalle a la orden '.$orden->numero, $orden->orden_id, '');

            DB::commit();
            return response()->json(["result"=>"OK", "mensaje"=>"agregado correctamente"], 201);
        }
        catch(\Exception $e){
            DB::rollBack();
            return response()->json(["result"=>"FAIL", "mensaje"=>"no se pudo agregar al Responsable".$e->getMessage(),], 410);
        }
    }

    public function quitarDetalleMantenimiento(Request $request){
        try{
            DB::beginTransaction();

            $orden=Orden_Mantenimiento::findOrFail($request->orden_id);
            $detalle=Detalle_Mantenimiento::findOrFail($request->detalle_id);
            $detalle->delete();

            $auditoria = new generalController();
            $auditoria->registrarAuditoria('Se eliminó un detalle a la orden '.$orden->numero, $orden->orden_id, 'id :'.$request->detalle_id);

            DB::commit();
            return response()->json(["result"=>"OK", "mensaje"=>"detalle eliminado correctamente"], 201);
        }
        catch(\Exception $e){
            DB::rollBack();
            return response()->json(["result"=>"FAIL", "mensaje"=>"no se pudo eliminar detalle".$e->getMessage(),], 410);
        }
    }


    public function show(){
        
    }

    public function edit(){
        
    }

    public function update(){
        
    }

    public function editar(){

    }

    public function getProductos(){
        //$productos
    }
}
