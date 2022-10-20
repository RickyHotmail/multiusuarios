<?php

namespace App\Http\Controllers;

use App\Models\Cuenta;
use App\Models\Cuenta_Bancaria;
use App\Models\Detalle_Diario;
use App\Models\Diario;
use App\Models\Empresa;
use App\Models\Punto_Emision;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class cuentaController extends Controller
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
            $cuentas=Cuenta::nivel(0)->get();
            $arbol='';
            $cuentas = Cuenta::cuentas()->select('cuenta_id','cuenta_numero','cuenta_nombre','empresa_id','cuenta_nivel',DB::raw('(select count(*) from detalle_diario where cuenta.cuenta_id=detalle_diario.cuenta_id ) as detallesContable'))->get();
          /*  foreach($cuentas as $cuenta){
                $arbol=$arbol.'<li style="border: 1px solid rgba(0,0,0,.125);font-weight: bold;"><span class="caret">'.$cuenta->cuenta_numero.' - '.$cuenta->cuenta_nombre.'     ';
                $arbol=$arbol.'&nbsp;&nbsp;&nbsp;<a href="{{ url('."'".'cuenta/'.$cuenta->cuenta_id.'/edit'."'".') }}" class="btn btn-xs btn-primary"  data-toggle="tooltip" data-placement="top" title="Editar"><i class="fa fa-edit" aria-hidden="true"></i></a>
                <a href="{{ url("cuenta/'.$cuenta->cuenta_id.'/eliminar") }}" class="btn btn-xs btn-danger"  data-toggle="tooltip" data-placement="top" title="Eliminar"><i class="fa fa-trash" aria-hidden="true"></i></a>';
                if($cuenta->detallescontable == 0){
                    $arbol = $arbol.'<a href="{{ url("cuenta/'.$cuenta->cuenta_id.'/subcuenta") }}" class="btn btn-xs btn-secondary"  data-toggle="tooltip" data-placement="top" title="Añadir Cuenta"><i class="fa fa-tasks" aria-hidden="true"></i></a>';
                }
                $arbol=$arbol.'</span>';
                $arbol=$arbol.'<ul class="nested">';
                $arbol=$arbol.$this->cuentasHijas($cuenta->cuenta_id);
                $arbol=$arbol.'</ul>';
                $arbol=$arbol.'</li>';
            }*/
            $secuencial = 1;
            $secuencialAux=Cuenta::nivel(0)->max('cuenta_secuencial');
            $cuentas = Cuenta::cuentas()->select('cuenta_id','cuenta_numero','cuenta_nombre','empresa_id','cuenta_nivel',DB::raw('(select count(*) from detalle_diario where cuenta.cuenta_id=detalle_diario.cuenta_id ) as detallesContable'))->get();
            if($secuencialAux){$secuencial=$secuencialAux+1;}

           
            return view('admin.contabilidad.planCuenta.index',['secuencial'=>$secuencial,'PE'=>Punto_Emision::puntos()->get(),'cuentas'=>$cuentas, 'arbol'=>$arbol, 'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        }catch(\Exception $ex){
    
            return redirect('inicio')->with('error2','Ocurrio un error vuelva a intentarlo('.$ex->getMessage().')');
        }
    }
    public function subir()
    {
        try{ 
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
           
            return view('admin.contabilidad.planCuenta.cargar',['PE'=>Punto_Emision::puntos()->get(), 'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        }catch(\Exception $ex){
    
            return redirect('inicio')->with('error2','Ocurrio un error vuelva a intentarlo('.$ex->getMessage().')');
        }
    }
    public function cuentasHijas($cuentaPadres){
        try{
            $hijas=Cuenta::nivel($cuentaPadres)->get();
            $arbol='';
            foreach($hijas as $cuenta){
                $hijasAux=Cuenta::nivel($cuenta->cuenta_id)->count('*');
                $arbol=$arbol.'<ul class="nested">';
                $arbol=$arbol.'<li';
                if($hijasAux > 0){
                    $arbol=$arbol.' style="font-weight: bold;"';
                }
                $arbol=$arbol.'><span class="caret">'.$cuenta->cuenta_numero.' - '.$cuenta->cuenta_nombre.'   '.'</span>';
                $arbol=$arbol.'&nbsp;&nbsp;&nbsp;<a href="{{ url("cuenta/'.$cuenta->cuenta_id.'/edit") }}" class="btn btn-xs btn-primary"  data-toggle="tooltip" data-placement="top" title="Editar"><i class="fa fa-edit" aria-hidden="true"></i></a>
                <a href="{{ url("cuenta/'.$cuenta->cuenta_id.'/eliminar") }}" class="btn btn-xs btn-danger"  data-toggle="tooltip" data-placement="top" title="Eliminar"><i class="fa fa-trash" aria-hidden="true"></i></a>
                @if('.$cuenta->detallescontable.' == 0) <a href="{{ url("cuenta/'.$cuenta->cuenta_id.'/subcuenta") }}" class="btn btn-xs btn-secondary"  data-toggle="tooltip" data-placement="top" title="Añadir Cuenta"><i class="fa fa-tasks" aria-hidden="true"></i></a>@endif';
                $arbol=$arbol.$this->cuentasHijas($cuenta->cuenta_id);
                $arbol=$arbol.'</li>';
                $arbol=$arbol.'</ul>';
            }
            return $arbol;
        }catch(\Exception $ex){
           
            return redirect('inicio')->with('error2','Ocurrio un error vuelva a intentarlo('.$ex->getMessage().')');
        }
    }

    public function guardar(Request $request)
    {
        try{            
            DB::beginTransaction();
        $numero = $request->get('idNumero');
        $nombre = $request->get('idNombre');
        $secuencial = $request->get('idSecuencial');
        $nivel = $request->get('idNivel');
        $padre = $request->get('idPadre');
      
        for ($i = 0; $i < count($numero); ++$i) {
            $cuenta = new Cuenta();
            $cuenta->cuenta_numero = $numero[$i];
            $cuenta->cuenta_nombre = $nombre[$i];
            $cuenta->cuenta_secuencial =$secuencial[$i];
            $cuenta->cuenta_nivel =$nivel[$i];
            $cuenta->cuenta_estado = 1;
            if ($padre[$i]) {
                $cpadre=Cuenta::NivelPadre($padre[$i])->first();
                $cuenta->cuenta_padre_id =$cpadre->cuenta_id;
            }
    
            
            $cuenta->empresa_id = Auth::user()->empresa_id;
            $cuenta->save();
            /*Inicio de registro de auditoria */
            $auditoria = new generalController();
            $auditoria->registrarAuditoria('Registro de cuenta -> '.$cuenta->cuenta_nombre, '0', 'Numero de la cuenta registrada es -> '.$cuenta->cuenta_nombre);
        }
            /*Fin de registro de auditoria */
            DB::commit();
            return redirect('cuenta')->with('success','Datos guardados exitosamente');
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('cuenta')->with('error2','Ocurrio un error vuelva a intentarlo('.$ex->getMessage().')');
        }
    }
    public function CargarExcelCuenta(Request $request){
        if (isset($_POST['cargar'])){
            return $this->cargarguardar($request);
        }
        if (isset($_POST['guardar'])){
            return $this->guardar($request);
        }
    }
    public function cargarguardar(Request $request){
        try{
            DB::beginTransaction();
            if($request->file('excelProv')->isValid()){
                $empresa = Empresa::empresa()->first();
                $name = $empresa->empresa_ruc. '.' .$request->file('excelProv')->getClientOriginalExtension();
                $path = $request->file('excelProv')->move(public_path().'\temp\plancuentas', $name); 
                $array = Excel::toArray(new Cuenta(), $path); 
                for ($i=1;$i < count($array[0]);$i++) {
                    $validar=trim($array[0][$i][0]);
                    $validacion=Cuenta::NivelPadre($validar)->get();
                    if (count($validacion)==0) {
                        $cuenta = new Cuenta();
                        $cuenta->cuenta_numero = $validar;
                        $cuenta->cuenta_nombre = $array[0][$i][1];
                        $nume= $validar;
                        $porciones = explode(".", $nume);
                        $padre='';
                        for ($j=0;$j <= count($porciones)-2;$j++) {
                            $padre=$padre.$porciones[$j].'.';
                        }
                        $cuenta->cuenta_secuencial =(int)$porciones[count($porciones)-1];
                        $cuenta->cuenta_nivel =$array[0][$i][2];
                        if (strlen($nume)>1) {
                            $cpadre=Cuenta::NivelPadre(substr($padre, 0, -1))->first();
                            $cuenta->cuenta_padre_id =$cpadre->cuenta_id;
                        } else {
                            $cuenta->cuenta_padre_id =null;
                        }
                        $cuenta->empresa_id = Auth::user()->empresa_id;
                        $cuenta->cuenta_estado = 1;
                        $cuenta->save();
                        /*Inicio de registro de auditoria */
                        $auditoria = new generalController();
                        $auditoria->registrarAuditoria('Registro de cuenta -> '.$cuenta->cuenta_nombre, '0', 'Numero de la cuenta registrada es -> '.$cuenta->cuenta_nombre);
                    }
                }
            }
        DB::commit();
        return redirect('cuenta')->with('success','Datos guardados exitosamente');
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('cuenta')->with('error2','Ocurrio un error vuelva a intentarlo('.$ex->getMessage().')');
        }
    }
    public function cargar(Request $request){
        try{
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $datos = null;
            $count = 1;
            
            if($request->file('excelProv')->isValid()){
                $empresa = Empresa::empresa()->first();
                $name = $empresa->empresa_ruc. '.' .$request->file('excelProv')->getClientOriginalExtension();
                $path = $request->file('excelProv')->move(public_path().'\temp', $name); 
                $array = Excel::toArray(new Cuenta(), $path); 
                for($i=1;$i < count($array[0]);$i++){
                    $nume= $array[0][$i][0];
                    $datos[$count]['numero'] = $array[0][$i][0];
                    $datos[$count]['nombre'] = $array[0][$i][1];
                   
                    $porciones = explode(".", $nume);
                    $padre='';
                    for($j=0;$j <= count($porciones)-2;$j++){
                        $padre=$padre.$porciones[$j].'.';
                    }
                    
                    $datos[$count]['secuencial'] = (int)$porciones[count($porciones)-1];
                    $datos[$count]['Nivel'] = $array[0][$i][2];
                   if(strlen($nume)>1){
                   
                    $datos[$count]['Padre'] = substr($padre, 0, -1);
                   }
                   else{
                    $datos[$count]['Padre'] =null;
                   }
                   $datos[$count]['estado'] ='1';
                   $datos[$count]['empresa'] = $empresa->empresa_id;
                    $count ++;
                }  

            }
            return view('admin.parametrizacion.plancuenta.index',['datos'=>$datos,'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        }catch(\Exception $ex){
          
            return redirect('cuenta')->with('error2','Ocurrio un error vuelva a intentarlo('.$ex->getMessage().')');
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
            $cuenta = new Cuenta();
            $cuenta->cuenta_numero = $request->get('cuenta_numero');
            $cuenta->cuenta_nombre = $request->get('cuenta_nombre');
            $cuenta->cuenta_secuencial = $request->get('cuenta_numero');
            $cuenta->cuenta_nivel = $request->get('cuenta_nivel');
            $cuenta->cuenta_estado = 1;
            $cuenta->empresa_id = Auth::user()->empresa_id;
            $cuenta->save();
            /*Inicio de registro de auditoria */
            $auditoria = new generalController();
            $auditoria->registrarAuditoria('Registro de cuenta -> '.$request->get('cuenta_nombre'),'0','Numero de la cuenta registrada es -> '.$request->get('cuenta_numero'));
            /*Fin de registro de auditoria */
            DB::commit();
            return redirect('cuenta')->with('success','Datos guardados exitosamente');
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('cuenta')->with('error2','Ocurrio un error vuelva a intentarlo('.$ex->getMessage().')');
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
            $cuenta=Cuenta::cuenta($id)->first();
            if($cuenta){
                return view('admin.contabilidad.planCuenta.editar', ['cuenta'=>$cuenta, 'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
            }else{
                return redirect('/denegado');
            }
        }catch(\Exception $ex){
           
            return redirect('inicio')->with('error2','Ocurrio un error vuelva a intentarlo('.$ex->getMessage().')');
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
            $cuenta = Cuenta::findOrFail($id);
            $cuenta->cuenta_nombre = $request->get('cuenta_nombre');
            if ($request->get('cuenta_estado') == "on"){
                $cuenta->cuenta_estado = 1;
            }else{
                $cuenta->cuenta_estado = 0;
            }            
            $cuenta->save();
            /*Inicio de registro de auditoria */
            $auditoria = new generalController();
            $auditoria->registrarAuditoria('Actualizacion de cuenta -> '.$request->get('cuenta_nombre'),'0','Numero de la cuenta actualizada es -> '.$request->get('cuenta_numero'));
            /*Fin de registro de auditoria */
            DB::commit();
            return redirect('cuenta')->with('success','Datos actualizados exitosamente');
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('cuenta')->with('error2','Ocurrio un error vuelva a intentarlo('.$ex->getMessage().')');
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
            $cuenta = Cuenta::findOrFail($id);
            $nombre=$cuenta->cuenta_nombre;
            $numero=$cuenta->cuenta_numero;
            $cuenta->delete();
            /*Inicio de registro de auditoria */
            $auditoria = new generalController();
            $auditoria->registrarAuditoria('Eliminarcion de cuenta -> '.$nombre,'0','Numero de la cuenta eliminada es -> '.$numero);
            /*Fin de registro de auditoria */
            DB::commit();
            return redirect('cuenta')->with('success','Datos eliminados exitosamente');
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('cuenta')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }

    public function delete($id){
        try{
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $cuenta=Cuenta::cuenta($id)->first();
            if($cuenta){
                return view('admin.contabilidad.planCuenta.eliminar',['cuenta'=>$cuenta, 'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
            }else{
                return redirect('/denegado');
            }
        }catch(\Exception $ex){
            return redirect('inicio')->with('error2','Ocurrio un error vuelva a intentarlo('.$ex->getMessage().')');
        }
    }

    public function agregarCuenta($id){
        try{
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $cuentaPadre=Cuenta::cuenta($id)->first();
            $secuencial = 1;
            $secuencialAux=Cuenta::nivel($id)->max('cuenta_secuencial');
            if($secuencialAux){
                $secuencial=$secuencialAux+1;
            }
            if($cuentaPadre){
                return view('admin.contabilidad.planCuenta.agregarCuentas',['cuentaPadre'=>$cuentaPadre, 'PE'=>Punto_Emision::puntos()->get(),'secuencial'=>$secuencial, 'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
            }else{
                return redirect('/denegado');
            }
        }catch(\Exception $ex){
            return redirect('inicio')->with('error2','Ocurrio un error vuelva a intentarlo('.$ex->getMessage().')');
        }
    }

    public function guardarCuenta(Request $request, $id){
        try{            
            DB::beginTransaction();
            $cuenta = new Cuenta();
            $cuenta->cuenta_numero = $request->get('cuenta_padre').'.'.$request->get('cuenta_numero');
            $cuenta->cuenta_nombre = $request->get('cuenta_nombre');
            $cuenta->cuenta_secuencial = $request->get('cuenta_numero');
            $cuenta->cuenta_nivel = $request->get('cuenta_nivel');
            $cuenta->cuenta_estado = 1;
            $cuenta->empresa_id = Auth::user()->empresa_id;;
            $cuenta->cuenta_padre_id = $id;
            $cuenta->save();
            /*Inicio de registro de auditoria */
            $auditoria = new generalController();
            $auditoria->registrarAuditoria('Registro de cuenta -> '.$request->get('cuenta_nombre'),'0','Numero de la cuenta registrada es -> '.$request->get('cuenta_padre').'.'.$request->get('cuenta_numero'));
            /*Fin de registro de auditoria */
            DB::commit();
            return redirect('cuenta')->with('success','Datos guardados exitosamente');
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('cuenta')->with('error2','Ocurrio un error vuelva a intentarlo('.$ex->getMessage().')');
        }
    }
    public function cuentaPadre($id){
        $cuenta = Cuenta::findOrFail($id);
        return $cuenta->cuentaPadre;
    }
    public function CargarExcel()
    {
        try{ 
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $cuentas=Cuenta::Cuentas()->get();
            return view('admin.contabilidad.cambiocuenta.cargarExcel',['PE'=>Punto_Emision::puntos()->get(),'cuentas'=>$cuentas,  'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        }catch(\Exception $ex){
    
            return redirect('inicio')->with('error2','Ocurrio un error vuelva a intentarlo('.$ex->getMessage().')');
        }
    }
    public function cambiarCuentas(Request $request){
       
            if($request->file('excelCuenta')->isValid()){
                $empresa = Empresa::empresa()->first();
                $name = $empresa->empresa_ruc. '.' .$request->file('excelCuenta')->getClientOriginalExtension();
                $path = $request->file('excelCuenta')->move(public_path().'\temp\CambioCuenta', $name); 
                $array = Excel::toArray(new Cuenta(), $path); 
                for ($i=1;$i < count($array[0]);$i++) {
                    $validar=trim($array[0][$i][7]);
                    $validacion=Diario::DiarioCodigo($validar)->first();   
                    if ($validacion) {
                        $diario = Diario::findOrFail($validacion->diario_id);
                        foreach($diario->detalles as $detalle){
                            if($request->get('cuenta')==$detalle->cuenta_id){
                                $detalles=Detalle_Diario::findOrFail($detalle->detalle_id);
                                $detalles->cuenta_id=$request->get('cuentanew');
                                $detalles->save();
                                $auditoria = new generalController();
                                $auditoria->registrarAuditoria('Cabmio de cuenta en Diario -> '.$diario->diario_codigo.' con cuenta numero '.$detalle->cuenta->cuenta_numero.'-'.$detalle->cuenta->cuenta_nombre.' a cuenta nueva '.$detalles->cuenta->cuenta_numero.'-'.$detalles->cuenta->cuenta_nombre, '0', '');
                            }
                        }  
                    }
                }
            }
         
            return redirect('excelCambioCuentas')->with('success','Datos guardados exitosamente');
       
    }
}
