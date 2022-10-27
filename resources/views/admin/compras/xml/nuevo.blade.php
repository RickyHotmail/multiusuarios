@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-primary card-outline">
    <form class="form-horizontal" method="POST"  action="{{ url("transaccionCompra") }} " onsubmit="return validarForm();">
        @csrf
        <div class="card-header">
            <div class="row">
                <div class="col-sm-7">
                    <h2 class="card-title"><b>COMPRAS - Nuevo Documento</b> - <h style="color: #990303;">Su firma electronica caduca {{ date('d/m/Y H:i:s', $caduca) }}<h></h2>
                </div>
                <div class="col-sm-5">
                    <div class="float-right">
                        <button type="button" id="nuevoID" onclick="nuevo()" class="btn btn-primary btn-sm"><i
                                    class="fas fa-receipt"></i><span> Nuevo</span></button>
                        <a id="xmlID" href="{{ url("compras/xml/{$rangoDocumento->puntoEmision->punto_id}") }}" class="btn btn-secondary btn-sm" disabled><i
                                class="fas fa-file-code"></i><span> Archivo TXT</span></a>
                        <button id="guardarID" type="submit" class="btn btn-success btn-sm" @if(isset($poveedorXML) == false) disabled @endif><i
                                class="fa fa-save"></i><span> Guardar</span></button>
                        <a href="javascript: history.go(-1)" class="btn btn-danger btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</a>  
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
                            <label>Fecha Doc. </label>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                            <div class="form-group">
                                <div class="form-line">
                                    <input type="date" class="form-control" id="transaccion_fecha"
                                        name="transaccion_fecha"
                                        @if(isset($xml->infoFactura)) value="{{DateTime::createFromFormat('d/m/Y',$xml->infoFactura->fechaEmision)->format('Y-m-d')}}" 
                                        @elseif(isset($xml->infoNotaCredito)) value="{{DateTime::createFromFormat('d/m/Y',$xml->infoNotaCredito->fechaEmision)->format('Y-m-d')}}" 
                                        @else value='<?php echo(date("Y")."-".date("m")."-".date("d")); ?>' @endif 
                                        onchange="fechaRet();" required>
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
                                        @if(isset($xml->infoFactura)) value="{{DateTime::createFromFormat('d/m/Y',$xml->infoFactura->fechaEmision)->format('Y-m-d')}}" 
                                        @elseif(isset($xml->infoNotaCredito)) value="{{DateTime::createFromFormat('d/m/Y',$xml->infoNotaCredito->fechaEmision)->format('Y-m-d')}}" 
                                        @else value='<?php echo(date("Y")."-".date("m")."-".date("d")); ?>' @endif 
                                        required>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 form-control-label ">
                            <label>Fecha Ven.</label>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                            <div class="form-group">
                                <div class="form-line">
                                    <input type="date" class="form-control" id="transaccion_vencimiento" onchange="showDiff();"
                                        name="transaccion_vencimiento"
                                        @if(isset($xml->infoFactura)) value="{{DateTime::createFromFormat('d/m/Y',$xml->infoFactura->fechaEmision)->format('Y-m-d')}}" 
                                        @elseif(isset($xml->infoNotaCredito)) value="{{DateTime::createFromFormat('d/m/Y',$xml->infoNotaCredito->fechaEmision)->format('Y-m-d')}}" 
                                        @else value='<?php echo(date("Y")."-".date("m")."-".date("d")); ?>' @endif 
                                        required>
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
                                        @if(isset($xml->infoFactura)) value="{{DateTime::createFromFormat('d/m/Y',$xml->infoFactura->fechaEmision)->format('Y-m-d')}}"
                                        @elseif(isset($xml->infoNotaCredito)) value="{{DateTime::createFromFormat('d/m/Y',$xml->infoNotaCredito->fechaEmision)->format('Y-m-d')}}" 
                                        @else value='<?php echo(date("Y")."-".date("m")."-".date("d")); ?>' @endif 
                                        required>
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
                                <input id="proveedorID" name="proveedorID" type="hidden" @if(isset($poveedorXML)) value="{{$poveedorXML->proveedor_id}}" @endif>
                                <input id="buscarProveedor" name="buscarProveedor" type="text" class="form-control"
                                    placeholder="Proveedor" required @if(isset($poveedorXML)) value="{{$poveedorXML->proveedor_nombre}}" " @else disabled @endif>
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
                                    placeholder="9999999999999" @if(isset($poveedorXML)) value="{{$poveedorXML->proveedor_ruc}}" @endif required readonly>
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
                                        @if(isset($xml->infoFactura)) value="{{DateTime::createFromFormat('d/m/Y',$xml->infoFactura->fechaEmision)->format('Y-m-d')}}"  
                                        @elseif(isset($xml->infoNotaCredito)) value="{{DateTime::createFromFormat('d/m/Y',$xml->infoNotaCredito->fechaEmision)->format('Y-m-d')}}" 
                                        @else value='<?php echo(date("Y")."-".date("m")."-".date("d")); ?>' @endif 
                                        required>
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
                                    <option value="{{$comprobante->tipo_comprobante_id}}" @if(isset($xml)) @if($xml->infoTributaria->codDoc == $comprobante->tipo_comprobante_codigo) selected @endif @endif>
                                        {{$comprobante->tipo_comprobante_nombre}}
                                    </option>
                                    @endforeach
                                </select>
                                <select class="invisible" id="tipo_comprobante_codigo" name="tipo_comprobante_codigo"
                                    data-live-search="true" >
                                    @foreach($comprobantes as $comprobante)
                                    <option value="{{$comprobante->tipo_comprobante_codigo}}" @if(isset($xml)) @if($xml->infoTributaria->codDoc == $comprobante->tipo_comprobante_codigo) selected @endif @endif>
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
                                    <option value="EN EFECTIVO">EN EFECTIVO</option>
                                    <option value="CONTADO">CONTADO</option>
                                    <option value="CREDITO" selected>CREDITO</option>
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
                                        class="form-control " placeholder="001001" value="@if(isset($xml)){{$xml->infoTributaria->estab}}{{$xml->infoTributaria->ptoEmi}}@endif" required>
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
                                        class="form-control " placeholder="0000000001" value="@if(isset($xml)){{$xml->infoTributaria->secuencial}}@endif" required>
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
                                        placeholder="1702201205176001321000110010030001000011234567816" value="@if(isset($xml)){{$xml->infoTributaria->claveAcceso}}@endif" required>
                                </div>
                            </div>
                        </div>
                        <div id="IdCajaL" class="col-lg-1 col-md-1 col-sm-1 col-xs-1 form-control-label  invisible"
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
                                    onclick="seleccionarIva()" >
                                    @foreach($tarifasIva as $iva)
                                    <option value="{{$iva->tarifa_iva_porcentaje}}" @if($civa[1]["codigo"]==$iva->tarifa_iva_codigo) selected @endif>{{$iva->tarifa_iva_porcentaje}}%
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
                                    <input id="id_total" name="id_total" type="text" class="form-control centrar-texto"
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
                                    data-live-search="true" onchange="cargarSustento();">
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
                    <?php $count = 1; $iva = 0; $total = 0; $subtotal = 0; $descuento = 0; $t0 = 0; $t12 = 0; $ss = 0;$sb = 0;?>
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
                                            <th width="200">Producto</th>
                                            <th width="50">Con Iva</th>
                                            <th width="75">Iva</th>
                                            <th width="100">P.U.</th>
                                            <th width="100">Descuento</th>
                                            <th width="100">Total</th>
                                            <th width="150">Bodega</th>
                                            <th width="250">C. Consumo</th>
                                            <th width="300">Descripcion</th>
                                            <th width="90">Bien/Serv.</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                   
                                    @if(isset($datos))
                                       
                                        @for ($i = 1; $i <= count($datos); ++$i)
                                        
                                        <tr id="row_{{$i}}">
                                            <td><a onclick="eliminarItem({{$i}}, '{{ $datos[$i]['iva']}}', {{ $datos[$i]['subtotal2']}}, {{ $datos[$i]['descuento']}},'{{ $datos[$i]['bien']}}');" class="btn btn-danger waves-effect" style="padding: 2px 8px;">X</a></td>
                                            <td>{{ $datos[$i]['cantidad']}}<input class="invisible" name="Dcantidad[]" value="{{ $datos[$i]['cantidad']}}" /></td>
                                            <td>{{ $datos[$i]['codigo']}}<input class="invisible" name="DprodcutoID[]" value="{{ $datos[$i]['id']}}" /><input class="invisible" name="Dcodigo[]" value="{{ $datos[$i]['codigo']}}" /></td>
                                            <td style="white-space: pre-wrap;">{{ $datos[$i]['descripcion']}}<input class="invisible" name="Dnombre[]" value="{{ $datos[$i]['descripcion']}}" /></td>
                                            <td>{{ $datos[$i]['iva']}}<input class="invisible" name="Diva[]" value="{{ $datos[$i]['iva']}}" /></td>
                                            <td>{{ $datos[$i]['diva']}}<input class="invisible" name="DViva[]" value="{{ $datos[$i]['diva']}}" /></td>
                                            <td>{{ $datos[$i]['valor']}}<input class="invisible" name="Dpu[]" value="{{ $datos[$i]['valor']}}" /></td>
                                            <td>{{ $datos[$i]['descuento']}}<input class="invisible" name="Ddescuento[]" value="{{ $datos[$i]['descuento']}}" /></td>
                                            <td>{{ $datos[$i]['subtotal']}}<input class="invisible" name="Dtotal[]" value="{{ $datos[$i]['subtotal']}}" /></td>
                                            <td><select class="form-control select2" id="Dbodega" name="Dbodega[]"
                                                    data-live-search="true" >
                                                    @foreach($bodegas as $bodega)
                                                        <option value="{{$bodega->bodega_id}}">{{$bodega->bodega_nombre}}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <select class="form-control" id="cconsumos" name="Dcconsumo[]"
                                                    data-live-search="true" onchange="cargarSustento2();" required>
                                                    <option value="" selected disabled>--Seleccione una opcion--</option>
                                                    @foreach($centros as $centro)
                                                        <option value="{{$centro->centro_consumo_id}}" >{{$centro->centro_consumo_nombre}}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            
                                            <td><input type="text" class="form-control" name="Ddescripcion[]" value="{{ $datos[$i]['descripcion']}}" /></td>
                                            <td>{{ $datos[$i]['bien']}}<input class="invisible" name="DbienServ[]" value="{{ $datos[$i]['bien']}}" /></td>
                                            <?php $iva =$iva+$datos[$i]['diva']; 
                                            $subtotal =$subtotal+$datos[$i]['subtotal2']; 
                                            $descuento = $descuento+$datos[$i]['descuento']; 
                                            $t0 = $t0+$datos[$i]['t0']; 
                                            $t12 = $t12+$datos[$i]['t12']; 
                                            $sb =$sb+$datos[$i]['sb'];
                                            $ss = $ss+$datos[$i]['ss'];
                                            $total = $total+$datos[$i]['total'];

                                            ?>
                                            <?php $count++; ?>
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
                                                                @if(isset($xml->infoFactura)) value="{{DateTime::createFromFormat('d/m/Y',$xml->infoFactura->fechaEmision)->format('Y-m-d')}}"
                                                                @elseif(isset($xml->infoNotaCredito)) value="{{DateTime::createFromFormat('d/m/Y',$xml->infoNotaCredito->fechaEmision)->format('Y-m-d')}}" 
                                                                @else value='<?php echo(date("Y")."-".date("m")."-".date("d")); ?>' @endif >
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
                                                                value="{{ $secuencial }}">
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
                                                            class="form-control" placeholder="0.00" value="0.00" onkeyup="calcularRF();" onclick="calcularRF();"
                                                            >
                                                    </div>
                                                </div>
                                                <div class="col-lg-5 col-md-5 col-sm-5 col-xs-5"
                                                    style="margin-bottom : 0px;">
                                                    <div class="form-group">
                                                        <select class="form-control select2" id="conceptoFuenteID"
                                                            name="conceptoFuenteID" data-live-search="true"
                                                            onchange="calcularRF()">
                                                            <option value="" label>--Seleccione una opcion--</option>
                                                            @foreach($conceptosFuente as $concepto)
                                                            <option value="{{$concepto->concepto_id}}">
                                                                {{$concepto->concepto_codigo}} -
                                                                {{$concepto->concepto_porcentaje}}% -
                                                                {{$concepto->concepto_nombre}}</option>
                                                            @endforeach
                                                        </select>
                                                        <select class="invisible" id="conceptoFuenteIDAux"
                                                            name="conceptoFuenteIDAux">
                                                            <option value="" label>--Seleccione una opcion--</option>
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
                                                            <input type="hidden" id="totalBaseFuenteId" value="0.00">
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
                                                                class="form-control" placeholder="Total" value="0.00" onkeyup="calcularRI();" onclick="calcularRI();"
                                                                >
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5"
                                                    style="margin-bottom: 0px;">
                                                    <div class="form-group">
                                                        <select class="form-control select2" id="conceptoIvaID"
                                                            name="conceptoIvaID" data-live-search="true"
                                                            onchange="calcularRI()">
                                                            <option value="" label>--Seleccione una opcion--</option>
                                                            @foreach($conceptosIva as $concepto)
                                                            <option value="{{$concepto->concepto_id}}">
                                                                {{$concepto->concepto_codigo}} -
                                                                {{$concepto->concepto_porcentaje}}% -
                                                                {{$concepto->concepto_nombre}}</option>
                                                            @endforeach
                                                        </select>
                                                        <select class="invisible" id="conceptoIvaIDAux"
                                                            name="conceptoIvaIDAux">
                                                            <option value="" label>--Seleccione una opcion--</option>
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
                                                                class="form-control" placeholder="Total" value="0.00" readonly
                                                                >
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
                                            <div id="idSeleccionarFactura" class="row">
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
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <div class="form-group clearfix">
                                                        <div class="form-group clearfix">
                                                            <div class="icheck-primary d-inline">
                                                                <input type="checkbox" id="manualID" name="manualID" onchange="OtraFactura();" >
                                                                <label for="manualID">Otra Factura
                                                                </label>
                                                            </div>
                                                            
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div id="idOtraFactura" class="row invisible">
                                                <div class="col-sm-3">
                                                    <div class="form-group">
                                                        <label>Serie Factura</label>
                                                        <input id="serieFacManual" name="serieFacManual" type="text"
                                                            placeholder="001001"
                                                            class="form-control">
                                                    </div>
                                                </div>
                                                <div class="col-sm-3">
                                                    <div class="form-group">
                                                        <label>Numero Factura</label>
                                                        <input id="scuencialFacManual" name="scuencialFacManual" type="text"
                                                            placeholder="000000001"
                                                            class="form-control">
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label>Autorizacion Factura</label>
                                                        <input id="autorizacionFacManual" name="autorizacionFacManual" type="text"
                                                            placeholder="1702201205176001321000110010030001000011234567816"
                                                            class="form-control">
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
                                        <td id="subtotal" width="100" class="derecha-texto negrita">0.00</td>
                                        <input id="idSubtotal" name="idSubtotal" type="hidden" />
                                    </tr>
                                    <tr>
                                        <td class="letra-blanca fondo-azul-claro negrita">Descuento</td>
                                        <td id="descuento" class="derecha-texto negrita">0.00</td>
                                        <input id="idDescuento" name="idDescuento" type="hidden" />
                                    </tr>
                                    <tr>
                                        <td id="porcentajeIva" class="letra-blanca fondo-azul-claro negrita">Tarifa 12 %
                                        </td>
                                        <td id="tarifa12" class="derecha-texto negrita">0.00</td>
                                        <input id="idTarifa12" name="idTarifa12" type="hidden" />
                                    </tr>
                                    <tr>
                                        <td class="letra-blanca fondo-azul-claro negrita">Tarifa 0%</td>
                                        <td id="tarifa0" class="derecha-texto negrita">0.00</td>
                                        <input id="idTarifa0" name="idTarifa0" type="hidden" />
                                    </tr>
                                    <tr>
                                        <td id="iva12" class="letra-blanca fondo-azul-claro negrita">Iva 12 %</td>
                                        <td id="iva" class="derecha-texto negrita">0.00</td>
                                        <input id="idIva" name="idIva" type="hidden" />
                                    </tr>
                                    <tr>
                                        <td class="letra-blanca fondo-azul-claro negrita">Total</td>
                                        <td id="total" class="derecha-texto negrita">0.00</td>
                                        <input id="idTotal" name="idTotal" value="0" type="hidden" />
                                    </tr>
                                    <tr>
                                        <td class="letra-blanca fondo-verde-claro negrita">Total Factura XML</td>
                                        <td id="totalxml" name="totalxml" class="derecha-texto negrita">0.00</td>
                                       
                                    </tr>

                                </table>
                            </div>
                            <div class="row">
                                <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6  form-control-label">
                                    <center><label>Iva Serv.</label>
                                        <input id="IvaServiciosID" name="IvaServiciosID" type="text"
                                            class="form-control centrar-texto" value="0.00" readonly>
                                    </center>
                                </div>
                                <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6  form-control-label">
                                    <center><label>Iva Bien.</label>
                                        <input id="IvaBienesID" name="IvaBienesID" type="text"
                                            class="form-control centrar-texto" value="0.00" readonly>
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
<!-- /.card -->
@section('scriptAjax')
<script src="{{ asset('admin/js/ajax/autocompleteProveedor.js') }}"></script>
<script src="{{ asset('admin/js/ajax/autocompleteProductoCompra.js') }}"></script>
@endsection
<script type="text/javascript">
    
var id_item =1 ;

var id_itemRF = 1;
var id_itemRI = 1;
document.getElementById("idTarifa0").value = 0;
document.getElementById("idTarifa12").value = 0;
var combo = document.getElementById("transaccion_porcentaje_iva");
var porcentajeIva = combo.options[combo.selectedIndex].text;
porcentajeIva = parseFloat(porcentajeIva) / 100;

function cargarmetodo(){
    
    document.getElementById("totalxml").innerHTML=Number(<?php echo $totalxml;?>).toFixed(2);
    
    $('#transaccion_porcentaje_iva').css('pointer-events', 'none');
     id_item =<?php echo $count;?>;
    document.getElementById("subtotal").innerHTML = (<?php echo $subtotal;?>).toFixed(2);
    document.getElementById("idSubtotal").value = Number(<?php echo $subtotal;?>).toFixed(2);
    document.getElementById("descuento").innerHTML = (<?php echo $descuento;?>).toFixed(2);
    document.getElementById("idDescuento").value = <?php echo $descuento;?>;
    document.getElementById("tarifa12").innerHTML = (<?php echo  $t12;?>).toFixed(2);
    document.getElementById("idTarifa12").value = <?php echo  $t12;?>;
    document.getElementById("tarifa0").innerHTML = (<?php echo $t0;?>).toFixed(2);
    document.getElementById("idTarifa0").value = <?php echo $t0;?>;
    document.getElementById("iva").innerHTML = (<?php echo $iva;?>).toFixed(2);
    document.getElementById("idIva").value = <?php echo $iva;?>;
    document.getElementById("total").innerHTML = (<?php echo $total;?>).toFixed(2);
    document.getElementById("idTotal").value = <?php echo $total;?>;

    document.getElementById("IvaServiciosID").value = (<?php echo $ss;?>).toFixed(2);
    document.getElementById("IvaBienesID").value = (<?php echo $sb;?>).toFixed(2);

    document.getElementById("baseFuente").value = (<?php echo $subtotal;?>).toFixed(2);
    document.getElementById("baseIva").value = (<?php echo $iva;?>).toFixed(2);
}

function cargarSustento(){
    var centroConsumo = document.getElementById("ccProducto");
    var centroConsumoSustento = document.getElementById("idCCSustento");
    document.getElementById("sustento_id").value = centroConsumoSustento.options[centroConsumo.selectedIndex].value;
    $("#sustento_id").val(centroConsumoSustento.options[centroConsumo.selectedIndex].value).trigger('change');
}
function cargarSustento2(){
    var centroConsumo = document.getElementById("cconsumos");
    var centroConsumoSustento = document.getElementById("idCCSustento");
    document.getElementById("sustento_id").value = centroConsumoSustento.options[centroConsumo.selectedIndex].value;
    $("#sustento_id").val(centroConsumoSustento.options[centroConsumo.selectedIndex].value).trigger('change');
}
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
function nuevo() {
    $('#transaccion_porcentaje_iva').css('pointer-events', 'none');
    document.getElementById("nuevoID").disabled = true;
    document.getElementById("guardarID").disabled = false;
    document.getElementById("buscarProducto").disabled = false;
    document.getElementById("buscarProveedor").disabled = false;
    document.getElementById("baseFuente").disabled = false;
    document.getElementById("valorFuente").disabled = false;
    document.getElementById("baseIva").disabled = false;
    document.getElementById("valorIva").disabled = false;
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
function agregarItem() {
    if(document.getElementById("ccProducto").value != ''){
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
    }else{
        bootbox.alert({
            message: "Seleccione un centro de consumo.",
            size: 'small'
        });
    }
}

function cargarTotales(iva, total, descuento, tipo) {
    if (iva == "SI") {
        document.getElementById("IvaBienesID").value = 0;
        document.getElementById("IvaServiciosID").value = 0;
        for (var i = 1; i < id_item; i++) {
            if($("input[name='Dtotal[]']")[i]){
                if($("input[name='Diva[]']")[i].value == "SI"){
                    var ivaAux = Number($("input[name='Dtotal[]']")[i].value) * porcentajeIva;
                    if ($("input[name='DbienServ[]']")[i].value == "Bien") {
                        document.getElementById("IvaBienesID").value = Number(document.getElementById("IvaBienesID").value) + Number(ivaAux);
                    } else {
                        document.getElementById("IvaServiciosID").value = Number(document.getElementById("IvaServiciosID").value) + Number(ivaAux);
                    }
                }
            }
        }
        document.getElementById("IvaBienesID").value = Number(document.getElementById("IvaBienesID").value ).toFixed(2);
        document.getElementById("IvaServiciosID").value = Number(document.getElementById("IvaServiciosID").value ).toFixed(2);

     /*   var ivaAux = Number(Number(total) * porcentajeIva).toFixed(2);
        if (tipo == "Bien") {
            document.getElementById("IvaBienesID").value = Number(Number(document.getElementById("IvaBienesID").value) + Number(ivaAux)).toFixed(2);
        } else {
            document.getElementById("IvaServiciosID").value = Number(Number(document.getElementById("IvaServiciosID").value) + Number(ivaAux)).toFixed(2);
        }*/
    }
    var ivaAux = Number(Number(total) * porcentajeIva).toFixed(2);
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
    var iva = Number(Number(document.getElementById("tarifa12").innerHTML) * porcentajeIva).toFixed(2);
    document.getElementById("iva").innerHTML = iva;
    document.getElementById("idIva").value = iva;

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
    calcularIvasBienServicios();
}
function calcularIvasBienServicios(){
    document.getElementById("IvaBienesID").value = '0.00';
    document.getElementById("IvaServiciosID").value = '0.00';
    for (var i = 1; i < id_item; i++) {
        if($("input[name='Dtotal[]']")[i]){
            if($("input[name='Diva[]']")[i].value == "SI"){
                var ivaAux = Number($("input[name='Dtotal[]']")[i].value) * porcentajeIva;
                if ($("input[name='DbienServ[]']")[i].value == "Bien") {
                    document.getElementById("IvaBienesID").value = Number(document.getElementById("IvaBienesID").value) + Number(ivaAux);
                } else {
                    document.getElementById("IvaServiciosID").value = Number(document.getElementById("IvaServiciosID").value) + Number(ivaAux);
                }
            }
        }
    }
    document.getElementById("IvaBienesID").value = Number(document.getElementById("IvaBienesID").value ).toFixed(2);
    document.getElementById("IvaServiciosID").value = Number(document.getElementById("IvaServiciosID").value ).toFixed(2);
}
function calcularTotal() {
    document.getElementById("buscarProducto").classList.remove('is-invalid');
    document.getElementById("id_total").value = Number(document.getElementById("id_cantidad").value * document
        .getElementById("id_pu").value).toFixed(2);
}

function seleccionarIva() {
    var combo = document.getElementById("transaccion_porcentaje_iva");
    porcentajeIva = combo.options[combo.selectedIndex].text;
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
    if (document.getElementById("baseFuente").value > 0) {
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
    }else{
        bootbox.alert({
            message: "La base no puede ser 0.00",
            size: 'small'
        });
    }
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
    if (document.getElementById("baseIva").value > 0) {
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
    }else{
        bootbox.alert({
            message: "La base no puede ser 0.00",
            size: 'small'
        });
    }
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
    $subtotal=round(Number(document.getElementById("idSubtotal").value)-Number(document.getElementById("idDescuento").value));
    if(comprobanteCodigo.options[comprobante.selectedIndex].value != '04' ){
        if($subtotal != document.getElementById("totalBaseFuenteId").value){
            bootbox.alert({
                message: "El total se retención en la fuente es diferente del subtotal de la factura.",
                size: 'small'
            });
            return false;
        }
        if(document.getElementById("proveedorID").value == ''){
            bootbox.alert({
                message: "Seleccione un proveedor antes de guardar.",
                size: 'small'
            });
            return false;
        }
    }
   
    if(comprobanteCodigo.options[comprobante.selectedIndex].value == '04' || comprobanteCodigo.options[comprobante.selectedIndex].value == '05'){
        if(document.getElementById("manualID").checked==true){
            if(document.getElementById("serieFacManual").value == ''){
                bootbox.alert({
                    message: "Tiene que registrar la serie de la factura",
                    size: 'small'
                });
                return false
            }
            if(document.getElementById("scuencialFacManual").value == ''){
                bootbox.alert({
                    message: "Tiene que registrar el secuencial de la factura",
                    size: 'small'
                });
                return false
            }
            if(document.getElementById("autorizacionFacManual").value == ''){
                bootbox.alert({
                    message: "Tiene que registrar la autorizacion de la factura",
                    size: 'small'
                });
                return false
            }
        }else{
            if(document.getElementById("factura_id").value == '0'){
                bootbox.alert({
                    message: "Seleccione la factura antes de guardar",
                    size: 'small'
                });
                return false
            }
        }
    }
    var a = document.getElementById("cargarItemRI");
    var rows = a.rows.length;
    var a2 = document.getElementById("cargarItemRF");
    var rows2 = a2.rows.length;

    if ((rows+rows2) == 2 && comprobanteCodigo.options[comprobante.selectedIndex].value != '04'){
        alert('Registre datos de retencion antes de guardar');
        return false
    }
    if((rows+rows2) > 2 && comprobanteCodigo.options[comprobante.selectedIndex].value == '04'){
        alert('Elimine los datos de retencion antes de guardar');
        return false
    }
    if(document.getElementById("idTotal").value == 0){
        bootbox.alert({
            message: "El total de la compra tiene que set mayor a 0.00",
            size: 'small'
        });
        return false
    }
   
    if (Number(document.getElementById("total").innerHTML) != Number(document.getElementById("totalxml").innerHTML)) {
        let text = "EL TOTAL DEL XML ES DIFERENTE A LA FACTURA INGRESADA DESEA GUARDAR LA FACTURA";
        if (confirm(text) == true) {
            return true;
        } else {
            return false;
        }

    }
    return true;
}
function fechaRet(){
    document.getElementById("retencion_fecha").value = document.getElementById("transaccion_fecha").value;
    document.getElementById("transaccion_inventario").value = document.getElementById("transaccion_fecha").value;
    document.getElementById("transaccion_vencimiento").value = document.getElementById("transaccion_fecha").value;
    document.getElementById("transaccion_impresion").value = document.getElementById("transaccion_fecha").value;
    document.getElementById("transaccion_caducidad").value = document.getElementById("transaccion_fecha").value;
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
function OtraFactura(){
    if(document.getElementById("manualID").checked){
        document.getElementById("idSeleccionarFactura").classList.add('invisible');
        document.getElementById("idOtraFactura").classList.remove('invisible');
    }else{
        document.getElementById("idOtraFactura").classList.add('invisible');
        document.getElementById("idSeleccionarFactura").classList.remove('invisible');
    }
}
</script>
    @section('scriptCode')
        if(!!document.getElementById("proveedorID").value){
            $.ajax({
                url: '{{ url("facturasCompraactiva/searchN") }}' + '/' + document.getElementById("proveedorID").value,
                dataType: "json",
                type: "GET",
                async: false,
                data: {
                    buscar: document.getElementById("proveedorID").value
                },
                success: function(data){
                    document.getElementById("factura_id").innerHTML = "<option value='0' label>--Seleccione una opcion--</option>";
                    for (var i=0; i<data.length; i++) {
                        document.getElementById("factura_id").innerHTML += "<option value='"+data[i].transaccion_id+"'>"+data[i].transaccion_numero+"</option>";
                    }           
                },
            });
        }
    @endsection
@endsection