<?php

namespace App\Http\Controllers;

use App\Models\Cuenta;
use App\Models\Detalle_Diario;
use App\Models\Empresa;
use App\Models\Punto_Emision;
use App\Models\Sucursal;
use DateTime;
use PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Codedge\Fpdf\Fpdf\Fpdf;
use App\NEOPAGUPA\ViewExcel;
use Maatwebsite\Excel\Facades\Excel;

class mayorAuxiliarController extends Controller
{
    protected $fpdf;


    public function __construct()
    {
        $this->fpdf = new Fpdf('L', 'mm', 'A4');
    }

    public function nuevo(){
        try{
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
            $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            return view('admin.contabilidad.mayorAuxiliar.index',['sucursales'=>Sucursal::sucursales()->get(),'cuentas'=>Cuenta::Cuentas()->orderBy('cuenta_numero','asc')->get(),'cuentaFinal'=>Cuenta::CuentasDesc()->first()->cuenta_id,'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        }
        catch(\Exception $ex){      
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }


    public function consultar(Request $request){   
        ini_set('max_input_vars ', 10000);
        if (isset($_POST['buscar'])){
            return $this->buscar2($request);
        }
        if (isset($_POST['pdf'])){
            return $this->pdf2($request);
        }

        if (isset($_POST['excel'])) return $this->excel($request);
    }

    public function buscar(Request $request){
        try{   
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
            $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $datos = null;
            $count = 1;
            $debe = 0;
            $haber = 0;

            foreach(Cuenta::CuentasRango($request->get('cuenta_inicio'),$request->get('cuenta_fin'))->get() as $cuenta){
                $datos[$count]['cod'] = $cuenta->cuenta_numero;
                $datos[$count]['nom'] = $cuenta->cuenta_nombre;
                $datos[$count]['fec'] = '';
                $datos[$count]['doc'] = '';
                $datos[$count]['num'] = '';
                $datos[$count]['deb'] = '';
                $datos[$count]['hab'] = '';
                $datos[$count]['act'] = Detalle_Diario::SaldoAnteriorCuentaSucursal($cuenta->cuenta_id,$request->get('fecha_desde'),$request->get('sucursal_id'))->select(DB::raw('SUM(detalle_debe)-SUM(detalle_haber) as saldo'))->first()->saldo;
                if($datos[$count]['act'] == ''){
                    $datos[$count]['act'] = 0;
                }
                $datos[$count]['ben'] = '';
                $datos[$count]['dia'] = '';
                $datos[$count]['com'] = '';
                $datos[$count]['suc'] = '';
                $datos[$count]['tot'] = '1';
                $count ++;
                $debe = 0;
                $haber = 0;
                foreach(Detalle_Diario::MovimientosCuenta($cuenta->cuenta_id,$request->get('fecha_desde'),$request->get('fecha_hasta'),$request->get('sucursal_id'))->get() as $detalle){
                    $datos[$count]['cod'] = $cuenta->cuenta_numero;
                    $datos[$count]['nom'] = $cuenta->cuenta_nombre;
                    $datos[$count]['fec'] = $detalle->diario->diario_fecha;
                    $datos[$count]['doc'] = $detalle->detalle_tipo_documento;
                    $datos[$count]['num'] = $detalle->detalle_numero_documento;;
                    $datos[$count]['ant'] = '';
                    $datos[$count]['deb'] = $detalle->detalle_debe;
                    $datos[$count]['hab'] = $detalle->detalle_haber;
                    $datos[$count]['act'] = doubleval($datos[$count-1]['act']) + doubleval($datos[$count]['deb']) - doubleval($datos[$count]['hab']);
                    $datos[$count]['ben'] = $detalle->diario_beneficiario;
                    $datos[$count]['dia'] = $detalle->diario->diario_codigo;
                    $datos[$count]['com'] = 'Decripcion: '.$detalle->detalle_comentario.' '.'Comentario: '.$detalle->diario->diario_comentario;                   
                    $datos[$count]['suc'] = $detalle->diario->sucursal->sucursal_nombre;
                    $datos[$count]['tot'] = '0';
                    $debe = $debe + doubleval($datos[$count]['deb']);
                    $haber = $haber + doubleval($datos[$count]['hab']);
                    $count ++;
                }
                if($debe > 0 or $haber > 0){
                    $datos[$count]['cod'] = '';
                    $datos[$count]['nom'] = '';
                    $datos[$count]['fec'] = '';
                    $datos[$count]['doc'] = '';
                    $datos[$count]['num'] = '';
                    $datos[$count]['ant'] = '';
                    $datos[$count]['deb'] = $debe;
                    $datos[$count]['hab'] = $haber;
                    $datos[$count]['act'] = '';
                    $datos[$count]['ben'] = '';
                    $datos[$count]['dia'] = '';
                    $datos[$count]['com'] = '';
                    $datos[$count]['suc'] = '';
                    $datos[$count]['tot'] = '2';
                    $count ++;
                }
                if( $datos[$count-1]['tot'] == '1' ){
                    array_pop($datos);
                    $count = $count - 1;
                }
            }
            
            return view('admin.contabilidad.mayorAuxiliar.index',[
                'ini'=>$request->get('cuenta_inicio'),
                'fin'=>$request->get('cuenta_fin'),
                'sucursalC'=>$request->get('sucursal_id'),
                'sucursales'=>Sucursal::sucursales()->get(),
                'fDesde'=>$request->get('fecha_desde'),
                'fHasta'=>$request->get('fecha_hasta'),
                'datos'=>$datos,
                'cuentas'=>Cuenta::Cuentas()->orderBy('cuenta_numero','asc')->get(),
                'cuentaFinal'=>Cuenta::CuentasDesc()->first()->cuenta_id,
                'PE'=>Punto_Emision::puntos()->get(),
                'tipoPermiso'=>$tipoPermiso,
                'gruposPermiso'=>$gruposPermiso,
                'permisosAdmin'=>$permisosAdmin
            ]);
        }catch(\Exception $ex){
            return redirect('mayorAuxiliar')->with('error2','Oucrrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }

    /*
    public function buscar2(Request $request){
        ini_set('memory_limit','-1');
        ini_set('max_execution_time', 600);
       
        try{   
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
            $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            
            $sucursales="";
            if($request->sucursal_id>0) $sucursales=" and sucursal.sucursal_id=".$request->sucursal_id;

            $datos=DB::select(DB::raw("
                select cuenta.*, 
                    diario.diario_fecha, diario.diario_codigo, diario.diario_comentario, diario.diario_beneficiario, detalle_diario.detalle_debe, detalle_diario.detalle_haber, 
                    detalle_diario.detalle_comentario, detalle_diario.detalle_tipo_documento, detalle_diario.detalle_numero_documento,sucursal.sucursal_nombre 
                    
                from detalle_diario
                    inner join cuenta on cuenta.cuenta_id=detalle_diario.cuenta_id
                    inner join diario on diario.diario_id = detalle_diario.diario_id 
                    inner join empresa on empresa.empresa_id=cuenta.empresa_id
                    inner join sucursal on sucursal.sucursal_id=diario.sucursal_id
                where empresa.empresa_id = ".Auth::user()->empresa_id."
                    and cuenta.cuenta_estado = '1' 
                    and cuenta.cuenta_numero >= '$request->cuenta_inicio'
                    and cuenta.cuenta_numero <= '$request->cuenta_fin'
                    and diario.diario_fecha >= '$request->fecha_desde'
                    and diario.diario_fecha <= '$request->fecha_hasta' 
                    $sucursales
                order by 
                    cuenta.cuenta_id,
                    cuenta.cuenta_nombre,
                    diario.diario_fecha
            "));

            $saldo_anterior=DB::select(DB::raw("
                select 
                    cuenta.cuenta_id, SUM(detalle_debe)-SUM(detalle_haber) as saldo
                from cuenta
                    left join detalle_diario on cuenta.cuenta_id = detalle_diario.cuenta_id
                    inner join diario on diario.diario_id = detalle_diario.diario_id 
                    inner join sucursal on sucursal.sucursal_id=diario.sucursal_id
                where 
                    cuenta.empresa_id = ".Auth::user()->empresa_id."
                    and diario.diario_fecha < '$request->fecha_desde' 
                    and detalle_diario.cuenta_id=cuenta.cuenta_id
                    and cuenta.cuenta_numero >= '$request->cuenta_inicio' 
                    and cuenta.cuenta_numero <= '$request->cuenta_fin'
                    $sucursales
                group by
                    cuenta.cuenta_id
            "));
            
            return view('admin.contabilidad.mayorAuxiliar.index2',[
                'ini'=>$request->get('cuenta_inicio'),
                'fin'=>$request->get('cuenta_fin'),
                'sucursalC'=>$request->get('sucursal_id'),
                'sucursales'=>Sucursal::sucursales()->get(),
                'fDesde'=>$request->get('fecha_desde'),
                'fHasta'=>$request->get('fecha_hasta'),
                'datos'=>$datos,
                'saldos'=>$saldo_anterior,
                'cuentas'=>Cuenta::Cuentas()->orderBy('cuenta_numero','asc')->get(),
                'cuentaFinal'=>Cuenta::CuentasDesc()->first()->cuenta_id,
                'PE'=>Punto_Emision::puntos()->get(),
                'tipoPermiso'=>$tipoPermiso,
                'gruposPermiso'=>$gruposPermiso,
                'permisosAdmin'=>$permisosAdmin
            ]);
        }catch(\Exception $ex){
            return redirect('mayorAuxiliar')->with('error2','Oucrrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    */

    public function buscar2(Request $request){
        ini_set('memory_limit','-1');
        ini_set('max_execution_time', 600);
       
        try{   
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
            $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            
            $sucursales="";
            if($request->sucursal_id>0) $sucursales=" and sucursal.sucursal_id=".$request->sucursal_id;

            $datos=DB::select(DB::raw("
                select cuenta.*, 
                    diario.diario_fecha, diario.diario_codigo, diario.diario_comentario, diario.diario_beneficiario, detalle_diario.detalle_debe, detalle_diario.detalle_haber, 
                    detalle_diario.detalle_comentario, detalle_diario.detalle_tipo_documento, detalle_diario.detalle_numero_documento,sucursal.sucursal_nombre 
                    
                from detalle_diario
                    inner join cuenta on cuenta.cuenta_id=detalle_diario.cuenta_id
                    inner join diario on diario.diario_id = detalle_diario.diario_id 
                    inner join empresa on empresa.empresa_id=cuenta.empresa_id
                    inner join sucursal on sucursal.sucursal_id=diario.sucursal_id
                where empresa.empresa_id = ".Auth::user()->empresa_id."
                    and cuenta.cuenta_estado = '1' 
                    and cuenta.cuenta_numero >= '$request->cuenta_inicio'
                    and cuenta.cuenta_numero <= '$request->cuenta_fin'
                    and diario.diario_fecha >= '$request->fecha_desde'
                    and diario.diario_fecha <= '$request->fecha_hasta' 
                    $sucursales
                order by 
                    cuenta.cuenta_id,
                    cuenta.cuenta_nombre,
                    diario.diario_fecha
            "));

            $saldo_anterior=DB::select(DB::raw("
                select 
                    cuenta.cuenta_id, SUM(detalle_debe)-SUM(detalle_haber) as saldo
                from cuenta
                    left join detalle_diario on cuenta.cuenta_id = detalle_diario.cuenta_id
                    inner join diario on diario.diario_id = detalle_diario.diario_id 
                    inner join sucursal on sucursal.sucursal_id=diario.sucursal_id
                where 
                    cuenta.empresa_id = ".Auth::user()->empresa_id."
                    and diario.diario_fecha < '$request->fecha_desde' 
                    and detalle_diario.cuenta_id=cuenta.cuenta_id
                    and cuenta.cuenta_numero >= '$request->cuenta_inicio' 
                    and cuenta.cuenta_numero <= '$request->cuenta_fin'
                    $sucursales
                group by
                    cuenta.cuenta_id
            "));
            
            return view('admin.contabilidad.mayorAuxiliar.index2',[
                'ini'=>$request->get('cuenta_inicio'),
                'fin'=>$request->get('cuenta_fin'),
                'sucursalC'=>$request->get('sucursal_id'),
                'sucursales'=>Sucursal::sucursales()->get(),
                'fDesde'=>$request->get('fecha_desde'),
                'fHasta'=>$request->get('fecha_hasta'),
                'datos'=>$datos,
                'saldos'=>$saldo_anterior,
                'cuentas'=>Cuenta::Cuentas()->orderBy('cuenta_numero','asc')->get(),
                'cuentaFinal'=>Cuenta::CuentasDesc()->first()->cuenta_id,
                'PE'=>Punto_Emision::puntos()->get(),
                'tipoPermiso'=>$tipoPermiso,
                'gruposPermiso'=>$gruposPermiso,
                'permisosAdmin'=>$permisosAdmin
            ]);
        }catch(\Exception $ex){
            return redirect('mayorAuxiliar')->with('error2','Oucrrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }

    public function pdf(Request $request){
        ini_set('memory_limit','-1');
        ini_set('max_input_vars ', 10000);
        ini_set('max_execution_time', 600);


        //return $request;


        try{            
            $datos = null;
            $count = 1;
            $cod = $request->get('idCod');
            $nom = $request->get('idNom');
            $fec = $request->get('idFec');
            $doc = $request->get('idDoc');
            $num = $request->get('idNum');
            $deb = $request->get('idDeb');
            $hab = $request->get('idHab');
            $act = $request->get('idAct');
            $ben = $request->get('idBen');
            $dia = $request->get('idDia');
            $com = $request->get('idCom');
            $suc = $request->get('idSuc');
            $tot = $request->get('idTot');
            if($cod){
                for ($i = 0; $i < count($cod); ++$i){
                    $datos[$count]['cod'] = $cod[$i];
                    $datos[$count]['nom'] = $nom[$i];
                    $datos[$count]['fec'] = $fec[$i];
                    $datos[$count]['doc'] = $doc[$i];
                    $datos[$count]['num'] = $num[$i];
                    $datos[$count]['deb'] = $deb[$i];
                    $datos[$count]['hab'] = $hab[$i];
                    $datos[$count]['act'] = $act[$i];
                    $datos[$count]['ben'] = $ben[$i];
                    $datos[$count]['dia'] = $dia[$i];
                    $datos[$count]['com'] = isset($com[$i])? $com[$i]: '';
                    $datos[$count]['suc'] = isset($suc[$i])? $suc[$i]: '';
                    $datos[$count]['tot'] = isset($tot[$i])? $tot[$i]: '';
                    $count ++;
                }
            }
            $empresa =  Empresa::empresa()->first();
            $ruta = public_path().'/PDF/'.$empresa->empresa_ruc;
            if (!is_dir($ruta)) {
                mkdir($ruta, 0777, true);
            }
            $view =  \View::make('admin.formatosPDF.mayorAuxiliar', ['datos'=>$datos,'desde'=>DateTime::createFromFormat('Y-m-d', $request->get('fecha_desde'))->format('d/m/Y'),'hasta'=>DateTime::createFromFormat('Y-m-d', $request->get('fecha_hasta'))->format('d/m/Y'),'empresa'=>$empresa]);
            $nombreArchivo = 'MAYOR DE CUENTAS '.DateTime::createFromFormat('Y-m-d', $request->get('fecha_desde'))->format('d-m-Y').' AL '.DateTime::createFromFormat('Y-m-d', $request->get('fecha_hasta'))->format('d-m-Y');
            
            //return $view;
            return PDF::loadHTML($view)->setPaper('a4', 'landscape')->save('PDF/'.$empresa->empresa_ruc.'/'.$nombreArchivo.'.pdf')->download($nombreArchivo.'.pdf');
            //return PDF::loadHTML($view)->save('PDF/'.$empresa->empresa_ruc.'/'.$nombreArchivo.'.pdf')->stream($nombreArchivo.'.pdf');
        }catch(\Exception $ex){
            return redirect('mayorAuxiliar')->with('error2','Oucrrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }

    public function pdf2(Request $request){
        ini_set('memory_limit','-1');
        ini_set('max_execution_time', 600);

        try{            
            $sucursales="";
            if($request->sucursal_id>0) $sucursales=" and sucursal.sucursal_id=".$request->sucursal_id;

            $datos=DB::select(DB::raw("
                select cuenta.*, 
                    diario.diario_fecha, diario.diario_codigo, diario.diario_comentario, diario.diario_beneficiario, detalle_diario.detalle_debe, detalle_diario.detalle_haber, 
                    detalle_diario.detalle_comentario, detalle_diario.detalle_tipo_documento, detalle_diario.detalle_numero_documento,sucursal.sucursal_nombre 
                    
                from detalle_diario
                    inner join cuenta on cuenta.cuenta_id=detalle_diario.cuenta_id
                    inner join diario on diario.diario_id = detalle_diario.diario_id 
                    inner join empresa on empresa.empresa_id=cuenta.empresa_id
                    inner join sucursal on sucursal.sucursal_id=diario.sucursal_id
                where empresa.empresa_id = ".Auth::user()->empresa_id."
                    and cuenta.cuenta_estado = '1' 
                    and cuenta.cuenta_numero >= '$request->cuenta_inicio'
                    and cuenta.cuenta_numero <= '$request->cuenta_fin'
                    and diario.diario_fecha >= '$request->fecha_desde'
                    and diario.diario_fecha <= '$request->fecha_hasta' 
                    $sucursales
                order by 
                    cuenta.cuenta_id,
                    cuenta.cuenta_nombre,
                    diario.diario_fecha
            "));

            $saldo_anterior=DB::select(DB::raw("
                select 
                    cuenta.cuenta_id, SUM(detalle_debe)-SUM(detalle_haber) as saldo
                from cuenta
                    left join detalle_diario on cuenta.cuenta_id = detalle_diario.cuenta_id
                    inner join diario on diario.diario_id = detalle_diario.diario_id 
                    inner join sucursal on sucursal.sucursal_id=diario.sucursal_id
                where 
                    cuenta.empresa_id = ".Auth::user()->empresa_id."
                    and diario.diario_fecha < '$request->fecha_desde' 
                    and detalle_diario.cuenta_id=cuenta.cuenta_id
                    and cuenta.cuenta_numero >= '$request->cuenta_inicio' 
                    and cuenta.cuenta_numero <= '$request->cuenta_fin'
                    $sucursales
                group by
                    cuenta.cuenta_id
            "));

            $this->fpdf->AddPage();
            
            if(isset($datos)){
                $anterior=0;
                $actual=0;
                $tfila=0;  //1.-cabecera  2.-detalle   3.-final
                
                $saldo=0;
                $debe=0;
                $haber=0;

                foreach($datos as $dato){  
                    $actual=$dato->cuenta_numero;
                    if($anterior!=$actual) $tfila=0;
                    if($anterior==$actual) $tfila=1;

                    if($tfila==0){
                        if($anterior>0){
                            $this->fpdf->SetFillColor(211, 211, 211);
                            $this->fpdf->Cell(75,8,'',1,0,'L', true);
                            $this->fpdf->Cell(25,8,number_format($debe,2),1,0,'R', true);
                            $this->fpdf->Cell(25,8,number_format($haber,2),1,0,'R', true);
                            $this->fpdf->Cell(25,8,'',1,0,'R', true);
                            $this->fpdf->Cell(125,8,'',1,1,'L', true);
                            $debe=0;
                            $haber=0;
                        }

                       
                        $saldo=0;
                        foreach($saldo_anterior as $sd){
                            if($sd->cuenta_id==$dato->cuenta_id){
                                $saldo=$sd->saldo;
                                break;
                            }
                        }
                   
                        $this->fpdf->SetFillColor(175, 223, 255);
                        $this->fpdf->SetFont('Arial', 'B', 10);
                        $this->fpdf->Cell(125, 9, utf8_decode($dato->cuenta_numero.'  -  '.$dato->cuenta_nombre),1,0,'L', true);
                        $this->fpdf->Cell(25, 9,number_format($saldo,2),1,0,'R', true);
                        $this->fpdf->Cell(125, 9,'',1,1,'L', true);
                    }
                    
                    $debe=$debe+$dato->detalle_debe;
                    $haber=$haber+$dato->detalle_haber;
                
                    $saldo+=$dato->detalle_debe;
                    $saldo-=$dato->detalle_haber;
                    
                    $this->fpdf->SetFont('Arial', '', 7);
                    $this->fpdf->SetFillColor(255, 255, 255);
                    $this->fpdf->Cell(15,7, $dato->diario_fecha,1,0,'L');
                    $this->fpdf->Cell(60,7, utf8_decode(substr($dato->detalle_tipo_documento,0,20).'  -  '.$dato->detalle_numero_documento),1,0,'C');
                    $this->fpdf->Cell(25,7,number_format($dato->detalle_debe, 2),1,0,'R');
                    $this->fpdf->Cell(25,7,number_format($dato->detalle_haber, 2),1,0,'R');

                    

                    $this->fpdf->Cell(25,7,number_format($saldo,2),1,0,'R');

                    $this->fpdf->Cell(68,7,utf8_decode(substr($dato->diario_beneficiario,0,40).(strlen($dato->diario_beneficiario)>40 ? '...': '')),1,0,'L');
                    $this->fpdf->Cell(27,7,$dato->diario_codigo,1,0,'C');
                    $this->fpdf->Cell(30,7,utf8_decode($dato->sucursal_nombre),1,1,'C');

                    $anterior=$actual;
                }

                $this->fpdf->SetFillColor(211, 211, 211);
                $this->fpdf->Cell(75,8,'',1,0,'L', true);
                $this->fpdf->Cell(25,8,number_format($debe,2),1,0,'R', true);
                $this->fpdf->Cell(25,8,number_format($haber,2),1,0,'R', true);
                $this->fpdf->Cell(25,8,'',1,0,'R', true);
                $this->fpdf->Cell(125,8,'',1,1,'L', true);
            }

            $this->fpdf->Output();
            exit;
        }catch(\Exception $ex){
            return redirect('mayorAuxiliar')->with('error2','Oucrrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }

    public function excel(Request $request){
        ini_set('memory_limit','-1');
        ini_set('max_execution_time', 600);
        
        //try{            
            $sucursales="";
            if($request->sucursal_id>0) $sucursales=" and sucursal.sucursal_id=".$request->sucursal_id;

            $datos=DB::select(DB::raw("
                select cuenta.*, 
                    diario.diario_fecha, diario.diario_codigo, diario.diario_comentario, diario.diario_beneficiario, detalle_diario.detalle_debe, detalle_diario.detalle_haber, 
                    detalle_diario.detalle_comentario, detalle_diario.detalle_tipo_documento, detalle_diario.detalle_numero_documento,sucursal.sucursal_nombre 
                
                from detalle_diario
                    inner join cuenta on cuenta.cuenta_id=detalle_diario.cuenta_id
                    inner join diario on diario.diario_id = detalle_diario.diario_id 
                    inner join empresa on empresa.empresa_id=cuenta.empresa_id
                    inner join sucursal on sucursal.sucursal_id=diario.sucursal_id
                where empresa.empresa_id = ".Auth::user()->empresa_id."
                    and cuenta.cuenta_estado = '1' 
                    and cuenta.cuenta_numero >= '$request->cuenta_inicio'
                    and cuenta.cuenta_numero <= '$request->cuenta_fin'
                    and diario.diario_fecha >= '$request->fecha_desde'
                    and diario.diario_fecha <= '$request->fecha_hasta' 
                    $sucursales
                order by 
                    cuenta.cuenta_id,
                    cuenta.cuenta_nombre,
                    diario.diario_fecha
            "));

            $saldo_anterior=DB::select(DB::raw("
                select 
                    cuenta.cuenta_id, SUM(detalle_debe)-SUM(detalle_haber) as saldo
                from cuenta
                    left join detalle_diario on cuenta.cuenta_id = detalle_diario.cuenta_id
                    inner join diario on diario.diario_id = detalle_diario.diario_id 
                    inner join sucursal on sucursal.sucursal_id=diario.sucursal_id
                where 
                    cuenta.empresa_id = ".Auth::user()->empresa_id."
                    and diario.diario_fecha < '$request->fecha_desde' 
                    and detalle_diario.cuenta_id=cuenta.cuenta_id
                    and cuenta.cuenta_numero >= '$request->cuenta_inicio' 
                    and cuenta.cuenta_numero <= '$request->cuenta_fin'
                    $sucursales
                group by
                    cuenta.cuenta_id
            "));

            //return $saldo_anterior;

            return Excel::download(new ViewExcel('admin.formatosExcel.mayorAuxiliar', [
                'datos'=>$datos,
                'saldos'=>$saldo_anterior
            ]), 
            'NEOPAGUPA  Sistema Contable.xlsx');

            $this->fpdf->AddPage();
            
            if(isset($datos)){
                $anterior=0;
                $actual=0;
                $tfila=0;  //1.-cabecera  2.-detalle   3.-final
                
                $saldo=0;
                $debe=0;
                $haber=0;

                foreach($datos as $dato){  
                    $actual=$dato->cuenta_numero;
                    if($anterior!=$actual) $tfila=0;
                    if($anterior==$actual) $tfila=1;

                    if($tfila==0){
                        if($anterior>0){
                            $this->fpdf->SetFillColor(211, 211, 211);
                            $this->fpdf->Cell(75,8,'',1,0,'L', true);
                            $this->fpdf->Cell(25,8,number_format($debe,2),1,0,'R', true);
                            $this->fpdf->Cell(25,8,number_format($haber,2),1,0,'R', true);
                            $this->fpdf->Cell(25,8,'',1,0,'R', true);
                            $this->fpdf->Cell(125,8,'',1,1,'L', true);
                            $debe=0;
                            $haber=0;
                        }

                       
                        $saldo=0;
                        foreach($saldo_anterior as $sd){
                            if($sd->cuenta_id==$dato->cuenta_id){
                                $saldo=$sd->saldo;
                                break;
                            }
                        }
                   
                        $this->fpdf->SetFillColor(175, 223, 255);
                        $this->fpdf->SetFont('Arial', 'B', 10);
                        $this->fpdf->Cell(125, 9, utf8_decode($dato->cuenta_numero.'  -  '.$dato->cuenta_nombre),1,0,'L', true);
                        $this->fpdf->Cell(25, 9,number_format($saldo,2),1,0,'R', true);
                        $this->fpdf->Cell(125, 9,'',1,1,'L', true);
                    }
                    
                    $debe=$debe+$dato->detalle_debe;
                    $haber=$haber+$dato->detalle_haber;
                
                    $saldo+=$dato->detalle_debe;
                    $saldo-=$dato->detalle_haber;
                    
                    $this->fpdf->SetFont('Arial', '', 7);
                    $this->fpdf->SetFillColor(255, 255, 255);
                    $this->fpdf->Cell(15,7, $dato->diario_fecha,1,0,'L');
                    $this->fpdf->Cell(60,7, utf8_decode(substr($dato->detalle_tipo_documento,0,20).'  -  '.$dato->detalle_numero_documento),1,0,'C');
                    $this->fpdf->Cell(25,7,number_format($dato->detalle_debe, 2),1,0,'R');
                    $this->fpdf->Cell(25,7,number_format($dato->detalle_haber, 2),1,0,'R');

                    

                    $this->fpdf->Cell(25,7,number_format($saldo,2),1,0,'R');

                    $this->fpdf->Cell(68,7,utf8_decode(substr($dato->diario_beneficiario,0,40).(strlen($dato->diario_beneficiario)>40 ? '...': '')),1,0,'L');
                    $this->fpdf->Cell(27,7,$dato->diario_codigo,1,0,'C');
                    $this->fpdf->Cell(30,7,utf8_decode($dato->sucursal_nombre),1,1,'C');

                    $anterior=$actual;
                }
            }

            $this->fpdf->Output();
            exit;
        //}catch(\Exception $ex){
        //    return redirect('mayorAuxiliar')->with('error2','Oucrrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        //}
    }
}
