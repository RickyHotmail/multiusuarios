<?php

namespace App\Http\Controllers;

use App\Models\Rubro;
use App\Http\Controllers\Controller;
use App\Models\Categoria_Rol;
use App\Models\Cliente;
use App\Models\Empresa;
use App\Models\Punto_Emision;
use App\Models\Rol_Movimiento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use SebastianBergmann\Environment\Console;

class rubroController extends Controller
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
            $rubros = Rubro::rubros()->get();
            $categorias = Categoria_Rol::Categorias()->get();    
            return view('admin.recursosHumanos.rubro.index',['categorias'=>$categorias,'rubros'=>$rubros, 'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
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
    public function excelRubro(){
        try{
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            return view('admin.recursosHumanos.rubro.cargarExcel',['PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        }
        catch(\Exception $ex){      
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function CargarExcelRubro(Request $request){
        if (isset($_POST['cargar'])){
            return $this->cargarguardar($request);
        }
    }
    public function cargarguardar(Request $request){
        try {
            if ($request->file('excelClient')->isValid()) {
                $empresa = Empresa::empresa()->first();
                $name = $empresa->empresa_ruc. '.' .$request->file('excelClient')->getClientOriginalExtension();
                $path = $request->file('excelClient')->move(public_path().'\temp', $name);
                $array = Excel::toArray(new Rubro(), $path);
                DB::beginTransaction();    
                for ($i=1;$i < count($array[0]);$i++) {
                    $validar=trim($array[0][$i][0]);
                    $validacion=Rubro::existe($validar)->get();
                    if (count($validacion)==0) {
                        $rubro = new Rubro();
                        $rubro->rubro_nombre =  $validar;
                        $rubro->rubro_descripcion = $array[0][$i][1];
                        $rubro->rubro_tipo = $array[0][$i][2];
                        $rubro->rubro_estado = '1';
                        $rubro->empresa_id =  Auth::user()->empresa_id;                  
                        $rubro->save();
                        /*Inicio de registro de auditoria */
                        $auditoria = new generalController();
                        $auditoria->registrarAuditoria('Registro de Clientes -> '.mb_strtoupper($array[0][$i][1], 'UTF-8').'con codigo->'.mb_strtoupper($array[0][$i][0], 'UTF-8').'Mediante archivo excell', '0', '');
                    }
                }
                DB::commit();
                return redirect('rubro')->with('success','Datos guardados exitosamente');
            }
        }
        catch(\Exception $ex){ 
            DB::rollBack();     
            return redirect('rubro')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
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
            $rubro = new Rubro();
            $rubro->rubro_nombre = $request->get('rubro_nombre');
            $rubro->rubro_descripcion = $request->get('rubro_descripcion');
            $rubro->rubro_tipo = $request->get('rubro_tipo');
            $rubro->empresa_id = Auth::user()->empresa_id;
            $rubro->rubro_estado = 1;
            $rubro->save();
            /*Inicio de registro de auditoria */
            $auditoria = new generalController();
            $auditoria->registrarAuditoria('Registro de rubro -> '.$request->get('rubro_nombre').' de tipo -> '.$request->get('rubro_tipo'),'0','');
            /*Fin de registro de auditoria */
            DB::commit();
            return redirect('rubro')->with('success','Datos guardados exitosamente');
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('rubro')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
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
        try{
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $rubro = Rubro::rubro($id)->first();
            if($rubro){
                return view('admin.recursosHumanos.rubro.ver',['rubro'=>$rubro, 'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
            }else{
                return redirect('/denegado');
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
        try{
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $rubro = Rubro::rubro($id)->first();
            $categorias = Categoria_Rol::Categorias()->get();    
            if($rubro){
                return view('admin.recursosHumanos.rubro.editar', ['categorias'=>$categorias,'rubro'=>$rubro, 'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
            }else{
                return redirect('/denegado');
            }
        }
        catch(\Exception $ex){      
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
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
        try{            
            DB::beginTransaction();
            $rubro = Rubro::findOrFail($id);
            $rubro->rubro_nombre = $request->get('rubro_nombre');
            $rubro->rubro_descripcion = $request->get('rubro_descripcion');
            $rubro->rubro_tipo = $request->get('rubro_tipo');         
            if ($request->get('rubro_estado') == "on"){
                $rubro->rubro_estado = 1;
            }else{
                $rubro->rubro_estado = 0;
            }
            
            $rubro->save();       
            /*Inicio de registro de auditoria */
            $auditoria = new generalController();
            $auditoria->registrarAuditoria('Actualizacion de rubro -> '.$request->get('rubro_nombre').' de tipo -> '.$request->get('rubro_tipo'),'0','');
            /*Fin de registro de auditoria */   
            DB::commit();
            return redirect('rubro')->with('success','Datos actualizados exitosamente');
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('rubro')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try{
            DB::beginTransaction();
            $rubro = Rubro::findOrFail($id);
            $rubro->delete();
            /*Inicio de registro de auditoria */
            $auditoria = new generalController();
            $auditoria->registrarAuditoria('Eliminacion de rubro -> '.$rubro->rubro_nombre.' de tipo -> '.$rubro->rubro_tipo,'0','');
            /*Fin de registro de auditoria */
            DB::commit();
            return redirect('rubro')->with('success','Datos eliminados exitosamente');
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('rubro')->with('error','El registro no pudo ser borrado, tiene resgitros adjuntos.');
        }
    }

    public function delete($id)
    {
        try{
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $rubro = Rubro::rubro($id)->first();
            if($rubro){
                return view('admin.recursosHumanos.rubro.eliminar',['rubro'=>$rubro, 'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
            }else{
                return redirect('/denegado');
            }
        }
        catch(\Exception $ex){      
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function rubroById($id){
        return Rubro::Rubrotipo($id)->get();
    }
    public function cargaringreso(Request $request){
        $datos=null;
        $rubros=Rubro::Rubrotipo('2')->get();
        $mes=$request->get('mesrubro');
        $anio=$request->get('aniorubro');
        $movimientos=Rol_Movimiento::MovimientoEmpleado($request->get('buscar'),$mes,$anio,'2')->get();
        $count=0;
        foreach($rubros as $rubro)
        {
            $datos[$count]["idrol_mov"]=0;
            $datos[$count]["idrubro"]=$rubro->rubro_id;
            $datos[$count]["nombre"]=$rubro->rubro_nombre;
            $datos[$count]["descripcion"]=$rubro->rubro_descripcion;
            $datos[$count]["rubro_tipo"]=$rubro->rubro_tipo;
            $datos[$count]["valor"]=0;
            foreach($movimientos as $detalle){
                if($rubro->rubro_id==$detalle->rubro_id){
                    $datos[$count]["valor"]=$detalle->rol_movimiento_valor;
                    $datos[$count]["idrol_mov"]=$detalle->rol_movimiento_id;
                }
            }
            $count++;
        }

       
        return $datos;
    }
    public function cargaregreso(Request $request){
        $datos=null;
        $rubros=Rubro::Rubrotipo('1')->get();
        $mes=$request->get('mesrubro');
        $anio=$request->get('aniorubro');
 
        $movimientos=Rol_Movimiento::MovimientoEmpleado($request->get('buscar'),$mes,$anio,'1')->get();
        $count=0;
        foreach($rubros as $rubro)
        {
            $datos[$count]["idrol_mov"]=0;
            $datos[$count]["idrubro"]=$rubro->rubro_id;
            $datos[$count]["nombre"]=$rubro->rubro_nombre;
            $datos[$count]["descripcion"]=$rubro->rubro_descripcion;
            $datos[$count]["rubro_tipo"]=$rubro->rubro_tipo;
            $datos[$count]["valor"]=0;
            foreach($movimientos as $detalle){
                if($rubro->rubro_id==$detalle->rubro_id){
                    $datos[$count]["valor"]=$detalle->rol_movimiento_valor;
                    $datos[$count]["idrol_mov"]=$detalle->rol_movimiento_id;
                }
            }
            $count++;
        }

       
        return $datos;
    }
}
