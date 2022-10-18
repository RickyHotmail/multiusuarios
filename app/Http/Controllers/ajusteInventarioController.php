<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Bodega;
use App\Models\Centro_Consumo;
use App\Models\Empresa;
use App\Models\Movimiento_Producto;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
class ajusteInventarioController extends Controller
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
            $bodega=Bodega::Bodegas()->get();
            $centro=Centro_Consumo::CentroConsumos()->get();
            return view('admin.inventario.AjusteInventario.index',['centro'=>$centro,'bodega'=>$bodega, 'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
            return redirect('/denegado');
        }catch(\Exception $ex){
            return redirect('inicio')->with('error','Ocurrio un error vuelva a intentarlo('.$ex->getMessage().')');
        }
    }
    public function CargarExcelInventario(Request $request){
        try{
            DB::beginTransaction();
            if($request->file('excelProd')->isValid()){
                $empresa = Empresa::empresa()->first();
                $name = $empresa->empresa_ruc. '.' .$request->file('excelProd')->getClientOriginalExtension();
                $path = $request->file('excelProd')->move(public_path().'\temp', $name); 
                $array = Excel::toArray(new Movimiento_Producto(), $path); 
                for ($i=1;$i < count($array[0]);$i++) {
                    if (floatval($array[0][$i][2])>0 && floatval($array[0][$i][3])>0) {
                        $validar=trim($array[0][$i][0]);
                        $validacion=Producto::ProductoCodigo($validar)->first();   
                        if ($validacion) {
                            $movimiento = new Movimiento_Producto();
                            $movimiento->movimiento_fecha = $request->get('fecha');
                            $movimiento->movimiento_cantidad = floatval($array[0][$i][2]);
                            $movimiento->movimiento_precio = floatval($array[0][$i][3]);
                            $movimiento->movimiento_iva =0;
                            $movimiento->movimiento_total =floatval($array[0][$i][2])*floatval($array[0][$i][3]);
                            $movimiento->movimiento_stock_actual =0;
                            $movimiento->movimiento_costo_promedio =0;
                            $movimiento->movimiento_documento ='INGRESO DE BODEGA';
                            $movimiento->movimiento_motivo ='AJUSTE DE INVENTARIO';
                            $movimiento->movimiento_tipo ='ENTRADA';
                            $movimiento->movimiento_descripcion ='AJUSTE DE INVENTARIO';
                            $movimiento->movimiento_estado ='1';
                            $movimiento->producto_id =$validacion->producto_id;
                            $movimiento->bodega_id = $request->get('bodega_id');
                            $movimiento->centro_consumo_id = $request->get('centro_consumo_id');
                            $movimiento->empresa_id = Auth::user()->empresa_id;
                       
                            $movimiento->save();
                            /*Inicio de registro de auditoria */
                            $auditoria = new generalController();
                            $auditoria->registrarAuditoria('Registro de Movimiento de Ingreso  con producto Id-> '.$movimiento->producto_id. ' con Centro consumo Id'.$movimiento->bodega_id. ' con bodega Id'.$movimiento->centro_consumo_id, '0', 'Con la Cantidad '.$movimiento->movimiento_cantidad.' Con la Precio '.$movimiento->movimiento_precio);
                        }
                    }
                }
            }
        DB::commit();
        return redirect('ajusteInventario')->with('success','Datos guardados exitosamente');
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('ajusteInventario')->with('error2','Ocurrio un error vuelva a intentarlo('.$ex->getMessage().')');
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
        //
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
