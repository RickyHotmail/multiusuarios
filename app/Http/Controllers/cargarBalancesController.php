<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Cuenta;
use App\Models\Detalle_Diario;
use App\Models\Diario;
use App\Models\Empresa;
use App\Models\Punto_Emision;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class cargarBalancesController extends Controller
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
            $diarios=Diario::DiarioTipoDocumento('CDCO')->get();
            return view('admin.contabilidad.cargarBalance.cargarExcel',['diarios'=>$diarios,'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function CargarExcel(Request $request)
    {
        
        
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
        try{
            DB::beginTransaction();

            if($request->file('excelEmpl')->isValid()){
                $empresa = Empresa::empresa()->first();
                $name = $empresa->empresa_ruc. '.' .$request->file('excelEmpl')->getClientOriginalExtension();
                $path = $request->file('excelEmpl')->move(public_path().'\temp\ativosfijos', $name); 
                $array = Excel::toArray(new Detalle_Diario(), $path); 
                $general = new generalController(); 
                $diario=Diario::findOrFail($request->get('diario'));
                for ($i=1;$i < count($array[0]);$i++) {
                    $validarcuenta=trim($array[0][$i][0]);
                    $cuenta=Cuenta::buscarCuenta($validarcuenta)->first();
                    if (floatval($array[0][$i][1])>0) {
                        $detalleDiario = new Detalle_Diario();
                    
                    $detalleDiario->detalle_debe = floatval(floatval($array[0][$i][1]));
                    $detalleDiario->detalle_haber = 0.00;
                    $detalleDiario->detalle_comentario =  'P/R ASIENTO INICIAL BALANCE GENERAL';
                    $detalleDiario->detalle_tipo_documento = '';
                    $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                    $detalleDiario->detalle_conciliacion = '0';
                    $detalleDiario->detalle_estado = '1';
                    $detalleDiario->cuenta_id =  $cuenta->cuenta_id;
                    $diario->detalles()->save($detalleDiario);
                    $general->registrarAuditoria('Registro de Detalle de Diario codigo: -> '.$diario->diario_codigo, '0', 'En la cuenta del Debe -> '. $cuenta->cuenta_id.' con el valor de: -> '.floatval($array[0][$i][1]));
                    }
                    if (floatval($array[0][$i][2])>0) {
                        $detalleDiario = new Detalle_Diario();
                        $detalleDiario->detalle_debe = 0.00;
                        $detalleDiario->detalle_haber = floatval($array[0][$i][2]);
                        $detalleDiario->detalle_comentario =  'P/R ASIENTO INICIAL BALANCE GENERAL';
                        $detalleDiario->detalle_tipo_documento = '';
                        $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                        $detalleDiario->detalle_conciliacion = '0';
                        $detalleDiario->detalle_estado = '1';
                        $detalleDiario->cuenta_id =  $cuenta->cuenta_id;
                        $diario->detalles()->save($detalleDiario);
                        $general->registrarAuditoria('Registro de Detalle de Diario codigo: -> '.$diario->diario_codigo, '0', 'En la cuenta del Haber -> '. $cuenta->cuenta_id.' con el valor de: -> '.floatval($array[0][$i][2]));
                    }
                    
                }
                $diario->save();
                $general->registrarAuditoria('Actualizacion de Diario codigo: -> '.$diario->diario_codigo, '0','');
            }
            DB::commit();
            return redirect('cargaBalances')->with('success','Datos guardados exitosamente');
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('cargaBalances')->with('error2','Ocurrio un error vuelva a intentarlo('.$ex->getMessage().')');
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
