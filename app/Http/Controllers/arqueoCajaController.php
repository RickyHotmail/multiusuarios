<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Arqueo_Caja;
use App\Models\Caja;
use App\Models\Caja_Usuario;
use App\Models\Punto_Emision;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use function PHPUnit\Framework\isEmpty;

class arqueoCajaController extends Controller
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
            $cajasxusuarios = Caja_Usuario::CajaXusuario(Auth::user()->user_id)->get();        
            $cajas = Caja::cajas()->get();
            $cajaAbierta = Arqueo_Caja::ArqueoCajaxuser(Auth::user()->user_id)->get();
            if(count($cajaAbierta) == 0){            
                if(count($cajasxusuarios)>0){
                    return view('admin.caja.arqueoCaja.index',['cajas'=>$cajas,'cajasxusuarios'=>$cajasxusuarios, 'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
                }else{
                    return redirect('inicio')->with('error2','No tiene cajas asignadas, Solicite una caja con el administrador y vuelva a intentar');            
                }
            }else{
                return redirect('inicio')->with('error2','Usted ya cuenta con una caja abierta, Cierra su caja actual si desea abrir otra !!!');            
            }
        }catch(\Exception $ex){
           
            return redirect('inicio')->with('error','Ocurrio un error vuelva a intentarlo');
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return redirect('/denegado');
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
            $arqueoCajass=Arqueo_Caja::arqueoCajaxcaja($request->get('idCaja'))->first(); 

            if($arqueoCajass){
                return redirect('arqueoCaja')->with('error','La caja actual ya se encuentra aperturada.');
            }else{
                DB::beginTransaction();
                $arqueoCaja = new Arqueo_Caja();
                $caja=Caja::findOrFail($request->get('idCaja'));
                $arqueoCaja->arqueo_fecha=date("Y")."-".date("m")."-".date("d");
                $general = new generalController();
                $cierre = $general->cierre($arqueoCaja->arqueo_fecha,$caja->sucursal_id);
                if($cierre){
                    return redirect('arqueoCaja')->with('error2','No puede realizar la operacion por que pertenece a un mes bloqueado');
                }
                $arqueoCaja->arqueo_hora=date("H:i:s");
                $arqueoCaja->arqueo_observacion= $request->get('idMensaje');
                $arqueoCaja->arqueo_tipo="APERTURA";
                $arqueoCaja->arqueo_saldo_inicial=$request->get('idMonto');    
                $arqueoCaja->arqueo_monto= $request->get('idMonto');                        
                $arqueoCaja->arqueo_billete1= $request->get('billete1');
                $arqueoCaja->arqueo_billete5= $request->get('billete5');
                $arqueoCaja->arqueo_billete10= $request->get('billete10');
                $arqueoCaja->arqueo_billete20= $request->get('billete20');
                $arqueoCaja->arqueo_billete50= $request->get('billete50');
                $arqueoCaja->arqueo_billete100= $request->get('billete100');
                $arqueoCaja->arqueo_moneda01= $request->get('moneda01');
                $arqueoCaja->arqueo_moneda05= $request->get('moneda05');
                $arqueoCaja->arqueo_moneda10= $request->get('moneda10');
                $arqueoCaja->arqueo_moneda25= $request->get('moneda25');
                $arqueoCaja->arqueo_moneda50= $request->get('moneda50');
                $arqueoCaja->arqueo_moneda1= $request->get('moneda1');
                $arqueoCaja->arqueo_estado='1';
                $arqueoCaja->empresa_id = Auth::user()->empresa_id;
                $arqueoCaja->caja_id= $request->get('idCaja');
                $arqueoCaja->user_id=Auth::user()->user_id;                    
                $arqueoCaja->save();             
                /*Inicio de registro de auditoria */
                $auditoria = new generalController();
                $auditoria->registrarAuditoria('Se Apertura la Caja -> '.$arqueoCaja->caja->caja_nombre.' Con el Valor de: '.$request->get('idMonto'),'0', 'Por el usuario: '. $arqueoCaja->usuario->user_nombre);
                /*Fin de registro de auditoria */
                DB::commit();
                return redirect('inicio')->with('success','Datos guardados exitosamente');                
            }            
        }catch(\Exception $ex){
          
           return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
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
        return redirect('/denegado');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return redirect('/denegado');
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
        return redirect('/denegado');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return redirect('/denegado');
    }
}
