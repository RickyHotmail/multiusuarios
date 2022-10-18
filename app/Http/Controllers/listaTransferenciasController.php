<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Banco;
use App\Models\Banco_Lista;
use App\Models\Cuenta_Bancaria;
use App\Models\Empresa;
use App\Models\Punto_Emision;
use App\Models\Transferencia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Luecano\NumeroALetras\NumeroALetras;
use Maatwebsite\Excel\Facades\Excel;
class listaTransferenciasController extends Controller
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
            return view('admin.bancos.transferencias.cargarExcel',['PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
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

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            if ($request->file('excelTransfer')->isValid()) {
                $empresa = Empresa::empresa()->first();
                $name = $empresa->empresa_ruc. '.' .$request->file('excelTransfer')->getClientOriginalExtension();
                $path = $request->file('excelTransfer')->move(public_path().'\temp', $name);
                $array = Excel::toArray(new Transferencia(), $path);
                DB::beginTransaction();    
                for ($i=1;$i < count($array[0]);$i++){  
                    $banco='';            
                    $transferencia = new Transferencia();
                    $transferencia->transferencia_descripcion = $array[0][$i][0];
                    $transferencia->transferencia_beneficiario =  $array[0][$i][1];
                    $Excel_date2 = $array[0][$i][2]; 
                    $unix_date2 = ($Excel_date2 - 25569) * 86400;
                    $Excel_date2 = 25569 + ($unix_date2 / 86400);
                    $unix_date2 = ($Excel_date2 - 25569) * 86400;
                    $transferencia->transferencia_fecha = gmdate("Y-m-d", $unix_date2);
                    $transferencia->transferencia_valor =  $array[0][$i][3];
                    $bancoLista = Banco_Lista::BancoListaByNom($array[0][$i][5])->first();
                    if(isset($bancoLista->banco_lista_id)){
                        $banco = Banco::BancoXbancolista($bancoLista->banco_lista_id)->first();         
                    }         
                    $cuentaBancarias = Cuenta_Bancaria::CuentaBancariasBanco($banco->banco_id)->get();                    
                    foreach($cuentaBancarias as $cuentaBancaria){                        
                        if($cuentaBancaria->cuenta_bancaria_numero == $array[0][$i][4]){
                            //return($cuentaBancaria->cuenta_bancaria_id);
                            $transferencia->cuenta_bancaria_id =  $cuentaBancaria->cuenta_bancaria_id;
                        } 
                    }                                      
                    $transferencia->transferencia_estado =  '1';
                    $transferencia->empresa_id = Auth::user()->empresa->empresa_id;
                    $transferencia->save();

                    /*Inicio de registro de auditoria */
                    $auditoria = new generalController();
                    $auditoria->registrarAuditoria('Registro de Transferencia-> '.$array[0][$i][0].' Cuenta Bancaria '.$cuentaBancaria->cuenta_bancaria_numero.' Valor '.$array[0][$i][3], '0', ' subir datos con archivo de Excel ');
                
                }
                DB::commit();
                return redirect('exceltransferencia')->with('success','Datos guardados exitosamente');
            }
        }
        catch(\Exception $ex){ 
            DB::rollBack();     
            return redirect('exceltransferencia')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
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
