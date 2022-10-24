<?php

namespace App\Http\Controllers;
use App\Models\Anticipo_Cliente;
use App\Models\Detalle_Diario;
use App\Models\Cliente;
use App\Models\Cuenta;
use App\Models\Parametrizacion_Contable;
use App\Models\Banco;
use App\Models\Diario;
use App\Models\Punto_Emision;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Http\Controllers\Controller;
use App\Models\Arqueo_Caja;
use App\Models\Caja;
use App\Models\Cuenta_Bancaria;
use App\Models\Cuenta_Cobrar;
use App\Models\Deposito;
use App\Models\Descuento_Anticipo_Cliente;
use App\Models\Empresa;
use App\Models\Movimiento_Caja;
use App\Models\Rango_Documento;
use App\Models\Sucursal;
use DateTime;
use Illuminate\Http\Request;
use PhpParser\Node\Stmt\Return_;
use Maatwebsite\Excel\Facades\Excel;

class anticipoClienteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        
    }
    public function excelAnticipo()
    {
        try{
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            return view('admin.cuentasCobrar.anticipoCliente.cargarExcel',['PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function CargarExcel(Request $request)
    {
        try{
            DB::beginTransaction();
            if($request->file('excelEmpl')->isValid()){
                $empresa = Empresa::empresa()->first();
                $name = $empresa->empresa_ruc. '.' .$request->file('excelEmpl')->getClientOriginalExtension();
                $path = $request->file('excelEmpl')->move(public_path().'\temp', $name); 
                $array = Excel::toArray(new Anticipo_Cliente(), $path); 
                $detalleDiarioAux =  Detalle_Diario::findOrFail($request->get('idDiario'));
                $diario = $detalleDiarioAux->diario;
                $activador=false;
                $general = new generalController(); 
                for ($i=1;$i < count($array[0]);$i++) {
                    $validar=trim($array[0][$i][5]);
                    
                    if ($validar) {
                      
                        $cliente = Cliente::ClientesByCedula($validar)->first();
                      
                        if ($cliente) {
                          
                            /*extraer punto de emision y secuencial*/
                           
                            $puntoemeision = null;
                            $puntosEmision = Punto_Emision::PuntoxSucursal($diario->sucursal_id)->get();
                            foreach($puntosEmision as $punto){
                                $rangoDocumento=Rango_Documento::PuntoRango($punto->punto_id, 'Anticipo de Cliente')->first();
                                if($rangoDocumento){
                                    $puntoemeision = $punto;
                                    break;
                                }
                            }
                            if ($rangoDocumento) {
                                $secuencial=$rangoDocumento->rango_inicio;
                                $secuencialAux=Anticipo_Cliente::secuencial($rangoDocumento->rango_id)->max('anticipo_secuencial');
                                if ($secuencialAux) {
                                    $secuencial=$secuencialAux+1;
                                }
                            }
                           
                            $anticipoCliente = new Anticipo_Cliente();

                            $anticipoCliente->anticipo_numero = $rangoDocumento->puntoEmision->sucursal->sucursal_codigo.$rangoDocumento->puntoEmision->punto_serie.substr(str_repeat(0, 9).$secuencial, - 9);
                            $anticipoCliente->anticipo_serie = $rangoDocumento->puntoEmision->sucursal->sucursal_codigo.$rangoDocumento->puntoEmision->punto_serie;
                            $anticipoCliente->anticipo_secuencial = $secuencial;
                            
                            $Excel_date2 = $array[0][$i][0]; 
                            $unix_date2 = ($Excel_date2 - 25569) * 86400;
                            $Excel_date2 = 25569 + ($unix_date2 / 86400);
                            $unix_date2 = ($Excel_date2 - 25569) * 86400;
                            $anticipoCliente->anticipo_fecha = gmdate("Y-m-d", $unix_date2);

                            $anticipoCliente->anticipo_tipo = ($array[0][$i][1]);
                            $anticipoCliente->anticipo_documento ='0';
                                  
                            $anticipoCliente->anticipo_motivo = ($array[0][$i][2]);
                            $anticipoCliente->anticipo_valor = ($array[0][$i][3]);  
                            $anticipoCliente->anticipo_saldo = ($array[0][$i][4]);   
                            $anticipoCliente->anticipo_saldom = ($array[0][$i][4]); 
                            $anticipoCliente->cliente_id = $cliente->cliente_id;
                            $anticipoCliente->rango_id =$rangoDocumento->rango_id;
                          
                            $anticipoCliente->anticipo_estado = 1;

                            $detalleDiario = new Detalle_Diario();
                            $detalleDiario->detalle_debe =  0.00;
                            $detalleDiario->detalle_haber = ($array[0][$i][4]);
                            $detalleDiario->detalle_comentario = 'P/R CUENTA DE ANTICIPO CLIENTE';
                            $detalleDiario->detalle_tipo_documento = 'ANTICIPO DE CLIENTE';
                            $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                            $detalleDiario->detalle_conciliacion = '0';
                            $detalleDiario->detalle_estado = '1';        
                            $detalleDiario->cliente_id =  $cliente->cliente_id;
                            $activador=true;
                            $parametrizacionContable=Parametrizacion_Contable::ParametrizacionByNombre($diario->sucursal_id, 'ANTICIPO DE CLIENTE')->first();
                            if($parametrizacionContable->parametrizacion_cuenta_general == '1'){
                                $detalleDiario->cuenta_id = $parametrizacionContable->cuenta_id;
                            }else{
                                $parametrizacionContable = Cliente::findOrFail($cliente->cliente_id);
                                $detalleDiario->cuenta_id = $parametrizacionContable->cliente_cuenta_anticipo;
                            }
                            $diario->detalles()->save($detalleDiario);
                            $general->registrarAuditoria('Registro de Detalle de Diario codigo: -> '.$diario->diario_codigo,'0','En la cuenta del Haber -> '.$detalleDiario->cuenta->cuenta_numero.' con el valor de: -> '.$array[0][$i][3]);
                            $anticipoCliente->diario_id= $diario->diario_id;
                            $anticipoCliente->save();
                        }
                    }
                }
                /*
                if($activador==true){
                    $detalleDiarioAux->delete();
                }
                */
            }
            DB::commit();
            return redirect('listaAnticipoCliente')->with('success','Datos guardados exitosamente');
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('listaAnticipoCliente')->with('error2','Ocurrio un error vuelva a intentarlo('.$ex->getMessage().')');
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
            DB::beginTransaction();
            $general = new generalController();
            $rangoDocumento = Rango_Documento::Rango($request->get('rango_id'))->first();
           
            $cierre = $general->cierre($request->get('idFecha'),$rangoDocumento->puntoEmision->sucursal_id);
            
            if($cierre){
                return redirect('anticipoCliente/new/'.$rangoDocumento->punto_id)->with('error2','No puede realizar la operacion por que pertenece a un mes bloqueado');
            }
            $movimientoCaja = new Movimiento_Caja();
           
            $cuentaBanco = Cuenta_Bancaria::CuentaBanco($request->get('cuenta_id'))->first();
            $cuentacaja=Caja::caja($request->get('idCaja'))->first();
            $cajasxusuario=Arqueo_Caja::arqueoCajaxuser(Auth::user()->user_id)->first();  
            $cliente = Cliente::cliente($request->get('idCliente'))->first();
            $anticipoCliente = new Anticipo_Cliente();
            $anticipoCliente->anticipo_numero = $request->get('anticipo_serie').substr(str_repeat(0, 9).$request->get('anticipo_numero'), - 9);
            $anticipoCliente->anticipo_serie = $request->get('anticipo_serie');
            $anticipoCliente->anticipo_secuencial = $request->get('anticipo_numero');
            $anticipoCliente->anticipo_fecha = $request->get('idFecha');
            $anticipoCliente->anticipo_tipo = $request->get('idTipo');
            if($request->get('idTipo') == "Efectivo"){
                $anticipoCliente->anticipo_documento = 0;
                $anticipoCliente->arqueo_id = $cajasxusuario->arqueo_id;
            } 
            if($request->get('idTipo') == "Deposito"){
                $anticipoCliente->anticipo_documento = $request->get('idDocAnt');    
            }      
            $anticipoCliente->anticipo_motivo = $request->get('idMensaje');
            $anticipoCliente->anticipo_valor = $request->get('idValor');  
            $anticipoCliente->anticipo_saldo = $request->get('idValor');   
            $anticipoCliente->cliente_id = $request->get('idCliente');
            $anticipoCliente->rango_id = $request->get('rango_id');
            $anticipoCliente->anticipo_estado = 1; 

            if($request->get('idTipo') == 'Deposito'){
                $deposito =  new Deposito();
                $deposito->deposito_fecha = $request->get('idFecha');
                $deposito->deposito_tipo = $request->get('idTipo');
                $deposito->deposito_valor = $request->get('idValor');
                $deposito->deposito_descripcion = 'ANTICIPO DE CLIENTE : '.Cliente::findOrFail($request->get('idCliente'))->cliente_nombre;
                $deposito->deposito_estado = '1';
                $deposito->empresa_id = Auth::user()->empresa_id;
                $deposito->cuenta_bancaria_id = $cuentaBanco->cuenta_bancaria_id;
                $deposito->deposito_numero = $request->get('idDocAnt');
                $deposito->save();
                $general->registrarAuditoria('Registro de deposito por anticipo de cliente','0','Registro de deposito por anticipo de cliente '.Cliente::findOrFail($request->get('idCliente'))->cliente_nombre); 
            }
            /**********************asiento diario****************************/
            $general = new generalController();
            
                $diario = new Diario();
                $diario->diario_codigo = $general->generarCodigoDiario($request->get('idFecha'),'CIAC');
                $diario->diario_fecha = $request->get('idFecha');
                $diario->diario_referencia = 'COMPROBANTE DE ANTICIPO DE CLIENTE';
                $diario->diario_tipo_documento = 'ANTICIPO DE CLIENTE';            
                $diario->diario_numero_documento = $request->get('anticipo_serie').substr(str_repeat(0, 9).$request->get('anticipo_numero'), - 9);
                $diario->diario_beneficiario = $cliente->cliente_nombre;
                $diario->diario_tipo = 'CIAC';
                $diario->diario_secuencial = substr($diario->diario_codigo, 8);
                $diario->diario_mes = DateTime::createFromFormat('Y-m-d', $request->get('idFecha'))->format('m');
                $diario->diario_ano = DateTime::createFromFormat('Y-m-d', $request->get('idFecha'))->format('Y');
                $diario->diario_comentario = 'COMPROBANTE DE ANTICIPO A CLIENTE: '.$cliente->cliente_nombre.'  '.$request->get('idMensaje');;
                $diario->diario_cierre = '0';
                $diario->diario_estado = '1';
                $diario->empresa_id = Auth::user()->empresa_id;            
                $diario->sucursal_id = $rangoDocumento->puntoEmision->sucursal_id;            
                $diario->save();
                $general->registrarAuditoria('Registro de Diario de Diario codigo: -> '.$diario->diario_codigo,'0','Tipo de Diario -> '.$diario->diario_referencia.'');
                $anticipoCliente->diario()->associate($diario);
                if($request->get('idTipo') == "Efectivo"){
                    $movimientoCaja->diario()->associate($diario);
                }
            
          
            
            
           

            if($request->get('idTipo') == "Efectivo"){               
                /**********************movimiento caja****************************/               
               $movimientoCaja->movimiento_fecha=date("Y")."-".date("m")."-".date("d");
               $movimientoCaja->movimiento_hora=date("H:i:s");
               $movimientoCaja->movimiento_tipo="ENTRADA";
               $movimientoCaja->movimiento_descripcion= 'P/R ANTICIPO DE CLIENTE'.' '.$request->get('idMensaje');
               $movimientoCaja->movimiento_valor= $request->get('idValor');
               $movimientoCaja->movimiento_documento="ANTICIPO DE CLIENTE";
               $movimientoCaja->movimiento_numero_documento= $request->get('anticipo_serie').substr(str_repeat(0, 9).$request->get('anticipo_numero'), - 9);
               $movimientoCaja->movimiento_estado = 1;
               $movimientoCaja->arqueo_id = $cajasxusuario->arqueo_id;
               $movimientoCaja->save();   
               $general->registrarAuditoria('Registro de Movimiento numero: -> '.$request->get('anticipo_serie').substr(str_repeat(0, 9).$request->get('anticipo_numero'), - 9),'0','Por motivo de: -> '.$request->get('idMensaje').' con el valor de: -> '.$request->get('idValor'));

           }
           
                /********************detalle de diario de venta********************/
                $detalleDiario = new Detalle_Diario();
                $detalleDiario->detalle_debe = $request->get('idValor');
                $detalleDiario->detalle_haber = 0.00 ;
                
            
                $detalleDiario->detalle_tipo_documento = 'ANTICIPO DE CLIENTE';
                $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                $detalleDiario->detalle_conciliacion = '0';
                $detalleDiario->detalle_estado = '1';
                if($request->get('idTipo') == "Efectivo"){
                    $detalleDiario->cuenta_id = $cuentacaja->cuenta_id;
                    $detalleDiario->detalle_comentario = 'CUENTA DE ANTICIPO CAJA';
                }
                if($request->get('idTipo') == "Deposito"){
                    $detalleDiario->detalle_comentario = 'CUENTA DE ANTICIPO DE BANCO';
                    $detalleDiario->cuenta_id = $cuentaBanco->cuenta_id;
                    $detalleDiario->deposito()->associate($deposito);
                }
                $diario->detalles()->save($detalleDiario);
                $general->registrarAuditoria('Registro de Detalle de Diario codigo: -> '.$diario->diario_codigo,'0','En la cuenta del debe -> '.$detalleDiario->cuenta->cuenta_numero.' con el valor de: -> '.$request->get('idValor'));
                
                $detalleDiario = new Detalle_Diario();
                $detalleDiario->detalle_debe = 0.00;
                $detalleDiario->detalle_haber = $request->get('idValor');
                $detalleDiario->detalle_comentario = 'P/R CUENTA DE ANTICIPO CLIENTE';
                $detalleDiario->detalle_tipo_documento = 'ANTICIPO DE CLIENTE';
                $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                $detalleDiario->detalle_conciliacion = '0';
                $detalleDiario->detalle_estado = '1';
                $detalleDiario->cliente_id = $request->get('idCliente');
                $parametrizacionContable=Parametrizacion_Contable::ParametrizacionByNombre($diario->sucursal_id, 'ANTICIPO DE CLIENTE')->first();
                if($parametrizacionContable->parametrizacion_cuenta_general == '1'){
                    $detalleDiario->cuenta_id = $parametrizacionContable->cuenta_id;
                }else{
                    $parametrizacionContable = Cliente::findOrFail($request->get('idCliente'));
                    $detalleDiario->cuenta_id = $parametrizacionContable->cliente_cuenta_anticipo;
                }
                $diario->detalles()->save($detalleDiario);
                $general->registrarAuditoria('Registro de Detalle de Diario codigo: -> '.$diario->diario_codigo,'0','En la cuenta del Haber -> '.$detalleDiario->cuenta->cuenta_numero.' con el valor de: -> '.$request->get('idValor'));
            
            $anticipoCliente->save();            
            /*Inicio de registro de auditoria */
            $general = new generalController();
            $general->registrarAuditoria('Registro de Anticipo de Cliente -> '.$request->get('idCliente'),'0','Con motivo:'.$request->get('idMotivo'));
            /*Fin de registro de auditoria */
            $url ='';
            if(Auth::user()->empresa->empresa_llevaContabilidad== '1'){
                $url = $general->pdfDiario($diario);
            }
            DB::commit();
            return redirect('anticipoCliente/new/'.$rangoDocumento->punto_id)->with('success','Datos guardados exitosamente')->with('diario',$url);
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('anticipoCliente/new/'.$rangoDocumento->punto_id)->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
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
    public function nuevo($id){        
        $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
        $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
        $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
        $cajasxusuario=Arqueo_Caja::arqueoCajaxuser(Auth::user()->user_id)->first();    
        $cajas = Caja::cajas()->get();
        $rangoDocumento=Rango_Documento::PuntoRango($id, 'Anticipo de Cliente')->first();        
        $sucursalp=Punto_Emision::punto($id)->first();            
        $secuencial=1;        
        if($rangoDocumento){
            $secuencial=$rangoDocumento->rango_inicio;
            $secuencialAux=Anticipo_Cliente::secuencial($rangoDocumento->rango_id)->max('anticipo_secuencial');
            if($secuencialAux){
                $secuencial=$secuencialAux+1;
            }
            return view('admin.cuentasCobrar.anticipoCliente.nuevo',
            ['sucursales'=>Sucursal::sucursales()->get(),
            'clientes'=>Cliente::clientes()->get(),
            'bancos'=>Banco::bancos()->get(),
            'PE'=>Punto_Emision::puntos()->get(),
            'gruposPermiso'=>$gruposPermiso,
            'tipoPermiso'=>$tipoPermiso,
            'rangoDocumento'=>$rangoDocumento,
            'cajasxusuario'=>$cajasxusuario,
            'cajas'=>$cajas,
            'sucursalp'=>$sucursalp->sucursal_id,
            'secuencial'=>substr(str_repeat(0, 9).$secuencial, - 9),
            'permisosAdmin'=>$permisosAdmin]);

        }else{
            return redirect('inicio')->with('error','No tiene configurado, un punto de emisión o un rango de documentos para emitir egresos de banco, configueros y vuelva a intentar');
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
    public function buscarByCuentas(){
        return Cuenta::CuentasMovimiento()->get();
    }
    public function buscarByCliente(Request $request){
        return Anticipo_Cliente::AnticiposByCliente($request->get('cliente_id'))->get();
    }

    public function nuevoE(){
        try{  
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();        
            return view('admin.cuentasCobrar.eliminarAnticipo.index',['clientes'=>Cliente::ClientesAnticipos()->get(),'sucursales'=>Sucursal::sucursales()->get(),'bancos'=>Banco::bancos()->get(),'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        }catch(\Exception $ex){
            return redirect('incio')->with('error','Ocurrio un error vuelva a intentarlo');
        }
    }
    public function buscarEliminar(Request $request){
        if (isset($_POST['buscar'])){
            return $this->buscar($request);
        }
        if (isset($_POST['eliminar'])){
            return $this->eliminar($request);
        }
    }   
    public function buscar(Request $request){
        try{   
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
            $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $todo = 0;
            $count = 1;
            $datos = null;
            if ($request->get('fecha_todo') == "on"){
                $todo = 1; 
            }
            $cliente = Cliente::cliente($request->get('clienteID'))->first();
            $datos[$count]['cod'] = '';
            $datos[$count]['ben'] = $cliente->cliente_nombre; 
            $datos[$count]['mon'] = '';
            $datos[$count]['pag'] = '';
            $datos[$count]['sal'] = ''; 
            $datos[$count]['fec'] = ''; 
            $datos[$count]['fep'] = ''; 
            $datos[$count]['dir'] = ''; 
            $datos[$count]['tip'] = ''; 
            $datos[$count]['fac'] = ''; 
            $datos[$count]['chk'] = '0'; 
            $datos[$count]['tot'] = '1'; 
            $count ++;
            foreach(Anticipo_Cliente::AntCliByFec($request->get('fecha_desde'),$request->get('fecha_hasta'),$cliente->cliente_id, $request->get('sucursal_id'),$todo)->get() as $anticipo){
                $datos[$count]['cod'] = $anticipo->anticipo_id;
                $datos[$count]['ben'] = ''; 
                $datos[$count]['mon'] = $anticipo->anticipo_valor; 
                $datos[$count]['pag'] = '';
                $datos[$count]['sal'] = $anticipo->anticipo_saldo;
                $datos[$count]['fec'] = $anticipo->anticipo_fecha; 
                $datos[$count]['fep'] = ''; 
                $datos[$count]['dir'] = $anticipo->diario->diario_codigo; 
                $datos[$count]['tip'] = $anticipo->anticipo_tipo.' - '.$anticipo->anticipo_documento; 
                $datos[$count]['fac'] = ''; 
                $datos[$count]['chk'] = '0'; 
                $datos[$count]['tot'] = '2'; 
                $count ++;
                foreach(Descuento_Anticipo_Cliente::DescuentosByAnticipo($anticipo->anticipo_id)->select('descuento_anticipo_cliente.descuento_id','descuento_valor','descuento_fecha','descuento_anticipo_cliente.diario_id','descuento_anticipo_cliente.factura_id','descuento_descripcion')->get() as $descuento){
                    $datos[$count]['cod'] = $descuento->descuento_id;
                    $datos[$count]['ben'] = ''; 
                    $datos[$count]['mon'] = ''; 
                    $datos[$count]['sal'] = ''; 
                    $datos[$count]['fec'] = ''; 
                    $datos[$count]['pag'] = $descuento->descuento_valor;
                    $datos[$count]['fep'] = $descuento->descuento_fecha; 
                    $datos[$count]['dir'] = $descuento->diario->diario_codigo; 
                    $datos[$count]['tip'] = ''; 
                    if($descuento->descuento_descripcion == 'CRUCE DE ANTICIPO CON BANCO' or
                    $descuento->descuento_descripcion == 'CRUCE DE ANTICIPO CON CAJA'){
                        $datos[$count]['fac'] = $descuento->descuento_descripcion;
                    }else{
                        if(isset($descuento->factura->factura_numero)){
                            $datos[$count]['fac'] = 'Cruce con factura No. '.$descuento->factura->factura_numero; 
                        }else{
                            $datos[$count]['fac'] = 'Cruce con factura No. '.$descuento->descuento_descripcion;
                        }
                    }
                    $datos[$count]['chk'] = '1'; 
                    $datos[$count]['tot'] = '3'; 
                    $count ++;
                }
                if( $datos[$count-1]['tot'] == '2' ){
                    $datos[$count-1]['chk'] = '1'; 
                }
                if($anticipo->anticipo_tipo == 'RETENCION' or  $anticipo->anticipo_tipo == 'NOTA DE CRÉDITO' ){
                    $datos[$count-1]['chk'] = '0'; 
                }
            }
            return view('admin.cuentasCobrar.eliminarAnticipo.index',['sucurslaC'=>$request->get('sucursal_id'),'clienteC'=>$request->get('clienteID'),'fecI'=>$request->get('fecha_desde'),'fecF'=>$request->get('fecha_hasta'),'todo'=>$todo,'datos'=>$datos,'clientes'=>Cliente::ClientesAnticipos()->get(),'sucursales'=>Sucursal::sucursales()->get(),'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);      
        }catch(\Exception $ex){
            return redirect('eliminatAntCli')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }   
    }
    public function eliminar(Request $request){
        try {     
            DB::beginTransaction();
            $general = new generalController();          
            $auditoria = new generalController();   
            $noTienecaja =null;
            $jo=false;
            if($request->get('checkbox')){
                $seleccion = $request->get('checkbox');
                for ($i = 0; $i < count($seleccion); ++$i) {
                    $anticipo = Anticipo_Cliente::findOrFail($seleccion[$i]);
                    $cierre = $general->cierre($anticipo->anticipo_fecha,$anticipo->diario->sucursal_id);
                    if ($cierre) {
                        return redirect('eliminatAntCli')->with('error2', 'No puede realizar la operacion por que pertenece a un mes bloqueado');
                    }
                }
               
                for ($i = 0; $i < count($seleccion); ++$i) {
                    $anticipo = Anticipo_Cliente::findOrFail($seleccion[$i]);
                    $diario = null;
                    if(isset($anticipo->diario)){
                        $diario = $anticipo->diario;
                        if($anticipo->anticipo_tipo == 'Efectivo'){
                            $cajaAbierta=Arqueo_Caja::ArqueoCajaxid($anticipo->arqueo_id)->first();
                            if(isset($cajaAbierta->arqueo_id)){
                                $movimientoCaja = Movimiento_Caja::MovimientoCajaxarqueo($anticipo->arqueo_id, $anticipo->diario_id)->first();
                                $movimientoCaja->delete();
                                $jo=true;
                            }else{
                                $cajaAbierta=Arqueo_Caja::arqueoCajaxuser(Auth::user()->user_id)->first();
                                 if ($cajaAbierta){
                                    /**********************movimiento caja****************************/
                                    $movimientoCaja = new Movimiento_Caja();          
                                    $movimientoCaja->movimiento_fecha=date("Y")."-".date("m")."-".date("d");
                                    $movimientoCaja->movimiento_hora=date("H:i:s");
                                    $movimientoCaja->movimiento_tipo="SALIDA";
                                    $movimientoCaja->movimiento_descripcion= 'P/R ELIMINACION DE ANTICIPO DE CLIENTE :'.$anticipo->anticipo_motivo;
                                    $movimientoCaja->movimiento_valor= $anticipo->anticipo_valor;
                                    $movimientoCaja->movimiento_documento="P/R ELIMINACION DE ANTICIPO DE CLIENTE";
                                    $movimientoCaja->movimiento_numero_documento= 0;
                                    $movimientoCaja->movimiento_estado = 1;
                                    $movimientoCaja->arqueo_id = $cajaAbierta->arqueo_id;                                
                                    $movimientoCaja->save();
                                    
                                    $movimientoAnterior = Movimiento_Caja::MovimientoCajaxarqueo($anticipo->arqueo_id,$anticipo->diario_id)->first();
                                    $movimientoAnterior->diario_id = null;
                                    $movimientoAnterior->update();

                                    $jo=true;
                                /*********************************************************************/                               
                                }else{
                                    $noTienecaja = 'Lo valores en Efectivo no pudieron ser eliminados, porque no dispone de CAJA ABIERTA';                               
                                }
                            }
                        }else{
                            $jo=true;
                        }
                    }
                    if($jo){
                        $auditoria->registrarAuditoria('Eliminacion de anticipo de cliente  '.$anticipo->cliente->cliente_nombre,'',''); 
                        $anticipo->delete();                    
                        if(!is_null($diario)){
                            foreach($diario->detalles as $detalle){
                                if(isset($detalle->deposito)){
                                    if(isset($detalle->deposito->chequeCliente)){
                                        $detalle->deposito->chequeCliente->delete();
                                        $auditoria->registrarAuditoria('Eliminacion de cheque de cliente '.$detalle->deposito->chequeCliente->cheque_dueno,'','Eliminacion de cheque de cliente '.$detalle->deposito->chequeCliente->cheque_dueno.' numero '.$detalle->deposito->chequeCliente->cheque_numero.' por un valor de '.$detalle->deposito->chequeCliente->cheque_valor.' por eliminacion de anticipo');  
                                    }
                                    $depositoAux = $detalle->deposito;
                                }
                                $detalle->delete();
                                $auditoria->registrarAuditoria('Eliminacion del detalle diario  N°'.$diario->diario_codigo,$diario->diario_codigo,'Eliminacion de detalle de diario por eliminacion de anticipo');  
                                if(isset($depositoAux)){
                                    $depositoAux->delete();
                                    $auditoria->registrarAuditoria('Eliminacion de deposito de cliente ','','Eliminacion de deposito de cliente numero '.$depositoAux->deposito_numero.' por un valor de '.$depositoAux->deposito_valor.' por eliminacion de anticipo');  
                                }
                            }
                            $diario->delete();
                            $auditoria->registrarAuditoria('Eliminacion de diario  N°'.$diario->diario_codigo,$diario->diario_codigo,'Eliminacion de diario por eliminacion de anticipo');  
                        }
                    }
                }
            }
            if($request->get('checkbox2')){
                $seleccion2 = $request->get('checkbox2');
                for ($i = 0; $i < count($seleccion2); ++$i) {
                    $descuento =  Descuento_Anticipo_Cliente::findOrFail($seleccion2[$i]);
                   
                    $anticipo =  Anticipo_Cliente::findOrFail($descuento->anticipo_id);
                    $cierre = $general->cierre($descuento->descuento_fecha,$descuento->diario->sucursal_id);                   
                    if($cierre){
                        return redirect('eliminatAntCli')->with('error2','No puede realizar la operacion por que pertenece a un mes bloqueado');
                    }
                    $cierre = $general->cierre($anticipo->anticipo_fecha,$anticipo->diario->sucursal_id);    
                    if($cierre){
                        return redirect('eliminatAntCli')->with('error2','No puede realizar la operacion por que pertenece a un mes bloqueado');
                    } 
                }

                for ($i = 0; $i < count($seleccion2); ++$i) {
                    $descuento =  Descuento_Anticipo_Cliente::findOrFail($seleccion2[$i]);
                    $valorDescuento = $descuento->descuento_valor;
                    $anticipo =  Anticipo_Cliente::findOrFail($descuento->anticipo_id);
                    $general = new generalController();
                    $diario = null;
                    $cxcAux = null;
                     
                    if(isset($descuento->diario->diario_id)){
                        $diario=Diario::findOrFail($descuento->diario->diario_id);
                        $descuento->diario_id;
                        $descuento->save();
                    }
                    if(isset($descuento->factura->cuentaCobrar)){
                        $cxcAux = $descuento->factura->cuentaCobrar;
                    }else{
                        $cxcAux = Cuenta_Cobrar::CuentaByFacturaMigrada($descuento->descuento_descripcion)->first();
                    }
                    foreach($diario->detalles as $detalle){
                        $detalle->delete();
                        $auditoria->registrarAuditoria('Eliminacion del detalle diario  N°'.$diario->diario_codigo,$diario->diario_codigo,'Eliminacion de detalle de diario por eliminacion de '.$descuento->descuento_descripcion);  
                    }
                    $arqueoCaja=Arqueo_Caja::arqueoCaja(Auth::user()->user_id)->first();
                    if($diario->movimientocaja){
                        $movimientoCaja = Movimiento_Caja::MovimientoCajaxarqueo($arqueoCaja->arqueo_id, $diario->diario_id)->first();
                        if($movimientoCaja){
                            $movimientoCaja->delete();
                        }else{
                            $noTienecaja = 'Lo valores en Efectivo no pudieron ser eliminados, porque CAJA esta Cerrada';                               
                        }
                    }
                    $descuento =  Descuento_Anticipo_Cliente::findOrFail($seleccion2[$i]);
                    $descuento->diario_id=null;
                    $descuento->save();

                    $descuento->delete();
                    $auditoria->registrarAuditoria('Eliminacion del descuento con valor'.$descuento->descuento_valor,'',' Con Descripcion: '.$descuento->descuento_descripcion);  
                   
                    $diario->delete();
                    $auditoria->registrarAuditoria('Eliminacion del diario  N°'.$diario->diario_codigo,$diario->diario_codigo,'Eliminacion de detalle de diario por eliminacion de '.$descuento->descuento_descripcion);  
                  
                     
                    if ($cxcAux) {
                        if (isset($descuento->factura->cuentaCobrar)) {
                            $cxcAux->cuenta_saldo = $cxcAux->cuenta_monto - round(Cuenta_Cobrar::CuentaCobrarPagos($cxcAux->cuenta_id)->sum('detalle_pago_valor'),2) - round(Descuento_Anticipo_Cliente::DescuentosAnticipoByFactura($cxcAux->facturaVenta->factura_id)->sum('descuento_valor'),2);
                        } else {
                            $cxcAux->cuenta_saldo = $cxcAux->cuenta_saldo + $valorDescuento;
                        }
                        if ($cxcAux->cuenta_saldo == 0) {
                            $cxcAux->cuenta_estado = '2';
                        } else {
                            $cxcAux->cuenta_estado = '1';
                        }
                        $cxcAux->update();
                        if(isset($descuento->factura->factura_id)){
                            $auditoria->registrarAuditoria('Actualizacion de cuenta por cobrar de cliente', '0', 'Actualizacion de cuenta por cobrar por eliminacion de cruce de anticipos de cliente -> '.$cxcAux->facturaVenta->cliente->cliente_nombre.' con factura -> '.$cxcAux->facturaVenta->factura_numero);
                            $auditoria->registrarAuditoria('Actualizacion de anticipo cliente','0','Actualizacion de cuenta por cobrar por eliminacion de cruce de anticipos de cliente -> '.$cxcAux->facturaVenta->cliente->cliente_nombre.' con factura -> '.$cxcAux->facturaVenta->factura_numero);
                        }
                        if (!isset($descuento->factura->factura_id)) {
                            $auditoria->registrarAuditoria('Actualizacion de cuenta por cobrar de cliente', '0', 'Actualizacion de cuenta por cobrar por eliminacion de cruce de anticipos de cliente -> '.$cxcAux->cliente->cliente_nombre.' con factura -> '.$descuento->descuento_descripcion);
                            $auditoria->registrarAuditoria('Actualizacion de anticipo cliente','0','Actualizacion de cuenta por cobrar por eliminacion de cruce de anticipos de cliente -> '.$cxcAux->cliente->cliente_nombre.' con factura -> '.$descuento->descuento_descripcion);
                        }
                    }
                    if(is_null($anticipo->anticipo_documento)){
                        $anticipo->anticipo_saldo = $anticipo->anticipo_saldo + $valorDescuento;
                    }else{
                        $anticipo->anticipo_saldo = $anticipo->anticipo_valor - round(Descuento_Anticipo_Cliente::DescuentosByAnticipo($anticipo->anticipo_id)->sum('descuento_valor'),2);
                    }
                    if($anticipo->anticipo_valor == 0){
                        $anticipo->anticipo_estado = '2';
                    }else{
                        $anticipo->anticipo_estado = '1';
                    }
                    $anticipo->update();
                    $auditoria->registrarAuditoria('Actualizacion de Anticipo por la eliminacion de descuento de aniticipo con el valor de '.$descuento->descuento_valor, '0', 'Actualizacion por eliminacion de descuento anticipo de cruce de '.$descuento->descuento_descripcion);
                }
            }                                        
            DB::commit();
            if(isset($noTienecaja)){
                return redirect('eliminatAntCli')->with('success','Datos eliminados exitosamente')->with('error2',$noTienecaja);
            }else{
                return redirect('eliminatAntCli')->with('success','Datos eliminados exitosamente');
            }
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('eliminatAntCli')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
}
