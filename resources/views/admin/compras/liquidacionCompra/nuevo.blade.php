@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-primary card-outline">
    <form class="form-horizontal" method="POST" action="{{ url("liquidacionCompra") }} " onsubmit="return validarForm();">
        @csrf
        <div class="card-header">
            <div class="row">
                <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5">
                    <h2 class="card-title"><b>Nueva Lquidación de Compra</b></h2>
                </div>
                <div class="col-xs-7 col-sm-7 col-md-7 col-lg-7">
                    <div class="float-right">
                        <button type="button" id="nuevoID" onclick="nuevo()" class="btn btn-primary btn-sm"><i
                                class="fas fa-receipt"></i><span> Nuevo</span></button>
                        <button id="xmlID" type="submit" class="btn btn-secondary btn-sm" disabled><i
                                class="fas fa-file-code"></i><span> XML</span></button>
                        <button id="guardarID" type="submit" class="btn btn-success btn-sm" disabled><i
                                class="fa fa-save"></i><span> Guardar</span></button>
                        <button type="button" id="cancelarID" name="cancelarID" onclick="javascript:location.reload()"
                            class="btn btn-danger btn-sm not-active-neo" disabled><i
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
                            <label>NUMERO</label>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                            <div class="form-group">
                                <div class="form-line">
                                    <input id="punto_id_lc" name="punto_id_lc"
                                        value="{{ $rangoDocumentoLC->puntoEmision->punto_id }}" type="hidden">
                                    <input id="rango_id_lc" name="rango_id_lc" value="{{ $rangoDocumentoLC->rango_id }}"
                                        type="hidden">
                                    <input type="text" id="lc_serie" name="lc_serie"
                                        value="{{ $rangoDocumento->puntoEmision->sucursal->sucursal_codigo }}{{ $rangoDocumentoLC->puntoEmision->punto_serie }}"
                                        class="form-control derecha-texto negrita " placeholder="Serie" required readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                            <div class="form-group">
                                <div class="form-line">
                                    <input type="text" id="lc_secuencial" name="lc_secuencial" value="{{ $secuencialLC }}"
                                        class="form-control  negrita " placeholder="Numero" required readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2"></div>
                        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 form-control-label derecha-texto">
                            <label>TOTAL</label>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                            <div class="form-group">
                                <div class="form-line">
                                    <input type="text" id="idTotalFactura" name="idTotalFactura"
                                        class="form-control campo-total-global derecha-texto" placeholder="Total"
                                        disabled style="background-color: black" value="0.00">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row clearfix form-horizontal">
                        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 form-control-label  "
                            style="margin-bottom : 0px;">
                            <label>PROVEEDOR</label>
                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4" style="margin-bottom : 0px;">
                            <div class="form-group">
                                <input id="proveedorID" name="proveedorID" type="hidden">
                                <input id="buscarProveedor" name="buscarProveedor" type="text" class="form-control"
                                    placeholder="Proveedor" required disabled>
                            </div>
                        </div>
                        <div class="col-sm-1"><center><a href="{{ url("proveedor/create") }}" class="btn btn-info btn-sm" target="_blank"><i class="fa fa-user"></i>&nbsp;Nuevo</a></center></div>
                        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 form-control-label  "
                            style="margin-bottom : 0px;">
                            <CENTER><label>RUC/CI</label></CENTER>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2" style="margin-bottom : 0px;">
                            <div class="form-group">
                                <input id="idRUC" name="idRUC" type="text" class="form-control"
                                    placeholder="9999999999999" required disabled>
                            </div>
                        </div>
                        <label>Fecha </label>
                        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                            <div class="form-group">
                                <div class="form-line">
                                    <input type="date" class="form-control" id="lc_fecha"
                                        name="lc_fecha"
                                        value='<?php echo(date("Y")."-".date("m")."-".date("d")); ?>' required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row clearfix form-horizontal">
                        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 form-control-label  "
                            style="margin-bottom : 0px;">
                            <label>DIRECCION</label>
                        </div>
                        <div class="col-lg-5 col-md-5 col-sm-5 col-xs-5" style="margin-bottom : 0px;">
                            <div class="form-group">
                                <div class="form-line">
                                    <input type="text" id="idDireccion" name="idDireccion" class="form-control "
                                        placeholder="Direccion" disabled required>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 form-control-label  "
                            style="margin-bottom : 0px;">
                            <CENTER><label>PAGO</label></CENTER>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2" style="margin-bottom : 0px;">
                            <div class="form-group">
                                <select id="lc_tipo_pago" name="lc_tipo_pago"
                                    class="form-control custom-select" data-live-search="true" onchange="cambioPago();">
                                    <option value="EN EFECTIVO">EN EFECTIVO</option>
                                    <option value="CONTADO">CONTADO</option>
                                    <option value="CREDITO">CREDITO</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3" style="margin-bottom : 0px;">
                            <div class="demo-checkbox">
                                <input type="radio" value="ELECTRONICA" id="check1"
                                    class="with-gap radio-col-deep-orange" name="tipoDoc_lc" checked required />
                                <label for="check1">Documento Electronico</label>
                            </div>
                        </div>
                    </div>
                    <div class="row clearfix form-horizontal">
                        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 form-control-label  "
                            style="margin-bottom : 0px;">
                            <label>FORMA PAGO</label>
                        </div>
                        <div class="col-lg-5 col-md-5 col-sm-5 col-xs-5" style="margin-bottom : 0px;">
                            <div class="form-group">
                                <select class="form-control select2" id="forma_pago_id" name="forma_pago_id"
                                    data-live-search="true">
                                    @foreach($formasPago as $formaPago)
                                    <option value="{{ $formaPago->forma_pago_id }}" @if($formaPago->forma_pago_nombre ==
                                        'OTROS CON UTILIZACION DEL SISTEMA FINANCIERO') selected
                                        @endif>{{ $formaPago->forma_pago_nombre }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 form-control-label  "
                            style="margin-bottom : 0px;">
                            <CENTER><label>% IVA </label></CENTER>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2" style="margin-bottom : 0px;">
                            <div class="form-group">
                                <select class="form-control custom-select" id="lc_porcentaje_iva"
                                    name="lc_porcentaje_iva" data-live-search="true"
                                    onclick="seleccionarIva()">
                                    @foreach($tarifasIva as $iva)
                                    <option value="{{$iva->tarifa_iva_porcentaje}}">{{$iva->tarifa_iva_porcentaje}}%
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>                        
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3" style="margin-bottom : 0px;">
                            <div class="demo-checkbox">
                                <input type="radio" value="FISICA" id="check2" class="with-gap radio-col-deep-orange"
                                    name="tipoDoc_lc" required />
                                <label for="check2">Documento Fisico</label>
                            </div>
                        </div>
                    </div>
                    <div class="row clearfix form-horizontal">
                        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 form-control-label  "
                            style="margin-bottom : 0px;">
                            <label>SUSTENTO</label>
                        </div>
                        <div class="col-lg-5 col-md-5 col-sm-5 col-xs-5" style="margin-bottom : 0px;">
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
                        <div id="IdCajaL" class="col-lg-1 col-md-1 col-sm-1 col-xs-1 form-control-label ">
                            <CENTER><label>Caja </label></CENTER>
                        </div>
                        <div id="IdCajaI" class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                            <div class="form-group">
                                <div class="form-line">
                                        <select id="caja_id" name="caja_id" class="form-control show-tick" data-live-search="true" required>
                                            @if($cajaAbierta)
                                            <option value="{{ $cajaAbierta->caja->caja_id }}">{{ $cajaAbierta->caja->caja_nombre }}</option>
                                            @endif
                                        </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 form-control-label  "
                            style="margin-bottom : 0px;">
                            <CENTER><label>Dias de Plazo</label></CENTER>
                        </div>
                        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1" style="margin-bottom : 0px;">
                            <div class="form-group">
                                <input type="number" id="lc_dias_plazo" name="lc_dias_plazo"
                                    class="form-control " value="0" required>
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
                                    <input id="buscarProducto" name="buscarProducto" type="text" class="form-control" placeholder="Buscar producto" disabled>
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
                                    data-live-search="true">
                                    @foreach($centros as $centro)
                                    <option value="{{$centro->centro_consumo_id}}">{{$centro->centro_consumo_nombre}}
                                    </option>
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
                                            <th width="100">P.U.</th>
                                            <th width="100">Descuento</th>
                                            <th width="100">Total</th>
                                            <th>Bodega</th>
                                            <th>C. Consumo</th>
                                            <th>Descripcion</th>
                                            <th>Bien/Serv.</th>
                                        </tr>
                                    </thead>
                                    <tbody>
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
                                                        <input type="radio" value="ELECTRONICA" id="check3"
                                                            class="with-gap radio-col-deep-orange" name="tipoDoc"
                                                            checked required />
                                                        <label for="check3">Documento Electronico</label>
                                                    </div>
                                                </div>
                                                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2"
                                                    style="margin-bottom : 0px;">
                                                    <div class="demo-checkbox">
                                                        <input type="radio" value="FISICA" id="check4"
                                                            class="with-gap radio-col-deep-orange" name="tipoDoc"
                                                            required />
                                                        <label for="check4">Documento Fisico</label>
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
                                                                value="{{ $secuencial }}" readonly>
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
                                                            class="form-control" placeholder="0.00" value="0.00"  onkeyup="calcularRF();" onclick="calcularRF();"
                                                            disabled>
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
                                                                disabled readonly>
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
                                                                disabled>
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
                                                                disabled readonly>
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
                                        <input id="idTotal" name="idTotal" type="hidden" />
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
<script src="{{ asset('admin/js/ajax/autocompleteProveedorLC.js') }}"></script>
<script src="{{ asset('admin/js/ajax/autocompleteProductoCompra.js') }}"></script>
@endsection
<script type="text/javascript">
var id_item = 1;
var id_itemRF = 1;
var id_itemRI = 1;
document.getElementById("idTarifa0").value = 0;
document.getElementById("idTarifa12").value = 0;
var combo = document.getElementById("lc_porcentaje_iva");
var porcentajeIva = combo.options[combo.selectedIndex].text;
porcentajeIva = parseFloat(porcentajeIva) / 100;
function cambioPago(){
    if(document.getElementById("lc_tipo_pago").value == 'EN EFECTIVO'){
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
    $('#lc_porcentaje_iva').css('pointer-events', 'none');
    document.getElementById("guardarID").disabled = false;
    document.getElementById("cancelarID").disabled = false;
    document.getElementById("xmlID").disabled = false;
    document.getElementById("nuevoID").disabled = true;
    document.getElementById("buscarProducto").disabled = false;
    document.getElementById("buscarProveedor").disabled = false;
    document.getElementById("baseFuente").disabled = false;
    document.getElementById("valorFuente").disabled = false;
    document.getElementById("baseIva").disabled = false;
    document.getElementById("valorIva").disabled = false;
}

function agregarItem() {
    if (document.getElementById("nuevoID").disabled && document.getElementById("id_total").value > 0) {
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
            document.getElementById("IvaBienesID").value = Number(Number(document.getElementById("IvaBienesID").value) +
                Number(ivaAux)).toFixed(2);
        } else {
            document.getElementById("IvaServiciosID").value = Number(Number(document.getElementById("IvaServiciosID")
                .value) + Number(ivaAux)).toFixed(2);
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

    var total = Number(Number(document.getElementById("tarifa12").innerHTML) + Number(document.getElementById("tarifa0")
        .innerHTML) + Number(document.getElementById("iva").innerHTML)).toFixed(2);

    document.getElementById("total").innerHTML = total;
    document.getElementById("idTotal").value = total;
    document.getElementById("idTotalFactura").value = total;
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
    var combo = document.getElementById("lc_porcentaje_iva");
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

/************PROCESO DE RETENCION EN LA FUENTE******************/
function calcularRF() {
    codRF = document.getElementById("conceptoFuenteID");
    porcentajeRF = document.getElementById("conceptoFuenteIDAux");
    porcentajeRF = porcentajeRF.options[codRF.selectedIndex].text;
    document.getElementById("valorFuente").value = round((Number(document.getElementById("baseFuente").value) * Number(
        porcentajeRF)) / 100);
}

function round(num) {
        var m = Number((Math.abs(num) * 100).toPrecision(15));
         m =Math.round(m) / 100 * Math.sign(num);
         return (m).toFixed(2);
    }

function agregarItemRF() {
    if (document.getElementById("nuevoID").disabled && document.getElementById("baseFuente").value > 0) {
        baseRF = Number(document.getElementById("baseFuente").value);
        porcentajeRF = document.getElementById("conceptoFuenteIDAux");
        codRF = document.getElementById("conceptoFuenteID");
        valorRF = Number(document.getElementById("valorFuente").value);
        if(round(baseRF*(Number(porcentajeRF.options[codRF.selectedIndex].text)/100)) == round(valorRF)){
        var linea = $("#plantillaItemRF").html();
            linea = linea.replace(/{ID}/g, id_itemRF);
            linea = linea.replace(/{DbaseRF}/g, round(baseRF));
            linea = linea.replace(/{DcodigoRF}/g, codRF.options[codRF.selectedIndex].text);
            linea = linea.replace(/{DRFID}/g, codRF.value);
            linea = linea.replace(/{DporcentajeRF}/g, porcentajeRF.options[codRF.selectedIndex].text);
            linea = linea.replace(/{DvalorRF}/g, round(valorRF));
            $("#cargarItemRF tbody").append(linea);
            id_itemRF = id_itemRF + 1;
            totalRF(valorRF);
            resetearCamposRF();
        }else{
            bootbox.alert({
                message: "El valor retenido no es igual al calculado revise los valores y vuleva a intentar.",
                size: 'small'
            });
        }
    }else{
        bootbox.alert({
            message: "La base no puede ser 0.00",
            size: 'small'
        });
    }
}

function totalRF(valorF) {
    document.getElementById("id_total_fuente").value = round(Number(document.getElementById("id_total_fuente").value) +
        Number(valorF));
}

function resetearCamposRF() {
    document.getElementById("baseFuente").value = "0.00";
    document.getElementById("valorFuente").value = "0.00";
}

function eliminarItemF(id, valorF) {
    $("#rowRF_" + id).remove();
    totalRF(valorF * (-1));
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
    if (document.getElementById("nuevoID").disabled && document.getElementById("baseIva").value > 0) {
        baseRI = Number(document.getElementById("baseIva").value);
        porcentajeRI = document.getElementById("conceptoIvaIDAux");
        codRI = document.getElementById("conceptoIvaID");
        valorRI = Number(document.getElementById("valorIva").value);
        if(round(baseRI*(Number(porcentajeRI.options[codRI.selectedIndex].text)/100)) == round(valorRI)){
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
                message: "El valor retenido no es igual al calculado revise los valores y vuleva a intentar.",
                size: 'small'
            });
        }
    }else{
        bootbox.alert({
            message: "La base no puede ser 0.00",
            size: 'small'
        });
    }
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

function validarForm() {
    if(Number(document.getElementById("idTotal").value) <= 0){
        alert('Error, el total no puede ser 0.00');
        return false
    }
    var a = document.getElementById("cargarItemRI");
    var rows = a.rows.length;
    var a2 = document.getElementById("cargarItemRF");
    var rows2 = a2.rows.length;

    if ((rows+rows2) == 2) {
        alert('Registre datos de retencion antes de guardar');
        return false
    }
    return true;
}
</script>
@endsection