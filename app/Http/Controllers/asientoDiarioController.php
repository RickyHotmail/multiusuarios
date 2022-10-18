<?php

namespace App\Http\Controllers;

use App\Models\Cuenta;
use App\Models\Detalle_Diario;
use App\Models\Diario;
use App\Models\Empleado;
use App\Models\Empresa;
use App\Models\Punto_Emision;
use App\Models\Sucursal;
use DateTime;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Matrix\Functions;
use PDF;

class asientoDiarioController extends Controller
{
    public function nuevo()
    {
        try{ 
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            return view('admin.contabilidad.asientoDiario.index',['sucursales'=>Sucursal::sucursales()->get(),'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        }catch(\Exception $ex){
         
            return redirect('inicio')->with('error','Ocurrio un error vuelva a intentarlo');
        }
    }
    public function consultar(Request $request){
        if (isset($_POST['buscar'])){
            return $this->buscar($request);
        }
        if (isset($_POST['radioDiario'])){
            return $this->seleccionar($request);
        }
        
    }
    public function seleccionar(Request $request){
        try{
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $diarios = Diario::DiarioByBuscar($request->get('fecha_desde'),$request->get('fecha_hasta'),$request->get('IdBuscar'),$request->get('sucursal_id'))->get();
            return view('admin.contabilidad.asientoDiario.index',['diarioS'=>Diario::findOrFail($request->get('radioDiario')),'diarios'=>$diarios,'sucurslaC'=>$request->get('sucursal_id'),'b'=>$request->get('IdBuscar'),'fecI'=>$request->get('fecha_desde'),'fecF'=>$request->get('fecha_hasta'),'sucursales'=>Sucursal::sucursales()->get(),'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        }catch(\Exception $ex){
            return redirect('asientoDiario')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function buscar(Request $request){
        try{
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $diarios = Diario::DiarioByBuscar($request->get('fecha_desde'),$request->get('fecha_hasta'),$request->get('IdBuscar'),$request->get('sucursal_id'))->get();
            return view('admin.contabilidad.asientoDiario.index',['diarios'=>$diarios,'sucurslaC'=>$request->get('sucursal_id'),'b'=>$request->get('IdBuscar'),'fecI'=>$request->get('fecha_desde'),'fecF'=>$request->get('fecha_hasta'),'sucursales'=>Sucursal::sucursales()->get(),'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        }catch(\Exception $ex){
            return redirect('asientoDiario')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function listar(){
        try{
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            return view('admin.contabilidad.asientoDiario.listaAsientosAjuste',['sucursales'=>Sucursal::sucursales()->get(),'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        }catch(\Exception $ex){
         
            return redirect('inicio')->with('error','Ocurrio un error vuelva a intentarlo');
        }
    }
    public function buscarLista(){
        try{
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            return view('admin.contabilidad.asientoDiario.editar',['cuentas'=>Cuenta::CuentasMovimiento()->get(),'sucursales'=>Sucursal::sucursales()->get(),'diario'=>Diario::findOrFail($id),'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        }catch(\Exception $ex){
         
            return redirect('inicio')->with('error','Ocurrio un error vuelva a intentarlo');
        }
    }
    public function editar($id){
        try{
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            return view('admin.contabilidad.asientoDiario.editar',['cuentas'=>Cuenta::CuentasMovimiento()->get(),'sucursales'=>Sucursal::sucursales()->get(),'diario'=>Diario::findOrFail($id),'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        }catch(\Exception $ex){
         
            return redirect('inicio')->with('error','Ocurrio un error vuelva a intentarlo');
        }
    }
    public function editarD($id){
        try{
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            return view('admin.contabilidad.asientoDiario.editarDescuadrado',['cuentas'=>Cuenta::CuentasMovimiento()->get(),'sucursales'=>Sucursal::sucursales()->get(),'diario'=>Diario::findOrFail($id),'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        }catch(\Exception $ex){
          
            return redirect('inicio')->with('error','Ocurrio un error vuelva a intentarlo');
        }
    }
    public function guardarAsientoEditado(Request $request){
        try{
            DB::beginTransaction();
            $general = new generalController();
            $diario =  Diario::findOrFail($request->get('IdDiario'));
            $general = new generalController();
            $cierre = $general->cierre($diario->diario_fecha,$diario->sucursal_id);
            if($cierre){
                return redirect('/asientoDiario')->with('error2','No puede realizar la operacion por que pertenece a un mes bloqueado');
            }
            $cheque = null;
            $deposito = null;
            $transferencia = null;
            $voucher = null;

            $cliente = null;
            $proveedor = null;
            $empleado = null;
            $movimientoProducto = null;

            $detalleAux = null;
            $detalleAux2 = null;
            $comentario = '';
            foreach($diario->detalles as $detalle){
                if($detalle->cheque){
                    $detalleAux = $detalle;
                    $cheque = $detalle->cheque;
                    $detalle->cheque_id = null;
                    $detalle->update();
                    $comentario = 'Falta la cuenta del banco al que pertenece el cheque.';
                }
                if($detalle->transferencia){
                    $detalleAux = $detalle;
                    $transferencia = $detalle->transferencia;
                    $detalle->transferencia_id = null;
                    $detalle->update();
                    $comentario = 'Falta la cuenta del banco de la transferencia.';
                }
                if($detalle->deposito){
                    $detalleAux = $detalle;
                    $deposito = $detalle->deposito;
                    $detalle->deposito_id = null;
                    $detalle->update();
                    $comentario = 'Falta la cuenta del banco al que pertenece el deposito.';
                }
                if($detalle->voucher){
                    $detalleAux = $detalle;
                    $voucher = $detalle->voucher;
                    $detalle->voucher_id = null;
                    $detalle->update();
                    $comentario = 'Falta la cuenta al que pertenece el voucher.';
                }

                if($detalle->cliente){
                    $detalleAux2 = $detalle;
                    $cliente = $detalle->cliente;
                    $detalle->cliente_id = null;
                    $detalle->update();
                    $comentario = 'Falta la cuenta del cliente en el asiento contable.';
                }
                if($detalle->proveedor){
                    $detalleAux2 = $detalle;
                    $proveedor = $detalle->proveedor;
                    $detalle->proveedor_id = null;
                    $detalle->update();
                    $comentario = 'Falta la cuenta del proveedor en el asiento contable.';
                }
                if($detalle->empleado){
                    $detalleAux2 = $detalle;
                    $empleado = $detalle->empleado;
                    $detalle->empleado_id = null;
                    $detalle->update();
                    $comentario = 'Falta la cuenta del empleado en el asiento contable.';
                }
                if($detalle->movimientoProducto){
                    $detalleAux2 = $detalle;
                    $movimientoProducto = $detalle->movimientoProducto;
                    $detalle->movimiento_id = null;
                    $detalle->update();
                    $comentario = 'Falta la cuenta del movimiento del producto en el asiento contable.';
                }
            }
            $ban = true;
            $detalleEliminar=Detalle_Diario::where('diario_id','=',$diario->diario_id)->delete();
            $cuentaId = $request->get('DidCuenta');
            $debe = $request->get('Ddebe');
            $haber = $request->get('Dhaber');
            $descripcion = $request->get('Ddescripcion');
            for ($i = 2; $i < count($cuentaId); ++$i){
                if($cuentaId[$i] <> ''){
                    $detalleDiario = new Detalle_Diario();
                    $detalleDiario->detalle_debe = $debe[$i];
                    $detalleDiario->detalle_haber = $haber[$i];
                    $detalleDiario->detalle_comentario = $descripcion[$i];;
                    $detalleDiario->detalle_tipo_documento = 'DIARIO CONTABLE';
                    $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                    $detalleDiario->detalle_conciliacion = '0';
                    $detalleDiario->detalle_estado = '1';           
                    $detalleDiario->cuenta_id = $cuentaId[$i];
                    if($detalleAux){
                        if($detalleDiario->cuenta_id == $detalleAux->cuenta_id){
                            $ban = false;
                            if($detalleAux->cheque){
                                $detalleDiario->cheque()->associate($cheque);
                            }
                            if($detalleAux->transferencia){
                                $detalleDiario->transferencia()->associate($transferencia);
                            }
                            if($detalleAux->deposito){
                                $detalleDiario->deposito()->associate($deposito);
                            }
                            if($detalleAux->voucher){
                                $detalleDiario->voucher()->associate($voucher);
                            }
                        }
                    }else{
                        $ban = false;
                    }
                    if($detalleAux2){
                        if($detalleDiario->cuenta_id == $detalleAux2->cuenta_id){
                            $ban = false;
                            if($detalleAux2->cliente){
                                $detalleDiario->cliente()->associate($cliente);
                            }
                            if($detalleAux2->proveedor){
                                $detalleDiario->proveedor()->associate($proveedor);
                            }
                            if($detalleAux2->empleado){
                                $detalleDiario->empleado()->associate($empleado);
                            }
                            if($detalleAux2->movimientoProducto){
                                if(Auth::user()->empresa->empresa_contabilidad== '1'){
                                    $detalleDiario->associate($movimientoProducto);
                                }
                            }
                    }else{
                        $ban = false;
                    }
                    }
                    $diario->detalles()->save($detalleDiario);
                    $general->registrarAuditoria('Registro de Detalle de Diario codigo: -> '.$diario->diario_codigo, $diario->diario_codigo,'');
                }  
            }
            if($ban){
                throw new Exception($comentario);
            }
            $diario =  Diario::findOrFail($request->get('IdDiario'));
            $diario->sucursal_id = $request->get('sucursal_id');
            $diario->diario_numero_documento=$request->get('IdNumero');
            $diario->diario_comentario=$request->get('IdComentario');
            $diario->diario_beneficiario=$request->get('IdBeneficiario');
            $diario->save();
            $url = $general->pdfDiario($diario);
            DB::commit();
            return redirect('/asientoDiario')->with('success','Datos guardados exitosamente')->with('diario',$url);
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('/asientoDiario')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function guardarDescuadrado(Request $request){
        try{
            DB::beginTransaction();
            $general = new generalController();
            $diario =  Diario::findOrFail($request->get('IdDiario'));
            $general = new generalController();
            $cierre = $general->cierre($diario->diario_fecha,$diario->sucursal_id);
            if($cierre){
                return redirect('/asientoDiario/descuadrados')->with('error2','No puede realizar la operacion por que pertenece a un mes bloqueado');
            }
            $cheque = null;
            $deposito = null;
            $transferencia = null;
            $voucher = null;

            $cliente = null;
            $proveedor = null;
            $empleado = null;
            $movimientoProducto = null;

            $detalleAux = null;
            $detalleAux2 = null;
            $comentario = '';
            foreach($diario->detalles as $detalle){
                if($detalle->cheque){
                    $detalleAux = $detalle;
                    $cheque = $detalle->cheque;
                    $detalle->cheque_id = null;
                    $detalle->update();
                    $comentario = 'Falta la cuenta del banco al que pertenece el cheque.';
                }
                if($detalle->transferencia){
                    $detalleAux = $detalle;
                    $transferencia = $detalle->transferencia;
                    $detalle->transferencia_id = null;
                    $detalle->update();
                    $comentario = 'Falta la cuenta del banco de la transferencia.';
                }
                if($detalle->deposito){
                    $detalleAux = $detalle;
                    $deposito = $detalle->deposito;
                    $detalle->deposito_id = null;
                    $detalle->update();
                    $comentario = 'Falta la cuenta del banco al que pertenece el deposito.';
                }
                if($detalle->voucher){
                    $detalleAux = $detalle;
                    $voucher = $detalle->voucher;
                    $detalle->voucher_id = null;
                    $detalle->update();
                    $comentario = 'Falta la cuenta al que pertenece el voucher.';
                }

                if($detalle->cliente){
                    $detalleAux2 = $detalle;
                    $cliente = $detalle->cliente;
                    $detalle->cliente_id = null;
                    $detalle->update();
                    $comentario = 'Falta la cuenta del cliente en el asiento contable.';
                }
                if($detalle->proveedor){
                    $detalleAux2 = $detalle;
                    $proveedor = $detalle->proveedor;
                    $detalle->proveedor_id = null;
                    $detalle->update();
                    $comentario = 'Falta la cuenta del proveedor en el asiento contable.';
                }
                if($detalle->empleado){
                    $detalleAux2 = $detalle;
                    $empleado = $detalle->empleado;
                    $detalle->empleado_id = null;
                    $detalle->update();
                    $comentario = 'Falta la cuenta del empleado en el asiento contable.';
                }
                if($detalle->movimientoProducto){
                    $detalleAux2 = $detalle;
                    $movimientoProducto = $detalle->movimientoProducto;
                    $detalle->movimiento_id = null;
                    $detalle->update();
                    $comentario = 'Falta la cuenta del movimiento del producto en el asiento contable.';
                }
            }
            $ban = true;
            $detalleEliminar=Detalle_Diario::where('diario_id','=',$diario->diario_id)->delete();
            $cuentaId = $request->get('DidCuenta');
            $debe = $request->get('Ddebe');
            $haber = $request->get('Dhaber');
            $descripcion = $request->get('Ddescripcion');
            for ($i = 2; $i < count($cuentaId); ++$i){
                if($cuentaId[$i] <> ''){
                    $detalleDiario = new Detalle_Diario();
                    $detalleDiario->detalle_debe = $debe[$i];
                    $detalleDiario->detalle_haber = $haber[$i];
                    $detalleDiario->detalle_comentario = $descripcion[$i];;
                    $detalleDiario->detalle_tipo_documento = 'DIARIO CONTABLE';
                    $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                    $detalleDiario->detalle_conciliacion = '0';
                    $detalleDiario->detalle_estado = '1';           
                    $detalleDiario->cuenta_id = $cuentaId[$i];
                    if($detalleAux){
                        if($detalleDiario->cuenta_id == $detalleAux->cuenta_id){
                            $ban = false;
                            if($detalleAux->cheque){
                                $detalleDiario->cheque()->associate($cheque);
                            }
                            if($detalleAux->transferencia){
                                $detalleDiario->transferencia()->associate($transferencia);
                            }
                            if($detalleAux->deposito){
                                $detalleDiario->deposito()->associate($deposito);
                            }
                            if($detalleAux->voucher){
                                $detalleDiario->voucher()->associate($voucher);
                            }
                        }
                    }else{
                        $ban = false;
                    }
                    if($detalleAux2){
                        if($detalleDiario->cuenta_id == $detalleAux2->cuenta_id){
                            $ban = false;
                            if($detalleAux2->cliente){
                                $detalleDiario->cliente()->associate($cliente);
                            }
                            if($detalleAux2->proveedor){
                                $detalleDiario->proveedor()->associate($proveedor);
                            }
                            if($detalleAux2->empleado){
                                $detalleDiario->empleado()->associate($empleado);
                            }
                            if($detalleAux2->movimientoProducto){
                                $detalleDiario->movimientoProducto()->associate($movimientoProducto);
                            }
                        }
                    }else{
                        $ban = false;
                    }
                    $diario->detalles()->save($detalleDiario);
                    $general->registrarAuditoria('Registro de Detalle de Diario codigo: -> '.$diario->diario_codigo, $diario->diario_codigo,'');
                }
            }  
            if($ban){
                throw new Exception($comentario);
            }
            $diario =  Diario::findOrFail($request->get('IdDiario'));
            $url = $general->pdfDiario($diario);
            DB::commit();
            return redirect('/asientoDiario/descuadrados')->with('success','Datos guardados exitosamente')->with('diario',$url);
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('/asientoDiario/descuadrados')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function verEliminar($id){
        try{
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            return view('admin.contabilidad.asientoDiario.eliminarDiario',['diarioS'=>Diario::findOrFail($id),'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        }catch(\Exception $ex){
            return redirect('asientoDiario')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function eliminar(Request $request){
        try{
            DB::beginTransaction();
            $general = new generalController();
            $diario =  Diario::findOrFail($request->get('IdDiario'));
            $cierre = $general->cierre($diario->diario_fecha,$diario->sucursal_id);
            if($cierre){
                return redirect('/asientoDiario')->with('error2','No puede realizar la operacion por que pertenece a un mes bloqueado');
            }
            foreach($diario->detalles as $detalle){
                $detalle->delete();
                $general->registrarAuditoria('Eliminacion de detalle deDiario codigo: -> '.$diario->diario_codigo, $diario->diario_codigo,'');
            }
            $diario->delete();
            $general->registrarAuditoria('Eliminacion de Diario codigo: -> '.$diario->diario_codigo, $diario->diario_codigo,'');
            DB::commit();
            return redirect('/asientoDiario')->with('success','Datos eliminados exitosamente');
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('/asientoDiario')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function ver($id){
        try{
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $diario = Diario::DiarioCodigo($id)->first();
            if(!$diario){
                $diario = Diario::findOrFail($id);
            }
            return view('admin.contabilidad.asientoDiario.ver',['diario'=>$diario,'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        }catch(\Exception $ex){
           
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function verComprabanteegreso($id){
        try{
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $diario = Diario::DiarioCodigo($id)->first();
            if(!$diario){
                $diario = Diario::findOrFail($id);
            }
            return view('admin.contabilidad.asientoDiario.verEgreso',['diario'=>$diario,'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        }catch(\Exception $ex){
           
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function imprimir($id){
        try{
            $diario = Diario::findOrFail($id);
            $empresa = Empresa::empresa()->first();
            $ruta = public_path().'/DIARIOS/'.$empresa->empresa_ruc.'/'.DateTime::createFromFormat('Y-m-d', $diario->diario_fecha)->format('d-m-Y');
            if (!is_dir($ruta)) {
                mkdir($ruta, 0777, true);
            }
            $nombreArchivo = $diario->diario_codigo. ".pdf";
            $view =  \View::make('admin.formatosPDF.diario', ['empresa'=> $empresa,'diario'=> $diario]);
            return PDF::loadHTML($view)->save($ruta.'/'.$nombreArchivo)->stream('diario.pdf');
        }catch(\Exception $ex){
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function imprimirEgreso($id){
        try{
            $diario = Diario::findOrFail($id);
            $empleado=null;
            foreach($diario->detalles as $detalle){
                if(isset($detalle->empleado_id)){
                    $empleado=Empleado::findOrFail($detalle->empleado_id);
                } 
            }
            $empresa = Empresa::empresa()->first();
            $ruta = public_path().'/DIARIOS/'.$empresa->empresa_ruc.'/'.DateTime::createFromFormat('Y-m-d', $diario->diario_fecha)->format('d-m-Y');
            if (!is_dir($ruta)) {
                mkdir($ruta, 0777, true);
            }
            $nombreArchivo = $diario->diario_codigo. ".pdf";
            $view =  \View::make('admin.formatosPDF.diarioEgreso', ['empresa'=> $empresa,'diario'=> $diario,'empleado'=> $empleado]);
            return PDF::loadHTML($view)->save($ruta.'/'.$nombreArchivo)->stream('diario.pdf');
        }catch(\Exception $ex){
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function diarioById($id){
        return Diario::Diario($id)->join('detalle_diario','detalle_diario.diario_id','=','diario.diario_id')->join('cuenta','cuenta.cuenta_id','=','detalle_diario.cuenta_id')->get();
    }
    public function descuadradosIndex(){
        try{
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            return view('admin.contabilidad.asientoDiario.descuadrados',['sucursales'=>Sucursal::sucursales()->get(),'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        }catch(\Exception $ex){
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
        
    }
    public function descuadrados(Request $request){
        try{
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            
            /*
            $diarios = Diario::DiariosDescuadrados($request->get('sucursal_id'),$request->get('fecha_desde'),$request->get('fecha_hasta'))
            ->select('diario.diario_id','diario.diario_fecha','diario.diario_codigo',DB::raw("(select sum(detalle_diario.detalle_debe) from detalle_diario where diario.diario_id = detalle_diario.diario_id ) as debe"),DB::raw("(select sum(detalle_diario.detalle_haber) from detalle_diario where diario.diario_id = detalle_diario.diario_id ) as haber"),'diario.diario_beneficiario','diario.diario_tipo_documento','diario.diario_numero_documento','diario.diario_referencia','diario.diario_comentario')
            ->orhavingRaw("((select sum(detalle_diario.detalle_debe) from detalle_diario where diario.diario_id = detalle_diario.diario_id)-(select sum(detalle_diario.detalle_haber) from detalle_diario where diario.diario_id = detalle_diario.diario_id )) <> 0")->groupBy('diario.diario_id')->get();
            */

            /**/
            $diarios=DB::select(DB::raw("select * from (
                select dr.diario_id, sum(dt.detalle_debe)-sum(dt.detalle_haber) as diff, dr.diario_fecha, dr.diario_codigo, sum(dt.detalle_debe) as debe, sum(dt.detalle_haber) as haber,
                dr.diario_beneficiario, dr.diario_tipo_documento, dr.diario_numero_documento, dr.diario_referencia, dr.diario_comentario 
                from diario as dr, detalle_diario as dt 
                where dr.diario_id=dt.diario_id and dr.empresa_id =".Auth::user()->empresa_id." and dr.diario_fecha >= '".$request->get('fecha_desde')."' and dr.diario_fecha <= '".$request->get('fecha_hasta')."'
                group by dr.diario_id) as busqueda where diff<>0 order by diario_id"));
            /**/
            //return 'diarios '.count($diarios);


            return view('admin.contabilidad.asientoDiario.descuadrados',['diarios'=>$diarios,'sucurslaC'=>$request->get('sucursal_id'),'fecI'=>$request->get('fecha_desde'),'fecF'=>$request->get('fecha_hasta'),'sucursales'=>Sucursal::sucursales()->get(),'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        }catch(\Exception $ex){
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function asientoAjusteIndex(){
        try{
            $gruposPermiso=DB::table('usuario_rol')->select('grupo_permiso.grupo_id', 'grupo_nombre', 'grupo_icono','grupo_orden','grupo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('grupo_permiso','grupo_permiso.grupo_id','=','permiso.grupo_id')->join('tipo_grupo','tipo_grupo.grupo_id','=','grupo_permiso.grupo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('grupo_orden','asc')->distinct()->get();
            $tipoPermiso=DB::table('usuario_rol')->select('tipo_grupo.grupo_id','tipo_grupo.tipo_id', 'tipo_nombre','tipo_icono','tipo_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->join('tipo_grupo','tipo_grupo.tipo_id','=','permiso.tipo_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('tipo_orden','asc')->distinct()->get();
    $permisosAdmin=DB::table('usuario_rol')->select('permiso_ruta', 'permiso_nombre', 'permiso_icono', 'tipo_id', 'grupo_id', 'permiso_orden')->join('rol_permiso','usuario_rol.rol_id','=','rol_permiso.rol_id')->join('permiso','permiso.permiso_id','=','rol_permiso.permiso_id')->where('permiso_estado','=','1')->where('usuario_rol.user_id','=',Auth::user()->user_id)->orderBy('permiso_orden','asc')->get();
            $general = new generalController();
            return view('admin.contabilidad.asientoDiario.asientoAjuste',['cuentas'=>Cuenta::CuentasMovimiento()->get(),'codigo'=>$general->generarCodigoDiario(date("Y")."-".date("m")."-".date("d"),'CDCO'),'sucursales'=>Sucursal::sucursales()->get(),'PE'=>Punto_Emision::puntos()->get(),'tipoPermiso'=>$tipoPermiso,'gruposPermiso'=>$gruposPermiso, 'permisosAdmin'=>$permisosAdmin]);
        }catch(\Exception $ex){
            return redirect('inicio')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function asientoAjusteGuardar(Request $request){
        try{
            DB::beginTransaction();
            $general = new generalController();
            $diario = new Diario();
            $general = new generalController();
            
            $cierre = $general->cierre($request->get('IdFecha'),$request->get('sucursal_id'));
            if($cierre){
                return redirect('/asientoDiario/asientoAjuste')->with('error2','No puede realizar la operacion por que pertenece a un mes bloqueado');
            }
            $diario->diario_codigo = $request->get('IdCodigo');
            $diario->diario_fecha = $request->get('IdFecha');
            $diario->diario_referencia = $request->get('IdReferencia');
            $diario->diario_tipo_documento = $request->get('IdTipoDocumento');
            $diario->diario_numero_documento = $request->get('IdNumero');
            $diario->diario_beneficiario = $request->get('IdBeneficiario');
            $diario->diario_tipo = 'CDCO';
            $diario->diario_secuencial = substr($diario->diario_codigo, 8);
            $diario->diario_mes = DateTime::createFromFormat('Y-m-d', $request->get('IdFecha'))->format('m');
            $diario->diario_ano = DateTime::createFromFormat('Y-m-d', $request->get('IdFecha'))->format('Y');
            $diario->diario_comentario = $request->get('IdComentario');
            $diario->diario_cierre = '0';
            $diario->diario_estado = '1';
            $diario->empresa_id = Auth::user()->empresa_id;
            $diario->sucursal_id = $request->get('sucursal_id');
            $diario->save();
            $general->registrarAuditoria('Registro de Diario de Diario codigo: -> '.$diario->diario_codigo, $diario->diario_codigo,'Tipo de Diario -> '.$diario->diario_referencia.'');
            $cuentaId = $request->get('DidCuenta');
            $debe = $request->get('Ddebe');
            $haber = $request->get('Dhaber');
            $descripcion = $request->get('Ddescripcion');
            for ($i = 2; $i < count($cuentaId); ++$i){
                if($cuentaId[$i] <> ''){
                    $detalleDiario = new Detalle_Diario();
                    $detalleDiario->detalle_debe = $debe[$i];
                    $detalleDiario->detalle_haber = $haber[$i];
                    $detalleDiario->detalle_comentario = $descripcion[$i];;
                    $detalleDiario->detalle_tipo_documento = 'DIARIO CONTABLE';
                    $detalleDiario->detalle_numero_documento = $diario->diario_numero_documento;
                    $detalleDiario->detalle_conciliacion = '0';
                    $detalleDiario->detalle_estado = '1';           
                    $detalleDiario->cuenta_id = $cuentaId[$i];
                    $diario->detalles()->save($detalleDiario);
                    $general->registrarAuditoria('Registro de Detalle de Diario codigo: -> '.$diario->diario_codigo, $diario->diario_codigo,'');
                }
            }          
            $url = $general->pdfDiario($diario);
            DB::commit();
            return redirect('/asientoDiario/asientoAjuste')->with('success','Datos guardados exitosamente')->with('diario',$url);
        }catch(\Exception $ex){
            DB::rollBack();
            return redirect('/asientoDiario/asientoAjuste')->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }
    public function diarioCodigo($fecha){
        $general = new generalController();
        return $general->generarCodigoDiario($fecha,'CDCO');
    }
    public function diarioByCodigo($id){
        return Diario::Diario($id)->first();
    }
}
