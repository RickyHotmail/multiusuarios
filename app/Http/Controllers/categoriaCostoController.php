<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Categoria_Costo;
use App\Models\Punto_Emision;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class categoriaCostoController extends Controller
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
            $categorias = Categoria_Costo::categoriaClientes()->get();
            return view('admin.compras.categoriaCosto.index',['categorias'=>$categorias, 'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        }catch(\Exception $ex){
           
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

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      
            $idcategoria=$request->get('idcategoria');
            $cgeneral=$request->get('tgeneral');
            $ccosto=$request->get('tcosto');
            $cracewas=$request->get('tracewas');
            $caplicacion=$request->get('taplicacion');
            $cvisible=$request->get('tvisible');
            
            for ($i = 0; $i < count($idcategoria); $i++) {
                $categoria = Categoria_Costo::findOrFail($idcategoria[$i]);
                if ($cgeneral[$i]=='1') {
                    $categoria->categoriac_general = '1';
                }
                else{
                    $categoria->categoriac_general = '0';
                }
                if ($ccosto[$i]=='1') {
                    $categoria->categoriac_costo = '1';
                }
                else{
                    $categoria->categoriac_costo = '0';
                }
                if ($cracewas[$i]=='1') {
                    $categoria->categoriac_racewas = '1';
                }
                else{
                    $categoria->categoriac_racewas = '0';
                }
                if ($caplicacion[$i]=='1') {
                    $categoria->categoriac_sin_aplicacion = '1';
                }
                else{
                    $categoria->categoriac_sin_aplicacion = '0';
                }
                if ($cvisible[$i]=='1') {
                    $categoria->categoriac_visible = '1';
                }
                else{
                    $categoria->categoriac_visible = '0';
                }
                $categoria->save();

                /*Inicio de registro de auditoria */
                $auditoria = new generalController();
                $auditoria->registrarAuditoria('Actualizacion de categoria de costo -> '.$categoria->categoriac_id,'0','');
                /*Fin de registro de auditoria */
            }
           
        
            return redirect('categoriaCosto')->with('success','Datos guardados exitosamente');
     
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
