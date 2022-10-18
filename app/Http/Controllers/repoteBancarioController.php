<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Banco;
use App\Models\Banco_Lista;
use App\Models\Cheque;
use App\Models\Cuenta_Bancaria;
use App\Models\Diario;
use App\Models\Punto_Emision;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class repoteBancarioController extends Controller
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
            
            $sucursal=Diario::DiarioTransferenciaDistinc()->select('sucursal_nombre')->union(Diario::DiariochequeDistinc()->select('sucursal_nombre'))->union(Diario::DiarioDepositoDistinc()->select('sucursal_nombre'))->union(Diario::DiarioNotaDebitoDistinc()->select('sucursal_nombre'))->union(Diario::DiarioNotaCreditoDistinc()->select('sucursal_nombre'))->distinct()->get();
            $bancos=Diario::DiarioTransferenciaDistinc()->select('banco_lista.banco_lista_id','banco_lista_nombre')->union(Diario::DiariochequeDistinc()->select('banco_lista.banco_lista_id','banco_lista_nombre'))->union(Diario::DiarioDepositoDistinc()->select('banco_lista.banco_lista_id','banco_lista_nombre'))->union(Diario::DiarioNotaDebitoDistinc()->select('banco_lista.banco_lista_id','banco_lista_nombre'))->union(Diario::DiarioNotaCreditoDistinc()->select('banco_lista.banco_lista_id','banco_lista_nombre'))->distinct()->get();
            return view('admin.bancos.reporteBancario.index',['bancos'=>$bancos,'sucursal'=>$sucursal,'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);           
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
       try{
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $sucursal=Diario::DiarioTransferenciaDistinc()->select('sucursal_nombre')->union(Diario::DiariochequeDistinc()->select('sucursal_nombre'))->union(Diario::DiarioDepositoDistinc()->select('sucursal_nombre'))->union(Diario::DiarioNotaDebitoDistinc()->select('sucursal_nombre'))->union(Diario::DiarioNotaCreditoDistinc()->select('sucursal_nombre'))->distinct()->get();
            $bancos=Diario::DiarioTransferenciaDistinc()->select('banco_lista.banco_lista_id','banco_lista_nombre')->union(Diario::DiariochequeDistinc()->select('banco_lista.banco_lista_id','banco_lista_nombre'))->union(Diario::DiarioDepositoDistinc()->select('banco_lista.banco_lista_id','banco_lista_nombre'))->union(Diario::DiarioNotaDebitoDistinc()->select('banco_lista.banco_lista_id','banco_lista_nombre'))->union(Diario::DiarioNotaCreditoDistinc()->select('banco_lista.banco_lista_id','banco_lista_nombre'))->distinct()->get();
            $cuentas=null;
            if($request->get('banco') != "--TODOS--"){
                $cuentas=Cuenta_Bancaria::CuentaBancoNumero($request->get('banco'))->get();
            }
           
            $cheque=null;
            $deposito=null;
            $tranferencia=null;
            $notadebito=null;
            $notacredito=null;
            ////////////Todo Diferente
            if ($request->get('fecha_todo') != "on"  && $request->get('sucursal') != "--TODOS--" && $request->get('tipo') != "--TODOS--" && $request->get('banco') != "--TODOS--" && $request->get('cuenta_id') != "--TODOS--") {
                if($request->get('tipo')=="Cheque"){
                    $cheque=Diario::DiarioChequeBancoSucursalbuscar($request->get('fecha_desde'),$request->get('fecha_hasta'),$request->get('sucursal'),$request->get('banco'),$request->get('cuenta_id'))->select('cheque_fecha_pago as fecha','cheque_numero as Numero','cheque_valor as Valor','diario_codigo as Diario')->get();
                }
                if($request->get('tipo')=="Deposito"){
                    $deposito=Diario::DiarioDepositoBancoSucursalbuscar($request->get('fecha_desde'),$request->get('fecha_hasta'),$request->get('sucursal'),$request->get('banco'),$request->get('cuenta_id'))->select('deposito_fecha as fecha','deposito_numero as Numero','deposito_valor as Valor','diario_codigo as Diario')->get();
                }
                if($request->get('tipo')=="Transferencia"){
                    $tranferencia=Diario::DiarioTransferenciaBancoSucursalbuscar($request->get('fecha_desde'),$request->get('fecha_hasta'),$request->get('sucursal'),$request->get('banco'),$request->get('cuenta_id'))->select('transferencia_fecha as fecha','transferencia_valor as Valor','diario_codigo as Diario')->get();
                }
                if($request->get('tipo')=="Nota de Debito"){
                    $notadebito=Diario::DiarioNotaDebitoBancoSucursalbuscar($request->get('fecha_desde'),$request->get('fecha_hasta'),$request->get('sucursal'),$request->get('banco'),$request->get('cuenta_id'))->select('nota_fecha as fecha','nota_numero as Numero','nota_valor as Valor','diario_codigo as Diario')->get();  
                }
                if($request->get('tipo')=="Nota de Credito"){
                    $notacredito=Diario::DiarioNotaCreditoBancoSucursalbuscar($request->get('fecha_desde'),$request->get('fecha_hasta'),$request->get('sucursal'),$request->get('banco'),$request->get('cuenta_id'))->select('nota_fecha as fecha','nota_numero as Numero','nota_valor as Valor','diario_codigo as Diario')->get();
                }
            }
            ////////////Todos
            if ($request->get('fecha_todo') == "on"  && $request->get('sucursal') == "--TODOS--" && $request->get('tipo') == "--TODOS--" && $request->get('banco') == "--TODOS--" && $request->get('cuenta_id') == "--TODOS--") {
                $cheque=Diario::DiarioChequeBancoSucursal()->select('cheque_fecha_pago as fecha','cheque_numero as Numero','cheque_valor as Valor','diario_codigo as Diario')->get();
                $tranferencia=Diario::DiarioTransferenciaBancoSucursal()->select('transferencia_fecha as fecha','transferencia_valor as Valor','diario_codigo as Diario')->get();
                $deposito=Diario::DiariodepositoBancoSucursal()->select('deposito_fecha as fecha','deposito_numero as Numero','deposito_valor as Valor','diario_codigo as Diario')->get();
                $notadebito=Diario::DiarioNotaDebitoBancoSucursal()->select('nota_fecha as fecha','nota_numero as Numero','nota_valor as Valor','diario_codigo as Diario')->get();
                $notacredito=Diario::DiarioNotaCreditoBancoSucursal()->select('nota_fecha as fecha','nota_numero as Numero','nota_valor as Valor','diario_codigo as Diario')->get();
            }
              ////////////Fecha
            if ($request->get('fecha_todo') != "on"  && $request->get('sucursal') == "--TODOS--" && $request->get('tipo') == "--TODOS--" && $request->get('banco') == "--TODOS--" && $request->get('cuenta_id') == "--TODOS--") {
                $cheque=Diario::DiarioChequeFecha($request->get('fecha_desde'),$request->get('fecha_hasta'))->select('cheque_fecha_pago as fecha','cheque_numero as Numero','cheque_valor as Valor','diario_codigo as Diario')->get();
                $tranferencia=Diario::DiarioTransferenciaFecha($request->get('fecha_desde'),$request->get('fecha_hasta'))->select('transferencia_fecha as fecha','transferencia_valor as Valor','diario_codigo as Diario')->get();
                $deposito=Diario::DiarioDepositoFecha($request->get('fecha_desde'),$request->get('fecha_hasta'))->select('deposito_fecha as fecha','deposito_numero as Numero','deposito_valor as Valor','diario_codigo as Diario')->get();
                $notadebito=Diario::DiarioNotaDebitoBancoFecha($request->get('fecha_desde'),$request->get('fecha_hasta'))->select('nota_fecha as fecha','nota_numero as Numero','nota_valor as Valor','diario_codigo as Diario')->get();
                $notacredito=Diario::DiarioNotaCreditoBancoFecha($request->get('fecha_desde'),$request->get('fecha_hasta'))->select('nota_fecha as fecha','nota_numero as Numero','nota_valor as Valor','diario_codigo as Diario')->get();
            }
             ////////////Sucursal
            if ($request->get('fecha_todo') == "on"  && $request->get('sucursal') != "--TODOS--" && $request->get('tipo') == "--TODOS--" && $request->get('banco') == "--TODOS--" && $request->get('cuenta_id') == "--TODOS--") {
                $cheque=Diario::DiarioChequeSucursal($request->get('sucursal'))->select('cheque_fecha_pago as fecha','cheque_numero as Numero','cheque_valor as Valor','diario_codigo as Diario')->get();
                $tranferencia=Diario::DiarioTransferenciaSucursal($request->get('sucursal'))->select('transferencia_fecha as fecha','transferencia_valor as Valor','diario_codigo as Diario')->get();
                $deposito=Diario::DiarioDepositoSucursal($request->get('sucursal'))->select('deposito_fecha as fecha','deposito_numero as Numero','deposito_valor as Valor','diario_codigo as Diario')->get();
                $notadebito=Diario::DiarioNotaDebitoSucursa($request->get('sucursal'))->select('nota_fecha as fecha','nota_numero as Numero','nota_valor as Valor','diario_codigo as Diario')->get();
                $notacredito=Diario::DiarioNotaCreditoSucursal($request->get('sucursal'))->select('nota_fecha as fecha','nota_numero as Numero','nota_valor as Valor','diario_codigo as Diario')->get();
            }
             ////////////Tipo
            if ($request->get('fecha_todo') == "on"  && $request->get('sucursal') == "--TODOS--" && $request->get('tipo') != "--TODOS--" && $request->get('banco') == "--TODOS--" && $request->get('cuenta_id') == "--TODOS--") {
                if ($request->get('tipo')=="Cheque") {
                    $cheque=Diario::DiarioChequeBancoSucursal()->select('cheque_fecha_pago as fecha','cheque_numero as Numero','cheque_valor as Valor','diario_codigo as Diario')->get();
               
                }
                if($request->get('tipo')=="Transferencia"){
                    $tranferencia=Diario::DiarioTransferenciaBancoSucursal()->select('transferencia_fecha as fecha','transferencia_valor as Valor','diario_codigo as Diario')->get();
               
                } 
                if ($request->get('tipo')=="Deposito") {
                    $deposito=Diario::DiarioDepositoBancoSucursal()->select('deposito_fecha as fecha','deposito_numero as Numero','deposito_valor as Valor','diario_codigo as Diario')->get();
              
                }
                if ($request->get('tipo')=="Nota de Debito") {
                    $notadebito=Diario::DiarioNotaDebitoBancoSucursal()->select('nota_fecha as fecha','nota_numero as Numero','nota_valor as Valor','diario_codigo as Diario')->get();
            
                }
                if ($request->get('tipo')=="Nota de Credito") {
                    $notacredito=Diario::DiarioNotaCreditoBancoSucursal()->select('nota_fecha as fecha','nota_numero as Numero','nota_valor as Valor','diario_codigo as Diario')->get();
         
                }            
            }
            ////////////Banco
            if ($request->get('fecha_todo') == "on"  && $request->get('sucursal') == "--TODOS--" && $request->get('tipo') == "--TODOS--" && $request->get('banco') != "--TODOS--" && $request->get('cuenta_id') == "--TODOS--") {
                    $cheque=Diario::DiarioChequeBanco($request->get('banco'))->select('cheque_fecha_pago as fecha','cheque_numero as Numero','cheque_valor as Valor','diario_codigo as Diario')->get();
                    $tranferencia=Diario::DiarioTransferenciaBanco($request->get('banco'))->select('transferencia_fecha as fecha','transferencia_valor as Valor','diario_codigo as Diario')->get();
                    $deposito=Diario::DiarioDepositoBanco($request->get('banco'))->select('deposito_fecha as fecha','deposito_numero as Numero','deposito_valor as Valor','diario_codigo as Diario')->get();
                    $notadebito=Diario::DiarioNotaDebitoBanco($request->get('banco'))->select('nota_fecha as fecha','nota_numero as Numero','nota_valor as Valor','diario_codigo as Diario')->get();
                    $notacredito=Diario::DiarioNotaCreditoBanco($request->get('banco'))->select('nota_fecha as fecha','nota_numero as Numero','nota_valor as Valor','diario_codigo as Diario')->get();          
            }
              ////////////Banco-Cuenta
            if ($request->get('fecha_todo') == "on"  && $request->get('sucursal') == "--TODOS--" && $request->get('tipo') == "--TODOS--" && $request->get('banco') != "--TODOS--" && $request->get('cuenta_id') != "--TODOS--") {
                $cheque=Diario::DiarioChequeCuenta($request->get('banco'),$request->get('cuenta_id'))->select('cheque_fecha_pago as fecha','cheque_numero as Numero','cheque_valor as Valor','diario_codigo as Diario')->get();
                $tranferencia=Diario::DiarioTransferenciaCuenta($request->get('banco'),$request->get('cuenta_id'))->select('transferencia_fecha as fecha','transferencia_valor as Valor','diario_codigo as Diario')->get();
                $deposito=Diario::DiarioDepositoCuenta($request->get('banco'),$request->get('cuenta_id'))->select('deposito_fecha as fecha','deposito_numero as Numero','deposito_valor as Valor','diario_codigo as Diario')->get();
                $notadebito=Diario::DiarioNotaDebitoCuenta($request->get('banco'),$request->get('cuenta_id'))->select('nota_fecha as fecha','nota_numero as Numero','nota_valor as Valor','diario_codigo as Diario')->get();
                $notacredito=Diario::DiarioNotaCreditoCuenta($request->get('banco'),$request->get('cuenta_id'))->select('nota_fecha as fecha','nota_numero as Numero','nota_valor as Valor','diario_codigo as Diario')->get();          
            }
             ////////////Fecha-Sucursal
            if ($request->get('fecha_todo') != "on"  && $request->get('sucursal') != "--TODOS--" && $request->get('tipo') == "--TODOS--" && $request->get('banco') == "--TODOS--" && $request->get('cuenta_id') == "--TODOS--") {
                $cheque=Diario::DiariochequeFechaSucursal($request->get('fecha_desde'), $request->get('fecha_hasta'),$request->get('sucursal'))->select('cheque_fecha_pago as fecha','cheque_numero as Numero','cheque_valor as Valor','diario_codigo as Diario')->get();
                $tranferencia=Diario::DiarioTransferenciaFechaSucursal($request->get('fecha_desde'), $request->get('fecha_hasta'),$request->get('sucursal'))->select('transferencia_fecha as fecha','transferencia_valor as Valor','diario_codigo as Diario')->get();
                $deposito=Diario::DiarioDepositoFechaSucursal($request->get('fecha_desde'), $request->get('fecha_hasta'),$request->get('sucursal'))->select('deposito_fecha as fecha','deposito_numero as Numero','deposito_valor as Valor','diario_codigo as Diario')->get();
                $notadebito=Diario::DiarioNotaDebitoFechaSucursal($request->get('fecha_desde'), $request->get('fecha_hasta'),$request->get('sucursal'))->select('nota_fecha as fecha','nota_numero as Numero','nota_valor as Valor','diario_codigo as Diario')->get();
                $notacredito=Diario::DiarioNotaCreditoFechaSucursal($request->get('fecha_desde'), $request->get('fecha_hasta'),$request->get('sucursal'))->select('nota_fecha as fecha','nota_numero as Numero','nota_valor as Valor','diario_codigo as Diario')->get();          
            }
              ////////////Fecha-Banco
              if ($request->get('fecha_todo') != "on"  && $request->get('sucursal') == "--TODOS--" && $request->get('tipo') == "--TODOS--" && $request->get('banco') != "--TODOS--" && $request->get('cuenta_id') == "--TODOS--") {
                $cheque=Diario::DiarioChequeFechaBanco($request->get('fecha_desde'), $request->get('fecha_hasta'),$request->get('banco'))->select('cheque_fecha_pago as fecha','cheque_numero as Numero','cheque_valor as Valor','diario_codigo as Diario')->get();
                $tranferencia=Diario::DiarioTransferenciaFechaBanco($request->get('fecha_desde'), $request->get('fecha_hasta'),$request->get('banco'))->select('transferencia_fecha as fecha','transferencia_valor as Valor','diario_codigo as Diario')->get();
                $deposito=Diario::DiarioDepositoFechaBanco($request->get('fecha_desde'), $request->get('fecha_hasta'),$request->get('banco'))->select('deposito_fecha as fecha','deposito_numero as Numero','deposito_valor as Valor','diario_codigo as Diario')->get();
                $notadebito=Diario::DiarioNotaDebitoFechaBanco($request->get('fecha_desde'), $request->get('fecha_hasta'),$request->get('banco'))->select('nota_fecha as fecha','nota_numero as Numero','nota_valor as Valor','diario_codigo as Diario')->get();
                $notacredito=Diario::DiarioNotaCreditoFechaBanco($request->get('fecha_desde'), $request->get('fecha_hasta'),$request->get('banco'))->select('nota_fecha as fecha','nota_numero as Numero','nota_valor as Valor','diario_codigo as Diario')->get();          
            }
              ////////////Fecha-Banco-Cuenta
              if ($request->get('fecha_todo') != "on"  && $request->get('sucursal') == "--TODOS--" && $request->get('tipo') == "--TODOS--" && $request->get('banco') != "--TODOS--" && $request->get('cuenta_id') != "--TODOS--") {
                $cheque=Diario::DiarioChequeFechaBancoCuenta($request->get('fecha_desde'), $request->get('fecha_hasta'),$request->get('banco'),$request->get('cuenta_id'))->select('cheque_fecha_pago as fecha','cheque_numero as Numero','cheque_valor as Valor','diario_codigo as Diario')->get();
                $tranferencia=Diario::DiarioTransferenciaFechaBancoCuenta($request->get('fecha_desde'), $request->get('fecha_hasta'),$request->get('banco'),$request->get('cuenta_id'))->select('transferencia_fecha as fecha','transferencia_valor as Valor','diario_codigo as Diario')->get();
                $deposito=Diario::DiarioDepositoFechaBancoCuenta($request->get('fecha_desde'), $request->get('fecha_hasta'),$request->get('banco'),$request->get('cuenta_id'))->select('deposito_fecha as fecha','deposito_numero as Numero','deposito_valor as Valor','diario_codigo as Diario')->get();
                $notadebito=Diario::DiarioNotaDebitoFechaBancoCuenta($request->get('fecha_desde'), $request->get('fecha_hasta'),$request->get('banco'),$request->get('cuenta_id'))->select('nota_fecha as fecha','nota_numero as Numero','nota_valor as Valor','diario_codigo as Diario')->get();
                $notacredito=Diario::DiarioNotaCreditoFechaBancoCuenta($request->get('fecha_desde'), $request->get('fecha_hasta'),$request->get('banco'),$request->get('cuenta_id'))->select('nota_fecha as fecha','nota_numero as Numero','nota_valor as Valor','diario_codigo as Diario')->get();          
            }
              ////////////Sucursal-Banco
              if ($request->get('fecha_todo') != "on"  && $request->get('sucursal') == "--TODOS--" && $request->get('tipo') == "--TODOS--" && $request->get('banco') != "--TODOS--" && $request->get('cuenta_id') == "--TODOS--") {
                $cheque=Diario::DiarioChequeSucursalBanco($request->get('sucursal'),$request->get('banco'))->select('cheque_fecha_pago as fecha','cheque_numero as Numero','cheque_valor as Valor','diario_codigo as Diario')->get();
                $tranferencia=Diario::DiarioTransferenciaSucursalBanco($request->get('sucursal'),$request->get('banco'))->select('transferencia_fecha as fecha','transferencia_valor as Valor','diario_codigo as Diario')->get();
                $deposito=Diario::DiarioDepositoSucursalBanco($request->get('sucursal'),$request->get('banco'))->select('deposito_fecha as fecha','deposito_numero as Numero','deposito_valor as Valor','diario_codigo as Diario')->get();
                $notadebito=Diario::DiarioNotaDebitoSucursalBanco($request->get('sucursal'),$request->get('banco'))->select('nota_fecha as fecha','nota_numero as Numero','nota_valor as Valor','diario_codigo as Diario')->get();
                $notacredito=Diario::DiarioNotaCreditoSucursalBanco($request->get('sucursal'),$request->get('banco'))->select('nota_fecha as fecha','nota_numero as Numero','nota_valor as Valor','diario_codigo as Diario')->get();          
            }
            ////////////Sucursal-Banco-Cuenta
            if ($request->get('fecha_todo') != "on"  && $request->get('sucursal') == "--TODOS--" && $request->get('tipo') == "--TODOS--" && $request->get('banco') != "--TODOS--" && $request->get('cuenta_id') != "--TODOS--") {
                $cheque=Diario::DiarioChequeSucursalBancoCuenta($request->get('sucursal'),$request->get('banco'),$request->get('cuenta_id'))->select('cheque_fecha_pago as fecha','cheque_numero as Numero','cheque_valor as Valor','diario_codigo as Diario')->get();
                $tranferencia=Diario::DiarioTransferenciaSucursalBancoCuenta($request->get('sucursal'),$request->get('banco'),$request->get('cuenta_id'))->select('transferencia_fecha as fecha','transferencia_valor as Valor','diario_codigo as Diario')->get();
                $deposito=Diario::DiarioDepositoSucursalBancoCuenta($request->get('sucursal'),$request->get('banco'),$request->get('cuenta_id'))->select('deposito_fecha as fecha','deposito_numero as Numero','deposito_valor as Valor','diario_codigo as Diario')->get();
                $notadebito=Diario::DiarioNotaDebitoSucursalBancoCuenta($request->get('sucursal'),$request->get('banco'),$request->get('cuenta_id'))->select('nota_fecha as fecha','nota_numero as Numero','nota_valor as Valor','diario_codigo as Diario')->get();
                $notacredito=Diario::DiarioNotaCreditoSucursalBancoCuenta($request->get('sucursal'),$request->get('banco'),$request->get('cuenta_id'))->select('nota_fecha as fecha','nota_numero as Numero','nota_valor as Valor','diario_codigo as Diario')->get();          
            }
             ////////////Fecha-Tipo
            if ($request->get('fecha_todo') != "on"  && $request->get('sucursal') == "--TODOS--" && $request->get('tipo') != "--TODOS--" && $request->get('banco') == "--TODOS--" && $request->get('cuenta_id') == "--TODOS--") {
                if ($request->get('tipo')=="Cheque") {
                    
                    $cheque=Diario::DiarioChequeFecha($request->get('fecha_desde'), $request->get('fecha_hasta'))->select('cheque_fecha_pago as fecha', 'cheque_numero as Numero', 'cheque_valor as Valor', 'diario_codigo as Diario')->get();
                }
                if($request->get('tipo')=="Transferencia"){
                $tranferencia=Diario::DiarioTransferenciaFecha($request->get('fecha_desde'),$request->get('fecha_hasta'))->select('transferencia_fecha as fecha','transferencia_valor as Valor','diario_codigo as Diario')->get();
                } 
                if ($request->get('tipo')=="Deposito") {
                    $deposito=Diario::DiarioDepositoFecha($request->get('fecha_desde'), $request->get('fecha_hasta'))->select('deposito_fecha as fecha', 'deposito_numero as Numero', 'deposito_valor as Valor', 'diario_codigo as Diario')->get();
                   
                }
                if ($request->get('tipo')=="Nota de Debito") {
                    $notadebito=Diario::DiarioNotaDebitoBancoSucursalbuscar($request->get('fecha_desde'), $request->get('fecha_hasta'))->select('nota_fecha as fecha', 'nota_numero as Numero', 'nota_valor as Valor', 'diario_codigo as Diario')->get();
                }
                if ($request->get('tipo')=="Nota de Credito") {
                    $notacredito=Diario::DiarioNotaCreditoBancoSucursalbuscar($request->get('fecha_desde'), $request->get('fecha_hasta'))->select('nota_fecha as fecha', 'nota_numero as Numero', 'nota_valor as Valor', 'diario_codigo as Diario')->get();
                }
            }
                ////////////Sucursal-Tipo
            if ($request->get('fecha_todo') == "on"  && $request->get('sucursal') != "--TODOS--" && $request->get('tipo') != "--TODOS--" && $request->get('banco') == "--TODOS--" && $request->get('cuenta_id') == "--TODOS--") {
                if ($request->get('tipo')=="Cheque") {
                    $cheque=Diario::DiarioChequeSucursal($request->get('sucursal'))->select('cheque_fecha_pago as fecha', 'cheque_numero as Numero', 'cheque_valor as Valor', 'diario_codigo as Diario')->get();
                }
                if($request->get('tipo')=="Transferencia"){
                $tranferencia=Diario::DiarioTransferenciaSucursal($request->get('sucursal'))->select('transferencia_fecha as fecha','transferencia_valor as Valor','diario_codigo as Diario')->get();
                } 
                if ($request->get('tipo')=="Deposito") {
                    $deposito=Diario::DiarioDepositoSucursal($request->get('sucursal'))->select('deposito_fecha as fecha', 'deposito_numero as Numero', 'deposito_valor as Valor', 'diario_codigo as Diario')->get();
                }
                if ($request->get('tipo')=="Nota de Debito") {
                    $notadebito=Diario::DiarioNotaDebitoSucursa($request->get('sucursal'))->select('nota_fecha as fecha', 'nota_numero as Numero', 'nota_valor as Valor', 'diario_codigo as Diario')->get();
                }
                if ($request->get('tipo')=="Nota de Credito") {
                    $notacredito=Diario::DiarioNotaCreditoSucursal( $request->get('sucursal'))->select('nota_fecha as fecha', 'nota_numero as Numero', 'nota_valor as Valor', 'diario_codigo as Diario')->get();
                }
            }
                ////////////Tipo-Banco
            if ($request->get('fecha_todo') == "on"  && $request->get('sucursal') == "--TODOS--" && $request->get('tipo') != "--TODOS--" && $request->get('banco') != "--TODOS--" && $request->get('cuenta_id') == "--TODOS--") {
              
                if ($request->get('tipo')=="Cheque") {
                   
                    $cheque=Diario::DiarioChequeBanco($request->get('banco'))->select('cheque_fecha_pago as fecha','cheque_numero as Numero','cheque_valor as Valor','diario_codigo as Diario')->get();
               
                }
                if($request->get('tipo')=="Transferencia"){
                    $tranferencia=Diario::DiarioTransferenciaBanco($request->get('banco'))->select('transferencia_fecha as fecha','transferencia_valor as Valor','diario_codigo as Diario')->get();
               
                } 
                if ($request->get('tipo')=="Deposito") {
                    $deposito=Diario::DiarioDepositoBanco($request->get('banco'))->select('deposito_fecha as fecha','deposito_numero as Numero','deposito_valor as Valor','diario_codigo as Diario')->get();
              
                }
                if ($request->get('tipo')=="Nota de Debito") {
                    $notadebito=Diario::DiarioNotaDebitoBanco($request->get('banco'))->select('nota_fecha as fecha','nota_numero as Numero','nota_valor as Valor','diario_codigo as Diario')->get();
            
                }
                if ($request->get('tipo')=="Nota de Credito") {
                    $notacredito=Diario::DiarioNotaCreditoBanco($request->get('banco'))->select('nota_fecha as fecha','nota_numero as Numero','nota_valor as Valor','diario_codigo as Diario')->get();
         
                }            
            }
                ////////////Tipo-Banco-Cuenta

            if ($request->get('fecha_todo') == "on"  && $request->get('sucursal') == "--TODOS--" && $request->get('tipo') != "--TODOS--" && $request->get('banco') != "--TODOS--" && $request->get('cuenta_id') != "--TODOS--") {
                if ($request->get('tipo')=="Cheque") {
                    $cheque=Diario::DiarioChequeBancoCuenta($request->get('banco'),$request->get('cuenta_id'))->select('cheque_fecha_pago as fecha','cheque_numero as Numero','cheque_valor as Valor','diario_codigo as Diario')->get();
                    
                }
                if($request->get('tipo')=="Transferencia"){
                    $tranferencia=Diario::DiarioTransferenciaCuenta($request->get('banco'),$request->get('cuenta_id'))->select('transferencia_fecha as fecha','transferencia_valor as Valor','diario_codigo as Diario')->get();
               
                } 
                if ($request->get('tipo')=="Deposito") {
                    $deposito=Diario::DiarioDepositoBancoCuenta($request->get('banco'),$request->get('cuenta_id'))->select('deposito_fecha as fecha','deposito_numero as Numero','deposito_valor as Valor','diario_codigo as Diario')->get();
              
                }
                if ($request->get('tipo')=="Nota de Debito") {
                    $notadebito=Diario::DiarioNotaDebitoBancoCuenta($request->get('banco'),$request->get('cuenta_id'))->select('nota_fecha as fecha','nota_numero as Numero','nota_valor as Valor','diario_codigo as Diario')->get();
            
                }
                if ($request->get('tipo')=="Nota de Credito") {
                    $notacredito=Diario::DiarioNotaCreditoBancoCuenta($request->get('banco'),$request->get('cuenta_id'))->select('nota_fecha as fecha','nota_numero as Numero','nota_valor as Valor','diario_codigo as Diario')->get();
                }            
            }
              ////////////Fecha-Sucursal-Tipo
            if ($request->get('fecha_todo') != "on"  && $request->get('sucursal') != "--TODOS--" && $request->get('tipo') != "--TODOS--" && $request->get('banco') == "--TODOS--" && $request->get('cuenta_id') == "--TODOS--") {
                
                if ($request->get('tipo')=="Cheque") {
                    $cheque=Diario::DiarioChequeFechaSucursal($request->get('fecha_desde'), $request->get('fecha_hasta'),$request->get('sucursal'))->select('cheque_fecha_pago as fecha','cheque_numero as Numero','cheque_valor as Valor','diario_codigo as Diario')->get();
               
                }
                if($request->get('tipo')=="Transferencia"){
                    $tranferencia=Diario::DiarioTransferenciaFechaSucursal($request->get('fecha_desde'), $request->get('fecha_hasta'),$request->get('sucursal'))->select('transferencia_fecha as fecha','transferencia_valor as Valor','diario_codigo as Diario')->get();
               
                } 
                if ($request->get('tipo')=="Deposito") {
                    $deposito=Diario::DiarioDepositoFechaSucursal($request->get('fecha_desde'), $request->get('fecha_hasta'),$request->get('sucursal'))->select('deposito_fecha as fecha','deposito_numero as Numero','deposito_valor as Valor','diario_codigo as Diario')->get();
              
                }
                if ($request->get('tipo')=="Nota de Debito") {
                    $notadebito=Diario::DiarioNotaDebitoFechaSucursal($request->get('fecha_desde'), $request->get('fecha_hasta'),$request->get('sucursal'))->select('nota_fecha as fecha','nota_numero as Numero','nota_valor as Valor','diario_codigo as Diario')->get();
            
                }
                if ($request->get('tipo')=="Nota de Credito") {
                    $notacredito=Diario::DiarioNotaCreditoFechaSucursal($request->get('fecha_desde'), $request->get('fecha_hasta'),$request->get('sucursal'))->select('nota_fecha as fecha','nota_numero as Numero','nota_valor as Valor','diario_codigo as Diario')->get();
                }            
            }
              ////////////Fecha-Banco-Tipo
              if ($request->get('fecha_todo') != "on"  && $request->get('sucursal') == "--TODOS--" && $request->get('tipo') != "--TODOS--" && $request->get('banco') != "--TODOS--" && $request->get('cuenta_id') == "--TODOS--") {
                if ($request->get('tipo')=="Cheque") {
                    $cheque=Diario::DiarioChequeFechaBanco($request->get('fecha_desde'), $request->get('fecha_hasta'),$request->get('banco'))->select('cheque_fecha_pago as fecha','cheque_numero as Numero','cheque_valor as Valor','diario_codigo as Diario')->get();
               
                }
                if($request->get('tipo')=="Transferencia"){
                    $tranferencia=Diario::DiarioTransferenciaFechaBanco($request->get('fecha_desde'), $request->get('fecha_hasta'),$request->get('banco'))->select('transferencia_fecha as fecha','transferencia_valor as Valor','diario_codigo as Diario')->get();
               
                } 
                if ($request->get('tipo')=="Deposito") {
                    $deposito=Diario::DiarioDepositoFechaBanco($request->get('fecha_desde'), $request->get('fecha_hasta'),$request->get('banco'))->select('deposito_fecha as fecha','deposito_numero as Numero','deposito_valor as Valor','diario_codigo as Diario')->get();
              
                }
                if ($request->get('tipo')=="Nota de Debito") {
                    $notadebito=Diario::DiarioNotaDebitoFechaBanco($request->get('fecha_desde'), $request->get('fecha_hasta'),$request->get('banco'))->select('nota_fecha as fecha','nota_numero as Numero','nota_valor as Valor','diario_codigo as Diario')->get();
            
                }
                if ($request->get('tipo')=="Nota de Credito") {
                    $notacredito=Diario::DiarioNotaCreditoFechaBanco($request->get('fecha_desde'), $request->get('fecha_hasta'),$request->get('banco'))->select('nota_fecha as fecha','nota_numero as Numero','nota_valor as Valor','diario_codigo as Diario')->get();
                }            
            }
             ////////////Fecha-Banco-Tipo-Cuenta
            if ($request->get('fecha_todo') != "on"  && $request->get('sucursal') == "--TODOS--" && $request->get('tipo') != "--TODOS--" && $request->get('banco') != "--TODOS--" && $request->get('cuenta_id') != "--TODOS--") {
                if ($request->get('tipo')=="Cheque") {
                    $cheque=Diario::DiarioChequeFechaBancoCuenta($request->get('fecha_desde'), $request->get('fecha_hasta'),$request->get('banco'),$request->get('cuenta_id'))->select('cheque_fecha_pago as fecha','cheque_numero as Numero','cheque_valor as Valor','diario_codigo as Diario')->get();
               
                }
                if($request->get('tipo')=="Transferencia"){
                    $tranferencia=Diario::DiarioTransferenciaFechaBancoCuenta($request->get('fecha_desde'), $request->get('fecha_hasta'),$request->get('banco'),$request->get('cuenta_id'))->select('transferencia_fecha as fecha','transferencia_valor as Valor','diario_codigo as Diario')->get();
               
                } 
                if ($request->get('tipo')=="Deposito") {
                    $deposito=Diario::DiarioDepositoFechaBancoCuenta($request->get('fecha_desde'), $request->get('fecha_hasta'),$request->get('banco'),$request->get('cuenta_id'))->select('deposito_fecha as fecha','deposito_numero as Numero','deposito_valor as Valor','diario_codigo as Diario')->get();
              
                }
                if ($request->get('tipo')=="Nota de Debito") {
                    $notadebito=Diario::DiarioNotaDebitoFechaBancoCuenta($request->get('fecha_desde'), $request->get('fecha_hasta'),$request->get('banco'),$request->get('cuenta_id'))->select('nota_fecha as fecha','nota_numero as Numero','nota_valor as Valor','diario_codigo as Diario')->get();
            
                }
                if ($request->get('tipo')=="Nota de Credito") {
                    $notacredito=Diario::DiarioNotaCreditoFechaBancoCuenta($request->get('fecha_desde'), $request->get('fecha_hasta'),$request->get('banco'),$request->get('cuenta_id'))->select('nota_fecha as fecha','nota_numero as Numero','nota_valor as Valor','diario_codigo as Diario')->get();
                }            
            }
              ////////////Sucursal-Banco-Tipo
              if ($request->get('fecha_todo') == "on"  && $request->get('sucursal') != "--TODOS--" && $request->get('tipo') != "--TODOS--" && $request->get('banco') != "--TODOS--" && $request->get('cuenta_id') == "--TODOS--") {
                if ($request->get('tipo')=="Cheque") {
                    $cheque=Diario::DiarioChequeSucursalBanco($request->get('sucursal'),$request->get('banco'))->select('cheque_fecha_pago as fecha','cheque_numero as Numero','cheque_valor as Valor','diario_codigo as Diario')->get();
               
                }
                if($request->get('tipo')=="Transferencia"){
                    $tranferencia=Diario::DiarioTransferenciaSucursalBanco($request->get('sucursal'),$request->get('banco'))->select('transferencia_fecha as fecha','transferencia_valor as Valor','diario_codigo as Diario')->get();
               
                } 
                if ($request->get('tipo')=="Deposito") {
                    $deposito=Diario::DiarioDepositoSucursalBanco($request->get('sucursal'),$request->get('banco'))->select('deposito_fecha as fecha','deposito_numero as Numero','deposito_valor as Valor','diario_codigo as Diario')->get();
              
                }
                if ($request->get('tipo')=="Nota de Debito") {
                    $notadebito=Diario::DiarioNotaDebitoSucursalBanco($request->get('sucursal'),$request->get('banco'))->select('nota_fecha as fecha','nota_numero as Numero','nota_valor as Valor','diario_codigo as Diario')->get();
            
                }
                if ($request->get('tipo')=="Nota de Credito") {
                    $notacredito=Diario::DiarioNotaCreditoSucursalBanco($request->get('sucursal'),$request->get('banco'))->select('nota_fecha as fecha','nota_numero as Numero','nota_valor as Valor','diario_codigo as Diario')->get();
                }            
            }
              ////////////Sucursal-Banco-Cuenta-Tipo
            if ($request->get('fecha_todo') == "on"  && $request->get('sucursal') != "--TODOS--" && $request->get('tipo') != "--TODOS--" && $request->get('banco') != "--TODOS--" && $request->get('cuenta_id') != "--TODOS--") {
                if ($request->get('tipo')=="Cheque") {
                    $cheque=Diario::DiarioChequeSucursalBancoCuenta($request->get('sucursal'),$request->get('banco'),$request->get('cuenta_id'))->select('cheque_fecha_pago as fecha','cheque_numero as Numero','cheque_valor as Valor','diario_codigo as Diario')->get();
               
                }
                if($request->get('tipo')=="Transferencia"){
                    $tranferencia=Diario::DiarioTransferenciaSucursalBancoCuenta($request->get('sucursal'),$request->get('banco'),$request->get('cuenta_id'))->select('transferencia_fecha as fecha','transferencia_valor as Valor','diario_codigo as Diario')->get();
               
                } 
                if ($request->get('tipo')=="Deposito") {
                    $deposito=Diario::DiarioDepositoSucursalBancoCuenta($request->get('sucursal'),$request->get('banco'),$request->get('cuenta_id'))->select('deposito_fecha as fecha','deposito_numero as Numero','deposito_valor as Valor','diario_codigo as Diario')->get();
              
                }
                if ($request->get('tipo')=="Nota de Debito") {
                    $notadebito=Diario::DiarioNotaDebitoSucursalBancoCuenta($request->get('sucursal'),$request->get('banco'),$request->get('cuenta_id'))->select('nota_fecha as fecha','nota_numero as Numero','nota_valor as Valor','diario_codigo as Diario')->get();
            
                }
                if ($request->get('tipo')=="Nota de Credito") {
                    $notacredito=Diario::DiarioNotaCreditoSucursalBancoCuenta($request->get('sucursal'),$request->get('banco'),$request->get('cuenta_id'))->select('nota_fecha as fecha','nota_numero as Numero','nota_valor as Valor','diario_codigo as Diario')->get();
                }            
            }
            ////////////Fehca-Sucursal-Tipo-Banco
            if ($request->get('fecha_todo') != "on"  && $request->get('sucursal') != "--TODOS--" && $request->get('tipo') != "--TODOS--" && $request->get('banco') != "--TODOS--" && $request->get('cuenta_id') == "--TODOS--") {
                if ($request->get('tipo')=="Cheque") {
                    $cheque=Diario::DiarioChequeFechaSucursalBanco($request->get('fecha_desde'), $request->get('fecha_hasta'),$request->get('sucursal'),$request->get('banco'))->select('cheque_fecha_pago as fecha','cheque_numero as Numero','cheque_valor as Valor','diario_codigo as Diario')->get();
               
                }
                if($request->get('tipo')=="Transferencia"){
                    $tranferencia=Diario::DiarioTransferenciaFechaSucursalBanco($request->get('fecha_desde'), $request->get('fecha_hasta'),$request->get('sucursal'),$request->get('banco'))->select('transferencia_fecha as fecha','transferencia_valor as Valor','diario_codigo as Diario')->get();
               
                } 
                if ($request->get('tipo')=="Deposito") {
                    $deposito=Diario::DiarioDepositoFechaSucursalBanco($request->get('fecha_desde'), $request->get('fecha_hasta'),$request->get('sucursal'),$request->get('banco'))->select('deposito_fecha as fecha','deposito_numero as Numero','deposito_valor as Valor','diario_codigo as Diario')->get();
              
                }
                if ($request->get('tipo')=="Nota de Debito") {
                    $notadebito=Diario::DiarioNotaDebitoFechaSucursalBanco($request->get('fecha_desde'), $request->get('fecha_hasta'),$request->get('sucursal'),$request->get('banco'))->select('nota_fecha as fecha','nota_numero as Numero','nota_valor as Valor','diario_codigo as Diario')->get();
            
                }
                if ($request->get('tipo')=="Nota de Credito") {
                    $notacredito=Diario::DiarioNotaCreditoFechaSucursalBanco($request->get('fecha_desde'), $request->get('fecha_hasta'),$request->get('sucursal'),$request->get('banco'))->select('nota_fecha as fecha','nota_numero as Numero','nota_valor as Valor','diario_codigo as Diario')->get();
                }            
            }
            return view('admin.bancos.reporteBancario.index',['cuentas'=>$cuentas,'idcuenta'=>$request->get('cuenta_id'),'idbanco'=>$request->get('banco'),'idtipo'=>$request->get('tipo'),'fecha_desde'=>$request->get('fecha_desde'),'fecha_hasta'=>$request->get('fecha_hasta'),'idsucursal'=>$request->get('sucursal'),'fecha_todo'=>$request->get('fecha_todo'),'notacredito'=>$notacredito,'notadebito'=>$notadebito,'depositos'=>$deposito,'tranferencia'=>$tranferencia,'cheque'=>$cheque,'bancos'=>$bancos,'sucursal'=>$sucursal,'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);           
        }catch(\Exception $ex){
           
            return redirect('reporteBancario')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
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
