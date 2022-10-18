<?php

namespace App\Http\Controllers;

use App\Models\Anticipo_Proveedor;
use App\Models\Descuento_Anticipo_Proveedor;
use App\Models\Empresa;
use App\Models\Proveedor;
use App\Models\Punto_Emision;
use DateTime;
use PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class listaAnticipoProveedorController extends Controller
{
    public function nuevo()
    {
        try{  
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            return view('admin.cuentasPagar.listaAnticipo.index',['proveedores'=>Proveedor::ProveedoresAnticipos()->get(),'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        }
        catch(\Exception $ex){      
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function consultar(Request $request)
    {   
        if (isset($_POST['buscar'])){
            return $this->buscar($request);
        }
        if (isset($_POST['pdf'])){
            return $this->pdf($request);
        }
    }
    public function buscar(Request $request){
        try{   
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $count = 1;
            $countProveedor = 0;
            $datos = null;
            $totMon= 0;
            $totPag= 0;
            $totSal = 0;
            $saldo_cero = 0;
            if ($request->get('saldo_cero') == "on"){
                $saldo_cero = 1; 
            }
            if($request->get('proveedorID') == "0"){
                $proveedores = Proveedor::ProveedoresAnticipos()->get();
            }else{
                $proveedores = Proveedor::proveedor($request->get('proveedorID'))->get();
            }
            foreach($proveedores as $proveedor){
                $datos[$count]['ben'] = $proveedor->proveedor_nombre; 
                $datos[$count]['mon'] = Anticipo_Proveedor::AnticiposByProveedorFecha($proveedor->proveedor_id, $request->get('idCorte'))->sum('anticipo_valor'); 
                $datos[$count]['pag'] = Descuento_Anticipo_Proveedor::DescuentosAnticipoByProveedorFecha($proveedor->proveedor_id, $request->get('idCorte'))->sum('descuento_valor');
                $datos[$count]['sal'] = floatval($datos[$count]['mon']) - floatval($datos[$count]['pag']) - floatval(Anticipo_Proveedor::AnticiposByProveedorFecha($proveedor->proveedor_id, $request->get('idCorte'))->whereNull('anticipo_documento')->sum('anticipo_valor')) + floatval(Anticipo_Proveedor::AnticiposByProveedorFecha($proveedor->proveedor_id, $request->get('idCorte'))->whereNull('anticipo_documento')->sum('anticipo_saldom')); 
                $datos[$count]['fec'] = ''; 
                $datos[$count]['fep'] = ''; 
                $datos[$count]['dir'] = ''; 
                $datos[$count]['tip'] = ''; 
                $datos[$count]['fac'] = ''; 
                $datos[$count]['tot'] = '1'; 
                $totMon = $totMon + floatval($datos[$count]['mon']);
                $totSal = $totSal + floatval($datos[$count]['sal']);
                $totPag = $totPag + floatval($datos[$count]['pag']);
                $count ++;
                $countProveedor = $count - 1;
                foreach(Anticipo_Proveedor::AnticiposByProveedorFecha($proveedor->proveedor_id, $request->get('idCorte'))->get() as $anticipo){
                    $datos[$count]['ben'] = ''; 
                    $datos[$count]['mon'] = $anticipo->anticipo_valor; 
                    $datos[$count]['pag'] = '';
                    if(is_null($anticipo->anticipo_documento)){
                        $datos[$count]['sal'] = floatval($anticipo->anticipo_saldom) - Descuento_Anticipo_Proveedor::DescuentosAnticipo($anticipo->anticipo_id, $request->get('idCorte'))->sum('descuento_valor'); 
                    }else{
                        $datos[$count]['sal'] = floatval($datos[$count]['mon']) - Descuento_Anticipo_Proveedor::DescuentosAnticipo($anticipo->anticipo_id, $request->get('idCorte'))->sum('descuento_valor'); 
                    }                    
                    $datos[$count]['fec'] = $anticipo->anticipo_fecha; 
                    $datos[$count]['fep'] = ''; 
                    $datos[$count]['dir'] = $anticipo->diario->diario_codigo; 
                    $datos[$count]['tip'] = $anticipo->anticipo_tipo.' - '.$anticipo->anticipo_documento; 
                    $datos[$count]['fac'] = ''; 
                    $datos[$count]['tot'] = '0'; 
                    $count ++;
                    if($datos[$count-1]['sal'] == 0  && $saldo_cero == 0){
                        $datos[$countProveedor]['mon'] = floatval($datos[$countProveedor]['mon']) - floatval($datos[$count-1]['mon']);
                        $datos[$countProveedor]['pag'] = floatval($datos[$countProveedor]['pag']) - Descuento_Anticipo_Proveedor::DescuentosAnticipo($anticipo->anticipo_id, $request->get('idCorte'))->sum('descuento_valor');
                        array_pop($datos);
                        $count = $count - 1;
                    }else{
                        foreach(Descuento_Anticipo_Proveedor::DescuentosAnticipo($anticipo->anticipo_id, $request->get('idCorte'))->select('descuento_valor','descuento_fecha','descuento_anticipo_proveedor.diario_id','descuento_anticipo_proveedor.transaccion_id','descuento_descripcion')->get() as $descuento){
                            $datos[$count]['ben'] = ''; 
                            $datos[$count]['mon'] = ''; 
                            $datos[$count]['sal'] = ''; 
                            $datos[$count]['fec'] = ''; 
                            $datos[$count]['pag'] = $descuento->descuento_valor;
                            $datos[$count]['fep'] = $descuento->descuento_fecha; 
                            $datos[$count]['dir'] = $descuento->diario->diario_codigo; 
                            $datos[$count]['tip'] = '';
                            if(isset($descuento->transaccionCompra)){
                                $datos[$count]['fac'] = $descuento->transaccionCompra->transaccion_numero; 
                            }else{
                                $datos[$count]['fac'] = $descuento->descuento_descripcion;
                            }
                            
                            $datos[$count]['tot'] = '2'; 
                            $count ++;
                        }
                    }
                }
                if( $datos[$count-1]['tot'] == '1' ){
                    array_pop($datos);
                    $count = $count - 1;
                }
            }
            return view('admin.cuentasPagar.listaAnticipo.index',['saldo_cero'=>$saldo_cero,'proveedorC'=>$request->get('proveedorID'),'pag'=>$totPag,'monto'=>$totMon, 'saldo'=>$totSal,'fCorte'=>$request->get('idCorte'),'datos'=>$datos,'proveedores'=>Proveedor::ProveedoresAnticipos()->get(),'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);      
        }catch(\Exception $ex){
            return redirect('listaAnticipoProveedor')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }   
    }
    public function pdf(Request $request){
        try{            
            $datos = null;
            $count = 1;
            $ben = $request->get('idBen');
            $mon = $request->get('idMon');
            $sal = $request->get('idSal');
            $fec = $request->get('idFec');
            $pag = $request->get('idPag');
            $fep = $request->get('idFep');
            $dir = $request->get('idDir');
            $tip = $request->get('idTip');
            $fac = $request->get('idFac');
            $tot = $request->get('idTot');
            if($ben){
                for ($i = 0; $i < count($ben); ++$i){
                    $datos[$count]['ben'] = $ben[$i];
                    $datos[$count]['mon'] = $mon[$i]; 
                    $datos[$count]['sal'] = $sal[$i]; 
                    $datos[$count]['fec'] = $fec[$i]; 
                    $datos[$count]['pag'] = $pag[$i];
                    $datos[$count]['fep'] = $fep[$i]; 
                    $datos[$count]['dir'] = $dir[$i]; 
                    $datos[$count]['tip'] = $tip[$i]; 
                    $datos[$count]['fac'] = $fac[$i]; 
                    $datos[$count]['tot'] = $tot[$i]; 
                    $count ++;
                }
            }
            $empresa =  Empresa::empresa()->first();
            $ruta = public_path().'/PDF/'.$empresa->empresa_ruc;
            if (!is_dir($ruta)) {
                mkdir($ruta, 0777, true);
            }
            $view =  \View::make('admin.formatosPDF.listaAnticipoProveedor', ['pag'=>$request->get('idPago'),'monto'=>$request->get('idMonto'), 'saldo'=>$request->get('idSaldo'),'datos'=>$datos,'fCorte'=>DateTime::createFromFormat('Y-m-d', $request->get('idCorte'))->format('d/m/Y'),'empresa'=>$empresa]);
            $nombreArchivo = 'LISTA DE ANTICIPOS A PROVEEDORES AL '.DateTime::createFromFormat('Y-m-d', $request->get('idCorte'))->format('d-m-Y');
            return PDF::loadHTML($view)->save('PDF/'.$empresa->empresa_ruc.'/'.$nombreArchivo.'.pdf')->download($nombreArchivo.'.pdf');
        }catch(\Exception $ex){
            return redirect('listaAnticipoProveedor')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
}
