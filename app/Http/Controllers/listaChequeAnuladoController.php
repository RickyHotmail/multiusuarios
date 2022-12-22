<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Banco;
use App\Models\Banco_Lista;
use App\Models\Cheque;
use App\Models\Cuenta_Bancaria;
use App\Models\Documento_Anulado;
use App\Models\Empresa;
use App\Models\Punto_Emision;
use App\Models\Rubro;
use App\Models\Sucursal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Luecano\NumeroALetras\NumeroALetras;
use Maatwebsite\Excel\Facades\Excel;

class listaChequeAnuladoController extends Controller
{
    public function listarChequesAnulados(Request $request){   
        try{ 
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            
            
            $listadoCheques=Cheque::listadoCheques()
            ->where('banco_lista.empresa_id','=',Auth::user()->empresa_id);

            if($request->has("idHasta") && $request->has("idHasta")){
                $listadoCheques->where('cheque_fecha_emision','>=',$request->get('idDesde'))
                               ->where('cheque_fecha_emision','<=',$request->get('idHasta'));
            }

            $listadoCheques->where('cuenta_bancaria.cuenta_bancaria_id','=',$request->get('cuenta_id'))
                           ->where('cheque_estado','=','2')
                           ->orderBy('cheque_numero','asc');        
            
            $data = [
                'estadC'=>$request->get('idTodos'),
                'bancoC'=>$request->get('banco_id'),
                'cuentaBancaria'=>Cuenta_Bancaria::CuentaBancaria($request->get('cuenta_id'))->first(),
                'bancos'=>Banco::bancos()->get(),
                'listadoCheques'=>$listadoCheques->get(),
                'PE'=>Punto_Emision::puntos()->get(),
                'gruposPermiso'=>$gruposPermiso,
                'permisosAdmin'=>$permisosAdmin
            ];

            if($request->has("idHasta") && $request->has("idHasta")){
                $data['fechaI']=$request->get('idDesde');
                $data['fechaF']=$request->get('idHasta');
            }

            return view('admin.bancos.listaChequeAnulado.index', $data);
        }
        catch(\Exception $ex){      
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }      
    }

    public function eliminarChequeAnulado(Request $request){
        try{
            DB::beginTransaction();
            $cheque=Cheque::cheque($request->get("cheque_id"))->first();

            $general = new generalController();
            $auditoria = new generalController();
            $sucursales=Sucursal::Sucursales()->get();
            foreach($sucursales as $sucursal){
                $cierre = $auditoria->cierre($cheque->cheque_fecha_emision,$sucursal->sucursal_id);          
                if($cierre){
                    return redirect('listaChequesAnulados')->with('error2','No puede realizar la operacion por que pertenece a un mes bloqueado');
                }
            }
            
            $general->registrarAuditoria('Eliminado Cheque #'.$cheque->cheque_numero.' con valor de $'.$cheque->cheque_valor.' y cuenta bancaria id: '.$cheque->cuenta_bancaria_id.', beneficiario '.$cheque->cheque_beneficiario, $cheque->cheque_id, $cheque->cheque_descripcion);
            
            $cheque->delete();

            DB::commit();
            return redirect('listaChequesAnulados')->with('success','Documento Eliminado exitosamente');
        }
        catch(\Exception $ex){
            DB::rollBack();
            return redirect('listaChequesAnulados')->with('error2','Ocurrio un error en el proceso de Eliminar el Registro. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
}
