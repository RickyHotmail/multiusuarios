<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Examen;
use App\Models\Tipo_Examen;
use App\Models\Categoria_Producto;
use App\Models\Unidad_Medida_Producto;
use App\Models\Cuenta;
use App\Models\Marca_Producto;
use App\Models\Tamano_Producto;
use App\Models\Grupo_Producto;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use App\Models\Codigo_Producto;
use App\Models\Detalle_Lista;
use App\Models\Empresa;
use App\Models\Imagen;
use App\Models\Precio_Producto;
use App\Models\Proveedor;
use App\Models\Punto_Emision;
use App\Models\Sucursal;
use App\Models\Tipo_Imagen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

use function PHPUnit\Framework\isEmpty;
use function PHPUnit\Framework\isNull;

class productoController extends Controller
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
            $productos=Producto::productos()->where('producto_compra_venta','!=','1')->get();
            $productosGastos=Producto::productos()->where('producto_compra_venta','=','1')->get();
            $cuentas=Cuenta::CuentasMovimiento()->get();
            $categorias=Categoria_Producto::categorias()->get();
            $marcas=Marca_Producto::marcas()->get();
            $unidadMedidas=Unidad_Medida_Producto::unidadMedidas()->get();
            $tamanos=Tamano_Producto::tamanos()->get();
            $grupos=Grupo_Producto::grupos()->get();
            return view('admin.inventario.producto.index',['productos'=>$productos, 'productosGastos'=>$productosGastos,
            'cuentas'=>$cuentas,
            'PE'=>Punto_Emision::puntos()->get(),
            'sucursales'=>Sucursal::sucursales()->get(),
            'categorias'=>$categorias,
            'marcas'=>$marcas,
            'unidadMedidas'=>$unidadMedidas,
            'tamanos'=>$tamanos,
            'grupos'=>$grupos,
            'gruposPermiso'=>$gruposPermiso,
            'permisosAdmin'=>$permisosAdmin]);    
        }
        catch(\Exception $ex){      
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
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
            $producto = new Producto();
            $producto->producto_codigo = $request->get('producto_codigo');
            $producto->producto_nombre = $request->get('producto_nombre');
            $producto->producto_codigo_barras = $request->get('producto_codigo_barras');
            $producto->producto_tipo = $request->get('producto_tipo');
            $producto->producto_precio_costo = $request->get('producto_precio_costo');           
            $producto->producto_stock = "0";
            $producto->producto_stock_minimo = $request->get('producto_stock_minimo');
            $producto->producto_stock_maximo = $request->get('producto_stock_maximo');
            $producto->producto_fecha_ingreso = $request->get('producto_fecha_ingreso');
            if ($request->get('producto_tiene_iva') == "on"){
                $producto->producto_tiene_iva ="1";
            }else{
                $producto->producto_tiene_iva ="0";
            }
            if ($request->get('producto_tiene_descuento') == "on"){
                $producto->producto_tiene_descuento ="1";
            }else{
                $producto->producto_tiene_descuento ="0";
            }
            if ($request->get('producto_tiene_serie') == "on"){
                $producto->producto_tiene_serie ="1";
            }else{
                $producto->producto_tiene_serie ="0";
            }   
              
            $producto->producto_compra_venta = $request->get('idCompraventa');
            $producto->producto_precio1 = $request->get('producto_precio1');
            $producto->producto_estado = 1;
           
            if(Auth::user()->empresa->empresa_llevaContabilidad == '1'){
                $producto->producto_cuenta_inventario = $request->get('producto_cuenta_inventario');
                $producto->producto_cuenta_venta = $request->get('producto_cuenta_venta');
                $producto->producto_cuenta_gasto = $request->get('producto_cuenta_gasto');
            }
            $producto->categoria_id = $request->get('categoria_id');
            $producto->marca_id = $request->get('marca_id');
            $producto->unidad_medida_id = $request->get('unidad_medida_id');
            $producto->empresa_id = Auth::user()->empresa_id;
            $producto->tamano_id  = $request->get('tamano_id');
            $producto->grupo_id  = $request->get('grupo_id');

            if($request->get('sucursal_id') != '0'){
                $producto->sucursal_id  = $request->get('sucursal_id');
            }else{
                $sucursal=Sucursal::Sucursales()->first();
                $producto->sucursal_id=$sucursal->sucursal_id;    
            }

            if(Auth::user()->empresa->camaronera){
                if($request->get('producto_camaronero')){
                    $producto->producto_camaronero  = $request->get('producto_camaronero');
                }
            }
            $producto->save();

            $auditoria = new generalController();
            $grupo=Grupo_Producto::findOrFail($request->get('grupo_id'));

            if($grupo->grupo_nombre=="Laboratorio"){
                $examen=Examen::examenByIdProducto($producto->producto_id)->first();

                if(empty($examen))  $examen= new Examen;

                $examen->examen_estado = 1;
                $examen->producto_id = $producto->producto_id;
                $examen->save();
            }

            if($grupo->grupo_nombre=="Imagen" || $grupo->grupo_nombre=="IMAGEN"){
                $imagen = new Imagen();
                $imagen->producto_id=$producto->producto_id;
                $imagen->imagen_estado = 1;

                $tipoImagen=Tipo_Imagen::findOrFail(3);
                if($tipoImagen) $imagen->tipo_id = $tipoImagen('tipo_id');

                $imagen->save();
                $auditoria->registrarAuditoria('Registro de imagen -> '.$request->get('imagen_nombre').', producto_id -> '.$producto->producto_id,'0','');
            }

            
            /*Inicio de registro de auditoria */
           
            $auditoria->registrarAuditoria('Registro de producto -> '.$producto->producto_nombre.',  con codigo de producto ->'.$request->get('producto_codigo'),'0','');
            /*Fin de registro de auditoria */
            
            if(isset(Auth::user()->empresa->grupo)){
                foreach(Auth::user()->empresa->grupo->usuarios->empresas as $empresas){
                    if (Auth::user()->empresa->empresa_id != $empresas->empresa->empresa_id) {
                        if (Auth::user()->empresa->grupo->grupo_duplicado == '1') {
                        $producto = new Producto();
                        $producto->producto_codigo = $request->get('producto_codigo');
                        $producto->producto_nombre = $request->get('producto_nombre');
                        $producto->producto_codigo_barras = $request->get('producto_codigo_barras');
                        $producto->producto_tipo = $request->get('producto_tipo');
                        $producto->producto_precio_costo = $request->get('producto_precio_costo');           
                        $producto->producto_stock = "0";
                        $producto->producto_stock_minimo = $request->get('producto_stock_minimo');
                        $producto->producto_stock_maximo = $request->get('producto_stock_maximo');
                        $producto->producto_fecha_ingreso = $request->get('producto_fecha_ingreso');
                        if ($request->get('producto_tiene_iva') == "on"){
                            $producto->producto_tiene_iva ="1";
                        }else{
                            $producto->producto_tiene_iva ="0";
                        }
                        if ($request->get('producto_tiene_descuento') == "on"){
                            $producto->producto_tiene_descuento ="1";
                        }else{
                            $producto->producto_tiene_descuento ="0";
                        }
                        if ($request->get('producto_tiene_serie') == "on"){
                            $producto->producto_tiene_serie ="1";
                        }else{
                            $producto->producto_tiene_serie ="0";
                        }   
                          
                        $producto->producto_compra_venta = $request->get('idCompraventa');
                        $producto->producto_precio1 = $request->get('producto_precio1');
                        $producto->producto_estado = 1;
                       
                        if(Auth::user()->empresa->empresa_llevaContabilidad == '1'){
                            $producto->producto_cuenta_inventario = $request->get('producto_cuenta_inventario');
                            $producto->producto_cuenta_venta = $request->get('producto_cuenta_venta');
                            $producto->producto_cuenta_gasto = $request->get('producto_cuenta_gasto');
                        }
                            
                            
                            $producto->empresa_id = $empresas->empresa->empresa_id;
                            
                            $tipo=Categoria_Producto::findOrFail($request->get('categoria_id'));
                            $tipoaux=Categoria_Producto::CategoriaEmpresaNombre($tipo->categoria_nombre,$empresas->empresa->empresa_id)->first();
                            if($tipoaux){
                                $producto->categoria_id = $tipoaux->categoria_id;
                            }else{
                                $tipoaux=Categoria_Producto::CategoriaEmpresa($empresas->empresa->empresa_id)->first();
                                $producto->categoria_id= $tipoaux->categoria_id;
                            }

                           
                            $tipo=Marca_Producto::findOrFail($request->get('marca_id'));
                            $tipoaux=Marca_Producto::MarcaEmpresaNombre($tipo->marca_nombre,$empresas->empresa->empresa_id)->first();
                            if($tipoaux){
                                $producto->marca_id = $tipoaux->marca_id;
                            }else{
                                $tipoaux=Marca_Producto::MarcaEmpresa($empresas->empresa->empresa_id)->first();
                                $producto->marca_id= $tipoaux->marca_id;
                            }

                           
                            $tipo=Unidad_Medida_Producto::findOrFail($request->get('unidad_medida_id'));
                            $tipoaux=Unidad_Medida_Producto::UnidadEmpresaNombre($tipo->unidad_medida_nombre,$empresas->empresa->empresa_id)->first();
                            if($tipoaux){
                                $producto->unidad_medida_id = $tipoaux->unidad_medida_id;
                            }else{
                                $tipoaux=Unidad_Medida_Producto::UnidadEmpresa($empresas->empresa->empresa_id)->first();
                                $producto->unidad_medida_id= $tipoaux->unidad_medida_id;
                            }

                            
                            $tipo=Tamano_Producto::findOrFail($request->get('tamano_id'));
                            $tipoaux=Tamano_Producto::TamanoEmpresaNombre($tipo->tamano_nombre,$empresas->empresa->empresa_id)->first();
                            if($tipoaux){
                                $producto->tamano_id = $tipoaux->tamano_id;
                            }else{
                                $tipoaux=Tamano_Producto::TamanoEmpresa($empresas->empresa->empresa_id)->first();
                                $producto->tamano_id= $tipoaux->tamano_id;
                            }

                           
                            $tipo=Grupo_Producto::findOrFail($request->get('grupo_id'));
                            $tipoaux=Grupo_Producto::GrupoEmpresaNombre($tipo->grupo_nombre,$empresas->empresa->empresa_id)->first();
                            if($tipoaux){
                                $producto->grupo_id = $tipoaux->grupo_id;
                            }else{
                                $tipoaux=Grupo_Producto::GrupoEmpresa($empresas->empresa->empresa_id)->first();
                                $producto->grupo_id= $tipoaux->grupo_id;
                            }

                            
                            if($request->get('sucursal_id') != '0'){
                               
                                $tipo=Sucursal::findOrFail($request->get('sucursal_id'));
                                $tipoaux=Sucursal::SucursalesEmpresaNombre($tipo->sucursal_nombre,$empresas->empresa->empresa_id)->first();
                                if($tipoaux){
                                    $producto->sucursal_id = $tipoaux->sucursal_id;
                                }else{
                                    $tipoaux=Sucursal::SucursalesEmpresa($empresas->empresa->empresa_id)->first();
                                    $producto->sucursal_id = $tipoaux->sucursal_id;
                                }
                            }else{
                                $sucursal=Sucursal::SucursalesEmpresa($empresas->empresa->empresa_id)->first();
                                $producto->sucursal_id=$sucursal->sucursal_id;    
                            }

                            
                            $producto->save();
                            $auditoria = new generalController();
                            $auditoria->registrarAuditoria('Registro de producto -> '.$request->get('producto_nombre').'con codigo de ->'.$request->get('producto_codigo'),'0','');
                        }
                    }   
                }
            }
            
            DB::commit();
            return redirect('producto')->with('success','Datos guardados exitosamente');
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('producto')->with('error2','Oucrrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }

    public function storeQuick(Request $request){
       try{
            DB::beginTransaction();
            $producto = new Producto();
            $producto->producto_codigo = $request->get('producto_codigo');
            $producto->producto_nombre = $request->get('producto_nombre');
            $producto->producto_codigo_barras = $request->get('producto_codigo_barras');
            $producto->producto_tipo = $request->get('producto_tipo');
            $producto->producto_precio_costo = $request->get('producto_precio_costo');           
            $producto->producto_stock = "0";
            $producto->producto_stock_minimo = $request->get('producto_stock_minimo');
            $producto->producto_stock_maximo = $request->get('producto_stock_maximo');
            $producto->producto_fecha_ingreso = $request->get('producto_fecha_ingreso');
            if ($request->get('producto_tiene_iva') == "on"){
                $producto->producto_tiene_iva ="1";
            }else{
                $producto->producto_tiene_iva ="0";
            }
            if ($request->get('producto_tiene_descuento') == "on"){
                $producto->producto_tiene_descuento ="1";
            }else{
                $producto->producto_tiene_descuento ="0";
            }
            if ($request->get('producto_tiene_serie') == "on"){
                $producto->producto_tiene_serie ="1";
            }else{
                $producto->producto_tiene_serie ="0";
            }   
              
            $producto->producto_compra_venta = $request->get('idCompraventa');
            $producto->producto_precio1 = $request->get('producto_precio1');
            $producto->producto_estado = 1;
           
            if(Auth::user()->empresa->empresa_llevaContabilidad == '1'){
                $producto->producto_cuenta_inventario = $request->get('producto_cuenta_inventario');
                $producto->producto_cuenta_venta = $request->get('producto_cuenta_venta');
                $producto->producto_cuenta_gasto = $request->get('producto_cuenta_gasto');
            }
            $producto->categoria_id = $request->get('categoria_id');
            $producto->marca_id = $request->get('marca_id');
            $producto->unidad_medida_id = $request->get('unidad_medida_id');
            $producto->empresa_id = Auth::user()->empresa_id;
            $producto->tamano_id  = $request->get('tamano_id');
            $producto->grupo_id  = $request->get('grupo_id');

            if($request->get('sucursal_id') != '0'){
                $producto->sucursal_id  = $request->get('sucursal_id');
            }else{
                $sucursal=Sucursal::Sucursales()->first();
                $producto->sucursal_id=$sucursal->sucursal_id;    
            }

            if(Auth::user()->empresa->camaronera){
                if($request->get('producto_camaronero')){
                    $producto->producto_camaronero  = $request->get('producto_camaronero');
                }
            }
            $producto->save();

            $grupo=Grupo_Producto::findOrFail($request->get('grupo_id'));

            if($grupo->grupo_nombre=="Laboratorio"){
                $examen=Examen::examenByIdProducto($producto->producto_id)->first();

                if(empty($examen))  $examen= new Examen;

                $examen->examen_estado = 1;
                $examen->producto_id = $producto->producto_id;
                $examen->save();
            }

            
            /*Inicio de registro de auditoria */
            $auditoria = new generalController();
            $auditoria->registrarAuditoria('Registro de producto -> '.$request->get('producto_nombre').'con codigo de ->'.$request->get('producto_codigo'),'0','');
            /*Fin de registro de auditoria */
            DB::commit();
            return json_encode(array('result' => 'OK', 'producto'=>$producto));
        }catch(\Exception $ex){
            DB::rollBack();
            return json_encode(array('respuesta' => 'FAIL', 'message'=>$ex->getMessage()));
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
            $producto=Producto::producto($id)->first();        
            if($producto){
                return view('admin.inventario.producto.ver',['producto'=>$producto, 'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
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
            $producto=Producto::producto($id)->first();
            $cuentas=Cuenta::CuentasMovimiento()->get();
            $categorias=Categoria_Producto::categorias()->get();
            $marcas=Marca_Producto::marcas()->get();
            $unidadMedidas=Unidad_Medida_Producto::unidadMedidas()->get();
            $tamanos=Tamano_Producto::tamanos()->get();
            $grupos=Grupo_Producto::grupos()->get();
            $tiposExamen=Tipo_Examen::TipoExamenes()->get();

            if($producto){
                return view('admin.inventario.producto.editar',['producto'=>$producto,
                'PE'=>Punto_Emision::puntos()->get(),
                'sucursales'=>Sucursal::sucursales()->get(),
                'cuentas'=>$cuentas,
                'categorias'=>$categorias,
                'marcas'=>$marcas,
                'unidadMedidas'=>$unidadMedidas,
                'tamanos'=>$tamanos,
                'grupos'=>$grupos,
                'gruposPermiso'=>$gruposPermiso,
                'permisosAdmin'=>$permisosAdmin,
                'tiposExamen'=>$tiposExamen]);
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
            $producto = Producto::findOrFail($id);
            $productoaux = Producto::findOrFail($id);
            $producto->producto_codigo = $request->get('producto_codigo');
            $producto->producto_nombre = $request->get('producto_nombre');
            $producto->producto_codigo_barras = $request->get('producto_codigo_barras');
            $producto->producto_tipo = $request->get('producto_tipo');
            $producto->producto_precio_costo = $request->get('producto_precio_costo');           
            $producto->producto_stock_minimo = $request->get('producto_stock_minimo');
            $producto->producto_stock_maximo = $request->get('producto_stock_maximo');
            $producto->producto_fecha_ingreso = $request->get('producto_fecha_ingreso');
            if ($request->get('producto_tiene_iva') == "on"){
                $producto->producto_tiene_iva ="1";
            }else{
                $producto->producto_tiene_iva ="0";
            }
            if ($request->get('producto_tiene_descuento') == "on"){
                $producto->producto_tiene_descuento ="1";
            }else{
                $producto->producto_tiene_descuento ="0";
            }
            if ($request->get('producto_tiene_serie') == "on"){
                $producto->producto_tiene_serie ="1";
            }else{
                $producto->producto_tiene_serie ="0";
            }         
            $producto->producto_compra_venta = $request->get('producto_compra_venta');
            $producto->producto_precio1 = $request->get('producto_precio1');
            if ($request->get('producto_estado') == "on"){
                $producto->producto_estado = 1; 
            }else{
                $producto->producto_estado = 0;
            }
            if(Auth::user()->empresa->empresa_llevaContabilidad == '1'){
                $producto->producto_cuenta_inventario = $request->get('producto_cuenta_inventario');
                $producto->producto_cuenta_venta = $request->get('producto_cuenta_venta');
                $producto->producto_cuenta_gasto = $request->get('producto_cuenta_gasto');
            }
            $producto->categoria_id = $request->get('categoria_id');
            $producto->marca_id = $request->get('marca_id');
            $producto->unidad_medida_id = $request->get('unidad_medida_id');
            $producto->empresa_id = Auth::user()->empresa_id;
            $producto->tamano_id  = $request->get('tamano_id');
            $producto->grupo_id  = $request->get('grupo_id');

            $grupo=Grupo_Producto::findOrFail($request->get('grupo_id'));

            if($grupo->grupo_nombre=="Laboratorio"){
                $examen=Examen::examenByIdProducto($id)->first();

                if(empty($examen))  $examen= new Examen;

                $examen->examen_estado = 1;
                $examen->producto_id = $id;
                $examen->save();
            }

            if($request->get('sucursal_id') != '0'){
                $producto->sucursal_id  = $request->get('sucursal_id');
            }else{
                $producto->sucursal_id  = null;
            }

            if(Auth::user()->empresa->camaronera){
                if($request->get('producto_camaronero')){
                    $producto->producto_camaronero  = $request->get('producto_camaronero');
                }
            }
            $producto->save();
            /*Inicio de registro de auditoria */
            $auditoria = new generalController();
            $auditoria->registrarAuditoria('Actualizacion de producto -> '.$request->get('producto_nombre').' con codigo de -> '.$request->get('producto_codigo'),'0','');
            /*Fin de registro de auditoria */

            if (isset(Auth::user()->empresa->grupo)) {
                foreach (Auth::user()->empresa->grupo->usuarios->empresas as $empresas) {
                    if (Auth::user()->empresa->empresa_id != $empresas->empresa->empresa_id) {
                        if (Auth::user()->empresa->grupo->grupo_duplicado == '1') {
                            $productoaux=Producto::ProductoEmpresa($productoaux->producto_codigo,$empresas->empresa->empresa_id)->first();
                            $producto = Producto::findOrFail($productoaux->producto_id);
                            $producto->producto_codigo = $request->get('producto_codigo');
                            $producto->producto_nombre = $request->get('producto_nombre');
                            $producto->producto_codigo_barras = $request->get('producto_codigo_barras');
                            $producto->producto_tipo = $request->get('producto_tipo');
                            $producto->producto_precio_costo = $request->get('producto_precio_costo');           
                            $producto->producto_stock_minimo = $request->get('producto_stock_minimo');
                            $producto->producto_stock_maximo = $request->get('producto_stock_maximo');
                            $producto->producto_fecha_ingreso = $request->get('producto_fecha_ingreso');
                            if ($request->get('producto_tiene_iva') == "on"){
                                $producto->producto_tiene_iva ="1";
                            }else{
                                $producto->producto_tiene_iva ="0";
                            }
                            if ($request->get('producto_tiene_descuento') == "on"){
                                $producto->producto_tiene_descuento ="1";
                            }else{
                                $producto->producto_tiene_descuento ="0";
                            }
                            if ($request->get('producto_tiene_serie') == "on"){
                                $producto->producto_tiene_serie ="1";
                            }else{
                                $producto->producto_tiene_serie ="0";
                            }         
                            $producto->producto_compra_venta = $request->get('producto_compra_venta');
                            $producto->producto_precio1 = $request->get('producto_precio1');
                            if ($request->get('producto_estado') == "on"){
                                $producto->producto_estado = 1; 
                            }else{
                                $producto->producto_estado = 0;
                            }
                            if(Auth::user()->empresa->empresa_llevaContabilidad == '1'){
                                $producto->producto_cuenta_inventario = $request->get('producto_cuenta_inventario');
                                $producto->producto_cuenta_venta = $request->get('producto_cuenta_venta');
                                $producto->producto_cuenta_gasto = $request->get('producto_cuenta_gasto');
                            }
              
                          
  
                            $producto->empresa_id = $empresas->empresa->empresa_id;

                            


                            $tipo=Categoria_Producto::findOrFail($request->get('categoria_id'));
                            $tipoaux=Categoria_Producto::CategoriaEmpresaNombre($tipo->categoria_nombre,$empresas->empresa->empresa_id)->first();
                            if($tipoaux){
                                $producto->categoria_id = $tipoaux->categoria_id;
                            }else{
                                $tipoaux=Categoria_Producto::CategoriaEmpresa($empresas->empresa->empresa_id)->first();
                                $producto->categoria_id= $tipoaux->categoria_id;
                            }

                           
                            $tipo=Marca_Producto::findOrFail($request->get('marca_id'));
                            $tipoaux=Marca_Producto::MarcaEmpresaNombre($tipo->marca_nombre,$empresas->empresa->empresa_id)->first();
                            if($tipoaux){
                                $producto->marca_id = $tipoaux->marca_id;
                            }else{
                                $tipoaux=Marca_Producto::MarcaEmpresa($empresas->empresa->empresa_id)->first();
                                $producto->marca_id= $tipoaux->marca_id;
                            }

                           
                            $tipo=Unidad_Medida_Producto::findOrFail($request->get('unidad_medida_id'));
                            $tipoaux=Unidad_Medida_Producto::UnidadEmpresaNombre($tipo->unidad_medida_nombre,$empresas->empresa->empresa_id)->first();
                            if($tipoaux){
                                $producto->unidad_medida_id = $tipoaux->unidad_medida_id;
                            }else{
                                $tipoaux=Unidad_Medida_Producto::UnidadEmpresa($empresas->empresa->empresa_id)->first();
                                $producto->unidad_medida_id= $tipoaux->unidad_medida_id;
                            }

                            
                            $tipo=Tamano_Producto::findOrFail($request->get('tamano_id'));
                            $tipoaux=Tamano_Producto::TamanoEmpresaNombre($tipo->tamano_nombre,$empresas->empresa->empresa_id)->first();
                            if($tipoaux){
                                $producto->tamano_id = $tipoaux->tamano_id;
                            }else{
                                $tipoaux=Tamano_Producto::TamanoEmpresa($empresas->empresa->empresa_id)->first();
                                $producto->tamano_id= $tipoaux->tamano_id;
                            }

                           
                            $tipo=Grupo_Producto::findOrFail($request->get('grupo_id'));
                            $tipoaux=Grupo_Producto::GrupoEmpresaNombre($tipo->grupo_nombre,$empresas->empresa->empresa_id)->first();
                            if($tipoaux){
                                $producto->grupo_id = $tipoaux->grupo_id;
                            }else{
                                $tipoaux=Grupo_Producto::GrupoEmpresa($empresas->empresa->empresa_id)->first();
                                $producto->grupo_id= $tipoaux->grupo_id;
                            }

                            
                            if($request->get('sucursal_id') != '0'){
                               
                                $tipo=Sucursal::findOrFail($request->get('sucursal_id'));
                                $tipoaux=Sucursal::SucursalesEmpresaNombre($tipo->sucursal_nombre,$empresas->empresa->empresa_id)->first();
                                if($tipoaux){
                                    $producto->sucursal_id = $tipoaux->sucursal_id;
                                }else{
                                    $tipoaux=Sucursal::SucursalesEmpresa($empresas->empresa->empresa_id)->first();
                                    $producto->sucursal_id = $tipoaux->sucursal_id;
                                }
                            }else{
                                $sucursal=Sucursal::SucursalesEmpresa($empresas->empresa->empresa_id)->first();
                                $producto->sucursal_id=$sucursal->sucursal_id;    
                            }
                            $producto->save();
                        }
                    }
                }
            }
            DB::commit();
            return redirect('producto')->with('success','Datos actualizados exitosamente');
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('producto')->with('error2','Oucrrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
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
            $producto = Producto::findOrFail($id);
            $productoaux=Producto::findOrFail($id);        
            $producto->delete();
            /*Inicio de registro de auditoria */
            $auditoria = new generalController();
            $auditoria->registrarAuditoria('Eliminacion de producto -> '.$producto->producto_nombre.' con codigo de -> '.$producto->producto_codigo,'0','');
            /*Fin de registro de auditoria */

            if (isset(Auth::user()->empresa->grupo)) {
                foreach (Auth::user()->empresa->grupo->usuarios->empresas as $empresas) {
                    if (Auth::user()->empresa->empresa_id != $empresas->empresa->empresa_id) {
                        if (Auth::user()->empresa->grupo->grupo_duplicado == '1') {
                            $productoaux=Producto::ProductoEmpresa($productoaux->producto_codigo, $empresas->empresa->empresa_id)->first();
                            $producto = Producto::findOrFail($productoaux->producto_id);
                            $producto->delete();
                            $auditoria = new generalController();
                            $auditoria->registrarAuditoria('Eliminacion de producto -> '.$producto->producto_nombre.' con codigo de -> '.$producto->producto_codigo.' Con empresa ruc '.$empresas->empresa->empresa_ruc.' Con razon social'.$empresas->empresa->empresa_razonSocial, $empresas->empresa->empresa_id, '0', '0');
                        }
                    }
                }
            }
            DB::commit();
            return redirect('producto')->with('success','Datos eliminados exitosamente');
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('producto')->with('error','El registro no pudo ser borrado, tiene resgitros adjuntos.');

        }
    }
    public function delete($id)
    {
        try{
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
            $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $producto=Producto::producto($id)->first();
            if($producto){
                return view('admin.inventario.producto.eliminar',['producto'=>$producto, 'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
            }else{
                return redirect('/denegado');
            }
        }
        catch(\Exception $ex){      
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function buscarByNombre($buscar){
        return Producto::ProductosByNombreCodigo($buscar)->get();
    }
    public function buscarByNombreCompra($buscar){
        return Producto::ProductosByNombreCodigo($buscar)
        ->where(function ($query){
            $query->where('producto_compra_venta','=','1')->orwhere('producto_compra_venta','=','3');
        })->get(); 
    }
    public function buscarByNombreVenta($buscar){
        return Producto::ProductosByNombreCodigo($buscar)
        ->where(function ($query){
            $query->where('producto_compra_venta','=','2')->orwhere('producto_compra_venta','=','3');
        })->get(); 
    }
    public function buscarByNombreVentaprefactura($buscar){
        return Producto::ProductosByNombreCodigo($buscar)
        ->where(function ($query){
            $query->where('producto_compra_venta','=','2')->orwhere('producto_compra_venta','=','3');
        })
        ->where(function ($query){
            $query->where('categoria_nombre','!=','MUELLAJE')->orwhere('categoria_nombre','!=','AGUA');
        })->get(); 
    }
    public function buscarPrecio(Request $request){
        $precio = 0;
        $cliente = Cliente::findOrFail($request->get('cliente'));
        if(isset($cliente->listaPrecio->lista_id)){
            if($request->get('tipoPago')=='CREDITO'){
                $detalleLista = Detalle_Lista::PrecioCliente($cliente->listaPrecio->lista_id,$request->get('producto'),$request->get('plazo'))->first();     
                if($detalleLista){
                    $precio = $detalleLista->detallel_valor;
                }else{
                    $precioProducto = Precio_Producto::PrecioByProducto($request->get('producto'),$request->get('plazo'))->first();
                    if($precioProducto){
                        $precio = $precioProducto->precio_valor;
                    }
                } 
            }else{
                $detalleLista = Detalle_Lista::PrecioCliente($cliente->listaPrecio->lista_id,$request->get('producto'),0)->first();     
                if($detalleLista){
                    $precio = $detalleLista->detallel_valor;
                }else{
                    $precioProducto = Precio_Producto::PrecioByProducto($request->get('producto'),0)->first();
                    if($precioProducto){
                        $precio = $precioProducto->precio_valor;
                    }else{
                        $precio = Producto::findOrFail($request->get('producto'))->producto_precio1;
                    }
                }   
            }
        }else{
            if($request->get('tipoPago')=='CREDITO'){
                $precioProducto = Precio_Producto::PrecioByProducto($request->get('producto'),$request->get('plazo'))->first();
                if($precioProducto){
                    $precio = $precioProducto->precio_valor;
                }
            }else{
                $precioProducto = Precio_Producto::PrecioByProducto($request->get('producto'),0)->first();
                if($precioProducto){
                    $precio = $precioProducto->precio_valor;
                }else{
                    $precio = Producto::findOrFail($request->get('producto'))->producto_precio1;
                }
            }
        }
        return $precio;
    }
    public function servicios(Request $request){
        return Producto::Servicios($request->get('paciente'),$request->get('especialidad'))->get();
    }
    public function excelProducto(){
        try{
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
            $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            return view('admin.inventario.producto.cargarExcel',['PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        }
        catch(\Exception $ex){      
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function CargarExcelProducto(Request $request){
        if (isset($_POST['cargar'])){
            return $this->cargarguardar($request);
        }
        if (isset($_POST['guardar'])){
            return $this->cargarguardar($request);
        }
    }
    public function cargar(Request $request){
        try{
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
            $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $datos = null;
            $count = 1;
            if($request->file('excelProd')->isValid()){
                $empresa = Empresa::empresa()->first();
                $name = $empresa->empresa_ruc. '.' .$request->file('excelProd')->getClientOriginalExtension();
                $path = $request->file('excelProd')->move(public_path().'\temp', $name); 
                $array = Excel::toArray(new Producto, $path);   
                for($i=1;$i < count($array[0]);$i++){
                    $datos[$count]['cod'] = $array[0][$i][0];
                    $datos[$count]['nom'] = $array[0][$i][1];
                    $datos[$count]['bar'] = $array[0][$i][2];
                    $datos[$count]['tip'] = $array[0][$i][3];
                    $datos[$count]['cos'] = $array[0][$i][4];
                    $datos[$count]['min'] = $array[0][$i][5];
                    $datos[$count]['max'] = $array[0][$i][6];

                    $Excel_date = $array[0][$i][7]; 
                    $unix_date = ($Excel_date - 25569) * 86400;
                    $Excel_date = 25569 + ($unix_date / 86400);
                    $unix_date = ($Excel_date - 25569) * 86400;
                    $datos[$count]['fec'] = gmdate("Y-m-d", $unix_date);

                    $datos[$count]['iva'] = $array[0][$i][8];
                    $datos[$count]['des'] = $array[0][$i][9];
                    $datos[$count]['ser'] = $array[0][$i][10];
                    $datos[$count]['com'] = $array[0][$i][11];
                    $datos[$count]['gru'] = $array[0][$i][12];
                    $datos[$count]['cat'] = $array[0][$i][13];

                    $datos[$count]['mar'] = $array[0][$i][14];
                    $datos[$count]['uni'] = $array[0][$i][15];
                    $datos[$count]['tam'] = $array[0][$i][16];
                    $datos[$count]['pre'] = $array[0][$i][17];
                    $datos[$count]['uti'] = $array[0][$i][18];
                    $datos[$count]['dec'] = $array[0][$i][19];

                    $datos[$count]['cuentaventa'] = $array[0][$i][20];
                    $datos[$count]['cuentainventario'] = $array[0][$i][21];
                    $datos[$count]['cuentagasto'] = $array[0][$i][22];
                    $datos[$count]['sucursal'] = $array[0][$i][23];
                    
                    $count ++;
                }
            }
            return view('admin.inventario.producto.cargarExcel',['datos'=>$datos,'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        }
        catch(\Exception $ex){      
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function guardarSinUsar(Request $request){
        try{
            $cod = $request->get('idCod');
            $nom = $request->get('idNom');
            $bar = $request->get('idBar');
            $tip = $request->get('idTip');
            $cos = $request->get('idCos');
            $min = $request->get('idMin');
            $max = $request->get('idMax');
            $fec = $request->get('idFec');
            $iva = $request->get('idIva');
            $des = $request->get('idDes');
            $ser = $request->get('idSer');
            $com = $request->get('idCom');
            $gru = $request->get('idGru');
            $cat = $request->get('idCat');
            $mar = $request->get('idMar');
            $uni = $request->get('idUni');
            $tam = $request->get('idTam');
            $pre = $request->get('idPre');
            $uti = $request->get('idUti');
            $dec = $request->get('idDec');
            $ctaventa = $request->get('idCuentaventa');
            $ctagasto = $request->get('idCuentagasto');
            $ctainventario = $request->get('idCuentainventario');
            $sucursal = $request->get('idSucursal');
            $mensaje='';            
            DB::beginTransaction();
            if($cod){
                for ($i = 0; $i < count($cod); ++$i){
                    $prodcutoCod = Producto::ProductoCodigo($cod[$i])->first();
                    if(isset($prodcutoCod->producto_codigo)){                        
                        $mensaje = $mensaje.' '.$cod[$i];                        
                    }else{
                        $producto = new Producto();
                        $producto->producto_codigo = $cod[$i];
                        $producto->producto_nombre = $nom[$i];
                        if($bar[$i]==''){
                            $producto->producto_codigo_barras = 0;
                        }else{
                            $producto->producto_codigo_barras = $bar[$i];
                        }
                        
                        if($tip[$i] == 'Articulo'){
                            $producto->producto_tipo = '1';
                        }
                        if($tip[$i] == 'Servicio'){
                            $producto->producto_tipo = '2';
                        }
                        $producto->producto_precio_costo = $cos[$i];       
                        $producto->producto_stock = "0";
                        $producto->producto_stock_minimo = $min[$i];
                        $producto->producto_stock_maximo = $max[$i];
                        $producto->producto_fecha_ingreso = $fec[$i];
                        if ($iva[$i] == "SI"){
                            $producto->producto_tiene_iva ="1";
                        }else{
                            $producto->producto_tiene_iva ="0";
                        }
                        if ($des[$i] == "SI"){
                            $producto->producto_tiene_descuento ="1";
                        }else{
                            $producto->producto_tiene_descuento ="0";
                        }
                        if ($ser[$i] == "SI"){
                            $producto->producto_tiene_serie ="1";
                        }else{
                            $producto->producto_tiene_serie ="0";
                        }         
                        if($com[$i] == 'Compra'){
                            $producto->producto_compra_venta = '1';
                        }
                        if($com[$i] == 'Venta'){
                            $producto->producto_compra_venta = '2';
                        }
                        if($com[$i] == 'Compra/Venta'){
                            $producto->producto_compra_venta = '3';
                        }
                        if(isset($pre[$i])){                            
                            $producto->producto_precio1 = $pre[$i];
                        }else{                            
                            $producto->producto_precio1 = 0;
                        }
                        
                        $producto->producto_estado = 1;
                        $produCategoria =  Categoria_Producto::categoriaByName($cat[$i])->first();
                        if(isset($produCategoria->categoria_id)){
                            $producto->categoria_id = $produCategoria->categoria_id;
                        }else{
                            $categoria = new Categoria_Producto();
                            $categoria->categoria_nombre = strtoupper($cat[$i]);
                            $categoria->categoria_tipo = 'Articulo';                                                            
                            $categoria->categoria_estado  = 1;
                            $categoria->empresa_id = Auth::user()->empresa_id;
                            $categoria->save();
                            /*Inicio de registro de auditoria */
                            $auditoria = new generalController();
                            $auditoria->registrarAuditoria('Registro de Categoria de producto -> '.$cat[$i],'0','');
                            $producto->categoria()->associate($categoria);
                        }
                        $marca = Marca_Producto::marcaByName($mar[$i])->first();
                        if(isset($marca->marca_id)){
                            $producto->marca_id = $marca->marca_id;
                        }else{
                            $produMarca = new Marca_Producto();
                            $produMarca->marca_nombre = strtoupper($mar[$i]);                                                    
                            $produMarca->marca_estado  = 1;
                            $produMarca->empresa_id = Auth::user()->empresa_id;
                            $produMarca->save();
                            /*Inicio de registro de auditoria */
                            $auditoria = new generalController();
                            $auditoria->registrarAuditoria('Registro de Marca de Prodcuto -> '.$mar[$i],'0','');
                            $producto->marca()->associate($produMarca);

                        }
                        $unidadMedidaProdu = Unidad_Medida_Producto::unidadByName($uni[$i])->first();                       
                        if(isset($unidadMedidaProdu->unidad_medida_id)){                            
                            $producto->unidad_medida_id = $unidadMedidaProdu->unidad_medida_id;
                        }else{                            
                            $medidaProdu = new Unidad_Medida_Producto();
                            $medidaProdu->unidad_medida_nombre = strtoupper($uni[$i]);                                                    
                            $medidaProdu->unidad_medida_estado  = 1;
                            $medidaProdu->empresa_id = Auth::user()->empresa_id;                           
                            $medidaProdu->save();
                            $producto->unidadMedida()->associate($medidaProdu);
                            /*Inicio de registro de auditoria */
                            $auditoria = new generalController();
                            $auditoria->registrarAuditoria('Registro de Unidad de Medida de producto -> '.$uni[$i],'0','');
                        }
                        $producto->empresa_id = Auth::user()->empresa_id;
                        $tamanoProd =  Tamano_Producto::tamanoByName($tam[$i])->first();
                        if(isset($tamanoProd->tamano_id)){
                            $producto->tamano_id  = $tamanoProd->tamano_id;
                        }else{
                            $produTamanio = new Tamano_Producto();
                            $produTamanio->tamano_nombre = $tam[$i];                                                    
                            $produTamanio->tamano_estado  = 1;
                            $produTamanio->empresa_id = Auth::user()->empresa_id;
                            $produTamanio->save();
                            /*Inicio de registro de auditoria */
                            $auditoria = new generalController();
                            $auditoria->registrarAuditoria('Registro de Tamanio de producto -> '.$mar[$i],'0','');
                            $producto->tamano()->associate($produTamanio);
                            
                        }
                        $produGrupo = Grupo_Producto::grupoByName($gru[$i])->first();
                        if(isset($produGrupo->grupo_id)){
                            $producto->grupo_id  = $produGrupo->grupo_id;
                        }else{
                            $grupoProducto = new Grupo_Producto();
                            $grupoProducto->grupo_nombre = strtoupper($gru[$i]);                                                    
                            $grupoProducto->grupo_estado  = 1;
                            $grupoProducto->empresa_id = Auth::user()->empresa_id;
                            $grupoProducto->save();
                            /*Inicio de registro de auditoria */
                            $auditoria = new generalController();
                            $auditoria->registrarAuditoria('Registro de grupo de producto -> '.$gru[$i],'0','');
                            $producto->grupo()->associate($grupoProducto);
                        }
                        $sucursalProdu = Sucursal::SucursalByNombre($sucursal[$i])->first();
                        if(isset($sucursalProdu->sucursal_id)){
                            $producto->sucursal_id = $sucursalProdu->sucursal_id;
                        }
                        if((substr($ctainventario[$i], 0, 1) == '1')){
                            $numeroCuentaInventario = Cuenta::CuentaByNumero($ctainventario[$i])->first();
                            if(isset($numeroCuentaInventario->cuenta_id)){
                                $producto->producto_cuenta_inventario = $numeroCuentaInventario->cuenta_id;
                                $numeroCuentaVenta = Cuenta::CuentaByNumero($ctaventa[$i])->first();
                                if(isset($numeroCuentaVenta->cuenta_id)){
                                    $producto->producto_cuenta_venta= $numeroCuentaVenta->cuenta_id;
                                }
                            }
                        }else{
                            $numeroCuentaVenta = Cuenta::CuentaByNumero($ctaventa[$i])->first();
                            if(isset($numeroCuentaVenta->cuenta_id)){
                                $producto->producto_cuenta_venta= $numeroCuentaVenta->cuenta_id;
                            }
                            $numeroCuentaGasto = Cuenta::CuentaByNumero($ctagasto[$i])->first();
                            if(isset($numeroCuentaGasto->cuenta_id)){
                                $producto->producto_cuenta_gasto= $numeroCuentaGasto->cuenta_id;
                            }
                        } 
                        $producto->save();
                        /*Inicio de registro de auditoria */
                        $auditoria = new generalController();
                        $auditoria->registrarAuditoria('Registro de producto -> '.$request->get('producto_nombre').'con codigo de ->'.$request->get('producto_codigo').' mediante Excel.','0','');
                        /*Fin de registro de auditoria */
                  }  
                }
            }
            DB::commit();
            if($mensaje ==''){
                return redirect('producto')->with('success','Datos guardados exitosamente');               
            }else{
                return redirect('producto')->with('success','Datos guardados exitosamente')->with('error2','Algunos Datos no se registraron codigo repetido: '.' '.$mensaje);
            }
           
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('producto')->with('error2','Oucrrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function cargarguardar(Request $request){
        try{
            $mensaje='';            
            DB::beginTransaction();
            if($request->file('excelProd')->isValid()){
                $empresa = Empresa::empresa()->first();
                $name = $empresa->empresa_ruc. '.' .$request->file('excelProd')->getClientOriginalExtension();
                $path = $request->file('excelProd')->move(public_path().'\temp', $name); 
                $array = Excel::toArray(new Producto, $path);   
                for($i=1;$i < count($array[0]);$i++){
                    $prodcutoCod = Producto::ProductoCodigo($array[0][$i][0])->first();
                    if(isset($prodcutoCod->producto_codigo) or $array[0][$i][1]==''){                                             
                        $mensaje = $mensaje.' '.$array[0][$i][0];                        
                    }else{
                        $producto = new Producto();
                        $producto->producto_codigo = $array[0][$i][0];
                        $producto->producto_nombre = $array[0][$i][1];
                        if($array[0][$i][2]==''){
                            $producto->producto_codigo_barras = 0;
                        }else{
                            $producto->producto_codigo_barras = $array[0][$i][2];
                        }
                        
                        if($array[0][$i][3] == 'Articulo'){
                            $producto->producto_tipo = '1';
                        }
                        if($array[0][$i][3] == 'Servicio'){
                            $producto->producto_tipo = '2';
                        }
                        $producto->producto_precio_costo = $array[0][$i][4];       
                        $producto->producto_stock = "0";
                        $producto->producto_stock_minimo = $array[0][$i][5];
                        $producto->producto_stock_maximo = $array[0][$i][6];
                        $Excel_date = $array[0][$i][7]; 
                        $unix_date = ($Excel_date - 25569) * 86400;
                        $Excel_date = 25569 + ($unix_date / 86400);
                        $unix_date = ($Excel_date - 25569) * 86400;
                        $producto->producto_fecha_ingreso = gmdate("Y-m-d", $unix_date);
                        if ($array[0][$i][8] == "SI"){
                            $producto->producto_tiene_iva ="1";
                        }else{
                            $producto->producto_tiene_iva ="0";
                        }
                        if ($array[0][$i][9] == "SI"){
                            $producto->producto_tiene_descuento ="1";
                        }else{
                            $producto->producto_tiene_descuento ="0";
                        }
                        if ($array[0][$i][10] == "SI"){
                            $producto->producto_tiene_serie ="1";
                        }else{
                            $producto->producto_tiene_serie ="0";
                        }         
                        if($array[0][$i][11] == 'Compra'){
                            $producto->producto_compra_venta = '1';
                        }
                        if($array[0][$i][11] == 'Venta'){
                            $producto->producto_compra_venta = '2';
                        }
                        if($array[0][$i][11] == 'Compra/Venta'){
                            $producto->producto_compra_venta = '3';
                        }
                        if(isset($array[0][$i][17])){                            
                            $producto->producto_precio1 =  floatval($array[0][$i][17]);
                        }else{                            
                            $producto->producto_precio1 = 0;
                        }
                        
                        $producto->producto_estado = 1;
                        $produCategoria =  Categoria_Producto::categoriaByName($array[0][$i][13])->first();
                        if(isset($produCategoria->categoria_id)){
                            $producto->categoria_id = $produCategoria->categoria_id;
                        }else{
                            $categoria = new Categoria_Producto();
                            $categoria->categoria_nombre = strtoupper($array[0][$i][13]);
                            $categoria->categoria_tipo = 'Articulo';                                                            
                            $categoria->categoria_estado  = 1;
                            $categoria->empresa_id = Auth::user()->empresa_id;
                            $categoria->save();
                            /*Inicio de registro de auditoria */
                            $auditoria = new generalController();
                            $auditoria->registrarAuditoria('Registro de Categoria de producto -> '.$array[0][$i][13],'0','');
                            $producto->categoria()->associate($categoria);
                        }
                        $marca = Marca_Producto::marcaByName($array[0][$i][14])->first();
                        if(isset($marca->marca_id)){
                            $producto->marca_id = $marca->marca_id;
                        }else{
                            $produMarca = new Marca_Producto();
                            $produMarca->marca_nombre = strtoupper($array[0][$i][14]);                                                    
                            $produMarca->marca_estado  = 1;
                            $produMarca->empresa_id = Auth::user()->empresa_id;
                            $produMarca->save();
                            /*Inicio de registro de auditoria */
                            $auditoria = new generalController();
                            $auditoria->registrarAuditoria('Registro de Marca de Prodcuto -> '.$array[0][$i][14],'0','');
                            $producto->marca()->associate($produMarca);

                        }
                        $unidadMedidaProdu = Unidad_Medida_Producto::unidadByName($array[0][$i][15])->first();                       
                        if(isset($unidadMedidaProdu->unidad_medida_id)){                            
                            $producto->unidad_medida_id = $unidadMedidaProdu->unidad_medida_id;
                        }else{                            
                            $medidaProdu = new Unidad_Medida_Producto();
                            $medidaProdu->unidad_medida_nombre = strtoupper($array[0][$i][15]);                                                    
                            $medidaProdu->unidad_medida_estado  = 1;
                            $medidaProdu->empresa_id = Auth::user()->empresa_id;                           
                            $medidaProdu->save();
                            $producto->unidadMedida()->associate($medidaProdu);
                            /*Inicio de registro de auditoria */
                            $auditoria = new generalController();
                            $auditoria->registrarAuditoria('Registro de Unidad de Medida de producto -> '.$array[0][$i][15],'0','');
                        }
                        $producto->empresa_id = Auth::user()->empresa_id;
                        $tamanoProd =  Tamano_Producto::tamanoByName($array[0][$i][16])->first();
                        if(isset($tamanoProd->tamano_id)){
                            $producto->tamano_id  = $tamanoProd->tamano_id;
                        }else{
                            $produTamanio = new Tamano_Producto();
                            $produTamanio->tamano_nombre = $array[0][$i][16];                                                    
                            $produTamanio->tamano_estado  = 1;
                            $produTamanio->empresa_id = Auth::user()->empresa_id;
                            $produTamanio->save();
                            /*Inicio de registro de auditoria */
                            $auditoria = new generalController();
                            $auditoria->registrarAuditoria('Registro de Tamanio de producto -> '.$array[0][$i][16],'0','');
                            $producto->tamano()->associate($produTamanio);
                            
                        }
                        $produGrupo = Grupo_Producto::grupoByName($array[0][$i][12])->first();
                        if(isset($produGrupo->grupo_id)){
                            $producto->grupo_id  = $produGrupo->grupo_id;
                        }else{
                            $grupoProducto = new Grupo_Producto();
                            $grupoProducto->grupo_nombre = strtoupper($array[0][$i][12]);                                                    
                            $grupoProducto->grupo_estado  = 1;
                            $grupoProducto->empresa_id = Auth::user()->empresa_id;
                            $grupoProducto->save();
                            /*Inicio de registro de auditoria */
                            $auditoria = new generalController();
                            $auditoria->registrarAuditoria('Registro de grupo de producto -> '.$array[0][$i][12],'0','');
                            $producto->grupo()->associate($grupoProducto);
                        }
                        $sucursalProdu = Sucursal::SucursalByNombre($array[0][$i][23])->first();
                        if(isset($sucursalProdu->sucursal_id)){
                            $producto->sucursal_id = $sucursalProdu->sucursal_id;
                        }
                        $numeroCuentaInventario = Cuenta::CuentaByNumero($array[0][$i][21])->first();
                        if(isset($numeroCuentaInventario->cuenta_id)){
                            $producto->producto_cuenta_inventario= $numeroCuentaInventario->cuenta_id;
                        }
                        $numeroCuentaVenta = Cuenta::CuentaByNumero($array[0][$i][20])->first();
                        if(isset($numeroCuentaVenta->cuenta_id)){
                            $producto->producto_cuenta_venta= $numeroCuentaVenta->cuenta_id;
                        }
                        $numeroCuentaGasto = Cuenta::CuentaByNumero($array[0][$i][22])->first();
                        if(isset($numeroCuentaGasto->cuenta_id)){
                                $producto->producto_cuenta_gasto= $numeroCuentaGasto->cuenta_id;
                            }
                        $producto->save();
                        /*Inicio de registro de auditoria */
                        $auditoria = new generalController();
                        $auditoria->registrarAuditoria('Registro de producto -> '.$request->get('producto_nombre').'con codigo de ->'.$request->get('producto_codigo').' mediante Excel.','0','');
                        /*Fin de registro de auditoria */
                  }  
                }
            }
           DB::commit();
            if($mensaje ==''){
                return redirect('producto')->with('success','Datos guardados exitosamente');               
            }else{
                return redirect('producto')->with('success','Datos guardados exitosamente')->with('error2','Algunos Datos no se registraron codigo repetido: '.' '.$mensaje);
            }
           
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('producto')->with('error2','Oucrrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function nuevoPrecio($id){
        try{
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
            $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $producto=Producto::producto($id)->first();
            if($producto){
                return view('admin.inventario.producto.precioProducto',['producto'=>$producto,
                'PE'=>Punto_Emision::puntos()->get(),
                'gruposPermiso'=>$gruposPermiso,
                'permisosAdmin'=>$permisosAdmin]);
            }else{
                return redirect('/denegado');
            }
        }
        catch(\Exception $ex){      
            return redirect('producto')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function nuevoCodigo($id){
        try{
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
            $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $producto=Producto::producto($id)->first();
            $proveedores=Proveedor::Proveedores()->get();
            if($producto){
                return view('admin.inventario.producto.codigoProveedor',['producto'=>$producto,'proveedores'=>$proveedores,
                'PE'=>Punto_Emision::puntos()->get(),
                'gruposPermiso'=>$gruposPermiso,
                'permisosAdmin'=>$permisosAdmin]);
            }else{
                return redirect('/denegado');
            }
        }
        catch(\Exception $ex){      
            return redirect('producto')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function guardarCodigo(Request $request){
        try {
            DB::beginTransaction();
            $nombre = $request->get('DLdias');
            $provedor = $request->get('idpr');
            $producto = Producto::findOrFail($request->get('idProducto'));
            $codigo=Codigo_Producto::where('producto_id','=',$producto->producto_id)->delete();
            for ($i=1; $i < count($nombre); $i++) { 
                $codigo = new Codigo_Producto();
                $codigo->codigo_nombre = $nombre[$i];
                $codigo->proveedor_id = $provedor[$i];
                $codigo->codigo_estado = 1;
                $codigo->producto_id = $producto->producto_id;
                $codigo->save();
            }
            $auditoria = new generalController();
            $auditoria->registrarAuditoria('Registro de Codigo a producto-> '.$producto->producto_nomrbe,'0','');
            DB::commit();
            return redirect('producto')->with('success','Datos guardados exitosamente');
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('producto')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function guardarPrecio(Request $request){
        try {
            DB::beginTransaction();
            $dias = $request->get('DLdias');
            $valor = $request->get('DLvalor');
            $producto = Producto::findOrFail($request->get('idProducto'));
            $productoaux = Producto::findOrFail($request->get('idProducto'));
            $precio=Precio_Producto::where('producto_id','=',$producto->producto_id)->delete();
            for ($i=1; $i < count($dias); $i++) { 
                $precio = new Precio_Producto();
                $precio->precio_dias = $dias[$i];
                $precio->precio_valor = $valor[$i];
                $precio->precio_estado = 1;
                $precio->producto_id = $producto->producto_id;
                $precio->save();
            }
            $auditoria = new generalController();
            $auditoria->registrarAuditoria('Registro de precios a producto-> '.$producto->producto_nomrbe,'0','');
            if (isset(Auth::user()->empresa->grupo)) {
                foreach (Auth::user()->empresa->grupo->usuarios->empresas as $empresas) {
                    if (Auth::user()->empresa->empresa_id != $empresas->empresa->empresa_id) {
                        if (Auth::user()->empresa->grupo->grupo_duplicado == '1') {
                           
                                $productoaux=Producto::ProductoEmpresa($productoaux->producto_codigo,$empresas->empresa->empresa_id)->first();
                                $producto = Producto::findOrFail($productoaux->producto_id);
                                $precio=Precio_Producto::where('producto_id','=',$producto->producto_id)->delete();
                                for ($i=1; $i < count($dias); $i++) { 
                                    $precio = new Precio_Producto();
                                    $precio->precio_dias = $dias[$i];
                                    $precio->precio_valor = $valor[$i];
                                    $precio->precio_estado = 1;
                                    $precio->producto_id = $producto->producto_id;
                                    $precio->save();
                                }
                            
                        }
                        
                    }
                }
            }
            DB::commit();
            return redirect('producto')->with('success','Datos guardados exitosamente');
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('producto')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function buscarByProducto($buscar){
        return Producto::Producto($buscar)->get();
    }

    public function buscarProductoByNombre(Request $request){
        return Producto::productosByNombreStock($request->buscar)
            ->limit(10)
            ->get();
    }
}
