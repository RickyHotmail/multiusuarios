<?php

namespace App\Http\Controllers;

use App\Models\Suscripcion;
use App\Models\Banco_Lista;
use App\Models\Plan;
use App\Models\Pago;
use App\Http\Controllers\Controller;
use App\Models\Bodega;
use App\Models\Caja;
use App\Models\Categoria_Cliente;
use App\Models\Categoria_Producto;
use App\Models\Categoria_Proveedor;
use App\Models\Ciudad;
use App\Models\Cliente;
use App\Models\Credito;
use App\Models\Email_Empresa;
use App\Models\Empresa;
use App\Models\Grupo_Producto;
use App\Models\Marca_Producto;
use App\Models\Pais;
use App\Models\Parametrizacion_Permiso;
use App\Models\Provincia;
use App\Models\Punto_Emision;
use App\Models\Rango_Documento;
use App\Models\Rol;
use App\Models\Rol_Permiso;
use App\Models\sucursal;
use App\Models\Tamano_Producto;
use App\Models\Tarifa_Iva;
use App\Models\Tipo_Cliente;
use App\Models\Tipo_Identificacion;
use App\Models\Unidad_Medida_Producto;
use App\Models\User;
use App\Models\Usuario_PuntoE;
use App\Models\Usuario_Rol;
use App\Models\Vendedor;
use App\Models\Zona;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;

class SuscripcionController extends Controller{
    public function index(){
        $suscripcion=Auth::user()->empresa->suscripcion;
        $hoy = date("Y-m-d");

        $caducado=false;
        $dias=0;

        if(!$suscripcion) return 'Tu sistema no trabaja con suscripción';

        if($suscripcion->suscripcion_fecha_finalizacion < $hoy){
            $caducado=true;

            $dias = (strtotime($hoy)-strtotime($suscripcion->suscripcion_fecha_finalizacion))/86400;
            $dias = abs($dias); $dias = floor($dias);
        }
        else{
            $dias = (strtotime($suscripcion->suscripcion_fecha_finalizacion)-strtotime($hoy))/86400;
            $dias = abs($dias); $dias = floor($dias);
        }
        
        return view('admin.suscripcion.ver', [
            'suscripcion'=>$suscripcion,
            'caducado'=>$caducado,
            'dias'=>$dias
        ]);
    }

    public function store(Request $request){
        try{
            DB::beginTransaction();
            $empresa = new Empresa();
                $empresa->empresa_ruc =$request->get('Ruc');
                $empresa->empresa_nombreComercial =$request->get('idNombre');
                $empresa->empresa_razonSocial =$request->get('idRazon');
                $empresa->empresa_direccion =$request->get('idDireccion');
                $empresa->empresa_telefono =$request->get('idTelefono');
                $empresa->empresa_celular=$request->get('idCelular');
                $empresa->empresa_ciudad =$request->get('idCiudad');
                $empresa->empresa_logo="0";
                $empresa->empresa_cedula_representante=$request->get('idcedulaRepresentante');
                $empresa->empresa_representante =$request->get('idRepresentante');
                $empresa->empresa_cedula_contador="";
                $empresa->empresa_contador="";
                $empresa->empresa_fecha_ingreso=Carbon::now();
                $empresa->empresa_email=$request->get('idEmail');
                $empresa->empresa_llevaContabilidad ="0";

                if ($request->get('idContabilidad') == "on") $empresa->empresa_llevaContabilidad ="1";

                if ($request->get('idContabilidad2') == "on"){
                    $empresa->empresa_contabilidad ="1";
                }else{
                    $empresa->empresa_contabilidad ="0";
                }
                
                $empresa->empresa_electronica ="1";
                $empresa->empresa_nomina ="0";
                $empresa->empresa_medico ="0";
                $empresa->empresa_estado_cambiar_precio ="0";
                
                $empresa->empresa_tipo =$request->get('idTipo');
                $empresa->empresa_contribuyenteEspecial =$request->get('idContribuyente');
                $empresa->empresa_estado=1;
            $empresa->save();

            $emailEmpresa = new Email_Empresa();
            $emailEmpresa->email_servidor = 'neopagupa-com.correoseguro.dinaserver.com';
            $emailEmpresa->email_email = 'neopagupa@neopagupa.com';
            $emailEmpresa->email_usuario = 'neopagupa';
            $emailEmpresa->email_pass = 'ELWc0X]3:96{';
            $emailEmpresa->email_puerto = '465';
            $emailEmpresa->email_mensaje = ' NEOPAGUPA // SISTEMA DE FACTURACIÓN ELECTRÓNICA  !!! ATENCIÓN ESTE DOCUMENTO TIENE VALIDEZ TRIBUTARIA!!!  Con la finalidad de brindar un mejor servicio, adjunto encontrará la Factura Electrónica, legalmente válida para las declaraciones de impuestos ante el SRI.  El archivo XML adjunto, le sugerimos que almacene de manera segura puesto que tiene validez tributaria.  La factura electrónica en formato PDF adjunto, no es necesario que la imprima, le sirve para verificar el detalle del servicio. ';
            $emailEmpresa->email_neopagupa = '1';
            $emailEmpresa->email_estado  = 1;
            $emailEmpresa->empresa_id = Auth::user()->empresa_id;
            $emailEmpresa->save();


            //////////////////////crear una sucursal /////////////////////////////////////////////
            $sucursal = new sucursal();
                $sucursal->sucursal_nombre = $request->get('idNombreSucursal');
                $sucursal->sucursal_codigo = $request->get('idCodigoSucursal');
                $sucursal->sucursal_direccion = $request->get('idDireccionSucursal');
                $sucursal->sucursal_telefono = $request->get('idTelefono');
                $sucursal->empresa_id = $empresa->empresa_id;
                $sucursal->sucursal_estado = 1;
            $sucursal->save();

            $puntoEmision = new Punto_Emision();
                $puntoEmision->punto_serie = $request->get('idCodigoPuntoVenta');
                $puntoEmision->punto_descripcion = 'Punto de Venta '.$request->get('idCodigoPuntoVenta');
                $puntoEmision->punto_estado = 1;
                $puntoEmision->sucursal_id = $sucursal->sucursal_id;
            $puntoEmision->save();

            $caja = new Caja();
                $caja->caja_nombre = 'Efectivo';            
                $caja->empresa_id = $empresa->empresa_id;
                $caja->sucursal_id = $sucursal->sucursal_id;
                $caja->caja_estado = 1;
            $caja->save();

            $bodega = new Bodega();
                $bodega->bodega_nombre = 'BODEGA '.$sucursal->sucursal_nombre;
                $bodega->bodega_descripcion = 'BODEGA 1';
                $bodega->bodega_direccion = $request->get('idDireccionSucursal');
                $bodega->bodega_telefono = $request->get('idTelefono');
                $bodega->bodega_fax = '-';
                $bodega->ciudad_id = 1;
                $bodega->sucursal_id = $sucursal->sucursal_id;
                $bodega->bodega_estado = 1;
            $bodega->save();


            $rango=new Rango_Documento();
                $rango->rango_descripcion='DOC ELECTRONICOS';
                $rango->rango_inicio=$request->idSecuencia;
                $rango->rango_fin=100000000;
                $rango->rango_fecha_inicio='2022-01-01';
                $rango->rango_fecha_fin='2030-12-31';
                $rango->rango_autorizacion=0;
                $rango->rango_estado=1;
                $rango->tipo_comprobante_id=1;
                $rango->punto_id=$puntoEmision->punto_id;
            $rango->save();

            
            

            $rango=new Rango_Documento();
                $rango->rango_descripcion='DOC ELECTRONICOS';
                $rango->rango_inicio=$request->idSecuencia;
                $rango->rango_fin=100000000;
                $rango->rango_fecha_inicio='2022-01-01';
                $rango->rango_fecha_fin='2030-12-31';
                $rango->rango_autorizacion=0;
                $rango->rango_estado=1;
                $rango->tipo_comprobante_id=2;
                $rango->punto_id=$puntoEmision->punto_id;
            $rango->save();

            $rango=new Rango_Documento();
                $rango->rango_descripcion='DOC ELECTRONICOS';
                $rango->rango_inicio=$request->idSecuencia;
                $rango->rango_fin=100000000;
                $rango->rango_fecha_inicio='2022-01-01';
                $rango->rango_fecha_fin='2030-12-31';
                $rango->rango_autorizacion=0;
                $rango->rango_estado=1;
                $rango->tipo_comprobante_id=3;
                $rango->punto_id=$puntoEmision->punto_id;
            $rango->save();

            $rango=new Rango_Documento();
                $rango->rango_descripcion='DOC ELECTRONICOS';
                $rango->rango_inicio=$request->idSecuencia;
                $rango->rango_fin=100000000;
                $rango->rango_fecha_inicio='2022-01-01';
                $rango->rango_fecha_fin='2030-12-31';
                $rango->rango_autorizacion=0;
                $rango->rango_estado=1;
                $rango->tipo_comprobante_id=4;
                $rango->punto_id=$puntoEmision->punto_id;
            $rango->save();

            $rango=new Rango_Documento();
                $rango->rango_descripcion='DOC ELECTRONICOS';
                $rango->rango_inicio=$request->idSecuencia;
                $rango->rango_fin=100000000;
                $rango->rango_fecha_inicio='2022-01-01';
                $rango->rango_fecha_fin='2030-12-31';
                $rango->rango_autorizacion=0;
                $rango->rango_estado=1;
                $rango->tipo_comprobante_id=5;
                $rango->punto_id=$puntoEmision->punto_id;
            $rango->save();

            $rango=new Rango_Documento();
                $rango->rango_descripcion='DOC ELECTRONICOS';
                $rango->rango_inicio=$request->idSecuencia;
                $rango->rango_fin=100000000;
                $rango->rango_fecha_inicio='2022-01-01';
                $rango->rango_fecha_fin='2030-12-31';
                $rango->rango_autorizacion=0;
                $rango->rango_estado=1;
                $rango->tipo_comprobante_id=6;
                $rango->punto_id=$puntoEmision->punto_id;
            $rango->save();

            $rango=new Rango_Documento();
            $rango->rango_descripcion='DOC ELECTRONICOS';
            $rango->rango_inicio=$request->idSecuencia;
            $rango->rango_fin=100000000;
            $rango->rango_fecha_inicio='2022-01-01';
            $rango->rango_fecha_fin='2030-12-31';
            $rango->rango_autorizacion=0;
            $rango->rango_estado=1;
            $rango->tipo_comprobante_id=7;
            $rango->punto_id=$puntoEmision->punto_id;
        $rango->save();
        
            $rango=new Rango_Documento();
            $rango->rango_descripcion='DOC FISICO';
            $rango->rango_inicio=$request->idSecuencia;
            $rango->rango_fin=100000000;
            $rango->rango_fecha_inicio='2022-01-01';
            $rango->rango_fecha_fin='2030-12-31';
            $rango->rango_autorizacion=0;
            $rango->rango_estado=1;
            $rango->tipo_comprobante_id=8;
            $rango->punto_id=$puntoEmision->punto_id;
        $rango->save();
        $rango=new Rango_Documento();
                $rango->rango_descripcion='DOC FISICO';
                $rango->rango_inicio=$request->idSecuencia;
                $rango->rango_fin=100000000;
                $rango->rango_fecha_inicio='2022-01-01';
                $rango->rango_fecha_fin='2030-12-31';
                $rango->rango_autorizacion=0;
                $rango->rango_estado=1;
                $rango->tipo_comprobante_id=9;
                $rango->punto_id=$puntoEmision->punto_id;
            $rango->save();

            $rango=new Rango_Documento();
                $rango->rango_descripcion='DOC FISICO';
                $rango->rango_inicio=$request->idSecuencia;
                $rango->rango_fin=100000000;
                $rango->rango_fecha_inicio='2022-01-01';
                $rango->rango_fecha_fin='2030-12-31';
                $rango->rango_autorizacion=0;
                $rango->rango_estado=1;
                $rango->tipo_comprobante_id=11;
                $rango->punto_id=$puntoEmision->punto_id;
            $rango->save();

            $rango=new Rango_Documento();
                $rango->rango_descripcion='DOC FISICO';
                $rango->rango_inicio=$request->idSecuencia;
                $rango->rango_fin=100000000;
                $rango->rango_fecha_inicio='2022-01-01';
                $rango->rango_fecha_fin='2030-12-31';
                $rango->rango_autorizacion=0;
                $rango->rango_estado=1;
                $rango->tipo_comprobante_id=56;
                $rango->punto_id=$puntoEmision->punto_id;
            $rango->save();

            $rango=new Rango_Documento();
                $rango->rango_descripcion='DOC FISICO';
                $rango->rango_inicio=$request->idSecuencia;
                $rango->rango_fin=100000000;
                $rango->rango_fecha_inicio='2022-01-01';
                $rango->rango_fecha_fin='2030-12-31';
                $rango->rango_autorizacion=0;
                $rango->rango_estado=1;
                $rango->tipo_comprobante_id=41;
                $rango->punto_id=$puntoEmision->punto_id;
            $rango->save();


            //Buscar los permisos permitidos para facturacion
            $parmetrizacionesP=Parametrizacion_Permiso::parametrizacionesPermiso()->get();
            $rol=new Rol();
            $rol->rol_nombre='Administrador';
            $rol->empresa_id=$empresa->empresa_id;
            $rol->rol_tipo=1;
            $rol->rol_estado=1;
            $rol->save();

            foreach($parmetrizacionesP as $param){
                if($param->parametrizacionp_facturacion==1){
                    $rolPermiso=new Rol_Permiso();
                    $rolPermiso->permiso_id=$param->permiso_id;
                    $rolPermiso->rol_id=$rol->rol_id;
                    $rolPermiso->save();
                }
            }

            $usuarioControlador = new usuarioController();
            $usuario = new User();
                $usuario->user_username = $request->idNombre;
                $usuario->user_cedula = $request->idCedula;
                $usuario->user_nombre = $request->idNombre;
                $usuario->user_correo = $request->idEmail;
                $usuario->user_tipo  = 1;
                $usuario->user_cambio_clave=1;
                $usuario->user_estado  = 1;
                $password=$usuarioControlador->generarPass();
                $usuario->password  = bcrypt($password);
                $usuario->empresa()->associate($empresa);
            $usuario->save();

            $zona = new Zona();
                $zona->zona_nombre = 'Sin zona';
                $zona->zona_descripcion = 'Sin zona';          
                $zona->empresa_id = $empresa->empresa_id;
                $zona->zona_estado = 1;
            $zona->save();

            $vendedor = new Vendedor();
                $vendedor->vendedor_cedula = $request->idCedula;
                $vendedor->vendedor_nombre = $request->idNombre;
                $vendedor->vendedor_direccion = '-';
                $vendedor->vendedor_telefono = '-';
                $vendedor->vendedor_email = $request->idEmail;
                $vendedor->vendedor_comision_porcentaje = 0;
                $vendedor->vendedor_fecha_ingreso = Carbon::Now();
                $vendedor->vendedor_fecha_salida = Carbon::Now();
                $vendedor->zona_id = $zona->zona_id;
                $vendedor->vendedor_estado  = 1;      
            $vendedor->save();

            $usuario_puntoE= new Usuario_PuntoE();
                $usuario_puntoE->punto_id=$puntoEmision->punto_id;
                $usuario_puntoE->user_id=$usuario->user_id;
                $usuario_puntoE->usuarioP_estado=1;
            $usuario_puntoE->save();

            DB::afterCommit(function () use($usuario, $password, $request){
                $html  = "<p style='text-align: justify'>";
                $html .= "Bienvenido <strong>$usuario->user_nombre</strong>,<br><br>";
                $html .= "Somos <strong>PAGUPA SOFT</strong>, !gracias por registrarte en nuestro Sistema <strong>NEOPAGUPA</strong>!, recuerda que puedes Ingresar ";
                $html .= "a tu cuenta con el nombre de Usuario que usaste en el Registro.<br><br>";
                $html .= "Tu clave temportal para el ingreso es: <br><strong>$password</strong><br>La puedes cambiar más adelante cuando quieras.<br><br>";
                $html .= "No te olvides que tienes 30 días para probar nuestro software, o también generar 20 facturas electrónicas, cualquiera ";
                $html .= "que se cumpla primero.<br><br>";
                $html .= "Te deseamos éxito en tus Negocios.<br><br><br>";
                $html .= "Atentamente,<br><br><br>";
                $html .= "__________________________________________<br>";
                $html .= "<strong>PAGUPA SOFT</strong>";
                $html .= "</p>";


                $textoPlano = "Bienvenido $usuario->user_nombre,\n\n";
                $textoPlano .= "Somos PAGUPA SOFT, !gracias por registrarte en nuestro Sistema NEOPAGUPA!, recuerda que puedes ingresar ";
                $textoPlano .= "a tu cuenta con el nombre de Usuario que usaste en el Registro.\n\n";
                $textoPlano .= "Tu clave temporal para el ingreso es: $password, la puedes cambiar más adelante cuando quieras.\n\n";
                $textoPlano .= "No te olvides que tienes 30 días para probar nuestro software, o también generar 20 facturas electrónicas, cualquiera ";
                $textoPlano .= "que se cumpla primero.\n\n";
                $textoPlano .= "Te deseamos éxito en tus Negocios,\n\n\n";
                $textoPlano .= "Atentamente,\n\n\n";
                $textoPlano .= "__________________________________________\n";
                $textoPlano .= "PAGUPA SOFT";

                $generalController = new generalController();
                $result=$generalController->enviarCorreo($request->idEmail, $usuario->user_nombre, "Registro en el Sistema", $html, $textoPlano, []);
            });


            
            $usuario_rol= new Usuario_Rol();
                $usuario_rol->rol()->associate($rol);
                $usuario_rol->usuario()->associate($usuario);
            $usuario_rol->save();


            $plan=Plan::byNombre('BASICO')->first();

            $suscripcion=new Suscripcion();
                $suscripcion->empresa_id=$empresa->empresa_id;
                $suscripcion->plan_id=$plan->plan_id;
                $suscripcion->suscripcion_fecha_inicio= date("Y-m-d");
                $suscripcion->suscripcion_fecha_finalizacion= date("Y-m-d",strtotime(date("Y-m-d")."+ ".intval($plan->tiempo)));
                $suscripcion->suscripcion_cantidad_generado=0;
                $suscripcion->suscripcion_permiso='FACTURACION';
                $suscripcion->estado=1;
            $suscripcion->save();

            $tarifaIva = new Tarifa_Iva();
                $tarifaIva->tarifa_iva_codigo = '02';
                $tarifaIva->tarifa_iva_porcentaje = 12;
                $tarifaIva->empresa_id = $empresa->empresa_id;
                $tarifaIva->tarifa_iva_estado = 1;
            $tarifaIva->save();

            $tipoClien1 = new Tipo_Cliente();
                $tipoClien1->tipo_cliente_nombre = 'CLIENTE FINAL';
                $tipoClien1->empresa_id = $empresa->empresa_id;
                $tipoClien1->tipo_cliente_estado = 1;
            $tipoClien1->save();

            $tipoClien2 = new Tipo_Cliente();
                $tipoClien2->tipo_cliente_nombre = 'INSTITUCIÓN PÚBLICA';
                $tipoClien2->empresa_id = $empresa->empresa_id;
                $tipoClien2->tipo_cliente_estado = 1;
            $tipoClien2->save();


            $credito = new Credito();
                $credito->credito_nombre = 'SIN CRÉDITO';
                $credito->credito_descripcion = 'El Cliente no tiene crédito';
                $credito->credito_monto = 0;
                $credito->empresa_id = $empresa->empresa_id;
                $credito->credito_estado = 1;
            $credito->save();


            

            $medida = new Unidad_Medida_Producto();
                $medida->unidad_medida_nombre = 'UNIDAD';           
                $medida->empresa_id = $empresa->empresa_id;
                $medida->unidad_medida_estado = 1;
            $medida->save();

            $grupo = new Grupo_Producto();
                $grupo->grupo_nombre = 'SIN GRUPO';
                $grupo->empresa_id = $empresa->empresa_id;
                $grupo->grupo_estado = 1;
            $grupo->save();

            $marca = new Marca_Producto();
                $marca->marca_nombre = 'SIN MARCA';           
                $marca->empresa_id = $empresa->empresa_id;
                $marca->marca_estado = 1;
            $marca->save();

            $categoriaCliente = new Categoria_Cliente();
                $categoriaCliente->categoria_cliente_nombre = 'SIN CATEGORIA';
                $categoriaCliente->categoria_cliente_descripcion = '-';
                $categoriaCliente->empresa_id = $empresa->empresa_id;
                $categoriaCliente->categoria_cliente_estado = 1;
            $categoriaCliente->save();

            $categoriaproducto = new Categoria_Producto();
            $categoriaproducto->categoria_nombre = 'SIN CATEGORIA';
            $categoriaproducto->categoria_tipo = '-';
            $categoriaproducto->empresa_id = $empresa->empresa_id;
            $categoriaproducto->categoria_estado = 1;
            $categoriaproducto->save();

            $Tamano = new Tamano_Producto();
            $Tamano->tamano_nombre = 'SIN TAMAÑO';
            $Tamano->empresa_id = $empresa->empresa_id;
            $Tamano->tamano_estado = 1;
            $Tamano->save();


            $cliente = new Cliente();
                $cliente->cliente_cedula = '9999999999999';
                $cliente->cliente_nombre = 'CONSUMIDOR FINAL';
                $cliente->cliente_direccion = 'SN';
                $cliente->cliente_telefono = '0';
                $cliente->cliente_celular = '0';
                $cliente->cliente_email = 'SN';
                $cliente->cliente_fecha_ingreso = Carbon::now();
                $cliente->cliente_lleva_contabilidad ="0";
                $cliente->ciudad_id = 1;
                $cliente->tipo_identificacion_id = 4;
                $cliente->tipo_cliente_id = $tipoClien1->tipo_cliente_id;
                $cliente->categoria_cliente_id = $categoriaCliente->categoria_cliente_id;
                $cliente->cliente_tiene_credito ="0";
                $cliente->cliente_credito = 0;
                $cliente->credito_id = $credito->credito_id;
                $cliente->cliente_estado  = 1;            
            $cliente->save();

            $categoriaProv = new Categoria_Proveedor();
                $categoriaProv->categoria_proveedor_nombre = 'SIN CATEGORÍA';
                $categoriaProv->categoria_proveedor_descripcion = '-';
                $categoriaProv->empresa_id = $empresa->empresa_id;
                $categoriaProv->categoria_proveedor_estado = 1;
            $categoriaProv->save();

            $Pais = new Pais();
                $Pais->pais_nombre = "ECUADOR";
                $Pais->pais_codigo = "+593";
                $Pais->pais_estado = 1;
                $Pais->empresa_id = $empresa->empresa_id;
            $Pais->save();

            $provincia = new Provincia();
                $provincia->provincia_nombre = 'AZUAY';
                $provincia->provincia_codigo = '1';
                $provincia->pais_id = $Pais->pais_id;
                $provincia->provincia_estado = 1;
            $provincia->save();

            $provincia = new Provincia();
                $provincia->provincia_nombre = 'BOLIVAR';
                $provincia->provincia_codigo = '2';
                $provincia->pais_id = $Pais->pais_id;
                $provincia->provincia_estado = 1;
            $provincia->save();

            $provincia = new Provincia();
                $provincia->provincia_nombre = 'CAÑAR';
                $provincia->provincia_codigo = '3';
                $provincia->pais_id = $Pais->pais_id;
                $provincia->provincia_estado = 1;
            $provincia->save();

            $provincia = new Provincia();
                $provincia->provincia_nombre = 'CARCHI';
                $provincia->provincia_codigo = '4';
                $provincia->pais_id = $Pais->pais_id;
                $provincia->provincia_estado = 1;
            $provincia->save();

            $provincia = new Provincia();
                $provincia->provincia_nombre = 'COTOPAXI';
                $provincia->provincia_codigo = '5';
                $provincia->pais_id = $Pais->pais_id;
                $provincia->provincia_estado = 1;
            $provincia->save();

            $provincia = new Provincia();
                $provincia->provincia_nombre = 'CHIMBORAZO';
                $provincia->provincia_codigo = '6';
                $provincia->pais_id = $Pais->pais_id;
                $provincia->provincia_estado = 1;
            $provincia->save();

            $provincia = new Provincia();
                $provincia->provincia_nombre = 'EL ORO';
                $provincia->provincia_codigo = '7';
                $provincia->pais_id = $Pais->pais_id;
                $provincia->provincia_estado = 1;
            $provincia->save();

            $ciudad = new Ciudad();
                $ciudad->ciudad_nombre = 'MACHALA';
                $ciudad->ciudad_codigo = '070202';
                $ciudad->provincia_id = $provincia->provincia_id;
                $ciudad->ciudad_estado = 1;
            $ciudad->save();

            


            DB::commit();
            return redirect('login')->with('success','Datos guardados exitosamente');
        }catch(\Exception $ex){
            DB::rollBack();
            return back()->with('error2','Ocurrio un error en el procedimiento. Vuelva a intentar. ('.$ex->getMessage().')');
        }
    }

    public function registro(){
        return view('admin.suscripcion.registro');
    }

    public function registrarse(Request $Request){

    }

    public function pago(){
        $suscripcion=Auth::user()->empresa->suscripcion;
        $planes=Plan::planes()->get();
        $bancos=Banco_Lista::bancoListas()->get();
        $hoy = date("Y-m-d");

        $caducado=false;
        $dias=0;

        if(!$suscripcion) return 'Tu sistema no trabaja con suscripción';

        if($suscripcion->suscripcion_fecha_finalizacion < $hoy){
            $caducado=true;

            $dias = (strtotime($hoy)-strtotime($suscripcion->suscripcion_fecha_finalizacion))/86400;
            $dias = abs($dias); $dias = floor($dias);
        }
        else{
            $dias = (strtotime($suscripcion->suscripcion_fecha_finalizacion)-strtotime($hoy))/86400;
            $dias = abs($dias); $dias = floor($dias);
        }

        return view('admin.suscripcion.pago', [
            'suscripcion'=>$suscripcion,
            'planes'=>$planes,
            'bancos'=>$bancos,
            'caducado'=>$caducado,
            'dias'=>$dias
        ]);
    }

    public function registrarPago(Request $request){
        try{
            DB::beginTransaction();
            $suscripcion=Auth::user()->empresa->suscripcion;
            $imagen=$request->fotoDeposito;
            $hoy = date("Y-m-d");

            if($imagen) {
                $pago=new Pago();
                $pago->suscripcion_id=$suscripcion->suscripcion_id;
                $pago->plan_id=$request->idPlan;
                $pago->pago_fecha=$hoy;
                $pago->pago_documento=$request->idDocumento;
                $pago->pago_valor=Plan::findOrFail($request->idPlan)->plan_precio;
                $pago->pago_estado=0;
                $pago->save();

                $ruta = 'documentos/pagos/'.Auth::user()->empresa->empresa_ruc;
                if (!is_dir(public_path().'/'.$ruta)) mkdir(public_path().'/'.$ruta, 0777, true);
                
                $extension=$imagen->extension();
                $name = 'comprobante_'.$pago->pago_id.'.'.$extension;
                $path=$imagen->move(public_path().'/'.$ruta, $name);

                $pago->pago_comprobante=$path;
                $pago->save();

                DB::commit();
                return redirect('pago')->with('success', 'El Pago se registró correctamente, el procesamiento tomará máximo 30 minutos');
            }
            else{
                DB::rollback();
                return back()->with('error', 'No se pudo registrar el Pago, no se econtró el comprobante ');
            }
        }
        catch(\Exception $ex){
            DB::rollback();
            return back()->with('error', 'No se pudo registrar el Pago: '. $ex->getMessage());
        }
    }

    public function verificar(){
        $suscripcion=Auth::user()->empresa->suscripcion;
        $planes=Plan::planes()->get();
        $bancos=Banco_Lista::bancoListas()->get();
        $hoy = date("Y-m-d");

        $caducado=false;
        $dias=0;

        //return $suscripcion->plan;

        if($suscripcion->suscripcion_fecha_finalizacion < $hoy){
            $caducado=true;

            $dias = (strtotime($hoy)-strtotime($suscripcion->suscripcion_fecha_finalizacion))/86400;
            $dias = abs($dias); $dias = floor($dias);
        }
        else{
           $dias = (strtotime($suscripcion->suscripcion_fecha_finalizacion)-strtotime($hoy))/86400;
            $dias = abs($dias); $dias = floor($dias);
        }

        return view('admin.suscripcion.verificar', [
            'suscripcion'=>$suscripcion,
            'planes'=>$planes,
            'bancos'=>$bancos,
            'caducado'=>$caducado,
            'dias'=>$dias
        ]);
    }

    public function autorizarPago(Request $request){
        try{
            DB::beginTransaction();
            $suscripcion=Auth::user()->empresa->suscripcion;
            $imagen=$request->fotoDeposito;
            $hoy = date("Y-m-d");

            if($imagen) {
                $pago=new Pago();
                $pago->suscripcion_id=$suscripcion->suscripcion_id;
                $pago->plan_id=$request->idPlan;
                $pago->pago_fecha=$hoy;
                $pago->pago_documento=$request->idDocumento;
                $pago->pago_valor=Plan::findOrFail($request->idPlan)->plan_precio;
                $pago->pago_estado=0;
                $pago->save();

                $ruta = 'documentos/pagos/'.Auth::user()->empresa->empresa_ruc;
                if (!is_dir(public_path().'/'.$ruta)) mkdir(public_path().'/'.$ruta, 0777, true);
                
                $extension=$imagen->extension();
                $name = 'comprobante_'.$pago->pago_id.'.'.$extension;
                $path=$imagen->move(public_path().'/'.$ruta, $name);

                $pago->pago_comprobante=$path;
                $pago->save();

                DB::commit();
                return redirect('pago')->with('success', 'El Pago se registró correctamente, el procesamiento tomará máximo 30 minutos');
            }
            else{
                DB::rollback();
                return back()->with('error', 'No se pudo registrar el Pago, no se econtró el comprobante ');
            }
        }
        catch(\Exception $ex){
            DB::rollback();
            return back()->with('error', 'No se pudo registrar el Pago: '. $ex->getMessage());
        }
    }
}
