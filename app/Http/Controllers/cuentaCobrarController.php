<?php

namespace App\Http\Controllers;

use App\Models\Arqueo_Caja;
use App\Models\Cliente;
use App\Models\Cuenta;
use App\Models\Cuenta_Cobrar;
use App\Models\Descuento_Anticipo_Cliente;
use App\Models\Detalle_Diario;
use App\Models\Detalle_Pago_CXC;
use App\Models\Empresa;
use App\Models\Punto_Emision;
use App\Models\Sucursal;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PDF;
use Codedge\Fpdf\Fpdf\Fpdf;

use App\NEOPAGUPA\ViewExcel;
use Maatwebsite\Excel\Facades\Excel;

class cuentaCobrarController extends Controller
{
    protected $fpdf;

    public function __construct()
    {
        $this->fpdf = new Fpdf('L', 'mm', 'A4');
    }

    public function index()
    {
        try {
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono', 'grupo_orden', 'grupo_orden')->join('rol_permiso', 'usuario_rol.rol_id', '=', 'rol_permiso.rol_id')->join('permiso', 'permiso.permiso_id', '=', 'rol_permiso.permiso_id')->join('grupo_permiso', 'grupo_permiso.grupo_id', '=', 'permiso.grupo_id')->join('tipo_grupo', 'tipo_grupo.grupo_id', '=', 'grupo_permiso.grupo_id')->where('permiso_estado', '=', '1')->where('usuario_rol.user_id', '=', Auth::user()->user_id)->orderBy('grupo_orden', 'asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id', 'tipo_grupo.tipo_id', 'tipo_nombre', 'tipo_icono', 'tipo_orden')->join('rol_permiso', 'usuario_rol.rol_id', '=', 'rol_permiso.rol_id')->join('permiso', 'permiso.permiso_id', '=', 'rol_permiso.permiso_id')->join('tipo_grupo', 'tipo_grupo.tipo_id', '=', 'permiso.tipo_id')->where('permiso_estado', '=', '1')->where('usuario_rol.user_id', '=', Auth::user()->user_id)->orderBy('tipo_orden', 'asc')->distinct()->get();
            $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso', 'usuario_rol.rol_id', '=', 'rol_permiso.rol_id')->join('permiso', 'permiso.permiso_id', '=', 'rol_permiso.permiso_id')->where('permiso_estado', '=', '1')->where('usuario_rol.user_id', '=', Auth::user()->user_id)->orderBy('permiso_orden', 'asc')->get();
            return view('admin.cuentasCobrar.estadoCuenta.index', ['cuentas'=>Cuenta::Cuentas()->orderBy('cuenta_numero', 'asc')->get(),'gruposPermiso'=>$gruposPermiso,'sucursales'=>Sucursal::sucursales()->get(),'clientes'=>Cliente::clientes()->get(),'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        } catch(\Exception $ex) {
            return redirect('inicio')->with('error', 'Ocurrio un error vuelva a intentarlo('.$ex->getMessage().')');
        }
    }
    public function CargarExcel()
    {
        try {
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono', 'grupo_orden', 'grupo_orden')->join('rol_permiso', 'usuario_rol.rol_id', '=', 'rol_permiso.rol_id')->join('permiso', 'permiso.permiso_id', '=', 'rol_permiso.permiso_id')->join('grupo_permiso', 'grupo_permiso.grupo_id', '=', 'permiso.grupo_id')->join('tipo_grupo', 'tipo_grupo.grupo_id', '=', 'grupo_permiso.grupo_id')->where('permiso_estado', '=', '1')->where('usuario_rol.user_id', '=', Auth::user()->user_id)->orderBy('grupo_orden', 'asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id', 'tipo_grupo.tipo_id', 'tipo_nombre', 'tipo_icono', 'tipo_orden')->join('rol_permiso', 'usuario_rol.rol_id', '=', 'rol_permiso.rol_id')->join('permiso', 'permiso.permiso_id', '=', 'rol_permiso.permiso_id')->join('tipo_grupo', 'tipo_grupo.tipo_id', '=', 'permiso.tipo_id')->where('permiso_estado', '=', '1')->where('usuario_rol.user_id', '=', Auth::user()->user_id)->orderBy('tipo_orden', 'asc')->distinct()->get();
            $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso', 'usuario_rol.rol_id', '=', 'rol_permiso.rol_id')->join('permiso', 'permiso.permiso_id', '=', 'rol_permiso.permiso_id')->where('permiso_estado', '=', '1')->where('usuario_rol.user_id', '=', Auth::user()->user_id)->orderBy('permiso_orden', 'asc')->get();
            return view('admin.cuentasCobrar.estadoCuenta.cargarExcel', ['PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        } catch(\Exception $ex) {
            return redirect('inicio')->with('error2', 'Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function CargarExcelCuentaCobrar(Request $request)
    {
        try {
            DB::beginTransaction();
            if ($request->file('excelCXC')->isValid()) {
                $empresa = Empresa::empresa()->first();
                $name = $empresa->empresa_ruc. '.' .$request->file('excelCXC')->getClientOriginalExtension();
                $path = $request->file('excelCXC')->move(public_path().'\temp\CXC', $name);
                $array = Excel::toArray(new Cuenta_Cobrar(), $path);
                for ($i=1;$i < count($array[0]);$i++) {
                    $validar=trim($array[0][$i][3]);
                    if ($validar) {
                        $validacion=Cliente::Existe($validar)->get();
                        //return $validacion;
                        if (count($validacion)==1) {
                            $validacion=Cliente::Existe($validar)->first();
                            foreach (Cuenta_Cobrar::CuentaByFacturaMigrada($array[0][$i][0])->get() as $cuenta) {
                                if (substr($cuenta->cuenta_descripcion, 38)==$array[0][$i][0] && $validacion->cliente_id==$cuenta->cliente_id) {
                                    $cuentas=Cuenta_Cobrar::findOrFail($cuenta->cuenta_id);
                                    $cuentas->cuenta_saldom=($array[0][$i][1]);
                                    $cuentas->save();
                                } else {
                                    return dd($array[0][$i][0]);
                                }
                            }
                        }
                    }
                }
            }
            DB::commit();
            return redirect('excelCXC')->with('success', 'Datos guardados exitosamente');
        } catch(\Exception $ex) {
            DB::rollBack();
            return redirect('excelCXC')->with('error2', 'Ocurrio un error vuelva a intentarlo('.$ex->getMessage().')');
        }
    }
    public function consultar(Request $request)
    {
        if ($request->get('tipoConsulta') == "0") {
            if (isset($_POST['buscar'])) {
                return $this->pagos2($request);
            } elseif (isset($_POST['pdf'])) {
                return $this->pdfPagos($request);
            } else {
                return $this->excelPagos($request);
            }
        } else {
            if (isset($_POST['buscar'])) {
                return $this->pendientesPago($request);
            } elseif (isset($_POST['pdf'])) {
                return $this->pdfPendiente($request);
            } else {
                return $this->excelPendiente($request);
            }
        }
    }
    
    public function pagos2(Request $request)
    {
        ini_set('max_execution_time', 500);
        ini_set('memory_limit', -1);
        //try{
        $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono', 'grupo_orden', 'grupo_orden')->join('rol_permiso', 'usuario_rol.rol_id', '=', 'rol_permiso.rol_id')->join('permiso', 'permiso.permiso_id', '=', 'rol_permiso.permiso_id')->join('grupo_permiso', 'grupo_permiso.grupo_id', '=', 'permiso.grupo_id')->join('tipo_grupo', 'tipo_grupo.grupo_id', '=', 'grupo_permiso.grupo_id')->where('permiso_estado', '=', '1')->where('usuario_rol.user_id', '=', Auth::user()->user_id)->orderBy('grupo_orden', 'asc')->distinct()->get();
        $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id', 'tipo_grupo.tipo_id', 'tipo_nombre', 'tipo_icono', 'tipo_orden')->join('rol_permiso', 'usuario_rol.rol_id', '=', 'rol_permiso.rol_id')->join('permiso', 'permiso.permiso_id', '=', 'rol_permiso.permiso_id')->join('tipo_grupo', 'tipo_grupo.tipo_id', '=', 'permiso.tipo_id')->where('permiso_estado', '=', '1')->where('usuario_rol.user_id', '=', Auth::user()->user_id)->orderBy('tipo_orden', 'asc')->distinct()->get();
        $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso', 'usuario_rol.rol_id', '=', 'rol_permiso.rol_id')->join('permiso', 'permiso.permiso_id', '=', 'rol_permiso.permiso_id')->where('permiso_estado', '=', '1')->where('usuario_rol.user_id', '=', Auth::user()->user_id)->orderBy('permiso_orden', 'asc')->get();
        $count = 1;
        $countCliente = 0;
        $countCuenta = 0;
        $datos = null;
        $todo = 0;
        $mon = 0;
        $sal = 0;
        $pag = 0;

        
        /* if ($request->get('clienteID') == "0") {
            $clientes = Cliente::clientes()->get();
        } else {
            $clientes = Cliente::cliente($request->get('clienteID'))->get();
        } */

        $fechas="";
        $cliente="";
        $fechas= "and pago_fecha >= '$request->fecha_desde' and pago_fecha <= '$request->fecha_hasta'";

        if ($request->clienteID>0) {
            $cliente="and cliente.cliente_id=$request->clienteID";
        }

        $datos=DB::select(DB::raw("
                select distinct
                    cuenta_cobrar.cuenta_id,
                    factura_venta.factura_numero,
                    d2.diario_codigo as diario_factura,
                    retencion_venta.retencion_numero,
                    nota_entrega.nt_numero,
                    nota_debito.nd_numero,
                    descuento_anticipo_cliente.descuento_id,
                    descuento_anticipo_cliente.descuento_valor,
                    descuento_anticipo_cliente.descuento_fecha,
                    cuenta_cobrar.cuenta_fecha,
                    cuenta_cobrar.cuenta_monto,
                    cuenta_cobrar.cuenta_descripcion,
                    cuenta_cobrar.cuenta_saldo,
                    cuenta_cobrar.cuenta_saldom as saldom,
                    cliente.cliente_nombre,
                    detalle_pago_cxc.detalle_pago_id,
                    detalle_pago_cxc.detalle_pago_valor,
                    pago_cxc.pago_fecha,
                    diario.diario_codigo,
                    detalle_pago_cxc.detalle_pago_descripcion
                from cuenta_cobrar
                    left join factura_venta on factura_venta.cuenta_id=cuenta_cobrar.cuenta_id
                    left join diario as d2 on d2.diario_id=factura_venta.diario_id
                    left join retencion_venta on retencion_venta.factura_id=factura_venta.factura_id
                    left join nota_entrega on nota_entrega.cuenta_id=cuenta_cobrar.cuenta_id
                    left join nota_debito on nota_debito.cuenta_id=cuenta_cobrar.cuenta_id
                    left join descuento_anticipo_cliente on factura_venta.factura_id=descuento_anticipo_cliente.factura_id
                    inner join detalle_pago_cxc on cuenta_cobrar.cuenta_id = detalle_pago_cxc.cuenta_id
                    inner join pago_cxc on detalle_pago_cxc.pago_id = pago_cxc.pago_id 
                    inner join cliente on cliente.cliente_id = cuenta_cobrar.cliente_id
                    inner join diario on diario.diario_id=pago_cxc.diario_id
                    inner join tipo_identificacion on tipo_identificacion.tipo_identificacion_id = cliente.tipo_identificacion_id 
                
                where 
                    tipo_identificacion.empresa_id = ".Auth::user()->empresa_id."
                    $fechas 
                    $cliente 
                order by
                    cliente.cliente_nombre,
                    cuenta_cobrar.cuenta_fecha asc, 
                    cuenta_cobrar.cuenta_id asc,
                    pago_cxc.pago_fecha
            "));

        $totales=DB::select(DB::raw("
                select
                    cliente_nombre, sum(monto) as monto, sum(saldo) as saldo, sum(saldom) as saldom,
                    sum(descuento_valor) as descuento_valor, sum(detalle_pago_valor) as detalle_pago_valor, sum(pago_migrado) as pago_migrado
                from (
                    select distinct
                        cuenta_cobrar.cuenta_id, cliente.cliente_nombre, cuenta_cobrar.cuenta_monto as monto,
                        cuenta_cobrar.cuenta_saldo as saldo, cuenta_cobrar.cuenta_saldom as saldom,
                        sum(descuento_anticipo_cliente.descuento_valor) as descuento_valor, sum(detalle_pago_cxc.detalle_pago_valor) as detalle_pago_valor,
                        case when cuenta_cobrar.cuenta_saldom>0 then sum(detalle_pago_cxc.detalle_pago_valor) else 0 end as pago_migrado
                    from cuenta_cobrar
                        left join factura_venta on factura_venta.cuenta_id=cuenta_cobrar.cuenta_id
                        left join diario as d2 on d2.diario_id=factura_venta.diario_id
                        left join retencion_venta on retencion_venta.factura_id=factura_venta.factura_id
                        left join nota_entrega on nota_entrega.cuenta_id=cuenta_cobrar.cuenta_id
                        left join nota_debito on nota_debito.cuenta_id=cuenta_cobrar.cuenta_id
                        left join descuento_anticipo_cliente on factura_venta.factura_id=descuento_anticipo_cliente.factura_id
                        inner join detalle_pago_cxc on cuenta_cobrar.cuenta_id = detalle_pago_cxc.cuenta_id
                        inner join pago_cxc on detalle_pago_cxc.pago_id = pago_cxc.pago_id 
                        inner join cliente on cliente.cliente_id = cuenta_cobrar.cliente_id
                        inner join diario on diario.diario_id=pago_cxc.diario_id
                        inner join tipo_identificacion on tipo_identificacion.tipo_identificacion_id = cliente.tipo_identificacion_id
                    
                    where 
                        tipo_identificacion.empresa_id = ".Auth::user()->empresa_id."
                        $fechas
                        $cliente 
                    group by 
                        cuenta_cobrar.cuenta_id, cliente.cliente_nombre, cuenta_cobrar.cuenta_monto,
                        cuenta_cobrar.cuenta_saldo, cuenta_cobrar.cuenta_saldom
                    ) as val
                group by cliente_nombre
                order by cliente_nombre
            "));

        $pagos=DB::select(DB::raw("
                select 
                cliente_nombre, sum(pagos) as pagos, sum(anticipos) as anticipos
                from (
                    select distinct
                        cuenta_cobrar.cuenta_id,
                        cliente.cliente_nombre,
                        retencion_venta.retencion_numero,
                        factura_venta.factura_numero,
                        cuenta_cobrar.cuenta_saldo,
                        detalle_pago_cxc.detalle_pago_valor as pagos,
                        descuento_anticipo_cliente.descuento_valor as anticipos,
                        diario.diario_codigo
                    from cuenta_cobrar
                        left join factura_venta on factura_venta.cuenta_id=cuenta_cobrar.cuenta_id
                        left join retencion_venta on retencion_venta.factura_id=factura_venta.factura_id
                        left join nota_entrega on nota_entrega.cuenta_id=cuenta_cobrar.cuenta_id
                        left join nota_debito on nota_debito.cuenta_id=cuenta_cobrar.cuenta_id
                        left join descuento_anticipo_cliente on factura_venta.factura_id=descuento_anticipo_cliente.factura_id
                        inner join detalle_pago_cxc on cuenta_cobrar.cuenta_id = detalle_pago_cxc.cuenta_id
                        inner join pago_cxc on detalle_pago_cxc.pago_id = pago_cxc.pago_id 
                        inner join cliente on cliente.cliente_id = cuenta_cobrar.cliente_id
                        inner join diario on diario.diario_id=pago_cxc.diario_id
                        inner join tipo_identificacion on tipo_identificacion.tipo_identificacion_id = cliente.tipo_identificacion_id 
                    where 
                        tipo_identificacion.empresa_id = ".Auth::user()->empresa_id."
                        $fechas
                        $cliente 
                    order by
                        cliente.cliente_nombre,
                        cuenta_cobrar.cuenta_id asc
                    ) as val
                group by cliente_nombre
            "));

        $pagosF=DB::select(DB::raw("
            select distinct
                cuenta_cobrar.cuenta_id,
                cuenta_cobrar.cuenta_monto,
                cuenta_cobrar.cuenta_saldo,
                cuenta_cobrar.cuenta_saldom,
                detalle_pago_cxc.detalle_pago_valor as pagos,
                descuento_anticipo_cliente.descuento_valor as anticipos
            from cuenta_cobrar
                left join factura_venta on factura_venta.cuenta_id=cuenta_cobrar.cuenta_id
                left join diario as d2 on d2.diario_id=factura_venta.diario_id
                left join retencion_venta on retencion_venta.factura_id=factura_venta.factura_id
                left join nota_entrega on nota_entrega.cuenta_id=cuenta_cobrar.cuenta_id
                left join nota_debito on nota_debito.cuenta_id=cuenta_cobrar.cuenta_id
                left join descuento_anticipo_cliente on factura_venta.factura_id=descuento_anticipo_cliente.factura_id
                inner join detalle_pago_cxc on cuenta_cobrar.cuenta_id = detalle_pago_cxc.cuenta_id
                inner join pago_cxc on detalle_pago_cxc.pago_id = pago_cxc.pago_id 
                inner join cliente on cliente.cliente_id = cuenta_cobrar.cliente_id
                inner join diario on diario.diario_id=pago_cxc.diario_id
                inner join tipo_identificacion on tipo_identificacion.tipo_identificacion_id = cliente.tipo_identificacion_id   
            
            where 
                tipo_identificacion.empresa_id = ".Auth::user()->empresa_id."
                $fechas
                $cliente 
            order by
                cuenta_cobrar.cuenta_id asc
        "));

        ///return $totales;
        //return $pagosF;


        return view('admin.cuentasCobrar.estadoCuenta.index2', [
            'cuentas'=>Cuenta::Cuentas()->orderBy('cuenta_numero', 'asc')->get(),
            'tab'=>'1',
            'mon'=>$mon,
            'sal'=>$sal,
            'pag'=>$pag,
            'fecC'=>$request->get('fecha_corte'),
            'tipo'=>$request->get('tipoConsulta'),
            'sucurslaC'=>$request->get('sucursal_id'),
            'sucursales'=>Sucursal::sucursales()->get(),
            'clienteC'=>$request->get('clienteID'),
            'fecI'=>$request->get('fecha_desde'),
            'fecF'=>$request->get('fecha_hasta'),
            'todo'=>$todo,
            'datos'=>$datos,
            'saldos'=>$totales,
            'pagos'=>$pagos,
            'totalF'=>$pagosF,
            'clientes'=>Cliente::clientes()->get(),
            'PE'=>Punto_Emision::puntos()->get(),
            'tipoPermiso'=>$tipoPermiso,
            'gruposPermiso'=>$gruposPermiso,
            'permisosAdmin'=>$permisosAdmin
        ]);
        //}catch(\Exception $ex){
        //    return redirect('cxc')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        //}
    }

    public function pdfPagos(Request $request){
        ini_set('max_execution_time', 1200);

        //try {
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono', 'grupo_orden', 'grupo_orden')->join('rol_permiso', 'usuario_rol.rol_id', '=', 'rol_permiso.rol_id')->join('permiso', 'permiso.permiso_id', '=', 'rol_permiso.permiso_id')->join('grupo_permiso', 'grupo_permiso.grupo_id', '=', 'permiso.grupo_id')->join('tipo_grupo', 'tipo_grupo.grupo_id', '=', 'grupo_permiso.grupo_id')->where('permiso_estado', '=', '1')->where('usuario_rol.user_id', '=', Auth::user()->user_id)->orderBy('grupo_orden', 'asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id', 'tipo_grupo.tipo_id', 'tipo_nombre', 'tipo_icono', 'tipo_orden')->join('rol_permiso', 'usuario_rol.rol_id', '=', 'rol_permiso.rol_id')->join('permiso', 'permiso.permiso_id', '=', 'rol_permiso.permiso_id')->join('tipo_grupo', 'tipo_grupo.tipo_id', '=', 'permiso.tipo_id')->where('permiso_estado', '=', '1')->where('usuario_rol.user_id', '=', Auth::user()->user_id)->orderBy('tipo_orden', 'asc')->distinct()->get();
            $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso', 'usuario_rol.rol_id', '=', 'rol_permiso.rol_id')->join('permiso', 'permiso.permiso_id', '=', 'rol_permiso.permiso_id')->where('permiso_estado', '=', '1')->where('usuario_rol.user_id', '=', Auth::user()->user_id)->orderBy('permiso_orden', 'asc')->get();
            $count = 1;
            $countCliente = 0;
            $countCuenta = 0;
            $datos = null;
            $todo = 0;
            $mon = 0;
            $sal = 0;
            $pag = 0;

            if ($request->get('clienteID') == "0") {
                $clientes = Cliente::clientes()->get();
            } else {
                $clientes = Cliente::cliente($request->get('clienteID'))->get();
            }

            $fechas="";
            $cliente="";
            $fechas= "and pago_fecha >= '$request->fecha_desde' and pago_fecha <= '$request->fecha_hasta'";

            if ($request->clienteID>0) {
                $cliente="and cliente.cliente_id=$request->clienteID";
            }


            /* foreach ($clientes as $cliente) {
                $datos[$count]['nom'] = $cliente->cliente_nombre;
                $datos[$count]['doc'] = '';
                $datos[$count]['num'] = '';
                $datos[$count]['fec'] = '';
                $datos[$count]['mon'] = 0;
                $datos[$count]['sal'] = 0;
                $datos[$count]['pag'] = 0;
                $datos[$count]['fep'] = '';
                $datos[$count]['dia'] = '';
                $datos[$count]['tip'] = '';
                $datos[$count]['tot'] = '1';
                $count ++;
                $countCliente = $count - 1;
                $banderaMigrada = false;
                foreach (Cuenta_Cobrar::CuentasCobrarByPagos($request->get('fecha_desde'), $request->get('fecha_hasta'), $cliente->cliente_id, $todo, $request->get('sucursal_id'))->select('cuenta_cobrar.cuenta_id', 'cuenta_cobrar.cuenta_fecha', 'cuenta_cobrar.cuenta_monto', 'cuenta_cobrar.cuenta_descripcion', 'cuenta_cobrar.cuenta_saldo')->distinct('cuenta_cobrar.cuenta_fecha', 'cuenta_cobrar.cuenta_id')->get() as $cuenta) {
                    $banderaMigrada = false;
                    $datos[$count]['nom'] = '';
                    $datos[$count]['doc'] = '';
                    $datos[$count]['num'] = '';
                    $datos[$count]['dia'] = '';
                    if ($cuenta->facturaVenta) {
                        $datos[$count]['doc'] = 'FACTURA';
                        $datos[$count]['num'] = $cuenta->facturaVenta->factura_numero;
                        $datos[$count]['dia'] = $cuenta->facturaVenta->diario->diario_codigo;
                    }
                    if ($cuenta->notaEntrega) {
                        $datos[$count]['doc'] = 'NOTA DE ENTREGA';
                        $datos[$count]['num'] = $cuenta->notaEntrega->nt_numero;
                        if (Auth::user()->empresa->empresa_contabilidad == '1') {
                            $datos[$count]['dia'] = $cuenta->notaEntrega->diario->diario_codigo;
                        } else {
                            $datos[$count]['dia'] = '';
                        }
                    }
                    if ($cuenta->notaDebito) {
                        $datos[$count]['doc'] = 'NOTA DE DÉBITO';
                        $datos[$count]['num'] = $cuenta->notaDebito->nd_numero;
                        $datos[$count]['dia'] = $cuenta->notaDebito->diario->diario_codigo;
                    }
                    if ($datos[$count]['doc'] == '') {
                        $datos[$count]['doc'] = 'FACTURA';
                        $datos[$count]['num'] = substr($cuenta->cuenta_descripcion, 38);
                        $datos[$count]['dia'] = '';
                        $banderaMigrada = true;
                    }
                    $datos[$count]['fec'] = $cuenta->cuenta_fecha;
                    $datos[$count]['mon'] = $cuenta->cuenta_monto;
                    if ($banderaMigrada) {
                        $datos[$count]['sal'] = $cuenta->cuenta_saldo;
                    } else {
                        $datos[$count]['sal'] = $cuenta->cuenta_monto;
                    }
                    $datos[$count]['pag'] = 0;
                    $datos[$count]['fep'] = '';
                    $datos[$count]['tip'] = '';
                    $datos[$count]['tot'] = '2';
                    $count ++;
                    $countCuenta = $count - 1;
                    foreach (Detalle_Pago_CXC::CuentaCobrarPagosFecha($cuenta->cuenta_id, $request->get('fecha_desde'), $request->get('fecha_hasta'), $todo)->orderBy('pago_fecha')->get() as $pago) {
                        $datos[$count]['nom'] = '';
                        $datos[$count]['doc'] = '';
                        $datos[$count]['num'] = '';
                        $datos[$count]['fec'] = '';
                        $datos[$count]['mon'] = '';
                        $datos[$count]['sal'] = '';
                        $datos[$count]['pag'] = $pago->detalle_pago_valor;
                        $datos[$count]['fep'] = $pago->pagoCXC->pago_fecha;
                        if (Auth::user()->empresa->empresa_contabilidad == '1') {
                            $datos[$count]['dia'] = $pago->pagoCXC->diario->diario_codigo;
                        } else {
                            $datos[$count]['dia'] = '';
                        }
                        $datos[$count]['tip'] = $pago->detalle_pago_descripcion;
                        $datos[$count]['tot'] = '3';
                        if (!$banderaMigrada) {
                            $datos[$countCuenta]['sal'] = floatval($datos[$countCuenta]['sal']) - floatval($pago->detalle_pago_valor);
                        } else {
                            $datos[$countCuenta]['sal'] = floatval($datos[$countCuenta]['sal']);
                        }
                        $datos[$countCuenta]['pag'] = floatval($datos[$countCuenta]['pag']) + floatval($datos[$count]['pag']);
                        $count ++;
                    }
                    if ($cuenta->facturaVenta) {
                        foreach (Descuento_Anticipo_Cliente::DescuentosAnticipoByFactura($cuenta->facturaVenta->factura_id)->orderBy('descuento_fecha')->get() as $pago) {
                            $datos[$count]['nom'] = '';
                            $datos[$count]['doc'] = '';
                            $datos[$count]['num'] = '';
                            $datos[$count]['fec'] = '';
                            $datos[$count]['mon'] = '';
                            $datos[$count]['sal'] = '';
                            $datos[$count]['pag'] = $pago->descuento_valor;
                            $datos[$count]['fep'] = $pago->descuento_fecha;
                            if (Auth::user()->empresa->empresa_contabilidad == '1') {
                                $datos[$count]['dia'] = $pago->diario->diario_codigo;
                            } else {
                                $datos[$count]['dia'] = '';
                            }
                            $datos[$count]['tip'] = 'DESCUENTO DE ANTICIPO DE CLIENTE';
                            $datos[$count]['tot'] = '3';
                            if (!$banderaMigrada) {
                                $datos[$countCuenta]['sal'] = floatval($datos[$countCuenta]['sal']) - floatval($pago->descuento_valor);
                            } else {
                                $datos[$countCuenta]['sal'] = floatval($datos[$countCuenta]['sal']);
                            }
                            $datos[$countCuenta]['pag'] = floatval($datos[$countCuenta]['pag']) + floatval($datos[$count]['pag']);
                            $count ++;
                        }
                    }
                    if ($banderaMigrada) {
                        foreach (Descuento_Anticipo_Cliente::DescuentosAnticipoByCXCCorte(substr($cuenta->cuenta_descripcion, 38), $request->get('fecha_corte'))->orderBy('descuento_fecha')->get() as $pago) {
                            $datos[$count]['nom'] = '';
                            $datos[$count]['doc'] = '';
                            $datos[$count]['num'] = '';
                            $datos[$count]['fec'] = '';
                            $datos[$count]['mon'] = '';
                            $datos[$count]['sal'] = '';
                            $datos[$count]['pag'] = $pago->descuento_valor;
                            $datos[$count]['fep'] = $pago->descuento_fecha;
                            if (Auth::user()->empresa->empresa_contabilidad == '1') {
                                $datos[$count]['dia'] = $pago->diario->diario_codigo;
                            } else {
                                $datos[$count]['dia'] = '';
                            }
                            $datos[$count]['tip'] = 'DESCUENTO DE ANTICIPO DE CLIENTE';
                            $datos[$count]['tot'] = '3';

                            $datos[$countCuenta]['sal'] = floatval($datos[$countCuenta]['sal'])- floatval($pago->descuento_valor);
                            $datos[$countCuenta]['pag'] = floatval($datos[$countCuenta]['pag']) + floatval($datos[$count]['pag']);
                            $count ++;
                        }
                    }
                    $datos[$countCliente]['mon'] = floatval($datos[$countCliente]['mon']) + floatval($datos[$countCuenta]['mon']);
                    $datos[$countCliente]['sal'] = floatval($datos[$countCliente]['sal']) + floatval($datos[$countCuenta]['sal']);
                    $datos[$countCliente]['pag'] = floatval($datos[$countCliente]['pag']) + floatval($datos[$countCuenta]['pag']);
                }
                $mon = $mon + floatval($datos[$countCliente]['mon']);
                $sal = $sal + floatval($datos[$countCliente]['sal']);
                $pag = $pag + floatval($datos[$countCliente]['pag']);
                if ($datos[$count-1]['tot'] == '1') {
                    array_pop($datos);
                    $count = $count - 1;
                }
            } */

            $datos=DB::select(DB::raw("
                select distinct
                    cuenta_cobrar.cuenta_id,
                    factura_venta.factura_numero,
                    d2.diario_codigo as diario_factura,
                    retencion_venta.retencion_numero,
                    nota_entrega.nt_numero,
                    nota_debito.nd_numero,
                    descuento_anticipo_cliente.descuento_valor,
                    descuento_anticipo_cliente.descuento_fecha,
                    cuenta_cobrar.cuenta_fecha,
                    cuenta_cobrar.cuenta_monto,
                    cuenta_cobrar.cuenta_descripcion,
                    cuenta_cobrar.cuenta_saldo,
                    cuenta_cobrar.cuenta_saldom as saldom,
                    cliente.cliente_nombre,
                    detalle_pago_cxc.detalle_pago_valor,
                    pago_cxc.pago_fecha,
                    diario.diario_codigo,
                    detalle_pago_cxc.detalle_pago_descripcion
                from cuenta_cobrar
                    left join factura_venta on factura_venta.cuenta_id=cuenta_cobrar.cuenta_id
                    left join diario as d2 on d2.diario_id=factura_venta.diario_id
                    left join retencion_venta on retencion_venta.factura_id=factura_venta.factura_id
                    left join nota_entrega on nota_entrega.cuenta_id=cuenta_cobrar.cuenta_id
                    left join nota_debito on nota_debito.cuenta_id=cuenta_cobrar.cuenta_id
                    left join descuento_anticipo_cliente on factura_venta.factura_id=descuento_anticipo_cliente.factura_id
                    inner join detalle_pago_cxc on cuenta_cobrar.cuenta_id = detalle_pago_cxc.cuenta_id
                    inner join pago_cxc on detalle_pago_cxc.pago_id = pago_cxc.pago_id 
                    inner join cliente on cliente.cliente_id = cuenta_cobrar.cliente_id
                    inner join diario on diario.diario_id=pago_cxc.diario_id
                    inner join tipo_identificacion on tipo_identificacion.tipo_identificacion_id = cliente.tipo_identificacion_id 
                
                where 
                    tipo_identificacion.empresa_id = ".Auth::user()->empresa_id."
                    $fechas 
                    $cliente 
                order by
                    cliente.cliente_nombre,
                    cuenta_cobrar.cuenta_fecha asc, 
                    cuenta_cobrar.cuenta_id asc,
                    pago_cxc.pago_fecha
            "));

            $saldos=DB::select(DB::raw("
                    select 
                        cliente_nombre, sum(monto) as monto, sum(saldo) as saldo, sum(saldom) as saldom,
                        sum(descuento_valor) as descuento_valor, sum(detalle_pago_valor) as detalle_pago_valor, sum(pago_migrado) as pago_migrado
                    from (
                        select distinct
                            cuenta_cobrar.cuenta_id, cliente.cliente_nombre, cuenta_cobrar.cuenta_monto as monto,
                            cuenta_cobrar.cuenta_saldo as saldo, cuenta_cobrar.cuenta_saldom as saldom,
                            sum(descuento_anticipo_cliente.descuento_valor) as descuento_valor, sum(detalle_pago_cxc.detalle_pago_valor) as detalle_pago_valor,
                            case when cuenta_cobrar.cuenta_saldom>0 then sum(detalle_pago_cxc.detalle_pago_valor) else 0 end as pago_migrado
                        from cuenta_cobrar
                            left join factura_venta on factura_venta.cuenta_id=cuenta_cobrar.cuenta_id
                            left join diario as d2 on d2.diario_id=factura_venta.diario_id
                            left join retencion_venta on retencion_venta.factura_id=factura_venta.factura_id
                            left join nota_entrega on nota_entrega.cuenta_id=cuenta_cobrar.cuenta_id
                            left join nota_debito on nota_debito.cuenta_id=cuenta_cobrar.cuenta_id
                            left join descuento_anticipo_cliente on factura_venta.factura_id=descuento_anticipo_cliente.factura_id
                            inner join detalle_pago_cxc on cuenta_cobrar.cuenta_id = detalle_pago_cxc.cuenta_id
                            inner join pago_cxc on detalle_pago_cxc.pago_id = pago_cxc.pago_id 
                            inner join cliente on cliente.cliente_id = cuenta_cobrar.cliente_id
                            inner join diario on diario.diario_id=pago_cxc.diario_id
                            inner join tipo_identificacion on tipo_identificacion.tipo_identificacion_id = cliente.tipo_identificacion_id
                        
                        where 
                            tipo_identificacion.empresa_id = ".Auth::user()->empresa_id."
                            $fechas
                            $cliente 
                        group by 
                            cuenta_cobrar.cuenta_id, cliente.cliente_nombre, cuenta_cobrar.cuenta_monto,
                            cuenta_cobrar.cuenta_saldo, cuenta_cobrar.cuenta_saldom
                        ) as val
                    group by cliente_nombre
                    order by cliente_nombre
                "));

            $pagos=DB::select(DB::raw("
                    select 
                    cliente_nombre, sum(pagos) as pagos, sum(anticipos) as anticipos
                    from (
                        select distinct
                            cuenta_cobrar.cuenta_id,
                            cliente.cliente_nombre,
                            retencion_venta.retencion_numero,
                            factura_venta.factura_numero,
                            cuenta_cobrar.cuenta_saldo,
                            detalle_pago_cxc.detalle_pago_valor as pagos,
                            descuento_anticipo_cliente.descuento_valor as anticipos,
                            diario.diario_codigo
                        from cuenta_cobrar
                            left join factura_venta on factura_venta.cuenta_id=cuenta_cobrar.cuenta_id
                            left join retencion_venta on retencion_venta.factura_id=factura_venta.factura_id
                            left join nota_entrega on nota_entrega.cuenta_id=cuenta_cobrar.cuenta_id
                            left join nota_debito on nota_debito.cuenta_id=cuenta_cobrar.cuenta_id
                            left join descuento_anticipo_cliente on factura_venta.factura_id=descuento_anticipo_cliente.factura_id
                            inner join detalle_pago_cxc on cuenta_cobrar.cuenta_id = detalle_pago_cxc.cuenta_id
                            inner join pago_cxc on detalle_pago_cxc.pago_id = pago_cxc.pago_id 
                            inner join cliente on cliente.cliente_id = cuenta_cobrar.cliente_id
                            inner join diario on diario.diario_id=pago_cxc.diario_id
                            inner join tipo_identificacion on tipo_identificacion.tipo_identificacion_id = cliente.tipo_identificacion_id 
                        where 
                            tipo_identificacion.empresa_id = ".Auth::user()->empresa_id."
                            $fechas
                            $cliente 
                        order by
                            cliente.cliente_nombre,
                            cuenta_cobrar.cuenta_id asc
                        ) as val
                    group by cliente_nombre
                "));

            $totalF=DB::select(DB::raw("
                select distinct
                    cuenta_cobrar.cuenta_id,
                    cuenta_cobrar.cuenta_monto,
                    cuenta_cobrar.cuenta_saldo,
                    cuenta_cobrar.cuenta_saldom,
                    detalle_pago_cxc.detalle_pago_valor as pagos,
                    descuento_anticipo_cliente.descuento_valor as anticipos
                from cuenta_cobrar
                    left join factura_venta on factura_venta.cuenta_id=cuenta_cobrar.cuenta_id
                    left join diario as d2 on d2.diario_id=factura_venta.diario_id
                    left join retencion_venta on retencion_venta.factura_id=factura_venta.factura_id
                    left join nota_entrega on nota_entrega.cuenta_id=cuenta_cobrar.cuenta_id
                    left join nota_debito on nota_debito.cuenta_id=cuenta_cobrar.cuenta_id
                    left join descuento_anticipo_cliente on factura_venta.factura_id=descuento_anticipo_cliente.factura_id
                    inner join detalle_pago_cxc on cuenta_cobrar.cuenta_id = detalle_pago_cxc.cuenta_id
                    inner join pago_cxc on detalle_pago_cxc.pago_id = pago_cxc.pago_id 
                    inner join cliente on cliente.cliente_id = cuenta_cobrar.cliente_id
                    inner join diario on diario.diario_id=pago_cxc.diario_id
                    inner join tipo_identificacion on tipo_identificacion.tipo_identificacion_id = cliente.tipo_identificacion_id   
                
                where 
                    tipo_identificacion.empresa_id = ".Auth::user()->empresa_id."
                    $fechas
                    $cliente 
                order by
                    cuenta_cobrar.cuenta_id asc
            "));

            $this->fpdf->AddPage();


            $this->fpdf->SetFont('Arial', 'B', 12);
            $this->fpdf->Cell(40, 8, '', 0, 0, 'L');
            $this->fpdf->Cell(20, 8, 'Monto: ', 0, 0, 'L');
            $this->fpdf->SetFont('Arial', '', 12);
            $this->fpdf->Cell(30, 8, number_format($mon, 2), 0, 0, 'R');

            $this->fpdf->Cell(20, 8, '', 0, 0, 'L');
            $this->fpdf->SetFont('Arial', 'B', 12);
            $this->fpdf->Cell(20, 8, 'Saldo: ', 0, 0, 'L');
            $this->fpdf->SetFont('Arial', '', 12);
            $this->fpdf->Cell(30, 8, number_format($sal, 2), 0, 0, 'R');

            $this->fpdf->Cell(20, 8, '', 0, 0, 'L');
            $this->fpdf->SetFont('Arial', 'B', 12);
            $this->fpdf->Cell(20, 8, 'Pagos: ', 0, 0, 'L');
            $this->fpdf->SetFont('Arial', '', 12);
            $this->fpdf->Cell(30, 8, number_format($pag, 2), 0, 1, 'R');
            $this->fpdf->Ln(2);

            $this->fpdf->SetFont('Arial', '', 10);

            if(isset($datos)){
                $anterior_cliente=0;
                $anterior_factura=0;
                $actual_cliente=0;
                $actual_factura=0;

                $cliente=0;
                $factura=0;
                
                $saldo=0;
                $debe=0;
                $haber=0;

                $total_monto=0;
                $total_pago=0;
                $total_saldo=0;

                foreach($datos as $dato){
                    $actual_cliente=$dato->cliente_nombre;
                    $actual_factura=$dato->cuenta_id;

                    if($anterior_cliente!=$actual_cliente) $cliente=1; else $cliente=0;
                    if($anterior_factura!=$actual_factura) $factura=1; else $factura=0;
                    
                    
                    if($cliente==1){
                        $montoCliente=0;
                        $saldoCliente=0;
                        $pagoCliente=0;
                        $pagoMigradoCliente=0;
                        $anticipoCliente=0;

                        $i=array_search($dato->cliente_nombre, array_column($saldos, 'cliente_nombre'));

                        if($i>=0){
                            $montoCliente=$saldos[$i]->monto;
                            $saldoCliente=$saldos[$i]->saldo;
                            $pagoMigradoCliente=$saldos[$i]->pago_migrado;

                            if ($saldos[$i]->saldom>0) $saldoCliente=$saldos[$i]->saldo+$saldos[$i]->saldom-$pagoMigradoCliente;
                        }

                        $i=array_search($dato->cliente_nombre, array_column($pagos, 'cliente_nombre'));

                        if($i>=0){
                            $pagoCliente=$pagos[$i]->pagos? $pagos[$i]->pagos:0;
                            $anticipoCliente=$pagos[$i]->anticipos? $pagos[$i]->anticipos:0;
                        }
                        
                        $this->fpdf->SetFillColor(175, 223, 255);
                        $this->fpdf->SetFont('Arial', 'B', 10);
                        $this->fpdf->Cell(80, 8, utf8_decode($dato->cliente_nombre), 1, 0, 'L', true);
                        $this->fpdf->Cell(25, 8, number_format($montoCliente, 2), 1, 0, 'R', true);
                        $this->fpdf->Cell(25, 8, number_format($saldoCliente, 2), 1, 0, 'R', true);
                        $this->fpdf->Cell(25, 8, number_format($pagoCliente+$anticipoCliente, 2), 1, 0, 'R', true);
                        $this->fpdf->Cell(125, 8, '', 1, 1, 'L', true);

                        $total_monto+=round($montoCliente,2);
                        $total_pago+=round($pagoCliente+$anticipoCliente,2);
                        $total_saldo+=round($saldoCliente,2);
                        
                    }
                    
                    if($factura==1){
                        $migrada=false;


                        $this->fpdf->SetFont('Arial', '', 7);

                        if($dato->factura_numero){
                            $this->fpdf->Cell(30, 8, 'FACTURA', 1, 0, 'L');
                            $this->fpdf->Cell(30, 8, $dato->factura_numero, 1, 0, 'R');
                        }
                        else if($dato->nt_numero){
                            $this->fpdf->Cell(30, 8, 'NOTA DE ENTREGA', 1, 0, 'L');
                            $this->fpdf->Cell(30, 8, $dato->nt_numero, 1, 0, 'R');
                        }
                        else if($dato->nd_numero){
                            $this->fpdf->Cell(30, 8, 'NOTA DE DEBITO', 1, 0, 'L');
                            $this->fpdf->Cell(30, 8, $dato->nd_numero, 1, 0, 'R');
                        }
                        else{
                            $this->fpdf->Cell(30, 8, 'FACTURA', 1, 0, 'L');
                            $this->fpdf->Cell(30, 8, substr($dato->cuenta_descripcion, 38), 1, 0, 'R');
                        }

                        $pagoFac=0;
                        $montoFac=0;
                        $saldoFac=0;
                        $encontre=false;

                        foreach($totalF as $f){
                            if($f->cuenta_id==$dato->cuenta_id){
                                $encontre=true;
                                $montoFac=$f->cuenta_monto;
                                $saldoFac=$f->cuenta_saldo;
                                $pagoFac+= $f->pagos+$f->anticipos;
                                if($f->cuenta_saldom>0 && $migrada) $saldoFac=$f->cuenta_saldom-$pagoFac;
                            }
                            else{
                                if($encontre) break;
                            }
                        }

                        $this->fpdf->Cell(20, 8, $dato->cuenta_fecha, 1, 0, 'R');
                        $this->fpdf->Cell(25, 8, number_format($montoFac,2), 1, 0, 'R');
                        $this->fpdf->Cell(25, 8, number_format($saldoFac,2), 1, 0, 'R');
                        $this->fpdf->Cell(25, 8, number_format($pagoFac,2), 1, 0, 'R');
                        $this->fpdf->Cell(20, 8, '', 1, 0, 'C');


                        if (Auth::user()->empresa->empresa_llevaContabilidad == '1') {
                            $this->fpdf->Cell(30, 8, $dato->diario_factura, 1, 0, 'C');
                        } else {
                            $this->fpdf->Cell(30, 8, '', 1, 0, 'C');
                        }

                        if (str_starts_with($dato->cuenta_descripcion, 'PAGO EN DEPOSITO DE CHEQUE')) 
                            $this->fpdf->Cell(75, 8, utf8_decode('PAG DEP CH '.substr($dato->cuenta_descripcion, 26)), 1, 1, 'L');
                        else if (str_starts_with($dato->cuenta_descripcion, 'PAGO EN TRANSFERENCIA')) 
                            $this->fpdf->Cell(75, 8, utf8_decode('PAG EN TRANSF. '.substr($dato->cuenta_descripcion, 21)), 1, 1, 'L');
                        else
                            $this->fpdf->Cell(75, 8, utf8_decode($dato->cuenta_descripcion), 1, 1, 'L');
                        
                    }

                    if(!$dato->retencion_numero){
                        $this->fpdf->SetFont('Arial', '', 7);
                        $this->fpdf->Cell(30, 8, '', 1, 0, 'L');
                        $this->fpdf->Cell(30, 8, '', 1, 0, 'R');
                        $this->fpdf->Cell(20, 8,'', 1, 0, 'R');
                        $this->fpdf->Cell(25, 8, '', 1, 0, 'R');
                        $this->fpdf->Cell(25, 8, '', 1, 0, 'R');
                        $this->fpdf->Cell(25, 8, number_format($dato->detalle_pago_valor,2), 1, 0, 'R');
                        $this->fpdf->Cell(20, 8, $dato->pago_fecha, 1, 0, 'C');


                        if (Auth::user()->empresa->empresa_llevaContabilidad == '1')
                            $this->fpdf->Cell(30, 8, $dato->diario_codigo, 1, 0, 'C');
                        else
                            $this->fpdf->Cell(30, 8, '', 1, 0, 'C');
                        

                        if (str_starts_with($dato->detalle_pago_descripcion, 'PAGO EN DEPOSITO DE CHEQUE'))
                            $this->fpdf->Cell(75, 8, utf8_decode('PAG DEP CH '.substr($dato->detalle_pago_descripcion, 26)), 1, 1, 'L');
                        else if (str_starts_with($dato->detalle_pago_descripcion, 'PAGO EN TRANSFERENCIA')) 
                            $this->fpdf->Cell(75, 8, utf8_decode('PAG EN TRANSF. '.substr($dato->detalle_pago_descripcion, 21)), 1, 1, 'L');
                        else
                            $this->fpdf->Cell(75, 8, utf8_decode($dato->detalle_pago_descripcion), 1, 1, 'L');
                        
                        //$this->fpdf->Cell(70, 8, utf8_decode($dato->detalle_pago_descripcion), 1, 1, 'L');
                    }

                    if($dato->retencion_numero){
                        $this->fpdf->SetFont('Arial', '', 7);
                        $this->fpdf->Cell(30, 8, '', 1, 0, 'L');
                        $this->fpdf->Cell(30, 8, '', 1, 0, 'R');
                        $this->fpdf->Cell(20, 8, '', 1, 0, 'R');
                        $this->fpdf->Cell(25, 8, '', 1, 0, 'R');
                        $this->fpdf->Cell(25, 8, '', 1, 0, 'R');
                        $this->fpdf->Cell(25, 8, number_format($dato->detalle_pago_valor,2), 1, 0, 'R');
                        $this->fpdf->Cell(20, 8, $dato->pago_fecha, 1, 0, 'C');


                        if (Auth::user()->empresa->empresa_llevaContabilidad == '1') {
                            $this->fpdf->Cell(30, 8, $dato->diario_codigo, 1, 0, 'C');
                        } else {
                            $this->fpdf->Cell(30, 8, '', 1, 0, 'C');
                        }

                        if (str_starts_with($dato->detalle_pago_descripcion, 'PAGO EN DEPOSITO DE CHEQUE'))
                            $this->fpdf->Cell(75, 8, utf8_decode('PAG DEP CH '.substr($dato->detalle_pago_descripcion, 26)), 1, 1, 'L');
                        else if (str_starts_with($dato->detalle_pago_descripcion, 'PAGO EN TRANSFERENCIA')) 
                            $this->fpdf->Cell(75, 8, utf8_decode('PAG EN TRANSF. '.substr($dato->detalle_pago_descripcion, 21)), 1, 1, 'L');
                        else
                            $this->fpdf->Cell(75, 8, utf8_decode($dato->detalle_pago_descripcion), 1, 1, 'L');
                    }

                    if($dato->descuento_valor){
                        $this->fpdf->SetFont('Arial', '', 7);
                        $this->fpdf->Cell(30, 8, '', 1, 0, 'L');
                        $this->fpdf->Cell(30, 8, '', 1, 0, 'R');
                        $this->fpdf->Cell(20, 8, '', 1, 0, 'R');
                        $this->fpdf->Cell(25, 8, '', 1, 0, 'R');
                        $this->fpdf->Cell(25, 8, '', 1, 0, 'R');
                        $this->fpdf->Cell(25, 8, number_format($dato->descuento_valor,2), 1, 0, 'R');
                        $this->fpdf->Cell(20, 8, $dato->descuento_fecha, 1, 0, 'C');


                        if (Auth::user()->empresa->empresa_llevaContabilidad == '1') {
                            $this->fpdf->Cell(30, 8, $dato->diario_codigo, 1, 0, 'C');
                        } else {
                            $this->fpdf->Cell(30, 8, '', 1, 0, 'C');
                        }

                        $this->fpdf->Cell(75, 8, 'DESCUENTO DE ANTICIPO DE CLIENTE', 1, 1, 'L');
                    }
                    
                    $anterior_cliente=$actual_cliente;
                    $anterior_factura=$actual_factura;
                    
                }

                $this->fpdf->Output();
                exit;
            }



            /* if (isset($datos)) {
                for ($i = 1; $i <= count($datos); ++$i) {
                    if ($datos[$i]['tot'] == '1') {
                        $this->fpdf->SetFillColor(175, 223, 255);
                        $this->fpdf->SetFont('Arial', 'B', 10);
                        $this->fpdf->Cell(80, 8, $datos[$i]['nom'], 1, 0, 'L', true);
                        $this->fpdf->Cell(25, 8, number_format($datos[$i]['mon'], 2), 1, 0, 'R', true);
                        $this->fpdf->Cell(25, 8, number_format($datos[$i]['sal'], 2), 1, 0, 'R', true);
                        $this->fpdf->Cell(25, 8, number_format($datos[$i]['pag'], 2), 1, 0, 'R', true);
                        $this->fpdf->Cell(120, 8, '', 1, 1, 'L', true);
                    }

                    if ($datos[$i]['tot'] == '2') {
                        $this->fpdf->SetFont('Arial', '', 7);
                        $this->fpdf->Cell(30, 8, $datos[$i]['doc'], 1, 0, 'L');
                        $this->fpdf->Cell(30, 8, $datos[$i]['num'], 1, 0, 'R');
                        $this->fpdf->Cell(20, 8, $datos[$i]['fec'], 1, 0, 'R');

                        $this->fpdf->Cell(25, 8, number_format($datos[$i]['mon'], 2), 1, 0, 'R');
                        $this->fpdf->Cell(25, 8, number_format($datos[$i]['sal'], 2), 1, 0, 'R');
                        $this->fpdf->Cell(25, 8, number_format($datos[$i]['pag'], 2), 1, 0, 'R');
                        $this->fpdf->Cell(20, 8, $datos[$i]['fep'], 1, 0, 'C');

                        if (Auth::user()->empresa->empresa_llevaContabilidad == '1') {
                            $this->fpdf->Cell(30, 8, $datos[$i]['dia'], 1, 0, 'C');
                        } else {
                            $this->fpdf->Cell(30, 8, '', 1, 0, 'C');
                        }

                        if (str_starts_with($datos[$i]['tip'], 'PAGO EN DEPOSITO DE CHEQUE')) {
                            $this->fpdf->Cell(70, 8, utf8_decode('PAG DEP CH '.substr($datos[$i]['tip'], 26)), 1, 1, 'L');
                        } else {
                            $this->fpdf->Cell(70, 8, utf8_decode($datos[$i]['tip']), 1, 1, 'L');
                        }
                    }

                    if ($datos[$i]['tot'] == '3') {
                        $this->fpdf->SetFillColor(211, 211, 211);
                        $this->fpdf->SetFont('Arial', 'B', 7);
                        $this->fpdf->Cell(130, 8, '', 1, 0, 'L', true);
                        $this->fpdf->Cell(25, 8, number_format($datos[$i]['pag'], 2), 1, 0, 'R', true);
                        $this->fpdf->Cell(20, 8, $datos[$i]['fep'], 1, 0, 'C', true);

                        if (Auth::user()->empresa->empresa_llevaContabilidad == '1') {
                            $this->fpdf->Cell(30, 8, $datos[$i]['dia'], 1, 0, 'C', true);
                        } else {
                            $this->fpdf->Cell(30, 8, '', 1, 0, 'C');
                        }

                        if (str_starts_with($datos[$i]['tip'], 'PAGO EN DEPOSITO DE CHEQUE')) {
                            $this->fpdf->Cell(70, 8, utf8_decode('PAG DEP CH '.substr($datos[$i]['tip'], 26)), 1, 1, 'L', true);
                        } else {
                            $this->fpdf->Cell(70, 8, utf8_decode($datos[$i]['tip']), 1, 1, 'L', true);
                        }
                    }
                }

                $this->fpdf->Output();
                exit;
            } */

            return 1;

            return view('admin.cuentasCobrar.estadoCuenta.index', ['cuentas'=>Cuenta::Cuentas()->orderBy('cuenta_numero', 'asc')->get(),'tab'=>'1','mon'=>$mon,'sal'=>$sal,'pag'=>$pag,'fecC'=>$request->get('fecha_corte'),'tipo'=>$request->get('tipoConsulta'),'sucurslaC'=>$request->get('sucursal_id'),'sucursales'=>Sucursal::sucursales()->get(),'clienteC'=>$request->get('clienteID'),'fecI'=>$request->get('fecha_desde'),'fecF'=>$request->get('fecha_hasta'),'todo'=>$todo,'datos'=>$datos,'clientes'=>Cliente::clientes()->get(),'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        //} catch(\Exception $ex) {
        //    return redirect('cxc')->with('error2', 'Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        //}
    }

    public function excelPagos(Request $request){
        ini_set('max_execution_time', 1200);
        $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono', 'grupo_orden', 'grupo_orden')->join('rol_permiso', 'usuario_rol.rol_id', '=', 'rol_permiso.rol_id')->join('permiso', 'permiso.permiso_id', '=', 'rol_permiso.permiso_id')->join('grupo_permiso', 'grupo_permiso.grupo_id', '=', 'permiso.grupo_id')->join('tipo_grupo', 'tipo_grupo.grupo_id', '=', 'grupo_permiso.grupo_id')->where('permiso_estado', '=', '1')->where('usuario_rol.user_id', '=', Auth::user()->user_id)->orderBy('grupo_orden', 'asc')->distinct()->get();
        $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id', 'tipo_grupo.tipo_id', 'tipo_nombre', 'tipo_icono', 'tipo_orden')->join('rol_permiso', 'usuario_rol.rol_id', '=', 'rol_permiso.rol_id')->join('permiso', 'permiso.permiso_id', '=', 'rol_permiso.permiso_id')->join('tipo_grupo', 'tipo_grupo.tipo_id', '=', 'permiso.tipo_id')->where('permiso_estado', '=', '1')->where('usuario_rol.user_id', '=', Auth::user()->user_id)->orderBy('tipo_orden', 'asc')->distinct()->get();
        $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso', 'usuario_rol.rol_id', '=', 'rol_permiso.rol_id')->join('permiso', 'permiso.permiso_id', '=', 'rol_permiso.permiso_id')->where('permiso_estado', '=', '1')->where('usuario_rol.user_id', '=', Auth::user()->user_id)->orderBy('permiso_orden', 'asc')->get();
        $count = 1;
        $countCliente = 0;
        $countCuenta = 0;
        $datos = null;
        $todo = 0;
        $mon = 0;
        $sal = 0;
        $pag = 0;
        /* if ($request->get('fecha_todo') == "on") {
            $todo = 1;
        }
        if ($request->get('clienteID') == "0") {
            $clientes = Cliente::clientes()->get();
        } else {
            $clientes = Cliente::cliente($request->get('clienteID'))->get();
        }
        foreach ($clientes as $cliente) {
            $datos[$count]['nom'] = $cliente->cliente_nombre;
            $datos[$count]['doc'] = '';
            $datos[$count]['num'] = '';
            $datos[$count]['fec'] = '';
            $datos[$count]['mon'] = 0;
            $datos[$count]['sal'] = 0;
            $datos[$count]['pag'] = 0;
            $datos[$count]['fep'] = '';
            $datos[$count]['dia'] = '';
            $datos[$count]['tip'] = '';
            $datos[$count]['tot'] = '1';
            $count ++;
            $countCliente = $count - 1;
            $banderaMigrada = false;
            foreach (Cuenta_Cobrar::CuentasCobrarByPagos($request->get('fecha_desde'), $request->get('fecha_hasta'), $cliente->cliente_id, $todo, $request->get('sucursal_id'))->select('cuenta_cobrar.cuenta_id', 'cuenta_cobrar.cuenta_fecha', 'cuenta_cobrar.cuenta_monto', 'cuenta_cobrar.cuenta_descripcion', 'cuenta_cobrar.cuenta_saldo')->distinct('cuenta_cobrar.cuenta_fecha', 'cuenta_cobrar.cuenta_id')->get() as $cuenta) {
                $banderaMigrada = false;
                $datos[$count]['nom'] = '';
                $datos[$count]['doc'] = '';
                $datos[$count]['num'] = '';
                $datos[$count]['dia'] = '';
                if ($cuenta->facturaVenta) {
                    $datos[$count]['doc'] = 'FACTURA';
                    $datos[$count]['num'] = $cuenta->facturaVenta->factura_numero;
                    $datos[$count]['dia'] = $cuenta->facturaVenta->diario->diario_codigo;
                }
                if ($cuenta->notaEntrega) {
                    $datos[$count]['doc'] = 'NOTA DE ENTREGA';
                    $datos[$count]['num'] = $cuenta->notaEntrega->nt_numero;
                    if (Auth::user()->empresa->empresa_contabilidad == '1') {
                        $datos[$count]['dia'] = $cuenta->notaEntrega->diario->diario_codigo;
                    } else {
                        $datos[$count]['dia'] = '';
                    }
                }
                if ($cuenta->notaDebito) {
                    $datos[$count]['doc'] = 'NOTA DE DÉBITO';
                    $datos[$count]['num'] = $cuenta->notaDebito->nd_numero;
                    $datos[$count]['dia'] = $cuenta->notaDebito->diario->diario_codigo;
                }
                if ($datos[$count]['doc'] == '') {
                    $datos[$count]['doc'] = 'FACTURA';
                    $datos[$count]['num'] = substr($cuenta->cuenta_descripcion, 38);
                    $datos[$count]['dia'] = '';
                    $banderaMigrada = true;
                }
                $datos[$count]['fec'] = $cuenta->cuenta_fecha;
                $datos[$count]['mon'] = $cuenta->cuenta_monto;
                if ($banderaMigrada) {
                    $datos[$count]['sal'] = $cuenta->cuenta_saldo;
                } else {
                    $datos[$count]['sal'] = $cuenta->cuenta_monto;
                }
                $datos[$count]['pag'] = 0;
                $datos[$count]['fep'] = '';
                $datos[$count]['tip'] = '';
                $datos[$count]['tot'] = '2';
                $count ++;
                $countCuenta = $count - 1;
                foreach (Detalle_Pago_CXC::CuentaCobrarPagosFecha($cuenta->cuenta_id, $request->get('fecha_desde'), $request->get('fecha_hasta'), $todo)->orderBy('pago_fecha')->get() as $pago) {
                    $datos[$count]['nom'] = '';
                    $datos[$count]['doc'] = '';
                    $datos[$count]['num'] = '';
                    $datos[$count]['fec'] = '';
                    $datos[$count]['mon'] = '';
                    $datos[$count]['sal'] = '';
                    $datos[$count]['pag'] = $pago->detalle_pago_valor;
                    $datos[$count]['fep'] = $pago->pagoCXC->pago_fecha;
                    if (Auth::user()->empresa->empresa_contabilidad == '1') {
                        $datos[$count]['dia'] = $pago->pagoCXC->diario->diario_codigo;
                    } else {
                        $datos[$count]['dia'] = '';
                    }
                    $datos[$count]['tip'] = $pago->detalle_pago_descripcion;
                    $datos[$count]['tot'] = '3';
                    if (!$banderaMigrada) {
                        $datos[$countCuenta]['sal'] = floatval($datos[$countCuenta]['sal']) - floatval($pago->detalle_pago_valor);
                    } else {
                        $datos[$countCuenta]['sal'] = floatval($datos[$countCuenta]['sal']);
                    }
                    $datos[$countCuenta]['pag'] = floatval($datos[$countCuenta]['pag']) + floatval($datos[$count]['pag']);
                    $count ++;
                }
                if ($cuenta->facturaVenta) {
                    foreach (Descuento_Anticipo_Cliente::DescuentosAnticipoByFactura($cuenta->facturaVenta->factura_id)->orderBy('descuento_fecha')->get() as $pago) {
                        $datos[$count]['nom'] = '';
                        $datos[$count]['doc'] = '';
                        $datos[$count]['num'] = '';
                        $datos[$count]['fec'] = '';
                        $datos[$count]['mon'] = '';
                        $datos[$count]['sal'] = '';
                        $datos[$count]['pag'] = $pago->descuento_valor;
                        $datos[$count]['fep'] = $pago->descuento_fecha;
                        if (Auth::user()->empresa->empresa_contabilidad == '1') {
                            $datos[$count]['dia'] = $pago->diario->diario_codigo;
                        } else {
                            $datos[$count]['dia'] = '';
                        }
                        $datos[$count]['tip'] = 'DESCUENTO DE ANTICIPO DE CLIENTE';
                        $datos[$count]['tot'] = '3';
                        if (!$banderaMigrada) {
                            $datos[$countCuenta]['sal'] = floatval($datos[$countCuenta]['sal']) - floatval($pago->descuento_valor);
                        } else {
                            $datos[$countCuenta]['sal'] = floatval($datos[$countCuenta]['sal']);
                        }
                        $datos[$countCuenta]['pag'] = floatval($datos[$countCuenta]['pag']) + floatval($datos[$count]['pag']);
                        $count ++;
                    }
                }
                if ($banderaMigrada) {
                    foreach (Descuento_Anticipo_Cliente::DescuentosAnticipoByCXCCorte(substr($cuenta->cuenta_descripcion, 38), $request->get('fecha_corte'))->orderBy('descuento_fecha')->get() as $pago) {
                        $datos[$count]['nom'] = '';
                        $datos[$count]['doc'] = '';
                        $datos[$count]['num'] = '';
                        $datos[$count]['fec'] = '';
                        $datos[$count]['mon'] = '';
                        $datos[$count]['sal'] = '';
                        $datos[$count]['pag'] = $pago->descuento_valor;
                        $datos[$count]['fep'] = $pago->descuento_fecha;
                        if (Auth::user()->empresa->empresa_contabilidad == '1') {
                            $datos[$count]['dia'] = $pago->diario->diario_codigo;
                        } else {
                            $datos[$count]['dia'] = '';
                        }
                        $datos[$count]['tip'] = 'DESCUENTO DE ANTICIPO DE CLIENTE';
                        $datos[$count]['tot'] = '3';

                        $datos[$countCuenta]['sal'] = floatval($datos[$countCuenta]['sal'])- floatval($pago->descuento_valor);
                        $datos[$countCuenta]['pag'] = floatval($datos[$countCuenta]['pag']) + floatval($datos[$count]['pag']);
                        $count ++;
                    }
                }
                $datos[$countCliente]['mon'] = floatval($datos[$countCliente]['mon']) + floatval($datos[$countCuenta]['mon']);
                $datos[$countCliente]['sal'] = floatval($datos[$countCliente]['sal']) + floatval($datos[$countCuenta]['sal']);
                $datos[$countCliente]['pag'] = floatval($datos[$countCliente]['pag']) + floatval($datos[$countCuenta]['pag']);
            }
            $mon = $mon + floatval($datos[$countCliente]['mon']);
            $sal = $sal + floatval($datos[$countCliente]['sal']);
            $pag = $pag + floatval($datos[$countCliente]['pag']);
            if ($datos[$count-1]['tot'] == '1') {
                array_pop($datos);
                $count = $count - 1;
            }
        } */

        $fechas="";
        $cliente="";
        $fechas= "and pago_fecha >= '$request->fecha_desde' and pago_fecha <= '$request->fecha_hasta'";

        if ($request->clienteID>0) {
            $cliente="and cliente.cliente_id=$request->clienteID";
        }

        $datos=DB::select(DB::raw("
                select distinct
                    cuenta_cobrar.cuenta_id,
                    factura_venta.factura_numero,
                    d2.diario_codigo as diario_factura,
                    retencion_venta.retencion_numero,
                    nota_entrega.nt_numero,
                    nota_debito.nd_numero,
                    descuento_anticipo_cliente.descuento_valor,
                    descuento_anticipo_cliente.descuento_fecha,
                    cuenta_cobrar.cuenta_fecha,
                    cuenta_cobrar.cuenta_monto,
                    cuenta_cobrar.cuenta_descripcion,
                    cuenta_cobrar.cuenta_saldo,
                    cuenta_cobrar.cuenta_saldom as saldom,
                    cliente.cliente_nombre,
                    detalle_pago_cxc.detalle_pago_valor,
                    pago_cxc.pago_fecha,
                    diario.diario_codigo,
                    detalle_pago_cxc.detalle_pago_descripcion
                from cuenta_cobrar
                    left join factura_venta on factura_venta.cuenta_id=cuenta_cobrar.cuenta_id
                    left join diario as d2 on d2.diario_id=factura_venta.diario_id
                    left join retencion_venta on retencion_venta.factura_id=factura_venta.factura_id
                    left join nota_entrega on nota_entrega.cuenta_id=cuenta_cobrar.cuenta_id
                    left join nota_debito on nota_debito.cuenta_id=cuenta_cobrar.cuenta_id
                    left join descuento_anticipo_cliente on factura_venta.factura_id=descuento_anticipo_cliente.factura_id
                    inner join detalle_pago_cxc on cuenta_cobrar.cuenta_id = detalle_pago_cxc.cuenta_id
                    inner join pago_cxc on detalle_pago_cxc.pago_id = pago_cxc.pago_id 
                    inner join cliente on cliente.cliente_id = cuenta_cobrar.cliente_id
                    inner join diario on diario.diario_id=pago_cxc.diario_id
                    inner join tipo_identificacion on tipo_identificacion.tipo_identificacion_id = cliente.tipo_identificacion_id 
                
                where 
                    tipo_identificacion.empresa_id = ".Auth::user()->empresa_id."
                    $fechas 
                    $cliente 
                order by
                    cliente.cliente_nombre,
                    cuenta_cobrar.cuenta_fecha asc, 
                    cuenta_cobrar.cuenta_id asc,
                    pago_cxc.pago_fecha
            "));

        $totales=DB::select(DB::raw("
                select
                    cliente_nombre, sum(monto) as monto, sum(saldo) as saldo, sum(saldom) as saldom,
                    sum(descuento_valor) as descuento_valor, sum(detalle_pago_valor) as detalle_pago_valor, sum(pago_migrado) as pago_migrado
                from (
                    select distinct
                        cuenta_cobrar.cuenta_id, cliente.cliente_nombre, cuenta_cobrar.cuenta_monto as monto,
                        cuenta_cobrar.cuenta_saldo as saldo, cuenta_cobrar.cuenta_saldom as saldom,
                        sum(descuento_anticipo_cliente.descuento_valor) as descuento_valor, sum(detalle_pago_cxc.detalle_pago_valor) as detalle_pago_valor,
                        case when cuenta_cobrar.cuenta_saldom>0 then sum(detalle_pago_cxc.detalle_pago_valor) else 0 end as pago_migrado
                    from cuenta_cobrar
                        left join factura_venta on factura_venta.cuenta_id=cuenta_cobrar.cuenta_id
                        left join diario as d2 on d2.diario_id=factura_venta.diario_id
                        left join retencion_venta on retencion_venta.factura_id=factura_venta.factura_id
                        left join nota_entrega on nota_entrega.cuenta_id=cuenta_cobrar.cuenta_id
                        left join nota_debito on nota_debito.cuenta_id=cuenta_cobrar.cuenta_id
                        left join descuento_anticipo_cliente on factura_venta.factura_id=descuento_anticipo_cliente.factura_id
                        inner join detalle_pago_cxc on cuenta_cobrar.cuenta_id = detalle_pago_cxc.cuenta_id
                        inner join pago_cxc on detalle_pago_cxc.pago_id = pago_cxc.pago_id 
                        inner join cliente on cliente.cliente_id = cuenta_cobrar.cliente_id
                        inner join diario on diario.diario_id=pago_cxc.diario_id
                        inner join tipo_identificacion on tipo_identificacion.tipo_identificacion_id = cliente.tipo_identificacion_id
                    
                    where 
                        tipo_identificacion.empresa_id = ".Auth::user()->empresa_id."
                        $fechas
                        $cliente 
                    group by 
                        cuenta_cobrar.cuenta_id, cliente.cliente_nombre, cuenta_cobrar.cuenta_monto,
                        cuenta_cobrar.cuenta_saldo, cuenta_cobrar.cuenta_saldom
                    ) as val
                group by cliente_nombre
                order by cliente_nombre
            "));

        $pagos=DB::select(DB::raw("
                select 
                cliente_nombre, sum(pagos) as pagos, sum(anticipos) as anticipos
                from (
                    select distinct
                        cuenta_cobrar.cuenta_id,
                        cliente.cliente_nombre,
                        retencion_venta.retencion_numero,
                        factura_venta.factura_numero,
                        cuenta_cobrar.cuenta_saldo,
                        detalle_pago_cxc.detalle_pago_valor as pagos,
                        descuento_anticipo_cliente.descuento_valor as anticipos,
                        diario.diario_codigo
                    from cuenta_cobrar
                        left join factura_venta on factura_venta.cuenta_id=cuenta_cobrar.cuenta_id
                        left join retencion_venta on retencion_venta.factura_id=factura_venta.factura_id
                        left join nota_entrega on nota_entrega.cuenta_id=cuenta_cobrar.cuenta_id
                        left join nota_debito on nota_debito.cuenta_id=cuenta_cobrar.cuenta_id
                        left join descuento_anticipo_cliente on factura_venta.factura_id=descuento_anticipo_cliente.factura_id
                        inner join detalle_pago_cxc on cuenta_cobrar.cuenta_id = detalle_pago_cxc.cuenta_id
                        inner join pago_cxc on detalle_pago_cxc.pago_id = pago_cxc.pago_id 
                        inner join cliente on cliente.cliente_id = cuenta_cobrar.cliente_id
                        inner join diario on diario.diario_id=pago_cxc.diario_id
                        inner join tipo_identificacion on tipo_identificacion.tipo_identificacion_id = cliente.tipo_identificacion_id 
                    where 
                        tipo_identificacion.empresa_id = ".Auth::user()->empresa_id."
                        $fechas
                        $cliente 
                    order by
                        cliente.cliente_nombre,
                        cuenta_cobrar.cuenta_id asc
                    ) as val
                group by cliente_nombre
            "));

        $pagosF=DB::select(DB::raw("
            select distinct
                cuenta_cobrar.cuenta_id,
                cuenta_cobrar.cuenta_monto,
                cuenta_cobrar.cuenta_saldo,
                cuenta_cobrar.cuenta_saldom,
                detalle_pago_cxc.detalle_pago_valor as pagos,
                descuento_anticipo_cliente.descuento_valor as anticipos
            from cuenta_cobrar
                left join factura_venta on factura_venta.cuenta_id=cuenta_cobrar.cuenta_id
                left join diario as d2 on d2.diario_id=factura_venta.diario_id
                left join retencion_venta on retencion_venta.factura_id=factura_venta.factura_id
                left join nota_entrega on nota_entrega.cuenta_id=cuenta_cobrar.cuenta_id
                left join nota_debito on nota_debito.cuenta_id=cuenta_cobrar.cuenta_id
                left join descuento_anticipo_cliente on factura_venta.factura_id=descuento_anticipo_cliente.factura_id
                inner join detalle_pago_cxc on cuenta_cobrar.cuenta_id = detalle_pago_cxc.cuenta_id
                inner join pago_cxc on detalle_pago_cxc.pago_id = pago_cxc.pago_id 
                inner join cliente on cliente.cliente_id = cuenta_cobrar.cliente_id
                inner join diario on diario.diario_id=pago_cxc.diario_id
                inner join tipo_identificacion on tipo_identificacion.tipo_identificacion_id = cliente.tipo_identificacion_id   
            
            where 
                tipo_identificacion.empresa_id = ".Auth::user()->empresa_id."
                $fechas
                $cliente 
            order by
                cuenta_cobrar.cuenta_id asc
        "));



        return Excel::download(new ViewExcel('admin.formatosExcel.estadoCuentaCXC2', [
            'datos'=>$datos,
            'saldos'=>$totales,
            'pagos'=>$pagos,
            'totalF'=>$pagosF
        ]), 
        'NEOPAGUPA  Sistema Contable.xlsx');
    }

    public function pendientesPago(Request $request){
        ini_set('max_execution_time', 1200);
        //return $request;

        try {
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono', 'grupo_orden', 'grupo_orden')->join('rol_permiso', 'usuario_rol.rol_id', '=', 'rol_permiso.rol_id')->join('permiso', 'permiso.permiso_id', '=', 'rol_permiso.permiso_id')->join('grupo_permiso', 'grupo_permiso.grupo_id', '=', 'permiso.grupo_id')->join('tipo_grupo', 'tipo_grupo.grupo_id', '=', 'grupo_permiso.grupo_id')->where('permiso_estado', '=', '1')->where('usuario_rol.user_id', '=', Auth::user()->user_id)->orderBy('grupo_orden', 'asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id', 'tipo_grupo.tipo_id', 'tipo_nombre', 'tipo_icono', 'tipo_orden')->join('rol_permiso', 'usuario_rol.rol_id', '=', 'rol_permiso.rol_id')->join('permiso', 'permiso.permiso_id', '=', 'rol_permiso.permiso_id')->join('tipo_grupo', 'tipo_grupo.tipo_id', '=', 'permiso.tipo_id')->where('permiso_estado', '=', '1')->where('usuario_rol.user_id', '=', Auth::user()->user_id)->orderBy('tipo_orden', 'asc')->distinct()->get();
            $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso', 'usuario_rol.rol_id', '=', 'rol_permiso.rol_id')->join('permiso', 'permiso.permiso_id', '=', 'rol_permiso.permiso_id')->where('permiso_estado', '=', '1')->where('usuario_rol.user_id', '=', Auth::user()->user_id)->orderBy('permiso_orden', 'asc')->get();
            $count = 1;
            $countCliente = 0;
            $countCuenta = 0;
            $datos = null;
            $todo = 0;
            $mon = 0;
            $sal = 0;
            $pag = 0;
            if ($request->get('fecha_todo') == "on") {
                $todo = 1;
            }
            if ($request->get('clienteID') == "0") {
                $clientes = Cliente::clientes()->get();
            } else {
                $clientes = Cliente::cliente($request->get('clienteID'))->get();
            }

            foreach ($clientes as $cliente) {
                $datos[$count]['nom'] = $cliente->cliente_nombre;
                $datos[$count]['doc'] = '';
                $datos[$count]['num'] = '';
                $datos[$count]['fec'] = '';
                $datos[$count]['mon'] = 0;
                $datos[$count]['sal'] = 0;
                $datos[$count]['pag'] = 0;
                $datos[$count]['fep'] = '';
                $datos[$count]['dia'] = '';
                $datos[$count]['tip'] = '';
                $datos[$count]['tot'] = '1';
                $count ++;
                $countCliente = $count - 1;
                $banderaMigrada = false;

                $montoEliminado=0;
                $saldoEliminado=0;
                $pagoEliminado=0;

                foreach (Cuenta_Cobrar::CuentasCobrarPendientes($request->get('fecha_corte'), $cliente->cliente_id, $request->get('sucursal_id'))->select('cuenta_cobrar.cuenta_id', 'cuenta_cobrar.cuenta_fecha', 'cuenta_cobrar.cuenta_monto', 'cuenta_cobrar.cuenta_saldom', 'cuenta_cobrar.cuenta_descripcion', 'cuenta_cobrar.cuenta_saldom')->having('cuenta_monto', '>', DB::raw("(SELECT sum(detalle_pago_valor) FROM detalle_pago_cxc inner join pago_cxc on pago_cxc.pago_id = detalle_pago_cxc.pago_id WHERE pago_fecha <= '".$request->get('fecha_corte')."' and detalle_pago_cxc.cuenta_id = cuenta_cobrar.cuenta_id)"))->orhavingRaw("(SELECT sum(detalle_pago_valor) FROM detalle_pago_cxc inner join pago_cxc on pago_cxc.pago_id = detalle_pago_cxc.pago_id WHERE pago_fecha <= '".$request->get('fecha_corte')."' and detalle_pago_cxc.cuenta_id = cuenta_cobrar.cuenta_id) is null")->groupBy('cuenta_cobrar.cuenta_id', 'cuenta_cobrar.cuenta_fecha', 'cuenta_cobrar.cuenta_monto')->get() as $cuenta) {
                    $banderaMigrada = false;
                    $datos[$count]['nom'] = '';
                    $datos[$count]['doc'] = '';
                    $datos[$count]['num'] = '';
                    $datos[$count]['dia'] = '';
                    if ($cuenta->facturaVenta) {
                        $datos[$count]['doc'] = 'FACTURA';
                        $datos[$count]['num'] = $cuenta->facturaVenta->factura_numero;
                        $datos[$count]['dia'] = $cuenta->facturaVenta->diario->diario_codigo;
                    }
                    if ($cuenta->notaEntrega) {
                        $datos[$count]['doc'] = 'NOTA DE ENTREGA';
                        $datos[$count]['num'] = $cuenta->notaEntrega->nt_numero;
                        if (Auth::user()->empresa->empresa_contabilidad == '1') {
                            $datos[$count]['dia'] = $cuenta->notaEntrega->diario->diario_codigo;
                        } else {
                            $datos[$count]['dia'] = '';
                        }
                    }
                    if ($cuenta->notaDebito) {
                        $datos[$count]['doc'] = 'NOTA DE DÉBITO';
                        $datos[$count]['num'] = $cuenta->notaDebito->nd_numero;
                        $datos[$count]['dia'] = $cuenta->notaDebito->diario->diario_codigo;
                    }
                    if ($datos[$count]['doc'] == '') {
                        $datos[$count]['num'] = substr($cuenta->cuenta_descripcion, 38);
                        $datos[$count]['doc'] = 'FACTURA';
                        $datos[$count]['dia'] = '';
                        $banderaMigrada = true;
                    }
                    $datos[$count]['fec'] = date("Y-m-d", strtotime($cuenta->cuenta_fecha));
                    $datos[$count]['mon'] = $cuenta->cuenta_monto;
                    if ($banderaMigrada) {
                        $datos[$count]['sal'] = $cuenta->cuenta_saldom;
                        /*
                        if($cuenta->cuenta_saldom>0) $datos[$count]['mon'] = $cuenta->cuenta_saldom;
                        $datos[$count]['sal'] = $cuenta->cuenta_saldo+Detalle_Pago_CXC::cuentaCobrarPagosAfterCorte($cuenta->cuenta_id,$request->get('fecha_corte'))->sum('detalle_pago_valor')
                        +Descuento_Anticipo_Cliente::DescuentosAnticipoByCXCAfterCorte(substr($cuenta->cuenta_descripcion, 38),$request->get('fecha_corte'))->sum('descuento_valor');
                        */
                    } else {
                        $datos[$count]['sal'] = $cuenta->cuenta_monto;
                    }
                    $datos[$count]['pag'] = 0;
                    $datos[$count]['fep'] = '';
                    $datos[$count]['tip'] = '';
                    $datos[$count]['tot'] = '2';
                    $count ++;
                    $countCuenta = $count - 1;
                    foreach (Detalle_Pago_CXC::CuentaCobrarPagosCorte($cuenta->cuenta_id, $request->get('fecha_corte'))->orderBy('pago_fecha')->get() as $pago) {
                        $datos[$count]['nom'] = '';
                        $datos[$count]['doc'] = '';
                        $datos[$count]['num'] = '';
                        $datos[$count]['fec'] = '';
                        $datos[$count]['mon'] = '';
                        $datos[$count]['sal'] = '';
                        $datos[$count]['pag'] = $pago->detalle_pago_valor;
                        $datos[$count]['fep'] = date("Y-m-d", strtotime($pago->pagoCXC->pago_fecha));  //date("d-m-Y", strtotime($originalDate));

                        if (Auth::user()->empresa->empresa_contabilidad == '1') {
                            $datos[$count]['dia'] = $pago->pagoCXC->diario->diario_codigo;
                        } else {
                            $datos[$count]['dia'] = '';
                        }
                        $datos[$count]['tip'] = $pago->detalle_pago_descripcion;
                        $datos[$count]['tot'] = '3';

                        if (floatval($datos[$countCuenta]['sal'])>0) {
                            $datos[$countCuenta]['sal'] = floatval($datos[$countCuenta]['sal']) - floatval($pago->detalle_pago_valor);
                        } else {
                            $datos[$countCuenta]['sal'] = floatval($datos[$countCuenta]['sal']);
                        }
                        /*
                        if(!$banderaMigrada){
                            $datos[$countCuenta]['sal'] = floatval($datos[$countCuenta]['sal']) - floatval($pago->detalle_pago_valor);
                        }else{
                            $datos[$countCuenta]['sal'] = floatval($datos[$countCuenta]['sal']);
                        }
                        */
                        $datos[$countCuenta]['pag'] = floatval($datos[$countCuenta]['pag']) + floatval($datos[$count]['pag']);
                        $count ++;
                    }
                    if (isset($cuenta->facturaVenta->factura_id)) {
                        foreach (Descuento_Anticipo_Cliente::DescuentosAnticipoByFacturaCorte($cuenta->facturaVenta->factura_id, $request->get('fecha_corte'))->orderBy('descuento_fecha')->get() as $pago) {
                            $datos[$count]['nom'] = '';
                            $datos[$count]['doc'] = '';
                            $datos[$count]['num'] = '';
                            $datos[$count]['fec'] = '';
                            $datos[$count]['mon'] = '';
                            $datos[$count]['sal'] = '';
                            $datos[$count]['pag'] = $pago->descuento_valor;
                            $datos[$count]['fep'] = date("Y-m-d", strtotime($pago->descuento_fecha));
                            if (Auth::user()->empresa->empresa_contabilidad == '1') {
                                $datos[$count]['dia'] = $pago->diario->diario_codigo;
                            } else {
                                $datos[$count]['dia'] = '';
                            }
                            $datos[$count]['tip'] = 'DESCUENTO DE ANTICIPO DE CLIENTE';
                            $datos[$count]['tot'] = '3';

                            $datos[$countCuenta]['sal'] = floatval($datos[$countCuenta]['sal']) - floatval($pago->descuento_valor);
                            $datos[$countCuenta]['pag'] = floatval($datos[$countCuenta]['pag']) + floatval($datos[$count]['pag']);
                            $count ++;
                        }
                    }
                    if ($banderaMigrada) {
                        foreach (Descuento_Anticipo_Cliente::DescuentosAnticipoByCXCCorte(substr($cuenta->cuenta_descripcion, 38), $request->get('fecha_corte'))->orderBy('descuento_fecha')->get() as $pago) {
                            $datos[$count]['nom'] = '';
                            $datos[$count]['doc'] = '';
                            $datos[$count]['num'] = '';
                            $datos[$count]['fec'] = '';
                            $datos[$count]['mon'] = '';
                            $datos[$count]['sal'] = '';
                            $datos[$count]['pag'] = $pago->descuento_valor;
                            $datos[$count]['fep'] = date("Y-m-d", strtotime($pago->descuento_fecha));
                            if (Auth::user()->empresa->empresa_contabilidad == '1') {
                                $datos[$count]['dia'] = $pago->diario->diario_codigo;
                            } else {
                                $datos[$count]['dia'] = '';
                            }
                            $datos[$count]['tip'] = 'DESCUENTO DE ANTICIPO DE CLIENTE';
                            $datos[$count]['tot'] = '3';

                            $datos[$countCuenta]['sal'] = floatval($datos[$countCuenta]['sal'])- floatval($pago->descuento_valor);
                            $datos[$countCuenta]['pag'] = floatval($datos[$countCuenta]['pag']) + floatval($datos[$count]['pag']);
                            $count ++;
                        }
                    }
                    $datos[$countCliente]['mon'] = floatval($datos[$countCliente]['mon']) + floatval($datos[$countCuenta]['mon']);
                    $datos[$countCliente]['sal'] = floatval($datos[$countCliente]['sal']) + floatval($datos[$countCuenta]['sal']);
                    $datos[$countCliente]['pag'] = floatval($datos[$countCliente]['pag']) + floatval($datos[$countCuenta]['pag']);


                    if (round($datos[$countCuenta]['sal'], 2) == 0) {
                        $count = $count - 1;
                        while ($countCuenta <= $count) {
                            $datos[$countCliente]['mon'] -= floatval($datos[$count]['mon']);
                            $datos[$countCliente]['sal'] -= floatval($datos[$count]['sal']);

                            if ($datos[$count]['tot']=='3') {
                                $datos[$countCliente]['pag'] -= floatval($datos[$count]['pag']);
                            }



                            //$montoEliminado+=$datos[$countCuenta]['mon'];
                            //$saldoEliminado+=$datos[$countCuenta]['sal'];
                            //$pagoEliminado+= floatval($datos[$count]['pag']);

                            array_pop($datos);
                            $count = $count - 1;
                        }
                        //$datos[$countCliente]['pag'] = floatval($datos[$countCliente]['mon'])-floatval($datos[$countCliente]['sal']);

                        $count = $count + 1;
                    }
                }

                //echo number_format($pagoEliminado, 2).'<br>';
                $mon = $mon + floatval($datos[$countCliente]['mon']);
                $sal = $sal + floatval($datos[$countCliente]['sal']);
                $pag = $pag + floatval($datos[$countCliente]['pag']);
                //$pag = $pag + floatval($datos[$countCliente]['mon']) - floatval($datos[$countCliente]['sal']);

                if ($datos[$count-1]['tot'] == '1') {
                    array_pop($datos);
                    $count = $count - 1;
                }
            }

            return view('admin.cuentasCobrar.estadoCuenta.pendiente', ['cuentas'=>Cuenta::Cuentas()->orderBy('cuenta_numero', 'asc')->get(),'tab'=>'1','mon'=>$mon,'sal'=>$sal,'pag'=>$pag,'fecC'=>$request->get('fecha_corte'),'tipo'=>$request->get('tipoConsulta'),'sucurslaC'=>$request->get('sucursal_id'),'sucursales'=>Sucursal::sucursales()->get(),'clienteC'=>$request->get('clienteID'),'fecI'=>$request->get('fecha_desde'),'fecF'=>$request->get('fecha_hasta'),'todo'=>$todo,'datos'=>$datos,'clientes'=>Cliente::clientes()->get(),'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        } catch(\Exception $ex) {
            return redirect('cxc')->with('error2', 'Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function pdfPendiente(Request $request)
    {
        ini_set('max_execution_time', 1200);
        try {
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono', 'grupo_orden', 'grupo_orden')->join('rol_permiso', 'usuario_rol.rol_id', '=', 'rol_permiso.rol_id')->join('permiso', 'permiso.permiso_id', '=', 'rol_permiso.permiso_id')->join('grupo_permiso', 'grupo_permiso.grupo_id', '=', 'permiso.grupo_id')->join('tipo_grupo', 'tipo_grupo.grupo_id', '=', 'grupo_permiso.grupo_id')->where('permiso_estado', '=', '1')->where('usuario_rol.user_id', '=', Auth::user()->user_id)->orderBy('grupo_orden', 'asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id', 'tipo_grupo.tipo_id', 'tipo_nombre', 'tipo_icono', 'tipo_orden')->join('rol_permiso', 'usuario_rol.rol_id', '=', 'rol_permiso.rol_id')->join('permiso', 'permiso.permiso_id', '=', 'rol_permiso.permiso_id')->join('tipo_grupo', 'tipo_grupo.tipo_id', '=', 'permiso.tipo_id')->where('permiso_estado', '=', '1')->where('usuario_rol.user_id', '=', Auth::user()->user_id)->orderBy('tipo_orden', 'asc')->distinct()->get();
            $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso', 'usuario_rol.rol_id', '=', 'rol_permiso.rol_id')->join('permiso', 'permiso.permiso_id', '=', 'rol_permiso.permiso_id')->where('permiso_estado', '=', '1')->where('usuario_rol.user_id', '=', Auth::user()->user_id)->orderBy('permiso_orden', 'asc')->get();
            $count = 1;
            $countCliente = 0;
            $countCuenta = 0;
            $datos = null;
            $todo = 0;
            $mon = 0;
            $sal = 0;
            $pag = 0;
            if ($request->get('fecha_todo') == "on") {
                $todo = 1;
            }
            if ($request->get('clienteID') == "0") {
                $clientes = Cliente::clientes()->get();
            } else {
                $clientes = Cliente::cliente($request->get('clienteID'))->get();
            }

            foreach ($clientes as $cliente) {
                $datos[$count]['nom'] = $cliente->cliente_nombre;
                $datos[$count]['doc'] = '';
                $datos[$count]['num'] = '';
                $datos[$count]['fec'] = '';
                $datos[$count]['mon'] = 0;
                $datos[$count]['sal'] = 0;
                $datos[$count]['pag'] = 0;
                $datos[$count]['fep'] = '';
                $datos[$count]['dia'] = '';
                $datos[$count]['tip'] = '';
                $datos[$count]['tot'] = '1';
                $count ++;
                $countCliente = $count - 1;
                $banderaMigrada = false;

                $montoEliminado=0;
                $saldoEliminado=0;
                $pagoEliminado=0;

                foreach (Cuenta_Cobrar::CuentasCobrarPendientes($request->get('fecha_corte'), $cliente->cliente_id, $request->get('sucursal_id'))->select('cuenta_cobrar.cuenta_id', 'cuenta_cobrar.cuenta_fecha', 'cuenta_cobrar.cuenta_monto', 'cuenta_cobrar.cuenta_saldom', 'cuenta_cobrar.cuenta_descripcion', 'cuenta_cobrar.cuenta_saldom')->having('cuenta_monto', '>', DB::raw("(SELECT sum(detalle_pago_valor) FROM detalle_pago_cxc inner join pago_cxc on pago_cxc.pago_id = detalle_pago_cxc.pago_id WHERE pago_fecha <= '".$request->get('fecha_corte')."' and detalle_pago_cxc.cuenta_id = cuenta_cobrar.cuenta_id)"))->orhavingRaw("(SELECT sum(detalle_pago_valor) FROM detalle_pago_cxc inner join pago_cxc on pago_cxc.pago_id = detalle_pago_cxc.pago_id WHERE pago_fecha <= '".$request->get('fecha_corte')."' and detalle_pago_cxc.cuenta_id = cuenta_cobrar.cuenta_id) is null")->groupBy('cuenta_cobrar.cuenta_id', 'cuenta_cobrar.cuenta_fecha', 'cuenta_cobrar.cuenta_monto')->get() as $cuenta) {
                    $banderaMigrada = false;
                    $datos[$count]['nom'] = '';
                    $datos[$count]['doc'] = '';
                    $datos[$count]['num'] = '';
                    $datos[$count]['dia'] = '';
                    if ($cuenta->facturaVenta) {
                        $datos[$count]['doc'] = 'FACTURA';
                        $datos[$count]['num'] = $cuenta->facturaVenta->factura_numero;
                        $datos[$count]['dia'] = $cuenta->facturaVenta->diario->diario_codigo;
                    }
                    if ($cuenta->notaEntrega) {
                        $datos[$count]['doc'] = 'NOTA DE ENTREGA';
                        $datos[$count]['num'] = $cuenta->notaEntrega->nt_numero;
                        if (Auth::user()->empresa->empresa_contabilidad == '1') {
                            $datos[$count]['dia'] = $cuenta->notaEntrega->diario->diario_codigo;
                        } else {
                            $datos[$count]['dia'] = '';
                        }
                    }
                    if ($cuenta->notaDebito) {
                        $datos[$count]['doc'] = 'NOTA DE DÉBITO';
                        $datos[$count]['num'] = $cuenta->notaDebito->nd_numero;
                        $datos[$count]['dia'] = $cuenta->notaDebito->diario->diario_codigo;
                    }
                    if ($datos[$count]['doc'] == '') {
                        $datos[$count]['num'] = substr($cuenta->cuenta_descripcion, 38);
                        $datos[$count]['doc'] = 'FACTURA';
                        $datos[$count]['dia'] = '';
                        $banderaMigrada = true;
                    }
                    $datos[$count]['fec'] = date("Y-m-d", strtotime($cuenta->cuenta_fecha));
                    $datos[$count]['mon'] = $cuenta->cuenta_monto;
                    if ($banderaMigrada) {
                        $datos[$count]['sal'] = $cuenta->cuenta_saldom;
                        /*
                        if($cuenta->cuenta_saldom>0) $datos[$count]['mon'] = $cuenta->cuenta_saldom;
                        $datos[$count]['sal'] = $cuenta->cuenta_saldo+Detalle_Pago_CXC::cuentaCobrarPagosAfterCorte($cuenta->cuenta_id,$request->get('fecha_corte'))->sum('detalle_pago_valor')
                        +Descuento_Anticipo_Cliente::DescuentosAnticipoByCXCAfterCorte(substr($cuenta->cuenta_descripcion, 38),$request->get('fecha_corte'))->sum('descuento_valor');
                        */
                    } else {
                        $datos[$count]['sal'] = $cuenta->cuenta_monto;
                    }
                    $datos[$count]['pag'] = 0;
                    $datos[$count]['fep'] = '';
                    $datos[$count]['tip'] = '';
                    $datos[$count]['tot'] = '2';
                    $count ++;
                    $countCuenta = $count - 1;
                    foreach (Detalle_Pago_CXC::CuentaCobrarPagosCorte($cuenta->cuenta_id, $request->get('fecha_corte'))->orderBy('pago_fecha')->get() as $pago) {
                        $datos[$count]['nom'] = '';
                        $datos[$count]['doc'] = '';
                        $datos[$count]['num'] = '';
                        $datos[$count]['fec'] = '';
                        $datos[$count]['mon'] = '';
                        $datos[$count]['sal'] = '';
                        $datos[$count]['pag'] = $pago->detalle_pago_valor;
                        $datos[$count]['fep'] = date("Y-m-d", strtotime($pago->pagoCXC->pago_fecha));  //date("d-m-Y", strtotime($originalDate));

                        if (Auth::user()->empresa->empresa_contabilidad == '1') {
                            $datos[$count]['dia'] = $pago->pagoCXC->diario->diario_codigo;
                        } else {
                            $datos[$count]['dia'] = '';
                        }
                        $datos[$count]['tip'] = $pago->detalle_pago_descripcion;
                        $datos[$count]['tot'] = '3';

                        if (floatval($datos[$countCuenta]['sal'])>0) {
                            $datos[$countCuenta]['sal'] = floatval($datos[$countCuenta]['sal']) - floatval($pago->detalle_pago_valor);
                        } else {
                            $datos[$countCuenta]['sal'] = floatval($datos[$countCuenta]['sal']);
                        }
                        /*
                        if(!$banderaMigrada){
                            $datos[$countCuenta]['sal'] = floatval($datos[$countCuenta]['sal']) - floatval($pago->detalle_pago_valor);
                        }else{
                            $datos[$countCuenta]['sal'] = floatval($datos[$countCuenta]['sal']);
                        }
                        */
                        $datos[$countCuenta]['pag'] = floatval($datos[$countCuenta]['pag']) + floatval($datos[$count]['pag']);
                        $count ++;
                    }
                    if (isset($cuenta->facturaVenta->factura_id)) {
                        foreach (Descuento_Anticipo_Cliente::DescuentosAnticipoByFacturaCorte($cuenta->facturaVenta->factura_id, $request->get('fecha_corte'))->orderBy('descuento_fecha')->get() as $pago) {
                            $datos[$count]['nom'] = '';
                            $datos[$count]['doc'] = '';
                            $datos[$count]['num'] = '';
                            $datos[$count]['fec'] = '';
                            $datos[$count]['mon'] = '';
                            $datos[$count]['sal'] = '';
                            $datos[$count]['pag'] = $pago->descuento_valor;
                            $datos[$count]['fep'] = date("Y-m-d", strtotime($pago->descuento_fecha));
                            if (Auth::user()->empresa->empresa_contabilidad == '1') {
                                $datos[$count]['dia'] = $pago->diario->diario_codigo;
                            } else {
                                $datos[$count]['dia'] = '';
                            }
                            $datos[$count]['tip'] = 'DESCUENTO DE ANTICIPO DE CLIENTE';
                            $datos[$count]['tot'] = '3';

                            $datos[$countCuenta]['sal'] = floatval($datos[$countCuenta]['sal']) - floatval($pago->descuento_valor);
                            $datos[$countCuenta]['pag'] = floatval($datos[$countCuenta]['pag']) + floatval($datos[$count]['pag']);
                            $count ++;
                        }
                    }
                    if ($banderaMigrada) {
                        foreach (Descuento_Anticipo_Cliente::DescuentosAnticipoByCXCCorte(substr($cuenta->cuenta_descripcion, 38), $request->get('fecha_corte'))->orderBy('descuento_fecha')->get() as $pago) {
                            $datos[$count]['nom'] = '';
                            $datos[$count]['doc'] = '';
                            $datos[$count]['num'] = '';
                            $datos[$count]['fec'] = '';
                            $datos[$count]['mon'] = '';
                            $datos[$count]['sal'] = '';
                            $datos[$count]['pag'] = $pago->descuento_valor;
                            $datos[$count]['fep'] = date("Y-m-d", strtotime($pago->descuento_fecha));
                            if (Auth::user()->empresa->empresa_contabilidad == '1') {
                                $datos[$count]['dia'] = $pago->diario->diario_codigo;
                            } else {
                                $datos[$count]['dia'] = '';
                            }
                            $datos[$count]['tip'] = 'DESCUENTO DE ANTICIPO DE CLIENTE';
                            $datos[$count]['tot'] = '3';

                            $datos[$countCuenta]['sal'] = floatval($datos[$countCuenta]['sal'])- floatval($pago->descuento_valor);
                            $datos[$countCuenta]['pag'] = floatval($datos[$countCuenta]['pag']) + floatval($datos[$count]['pag']);
                            $count ++;
                        }
                    }
                    $datos[$countCliente]['mon'] = floatval($datos[$countCliente]['mon']) + floatval($datos[$countCuenta]['mon']);
                    $datos[$countCliente]['sal'] = floatval($datos[$countCliente]['sal']) + floatval($datos[$countCuenta]['sal']);
                    $datos[$countCliente]['pag'] = floatval($datos[$countCliente]['pag']) + floatval($datos[$countCuenta]['pag']);


                    if (round($datos[$countCuenta]['sal'], 2) == 0) {
                        $count = $count - 1;
                        while ($countCuenta <= $count) {
                            $datos[$countCliente]['mon'] -= floatval($datos[$count]['mon']);
                            $datos[$countCliente]['sal'] -= floatval($datos[$count]['sal']);

                            if ($datos[$count]['tot']=='3') {
                                $datos[$countCliente]['pag'] -= floatval($datos[$count]['pag']);
                            }



                            //$montoEliminado+=$datos[$countCuenta]['mon'];
                            //$saldoEliminado+=$datos[$countCuenta]['sal'];
                            //$pagoEliminado+= floatval($datos[$count]['pag']);

                            array_pop($datos);
                            $count = $count - 1;
                        }
                        //$datos[$countCliente]['pag'] = floatval($datos[$countCliente]['mon'])-floatval($datos[$countCliente]['sal']);

                        $count = $count + 1;
                    }
                }

                //echo number_format($pagoEliminado, 2).'<br>';
                $mon = $mon + floatval($datos[$countCliente]['mon']);
                $sal = $sal + floatval($datos[$countCliente]['sal']);
                $pag = $pag + floatval($datos[$countCliente]['pag']);
                //$pag = $pag + floatval($datos[$countCliente]['mon']) - floatval($datos[$countCliente]['sal']);

                if ($datos[$count-1]['tot'] == '1') {
                    array_pop($datos);
                    $count = $count - 1;
                }
            }


            $this->fpdf->AddPage();


            $this->fpdf->SetFont('Arial', 'B', 12);
            $this->fpdf->Cell(40, 8, '', 0, 0, 'L');
            $this->fpdf->Cell(20, 8, 'Monto: ', 0, 0, 'L');
            $this->fpdf->SetFont('Arial', '', 12);
            $this->fpdf->Cell(30, 8, number_format($mon, 2), 0, 0, 'R');

            $this->fpdf->Cell(20, 8, '', 0, 0, 'L');
            $this->fpdf->SetFont('Arial', 'B', 12);
            $this->fpdf->Cell(20, 8, 'Saldo: ', 0, 0, 'L');
            $this->fpdf->SetFont('Arial', '', 12);
            $this->fpdf->Cell(30, 8, number_format($sal, 2), 0, 0, 'R');

            $this->fpdf->Cell(20, 8, '', 0, 0, 'L');
            $this->fpdf->SetFont('Arial', 'B', 12);
            $this->fpdf->Cell(20, 8, 'Pagos: ', 0, 0, 'L');
            $this->fpdf->SetFont('Arial', '', 12);
            $this->fpdf->Cell(30, 8, number_format($pag, 2), 0, 1, 'R');
            $this->fpdf->Ln(2);

            $this->fpdf->SetFont('Arial', '', 10);

            if (isset($datos)) {
                for ($i = 1; $i <= count($datos); ++$i) {
                    if ($datos[$i]['tot'] == '1') {
                        $this->fpdf->SetFillColor(175, 223, 255);
                        $this->fpdf->SetFont('Arial', 'B', 10);
                        $this->fpdf->Cell(80, 8, $datos[$i]['nom'], 1, 0, 'L', true);
                        $this->fpdf->Cell(25, 8, number_format($datos[$i]['mon'], 2), 1, 0, 'R', true);
                        $this->fpdf->Cell(25, 8, number_format($datos[$i]['sal'], 2), 1, 0, 'R', true);
                        $this->fpdf->Cell(25, 8, number_format($datos[$i]['pag'], 2), 1, 0, 'R', true);
                        $this->fpdf->Cell(120, 8, '', 1, 1, 'L', true);
                    }

                    if ($datos[$i]['tot'] == '2') {
                        $this->fpdf->SetFont('Arial', '', 7);
                        $this->fpdf->Cell(30, 8, $datos[$i]['doc'], 1, 0, 'L');
                        $this->fpdf->Cell(30, 8, $datos[$i]['num'], 1, 0, 'R');
                        $this->fpdf->Cell(20, 8, $datos[$i]['fec'], 1, 0, 'R');

                        $this->fpdf->Cell(25, 8, number_format($datos[$i]['mon'], 2), 1, 0, 'R');
                        $this->fpdf->Cell(25, 8, number_format($datos[$i]['sal'], 2), 1, 0, 'R');
                        $this->fpdf->Cell(25, 8, number_format($datos[$i]['pag'], 2), 1, 0, 'R');
                        $this->fpdf->Cell(20, 8, $datos[$i]['fep'], 1, 0, 'C');

                        if (Auth::user()->empresa->empresa_llevaContabilidad == '1') {
                            $this->fpdf->Cell(30, 8, $datos[$i]['dia'], 1, 0, 'C');
                        } else {
                            $this->fpdf->Cell(30, 8, '', 1, 0, 'C');
                        }


                        if (str_starts_with($datos[$i]['tip'], 'PAGO EN DEPOSITO DE CHEQUE')) {
                            $this->fpdf->Cell(70, 8, utf8_decode('PAG DEP CH '.substr($datos[$i]['tip'], 26)), 1, 1, 'L');
                        } else {
                            $this->fpdf->Cell(70, 8, utf8_decode($datos[$i]['tip']), 1, 1, 'L');
                        }
                    }

                    if ($datos[$i]['tot'] == '3') {
                        $this->fpdf->SetFillColor(211, 211, 211);
                        $this->fpdf->SetFont('Arial', 'B', 7);
                        $this->fpdf->Cell(130, 8, '', 1, 0, 'L', true);
                        $this->fpdf->Cell(25, 8, number_format($datos[$i]['pag'], 2), 1, 0, 'R', true);
                        $this->fpdf->Cell(20, 8, $datos[$i]['fep'], 1, 0, 'C', true);

                        if (Auth::user()->empresa->empresa_llevaContabilidad == '1') {
                            $this->fpdf->Cell(30, 8, $datos[$i]['dia'], 1, 0, 'C', true);
                        } else {
                            $this->fpdf->Cell(30, 8, '', 1, 0, 'C');
                        }

                        if (str_starts_with($datos[$i]['tip'], 'PAGO EN DEPOSITO DE CHEQUE')) {
                            $this->fpdf->Cell(70, 8, utf8_decode('PAG DEP CH '.substr($datos[$i]['tip'], 26)), 1, 1, 'L', true);
                        } else {
                            $this->fpdf->Cell(70, 8, utf8_decode($datos[$i]['tip']), 1, 1, 'L', true);
                        }
                    }
                }

                $this->fpdf->Output();
                exit;
            }
            return 'error desconocido';
        } catch(\Exception $ex) {
            return redirect('cxc')->with('error2', 'Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }

    public function excelPendiente(Request $request){
        ini_set('max_execution_time', 1200);
        $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono', 'grupo_orden', 'grupo_orden')->join('rol_permiso', 'usuario_rol.rol_id', '=', 'rol_permiso.rol_id')->join('permiso', 'permiso.permiso_id', '=', 'rol_permiso.permiso_id')->join('grupo_permiso', 'grupo_permiso.grupo_id', '=', 'permiso.grupo_id')->join('tipo_grupo', 'tipo_grupo.grupo_id', '=', 'grupo_permiso.grupo_id')->where('permiso_estado', '=', '1')->where('usuario_rol.user_id', '=', Auth::user()->user_id)->orderBy('grupo_orden', 'asc')->distinct()->get();
        $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id', 'tipo_grupo.tipo_id', 'tipo_nombre', 'tipo_icono', 'tipo_orden')->join('rol_permiso', 'usuario_rol.rol_id', '=', 'rol_permiso.rol_id')->join('permiso', 'permiso.permiso_id', '=', 'rol_permiso.permiso_id')->join('tipo_grupo', 'tipo_grupo.tipo_id', '=', 'permiso.tipo_id')->where('permiso_estado', '=', '1')->where('usuario_rol.user_id', '=', Auth::user()->user_id)->orderBy('tipo_orden', 'asc')->distinct()->get();
        $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso', 'usuario_rol.rol_id', '=', 'rol_permiso.rol_id')->join('permiso', 'permiso.permiso_id', '=', 'rol_permiso.permiso_id')->where('permiso_estado', '=', '1')->where('usuario_rol.user_id', '=', Auth::user()->user_id)->orderBy('permiso_orden', 'asc')->get();
        $count = 1;
        $countCliente = 0;
        $countCuenta = 0;
        $datos = null;
        $todo = 0;
        $mon = 0;
        $sal = 0;
        $pag = 0;
        if ($request->get('fecha_todo') == "on") {
            $todo = 1;
        }
        if ($request->get('clienteID') == "0") {
            $clientes = Cliente::clientes()->get();
        } else {
            $clientes = Cliente::cliente($request->get('clienteID'))->get();
        }
        foreach ($clientes as $cliente) {
            $datos[$count]['nom'] = $cliente->cliente_nombre;
            $datos[$count]['doc'] = '';
            $datos[$count]['num'] = '';
            $datos[$count]['fec'] = '';
            $datos[$count]['mon'] = 0;
            $datos[$count]['sal'] = 0;
            $datos[$count]['pag'] = 0;
            $datos[$count]['fep'] = '';
            $datos[$count]['dia'] = '';
            $datos[$count]['tip'] = '';
            $datos[$count]['tot'] = '1';
            $count ++;
            $countCliente = $count - 1;
            $banderaMigrada = false;

            $montoEliminado=0;
            $saldoEliminado=0;
            $pagoEliminado=0;

            foreach (Cuenta_Cobrar::CuentasCobrarPendientes($request->get('fecha_corte'), $cliente->cliente_id, $request->get('sucursal_id'))->select('cuenta_cobrar.cuenta_id', 'cuenta_cobrar.cuenta_fecha', 'cuenta_cobrar.cuenta_monto', 'cuenta_cobrar.cuenta_saldom', 'cuenta_cobrar.cuenta_descripcion', 'cuenta_cobrar.cuenta_saldom')->having('cuenta_monto', '>', DB::raw("(SELECT sum(detalle_pago_valor) FROM detalle_pago_cxc inner join pago_cxc on pago_cxc.pago_id = detalle_pago_cxc.pago_id WHERE pago_fecha <= '".$request->get('fecha_corte')."' and detalle_pago_cxc.cuenta_id = cuenta_cobrar.cuenta_id)"))->orhavingRaw("(SELECT sum(detalle_pago_valor) FROM detalle_pago_cxc inner join pago_cxc on pago_cxc.pago_id = detalle_pago_cxc.pago_id WHERE pago_fecha <= '".$request->get('fecha_corte')."' and detalle_pago_cxc.cuenta_id = cuenta_cobrar.cuenta_id) is null")->groupBy('cuenta_cobrar.cuenta_id', 'cuenta_cobrar.cuenta_fecha', 'cuenta_cobrar.cuenta_monto')->get() as $cuenta) {
                $banderaMigrada = false;
                $datos[$count]['nom'] = '';
                $datos[$count]['doc'] = '';
                $datos[$count]['num'] = '';
                $datos[$count]['dia'] = '';
                if ($cuenta->facturaVenta) {
                    $datos[$count]['doc'] = 'FACTURA';
                    $datos[$count]['num'] = $cuenta->facturaVenta->factura_numero;
                    $datos[$count]['dia'] = $cuenta->facturaVenta->diario->diario_codigo;
                }
                if ($cuenta->notaEntrega) {
                    $datos[$count]['doc'] = 'NOTA DE ENTREGA';
                    $datos[$count]['num'] = $cuenta->notaEntrega->nt_numero;
                    if (Auth::user()->empresa->empresa_contabilidad == '1') {
                        $datos[$count]['dia'] = $cuenta->notaEntrega->diario->diario_codigo;
                    } else {
                        $datos[$count]['dia'] = '';
                    }
                }
                if ($cuenta->notaDebito) {
                    $datos[$count]['doc'] = 'NOTA DE DÉBITO';
                    $datos[$count]['num'] = $cuenta->notaDebito->nd_numero;
                    $datos[$count]['dia'] = $cuenta->notaDebito->diario->diario_codigo;
                }
                if ($datos[$count]['doc'] == '') {
                    $datos[$count]['num'] = substr($cuenta->cuenta_descripcion, 38);
                    $datos[$count]['doc'] = 'FACTURA';
                    $datos[$count]['dia'] = '';
                    $banderaMigrada = true;
                }
                $datos[$count]['fec'] = date("Y-m-d", strtotime($cuenta->cuenta_fecha));
                $datos[$count]['mon'] = $cuenta->cuenta_monto;
                if ($banderaMigrada) {
                    $datos[$count]['sal'] = $cuenta->cuenta_saldom;
                    /*
                    if($cuenta->cuenta_saldom>0) $datos[$count]['mon'] = $cuenta->cuenta_saldom;
                    $datos[$count]['sal'] = $cuenta->cuenta_saldo+Detalle_Pago_CXC::cuentaCobrarPagosAfterCorte($cuenta->cuenta_id,$request->get('fecha_corte'))->sum('detalle_pago_valor')
                    +Descuento_Anticipo_Cliente::DescuentosAnticipoByCXCAfterCorte(substr($cuenta->cuenta_descripcion, 38),$request->get('fecha_corte'))->sum('descuento_valor');
                    */
                } else {
                    $datos[$count]['sal'] = $cuenta->cuenta_monto;
                }
                $datos[$count]['pag'] = 0;
                $datos[$count]['fep'] = '';
                $datos[$count]['tip'] = '';
                $datos[$count]['tot'] = '2';
                $count ++;
                $countCuenta = $count - 1;
                foreach (Detalle_Pago_CXC::CuentaCobrarPagosCorte($cuenta->cuenta_id, $request->get('fecha_corte'))->orderBy('pago_fecha')->get() as $pago) {
                    $datos[$count]['nom'] = '';
                    $datos[$count]['doc'] = '';
                    $datos[$count]['num'] = '';
                    $datos[$count]['fec'] = '';
                    $datos[$count]['mon'] = '';
                    $datos[$count]['sal'] = '';
                    $datos[$count]['pag'] = $pago->detalle_pago_valor;
                    $datos[$count]['fep'] = date("Y-m-d", strtotime($pago->pagoCXC->pago_fecha));  //date("d-m-Y", strtotime($originalDate));

                    if (Auth::user()->empresa->empresa_contabilidad == '1') {
                        $datos[$count]['dia'] = $pago->pagoCXC->diario->diario_codigo;
                    } else {
                        $datos[$count]['dia'] = '';
                    }
                    $datos[$count]['tip'] = $pago->detalle_pago_descripcion;
                    $datos[$count]['tot'] = '3';

                    if (floatval($datos[$countCuenta]['sal'])>0) {
                        $datos[$countCuenta]['sal'] = floatval($datos[$countCuenta]['sal']) - floatval($pago->detalle_pago_valor);
                    } else {
                        $datos[$countCuenta]['sal'] = floatval($datos[$countCuenta]['sal']);
                    }
                    /*
                    if(!$banderaMigrada){
                        $datos[$countCuenta]['sal'] = floatval($datos[$countCuenta]['sal']) - floatval($pago->detalle_pago_valor);
                    }else{
                        $datos[$countCuenta]['sal'] = floatval($datos[$countCuenta]['sal']);
                    }
                    */
                    $datos[$countCuenta]['pag'] = floatval($datos[$countCuenta]['pag']) + floatval($datos[$count]['pag']);
                    $count ++;
                }
                if (isset($cuenta->facturaVenta->factura_id)) {
                    foreach (Descuento_Anticipo_Cliente::DescuentosAnticipoByFacturaCorte($cuenta->facturaVenta->factura_id, $request->get('fecha_corte'))->orderBy('descuento_fecha')->get() as $pago) {
                        $datos[$count]['nom'] = '';
                        $datos[$count]['doc'] = '';
                        $datos[$count]['num'] = '';
                        $datos[$count]['fec'] = '';
                        $datos[$count]['mon'] = '';
                        $datos[$count]['sal'] = '';
                        $datos[$count]['pag'] = $pago->descuento_valor;
                        $datos[$count]['fep'] = date("Y-m-d", strtotime($pago->descuento_fecha));
                        if (Auth::user()->empresa->empresa_contabilidad == '1') {
                            $datos[$count]['dia'] = $pago->diario->diario_codigo;
                        } else {
                            $datos[$count]['dia'] = '';
                        }
                        $datos[$count]['tip'] = 'DESCUENTO DE ANTICIPO DE CLIENTE';
                        $datos[$count]['tot'] = '3';

                        $datos[$countCuenta]['sal'] = floatval($datos[$countCuenta]['sal']) - floatval($pago->descuento_valor);
                        $datos[$countCuenta]['pag'] = floatval($datos[$countCuenta]['pag']) + floatval($datos[$count]['pag']);
                        $count ++;
                    }
                }
                if ($banderaMigrada) {
                    foreach (Descuento_Anticipo_Cliente::DescuentosAnticipoByCXCCorte(substr($cuenta->cuenta_descripcion, 38), $request->get('fecha_corte'))->orderBy('descuento_fecha')->get() as $pago) {
                        $datos[$count]['nom'] = '';
                        $datos[$count]['doc'] = '';
                        $datos[$count]['num'] = '';
                        $datos[$count]['fec'] = '';
                        $datos[$count]['mon'] = '';
                        $datos[$count]['sal'] = '';
                        $datos[$count]['pag'] = $pago->descuento_valor;
                        $datos[$count]['fep'] = date("Y-m-d", strtotime($pago->descuento_fecha));
                        if (Auth::user()->empresa->empresa_contabilidad == '1') {
                            $datos[$count]['dia'] = $pago->diario->diario_codigo;
                        } else {
                            $datos[$count]['dia'] = '';
                        }
                        $datos[$count]['tip'] = 'DESCUENTO DE ANTICIPO DE CLIENTE';
                        $datos[$count]['tot'] = '3';

                        $datos[$countCuenta]['sal'] = floatval($datos[$countCuenta]['sal'])- floatval($pago->descuento_valor);
                        $datos[$countCuenta]['pag'] = floatval($datos[$countCuenta]['pag']) + floatval($datos[$count]['pag']);
                        $count ++;
                    }
                }
                $datos[$countCliente]['mon'] = floatval($datos[$countCliente]['mon']) + floatval($datos[$countCuenta]['mon']);
                $datos[$countCliente]['sal'] = floatval($datos[$countCliente]['sal']) + floatval($datos[$countCuenta]['sal']);
                $datos[$countCliente]['pag'] = floatval($datos[$countCliente]['pag']) + floatval($datos[$countCuenta]['pag']);


                if (round($datos[$countCuenta]['sal'], 2) == 0) {
                    $count = $count - 1;
                    while ($countCuenta <= $count) {
                        $datos[$countCliente]['mon'] -= floatval($datos[$count]['mon']);
                        $datos[$countCliente]['sal'] -= floatval($datos[$count]['sal']);

                        if ($datos[$count]['tot']=='3') {
                            $datos[$countCliente]['pag'] -= floatval($datos[$count]['pag']);
                        }



                        //$montoEliminado+=$datos[$countCuenta]['mon'];
                        //$saldoEliminado+=$datos[$countCuenta]['sal'];
                        //$pagoEliminado+= floatval($datos[$count]['pag']);

                        array_pop($datos);
                        $count = $count - 1;
                    }
                    //$datos[$countCliente]['pag'] = floatval($datos[$countCliente]['mon'])-floatval($datos[$countCliente]['sal']);

                    $count = $count + 1;
                }
            }

            //echo number_format($pagoEliminado, 2).'<br>';
            $mon = $mon + floatval($datos[$countCliente]['mon']);
            $sal = $sal + floatval($datos[$countCliente]['sal']);
            $pag = $pag + floatval($datos[$countCliente]['pag']);
            //$pag = $pag + floatval($datos[$countCliente]['mon']) - floatval($datos[$countCliente]['sal']);

            if ($datos[$count-1]['tot'] == '1') {
                array_pop($datos);
                $count = $count - 1;
            }
        }


       
            
            return Excel::download(new ViewExcel('admin.formatosExcel.estadoCuentaCXC',$datos), 'NEOPAGUPA  Sistema Contable.xlsx');
       
    }
    public function consultarSaldo(Request $request){
        if (isset($_POST['buscar'])) {
            return $this->buscarSaldo($request);
        }
        if (isset($_POST['pdf'])) {
            return $this->pdfSaldo($request);
        }
        if (isset($_POST['excel'])) {
            return $this->excelSaldo($request);
        }
    }
    public function buscarSaldo(Request $request){
        try {
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono', 'grupo_orden', 'grupo_orden')->join('rol_permiso', 'usuario_rol.rol_id', '=', 'rol_permiso.rol_id')->join('permiso', 'permiso.permiso_id', '=', 'rol_permiso.permiso_id')->join('grupo_permiso', 'grupo_permiso.grupo_id', '=', 'permiso.grupo_id')->join('tipo_grupo', 'tipo_grupo.grupo_id', '=', 'grupo_permiso.grupo_id')->where('permiso_estado', '=', '1')->where('usuario_rol.user_id', '=', Auth::user()->user_id)->orderBy('grupo_orden', 'asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id', 'tipo_grupo.tipo_id', 'tipo_nombre', 'tipo_icono', 'tipo_orden')->join('rol_permiso', 'usuario_rol.rol_id', '=', 'rol_permiso.rol_id')->join('permiso', 'permiso.permiso_id', '=', 'rol_permiso.permiso_id')->join('tipo_grupo', 'tipo_grupo.tipo_id', '=', 'permiso.tipo_id')->where('permiso_estado', '=', '1')->where('usuario_rol.user_id', '=', Auth::user()->user_id)->orderBy('tipo_orden', 'asc')->distinct()->get();
            $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso', 'usuario_rol.rol_id', '=', 'rol_permiso.rol_id')->join('permiso', 'permiso.permiso_id', '=', 'rol_permiso.permiso_id')->where('permiso_estado', '=', '1')->where('usuario_rol.user_id', '=', Auth::user()->user_id)->orderBy('permiso_orden', 'asc')->get();
            $count = 1;
            $datos = null;
            $todo = 0;
            $sal = 0;
            if ($request->get('fecha_todo2') == "on") {
                $todo = 1;
            }
            foreach (Cliente::clientes()->get() as $cliente) {
                $datos[$count]['ruc'] = $cliente->cliente_cedula;
                $datos[$count]['nom'] = $cliente->cliente_nombre;
                $datos[$count]['ant'] = '0.00';
               
                $datos[$count]['deb'] = Detalle_Diario::MayorCliente2($cliente->cliente_id,$request->get('fecha_desde2'),$request->get('fecha_hasta2'),$request->get('cuenta_id'),$request->get('sucursal_id2'))->sum('detalle_debe'); 
                $datos[$count]['hab'] = Detalle_Diario::MayorCliente2($cliente->cliente_id,$request->get('fecha_desde2'),$request->get('fecha_hasta2'),$request->get('cuenta_id'),$request->get('sucursal_id2'))->sum('detalle_haber');
            
                $datos[$count]['sal'] = floatval($datos[$count]['ant']) + floatval($datos[$count]['deb']) - floatval($datos[$count]['hab']);
                $count ++;
                $sal = $sal + floatval($datos[$count-1]['sal']);
                if (floatval($datos[$count-1]['sal']) == 0) {
                    array_pop($datos);
                    $count = $count - 1;
                }
            }
            return view('admin.cuentasCobrar.estadoCuenta.index',['cuentas'=>Cuenta::Cuentas()->orderBy('cuenta_numero','asc')->get(),'tab'=>'2','sal2'=>$sal,'sucurslaC2'=>$request->get('sucursal_id2'),'sucursales'=>Sucursal::sucursales()->get(),'fecI2'=>$request->get('fecha_desde2'),'fecF2'=>$request->get('fecha_hasta2'),'todo2'=>$todo,'datosSaldo'=>$datos,'clientes'=>Cliente::clientes()->get(),'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);      
        }catch(\Exception $ex){
            return redirect('cxc')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function pdfSaldo(Request $request){
        try {
            $todo = 0;
            if ($request->get('fecha_todo2') == "on") {
                $todo = 1;
            }
            $datos = null;
            $count = 1;
            $ruc = $request->get('idRuc');
            $nom = $request->get('idNom');
            $ant = $request->get('idAnt');
            $deb = $request->get('idDeb');
            $hab = $request->get('idHab');
            $sal = $request->get('idSal');
            if ($ruc) {
                for ($i = 0; $i < count($ruc); ++$i) {
                    $datos[$count]['ruc'] = $ruc[$i];
                    $datos[$count]['nom'] = $nom[$i];
                    $datos[$count]['ant'] = $ant[$i];
                    $datos[$count]['deb'] = $deb[$i];
                    $datos[$count]['hab'] = $hab[$i];
                    $datos[$count]['sal'] = $sal[$i];
                    $count ++;
                }
            }
            $empresa =  Empresa::empresa()->first();
            $ruta = public_path().'/PDF/'.$empresa->empresa_ruc;
            if (!is_dir($ruta)) {
                mkdir($ruta, 0777, true);
            }

            $view =  \View::make('admin.formatosPDF.saldoClientes', ['sal'=>$request->get('idSaldo2'),'todo'=>$todo,'datos'=>$datos,'desde'=>$request->get('fecha_desde2'),'hasta'=>$request->get('fecha_hasta2'),'actual'=>DateTime::createFromFormat('Y-m-d', date('Y-m-d'))->format('d/m/Y'),'empresa'=>$empresa]);
            if ($todo == 1) {
                $nombreArchivo = 'SALDO DE CLIENTES AL '.DateTime::createFromFormat('Y-m-d', date('Y-m-d'))->format('d-m-Y');
            } else {
                $nombreArchivo = 'SALDO DE CLIENTES DEL '.DateTime::createFromFormat('Y-m-d', $request->get('fecha_desde2'))->format('d-m-Y').' AL '.DateTime::createFromFormat('Y-m-d', $request->get('fecha_hasta2'))->format('d-m-Y');
            }
            return PDF::loadHTML($view)->save('PDF/'.$empresa->empresa_ruc.'/'.$nombreArchivo.'.pdf')->download($nombreArchivo.'.pdf');
        } catch(\Exception $ex) {
            return redirect('cxc')->with('error2', 'Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function excelSaldo(Request $request){
        try {
            $datos = null;
            $count = 1;
            $ruc = $request->get('idRuc');
            $nom = $request->get('idNom');
            $ant = $request->get('idAnt');
            $deb = $request->get('idDeb');
            $hab = $request->get('idHab');
            $sal = $request->get('idSal');
            if ($ruc) {
                for ($i = 0; $i < count($ruc); ++$i) {
                    $datos[$count]['ruc'] = $ruc[$i];
                    $datos[$count]['nom'] = $nom[$i];
                    $datos[$count]['ant'] = $ant[$i];
                    $datos[$count]['deb'] = $deb[$i];
                    $datos[$count]['hab'] = $hab[$i];
                    $datos[$count]['sal'] = $sal[$i];
                    $count ++;
                }
            }
            return Excel::download(new ViewExcel('admin.formatosExcel.saldoClientes', $datos), 'NEOPAGUPA  Sistema Contable.xlsx');
        } catch(\Exception $ex) {
            return redirect('cxc')->with('error2', 'Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function buscarByCliente(Request $request){
        return Cuenta_Cobrar::CuentasByCliente($request->get('cliente_id'), $request->get('sucursal_id'))->select('cuenta_id', DB::raw('(SELECT factura_numero FROM factura_venta WHERE factura_venta.cuenta_id = cuenta_cobrar.cuenta_id) as factura_numero'), DB::raw('(SELECT nt_numero FROM nota_entrega WHERE nota_entrega.cuenta_id = cuenta_cobrar.cuenta_id) as nt_numero'), DB::raw('(SELECT nd_numero FROM nota_debito WHERE nota_debito.cuenta_id = cuenta_cobrar.cuenta_id) as nd_numero'), 'cuenta_saldo', 'cuenta_fecha', 'cuenta_fecha_fin', 'cliente.cliente_id', 'cliente.cliente_cedula', 'cliente.cliente_nombre', DB::raw('(SELECT sum(anticipo_saldo) FROM anticipo_cliente WHERE anticipo_cliente.cliente_id = cliente.cliente_id) as saldo_cliente'), 'cuenta_cobrar.cuenta_descripcion')->get();
    }
}