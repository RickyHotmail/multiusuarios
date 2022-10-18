<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Alimentacion;
use App\Models\Anticipo_Empleado;
use App\Models\Categoria_Producto;
use App\Models\Centro_Consumo;
use App\Models\Cheque;
use App\Models\Control_Dia;
use App\Models\Descuento_Anticipo_Empleado;
use App\Models\Detalle_Diario;
use App\Models\Detalle_Rol;
use App\Models\Diario;
use App\Models\Empleado;
use App\Models\Impuesto_Renta_Rol;
use App\Models\Parametrizacion_Contable;
use App\Models\Punto_Emision;
use App\Models\Quincena;
use App\Models\Rango_Documento;
use App\Models\Rol_Consolidado;
use App\Models\Transferencia;
use App\Models\Vacacion;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Luecano\NumeroALetras\NumeroALetras;
use PhpParser\Node\Stmt\Return_;

use function PHPUnit\Framework\isNull;

class rolConsolidadoController extends Controller
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

            return view('admin.recursosHumanos.rolPagoConsolidado.view',['consumo'=>Centro_Consumo::CentroConsumos()->get(),'categoria'=>Categoria_Producto::Categorias()->get(),'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        }
        catch(\Exception $ex){      
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
            
        }
    }
    public function nuevo($id){
        try{ 
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
           
            $rangoDocumento=Rango_Documento::PuntoRango($id, 'Rol')->first();
            if($rangoDocumento){
                return view('admin.recursosHumanos.rolPagoConsolidado.view',['rangoDocumento'=>$rangoDocumento,'consumo'=>Centro_Consumo::CentroConsumos()->get(),'categoria'=>Categoria_Producto::Categorias()->get(),'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);   
            }else{
                return redirect('inicio')->with('error','No tiene configurado, un punto de emisión o un rango de documentos para emitir facturas de venta, configueros y vuelva a intentar');
            }
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
    public function extraer(Request $request)
    {
        try{
            DB::beginTransaction();
        $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
        $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
       
        $rangoDocumento=Rango_Documento::PuntoRango($request->get('punto'), 'Rol')->first();
      
        $datos = null;
        $anticipos = null;
        $quincenas = null;
        $mvacaciones = null;
        $alimentar = null;
        $validacion=Rol_Consolidado::RolFecha($request->get('fecha_hasta'))->get();
      
        $anti=Anticipo_Empleado::EmpleadosRol()->select('empleado.empleado_id')->groupBy('empleado.empleado_id')->selectRaw('sum(anticipo_saldo) as sum, empleado.empleado_id')->get();
    
        
            $cou=0;
            $count = 1;
            foreach (Empleado::EmpleadosRolSucursal($rangoDocumento->puntoEmision->sucursal_id)->get() as $empleado) {
                $datos[$count]['IDE'] =$empleado->empleado_id;
                $datos[$count]['ID'] =$cou;
                $datos[$count]['count'] =$count;
                $datos[$count]['Dcedula'] =$empleado->empleado_cedula;
                $datos[$count]['Dnombre'] =$empleado->empleado_nombre;
                $datos[$count]['dias'] =30;
                $cou++;
                $datos[$count]['SHorasExtras'] =$empleado->empleado_horas_extra;
                $datos[$count]['Dsueldo'] =round($empleado->empleado_sueldo, 2);
                $datos[$count]['DCSueldo'] =round(($empleado->empleado_sueldo/30)*30, 2);
                
              
               
                $datos[$count]['Dextras'] =0.00;
                $datos[$count]['Dhoras_suplementarias'] =0.00;
                $datos[$count]['Dtransporte'] =0.00;
                $datos[$count]['Dotrosbon'] =0.00;
                $datos[$count]['Dfondo'] =0.00;
                $datos[$count]['Dfondoacumula']=0.00;
                $datos[$count]['Iesspatronal'] =0.00;
                $datos[$count]['Iesspersonal'] =0.00;
                $datos[$count]['Iess'] =0.00;
                $datos[$count]['Iessasu'] =0.00;
                $datos[$count]['fondofecha'] =0;
                $datos[$count]['Dtercero'] =0.00;
                $datos[$count]['Dcuarto'] =0.00;
           
                $datos[$count]['IECESECAP']=0.00;
                $datos[$count]['impurenta'] =0.00;
                $datos[$count]['Dvacaciones'] =0.00;
                $datos[$count]['Dcuartoacu'] =0;
                $datos[$count]['Dterceroacu'] =0;
                $datos[$count]['fondoreser'] =$empleado->empleado_fondos_reserva;
                $datos[$count]['porcefondo'] =$empleado->parametrizar_fondos_reserva;
                if ($empleado->empleado_fondos_reserva=="0") {
                    $datos[$count]['fondofecha'] =1;
             
                    if ($empleado->empleado_fecha_inicioFR <= date('Y-m-d')) {
                        if ($empleado->empleado_fondos_reserva=="0") {
                            $datos[$count]['Dfondo'] =round(($empleado->empleado_sueldo*$empleado->parametrizar_fondos_reserva)/100, 2);
                            $datos[$count]['Dfondoacumula'] =0;
                        }
                        else{
                            $datos[$count]['Dfondoacumula'] =round(($empleado->empleado_sueldo*$empleado->parametrizar_fondos_reserva)/100, 2);
                            $datos[$count]['Dfondo'] =0;
                        }
                    }
                }

                if ($empleado->empleado_afiliado=="1") {
                    $datos[$count]['Iesspatronal'] =round(($empleado->empleado_sueldo*$empleado->parametrizar_iess_patronal)/100, 2);
                    $datos[$count]['Iesspersonal'] =round(($empleado->empleado_sueldo*$empleado->parametrizar_iess_personal)/100, 2);
                    $datos[$count]['IECESECAP'] =round(($empleado->empleado_sueldo*$empleado->parametrizar_iece_secap)/100, 2);
                    if ($empleado->empleado_iess_asumido=="1") {
                        $datos[$count]['Iessasu'] =round(($empleado->empleado_sueldo*$empleado->parametrizar_iess_personal)/100, 2);
                    } else {
                        $datos[$count]['Iess'] =round(($empleado->empleado_sueldo*$empleado->parametrizar_iess_personal)/100, 2);
                    }
                    if ($empleado->empleado_decimo_tercero=="1") {
                        $datos[$count]['Dtercero'] =round($empleado->empleado_sueldo/12, 2);
                        $datos[$count]['Dterceroacu'] =0;
                    }
                    else{
                        $datos[$count]['Dterceroacu'] =round($empleado->empleado_sueldo/12, 2);
                        $datos[$count]['Dtercero'] =0;
                    }
                    if ($empleado->empleado_decimo_cuarto=="1") {
                        $datos[$count]['Dcuarto'] =round(($empleado->parametrizar_sueldo_basico/360)* $empleado->parametrizar_dias_trabajo, 2);
                        $datos[$count]['Dcuartoacu'] =0;
                    }
                    else{
                        $datos[$count]['Dcuartoacu'] =round(($empleado->parametrizar_sueldo_basico/360)* $empleado->parametrizar_dias_trabajo, 2);
                        $datos[$count]['Dcuarto'] =0;
                    }
                  
                    foreach (Vacacion::vacaciones()->get() as $vacaciones) {
                        if ($empleado->empleado_id == $vacaciones->empleado_id) {
                            $datos[$count]['Dvacaciones'] =round($vacaciones->vacacion_valor,2);
                        }
                    }
                    
                    if ($empleado->empleado_impuesto_renta=="1") {
                        foreach (Impuesto_Renta_Rol::Roles()->get() as $impuesto) {
                            if ((($empleado->empleado_sueldo*12) >= $impuesto->impuesto_fraccion_basica) && (($empleado->empleado_sueldo*12) < $impuesto->impuesto_exceso_hasta)) {
                                
                                $datos[$count]['impurenta']=round((((($impuesto->impuesto_exceso_hasta-($empleado->empleado_sueldo*12))*$impuesto->impuesto_sobre_fraccion)/100)+$impuesto->impuesto_fraccion_excede)/12, 2);
                                
                            }
                        }
                    }
                }
           
               
               
            
                $datos[$count]['Dotrosin'] =0.00;
            
                $datos[$count]['tercero'] =$empleado->empleado_decimo_tercero;
            
                $datos[$count]['cuarto'] =$empleado->empleado_decimo_cuarto;

                $datos[$count]['sueldobasico'] =$empleado->parametrizar_sueldo_basico;
                $datos[$count]['DTingresos'] =round($datos[$count]['DCSueldo']+$datos[$count]['Dtercero']+$datos[$count]['Dcuarto']+$datos[$count]['Dfondo'], 2);
                $datos[$count]['DTotalingresos'] =round($datos[$count]['DCSueldo'], 2);
                $datos[$count]['diastraba'] =$empleado->parametrizar_dias_trabajo;
           
                $datos[$count]['%iess'] =$empleado->parametrizar_iess_personal;
                $datos[$count]['%iessasu_check'] =$empleado->empleado_iess_asumido;
            

                $datos[$count]['Dsalud'] =0.00;
                $datos[$count]['Dalimentacion'] =0.00;
                foreach (Alimentacion::alimentaciones()->get() as $alimentaciones) {
                    if ($empleado->empleado_id == $alimentaciones->empleado_id) {
                        $datos[$count]['Dalimentacion'] +=round($alimentaciones->alimentacion_valor,2);
                    }
                }
                $datos[$count]['Dppqq'] =0.00;
                $datos[$count]['Dhipotecarios'] =0.00;
                $datos[$count]['Dprestamos'] =0.00;
                $datos[$count]['anticipos'] =0.00;
                $datos[$count]['Dmultas'] =0.00;
                if (isset($anti)) {
                    foreach ($anti as $anticipo) {
                        if ($anticipo->empleado_id == $empleado->empleado_id) {
                            $datos[$count]['anticipos'] =$anticipo->sum;
                        }
                    }
                }
           
                
                $datos[$count]['quince'] =0.00;
                foreach (Quincena::Quincenas()->get() as $quincena) {
                    if ($empleado->empleado_id == $quincena->empleado_id) {
                        $datos[$count]['quince'] =round($quincena->quincena_valor, 2);
                    }
                }
              
            
                $datos[$count]['Dotrosegre'] =0.00;
                $datos[$count]['totalegre'] =round($datos[$count]['anticipos']+$datos[$count]['Dalimentacion']+$datos[$count]['impurenta']+$datos[$count]['Dvacaciones']+$datos[$count]['Iessasu']+$datos[$count]['Iess']+$datos[$count]['quince'], 2);
                $datos[$count]['total'] =round($datos[$count]['DTingresos']-$datos[$count]['totalegre'], 2);
                $datos[$count]['totalegresos'] =0.00;
                $count++;
            }
            $count = 1;
            foreach (Anticipo_Empleado::EmpleadosRol()->get() as $antici) {
                $anticipos[$count]['id']=$antici->anticipo_id;
                $count++;
            }
            $count = 1;
            foreach (Quincena::Quincenas()->get() as $quincena) {
                $quincenas[$count]['id']=$quincena->quincena_id;
                $count++;
            }
            $count = 1;
            foreach (Vacacion::vacaciones()->get() as $vacacion) {
                $mvacaciones[$count]['id']=$vacacion->vacacion_id;
                $count++;
            }
            $count = 1;
            foreach (Alimentacion::alimentaciones()->get() as $alimentaciones) {
                $alimentar[$count]['id']=$alimentaciones->alimentacion_id;
                $count++;
            }
           
            DB::commit();
            return view('admin.recursosHumanos.rolPagoConsolidado.view', ['rangoDocumento'=>$rangoDocumento,'consumo'=>Centro_Consumo::CentroConsumos()->get(),'categoria'=>Categoria_Producto::Categorias()->get(),'alimentar'=>$alimentar,'vacaciones'=>$mvacaciones,'quincenas'=>$quincenas,'fecha_desde'=>$request->get('fecha_desde'),'fecha_hasta'=>$request->get('fecha_hasta'),'datos'=>$datos,'anticipos'=>$anticipos,'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        
      
        
    }catch(\Exception $ex){
        DB::rollBack();
        return redirect('/rolConsolidado')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
    }  
    }
  
    public function imprimirrol($id)
    {   
        try{
            $rol=Rol_Consolidado::Rol($id)->get()->first();
            $general = new generalController();
            $url = $general->pdfRol($rol);
            return $url;
        }
        catch(\Exception $ex){      
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function imprimirrolindividual($id)
    {   
        try{
            $rol=Rol_Consolidado::Rol($id)->get()->first();
            $general = new generalController();
            $url = $general->pdfRol($rol);
            return $url;
        }
        catch(\Exception $ex){      
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function imprimirdiario($id)
    { 
        try{
            $rol=Rol_Consolidado::Rol($id)->get()->first();
            $general = new generalController();
            $url = $general->pdfDiariourl($rol->diariopago);
            return $url;
        }
        catch(\Exception $ex){      
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }

    public function imprimirdiariocontabilizado($id)
    { 
        try{
            $rol=Rol_Consolidado::Rol($id)->get()->first();
            $general = new generalController();
            $url = $general->pdfDiariourl($rol->diariocontabilizacion);
            return $url;
        }
        catch(\Exception $ex){      
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
        try{
            DB::beginTransaction();
            $auditoria = new generalController();
            $rol = Rol_Consolidado::findOrFail($id);
            $cierre = $auditoria->cierre($rol->cabecera_rol_fecha,$rol->diariopago->sucursal_id);          
            if($cierre){
                return redirect('listaroles')->with('error2','No puede realizar la operacion por que pertenece a un mes bloqueado');
            }
            $aux=Rol_Consolidado::findOrFail($id);
            $aux->diario_pago_id=null;
            $aux->diario_contabilizacion_id=null;   
            $aux->save();
            $auditoria->registrarAuditoria('Actualziacion de Rol de diarios null para la eliminacion de diario para la eliminacion de rol  con valor '.$rol->cabecera_rol_pago ,'0','');
            if (isset($rol->quincena->quincena_id)){
                $quincena=Quincena::findOrFail($rol->quincena->quincena_id);
                $quincena->cabecera_rol_id=null;
                $quincena->quincena_estado='1';
                $quincena->save();
            }
            if (isset($rol->vacaciones)) {
                $vacaciones=Vacacion::findOrFail($rol->vacaciones->vacacion_id);
                $vacaciones->cabecera_rol_id=null;
                $vacaciones->vacacion_estado='1';
                $vacaciones->save();
                if($vacaciones->diario_id=$rol->diario_pago_id){
                    $vacaciones->delete();
                }
                $auditoria->registrarAuditoria('Actualziacion de  Vacaciones para la eliminacion de rol con valor de  '.$rol->vacaciones->vacacion_valor ,'0','');
            }
            if (isset($rol->anticipos)) {
                foreach ($rol->anticipos as $detalle) {
                    $anticipos = Descuento_Anticipo_Empleado::findOrFail($detalle->descuento_id);
                    $anticip=Anticipo_Empleado::findOrFail($anticipos->anticipo_id);
                    $anticipos->delete();
                    $auditoria->registrarAuditoria('Eliminacion de Descuento de Anticipos para la eliminacion de rol con valor '.$detalle->descuento_valor ,'0','');

                    $anticip->anticipo_saldo=$anticip->anticipo_valor-(Descuento_Anticipo_Empleado::Anticipos($anticipos->anticipo_id)->sum('descuento_valor'));
                    
                    $anticip->anticipo_estado='1';
                    $anticip->save();
                    $auditoria->registrarAuditoria('Actualziacion de  Anticipos para la eliminacion de descuento de anticipo para la eliminacion de rol con valor '.$anticipos->descuento_valor ,'0','');
                   
                    
                }
            }
           
            if (isset($rol->alimentacion)) {
                foreach ($rol->alimentacion as $alimentacion) {
                    $alimentaciones = Alimentacion::findOrFail($alimentacion->alimentacion_id);
                    $alimentaciones->cabecera_rol_id=null;
                    $alimentaciones->alimentacion_estado='1';
                    $alimentaciones->save();
                    $auditoria->registrarAuditoria('Actualziacion de  Alimentacion a null para la eliminacion de rol ','0','');
                }
            }
            if (isset($rol->control)) {
                $Control=Control_Dia::findOrFail($rol->control->control_id);
                $Control->cabecera_rol_id=null;
                $Control->control_estado='1';
                $Control->save();
                $auditoria->registrarAuditoria('Actualziacion de  Control de dia para la eliminacion de rol ','0','');
            }
            if (isset($rol->diariopago->diario_id)) {
                foreach ($rol->diariopago->detalles as $detalle) {
                    if (isset($detalle->cheque->cheque_id)) {
                        $cheques = Cheque::findOrFail($detalle->cheque->cheque_id);
                    }
                    if (isset($detalle->transferencia->transferencia_id)) {
                        $transferencias = Transferencia::findOrFail($detalle->transferencia->transferencia_id);
                    }
                }
                foreach ($rol->diariopago->detalles as $detalle) {
                    $detalles = Detalle_Diario::findOrFail($detalle->detalle_id);
                    $detalles->delete();
                    $auditoria->registrarAuditoria('Eliminacion de Detalle diario de pago de Rol tipo-> '.$rol->cabecera_rol_tipo.' para la eliminacion de rol con valor '.$rol->cabecera_rol_pago, '0', '');
                }
                if(isset($cheques)){
                    $cheques->delete();
                    $auditoria->registrarAuditoria('Eliminacion de Cheque -> '.$cheques->cheque_numero.' de pago de Rol tipo -> '.$rol->cabecera_rol_tipo.' para la eliminacion de rol con valor '.$rol->cabecera_rol_pago, '0', '');
                }
                if(isset($transferencias)){
                    $transferencias->delete();
                    $auditoria->registrarAuditoria('Eliminacion de Tranferencia de pago de Rol tipo -> '.$rol->cabecera_rol_tipo.' para la eliminacion de rol con valor '.$rol->cabecera_rol_pago, '0', '');
                }
                $rol->diariopago->delete();
                $auditoria->registrarAuditoria('Eliminacion de Diario de rol de pago tipo-> '.$rol->cabecera_rol_tipo.' para la eliminacion de rol con valor '.$rol->cabecera_rol_pago ,'0','');
                
            }
            if (isset($rol->diariocontabilizacion)) {
               
                foreach ($rol->diariocontabilizacion->detalles as $detalle) {
                    $detalles = Detalle_Diario::findOrFail($detalle->detalle_id);
                    $detalles->delete();
                    $auditoria->registrarAuditoria('Eliminacion de Detalle diario contabilizado de pago de Rol tipo-> '.$rol->cabecera_rol_tipo.' para la eliminacion de rol con valor '.$rol->cabecera_rol_pago, '0', '');
                }
                $rol->diariocontabilizacion->delete();
                $auditoria->registrarAuditoria('Eliminacion de Diario contabilizado de rol de pago tipo-> '.$rol->cabecera_rol_tipo.' para la eliminacion de rol con valor '.$rol->cabecera_rol_pago, '0', '');


            }
            
            foreach ($rol->detalles as $detalle) {
                $detalles = Detalle_Rol::findOrFail($detalle->detalle_rol_id);
                $detalles->delete();
                $auditoria->registrarAuditoria('Eliminacion de Detalle Rol de pago de Rol tipo-> '.$rol->cabecera_rol_tipo.' para la eliminacion de rol con valor '.$rol->cabecera_rol_pago ,'0','');
            }
            
            
            
            $rol->delete();
            $auditoria->registrarAuditoria('Eliminacion de  de rol  tipo -> '.$rol->cabecera_rol_tipo.' con valor '.$rol->cabecera_rol_pago ,'0','');
            /*Fin de registro de auditoria */
           
         
            DB::commit();
            return redirect('listaroles')->with('success','Datos eliminados exitosamente');
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('/listaroles')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }  
       
    }
    public function verChequeindividual(Request $request, $id){
        try{
            DB::beginTransaction();
        $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
        $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();         
        $datos=null;
        
        $alimentacion=null;
        $anticipo=null;
        $rol2= Rol_Consolidado::findOrFail($id);
        $count=1;
        foreach($rol2->alimentacion as $alimentaciones){
            $alimentacion[$count]['fecha']=$alimentaciones->alimentacion_fecha;
            $alimentacion[$count]['valor']=$alimentaciones->alimentacion_valor;
            $alimentacion[$count]['factura']=$alimentaciones->transaccion->transaccion_numero;
            $count++;
        }
        $count=1;
        foreach($rol2->anticipos as $anticipos){
            $anticipo[$count]['descuento_fecha']=$anticipos->descuento_fecha;
            $anticipo[$count]['descuento_valor']=$anticipos->descuento_valor;
            $anticipo[$count]['Valor_Anticipó']=$anticipos->anticipo->anticipo_valor;
            $count++;
        }  
        $count=1;
        $datos[$count]['descripcion']=$rol2->cabecera_rol_descripcion;
        $datos[$count]['tingresos']=$rol2->cabecera_rol_total_ingresos;
        $datos[$count]['tegresos']=$rol2->cabecera_rol_total_egresos;
        $datos[$count]['anticipos']=$rol2->cabecera_rol_total_anticipos;
        $datos[$count]['pago']=$rol2->cabecera_rol_pago;
        $datos[$count]['vcosecha']=$rol2->empleado_cosecha;
        
        $datos[$count]['pago']=$rol2->cabecera_rol_pago;
        $datos[$count]['fondosacu']=$rol2->cabecera_rol_fr_acumula;
        $datos[$count]['tipo']="Efectivo";
        foreach($rol2->diariopago->detalles as $detalle){
            if ($detalle->detalle_haber>0) {
                $datos[$count]['iddetalle']=$detalle->detalle_id;
                if (isset($detalle->cheque)) {
                   
                    $datos[$count]['tipo']="Cheque";
                    $datos[$count]['idcheque']=$detalle->cheque->cheque_id;
                    $datos[$count]['cheque']=$detalle->cheque->cheque_numero;
                    $datos[$count]['fecha']=$detalle->cheque->cheque_fecha_pago;
                    $datos[$count]['numero']=$detalle->cheque->cuentaBancaria->cuenta_bancaria_numero;
                    $datos[$count]['banco']=$detalle->cheque->cuentaBancaria->banco->bancoLista->banco_lista_nombre;
         
                    $count++;
                }
                if (isset($detalle->transferencia)) {
                   
                    $datos[$count]['tipo']="Transferencia";
                    $datos[$count]['numero']=$detalle->transferencia->cuentaBancaria->cuenta_bancaria_numero;
                    $datos[$count]['banco']=$detalle->transferencia->cuentaBancaria->banco->bancoLista->banco_lista_nombre;
            
                    $count++;
                }
            }
        }
        $count=1;
        foreach($rol2->detalles as $detalle){
            $datos[$count]['fecha_inicio']=$detalle->detalle_rol_fecha_inicio;
            $datos[$count]['fecha_fin']=$detalle->detalle_rol_fecha_fin;
            $datos[$count]['dias']=$detalle->detalle_rol_dias;
            $datos[$count]['porcentaje']=$detalle->detalle_rol_porcentaje;
            $datos[$count]['sueldo']=$detalle->detalle_rol_total_dias;
            $datos[$count]['cosecha']=$detalle->detalle_rol_cosecha;
            $datos[$count]['otrosingre']=$detalle->detalle_rol_otros_ingresos;
            $datos[$count]['presta_qui']=$detalle->detalle_rol_prestamo_quirografario;
            $datos[$count]['contro_sol']=$detalle->detalle_rol_ley_sol;
            $datos[$count]['alimentacion']=$detalle->detalle_rol_total_comisariato;
            $datos[$count]['extra']=$detalle->detalle_rol_valor_he;
            
            $datos[$count]['horas_suplementarias']=$detalle->detalle_rol_bonificacion_valor;
            $datos[$count]['transporte']=$detalle->detalle_rol_transporte;
            $datos[$count]['otrasbonificaciones']=$detalle->detalle_rol_otra_bonificacion;

            $datos[$count]['salud']=$detalle->detalle_rol_ext_salud;
            $datos[$count]['ppqq']=$detalle->detalle_rol_prestamo_quirografario;
            $datos[$count]['hipoteca']=$detalle->detalle_rol_prestamo_hipotecario;
            $datos[$count]['prestamos']=$detalle->detalle_rol_prestamo;
            $datos[$count]['multas']=$detalle->detalle_rol_multa;
            $datos[$count]['salud']=$detalle->detalle_rol_ley_sol;


            $datos[$count]['otro_egre']=$detalle->detalle_rol_otros_egresos;
            $datos[$count]['Patronal']=$detalle->detalle_rol_aporte_patronal;
            $datos[$count]['Tercero_acu']=$detalle->detalle_rol_decimo_cuartoacum;
            $datos[$count]['Cuarto_acu']=$detalle->detalle_rol_decimo_terceroacum;
            $datos[$count]['Tercero']=$detalle->detalle_rol_decimo_tercero;
            $datos[$count]['Cuarto']=$detalle->detalle_rol_decimo_cuarto;
            $datos[$count]['IECE']=$detalle->detalle_rol_aporte_iecesecap;
            $datos[$count]['SETEC']=0;
            
            $datos[$count]['quincena']=$detalle->detalle_rol_quincena;
            $datos[$count]['iess']=$detalle->detalle_rol_iess;
            $datos[$count]['iessasumidao']=$detalle->detalle_rol_iess_asumido;
            $datos[$count]['renta']=$detalle->detalle_rol_impuesto_renta;
            $datos[$count]['vaca_acu']=$detalle->detalle_rol_vacaciones_anticipadas;
            $datos[$count]['vaca']=$detalle->detalle_rol_vacaciones;
            $datos[$count]['fondos']=$detalle->detalle_rol_fondo_reserva;
            $datos[$count]['ingresos']=$detalle->detalle_rol_total_ingreso;
            $datos[$count]['egresos']=$detalle->detalle_rol_total_egreso;
            $count++;
        }



        DB::commit();
        return view('admin.recursosHumanos.listarRol.cambiochequeindividual',['anticipo'=>$anticipo,'alimentacion'=>$alimentacion,'datos'=>$datos,'rol'=>$rol2,'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
       
    }catch(\Exception $ex){
        DB::rollBack();
        return redirect('/listaroles')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
    }  

    }
    public function verChequeoperativo(Request $request, $id){
        try{
            DB::beginTransaction();
        $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
        $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();         
        $datos=null;
        $alimentacion=null;
        $anticipo=null;
        $rol= Cheque::BuscarRol($id)->get();
        $rol2= Rol_Consolidado::findOrFail($id);
        $count=1;
        foreach($rol2->alimentacion as $alimentaciones){
            $alimentacion[$count]['fecha']=$alimentaciones->alimentacion_fecha;
            $alimentacion[$count]['valor']=$alimentaciones->alimentacion_valor;
            $alimentacion[$count]['factura']=$alimentaciones->transaccion->transaccion_numero;
            $count++;
        }
        $count=1;
        foreach($rol2->anticipos as $anticipos){
            $anticipo[$count]['descuento_fecha']=$anticipos->descuento_fecha;
            $anticipo[$count]['descuento_valor']=$anticipos->descuento_valor;
            $anticipo[$count]['Valor_Anticipó']=$anticipos->anticipo->anticipo_valor;
            $count++;
        }  
        $count=1;
        $datos[$count]['descripcion']=$rol2->cabecera_rol_descripcion;
        $datos[$count]['ingresos']=$rol2->cabecera_rol_total_ingresos;
        $datos[$count]['egresos']=$rol2->cabecera_rol_total_egresos;
        $datos[$count]['anticipos']=$rol2->cabecera_rol_total_anticipos;
        $datos[$count]['pago']=$rol2->cabecera_rol_pago;
        $datos[$count]['vcosecha']=$rol2->empleado_cosecha;
        
        $datos[$count]['pago']=$rol2->cabecera_rol_pago;
        $datos[$count]['fondosacu']=$rol2->cabecera_rol_fr_acumula;

        $datos[$count]['tipo']="Efectivo";
        foreach($rol2->diariopago->detalles as $detalle){
            if ($detalle->detalle_haber>0) {
                $datos[$count]['iddetalle']=$detalle->detalle_id;
                if (isset($detalle->cheque)) {
                   
                    $datos[$count]['tipo']="Cheque";
                    $datos[$count]['idcheque']=$detalle->cheque->cheque_id;
                    $datos[$count]['cheque']=$detalle->cheque->cheque_numero;
                    $datos[$count]['fecha']=$detalle->cheque->cheque_fecha_pago;
                    $datos[$count]['numero']=$detalle->cheque->cuentaBancaria->cuenta_bancaria_numero;
                    $datos[$count]['banco']=$detalle->cheque->cuentaBancaria->banco->bancoLista->banco_lista_nombre;
                    $count++;
                }
                if (isset($detalle->transferencia)) {
                   
                    $datos[$count]['tipo']="Transferencia";
                    $datos[$count]['numero']=$detalle->transferencia->cuentaBancaria->cuenta_bancaria_numero;
                    $datos[$count]['banco']=$detalle->transferencia->cuentaBancaria->banco->bancoLista->banco_lista_nombre;
            
                    $count++;
                }
            }
        }

        $count=1;
        foreach($rol2->detalles as $detalle){
            
            $datos[$count]['dias']=$detalle->detalle_rol_dias;
            $datos[$count]['porcentaje']=$detalle->detalle_rol_porcentaje;
            $datos[$count]['sueldo']=$detalle->detalle_rol_total_dias;
            $datos[$count]['cosecha']=$detalle->detalle_rol_cosecha;
            $datos[$count]['otrosingre']=$detalle->detalle_rol_otros_ingresos;
            $datos[$count]['presta_qui']=$detalle->detalle_rol_prestamo_quirografario;
            $datos[$count]['contro_sol']=$detalle->detalle_rol_ley_sol;
            $datos[$count]['alimentacion']=$detalle->detalle_rol_total_comisariato;
            $datos[$count]['extra']=$detalle->detalle_rol_valor_he;
            

            $datos[$count]['otro_egre']=$detalle->detalle_rol_otros_egresos;
            $datos[$count]['Patronal']=$detalle->detalle_rol_aporte_patronal;
            $datos[$count]['Tercero_acu']=$detalle->detalle_rol_decimo_cuartoacum;
            $datos[$count]['Cuarto_acu']=$detalle->detalle_rol_decimo_terceroacum;
            $datos[$count]['Tercero']=$detalle->detalle_rol_decimo_tercero;
            $datos[$count]['Cuarto']=$detalle->detalle_rol_decimo_cuarto;
            $datos[$count]['IECE']=$detalle->detalle_rol_aporte_iecesecap;
            $datos[$count]['SETEC']=0;
            
            $datos[$count]['quincena']=$detalle->detalle_rol_quincena;
            $datos[$count]['iess']=$detalle->detalle_rol_iess;
            $datos[$count]['iessasumidao']=$detalle->detalle_rol_iess_asumido;
            $datos[$count]['renta']=$detalle->detalle_rol_impuesto_renta;
            $datos[$count]['vaca_acu']=$detalle->detalle_rol_vacaciones_anticipadas;
            $datos[$count]['vaca']=$detalle->detalle_rol_vacaciones;
            $datos[$count]['fondos']=$detalle->detalle_rol_fondo_reserva;
            $count++;
        }



         DB::commit();
         return view('admin.recursosHumanos.listarRol.cambiochequeoperativo',['anticipo'=>$anticipo,'alimentacion'=>$alimentacion,'datos'=>$datos,'rol'=>$rol2,'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
       
    }catch(\Exception $ex){
        DB::rollBack();
        return redirect('/listaroles')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
    }  

    }
    public function verindividual(Request $request, $id){
        try{
            DB::beginTransaction();
        $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
        $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();         
        $datos=null;
        $alimentacion=null;
        $anticipo=null;
        $rol2= Rol_Consolidado::findOrFail($id);
        $count=1;
        foreach($rol2->alimentacion as $alimentaciones){
            $alimentacion[$count]['fecha']=$alimentaciones->alimentacion_fecha;
            $alimentacion[$count]['valor']=$alimentaciones->alimentacion_valor;
            $alimentacion[$count]['factura']=$alimentaciones->transaccion->transaccion_numero;
            $count++;
        }
        $count=1;
        foreach($rol2->anticipos as $anticipos){
            $anticipo[$count]['descuento_fecha']=$anticipos->descuento_fecha;
            $anticipo[$count]['descuento_valor']=$anticipos->descuento_valor;
            $anticipo[$count]['Valor_Anticipó']=$anticipos->anticipo->anticipo_valor;
            $count++;
        }  
        $count=1;
        $datos[$count]['descripcion']=$rol2->cabecera_rol_descripcion;
        $datos[$count]['tingresos']=$rol2->cabecera_rol_total_ingresos;
        $datos[$count]['tegresos']=$rol2->cabecera_rol_total_egresos;
        $datos[$count]['anticipos']=$rol2->cabecera_rol_total_anticipos;
        $datos[$count]['pago']=$rol2->cabecera_rol_pago;
        $datos[$count]['vcosecha']=$rol2->empleado_cosecha;
        
        $datos[$count]['pago']=$rol2->cabecera_rol_pago;
        $datos[$count]['fondosacu']=$rol2->cabecera_rol_fr_acumula;
        $datos[$count]['tipo']="Efectivo";
        foreach($rol2->diariopago->detalles as $detalle){
            if ($detalle->detalle_haber>0) {
                $datos[$count]['iddetalle']=$detalle->detalle_id;
                if (isset($detalle->cheque)) {
                   
                    $datos[$count]['tipo']="Cheque";
                    $datos[$count]['idcheque']=$detalle->cheque->cheque_id;
                    $datos[$count]['cheque']=$detalle->cheque->cheque_numero;
                    $datos[$count]['fecha']=$detalle->cheque->cheque_fecha_pago;
                    $datos[$count]['numero']=$detalle->cheque->cuentaBancaria->cuenta_bancaria_numero;
                    $datos[$count]['banco']=$detalle->cheque->cuentaBancaria->banco->bancoLista->banco_lista_nombre;
         
                    $count++;
                }
                if (isset($detalle->transferencia)) {
                   
                    $datos[$count]['tipo']="Transferencia";
                    $datos[$count]['numero']=$detalle->transferencia->cuentaBancaria->cuenta_bancaria_numero;
                    $datos[$count]['banco']=$detalle->transferencia->cuentaBancaria->banco->bancoLista->banco_lista_nombre;
            
                    $count++;
                }
            }
        }
        $count=1;
        foreach($rol2->detalles as $detalle){
            $datos[$count]['fecha_inicio']=$detalle->detalle_rol_fecha_inicio;
            $datos[$count]['fecha_fin']=$detalle->detalle_rol_fecha_fin;
            $datos[$count]['dias']=$detalle->detalle_rol_dias;
            $datos[$count]['porcentaje']=$detalle->detalle_rol_porcentaje;
            $datos[$count]['sueldo']=$detalle->detalle_rol_total_dias;
            $datos[$count]['cosecha']=$detalle->detalle_rol_cosecha;
            $datos[$count]['otrosingre']=$detalle->detalle_rol_otros_ingresos;
            $datos[$count]['presta_qui']=$detalle->detalle_rol_prestamo_quirografario;
            $datos[$count]['contro_sol']=$detalle->detalle_rol_ley_sol;
            $datos[$count]['alimentacion']=$detalle->detalle_rol_total_comisariato;
            $datos[$count]['extra']=$detalle->detalle_rol_valor_he;
            
            $datos[$count]['horas_suplementarias']=$detalle->detalle_rol_bonificacion_valor;
            $datos[$count]['transporte']=$detalle->detalle_rol_transporte;
            $datos[$count]['otrasbonificaciones']=$detalle->detalle_rol_otra_bonificacion;

            $datos[$count]['salud']=$detalle->detalle_rol_ext_salud;
            $datos[$count]['ppqq']=$detalle->detalle_rol_prestamo_quirografario;
            $datos[$count]['hipoteca']=$detalle->detalle_rol_prestamo_hipotecario;
            $datos[$count]['prestamos']=$detalle->detalle_rol_prestamo;
            $datos[$count]['multas']=$detalle->detalle_rol_multa;
            $datos[$count]['salud']=$detalle->detalle_rol_ley_sol;


            $datos[$count]['otro_egre']=$detalle->detalle_rol_otros_egresos;
            $datos[$count]['Patronal']=$detalle->detalle_rol_aporte_patronal;
            $datos[$count]['Tercero_acu']=$detalle->detalle_rol_decimo_cuartoacum;
            $datos[$count]['Cuarto_acu']=$detalle->detalle_rol_decimo_terceroacum;
            $datos[$count]['Tercero']=$detalle->detalle_rol_decimo_tercero;
            $datos[$count]['Cuarto']=$detalle->detalle_rol_decimo_cuarto;
            $datos[$count]['IECE']=$detalle->detalle_rol_aporte_iecesecap;
            $datos[$count]['SETEC']=0;
            
            $datos[$count]['quincena']=$detalle->detalle_rol_quincena;
            $datos[$count]['iess']=$detalle->detalle_rol_iess;
            $datos[$count]['iessasumidao']=$detalle->detalle_rol_iess_asumido;
            $datos[$count]['renta']=$detalle->detalle_rol_impuesto_renta;
            $datos[$count]['vaca_acu']=$detalle->detalle_rol_vacaciones_anticipadas;
            $datos[$count]['vaca']=$detalle->detalle_rol_vacaciones;
            $datos[$count]['fondos']=$detalle->detalle_rol_fondo_reserva;
            $datos[$count]['ingresos']=$detalle->detalle_rol_total_ingreso;
            $datos[$count]['egresos']=$detalle->detalle_rol_total_egreso;
            $count++;
        }



        DB::commit();
        return view('admin.recursosHumanos.listarRol.verIndividual',['anticipo'=>$anticipo,'alimentacion'=>$alimentacion,'datos'=>$datos,'rol'=>$rol2,'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
       
   }catch(\Exception $ex){
       DB::rollBack();
       return redirect('/listaroles')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
   }  

    }
    public function veroperativo(Request $request, $id){
        try{
            DB::beginTransaction();
        $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
        $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();         
        $datos=null;
        $alimentacion=null;
        $anticipo=null;
        $rol= Cheque::BuscarRol($id)->get();
        $rol2= Rol_Consolidado::findOrFail($id);
        $count=1;
        foreach($rol2->alimentacion as $alimentaciones){
            $alimentacion[$count]['fecha']=$alimentaciones->alimentacion_fecha;
            $alimentacion[$count]['valor']=$alimentaciones->alimentacion_valor;
            $alimentacion[$count]['factura']=$alimentaciones->transaccion->transaccion_numero;
            $count++;
        }
        $count=1;
        foreach($rol2->anticipos as $anticipos){
            $anticipo[$count]['descuento_fecha']=$anticipos->descuento_fecha;
            $anticipo[$count]['descuento_valor']=$anticipos->descuento_valor;
            $anticipo[$count]['Valor_Anticipó']=$anticipos->anticipo->anticipo_valor;
            $count++;
        }  
        $count=1;
        $datos[$count]['descripcion']=$rol2->cabecera_rol_descripcion;
        $datos[$count]['ingresos']=$rol2->cabecera_rol_total_ingresos;
        $datos[$count]['egresos']=$rol2->cabecera_rol_total_egresos;
        $datos[$count]['anticipos']=$rol2->cabecera_rol_total_anticipos;
        $datos[$count]['pago']=$rol2->cabecera_rol_pago;
        $datos[$count]['vcosecha']=$rol2->empleado_cosecha;
        
        $datos[$count]['pago']=$rol2->cabecera_rol_pago;
        $datos[$count]['fondosacu']=$rol2->cabecera_rol_fr_acumula;

        $datos[$count]['tipo']="Efectivo";
        foreach($rol2->diariopago->detalles as $detalle){
            if ($detalle->detalle_haber>0) {
                $datos[$count]['iddetalle']=$detalle->detalle_id;
                if (isset($detalle->cheque)) {
                   
                    $datos[$count]['tipo']="Cheque";
                    $datos[$count]['idcheque']=$detalle->cheque->cheque_id;
                    $datos[$count]['cheque']=$detalle->cheque->cheque_numero;
                    $datos[$count]['fecha']=$detalle->cheque->cheque_fecha_pago;
                    $datos[$count]['numero']=$detalle->cheque->cuentaBancaria->cuenta_bancaria_numero;
                    $datos[$count]['banco']=$detalle->cheque->cuentaBancaria->banco->bancoLista->banco_lista_nombre;
         
                    $count++;
                }
                if (isset($detalle->transferencia)) {
                   
                    $datos[$count]['tipo']="Transferencia";
                    $datos[$count]['numero']=$detalle->transferencia->cuentaBancaria->cuenta_bancaria_numero;
                    $datos[$count]['banco']=$detalle->transferencia->cuentaBancaria->banco->bancoLista->banco_lista_nombre;
            
                    $count++;
                }
            }
        }

        $count=1;
        foreach($rol2->detalles as $detalle){
            
            $datos[$count]['dias']=$detalle->detalle_rol_dias;
            $datos[$count]['porcentaje']=$detalle->detalle_rol_porcentaje;
            $datos[$count]['sueldo']=$detalle->detalle_rol_total_dias;
            $datos[$count]['cosecha']=$detalle->detalle_rol_cosecha;
            $datos[$count]['otrosingre']=$detalle->detalle_rol_otros_ingresos;
            $datos[$count]['presta_qui']=$detalle->detalle_rol_prestamo_quirografario;
            $datos[$count]['contro_sol']=$detalle->detalle_rol_ley_sol;
            $datos[$count]['alimentacion']=$detalle->detalle_rol_total_comisariato;
            $datos[$count]['extra']=$detalle->detalle_rol_valor_he;
            

            $datos[$count]['otro_egre']=$detalle->detalle_rol_otros_egresos;
            $datos[$count]['Patronal']=$detalle->detalle_rol_aporte_patronal;
            $datos[$count]['Tercero_acu']=$detalle->detalle_rol_decimo_cuartoacum;
            $datos[$count]['Cuarto_acu']=$detalle->detalle_rol_decimo_terceroacum;
            $datos[$count]['Tercero']=$detalle->detalle_rol_decimo_tercero;
            $datos[$count]['Cuarto']=$detalle->detalle_rol_decimo_cuarto;
            $datos[$count]['IECE']=$detalle->detalle_rol_aporte_iecesecap;
            $datos[$count]['SETEC']=0;
            
            $datos[$count]['quincena']=$detalle->detalle_rol_quincena;
            $datos[$count]['iess']=$detalle->detalle_rol_iess;
            $datos[$count]['iessasumidao']=$detalle->detalle_rol_iess_asumido;
            $datos[$count]['renta']=$detalle->detalle_rol_impuesto_renta;
            $datos[$count]['vaca_acu']=$detalle->detalle_rol_vacaciones_anticipadas;
            $datos[$count]['vaca']=$detalle->detalle_rol_vacaciones;
            $datos[$count]['fondos']=$detalle->detalle_rol_fondo_reserva;
            $count++;
        }



         DB::commit();
         return view('admin.recursosHumanos.listarRol.veroperativo',['anticipo'=>$anticipo,'alimentacion'=>$alimentacion,'datos'=>$datos,'rol'=>$rol2,'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
      
   }catch(\Exception $ex){
       DB::rollBack();
       return redirect('/listaroles')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
   }  

    }
    public function eliminarChequeindividual(Request $request, $id){
        try{
            DB::beginTransaction();
        $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
        $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();         
        $datos=null;
        $detalles=null;
        $alimentacion=null;
        $anticipo=null;
      
        $rol2= Rol_Consolidado::findOrFail($id);
        $count=1;
        foreach($rol2->alimentacion as $alimentaciones){
            $alimentacion[$count]['fecha']=$alimentaciones->alimentacion_fecha;
            $alimentacion[$count]['valor']=$alimentaciones->alimentacion_valor;
            $alimentacion[$count]['factura']=$alimentaciones->transaccion->transaccion_numero;
            $count++;
        }
        $count=1;
        foreach($rol2->anticipos as $anticipos){
            $anticipo[$count]['descuento_fecha']=$anticipos->descuento_fecha;
            $anticipo[$count]['descuento_valor']=$anticipos->descuento_valor;
            $anticipo[$count]['Valor_Anticipó']=$anticipos->anticipo->anticipo_valor;
            $count++;
        }  
        $count=1;
        $datos[$count]['rol_id']=$rol2->cabecera_rol_id;
        $datos[$count]['descripcion']=$rol2->cabecera_rol_descripcion;
        $datos[$count]['tingresos']=$rol2->cabecera_rol_total_ingresos;
        $datos[$count]['tegresos']=$rol2->cabecera_rol_total_egresos;
        $datos[$count]['anticipos']=$rol2->cabecera_rol_total_anticipos;
        $datos[$count]['pago']=$rol2->cabecera_rol_pago;
        $datos[$count]['vcosecha']=$rol2->empleado_cosecha;
        
        $datos[$count]['pago']=$rol2->cabecera_rol_pago;
        $datos[$count]['fondosacu']=$rol2->cabecera_rol_fr_acumula;
        $datos[$count]['tipo']="Efectivo";
      
        foreach($rol2->diariopago->detalles as $detalle){
            if ($detalle->detalle_haber>0) {
                $datos[$count]['iddetalle']=$detalle->detalle_id;
                if (isset($detalle->cheque)) {
                   
                    $datos[$count]['tipo']="Cheque";
                    $datos[$count]['idcheque']=$detalle->cheque->cheque_id;
                    $datos[$count]['cheque']=$detalle->cheque->cheque_numero;
                    $datos[$count]['fecha']=$detalle->cheque->cheque_fecha_pago;
                    $datos[$count]['numero']=$detalle->cheque->cuentaBancaria->cuenta_bancaria_numero;
                    $datos[$count]['banco']=$detalle->cheque->cuentaBancaria->banco->bancoLista->banco_lista_nombre;
         
                    $count++;
                }
                if (isset($detalle->transferencia)) {
                   
                    $datos[$count]['tipo']="Transferencia";
                    $datos[$count]['numero']=$detalle->transferencia->cuentaBancaria->cuenta_bancaria_numero;
                    $datos[$count]['banco']=$detalle->transferencia->cuentaBancaria->banco->bancoLista->banco_lista_nombre;
            
                    $count++;
                }
            }
        }
        $count=1;
        foreach($rol2->detalles as $detalle){
            $datos[$count]['fecha_inicio']=$detalle->detalle_rol_fecha_inicio;
            $datos[$count]['fecha_fin']=$detalle->detalle_rol_fecha_fin;
            $datos[$count]['dias']=$detalle->detalle_rol_dias;
            $datos[$count]['porcentaje']=$detalle->detalle_rol_porcentaje;
            $datos[$count]['sueldo']=$detalle->detalle_rol_total_dias;
            $datos[$count]['cosecha']=$detalle->detalle_rol_cosecha;
            $datos[$count]['otrosingre']=$detalle->detalle_rol_otros_ingresos;
            $datos[$count]['presta_qui']=$detalle->detalle_rol_prestamo_quirografario;
            $datos[$count]['contro_sol']=$detalle->detalle_rol_ley_sol;
            $datos[$count]['alimentacion']=$detalle->detalle_rol_total_comisariato;
            $datos[$count]['extra']=$detalle->detalle_rol_valor_he;
            
            $datos[$count]['horas_suplementarias']=$detalle->detalle_rol_bonificacion_valor;
            $datos[$count]['transporte']=$detalle->detalle_rol_transporte;
            $datos[$count]['otrasbonificaciones']=$detalle->detalle_rol_otra_bonificacion;

            $datos[$count]['salud']=$detalle->detalle_rol_ext_salud;
            $datos[$count]['ppqq']=$detalle->detalle_rol_prestamo_quirografario;
            $datos[$count]['hipoteca']=$detalle->detalle_rol_prestamo_hipotecario;
            $datos[$count]['prestamos']=$detalle->detalle_rol_prestamo;
            $datos[$count]['multas']=$detalle->detalle_rol_multa;
            $datos[$count]['salud']=$detalle->detalle_rol_ley_sol;


            $datos[$count]['otro_egre']=$detalle->detalle_rol_otros_egresos;
            $datos[$count]['Patronal']=$detalle->detalle_rol_aporte_patronal;
            $datos[$count]['Tercero_acu']=$detalle->detalle_rol_decimo_cuartoacum;
            $datos[$count]['Cuarto_acu']=$detalle->detalle_rol_decimo_terceroacum;
            $datos[$count]['Tercero']=$detalle->detalle_rol_decimo_tercero;
            $datos[$count]['Cuarto']=$detalle->detalle_rol_decimo_cuarto;
            $datos[$count]['IECE']=$detalle->detalle_rol_aporte_iecesecap;
            $datos[$count]['SETEC']=0;
            
            $datos[$count]['quincena']=$detalle->detalle_rol_quincena;
            $datos[$count]['iess']=$detalle->detalle_rol_iess;
            $datos[$count]['iessasumidao']=$detalle->detalle_rol_iess_asumido;
            $datos[$count]['renta']=$detalle->detalle_rol_impuesto_renta;
            $datos[$count]['vaca_acu']=$detalle->detalle_rol_vacaciones_anticipadas;
            $datos[$count]['vaca']=$detalle->detalle_rol_vacaciones;
            $datos[$count]['fondos']=$detalle->detalle_rol_fondo_reserva;
            $datos[$count]['ingresos']=$detalle->detalle_rol_total_ingreso;
            $datos[$count]['egresos']=$detalle->detalle_rol_total_egreso;
            $count++;
        }



        DB::commit();
        return view('admin.recursosHumanos.listarRol.eliminarindividual',['anticipo'=>$anticipo,'alimentacion'=>$alimentacion,'detalles'=>$detalles,'datos'=>$datos,'rol'=>$rol2,'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
      
  }catch(\Exception $ex){
      DB::rollBack();
      return redirect('/listaroles')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
  }  

    }
    public function eliminarChequeoperativo(Request $request, $id){
        try{
            DB::beginTransaction();
        $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
        $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();         
        $datos=null;
        $alimentacion=null;
        $anticipo=null;
        $rol= Cheque::BuscarRol($id)->get();
        $rol2= Rol_Consolidado::findOrFail($id);
        $count=1;
        foreach($rol2->alimentacion as $alimentaciones){
            $alimentacion[$count]['fecha']=$alimentaciones->alimentacion_fecha;
            $alimentacion[$count]['valor']=$alimentaciones->alimentacion_valor;
            $alimentacion[$count]['factura']=$alimentaciones->transaccion->transaccion_numero;
            $count++;
        }
        $count=1;
        foreach($rol2->anticipos as $anticipos){
            $anticipo[$count]['descuento_fecha']=$anticipos->descuento_fecha;
            $anticipo[$count]['descuento_valor']=$anticipos->descuento_valor;
            $anticipo[$count]['Valor_Anticipó']=$anticipos->anticipo->anticipo_valor;
            $count++;
        }  
        $count=1;
        $datos[$count]['rol_id']=$rol2->cabecera_rol_id;
        $datos[$count]['descripcion']=$rol2->cabecera_rol_descripcion;
        $datos[$count]['ingresos']=$rol2->cabecera_rol_total_ingresos;
        $datos[$count]['egresos']=$rol2->cabecera_rol_total_egresos;
        $datos[$count]['anticipos']=$rol2->cabecera_rol_total_anticipos;
        $datos[$count]['pago']=$rol2->cabecera_rol_pago;
        $datos[$count]['vcosecha']=$rol2->empleado_cosecha;
        
        $datos[$count]['pago']=$rol2->cabecera_rol_pago;
        $datos[$count]['fondosacu']=$rol2->cabecera_rol_fr_acumula;
        $datos[$count]['tipo']="Efectivo";
        
        foreach($rol2->diariopago->detalles as $detalle){
            if ($detalle->detalle_haber>0) {
                $datos[$count]['iddetalle']=$detalle->detalle_id;
                if (isset($detalle->cheque)) {
                   
                    $datos[$count]['tipo']="Cheque";
                    $datos[$count]['idcheque']=$detalle->cheque->cheque_id;
                    $datos[$count]['cheque']=$detalle->cheque->cheque_numero;
                    $datos[$count]['fecha']=$detalle->cheque->cheque_fecha_pago;
                    $datos[$count]['numero']=$detalle->cheque->cuentaBancaria->cuenta_bancaria_numero;
                    $datos[$count]['banco']=$detalle->cheque->cuentaBancaria->banco->bancoLista->banco_lista_nombre;
         
                    $count++;
                }
                if (isset($detalle->transferencia)) {
                   
                    $datos[$count]['tipo']="Transferencia";
                    $datos[$count]['numero']=$detalle->transferencia->cuentaBancaria->cuenta_bancaria_numero;
                    $datos[$count]['banco']=$detalle->transferencia->cuentaBancaria->banco->bancoLista->banco_lista_nombre;
            
                    $count++;
                }
            }
        }
        $count=1;
        foreach($rol2->detalles as $detalle){
            
            $datos[$count]['dias']=$detalle->detalle_rol_dias;
            $datos[$count]['porcentaje']=$detalle->detalle_rol_porcentaje;
            $datos[$count]['sueldo']=$detalle->detalle_rol_total_dias;
            $datos[$count]['cosecha']=$detalle->detalle_rol_cosecha;
            $datos[$count]['otrosingre']=$detalle->detalle_rol_otros_ingresos;
            $datos[$count]['presta_qui']=$detalle->detalle_rol_prestamo_quirografario;
            $datos[$count]['contro_sol']=$detalle->detalle_rol_ley_sol;
            $datos[$count]['alimentacion']=$detalle->detalle_rol_total_comisariato;
            $datos[$count]['extra']=$detalle->detalle_rol_valor_he;
            

            $datos[$count]['otro_egre']=$detalle->detalle_rol_otros_egresos;
            $datos[$count]['Patronal']=$detalle->detalle_rol_aporte_patronal;
            $datos[$count]['Tercero_acu']=$detalle->detalle_rol_decimo_cuartoacum;
            $datos[$count]['Cuarto_acu']=$detalle->detalle_rol_decimo_terceroacum;
            $datos[$count]['Tercero']=$detalle->detalle_rol_decimo_tercero;
            $datos[$count]['Cuarto']=$detalle->detalle_rol_decimo_cuarto;
            $datos[$count]['IECE']=$detalle->detalle_rol_aporte_iecesecap;
            $datos[$count]['SETEC']=0;
            
            $datos[$count]['quincena']=$detalle->detalle_rol_quincena;
            $datos[$count]['iess']=$detalle->detalle_rol_iess;
            $datos[$count]['iessasumidao']=$detalle->detalle_rol_iess_asumido;
            $datos[$count]['renta']=$detalle->detalle_rol_impuesto_renta;
            $datos[$count]['vaca_acu']=$detalle->detalle_rol_vacaciones_anticipadas;
            $datos[$count]['vaca']=$detalle->detalle_rol_vacaciones;
            $datos[$count]['fondos']=$detalle->detalle_rol_fondo_reserva;
            $count++;
        }



        DB::commit();
        return view('admin.recursosHumanos.listarRol.eliminaroperativo',['anticipo'=>$anticipo,'alimentacion'=>$alimentacion,'datos'=>$datos,'rol'=>$rol2,'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
       
    }catch(\Exception $ex){
        DB::rollBack();
        return redirect('/listaroles')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
    } 

    }
    public function verCheque(Request $request, $id){
        try{
            DB::beginTransaction();
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();         
            $rol= Cheque::BuscarRol($id)->get();
            $rol2= Rol_Consolidado::findOrFail($id);       
            DB::commit();
            return view('admin.recursosHumanos.listarRol.cambiochequeindividual',['rol'=>$rol2,'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);      
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('/listaroles')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        } 
    }
    public function store(Request $request)
    {
        if (isset($_POST['enviar'])){
            return $this->enviar($request);
        }
        if (isset($_POST['extraer'])){
          
            return $this->extraer($request);
 
        }
       
    }

    public function enviar(Request $request)
    {    
        try{
            DB::beginTransaction();
            $data=null;
            $urlcheque = '';
            $idempleado = $request->get('check');
            $count = $request->get('contador');
            
            
            $dias = $request->get('Tdias');
            $DCSueldo = $request->get('TCSueldo');
            $DTingresos = $request->get('Tingresos');
            $anticipos = $request->get('Tanticipos');
            $totalegre = $request->get('Ttotalegre');
            $Iesspatronal = $request->get('TIesspatronal');
            $Iesspersonal = $request->get('TIesspersonal');
            $fondoreser = $request->get('Tfondoreser');
            $total = $request->get('Ttotal');
            //detalle
            $Dsueldo = $request->get('Tsueldo');
            $Dextras = $request->get('Textras');
            $Dhoras_suplementarias = $request->get('Thoras_suplementarias');
            $Dotrosbon = $request->get('Totrosbon');
            $Dotrosin = $request->get('Totrosin');
            $Dfondo = $request->get('Tfondo');
            $Iess = $request->get('TIess');
            $Dmultas = $request->get('Tmultas');
            $Dalimentacion = $request->get('Talimentacion');
            $Dppqq = $request->get('Tppqq');

            $Dhipotecarios = $request->get('Thipotecarios');
            $Dprestamos = $request->get('Tprestamos');
            $Dsalud = $request->get('Tsalud');
            $Dleysol = $request->get('Tleysol');
            $Dotrosegre = $request->get('Totrosegre');
            $Iessasu = $request->get('TIessasu');
            $IECESECAP = $request->get('TIECESECAP');
            $Dvacaciones = $request->get('Tvacaciones');
            $Dtercero = $request->get('TCtercero');
            $Dcuarto = $request->get('TCcuarto');
            $Dporcentaje = $request->get('Tporcentaje');
            $Dtransporte = $request->get('Ttransporte');
            $Dinpuesto = $request->get('Timpurenta');

            $Dterceroacu = $request->get('TTerceroacu');
            $Dcuartoacu = $request->get('TCuartoacu');
            
            $DTtotalingresos = $request->get('Ttotalingresos');
            $DTtotalegresos = $request->get('Ttotalegresos');

            $quincena = $request->get('Tquincena');

            $quincenaid = $request->get('quincenaid');
            $aniticiposid = $request->get('anticiposid');
            
            $vacacionesid = $request->get('vacacionesid');
            $alimentacionid = $request->get('alimentacionid');

            $numerocheque=$request->get('idNcheque');
            $contador=1;
            for ($i = 0; $i < count($count); ++$i) {
                $j=floatval($count[$i]);
                $j=$j-1;
                $cumple=false;
                $valorquincena=0;
                $valoranticipos=0;
                $general = new generalController();
                $tipo=Empleado::EmpleadoBusquedaCuenta($idempleado[$j], 'sueldos')->first();
                $cierre = $general->cierre($request->get('fecha_hasta'),$tipo->sucursal_id);          
                if($cierre){
                    return redirect('rolConsolidado')->with('error2','No puede realizar la operacion por que pertenece a un mes bloqueado');
                }
                $cierre = $general->cierre($request->get('fecha_desde'),$tipo->sucursal_id);          
                if($cierre){
                    return redirect('rolConsolidado')->with('error2','No puede realizar la operacion por que pertenece a un mes bloqueado');
                }
                $cabecera_rol = new Rol_Consolidado();
                $cabecera_rol->cabecera_rol_fecha = $request->get('fecha_hasta');
                $cabecera_rol->cabecera_rol_total_dias = $dias[$j];
                $cabecera_rol->cabecera_rol_total_ingresos = floatval($DTtotalingresos[$j]);
                $cabecera_rol->cabecera_rol_total_anticipos =$anticipos[$j];
                $cabecera_rol->cabecera_rol_total_egresos =floatval($DTtotalegresos[$j]);
                $cabecera_rol->cabecera_rol_sueldo = floatval($DCSueldo[$j]);
                $cabecera_rol->cabecera_rol_pago = floatval($total[$j]);
                $cabecera_rol->cabecera_rol_fr_acumula = $fondoreser[$j];
                $cabecera_rol->cabecera_rol_iesspersonal = $Iesspersonal[$j];
                $cabecera_rol->cabecera_rol_iesspatronal = $Iesspatronal[$j];
                $cabecera_rol->empleado_id =$idempleado[$j];
                $cabecera_rol->cabecera_rol_tipo = 'CONSOLIDADO';
                $cabecera_rol->cabecera_rol_estado = 1;
                $data[$contador]["pago"]= $cabecera_rol->cabecera_rol_pago;
               
                $empleado=Empleado::Empleado($idempleado[$j])->get()->first();
                $data[$contador]["empleado"]=$empleado->empleado_nombre;
               
                if ($request->get('idTipo') == 'Cheque') {
                    $formatter = new NumeroALetras();
                    $cheque = new Cheque();
                    $cheque->cheque_numero = $numerocheque;
                    $numerocheque++;
                    $cheque->cheque_descripcion =  'PAGO ROL DE EMPLEADO '.$empleado->empleado_nombre.' CON MES Y AÑO '.date('Y', strtotime($request->get('fecha_hasta'))).'/'.date('F', strtotime($request->get('fecha_hasta')));
                    $cheque->cheque_beneficiario = $empleado->empleado_nombre;
                    $cheque->cheque_fecha_emision = $request->get('fecha_hasta');
                    $cheque->cheque_fecha_pago = $request->get('idFechaCheque');
                    $cheque->cheque_valor = $total[$j];
                    $cheque->cheque_valor_letras = $formatter->toInvoice($cheque->cheque_valor, 2, 'Dolares');
                    $cheque->cuenta_bancaria_id = $request->get('cuenta_id');
                    $cheque->cheque_estado = '1';
                    $cheque->empresa_id = Auth::user()->empresa->empresa_id;
                    $cheque->save();
                    $data[$contador]["cheque"]= $cheque->cheque_id;
                   
                    $general->registrarAuditoria('Registro de Cheque numero: -> '.$request->get('idNcheque'), '0', 'Por motivo de: -> '. $cabecera_rol->cabecera_rol_descripcion.' con el valor de: -> '.$total[$j]);
                }
                if ($request->get('idTipo') == 'Transferencia') {
                    $transferencia = new Transferencia();
                    $transferencia->transferencia_descripcion = 'PAGO ROL DE EMPLEADO '.$empleado->empleado_nombre.' CON MES Y AÑO '.date('Y', strtotime($request->get('fecha_hasta'))).'/'.date('F', strtotime($request->get('fecha_hasta')));
                    $transferencia->transferencia_beneficiario = $empleado->empleado_nombre;
                    $transferencia->transferencia_fecha = $request->get('fecha_hasta');
                    $transferencia->transferencia_valor =  $total[$j];
                    $transferencia->cuenta_bancaria_id = $request->get('cuenta_id');
                    $transferencia->transferencia_estado = '1';
                    $transferencia->empresa_id = Auth::user()->empresa->empresa_id;
                    $transferencia->save();
                    $data[$contador]["cheque"]= "0";
                    $general->registrarAuditoria('Registro de Transferencia numero: -> '.$request->get('ncuenta'), '0', 'Por motivo de: -> '. $cabecera_rol->cabecera_rol_descripcion.' con el valor de: -> '.$total[$j]);
                }

                $diario = new Diario();
                $diario->diario_codigo = $general->generarCodigoDiario($request->get('fecha_hasta'), 'CPRE');
                $diario->diario_fecha = $request->get('fecha_hasta');
                $diario->diario_referencia = 'COMPROBANTE DE PAGO DE ROL DE EMPLEADO';
                if ($request->get('tipo') == 'Cheque') {
                    $diario->diario_tipo_documento = 'CHEQUE';
                    $diario->diario_numero_documento = ($numerocheque-1);
                }
                if ($request->get('tipo') == 'Transferencia') {
                    $diario->diario_tipo_documento = 'TRANSFERENCIA';
                    $diario->diario_numero_documento = 0;
                }
                $diario->diario_tipo = 'CPRE';
                $diario->diario_secuencial = substr($diario->diario_codigo, 8);
                $diario->diario_mes = DateTime::createFromFormat('Y-m-d', $request->get('fecha_hasta'))->format('m');
                $diario->diario_ano = DateTime::createFromFormat('Y-m-d', $request->get('fecha_hasta'))->format('Y');
                $diario->diario_comentario = 'COMPROBANTE DE PAGO DE ROL DE EMPLEADO: '.$empleado->empleado_nombre.' Con el sueldo de: '.$DCSueldo[$j];
                $diario->diario_numero_documento = 0;
                $diario->diario_beneficiario =$empleado->empleado_nombre;
                $diario->diario_cierre = '0';
                $diario->diario_estado = '1';
                
                $diario->empresa_id = Auth::user()->empresa_id;
                $diario->sucursal_id =  $tipo->sucursal_id;
                $diario->save();
                $general->registrarAuditoria('Registro de diario de rol consolidado de Empleado -> '.$request->get('idEmpleado'), '0', 'Con motivo:'. $cabecera_rol->cabecera_rol_descripcion);
                $cabecera_rol->diariopago()->associate($diario); 
                
                /*
                $diariocontabilizado = new Diario();
                $diariocontabilizado->diario_codigo = $general->generarCodigoDiario($request->get('fecha_hasta'), 'CCMR');
                $diariocontabilizado->diario_fecha = $request->get('fecha_hasta');
                $diariocontabilizado->diario_referencia = 'COMPROBANTE DE CONTABILIZACION MENSUAL DE ROLES';
                $diariocontabilizado->diario_tipo_documento = 'CONTABILIZACION MENSUAL';
                $diariocontabilizado->diario_tipo = 'CCMR';
                $diariocontabilizado->diario_secuencial = substr($diario->diario_codigo, 8);
                $diariocontabilizado->diario_mes = DateTime::createFromFormat('Y-m-d', $request->get('fecha_hasta'))->format('m');
                $diariocontabilizado->diario_ano = DateTime::createFromFormat('Y-m-d', $request->get('fecha_hasta'))->format('Y');
                $diariocontabilizado->diario_comentario = 'COMPROBANTE DE CONTABILIZACION MENSUAL DE ROLES: '.$empleado->empleado_nombre.' Con el sueldo de: '.$DCSueldo[$j];
                $diariocontabilizado->diario_numero_documento = 0;
                $diariocontabilizado->diario_beneficiario =$empleado->empleado_nombre;
                $diariocontabilizado->diario_cierre = '0';
                $diariocontabilizado->diario_estado = '1';
                
                $diariocontabilizado->empresa_id = Auth::user()->empresa_id;
                $diariocontabilizado->sucursal_id =  $tipo->sucursal_id;
                $diariocontabilizado->save();
                $general->registrarAuditoria('Registro de diario de rol consolidado de Empleado -> '.$request->get('idEmpleado'), '0', 'Con motivo:'. $cabecera_rol->cabecera_rol_descripcion);
                $cabecera_rol->diariocontabilizacion()->associate($diariocontabilizado); 
                */

                $cabecera_rol->save();
                $data[$contador]["idrol"]=$cabecera_rol->cabecera_rol_id;
                $general->registrarAuditoria('Registro de Rol consolidado de Empleado -> '.$empleado->empleado_nombre, '0', 'Con motivo:'. $cabecera_rol->cabecera_rol_descripcion);
                


               


                if(isset($aniticiposid)){
                    for ($l = 0; $l < count($aniticiposid); ++$l) {
                        $anticipo=Anticipo_Empleado::findOrFail($aniticiposid[$l]);
                        if ($anticipo->empleado_id==$idempleado[$j]) {
                            $anticipodescuento=new Descuento_Anticipo_Empleado();
                            $anticipodescuento->descuento_fecha=$request->get('fecha_hasta');
                            $anticipodescuento->descuento_descripcion='Descuento de anticipo en Rol';
                            $anticipodescuento->descuento_valor=$anticipo->anticipo_saldo;
                            $anticipodescuento->descuento_estado='1';
                            $anticipodescuento->rol()->associate($cabecera_rol);
                            $anticipodescuento->anticipo()->associate($anticipo);
                            $anticipodescuento->diario()->associate($diario);
                            $anticipodescuento->save();

                            $auditoria = new generalController();
                            $auditoria->registrarAuditoria('Resgitro del descuento del Anticipo de empleado-> '.$request->get('idEmpleado'), '0', 'Por el pago de rol');
                        
                            $anticipo->anticipo_saldo=0;
                            $anticipo->anticipo_estado='2';
                            $anticipo->save();
                            $auditoria = new generalController();
                            $auditoria->registrarAuditoria('Actualizacion del Anticipo de estado a 2 con empleado-> '.$empleado->empleado_nombre, '0', 'Por el pago de rol');
                        }
                        
                    }
                }
                if (isset($quincenaid)) {
                    for ($k = 0; $k < count($quincenaid); ++$k) {
                        $quincenas=Quincena::findOrFail($quincenaid[$k]);
                        if ($quincenas->empleado_id==$idempleado[$j]) {
                            $quincenas->quincena_estado='2';
                            $quincenas->rol()->associate($cabecera_rol);
                            $quincenas->save();
                            $auditoria = new generalController();
                            $auditoria->registrarAuditoria('Actualizacion de Quincena de estado a 2 con empleado -> '.$empleado->empleado_nombre, '0', 'Por el pago de rol');
                        }
                    }
                }
                if (isset($alimentacionid)) {
                    for ($k = 0; $k < count($alimentacionid); ++$k) {
                        $alimentar=Alimentacion::findOrFail($alimentacionid[$k]);
                        if ($alimentar->empleado_id==$idempleado[$j]) {
                            $alimentar->alimentacion_estado='2';
                            $alimentar->rol()->associate($cabecera_rol);
                            $alimentar->save();
                            $auditoria = new generalController();
                            $auditoria->registrarAuditoria('Actualizacion de Alimentacion de estado a 2 con empleado -> '.$empleado->empleado_nombre, '0', 'Por el pago de rol');
                        }
                    }
                }
                if (isset($vacacionesid)) {
                    for ($k = 0; $k < count($vacacionesid); ++$k) {
                        $vacaciones=Quincena::findOrFail($vacacionesid[$k]);
                        if ($vacaciones->empleado_id==$idempleado[$j]) {
                            $vacaciones->quincena_estado='2';
                            $vacaciones->rol()->associate($cabecera_rol);
                            $vacaciones->save();
                            $auditoria = new generalController();
                            $auditoria->registrarAuditoria('Actualizacion de vacaciones de estado a 2 con empleado -> '.$empleado->empleado_nombre, '0', 'Por el pago de rol');
                        }
                    }
                }

                    $detalle= new Detalle_Rol();
                    $detalle->detalle_rol_fecha_inicio = $request->get('fecha_desde');
                    $detalle->detalle_rol_fecha_fin = $request->get('fecha_hasta');
                    $detalle->detalle_rol_sueldo = floatval($Dsueldo[$j]);
                    $detalle->detalle_rol_dias = $dias[$j];
                    $detalle->detalle_rol_valor_dia = floatval($Dsueldo[$j])/30;
                    $detalle->detalle_rol_total_dias = floatval($DCSueldo[$j]);
                    $detalle->detalle_rol_horas_extras = 0;
                    $detalle->detalle_rol_valor_he = $Dextras[$j];
                    $detalle->detalle_rol_bonificacion_dias = 0;
                    $detalle->detalle_rol_horas_suplementarias = $Dhoras_suplementarias[$j];
                    $detalle->detalle_rol_otra_bonificacion = $Dotrosbon[$j];
                    $detalle->detalle_rol_otros_ingresos = $Dotrosin[$j];
                    $detalle->detalle_rol_sueldo_rembolsable = 0;
                    $detalle->detalle_rol_fondo_reserva = floatval($Dfondo[$j]);
                    $detalle->detalle_rol_iess = floatval($Iess[$j]);
                    $detalle->detalle_rol_multa = $Dmultas[$j];
                    $detalle->detalle_rol_quincena = $quincena[$j];
                    $detalle->detalle_rol_total_anticipo = $anticipos[$j];
                    $detalle->detalle_rol_total_comisariato = $Dalimentacion[$j];
                    $detalle->detalle_rol_prestamo_quirografario = $Dppqq[$j];
                    $detalle->detalle_rol_prestamo_hipotecario = $Dhipotecarios[$j];
                    $detalle->detalle_rol_prestamo = $Dprestamos[$j];
                    $detalle->detalle_rol_transporte = $Dtransporte[$j];
                    $detalle->detalle_rol_ext_salud = $Dsalud[$j];
                    $detalle->detalle_rol_impuesto_renta = $Dinpuesto[$j];
                    $detalle->detalle_rol_ley_sol = $Dleysol[$j];
                    $detalle->detalle_rol_total_permiso = 0;
                    $detalle->detalle_rol_permiso_no_rem = 0;
                    $detalle->detalle_rol_cosecha = 0;
                    $detalle->detalle_rol_porcentaje = $Dporcentaje[$j];
                    $detalle->detalle_rol_otros_egresos = $Dotrosegre[$j];
                    $detalle->detalle_rol_liquido_pagar = floatval($total[$j]);
                    $detalle->detalle_rol_contabilizado = 1;
                    $detalle->detalle_rol_iess_asumido = floatval($Iessasu[$j]);
                    $detalle->detalle_rol_aporte_patronal = $Iesspatronal[$j];
                    $detalle->detalle_rol_aporte_iecesecap = $IECESECAP[$j];
                    $detalle->detalle_rol_vacaciones = 0;
                    $detalle->detalle_rol_vacaciones_anticipadas=$Dvacaciones[$j];
                    $detalle->detalle_rol_decimo_tercero = floatval($Dtercero[$j]);
                    $detalle->detalle_rol_decimo_cuarto = floatval($Dcuarto[$j]);
                    $detalle->detalle_rol_decimo_tercero = floatval($Dtercero[$j]);
                    $detalle->detalle_rol_decimo_cuartoacum = floatval($Dcuartoacu[$j]);
                    $detalle->detalle_rol_decimo_terceroacum = floatval($Dterceroacu[$j]);
                    $detalle->detalle_rol_total_egreso = floatval($DTtotalegresos[$j]);
                    $detalle->detalle_rol_total_ingreso = floatval($DTtotalingresos[$j]);
                    $detalle->detalle_rol_estado = 1;
                    $cabecera_rol->detalles()->save($detalle);
                    $data[$contador]["fecha"]=$detalle->detalle_rol_fecha_fin;
                    $data[$contador]["dias"]= $detalle->detalle_rol_dias;
                    $data[$contador]["sueldo"]= $detalle->detalle_rol_sueldo;
                    $general->registrarAuditoria('Registro de Detalle de Detalle Rol DE EMPLEADO -> '.$empleado->empleado_nombre, '0', ' con el valor de: -> '.$total[$j]);
                   
                   /*
                    ///////////////////////////Ingresos///////////////////////////////
                    if (floatval($DCSueldo[$j])>0) {
                        $detalleDiario = new Detalle_Diario();
                        $detalleDiario->detalle_debe = floatval($DCSueldo[$j]) ;
                        $detalleDiario->detalle_haber =0.00;
                        $detalleDiario->detalle_comentario = 'COMPROBANTE DE CONTABILIZACION MENSUAL DE ROLES  '.$empleado->empleado_nombre.' CON MES Y AÑO '.date('Y', strtotime($request->get('fecha_hasta'))).'/'.date('F', strtotime($request->get('fecha_hasta')));
                        $detalleDiario->detalle_tipo_documento = 'CONTABILIZACION MENSUAL';
                        $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                        $detalleDiario->detalle_conciliacion = '0';
                        $detalleDiario->detalle_estado = '1';
                        $tipo=Empleado::EmpleadoBusquedaCuenta($idempleado[$j], 'sueldos')->first();
                        $detalleDiario->cuenta_id = $tipo->cuenta_debe;
                        $detalleDiario->empleado_id =  $idempleado[$j];
                        $diariocontabilizado->detalles()->save($detalleDiario);
                        $general->registrarAuditoria('Registro de Detalle de Diario codigo: -> '.$diario->diario_codigo, '0', 'En la cuenta del Debe '.$tipo->cuenta_debe.' con el valor de: '.$DCSueldo[$j]);
                    }
                    if (floatval($Dotrosin[$j])>0) {
                        $detalleDiario = new Detalle_Diario();
                        $detalleDiario->detalle_debe = floatval($Dotrosin[$j]) ;
                        $detalleDiario->detalle_haber =0.00;
                        $detalleDiario->detalle_comentario = 'COMPROBANTE DE CONTABILIZACION MENSUAL DE ROLES  '.$empleado->empleado_nombre.' CON MES Y AÑO '.date('Y', strtotime($request->get('fecha_hasta'))).'/'.date('F', strtotime($request->get('fecha_hasta')));
                        $detalleDiario->detalle_tipo_documento = 'CONTABILIZACION MENSUAL';
                        $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                        $detalleDiario->detalle_conciliacion = '0';
                        $detalleDiario->detalle_estado = '1';
                        $tipo=Empleado::EmpleadoBusquedaCuenta($idempleado[$j], 'otrosIngresos')->first();
                        $detalleDiario->cuenta_id = $tipo->cuenta_debe;
                        $detalleDiario->empleado_id =  $idempleado[$j];
                        $diariocontabilizado->detalles()->save($detalleDiario);
                        $general->registrarAuditoria('Registro de Detalle de Diario codigo: -> '.$diario->diario_codigo, '0', 'En la cuenta del Debe '.$tipo->cuenta_debe.' con el valor de: '.$Dotrosin[$j]);
                    }
                    if (floatval($Dhoras_suplementarias[$j])>0) {
                        $detalleDiario = new Detalle_Diario();
                        $detalleDiario->detalle_debe = floatval($Dhoras_suplementarias[$j]) ;
                        $detalleDiario->detalle_haber =0.00;
                        $detalleDiario->detalle_comentario = 'COMPROBANTE DE CONTABILIZACION MENSUAL DE ROLES  '.$empleado->empleado_nombre.' CON MES Y AÑO '.date('Y', strtotime($request->get('fecha_hasta'))).'/'.date('F', strtotime($request->get('fecha_hasta')));
                        $detalleDiario->detalle_tipo_documento = 'CONTABILIZACION MENSUAL';
                        $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                        $detalleDiario->detalle_conciliacion = '0';
                        $detalleDiario->detalle_estado = '1';
                        $tipo=Empleado::EmpleadoBusquedaCuenta($idempleado[$j], 'horas_suplementarias')->first();
                        $detalleDiario->cuenta_id = $tipo->cuenta_debe;
                        $detalleDiario->empleado_id =  $idempleado[$j];
                        $diariocontabilizado->detalles()->save($detalleDiario);
                        $general->registrarAuditoria('Registro de Detalle de Diario codigo: -> '.$diario->diario_codigo, '0', 'En la cuenta del Debe '.$tipo->cuenta_debe.' con el valor de: '.$Dhoras_suplementarias[$j]);
                    }
                    if (floatval($Dtransporte[$j])>0) {
                        $detalleDiario = new Detalle_Diario();
                        $detalleDiario->detalle_debe = floatval($Dtransporte[$j]) ;
                        $detalleDiario->detalle_haber =0.00;
                        $detalleDiario->detalle_comentario = 'COMPROBANTE DE CONTABILIZACION MENSUAL DE ROLES  '.$empleado->empleado_nombre.' CON MES Y AÑO '.date('Y', strtotime($request->get('fecha_hasta'))).'/'.date('F', strtotime($request->get('fecha_hasta')));
                        $detalleDiario->detalle_tipo_documento = 'CONTABILIZACION MENSUAL';
                        $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                        $detalleDiario->detalle_conciliacion = '0';
                        $detalleDiario->detalle_estado = '1';
                        $tipo=Empleado::EmpleadoBusquedaCuenta($idempleado[$j], 'viaticos')->first();
                        $detalleDiario->cuenta_id = $tipo->cuenta_debe;
                        $detalleDiario->empleado_id =  $idempleado[$j];
                        $diariocontabilizado->detalles()->save($detalleDiario);
                        $general->registrarAuditoria('Registro de Detalle de Diario codigo: -> '.$diario->diario_codigo, '0', 'En la cuenta del Debe '.$tipo->cuenta_debe.' con el valor de: '.$Dtransporte[$j]);
                    }
                    if (floatval($Dotrosbon[$j])>0) {
                        $detalleDiario = new Detalle_Diario();
                        $detalleDiario->detalle_debe = floatval($Dotrosbon[$j]) ;
                        $detalleDiario->detalle_haber =0.00;
                        $detalleDiario->detalle_comentario = 'COMPROBANTE DE CONTABILIZACION MENSUAL DE ROLES  '.$empleado->empleado_nombre.' CON MES Y AÑO '.date('Y', strtotime($request->get('fecha_hasta'))).'/'.date('F', strtotime($request->get('fecha_hasta')));
                        $detalleDiario->detalle_tipo_documento = 'CONTABILIZACION MENSUAL';
                        $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                        $detalleDiario->detalle_conciliacion = '0';
                        $detalleDiario->detalle_estado = '1';
                        $tipo=Empleado::EmpleadoBusquedaCuenta($idempleado[$j], 'otrosBonificaciones')->first();
                        $detalleDiario->cuenta_id = $tipo->cuenta_debe;
                        $detalleDiario->empleado_id =  $idempleado[$j];
                        $diariocontabilizado->detalles()->save($detalleDiario);
                        $general->registrarAuditoria('Registro de Detalle de Diario codigo: -> '.$diario->diario_codigo, '0', 'En la cuenta del Debe '.$tipo->cuenta_debe.' con el valor de: '.$Dotrosbon[$j]);
                    }
                    if (floatval($Dotrosin[$j])>0) {
                        $detalleDiario = new Detalle_Diario();
                        $detalleDiario->detalle_debe = floatval($Dotrosin[$j]) ;
                        $detalleDiario->detalle_haber =0.00;
                        $detalleDiario->detalle_comentario = 'COMPROBANTE DE CONTABILIZACION MENSUAL DE ROLES  '.$empleado->empleado_nombre.' CON MES Y AÑO '.date('Y', strtotime($request->get('fecha_hasta'))).'/'.date('F', strtotime($request->get('fecha_hasta')));
                        $detalleDiario->detalle_tipo_documento = 'CONTABILIZACION MENSUAL';
                        $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                        $detalleDiario->detalle_conciliacion = '0';
                        $detalleDiario->detalle_estado = '1';
                        $tipo=Empleado::EmpleadoBusquedaCuenta($idempleado[$j], 'otrosIngresos')->first();
                        $detalleDiario->cuenta_id = $tipo->cuenta_debe;
                        $detalleDiario->empleado_id =  $idempleado[$j];
                        $diariocontabilizado->detalles()->save($detalleDiario);
                        $general->registrarAuditoria('Registro de Detalle de Diario codigo: -> '.$diario->diario_codigo, '0', 'En la cuenta del Debe '.$tipo->cuenta_debe.' con el valor de: '.$Dotrosin[$j]);
                    }

                    ///////////////////////////Egresos///////////////////////////////
                    if (floatval($Dsalud[$j])>0) {
                        

                        $detalleDiario = new Detalle_Diario();
                        $detalleDiario->detalle_debe = 0.00;
                        $detalleDiario->detalle_haber = floatval($Dsalud[$j]);
                        $detalleDiario->detalle_comentario = 'COMPROBANTE DE CONTABILIZACION MENSUAL DE ROLES '.$empleado->empleado_nombre.' CON MES Y AÑO '.date('Y', strtotime($request->get('fecha_hasta'))).'/'.date('F', strtotime($request->get('fecha_hasta')));
                        $detalleDiario->detalle_tipo_documento = 'CONTABILIZACION MENSUAL';
                        $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                        $detalleDiario->detalle_conciliacion = '0';
                        $detalleDiario->detalle_estado = '1';
                        $tipo=Empleado::EmpleadoBusquedaCuenta($idempleado[$j], 'extSalud')->first();
                        $detalleDiario->cuenta_id = $tipo->cuenta_haber;
                        $detalleDiario->empleado_id = $idempleado[$j];
                        $diariocontabilizado->detalles()->save($detalleDiario);
                        $general->registrarAuditoria('Registro de Detalle de Diario codigo: -> '.$diario->diario_codigo, '0', 'En la cuenta del Haber -> '.$tipo->cuenta_haber.' con el valor de: -> '.$Dsalud[$j]);
                    }
                    if (floatval($Dleysol[$j])>0) {
                        $detalleDiario = new Detalle_Diario();
                        $detalleDiario->detalle_debe = 0.00;
                        $detalleDiario->detalle_haber = floatval($Dleysol[$j]);
                        $detalleDiario->detalle_comentario = 'COMPROBANTE DE CONTABILIZACION MENSUAL DE ROLES '.$empleado->empleado_nombre.' CON MES Y AÑO '.date('Y', strtotime($request->get('fecha_hasta'))).'/'.date('F', strtotime($request->get('fecha_hasta')));
                        $detalleDiario->detalle_tipo_documento = 'CONTABILIZACION MENSUAL';
                        $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                        $detalleDiario->detalle_conciliacion = '0';
                        $detalleDiario->detalle_estado = '1';
                        $tipo=Empleado::EmpleadoBusquedaCuenta($idempleado[$j], 'leysalud')->first();
                        $detalleDiario->cuenta_id = $tipo->cuenta_haber;
                        $detalleDiario->empleado_id = $idempleado[$j];
                        $diariocontabilizado->detalles()->save($detalleDiario);
                        $general->registrarAuditoria('Registro de Detalle de Diario codigo: -> '.$diario->diario_codigo, '0', 'En la cuenta del Haber -> '.$tipo->cuenta_haber.' con el valor de: -> '.$Dleysol[$j]);
                    }
                    if (floatval($Dppqq[$j])>0) {
                        $detalleDiario = new Detalle_Diario();
                        $detalleDiario->detalle_debe = 0.00;
                        $detalleDiario->detalle_haber = floatval($Dppqq[$j]);
                        $detalleDiario->detalle_comentario = 'COMPROBANTE DE CONTABILIZACION MENSUAL DE ROLES '.$empleado->empleado_nombre.' CON MES Y AÑO '.date('Y', strtotime($request->get('fecha_hasta'))).'/'.date('F', strtotime($request->get('fecha_hasta')));
                        $detalleDiario->detalle_tipo_documento = 'CONTABILIZACION MENSUAL';
                        $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                        $detalleDiario->detalle_conciliacion = '0';
                        $detalleDiario->detalle_estado = '1';
                        $tipo=Empleado::EmpleadoBusquedaCuenta($idempleado[$j], 'prestamosQuirografarios')->first();
                        $detalleDiario->cuenta_id = $tipo->cuenta_haber;
                        $detalleDiario->empleado_id = $idempleado[$j];
                        $diariocontabilizado->detalles()->save($detalleDiario);
                        $general->registrarAuditoria('Registro de Detalle de Diario codigo: -> '.$diario->diario_codigo, '0', 'En la cuenta del Haber -> '.$tipo->cuenta_haber.' con el valor de: -> '.$Dppqq[$j]);
                    }
                    if (floatval($Dhipotecarios[$j])>0) {
                        $detalleDiario = new Detalle_Diario();
                        $detalleDiario->detalle_debe = 0.00;
                        $detalleDiario->detalle_haber = floatval($Dhipotecarios[$j]);
                        $detalleDiario->detalle_comentario = 'COMPROBANTE DE CONTABILIZACION MENSUAL DE ROLES '.$empleado->empleado_nombre.' CON MES Y AÑO '.date('Y', strtotime($request->get('fecha_hasta'))).'/'.date('F', strtotime($request->get('fecha_hasta')));
                        $detalleDiario->detalle_tipo_documento = 'CONTABILIZACION MENSUAL';
                        $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                        $detalleDiario->detalle_conciliacion = '0';
                        $detalleDiario->detalle_estado = '1';
                        $tipo=Empleado::EmpleadoBusquedaCuenta($idempleado[$j], 'prestamosHipotecarios')->first();
                        $detalleDiario->cuenta_id = $tipo->cuenta_haber;
                        $detalleDiario->empleado_id = $idempleado[$j];
                        $diariocontabilizado->detalles()->save($detalleDiario);
                        $general->registrarAuditoria('Registro de Detalle de Diario codigo: -> '.$diario->diario_codigo, '0', 'En la cuenta del Haber -> '.$tipo->cuenta_haber.' con el valor de: -> '.$Dhipotecarios[$j]);
                    }
                    if (floatval($Dalimentacion[$j])>0) {
                        $detalleDiario = new Detalle_Diario();
                        $detalleDiario->detalle_debe = 0.00;
                        $detalleDiario->detalle_haber = floatval($Dalimentacion[$j]);
                        $detalleDiario->detalle_comentario = 'COMPROBANTE DE CONTABILIZACION MENSUAL DE ROLES '.$empleado->empleado_nombre.' CON MES Y AÑO '.date('Y', strtotime($request->get('fecha_hasta'))).'/'.date('F', strtotime($request->get('fecha_hasta')));
                        $detalleDiario->detalle_tipo_documento = 'CONTABILIZACION MENSUAL';
                        $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                        $detalleDiario->detalle_conciliacion = '0';
                        $detalleDiario->detalle_estado = '1';
                        $tipo=Empleado::EmpleadoBusquedaCuenta($idempleado[$j], 'comisariato')->first();
                        $detalleDiario->cuenta_id = $tipo->cuenta_haber;
                        $detalleDiario->empleado_id = $idempleado[$j];
                        $diariocontabilizado->detalles()->save($detalleDiario);
                        $general->registrarAuditoria('Registro de Detalle de Diario codigo: -> '.$diario->diario_codigo, '0', 'En la cuenta del Haber -> '.$tipo->cuenta_haber.' con el valor de: -> '.$Dalimentacion[$j]);
                    }
                    if (floatval($Dprestamos[$j])>0) {
                        $detalleDiario = new Detalle_Diario();
                        $detalleDiario->detalle_debe = 0.00;
                        $detalleDiario->detalle_haber = floatval($Dprestamos[$j]);
                        $detalleDiario->detalle_comentario = 'COMPROBANTE DE CONTABILIZACION MENSUAL DE ROLES '.$empleado->empleado_nombre.' CON MES Y AÑO '.date('Y', strtotime($request->get('fecha_hasta'))).'/'.date('F', strtotime($request->get('fecha_hasta')));
                        $detalleDiario->detalle_tipo_documento = 'CONTABILIZACION MENSUAL';
                        $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                        $detalleDiario->detalle_conciliacion = '0';
                        $detalleDiario->detalle_estado = '1';
                        $tipo=Empleado::EmpleadoBusquedaCuenta($idempleado[$j], 'prestamos')->first();
                        $detalleDiario->cuenta_id = $tipo->cuenta_haber;
                        $detalleDiario->empleado_id = $idempleado[$j];
                        $diariocontabilizado->detalles()->save($detalleDiario);
                        $general->registrarAuditoria('Registro de Detalle de Diario codigo: -> '.$diario->diario_codigo, '0', 'En la cuenta del Haber -> '.$tipo->cuenta_haber.' con el valor de: -> '.$Dprestamos[$j]);
                    }
                    if (floatval($Dmultas[$j])>0) {
                        $detalleDiario = new Detalle_Diario();
                        $detalleDiario->detalle_debe = 0.00;
                        $detalleDiario->detalle_haber = floatval($Dmultas[$j]);
                        $detalleDiario->detalle_comentario = 'COMPROBANTE DE CONTABILIZACION MENSUAL DE ROLES '.$empleado->empleado_nombre.' CON MES Y AÑO '.date('Y', strtotime($request->get('fecha_hasta'))).'/'.date('F', strtotime($request->get('fecha_hasta')));
                        $detalleDiario->detalle_tipo_documento = 'CONTABILIZACION MENSUAL';
                        $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                        $detalleDiario->detalle_conciliacion = '0';
                        $detalleDiario->detalle_estado = '1';
                        $tipo=Empleado::EmpleadoBusquedaCuenta($idempleado[$j], 'multas')->first();
                        $detalleDiario->cuenta_id = $tipo->cuenta_haber;
                        $detalleDiario->empleado_id = $idempleado[$j];
                        $diariocontabilizado->detalles()->save($detalleDiario);
                        $general->registrarAuditoria('Registro de Detalle de Diario codigo: -> '.$diario->diario_codigo, '0', 'En la cuenta del Haber -> '.$tipo->cuenta_haber.' con el valor de: -> '.$Dmultas[$j]);
                    }
                    if (floatval($Iessasu[$j])>0) {
                        $detalleDiario = new Detalle_Diario();
                        $detalleDiario->detalle_debe = 0.00;
                        $detalleDiario->detalle_haber = floatval($Iessasu[$j]);
                        $detalleDiario->detalle_comentario = 'COMPROBANTE DE CONTABILIZACION MENSUAL DE ROLES '.$empleado->empleado_nombre.' CON MES Y AÑO '.date('Y', strtotime($request->get('fecha_hasta'))).'/'.date('F', strtotime($request->get('fecha_hasta')));
                        $detalleDiario->detalle_tipo_documento = 'CONTABILIZACION MENSUAL';
                        $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                        $detalleDiario->detalle_conciliacion = '0';
                        $detalleDiario->detalle_estado = '1';
                        $tipo=Empleado::EmpleadoBusquedaCuenta($idempleado[$j], 'iessAsumido')->first();
                        $detalleDiario->cuenta_id = $tipo->cuenta_haber;
                        $detalleDiario->empleado_id = $idempleado[$j];
                        $diariocontabilizado->detalles()->save($detalleDiario);
                        $general->registrarAuditoria('Registro de Detalle de Diario codigo: -> '.$diario->diario_codigo, '0', 'En la cuenta del Haber -> '.$tipo->cuenta_haber.' con el valor de: -> '.$Iessasu[$j]);
                    }
                    if (floatval($anticipos[$j])>0) {
                        $detalleDiario = new Detalle_Diario();
                        $detalleDiario->detalle_debe = 0.00;
                        $detalleDiario->detalle_haber = floatval($anticipos[$j]);
                        $detalleDiario->detalle_comentario = 'COMPROBANTE DE CONTABILIZACION MENSUAL DE ROLES '.$empleado->empleado_nombre.' CON MES Y AÑO '.date('Y', strtotime($request->get('fecha_hasta'))).'/'.date('F', strtotime($request->get('fecha_hasta')));
                        $detalleDiario->detalle_tipo_documento = 'CONTABILIZACION MENSUAL';
                        $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                        $detalleDiario->detalle_conciliacion = '0';
                        $detalleDiario->detalle_estado = '1';
                        $tipo=Empleado::EmpleadoBusquedaCuenta($idempleado[$j], 'anticipos')->first();
                        $detalleDiario->cuenta_id = $tipo->cuenta_haber;
                        $detalleDiario->empleado_id = $idempleado[$j];
                        $diariocontabilizado->detalles()->save($detalleDiario);
                        $general->registrarAuditoria('Registro de Detalle de Diario codigo: -> '.$diario->diario_codigo, '0', 'En la cuenta del Haber -> '.$tipo->cuenta_haber.' con el valor de: -> '.$anticipos[$j]);
                    }
                    if (floatval($Dinpuesto[$j])>0) {
                        $detalleDiario = new Detalle_Diario();
                        $detalleDiario->detalle_debe = 0.00;
                        $detalleDiario->detalle_haber = floatval($Dinpuesto[$j]);
                        $detalleDiario->detalle_comentario = 'COMPROBANTE DE CONTABILIZACION MENSUAL DE ROLES '.$empleado->empleado_nombre.' CON MES Y AÑO '.date('Y', strtotime($request->get('fecha_hasta'))).'/'.date('F', strtotime($request->get('fecha_hasta')));
                        $detalleDiario->detalle_tipo_documento = 'CONTABILIZACION MENSUAL';
                        $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                        $detalleDiario->detalle_conciliacion = '0';
                        $detalleDiario->detalle_estado = '1';
                        $tipo=Empleado::EmpleadoBusquedaCuenta($idempleado[$j], 'impuestoRenta')->first();
                        $detalleDiario->cuenta_id = $tipo->cuenta_haber;
                        $detalleDiario->empleado_id = $idempleado[$j];
                        $diariocontabilizado->detalles()->save($detalleDiario);
                        $general->registrarAuditoria('Registro de Detalle de Diario codigo: -> '.$diario->diario_codigo, '0', 'En la cuenta del Haber -> '.$tipo->cuenta_haber.' con el valor de: -> '.$Dinpuesto[$j]);
                    }
                    if (floatval($Dotrosegre[$j])>0) {
                        $detalleDiario = new Detalle_Diario();
                        $detalleDiario->detalle_debe = 0.00;
                        $detalleDiario->detalle_haber = floatval($Dotrosegre[$j]);
                        $detalleDiario->detalle_comentario = 'COMPROBANTE DE CONTABILIZACION MENSUAL DE ROLES '.$empleado->empleado_nombre.' CON MES Y AÑO '.date('Y', strtotime($request->get('fecha_hasta'))).'/'.date('F', strtotime($request->get('fecha_hasta')));
                        $detalleDiario->detalle_tipo_documento = 'CONTABILIZACION MENSUAL';
                        $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                        $detalleDiario->detalle_conciliacion = '0';
                        $detalleDiario->detalle_estado = '1';
                        $tipo=Empleado::EmpleadoBusquedaCuenta($idempleado[$j], 'otrosEgresos')->first();
                        $detalleDiario->cuenta_id = $tipo->cuenta_haber;
                        $detalleDiario->empleado_id = $idempleado[$j];
                        $diariocontabilizado->detalles()->save($detalleDiario);
                        $general->registrarAuditoria('Registro de Detalle de Diario codigo: -> '.$diario->diario_codigo, '0', 'En la cuenta del Haber -> '.$tipo->cuenta_haber.' con el valor de: -> '.$Dotrosegre[$j]);
                    }

                    if (floatval($cabecera_rol->cabecera_rol_iesspersonal)>0) {    
                        $detalleDiario = new Detalle_Diario();
                        $detalleDiario->detalle_debe = 0.00;
                        $detalleDiario->detalle_haber = floatval($cabecera_rol->cabecera_rol_iesspersonal);
                        $detalleDiario->detalle_comentario = 'COMPROBANTE DE CONTABILIZACION MENSUAL DE ROLES '.$empleado->empleado_nombre.' CON MES Y AÑO '.date('Y', strtotime($request->get('fechafinal'))).'/'.date('F', strtotime($request->get('fechafinal')));
                        $detalleDiario->detalle_tipo_documento = 'CONTABILIZACION MENSUAL';
                        $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                        $detalleDiario->detalle_conciliacion = '0';
                        $detalleDiario->detalle_estado = '1';
                        $tipo=Empleado::EmpleadoBusquedaCuenta($idempleado[$j], 'aportePersonal')->first();
                        $detalleDiario->cuenta_id = $tipo->cuenta_haber;
                        $detalleDiario->empleado_id = $idempleado[$j];
                        $diariocontabilizado->detalles()->save($detalleDiario);
                        $general->registrarAuditoria('Registro de Detalle de Diario codigo: -> '.$diario->diario_codigo, '0', 'En la cuenta del Haber -> '.$tipo->cuenta_haber.' con el valor de: -> '.floatval($cabecera_rol->cabecera_rol_iesspersonal));
                    }

                    if(((floatval($DCSueldo[$j])+floatval($Dotrosin[$j])+floatval($Dhoras_suplementarias[$j])+floatval($Dtransporte[$j])+floatval($Dotrosbon[$j])+floatval($Dotrosin[$j]))-(floatval($Dsalud[$j])+floatval($Dleysol[$j])+floatval($Dppqq[$j])+floatval($Dhipotecarios[$j])+floatval($Dalimentacion[$j])+floatval($Dprestamos[$j])+floatval($Dmultas[$j])+floatval($Iessasu[$j])+floatval($anticipos[$j])+floatval($Dinpuesto[$j])+floatval($Dotrosegre[$j])+floatval($cabecera_rol->cabecera_rol_iesspersonal)))>0){

                        $detalleDiario = new Detalle_Diario();
                        $detalleDiario->detalle_debe = 0.00;
                        $detalleDiario->detalle_haber = ((floatval($DCSueldo[$j])+floatval($Dotrosin[$j])+floatval($Dhoras_suplementarias[$j])+floatval($Dtransporte[$j])+floatval($Dotrosbon[$j])+floatval($Dotrosin[$j]))-(floatval($Dsalud[$j])+floatval($Dleysol[$j])+floatval($Dppqq[$j])+floatval($Dhipotecarios[$j])+floatval($Dalimentacion[$j])+floatval($Dprestamos[$j])+floatval($Dmultas[$j])+floatval($Iessasu[$j])+floatval($anticipos[$j])+floatval($Dinpuesto[$j])+floatval($Dotrosegre[$j])+floatval($cabecera_rol->cabecera_rol_iesspersonal)));
                        $detalleDiario->detalle_comentario = 'COMPROBANTE DE CONTABILIZACION MENSUAL DE ROLES '.$empleado->empleado_nombre.' CON MES Y AÑO '.date('Y', strtotime($request->get('fecha_hasta'))).'/'.date('F', strtotime($request->get('fecha_hasta')));
                        $detalleDiario->detalle_tipo_documento = 'CONTABILIZACION MENSUAL';
                        $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                        $detalleDiario->detalle_conciliacion = '0';
                        $detalleDiario->detalle_estado = '1';
                        $tipo=Empleado::EmpleadoBusquedaCuenta($idempleado[$j], 'sueldos')->first();
                        $detalleDiario->cuenta_id = $tipo->cuenta_haber;
                        $detalleDiario->empleado_id = $idempleado[$j];
                        $diariocontabilizado->detalles()->save($detalleDiario);
                        $general->registrarAuditoria('Registro de Detalle de Diario codigo: -> '.$diario->diario_codigo, '0', 'En la cuenta del Haber -> '.$tipo->cuenta_haber.' con el valor de: -> '.$detalleDiario->detalle_haber);
                    }


                    ///////////////////////////Provisiones///////////////////////////////
                    if (floatval($Iesspatronal[$j])>0) {
                        $detalleDiario = new Detalle_Diario();
                        $detalleDiario->detalle_debe = 0.00;
                        $detalleDiario->detalle_haber = floatval($Iesspatronal[$j]);
                        $detalleDiario->detalle_comentario = 'COMPROBANTE DE CONTABILIZACION MENSUAL DE ROLES '.$empleado->empleado_nombre.' CON MES Y AÑO '.date('Y', strtotime($request->get('fecha_hasta'))).'/'.date('F', strtotime($request->get('fecha_hasta')));
                        $detalleDiario->detalle_tipo_documento = 'CONTABILIZACION MENSUAL';
                        $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                        $detalleDiario->detalle_conciliacion = '0';
                        $detalleDiario->detalle_estado = '1';
                        $tipo=Empleado::EmpleadoBusquedaCuenta($idempleado[$j], 'aportePatronal')->first();
                        $detalleDiario->cuenta_id = $tipo->cuenta_debe;
                        $detalleDiario->empleado_id = $idempleado[$j];
                        $diariocontabilizado->detalles()->save($detalleDiario);
                        $general->registrarAuditoria('Registro de Detalle de Diario codigo: -> '.$diario->diario_codigo, '0', 'En la cuenta del Haber -> '.$tipo->cuenta_debe.' con el valor de: -> '.$Iesspatronal[$j]);
               

                        $detalleDiario = new Detalle_Diario();
                        $detalleDiario->detalle_debe = 0.00;
                        $detalleDiario->detalle_haber = floatval($Iesspatronal[$j]);
                        $detalleDiario->detalle_comentario = 'COMPROBANTE DE CONTABILIZACION MENSUAL DE ROLES '.$empleado->empleado_nombre.' CON MES Y AÑO '.date('Y', strtotime($request->get('fecha_hasta'))).'/'.date('F', strtotime($request->get('fecha_hasta')));
                        $detalleDiario->detalle_tipo_documento = 'CONTABILIZACION MENSUAL';
                        $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                        $detalleDiario->detalle_conciliacion = '0';
                        $detalleDiario->detalle_estado = '1';
                        $tipo=Empleado::EmpleadoBusquedaCuenta($idempleado[$j], 'aportePatronal')->first();
                        $detalleDiario->cuenta_id = $tipo->cuenta_haber;
                        $detalleDiario->empleado_id = $idempleado[$j];
                        $diariocontabilizado->detalles()->save($detalleDiario);
                        $general->registrarAuditoria('Registro de Detalle de Diario codigo: -> '.$diario->diario_codigo, '0', 'En la cuenta del Haber -> '.$tipo->cuenta_haber.' con el valor de: -> '.$Iesspatronal[$j]);
                    }
                    if (floatval($Dvacaciones[$j])>0) {
                        $detalleDiario = new Detalle_Diario();
                        $detalleDiario->detalle_debe = floatval($Dvacaciones[$j]);
                        $detalleDiario->detalle_haber = 0.00;
                        $detalleDiario->detalle_comentario = 'COMPROBANTE DE CONTABILIZACION MENSUAL DE ROLES '.$empleado->empleado_nombre.' CON MES Y AÑO '.date('Y', strtotime($request->get('fecha_hasta'))).'/'.date('F', strtotime($request->get('fecha_hasta')));
                        $detalleDiario->detalle_tipo_documento = 'CONTABILIZACION MENSUAL';
                        $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                        $detalleDiario->detalle_conciliacion = '0';
                        $detalleDiario->detalle_estado = '1';
                        $tipo=Empleado::EmpleadoBusquedaCuenta($idempleado[$j], 'vacacion')->first();
                        $detalleDiario->cuenta_id = $tipo->cuenta_debe;
                        $detalleDiario->empleado_id = $idempleado[$j];
                        $diariocontabilizado->detalles()->save($detalleDiario);
                        $general->registrarAuditoria('Registro de Detalle de Diario codigo: -> '.$diario->diario_codigo, '0', 'En la cuenta del Haber -> '.$tipo->cuenta_debe.' con el valor de: -> '.$Dvacaciones[$j]);
              

                        $detalleDiario = new Detalle_Diario();
                        $detalleDiario->detalle_debe = 0.00;
                        $detalleDiario->detalle_haber = floatval($Dvacaciones[$j]);
                        $detalleDiario->detalle_comentario = 'COMPROBANTE DE CONTABILIZACION MENSUAL DE ROLES '.$empleado->empleado_nombre.' CON MES Y AÑO '.date('Y', strtotime($request->get('fecha_hasta'))).'/'.date('F', strtotime($request->get('fecha_hasta')));
                        $detalleDiario->detalle_tipo_documento = 'CONTABILIZACION MENSUAL';
                        $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                        $detalleDiario->detalle_conciliacion = '0';
                        $detalleDiario->detalle_estado = '1';
                        $tipo=Empleado::EmpleadoBusquedaCuenta($idempleado[$j], 'vacacion')->first();
                        $detalleDiario->cuenta_id = $tipo->cuenta_haber;
                        $detalleDiario->empleado_id = $idempleado[$j];
                        $diariocontabilizado->detalles()->save($detalleDiario);
                        $general->registrarAuditoria('Registro de Detalle de Diario codigo: -> '.$diario->diario_codigo, '0', 'En la cuenta del Haber -> '.$tipo->cuenta_haber.' con el valor de: -> '.$Dvacaciones[$j]);
                    }
                    if (floatval($Dtercero[$j])>0) {
                        $detalleDiario = new Detalle_Diario();
                        $detalleDiario->detalle_debe = floatval($Dtercero[$j]);
                        $detalleDiario->detalle_haber = 0.00;
                        $detalleDiario->detalle_comentario = 'COMPROBANTE DE CONTABILIZACION MENSUAL DE ROLES '.$empleado->empleado_nombre.' CON MES Y AÑO '.date('Y', strtotime($request->get('fecha_hasta'))).'/'.date('F', strtotime($request->get('fecha_hasta')));
                        $detalleDiario->detalle_tipo_documento = 'CONTABILIZACION MENSUAL';
                        $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                        $detalleDiario->detalle_conciliacion = '0';
                        $detalleDiario->detalle_estado = '1';
                        $tipo=Empleado::EmpleadoBusquedaCuenta($idempleado[$j], 'decimoTercero')->first();
                        $detalleDiario->cuenta_id = $tipo->cuenta_debe;
                        $detalleDiario->empleado_id = $idempleado[$j];
                        $diariocontabilizado->detalles()->save($detalleDiario);
                        $general->registrarAuditoria('Registro de Detalle de Diario codigo: -> '.$diario->diario_codigo, '0', 'En la cuenta del Haber -> '.$tipo->cuenta_debe.' con el valor de: -> '.$Dtercero[$j]);
             

                        $detalleDiario = new Detalle_Diario();
                        $detalleDiario->detalle_debe = 0.00;
                        $detalleDiario->detalle_haber = floatval($Dtercero[$j]);
                        $detalleDiario->detalle_comentario = 'COMPROBANTE DE CONTABILIZACION MENSUAL DE ROLES '.$empleado->empleado_nombre.' CON MES Y AÑO '.date('Y', strtotime($request->get('fecha_hasta'))).'/'.date('F', strtotime($request->get('fecha_hasta')));
                        $detalleDiario->detalle_tipo_documento = 'CONTABILIZACION MENSUAL';
                        $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                        $detalleDiario->detalle_conciliacion = '0';
                        $detalleDiario->detalle_estado = '1';
                        $tipo=Empleado::EmpleadoBusquedaCuenta($idempleado[$j], 'decimoTercero')->first();
                        $detalleDiario->cuenta_id = $tipo->cuenta_haber;
                        $detalleDiario->empleado_id = $idempleado[$j];
                        $diariocontabilizado->detalles()->save($detalleDiario);
                        $general->registrarAuditoria('Registro de Detalle de Diario codigo: -> '.$diario->diario_codigo, '0', 'En la cuenta del Haber -> '.$tipo->cuenta_haber.' con el valor de: -> '.$Dtercero[$j]);
                    }
                    if (floatval($Dcuarto[$j])>0) {
                        $detalleDiario = new Detalle_Diario();
                        $detalleDiario->detalle_debe = floatval($Dcuarto[$j]);
                        $detalleDiario->detalle_haber = 0.00;
                        $detalleDiario->detalle_comentario = 'COMPROBANTE DE CONTABILIZACION MENSUAL DE ROLES '.$empleado->empleado_nombre.' CON MES Y AÑO '.date('Y', strtotime($request->get('fecha_hasta'))).'/'.date('F', strtotime($request->get('fecha_hasta')));
                        $detalleDiario->detalle_tipo_documento = 'CONTABILIZACION MENSUAL';
                        $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                        $detalleDiario->detalle_conciliacion = '0';
                        $detalleDiario->detalle_estado = '1';
                        $tipo=Empleado::EmpleadoBusquedaCuenta($idempleado[$j], 'decimoCuarto')->first();
                        $detalleDiario->cuenta_id = $tipo->cuenta_debe;
                        $detalleDiario->empleado_id = $idempleado[$j];
                        $diariocontabilizado->detalles()->save($detalleDiario);
                        $general->registrarAuditoria('Registro de Detalle de Diario codigo: -> '.$diario->diario_codigo, '0', 'En la cuenta del Haber -> '.$tipo->cuenta_debe.' con el valor de: -> '.$Dcuarto[$j]);
                   

                        $detalleDiario = new Detalle_Diario();
                        $detalleDiario->detalle_debe = 0.00;
                        $detalleDiario->detalle_haber = floatval($Dcuarto[$j]);
                        $detalleDiario->detalle_comentario = 'COMPROBANTE DE CONTABILIZACION MENSUAL DE ROLES '.$empleado->empleado_nombre.' CON MES Y AÑO '.date('Y', strtotime($request->get('fecha_hasta'))).'/'.date('F', strtotime($request->get('fecha_hasta')));
                        $detalleDiario->detalle_tipo_documento = 'CONTABILIZACION MENSUAL';
                        $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                        $detalleDiario->detalle_conciliacion = '0';
                        $detalleDiario->detalle_estado = '1';
                        $tipo=Empleado::EmpleadoBusquedaCuenta($idempleado[$j], 'decimoCuarto')->first();
                        $detalleDiario->cuenta_id = $tipo->cuenta_haber;
                        $detalleDiario->empleado_id = $idempleado[$j];
                        $diariocontabilizado->detalles()->save($detalleDiario);
                        $general->registrarAuditoria('Registro de Detalle de Diario codigo: -> '.$diario->diario_codigo, '0', 'En la cuenta del Haber -> '.$tipo->cuenta_haber.' con el valor de: -> '.$Dcuarto[$j]);
                    }
                    if (floatval($Dfondo[$j])>0) {
                        $detalleDiario = new Detalle_Diario();
                        $detalleDiario->detalle_debe = floatval($Dfondo[$j]);
                        $detalleDiario->detalle_haber = 0.00;
                        $detalleDiario->detalle_comentario = 'COMPROBANTE DE CONTABILIZACION MENSUAL DE ROLES '.$empleado->empleado_nombre.' CON MES Y AÑO '.date('Y', strtotime($request->get('fecha_hasta'))).'/'.date('F', strtotime($request->get('fecha_hasta')));
                        $detalleDiario->detalle_tipo_documento = 'CONTABILIZACION MENSUAL';
                        $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                        $detalleDiario->detalle_conciliacion = '0';
                        $detalleDiario->detalle_estado = '1';
                        $tipo=Empleado::EmpleadoBusquedaCuenta($idempleado[$j], 'fondoReserva')->first();
                        $detalleDiario->cuenta_id = $tipo->cuenta_debe;
                        $detalleDiario->empleado_id = $idempleado[$j];
                        $diariocontabilizado->detalles()->save($detalleDiario);
                        $general->registrarAuditoria('Registro de Detalle de Diario codigo: -> '.$diario->diario_codigo, '0', 'En la cuenta del Haber -> '.$tipo->cuenta_debe.' con el valor de: -> '.$Dfondo[$j]);
          

                        $detalleDiario = new Detalle_Diario();
                        $detalleDiario->detalle_debe = 0.00;
                        $detalleDiario->detalle_haber = floatval($Dfondo[$j]);
                        $detalleDiario->detalle_comentario = 'COMPROBANTE DE CONTABILIZACION MENSUAL DE ROLES '.$empleado->empleado_nombre.' CON MES Y AÑO '.date('Y', strtotime($request->get('fecha_hasta'))).'/'.date('F', strtotime($request->get('fecha_hasta')));
                        $detalleDiario->detalle_tipo_documento = 'CONTABILIZACION MENSUAL';
                        $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                        $detalleDiario->detalle_conciliacion = '0';
                        $detalleDiario->detalle_estado = '1';
                        $tipo=Empleado::EmpleadoBusquedaCuenta($idempleado[$j], 'fondoReserva')->first();
                        $detalleDiario->cuenta_id = $tipo->cuenta_haber;
                        $detalleDiario->empleado_id = $idempleado[$j];
                        $diariocontabilizado->detalles()->save($detalleDiario);
                        $general->registrarAuditoria('Registro de Detalle de Diario codigo: -> '.$diario->diario_codigo, '0', 'En la cuenta del Haber -> '.$tipo->cuenta_haber.' con el valor de: -> '.$Dfondo[$j]);
                    }
                    if (floatval($fondoreser[$j])>0) {
                        $detalleDiario = new Detalle_Diario();
                        $detalleDiario->detalle_debe = floatval($fondoreser[$j]);
                        $detalleDiario->detalle_haber = 0.00;
                        $detalleDiario->detalle_comentario = 'COMPROBANTE DE CONTABILIZACION MENSUAL DE ROLES '.$empleado->empleado_nombre.' CON MES Y AÑO '.date('Y', strtotime($request->get('fecha_hasta'))).'/'.date('F', strtotime($request->get('fecha_hasta')));
                        $detalleDiario->detalle_tipo_documento = 'CONTABILIZACION MENSUAL';
                        $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                        $detalleDiario->detalle_conciliacion = '0';
                        $detalleDiario->detalle_estado = '1';
                        $tipo=Empleado::EmpleadoBusquedaCuenta($idempleado[$j], 'fondoReservaAcumulada')->first();
                        $detalleDiario->cuenta_id = $tipo->cuenta_debe;
                        $detalleDiario->empleado_id = $idempleado[$j];
                        $diariocontabilizado->detalles()->save($detalleDiario);
                        $general->registrarAuditoria('Registro de Detalle de Diario codigo: -> '.$diario->diario_codigo, '0', 'En la cuenta del Haber -> '.$tipo->cuenta_debe.' con el valor de: -> '.$fondoreser[$j]);
                   

                        $detalleDiario = new Detalle_Diario();
                        $detalleDiario->detalle_debe = 0.00;
                        $detalleDiario->detalle_haber = floatval($fondoreser[$j]);
                        $detalleDiario->detalle_comentario = 'COMPROBANTE DE CONTABILIZACION MENSUAL DE ROLES '.$empleado->empleado_nombre.' CON MES Y AÑO '.date('Y', strtotime($request->get('fecha_hasta'))).'/'.date('F', strtotime($request->get('fecha_hasta')));
                        $detalleDiario->detalle_tipo_documento = 'CONTABILIZACION MENSUAL';
                        $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                        $detalleDiario->detalle_conciliacion = '0';
                        $detalleDiario->detalle_estado = '1';
                        $tipo=Empleado::EmpleadoBusquedaCuenta($idempleado[$j], 'fondoReservaAcumulada')->first();
                        $detalleDiario->cuenta_id = $tipo->cuenta_haber;
                        $detalleDiario->empleado_id = $idempleado[$j];
                        $diariocontabilizado->detalles()->save($detalleDiario);
                        $general->registrarAuditoria('Registro de Detalle de Diario codigo: -> '.$diario->diario_codigo, '0', 'En la cuenta del Haber -> '.$tipo->cuenta_haber.' con el valor de: -> '.$fondoreser[$j]);
                    }
                    if (floatval($IECESECAP[$j])>0) {
                        $detalleDiario = new Detalle_Diario();
                        $detalleDiario->detalle_debe = floatval($IECESECAP[$j]);
                        $detalleDiario->detalle_haber = 0.00;
                        $detalleDiario->detalle_comentario = 'COMPROBANTE DE CONTABILIZACION MENSUAL DE ROLES '.$empleado->empleado_nombre.' CON MES Y AÑO '.date('Y', strtotime($request->get('fecha_hasta'))).'/'.date('F', strtotime($request->get('fecha_hasta')));
                        $detalleDiario->detalle_tipo_documento = 'CONTABILIZACION MENSUAL';
                        $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                        $detalleDiario->detalle_conciliacion = '0';
                        $detalleDiario->detalle_estado = '1';
                        $tipo=Empleado::EmpleadoBusquedaCuenta($idempleado[$j], 'iece')->first();
                        $detalleDiario->cuenta_id = $tipo->cuenta_debe;
                        $detalleDiario->empleado_id = $idempleado[$j];
                        $diariocontabilizado->detalles()->save($detalleDiario);
                        $general->registrarAuditoria('Registro de Detalle de Diario codigo: -> '.$diario->diario_codigo, '0', 'En la cuenta del Haber -> '.$tipo->cuenta_debe.' con el valor de: -> '.$IECESECAP[$j]);
            

                        $detalleDiario = new Detalle_Diario();
                        $detalleDiario->detalle_debe = 0.00;
                        $detalleDiario->detalle_haber = floatval($IECESECAP[$j]);
                        $detalleDiario->detalle_comentario = 'COMPROBANTE DE CONTABILIZACION MENSUAL DE ROLES '.$empleado->empleado_nombre.' CON MES Y AÑO '.date('Y', strtotime($request->get('fecha_hasta'))).'/'.date('F', strtotime($request->get('fecha_hasta')));
                        $detalleDiario->detalle_tipo_documento = 'CONTABILIZACION MENSUAL';
                        $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                        $detalleDiario->detalle_conciliacion = '0';
                        $detalleDiario->detalle_estado = '1';
                        $tipo=Empleado::EmpleadoBusquedaCuenta($idempleado[$j], 'iece')->first();
                        $detalleDiario->cuenta_id = $tipo->cuenta_haber;
                        $detalleDiario->empleado_id = $idempleado[$j];
                        $diariocontabilizado->detalles()->save($detalleDiario);
                        $general->registrarAuditoria('Registro de Detalle de Diario codigo: -> '.$diario->diario_codigo, '0', 'En la cuenta del Haber -> '.$tipo->cuenta_haber.' con el valor de: -> '.$IECESECAP[$j]);
                    }
                    */

                    $detalleDiario = new Detalle_Diario();
                    $detalleDiario->detalle_debe = 0.00;
                    $detalleDiario->detalle_haber = floatval($total[$j]);
                    $detalleDiario->detalle_comentario = 'COMPROBANTE DE PAGO DE ROL DE EMPLEADO -> '.$empleado->empleado_nombre.' CON MES Y AÑO '.date('Y', strtotime($request->get('fecha_hasta'))).'/'.date('F', strtotime($request->get('fecha_hasta')));
                    $detalleDiario->detalle_tipo_documento = 'ROL CONSOLIDADO';
                    $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                    $detalleDiario->detalle_conciliacion = '0';
                    $detalleDiario->detalle_estado = '1';
                    $detalleDiario->cuenta_id = $request->get('idCuentaContable');
                    if ($request->get('idTipo') == 'Cheque'){      
                        $detalleDiario->cheque()->associate($cheque);
                    }
                    if ($request->get('idTipo') == 'Transferencia'){      
                        $detalleDiario->transferencia()->associate($transferencia);
                    }
                    $diario->detalles()->save($detalleDiario);
                    $general->registrarAuditoria('Registro de Detalle de Diario codigo: -> '.$diario->diario_codigo, '0', 'En la cuenta del Haber -> '.$request->get('idCuentaContable').' con el valor de: -> '.$total[$j]);

                    
                    if (floatval($Dfondo[$j])>0) {
                        $detalleDiario = new Detalle_Diario();
                        $detalleDiario->detalle_debe =  floatval($Dfondo[$j]);
                        $detalleDiario->detalle_haber =0.00;
                        $detalleDiario->detalle_comentario = 'COMPROBANTE DE PAGO DE ROL DE EMPLEADO ->  '.$empleado->empleado_nombre.' CON MES Y AÑO '.date('Y', strtotime($request->get('fecha_hasta'))).'/'.date('F', strtotime($request->get('fecha_hasta')));
                        $detalleDiario->detalle_tipo_documento = 'ROL CONSOLIDADO';
                        $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                        $detalleDiario->detalle_conciliacion = '0';
                        $detalleDiario->detalle_estado = '1';                
                        $tipo=Empleado::EmpleadoBusquedaCuenta($idempleado[$j], 'fondoReserva')->first();
                        $detalleDiario->cuenta_id = $tipo->cuenta_haber;
                        $detalleDiario->empleado_id =  $idempleado[$j];
                        $diario->detalles()->save($detalleDiario);
                        $general->registrarAuditoria('Registro de Detalle de Diario codigo: -> '.$diario->diario_codigo, '0', 'En la cuenta del Debe -> '.$tipo->cuenta_haber.' con el valor de: -> '.$total[$j]);
                    }
                    if ( floatval($Dtercero[$j])>0) {
                        $detalleDiario = new Detalle_Diario();
                        $detalleDiario->detalle_debe = floatval($Dtercero[$j]) ;
                        $detalleDiario->detalle_haber =0.00;
                        $detalleDiario->detalle_comentario = 'COMPROBANTE DE PAGO DE ROL DE EMPLEADO ->  '.$empleado->empleado_nombre.' CON MES Y AÑO '.date('Y', strtotime($request->get('fecha_hasta'))).'/'.date('F', strtotime($request->get('fecha_hasta')));
                        $detalleDiario->detalle_tipo_documento = 'ROL CONSOLIDADO';
                        $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                        $detalleDiario->detalle_conciliacion = '0';
                        $detalleDiario->detalle_estado = '1';                
                        $tipo=Empleado::EmpleadoBusquedaCuenta($idempleado[$j], 'decimoTercero')->first();
                        $detalleDiario->cuenta_id = $tipo->cuenta_haber;
                        $detalleDiario->empleado_id =  $idempleado[$j];
                        $diario->detalles()->save($detalleDiario);
                        $general->registrarAuditoria('Registro de Detalle de Diario codigo: -> '.$diario->diario_codigo, '0', 'En la cuenta del Debe -> '.$tipo->cuenta_haber.' con el valor de: -> '.$total[$j]);
                    }
                    if (floatval($Dcuarto[$j])>0) {
                        $detalleDiario = new Detalle_Diario();
                        $detalleDiario->detalle_debe =  floatval($Dcuarto[$j]);
                        $detalleDiario->detalle_haber =0.00;
                        $detalleDiario->detalle_comentario = 'COMPROBANTE DE PAGO DE ROL DE EMPLEADO ->  '.$empleado->empleado_nombre.' CON MES Y AÑO '.date('Y', strtotime($request->get('fecha_hasta'))).'/'.date('F', strtotime($request->get('fecha_hasta')));
                        $detalleDiario->detalle_tipo_documento = 'ROL CONSOLIDADO';
                        $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                        $detalleDiario->detalle_conciliacion = '0';
                        $detalleDiario->detalle_estado = '1';                
                        $tipo=Empleado::EmpleadoBusquedaCuenta($idempleado[$j], 'decimoCuarto')->first();
                        $detalleDiario->cuenta_id = $tipo->cuenta_haber;
                        $detalleDiario->empleado_id = $idempleado[$j];
                        $diario->detalles()->save($detalleDiario);
                        $general->registrarAuditoria('Registro de Detalle de Diario codigo: -> '.$diario->diario_codigo, '0', 'En la cuenta del Debe -> '.$tipo->cuenta_haber.' con el valor de: -> '.$total[$j]);
                    }
                        $tipo=Empleado::EmpleadoBusquedaCuenta($idempleado[$j], 'sueldos')->first();
                        $detalleDiario = new Detalle_Diario();
                        $detalleDiario->detalle_debe =  floatval($total[$j])-(floatval($Dcuarto[$j])+floatval($Dtercero[$j])+ floatval($Dfondo[$j]) );
                        $detalleDiario->detalle_haber =0.00;
                        $detalleDiario->detalle_comentario = 'COMPROBANTE DE PAGO DE ROL DE EMPLEADO ->  '.$empleado->empleado_nombre.' CON MES Y AÑO '.date('Y', strtotime($request->get('fecha_hasta'))).'/'.date('F', strtotime($request->get('fecha_hasta')));
                        $detalleDiario->detalle_tipo_documento = 'ROL CONSOLIDADO';
                        $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                        $detalleDiario->detalle_conciliacion = '0';
                        $detalleDiario->detalle_estado = '1';
                        $detalleDiario->cuenta_id = $tipo->cuenta_haber;
                        $detalleDiario->empleado_id = $idempleado[$j];
                        $diario->detalles()->save($detalleDiario);
                        $general->registrarAuditoria('Registro de Detalle de Diario codigo: -> '.$diario->diario_codigo, '0', 'En la cuenta del Haber -> '.$request->get('idCuentaContable').' con el valor de: -> '.$total[$j]);
               $contador++;
            }
           
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
           
            DB::commit();
            return view('admin.recursosHumanos.rolPagoConsolidado.impresion', ['data'=>$data,'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
         
           
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('/rolConsolidado/new/'.$request->get('punto'))->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }  
      
        
    }
    public function ver($fecha)
    {
        try{
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            return view('admin.recursosHumanos.rolPagoConsolidado.impresion', ['Rol'=>Rol_Consolidado::RolesFecha($fecha)->get(),'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        }
        catch(\Exception $ex){      
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }

}
