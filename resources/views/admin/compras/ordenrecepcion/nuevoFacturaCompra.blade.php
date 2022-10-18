@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-primary card-outline">
    <form class="form-horizontal" method="POST"  action="{{url("transaccionCompra/ordenrecepcion")}}" onsubmit="return validarForm();">
        @csrf
        <div class="card-header">
            <div class="row">
                <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5">
                    <h2 class="card-title"><b>COMPRAS - Nuevo Documento</b></h2>
                </div>
                <div class="col-xs-7 col-sm-7 col-md-7 col-lg-7">
                    <div class="float-right">
                        <button id="guardarID" name="guardarID" type="submit" class="btn btn-success btn-sm" ><i
                                class="fa fa-save"></i><span> Guardar</span></button>
                        <button id="TXTID" type="button" class="btn btn-secondary btn-sm" data-toggle="modal" data-target="#modal-TXT"><i
                                class="fas fa-file-code"></i><span> Archivo TXT</span></button>        
                        <button type="button" id="cancelarID" name="cancelarID" onclick='window.location = "{{ url("ordenRecepecion") }}";'
                            class="btn btn-danger btn-sm not-active-neo" ><i
                                class="fas fa-times-circle"></i><span> Cancelar</span></button>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.card-header -->
        <div class="card-body">
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                    <div class="row clearfix form-horizontal">
                        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 form-control-label ">
                            <label>Fecha Doc.</label>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                            <div class="form-group">
                                <div class="form-line">
                                    <input type="date" class="form-control" id="transaccion_fecha"
                                        name="transaccion_fecha" onchange="fechaRet();"
                                        value='<?php echo(date("Y")."-".date("m")."-".date("d")); ?>' onchange="fechaRet();"  required>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 form-control-label ">
                            <label>Fecha Inv.</label>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                            <div class="form-group">
                                <div class="form-line">
                                    <input type="date" class="form-control" id="transaccion_inventario"
                                        name="transaccion_inventario"
                                        value='<?php echo(date("Y")."-".date("m")."-".date("d")); ?>' required>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 form-control-label ">
                            <label>Fecha Ven.</label>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                            <div class="form-group">
                                <div class="form-line">
                                    <input type="date" class="form-control" id="transaccion_vencimiento"
                                        name="transaccion_vencimiento" onchange="showDiff();"
                                        value='<?php echo(date("Y")."-".date("m")."-".date("d")); ?>' required>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 form-control-label ">
                            <label>Fecha Imp.</label>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                            <div class="form-group">
                                <div class="form-line">
                                    <input type="date" class="form-control" id="transaccion_impresion"
                                        name="transaccion_impresion"
                                        value='<?php echo(date("Y")."-".date("m")."-".date("d")); ?>' required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row clearfix form-horizontal">
                        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 form-control-label  "
                            style="margin-bottom : 0px;">
                            <label>Proveedor</label>
                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4" style="margin-bottom : 0px;">
                            <div class="form-group">
                                <input id="proveedorID" name="proveedorID" value="{{$orden->proveedor->proveedor_id}}" type="hidden">
                                <input id="buscarProveedor" name="buscarProveedor" type="text" class="form-control"
                                    placeholder="Proveedor" value="{{$orden->proveedor->proveedor_nombre}}" required >
                            </div>
                        </div>
                        <div class="col-sm-1"><center><a href="{{ url("proveedor/create") }}" class="btn btn-info btn-sm" target="_blank"><i class="fa fa-user"></i>&nbsp;Nuevo</a></center></div>
                        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 form-control-label  "
                            style="margin-bottom : 0px;">
                            <label>RUC/CI</label>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2" style="margin-bottom : 0px;">
                            <div class="form-group">
                                <input id="idRUC" name="idRUC" type="text" class="form-control"
                                    placeholder="9999999999999" value="{{$orden->proveedor->proveedor_ruc}}" required disabled>
                            </div>
                        </div>
                        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 form-control-label ">
                            <label>Fecha Cadu.</label>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                            <div class="form-group">
                                <div class="form-line">
                                    <input type="date" class="form-control" id="transaccion_caducidad"
                                        name="transaccion_caducidad"
                                        value='<?php echo(date("Y")."-".date("m")."-".date("d")); ?>' required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row clearfix form-horizontal">
                        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 form-control-label  "
                            style="margin-bottom : 0px;">
                            <label>Comprobante</label>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3" style="margin-bottom : 0px;">
                            <div class="form-group">
                                <select class="form-control select2" id="tipo_comprobante_id" name="tipo_comprobante_id"
                                    data-live-search="true">
                                    @foreach($comprobantes as $comprobante)
                                    <option value="{{$comprobante->tipo_comprobante_id}}">
                                        {{$comprobante->tipo_comprobante_nombre}}
                                    </option>
                                    @endforeach
                                </select>
                                <select class="invisible" id="tipo_comprobante_codigo" name="tipo_comprobante_codigo"
                                    data-live-search="true" >
                                    @foreach($comprobantes as $comprobante)
                                    <option value="{{$comprobante->tipo_comprobante_codigo}}">
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 form-control-label  "
                            style="margin-bottom : 0px;">
                            <label>Transaccion</label>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3" style="margin-bottom : 0px;">
                            <div class="form-group">
                                <select id="transaccion_descripcion" name="transaccion_descripcion"
                                    class="form-control custom-select" data-live-search="true">
                                    <option value="COMPRA">COMPRA</option>
                                    <option value="DESCUENTO">DESCUENTO</option>
                                    <option value="DEVOLUCION DE PRODUCTO">DEVOLUCION DE PRODUCTO</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 form-control-label  "
                            style="margin-bottom : 0px;">
                            <label>Forma Pago</label>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3" style="margin-bottom : 0px;">
                            <div class="form-group">
                                <select id="transaccion_tipo_pago" name="transaccion_tipo_pago"
                                    class="form-control custom-select" data-live-search="true" onchange="cambioPago();">
                                    <option value="CONTADO">CONTADO</option>
                                    <option value="CREDITO" selected>CREDITO</option>
                                    <option value="EN EFECTIVO">EN EFECTIVO</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row clearfix form-horizontal">
                        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 form-control-label  "
                            style="margin-bottom : 0px;">
                            <label>No. Serie</label>
                        </div>
                        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1" style="margin-bottom : 0px;">
                            <div class="form-group">
                                <div class="form-line">
                                    <input type="text" id="transaccion_serie" name="transaccion_serie"
                                        class="form-control " placeholder="001001" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 form-control-label  "
                            style="margin-bottom : 0px;">
                            <label>No. Docu.</label>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2" style="margin-bottom : 0px;">
                            <div class="form-group">
                                <div class="form-line">
                                    <input type="text" id="transaccion_secuencial" name="transaccion_secuencial"
                                        class="form-control " placeholder="0000000001" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 form-control-label  "
                            style="margin-bottom : 0px;">
                            <label>Autorizacion</label>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3" style="margin-bottom : 0px;">
                            <div class="form-group">
                                <div class="form-line">
                                    <input type="text" id="transaccion_autorizacion" name="transaccion_autorizacion"
                                        class="form-control "
                                        placeholder="1702201205176001321000110010030001000011234567816" required>
                                </div>
                            </div>
                        </div>
                        <div id="IdCajaL" class="col-lg-1 col-md-1 col-sm-1 col-xs-1 form-control-label invisible"
                            style="margin-bottom : 0px;">
                            <label>Caja</label>
                        </div>
                        <div id="IdCajaI"  class="col-lg-2 col-md-2 col-sm-2 col-xs-2 invisible" style="margin-bottom : 0px;">
                            <div class="form-group">
                                <div class="form-line">
                                        <select id="caja_id" name="caja_id" class="form-control show-tick" data-live-search="true">
                                            @if($cajaAbierta)
                                            <option value="{{ $cajaAbierta->caja->caja_id }}">{{ $cajaAbierta->caja->caja_nombre }}</option>
                                            @endif
                                        </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row clearfix form-horizontal">
                        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 form-control-label  "
                            style="margin-bottom : 0px;">
                            <label>% IVA </label>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2" style="margin-bottom : 0px;">
                            <div class="form-group">
                                <select class="form-control custom-select" id="transaccion_porcentaje_iva"
                                    name="transaccion_porcentaje_iva" data-live-search="true"
                                    onclick="seleccionarIva()">
                                    @foreach($tarifasIva as $iva)
                                    <option value="{{$iva->tarifa_iva_porcentaje}}">{{$iva->tarifa_iva_porcentaje}}%
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 form-control-label  "
                            style="margin-bottom : 0px;">
                            <label>Dias de Plazo</label>
                        </div>
                        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1" style="margin-bottom : 0px;">
                            <div class="form-group">
                                <input type="number" id="transaccion_dias_plazo" name="transaccion_dias_plazo"
                                    class="form-control " value="0" required>
                            </div>
                        </div>
                        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 form-control-label  "
                            style="margin-bottom : 0px;">
                            <label>Sustento</label>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6" style="margin-bottom : 0px;">
                            <div class="form-group">
                                <select class="form-control select2" id="sustento_id" name="sustento_id"
                                    data-live-search="true">
                                    @foreach($sustentos as $sustento)
                                    <option value="{{$sustento->sustento_id}}">{{$sustento->sustento_codigo .' - '. $sustento->sustento_nombre}}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row clearfix form-horizontal">
                        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 form-control-label  "
                            style="margin-bottom : 0px;">
                            <label>Orden de Recepcion</label>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3" style="margin-bottom : 0px;">
                            <div class="form-group">
                                <div class="form-line">
                                    <div style=" height: 70px; width: 150px; font-size: 15px; overflow: auto;">
                                        <table  > 
                                            @if (isset($datos)) 
                                                @for($i = 1; $i <= count($ordenes); ++$i) 
                                                    <tr ><td>N° {{$ordenes[$i]['orden_numero']}} <input class="invisible" name="ordenes[]" value="{{$ordenes[$i]['orden_id']}}"/> </td> </tr>
                                                @endfor
                                            @endif
                                        </table >
                                        
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                 
                    <div class="row" style="background: #dadada; padding-top: 20px;margin-top: 5px;">
                        <div class="col-sm-4" style="margin-bottom: 0px;">
                            <label>Nombre de Producto</label>
                            <div class="form-group">
                                <div class="form-line">
                                    <input id="codigoProducto" name="idProducto" type="hidden">
                                    <input id="idProductoID" name="idProductoID" type="hidden">
                                    <input id="tipoProductoID" name="tipoProductoID" type="hidden">
                                    <input id="buscarProducto" name="buscarProducto" type="text" class="form-control" placeholder="Buscar producto" >
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1" style="margin-bottom: 0px;">
                            <center><label></label></center>
                            <center>
                                <div class="form-group" style="margin-bottom: 0px;">
                                    <div class="custom-control custom-checkbox">
                                        <input id="tieneIva" name="tieneIva" type="checkbox"
                                            class="custom-control-input" disabled />
                                        <label for="tieneIva" class="custom-control-label">Iva</label>
                                    </div>
                                </div>
                            </center>
                        </div>
                        <div class="col-sm-1" style="margin-bottom: 0px;">
                            <center><label>Cantidad</label></center>
                            <div class="form-group">
                                <div class="form-line">
                                    <input onchange="calcularTotal()" onkeyup="calcularTotal()" id="id_cantidad"
                                        name="id_cantidad" type="number" class="form-control" placeholder="0" value="1">
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-2" style="margin-bottom: 0px;">
                            <center><label>Precio</label></center>
                            <div class="form-group">
                                <div class="form-line">
                                    <input onchange="calcularTotal()" onkeyup="calcularTotal()" id="id_pu" name="id_pu"
                                        type="text" class="form-control centrar-texto" placeholder="Precio" value="0.00">
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-2" style="margin-bottom: 0px;">
                            <center><label>Desc. %</label></center>
                            <div class="form-group">
                                <div class="form-line">
                                    <input id="id_descuento" name="id_descuento" type="text" class="form-control centrar-texto"
                                        placeholder="Descuento" value="0.00">
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-2" style="margin-bottom: 0px;">
                            <center><label>Total</label></center>
                            <div class="form-group">
                                <div class="form-line">
                                    <input id="id_total" name="id_total" type="text" class="form-control centrar-texto""
                                        placeholder="Total" value="0.00" disabled>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row" style="background: #dadada;">
                        <div class="col-sm-4" style="margin-bottom: 0px;">
                            <div class="form-group">
                                <input id="descripcionProducto" name="descripcionProducto" type="text"
                                    class="form-control" placeholder="Descripcion">
                            </div>
                        </div>
                        <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1" style="margin-bottom: 0px;">
                            <center><label>Bodega</label></center>
                        </div>
                        <div class="col-sm-2" style="margin-bottom: 0px;">
                            <div class="form-group">
                                <select class="form-control select2" id="bodegaProducto" name="bodegaProducto"
                                    data-live-search="true">
                                    @foreach($bodegas as $bodega)
                                    <option value="{{$bodega->bodega_id}}">{{$bodega->bodega_nombre}}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1" style="margin-bottom: 0px;">
                            <center><label>C. Consumo</label></center>
                        </div>
                        <div class="col-sm-3" style="margin-bottom: 0px;">
                            <div class="form-group">
                                <select class="form-control select2" id="ccProducto" name="ccProducto"
                                    data-live-search="true" onchange="cargarSustento(this);">
                                    <option value="" label>--Seleccione una opcion--</option>
                                    @foreach($centros as $centro)
                                    <option value="{{$centro->centro_consumo_id}}">{{$centro->centro_consumo_nombre}}
                                    </option>
                                    @endforeach
                                </select>
                                <select class="invisible" id="idCCSustento" name="idCCSustento"
                                    data-live-search="true">
                                    <option value="" label>--Seleccione una opcion--</option>
                                    @foreach($centros as $centro)
                                    <option value="{{$centro->sustento_id}}">{{$centro->centro_consumo_nombre}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
                            <center><a onclick="agregarItem();" class="btn btn-primary"><i class="fas fa-plus"></i></a>
                            </center>
                        </div>
                    </div>
                    
                    <br>
                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" style="margin-bottom: 0px;">
                            <div class="table-responsive">
                                @include ('admin.compras.transaccionCompra.itemFactura')
                                <table id="cargarItemFactura"
                                    class="table table-striped table-hover boder-sar tabla-item-factura sin-salto"
                                    style="margin-bottom: 6px;">
                                    <thead>
                                        <tr class="letra-blanca fondo-azul-claro">
                                            <th width="40"></th>
                                            <th width="90">Cantidad</th>
                                            <th width="120">Codigo</th>
                                            <th width="250">Producto</th>
                                            <th width="75">Con Iva</th>
                                            <th width="100">Iva</th>
                                            <th width="120">P.U.</th>
                                            <th width="100">Descuento</th>
                                            <th width="120">Total</th>
                                            <th>Bodega</th>
                                            <th>C. Consumo</th>
                                            <th>Descripcion</th>
                                            <th>Bien/Serv.</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $cont = 1;?>
                                        @if (isset($datos)) 
                                            @for($i = 1; $i <= count($datos); ++$i) 
                                            <tr   id="row_<?php echo $cont; ?>">
                                                <td><a onclick="eliminarItem(<?php echo $cont; ?>, '{{$datos[$i]['TieneIva']}}', {{$datos[$i]['total']}}, {{$datos[$i]['descuento']}},'{{$datos[$i]['servicio']}}');" class="btn btn-danger waves-effect" style="padding: 2px 8px;">X</a></td>
                                                <td>{{$datos[$i]['detalle_cantidad']}}<input class="invisible" name="Dcantidad[]" value="{{$datos[$i]['detalle_cantidad']}}" /></td>
                                                <td>{{$datos[$i]['Codigo']}}<input class="invisible" name="DprodcutoID[]" value="{{$datos[$i]['producto_id']}}" /><input class="invisible" name="Dcodigo[]" value="{{$datos[$i]['Codigo']}}" /></td>
                                                <td>{{$datos[$i]['detalle_descripcion']}}<input class="invisible" name="Dnombre[]" value="{{$datos[$i]['detalle_descripcion']}}" /></td>
                                                <td>{{$datos[$i]['TieneIva']}}<input class="invisible" name="Diva[]" value="{{$datos[$i]['TieneIva']}}" /></td>
                                                <td><input class="form-control" name="DViva[]" value="{{$datos[$i]['Iva']}}" readonly /></td>
                                                <td><input type="number" class="form-control" name="Dpu[]" value="0" onclick="recalcular('<?php echo $cont; ?>','{{$datos[$i]['TieneIva']}}','{{$datos[$i]['servicio']}}');" onkeyup="recalcular('<?php echo $cont; ?>','{{$datos[$i]['TieneIva']}}','{{$datos[$i]['servicio']}}');" min="0" step="any" required/></td>
                                                <td><input class="form-control" class="invisible" name="Ddescuento[]" value="{{$datos[$i]['descuento']}}" readonly/></td>
                                                <td><input  class="form-control" name="Dtotal[]" value="{{$datos[$i]['total']}}" readonly /></td>
                                                <td>{{$datos[$i]['bodega']}}<input class="invisible" name="Dbodega[]" value="{{$datos[$i]['bodegaid']}}" /></td>
                                                <td><select class="form-control" id="Dcconsumo" name="Dcconsumo[]" data-live-search="true" onchange="cargarSustento2(this);"><option value="" label>--Seleccione una opcion--</option>@foreach($centros as $centro)<option value="{{$centro->centro_consumo_id}}">{{$centro->centro_consumo_nombre}}</option>@endforeach</select></td>              
                                                <td><input type="text" class="form-control" name="Ddescripcion[]" value="{{$datos[$i]['detalle_descripcion']}}"/></td>
                                                <td>{{$datos[$i]['servicio']}}<input class="invisible" name="Dservicio[]" value="{{$datos[$i]['servicio']}}" /></td>
                                               
                                                <?php $cont = $cont + 1;?>  
                                            </tr>
                                            
                                            @endfor  
                                        @endif  

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-9 col-sm-9 col-md-9 col-lg-9">
                            <div class="card card-primary card-outline card-tabs">
                                <div class="card-header p-0 pt-1 border-bottom-0">
                                    <ul class="nav nav-tabs" id="custom-tabs-one-tab" role="tablist"
                                        style="border-bottom: 1px solid #c3c4c5;">
                                        <li class="nav-item">
                                            <a class="nav-link active" id="custom-tabs-four-retencion-tab"
                                                data-toggle="pill" href="#custom-tabs-four-retencion" role="tab"
                                                aria-controls="custom-tabs-four-retencion" aria-selected="true"><b>No.
                                                    Retencion</b></a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" id="custom-tabs-four-fuente-tab" data-toggle="pill"
                                                href="#custom-tabs-four-fuente" role="tab"
                                                aria-controls="custom-tabs-four-fuente"
                                                aria-selected="false"><b>Retencion Fuente</b></a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" id="custom-tabs-four-iva-tab" data-toggle="pill"
                                                href="#custom-tabs-four-iva" role="tab"
                                                aria-controls="custom-tabs-four-iva" aria-selected="false"><b>Retencion
                                                    Iva</b></a>
                                        </li>
                                        <!-- <li class="nav-item">
                                            <a class="nav-link" id="custom-tabs-four-asumida-tab" data-toggle="pill"
                                                href="#custom-tabs-four-asumida" role="tab"
                                                aria-controls="custom-tabs-four-asumida"
                                                aria-selected="false"><b>Retencion Asumida</b></a>
                                        </li>-->
                                        <li class="nav-item">
                                            <a class="nav-link" id="custom-tabs-four-factura-tab" data-toggle="pill"
                                                href="#custom-tabs-four-factura" role="tab"
                                                aria-controls="custom-tabs-four-factura"
                                                aria-selected="false"><b>Factura</b></a>
                                        </li>
                                    </ul>
                                </div>
                                <div class="card-body">
                                    <div class="tab-content" id="custom-tabs-four-tabContent">
                                        <div class="tab-pane fade show active" id="custom-tabs-four-retencion"
                                            role="tabpanel" aria-labelledby="custom-tabs-four-retencion-tab">
                                            <div class="row">
                                                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3"
                                                    style="margin-bottom : 0px;">
                                                    <div class="demo-checkbox">
                                                        <input type="radio" value="ELECTRONICA" id="check1"
                                                            class="with-gap radio-col-deep-orange" name="tipoDoc"
                                                            checked required />
                                                        <label for="check1">Documento Electronico</label>
                                                    </div>
                                                </div>
                                                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2"
                                                    style="margin-bottom : 0px;">
                                                    <div class="demo-checkbox">
                                                        <input type="radio" value="FISICA" id="check2"
                                                            class="with-gap radio-col-deep-orange" name="tipoDoc"
                                                            required />
                                                        <label for="check2">Documento Fisico</label>
                                                    </div>
                                                </div>
                                            </div><br>
                                            <div class="row">
                                                <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2"
                                                    style="margin-bottom: 0px;">
                                                    <center><label>Fecha Retencion</label></center>
                                                </div>
                                                <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3"
                                                    style="margin-bottom: 0px;">
                                                    <div class="form-group">
                                                        <div class="form-line">
                                                            <input id="retencion_fecha" name="retencion_fecha"
                                                                type="date" class="form-control"
                                                                value="<?php echo(date("Y")."-".date("m")."-".date("d")); ?>">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1"
                                                    style="margin-bottom: 0px;">
                                                    <center><label>Serie</label></center>
                                                </div>
                                                <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2"
                                                    style="margin-bottom: 0px;">
                                                    <div class="form-group">
                                                        <div class="form-line">
                                                            <input id="punto_id" name="punto_id"
                                                                value="{{ $rangoDocumento->puntoEmision->punto_id }}"
                                                                type="hidden">
                                                            <input id="rango_id" name="rango_id"
                                                                value="{{ $rangoDocumento->rango_id }}" type="hidden">
                                                            <input id="retencion_serie" name="retencion_serie"
                                                                type="text" class="form-control"
                                                                value="{{ $rangoDocumento->puntoEmision->sucursal->sucursal_codigo }}{{ $rangoDocumento->puntoEmision->punto_serie }}" readonly>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1"
                                                    style="margin-bottom: 0px;">
                                                    <center><label>Numero</label></center>
                                                </div>
                                                <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3"
                                                    style="margin-bottom: 0px;">
                                                    <div class="form-group">
                                                        <div class="form-line">
                                                            <input id="retencion_secuencial" name="retencion_secuencial"
                                                                type="text" class="form-control"
                                                                value="{{ $secuencial }}" required>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane fade" id="custom-tabs-four-fuente" role="tabpanel"
                                            aria-labelledby="custom-tabs-four-fuente-tab">
                                            <div class="row">
                                                <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1"
                                                    style="margin-bottom: 0px;">
                                                    <label>Base</label>
                                                </div>
                                                <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2"
                                                    style="margin-bottom: 0px;">
                                                    <div class="form-group">
                                                        <input id="baseFuente" name="baseFuente" type="text"
                                                            class="form-control" placeholder="0.00" value="0.00"
                                                            onkeyup="calcularRF();" onclick="calcularRF();">
                                                    </div>
                                                </div>
                                                <div class="col-lg-5 col-md-5 col-sm-5 col-xs-5"
                                                    style="margin-bottom : 0px;">
                                                    <div class="form-group">
                                                        <select class="form-control select2" id="conceptoFuenteID"
                                                            name="conceptoFuenteID" data-live-search="true"
                                                            onchange="calcularRF()">
                                                            @foreach($conceptosFuente as $concepto)
                                                            <option value="{{$concepto->concepto_id}}">
                                                                {{$concepto->concepto_codigo}} -
                                                                {{$concepto->concepto_porcentaje}}% -
                                                                {{$concepto->concepto_nombre}}</option>
                                                            @endforeach
                                                        </select>
                                                        <select class="invisible" id="conceptoFuenteIDAux"
                                                            name="conceptoFuenteIDAux">
                                                            @foreach($conceptosFuente as $concepto)
                                                            <option value="{{$concepto->concepto_id}}">
                                                                {{$concepto->concepto_porcentaje}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1"
                                                    style="margin-bottom: 0px;">
                                                    <label>Valor</label>
                                                </div>
                                                <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2"
                                                    style="margin-bottom: 0px;">
                                                    <div class="form-group">
                                                        <div class="form-line">
                                                            <input id="valorFuente" name="valorFuente" type="text"
                                                                class="form-control" placeholder="0.00" value="0.00"
                                                                readonly>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1"
                                                    style="margin-bottom: 0px;">
                                                    <center><button type="button" onclick="agregarItemRF();"
                                                            class="btn btn-primary"><i class="fa fa-plus"></i></button>
                                                    </center>
                                                </div>
                                            </div>
                                            <div class="row">
                                                @include ('admin.compras.transaccionCompra.itemRetencionFuente')
                                                <table id="cargarItemRF" class="table table-bordered">
                                                    <thead>
                                                        <tr class="letra-blanca fondo-gris-claro">
                                                            <th></th>
                                                            <th>Base Retencion</th>
                                                            <th>Codigo Retencion</th>
                                                            <th>Porcentaje Retencion</th>
                                                            <th>Valor Retencion</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                    </tbody>
                                                </table>
                                            </div>
                                            <div class="row">
                                                <div class="col-xs-9 col-sm-9 col-md-9 col-lg-9"
                                                    style="margin-bottom: 0px;"></div>
                                                <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1"
                                                    style="margin-bottom: 0px;">
                                                    <label>Total</label>
                                                </div>
                                                <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2"
                                                    style="margin-bottom: 0px;">
                                                    <div class="form-group">
                                                        <div class="form-line">
                                                            <input id="id_total_fuente" name="id_total_fuente"
                                                                type="text" class="form-control" placeholder="0.00"
                                                                value="0.00" readonly>
                                                                <input type="text" id="totalBaseFuenteId" value="0.00">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane fade" id="custom-tabs-four-iva" role="tabpanel"
                                            aria-labelledby="custom-tabs-four-iva-tab">
                                            <div class="row">
                                                <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1"
                                                    style="margin-bottom: 0px;">
                                                    <label>Base</label>
                                                </div>
                                                <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2"
                                                    style="margin-bottom: 0px;">
                                                    <div class="form-group">
                                                        <div class="form-line">
                                                            <input id="baseIva" name="baseIva" type="text"
                                                                class="form-control" placeholder="Total" value="0.00"
                                                                onkeyup="calcularRI();" onclick="calcularRI();">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5"
                                                    style="margin-bottom: 0px;">
                                                    <div class="form-group">
                                                        <select class="form-control select2" id="conceptoIvaID"
                                                            name="conceptoIvaID" data-live-search="true"
                                                            onchange="calcularRI()">
                                                            @foreach($conceptosIva as $concepto)
                                                            <option value="{{$concepto->concepto_id}}">
                                                                {{$concepto->concepto_codigo}} -
                                                                {{$concepto->concepto_porcentaje}}% -
                                                                {{$concepto->concepto_nombre}}</option>
                                                            @endforeach
                                                        </select>
                                                        <select class="invisible" id="conceptoIvaIDAux"
                                                            name="conceptoIvaIDAux">
                                                            @foreach($conceptosIva as $concepto)
                                                            <option value="{{$concepto->concepto_id}}">
                                                                {{$concepto->concepto_porcentaje}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1"
                                                    style="margin-bottom: 0px;">
                                                    <label>Valor</label>
                                                </div>
                                                <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2"
                                                    style="margin-bottom: 0px;">
                                                    <div class="form-group">
                                                        <div class="form-line">
                                                            <input id="valorIva" name="valorIva" type="text"
                                                                class="form-control" placeholder="Total" value="0.00"
                                                                readonly>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1"
                                                    style="margin-bottom: 0px;">
                                                    <center><button type="button" onclick="agregarItemRI();"
                                                            class="btn btn-primary"><i class="fa fa-plus"></i></button>
                                                    </center>
                                                </div>
                                            </div>
                                            <div class="row">
                                                @include ('admin.compras.transaccionCompra.itemRetencionIva')
                                                <table id="cargarItemRI" class="table table-bordered">
                                                    <thead>
                                                        <tr class="letra-blanca fondo-gris-claro">
                                                            <th></th>
                                                            <th>Base Retencion</th>
                                                            <th>Codigo Retencion</th>
                                                            <th>Porcentaje Retencion</th>
                                                            <th>Valor Retencion</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody></tbody>
                                                </table>
                                            </div>
                                            <div class="row">
                                                <div class="col-xs-9 col-sm-9 col-md-9 col-lg-9"
                                                    style="margin-bottom: 0px;"></div>
                                                <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1"
                                                    style="margin-bottom: 0px;">
                                                    <label>Total</label>
                                                </div>
                                                <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2"
                                                    style="margin-bottom: 0px;">
                                                    <div class="form-group">
                                                        <div class="form-line">
                                                            <input id="id_total_iva" name="id_total_iva" type="text"
                                                                class="form-control" placeholder="0.00" value="0.00"
                                                                readonly>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane fade" id="custom-tabs-four-asumida" role="tabpanel"
                                            aria-labelledby="custom-tabs-four-asumida-tab">
                                            <div class="row">

                                            </div>
                                        </div>
                                        <div class="tab-pane fade" id="custom-tabs-four-factura" role="tabpanel"
                                            aria-labelledby="custom-tabs-four-factura-tab">
                                            <div class="row">
                                                <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2"
                                                    style="margin-bottom: 0px;">
                                                    <label>Seleccionar Factura</label>
                                                </div>
                                                <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4"
                                                    style="margin-bottom: 0px;">
                                                    <div class="form-group">
                                                        <select class="form-control select2" id="factura_id"
                                                            name="factura_id" data-live-search="true" onchange="cargarDatosFactura();">
                                                            <option value="" label>--Seleccione una opcion--</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3"
                                                    style="margin-bottom: 0px;">
                                                    <div class="form-group">
                                                        <input id="fechaFacturaID" name="fechaFacturaID" type="date"
                                                            value="<?php echo(date("Y")."-".date("m")."-".date("d")); ?>"
                                                            class="form-control" readonly>
                                                    </div>
                                                </div>
                                                <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3"
                                                    style="margin-bottom: 0px;">
                                                    <div class="form-group">
                                                        <input id="totalFacturaID" name="totalFacturaID" type="text"
                                                            class="form-control" placeholder="0.00" value="0.00"
                                                            readonly>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12  form-control-label">
                                <table class="table table-totalVenta">
                                    <tr>
                                        <td class="letra-blanca fondo-azul-claro negrita" width="90">Sub-Total
                                        </td>
                                        <td id="subtotal" width="100" class="derecha-texto negrita"><?php echo number_format($datos[1]['Sub'], 2)?></td>
                                        <input id="idSubtotal" name="idSubtotal" type="hidden" value="{{$datos[1]['Sub']}}" />
                                    </tr>
                                    <tr>
                                        <td class="letra-blanca fondo-azul-claro negrita">Descuento</td>
                                        <td id="descuento" class="derecha-texto negrita">0.00</td>
                                        <input id="idDescuento" name="idDescuento" type="hidden" />
                                    </tr>
                                    <tr>
                                        <td id="porcentajeIva" class="letra-blanca fondo-azul-claro negrita">Tarifa 12 %
                                        </td>
                                        <td id="tarifa12" class="derecha-texto negrita"><?php echo number_format($datos[1]['Tarifa12'], 2)?></td>
                                        <input id="idTarifa12" name="idTarifa12" type="hidden" value="{{$datos[1]['Tarifa12']}}" />
                                    </tr>
                                    <tr>
                                        <td class="letra-blanca fondo-azul-claro negrita">Tarifa 0%</td>
                                        <td id="tarifa0" class="derecha-texto negrita"><?php echo number_format($datos[1]['Tarifa0'], 2)?> </td>
                                        <input id="idTarifa0" name="idTarifa0" type="hidden" value="{{$datos[1]['Tarifa0']}}" />
                                    </tr>
                                    <tr>
                                        <td id="iva12" class="letra-blanca fondo-azul-claro negrita">Iva 12 %</td>
                                        <td id="iva" class="derecha-texto negrita"><?php echo number_format($datos[1]['Diva'], 2)?></td>
                                        <input id="idIva" name="idIva" type="hidden" value="{{$datos[1]['Iva']}}" />
                                    </tr>
                                    <tr>
                                        <td class="letra-blanca fondo-azul-claro negrita">Total</td>
                                        <td id="total" class="derecha-texto negrita"><?php echo number_format($datos[1]['servicioiva'], 2)?></td>
                                        <input id="idTotal" name="idTotal" type="hidden" value="{{$datos[1]['servicioiva']}}" />
                                    </tr>
                                    <tr>
                                        <td class="letra-blanca fondo-verde-claro negrita">Total Factura XML</td>
                                        <td id="totalXML" class="derecha-texto negrita">0.00</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="row">
                                <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6  form-control-label">
                                    <center><label>Iva Serv.</label>
                                        <input id="IvaServiciosID" name="IvaServiciosID" type="text"
                                            class="form-control centrar-texto" value="<?php echo number_format($datos[1]['servicioiva'], 2)?>" readonly>
                                    </center>
                                </div>
                                <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6  form-control-label">
                                    <center><label>Iva Bien.</label>
                                        <input id="IvaBienesID" name="IvaBienesID" type="text"
                                            class="form-control centrar-texto" value="<?php echo number_format($datos[1]['bieniva'], 2)?>" readonly>
                                    </center>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
<div class="modal fade" id="modal-TXT">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-secondary">
                <h4 class="modal-title">Cargar Compras</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="card-body">                        
                    <div class="form-group row">
                        <label for="idDescripcion" class="col-sm-2 col-form-label"><center>Archivo SRI : </center></label>
                        <div class="col-sm-10">
                            <input type="file" id="file_sri" name="file_sri" class="form-control" onchange="loadFile(this.files[0])"/>                
                        </div>
                    </div>  
                    <hr>
                    <h2 id="loading" style="display:none">Cargando...</h2>
                    <table id="example1" class="table table-bordered table-hover table-responsive sin-salto">
                        <thead>
                            <tr class="text-center neo-fondo-tabla">
                                <th></th>
                                <th>Fecha</th>
                                <th>Numero</th>
                                <th>Proveedor</th>
                                <th>Clave de Acceso</th>
                            </tr>
                        </thead>
                        <tbody> 
                        </tbody>
                    </table> 
                </div>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
            </div>             
        </div>
    </div>
</div>
@section('scriptAjax')
<script src="{{ asset('admin/js/ajax/autocompleteProveedor.js') }}"></script>
<script src="{{ asset('admin/js/ajax/autocompleteProductoCompra.js') }}"></script>
@endsection
<script type="text/javascript">
var documentoCompra = 0;
var data = [];
function buscarCompraClave(clave){
    $.ajax({
        async: false,
        url: '{{ url("compra/claveAcceso") }}'+ '/' + clave,
        dataType: "json",
        type: "GET",
        data: {
            buscar: clave
        },
        success: function(data){
            documentoCompra = data;         
        },
    });
}
function accionAsincrona(){
    $("#loading").show();
}
function accionAsincrona2(){
    $("#loading").hide();
}
async function loadFile(file) {
    await accionAsincrona();
    let text = await file.text();
    registros = text.split('\n');
    datosTXT = [];
    for(i=2; i < registros.length; i++){
        datos = registros[i].split('\t');
        if(datos[0]=='Factura'){
         //   buscarCompraClave(datos[9]);
          //  if(documentoCompra == 1){
                item = {}
                item ["fecha"] = datos[4];
                item ["numero"] = datos[1];
                item ["proveedor"] = datos[3];
                item ["claveAcceso"] = datos[9];
                item ["ruc"] = datos[2];
                datos3 = registros[i+1].split('\t');
                item ["total"] = datos3[0];
                datosTXT.push(item);
          //  }
        }
        
    }
    $("#example1").dataTable().fnDestroy();
    $('#example1').DataTable({
            responsive: false,
            lengthChange: false,
            autoWidth: false,
            bSort: false,
            ordering: false,
            info: false,
            data: datosTXT,
            columns: [
                { title: '',data: null,  wrap: true, 
                    "render": function (item) { 
                        return '<button onclick="cargarDocumento('+"'"+item.fecha+"',"+"'"+item.numero+"',"+"'"+item.claveAcceso+"',"+"'"+item.ruc+"',"+"'"+item.total+"',"+');" class="btn btn-sm btn-success"  data-toggle="tooltip" data-placement="top" title="Cargar"><i class="fas fa-file-upload" aria-hidden="true"></i></button>' 
                    } 
                },
                { title: "Fecha", data: "fecha" },
                { title: "Numero", data: "numero" },
                { title: "Proveedor", data: "proveedor" },
                { title: "Clave de Acceso", data: "claveAcceso" }
            ],
            buttons: ["copy", "excel"]
        }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
    await accionAsincrona2();

}
function showDiff(){
    var date2 = new Date(document.getElementById("transaccion_fecha").value)    
    var date1 = new Date(document.getElementById("transaccion_vencimiento").value) 
    if(date1<date2){        
        document.getElementById("transaccion_dias_plazo").value = 0;
    }else{   
        var diff = (date2 - date1)/1000;
        var diff = Math.abs(Math.floor(diff));    
        var days = Math.floor(diff/(24*60*60));        
        document.getElementById("transaccion_dias_plazo").value = days;
    }  
}
function fechaRet(){
    document.getElementById("retencion_fecha").value = document.getElementById("transaccion_fecha").value;
    document.getElementById("transaccion_inventario").value = document.getElementById("transaccion_fecha").value;
    document.getElementById("transaccion_vencimiento").value = document.getElementById("transaccion_fecha").value;
    document.getElementById("transaccion_impresion").value = document.getElementById("transaccion_fecha").value;
    document.getElementById("transaccion_caducidad").value = document.getElementById("transaccion_fecha").value;
}
function cargarDocumento(fecha, numero,clave,rucProveedor,total){
    var fechaAux = fecha.split('/');
    document.getElementById("transaccion_fecha").value = fechaAux[2] + "-" +fechaAux[1] + "-" + fechaAux[0];
    document.getElementById("transaccion_inventario").value = fechaAux[2] + "-" +fechaAux[1] + "-" + fechaAux[0];
    document.getElementById("transaccion_vencimiento").value = fechaAux[2] + "-" +fechaAux[1] + "-" + fechaAux[0];
    document.getElementById("transaccion_impresion").value = fechaAux[2] + "-" +fechaAux[1] + "-" + fechaAux[0];
    document.getElementById("transaccion_caducidad").value = fechaAux[2] + "-" +fechaAux[1] + "-" + fechaAux[0];
    numeroDoc = numero.split('-');
    document.getElementById("transaccion_serie").value = numeroDoc[0]+''+numeroDoc[1];
    document.getElementById("transaccion_secuencial").value = numeroDoc[2];
    document.getElementById("transaccion_autorizacion").value = clave;
    document.getElementById("totalXML").innerHTML = total;
    $("#modal-TXT").modal('hide');
}
if( <?php echo $cont;?> == 1){
    var id_item = 1;
}else{
    id_item = <?php echo $cont;?>;
} 
var id_itemRF = 1;
var id_itemRI = 1;
document.getElementById("idTarifa0").value = 0;
document.getElementById("idTarifa12").value = 0;
var combo = document.getElementById("transaccion_porcentaje_iva");
var porcentajeIva = combo.options[combo.selectedIndex].text;
porcentajeIva = parseFloat(porcentajeIva) / 100;
function cambioPago(){
    if(document.getElementById("transaccion_tipo_pago").value == 'EN EFECTIVO'){
        $('#caja_id').prop("required", true);
        document.getElementById("IdCajaL").classList.remove('invisible');
        document.getElementById("IdCajaI").classList.remove('invisible');
    }else{
        $('#caja_id').removeAttr("required");
        document.getElementById("IdCajaL").classList.add('invisible');
        document.getElementById("IdCajaI").classList.add('invisible');
    }
}
function recalcular(id,  iva, servicio) {
    
    cargarTotales($("input[name='Diva[]']")[id].value, Number($("input[name='Dtotal[]']")[id].value) * (-1), Number($(
        "input[name='Ddescuento[]']")[id].value) * (-1),servicio);
      
    $("input[name='Dtotal[]']")[id].value = Number((Number($("input[name='Dcantidad[]']")[id].value) * Number($(
        "input[name='Dpu[]']")[id].value)) - Number($("input[name='Ddescuento[]']")[id].value)).toFixed(2);

    if ($("input[name='Diva[]']")[id].value == "SI") {
        $("input[name='DViva[]']")[id].value = Number(Number($("input[name='Dtotal[]']")[id].value) * porcentajeIva)
            .toFixed(2);
    } else {
        $("input[name='DViva[]']")[id].value = "0.00";
    }
   
    cargarTotales($("input[name='Diva[]']")[id].value, Number($("input[name='Dtotal[]']")[id].value), Number($(
        "input[name='Ddescuento[]']")[id].value),servicio);
        
}
function agregarItem() {
    if (document.getElementById("id_total").value > 0 && document.getElementById("idProductoID").value != '') {
        total = Number(document.getElementById("id_total").value);
        descuento = Number(total * (document.getElementById("id_descuento").value / 100));
        var linea = $("#plantillaItemFactura").html();
        linea = linea.replace(/{ID}/g, id_item);
        linea = linea.replace(/{Dcantidad}/g, document.getElementById("id_cantidad").value);
        linea = linea.replace(/{Dcodigo}/g, document.getElementById("codigoProducto").value);
        linea = linea.replace(/{DprodcutoID}/g, document.getElementById("idProductoID").value);
        linea = linea.replace(/{Dnombre}/g, document.getElementById("buscarProducto").value);
        if (document.getElementById("tieneIva").checked) {
            linea = linea.replace(/{Diva}/g, "SI");
            linea = linea.replace(/{DViva}/g, round(Number((total - descuento) * porcentajeIva)));
            iva = "SI";
        } else {
            linea = linea.replace(/{Diva}/g, "NO");
            linea = linea.replace(/{DViva}/g, "0.00");
            iva = "NO";
        }
        linea = linea.replace(/{Dpu}/g, document.getElementById("id_pu").value);
        linea = linea.replace(/{Ddescuento}/g, Number(descuento).toFixed(2));
        linea = linea.replace(/{Dtotal}/g, Number(total - descuento).toFixed(2));
        linea = linea.replace(/{Dtotal2}/g, Number(total).toFixed(2));
        var aux = document.getElementById("bodegaProducto");
        linea = linea.replace(/{DbodegaAux}/g, aux.options[aux.selectedIndex].text);
        linea = linea.replace(/{Dbodega}/g, document.getElementById("bodegaProducto").value);
        var aux = document.getElementById("ccProducto");
        linea = linea.replace(/{DcconsumoAux}/g, aux.options[aux.selectedIndex].text);
        linea = linea.replace(/{Dcconsumo}/g, document.getElementById("ccProducto").value);
        linea = linea.replace(/{Ddescripcion}/g, document.getElementById("descripcionProducto").value);
        linea = linea.replace(/{DbienServ}/g, document.getElementById("tipoProductoID").value);
        $("#cargarItemFactura tbody").append(linea);
        id_item = id_item + 1;
        cargarTotales(iva, total, descuento, document.getElementById("tipoProductoID").value);
        resetearCampos();
        document.getElementById("baseFuente").value = document.getElementById("idSubtotal").value;
        document.getElementById("baseIva").value = document.getElementById("idIva").value;
    }
}

function cargarTotales(iva, total, descuento, tipo) {
   
    if (iva == "SI") {
        var ivaAux = Number(Number(total) * porcentajeIva).toFixed(2);
        if (tipo == "Bien") {
            document.getElementById("IvaBienesID").value = Number(Number(document.getElementById("IvaBienesID").value) + Number(ivaAux)).toFixed(2);
        } else {
            document.getElementById("IvaServiciosID").value = Number(Number(document.getElementById("IvaServiciosID").value) + Number(ivaAux)).toFixed(2);
        }
    }
    var subtotal = Number(Number(document.getElementById("subtotal").innerHTML) + total).toFixed(2);
    document.getElementById("subtotal").innerHTML = subtotal;
    document.getElementById("idSubtotal").value = subtotal;

    var tarifa12 = Number(Number(document.getElementById("tarifa12").innerHTML) + total - descuento).toFixed(2);
    var tarifa0 = Number(Number(document.getElementById("tarifa0").innerHTML) + total - descuento).toFixed(2);

    var descuento = Number(Number(document.getElementById("descuento").innerHTML) + descuento).toFixed(2);
    document.getElementById("descuento").innerHTML = descuento;
    document.getElementById("idDescuento").value = descuento;

    if (iva == "SI") {
        document.getElementById("tarifa12").innerHTML = tarifa12;
        document.getElementById("idTarifa12").value = tarifa12;
    } else {
        document.getElementById("tarifa0").innerHTML = tarifa0;
        document.getElementById("idTarifa0").value = tarifa0;
    }
    calcularTotales();
}

function calcularTotales() {
    var iva = round(Number(document.getElementById("tarifa12").innerHTML) * porcentajeIva);
    document.getElementById("iva").innerHTML = iva;
    document.getElementById("idIva").value = iva;
    
    document.getElementById("baseFuente").value=document.getElementById("subtotal").innerHTML;
    document.getElementById("baseIva").value=document.getElementById("iva").innerHTML;

    var total = Number(Number(document.getElementById("tarifa12").innerHTML) + Number(document.getElementById("tarifa0")
        .innerHTML) + Number(document.getElementById("iva").innerHTML)).toFixed(2);

    document.getElementById("total").innerHTML = total;
    document.getElementById("idTotal").value = total;
}

function resetearCampos() {
    document.getElementById("descripcionProducto").value = "";
    document.getElementById("id_cantidad").value = 1;
    document.getElementById("codigoProducto").value = "";
    document.getElementById("idProductoID").value = "";
    document.getElementById("buscarProducto").value = "";
    document.getElementById("id_pu").value = "0.00";
    document.getElementById("id_descuento").value = "0.00";
    document.getElementById("id_total").value = "0.00";
}

function eliminarItem(id, iva, total, descuento, tipo) {
    cargarTotales(iva, total * (-1), descuento * (-1), tipo);
    $("#row_" + id).remove();

}

function calcularTotal() {
    document.getElementById("buscarProducto").classList.remove('is-invalid');
    document.getElementById("id_total").value = Number(document.getElementById("id_cantidad").value * document
        .getElementById("id_pu").value).toFixed(2);
}

function seleccionarIva() {
    var combo = document.getElementById("transaccion_porcentaje_iva");
    var porcentajeIva = combo.options[combo.selectedIndex].text;
    porcentajeIva = parseFloat(porcentajeIva) / 100;
    document.getElementById("porcentajeIva").innerHTML = "Tarifa " + combo.options[combo.selectedIndex].text;
    document.getElementById("iva12").innerHTML = "Iva " + combo.options[combo.selectedIndex].text;
}

function ponerCeros(num) {
    num = num + '';
    while (num.length <= 1) {
        num = '0' + num;
    }
    return num;
}
function round(num) {
        var m = Number((Math.abs(num) * 100).toPrecision(15));
         m =Math.round(m) / 100 * Math.sign(num);
         return (m).toFixed(2);
    }
/************PROCESO DE RETENCION EN LA FUENTE******************/
function calcularRF() {
    codRF = document.getElementById("conceptoFuenteID");
    porcentajeRF = document.getElementById("conceptoFuenteIDAux");
    porcentajeRF = porcentajeRF.options[codRF.selectedIndex].text;
    document.getElementById("valorFuente").value = round((Number(document.getElementById("baseFuente").value) * Number(
        porcentajeRF)) / 100);
}

function agregarItemRF() {
    
        baseRF = Number(document.getElementById("baseFuente").value);
        porcentajeRF = document.getElementById("conceptoFuenteIDAux");
        codRF = document.getElementById("conceptoFuenteID");
        valorRF = Number(document.getElementById("valorFuente").value);

        var linea = $("#plantillaItemRF").html();
        linea = linea.replace(/{ID}/g, id_itemRF);
        linea = linea.replace(/{DbaseRF}/g, round(baseRF));
        linea = linea.replace(/{DcodigoRF}/g, codRF.options[codRF.selectedIndex].text);
        linea = linea.replace(/{DRFID}/g, codRF.value);
        linea = linea.replace(/{DporcentajeRF}/g, porcentajeRF.options[codRF.selectedIndex].text);
        linea = linea.replace(/{DvalorRF}/g, round(valorRF));
        $("#cargarItemRF tbody").append(linea);
        id_itemRF = id_itemRF + 1;
        totalRF(valorRF, baseRF);
        resetearCamposRF();
    
}

function totalRF(valorF, baseF) {
    document.getElementById("id_total_fuente").value = round(Number(document.getElementById("id_total_fuente").value) +
        Number(valorF));
    document.getElementById("totalBaseFuenteId").value = round(Number(document.getElementById("totalBaseFuenteId").value) +
        Number(baseF));
}

function resetearCamposRF() {
    document.getElementById("baseFuente").value = "0.00";
    document.getElementById("valorFuente").value = "0.00";
}

function eliminarItemF(id, valorF, baseF) {
    $("#rowRF_" + id).remove();
    totalRF(valorF * (-1), baseF * (-1));
}
/************PROCESO DE RETENCION DE IVA******************/
function calcularRI() {
    codRI = document.getElementById("conceptoIvaID");
    porcentajeRI = document.getElementById("conceptoIvaIDAux");
    porcentajeRI = porcentajeRI.options[codRI.selectedIndex].text
    document.getElementById("valorIva").value = round((Number(document.getElementById("baseIva").value) * Number(
        porcentajeRI)) / 100);
}

function agregarItemRI() {
  
        baseRI = Number(document.getElementById("baseIva").value);
        porcentajeRI = document.getElementById("conceptoIvaIDAux");
        codRI = document.getElementById("conceptoIvaID");
        valorRI = Number(document.getElementById("valorIva").value);

        var linea = $("#plantillaItemRI").html();
        linea = linea.replace(/{ID}/g, id_itemRI);
        linea = linea.replace(/{DbaseRI}/g, round(baseRI));
        linea = linea.replace(/{DcodigoRI}/g, codRI.options[codRI.selectedIndex].text);
        linea = linea.replace(/{DRIID}/g, codRI.value);
        linea = linea.replace(/{DporcentajeRI}/g, porcentajeRI.options[codRI.selectedIndex].text);
        linea = linea.replace(/{DvalorRI}/g, round(valorRI));
        $("#cargarItemRI tbody").append(linea);
        id_itemRI = id_itemRI + 1;
        totalRI(valorRI);
        resetearCamposRI();
    
}

function totalRI(valorI) {
    document.getElementById("id_total_iva").value = round(Number(document.getElementById("id_total_iva").value) +
        Number(valorI));
}

function resetearCamposRI() {
    document.getElementById("baseIva").value = "0.00";
    document.getElementById("valorIva").value = "0.00";
}

function eliminarItemI(id, valorI) {
    $("#rowRI_" + id).remove();
    totalRI(valorI * (-1));
}
function validarForm(){
    comprobante = document.getElementById("tipo_comprobante_id");
    comprobanteCodigo = document.getElementById("tipo_comprobante_codigo");
    if(comprobanteCodigo.options[comprobante.selectedIndex].value != '04' ){
        if(document.getElementById("idSubtotal").value != document.getElementById("totalBaseFuenteId").value){
            bootbox.alert({
                message: "El total se retención en la fuente es diferente del subtotal de la factura.",
                size: 'small'
            });
            return false;
        }
    }
    if(document.getElementById("proveedorID").value == ''){
        bootbox.alert({
            message: "Seleccione un proveedor antes de guardar.",
            size: 'small'
        });
        return false;
    }
    
    if(comprobanteCodigo.options[comprobante.selectedIndex].value == '04' || comprobanteCodigo.options[comprobante.selectedIndex].value == '05'){
        if(document.getElementById("factura_id").value == '0'){
            alert('Seleccione la factura antes de guardar');
            return false
        }
    }
    var a = document.getElementById("cargarItemRI");
    var rows = a.rows.length;
    var a2 = document.getElementById("cargarItemRF");
    var rows2 = a2.rows.length;

    if ((rows+rows2)+Number(id_itemRI) == 2 && comprobanteCodigo.options[comprobante.selectedIndex].value != '04'){
        alert('Registre datos de retencion antes de guardar');
        return false
    }
    if((rows+rows2) > 2 && comprobanteCodigo.options[comprobante.selectedIndex].value == '04'){
        alert('Elimine los datos de retencion antes de guardar');
        return false
    }
    return true;
}
function cargarDatosFactura(){
    $.ajax({
        url: '{{ url("datosFactCompra/searchN") }}'+ '/' + document.getElementById("factura_id").value,
        dataType: "json",
        type: "GET",
        data: {
            buscar: document.getElementById("factura_id").value
        },
        success: function(data){
            document.getElementById("fechaFacturaID").value = data.transaccion_fecha;       
            document.getElementById("totalFacturaID").value = Number(data.transaccion_total).toFixed(2);          
        },
    });
}
function fechaRet(){
    document.getElementById("retencion_fecha").value = document.getElementById("transaccion_fecha").value;
    document.getElementById("transaccion_inventario").value = document.getElementById("transaccion_fecha").value;
    document.getElementById("transaccion_vencimiento").value = document.getElementById("transaccion_fecha").value;
    document.getElementById("transaccion_impresion").value = document.getElementById("transaccion_fecha").value;
    document.getElementById("transaccion_caducidad").value = document.getElementById("transaccion_fecha").value;
}
function cargarSustento(centroConsumo){
    var centroConsumoSustento = document.getElementById("idCCSustento");
    document.getElementById("sustento_id").value = centroConsumoSustento.options[centroConsumo.selectedIndex].value;
    $("#sustento_id").val(centroConsumoSustento.options[centroConsumo.selectedIndex].value).trigger('change');
}
function cargarSustento2(centroConsumo){
    var centroConsumoSustento = document.getElementById("idCCSustento");
    document.getElementById("sustento_id").value = centroConsumoSustento.options[centroConsumo.selectedIndex].value;
    $("#sustento_id").val(centroConsumoSustento.options[centroConsumo.selectedIndex].value).trigger('change');
}
</script>
@endsection