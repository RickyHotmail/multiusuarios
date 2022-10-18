<?php

namespace App\Http\Controllers;

use App\Models\Bodega;
use App\Models\Tarifa_Iva;
use App\Models\Vendedor;
use App\Models\Cliente;
use App\Models\Forma_Pago;
use App\Models\Guia_Remision;
use App\Models\Orden_Despacho;
use App\Models\Punto_Emision;
use App\Models\Rango_Documento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ordenesDespachoGuiasremision extends Controller
{
   
    public function extraer(Request $request)
    {
        try{      
            $checkboxes = $request->input('checkbox');
            return($checkboxes);
            DB::beginTransaction();           
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $facturaprofo=Orden_Despacho::Proforma($request->get('PROFORMA_ID'))->get()->first();
            $rangoDocumento=Rango_Documento::PuntoRango($request->get('punto_id'), 'Guías de Remisión')->first();
            $secuencial=$rangoDocumento->rango_inicio;;          
            DB::commit();
            if ($rangoDocumento) {
                $secuencialAux=Guia_Remision::secuencial($rangoDocumento->rango_id)->max('gr_secuencial');
                if ($secuencialAux) {
                    $secuencial=$secuencialAux+1;
                }
            }else{
                return redirect('inicio')->with('error','No tiene configurado, un punto de emisión o un rango de documentos para emitir facturas de venta, configueros y vuelva a intentar');
            }
            return view('admin.ventas.ordenesdespacho.Guiasordenes',['vendedores'=>Vendedor::Vendedores()->get(),'clientes'=>Cliente::Clientes()->get(),'tarifasIva'=>Tarifa_Iva::TarifaIvas()->get(),'secuencial'=>substr(str_repeat(0, 9).$secuencial, - 9), 'bodegas'=>Bodega::bodegasSucursal($request->get('punto_id'))->get(),'formasPago'=>Forma_Pago::formaPagos()->get(), 'rangoDocumento'=>$rangoDocumento,'facturaprofo'=>$facturaprofo,'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        }
        catch(\Exception $ex){
            DB::rollBack();
            return redirect('inicio')->with('error','No tiene configurado, un punto de emisión o un rango de documentos para emitir facturas de venta, configueros y vuelva a intentar');
        }
    }
}
