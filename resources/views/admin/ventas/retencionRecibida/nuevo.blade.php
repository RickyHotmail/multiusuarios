@extends ('admin.layouts.admin')
@section('principal')
<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="card card-primary card-outline">
    <form class="form-horizontal" method="POST" action="{{ url("retencionVenta") }}" onsubmit="return validarForm();">
        @csrf
        <div class="card-header">
            <div class="row">
                <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5">
                    <h2 class="card-title"><b>Registrar Retención Recibida</b></h2>
                </div>
                <div class="col-xs-7 col-sm-7 col-md-7 col-lg-7">
                    <div class="float-right">
                        <button type="button" id="nuevoID" onclick="nuevo()" class="btn btn-primary btn-sm"><i
                                class="fas fa-receipt"></i><span> Nuevo</span></button>
                        <button id="guardarID" name="guardarID" type="submit" class="btn btn-success btn-sm" disabled><i
                                class="fa fa-save"></i><span> Guardar</span></button>
                        <button id="eliminarID" name="eliminarID" type="submit" class="btn btn-danger btn-sm invisible"><i
                                class="fa fa-trash"></i><span> Eliminar</span></button>
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
                        <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8" style="padding-top: 10px;">
                            <div class="row clearfix form-horizontal">
                                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2 form-control-label ">
                                    <label>FACTURA No:</label>
                                </div>
                                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                                    <div class="form-group">
                                        <div class="form-line">
                                            <input id="rango_id" name="rango_id" value="0"
                                                type="hidden">
                                            <input type="text" id="factura_serie" name="factura_serie"
                                                class="form-control derecha-texto negrita " placeholder="001001"
                                                required readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                                    <div class="form-group">
                                        <div class="form-line">
                                            <input type="text" id="factura_numero" name="factura_numero"
                                                class="form-control  negrita " placeholder="000000001" required
                                                readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row clearfix form-horizontal">
                                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2 form-control-label  "
                                    style="margin-bottom : 0px;">
                                    <label>CLIENTE :</label>
                                </div>
                                <div class="col-lg-5 col-md-5 col-sm-5 col-xs-5" style="margin-bottom : 0px;">
                                    <div class="form-group">
                                        <input id="nombreCliente" name="nombreCliente" type="text" class="form-control "
                                            placeholder="Cliente" required disabled>
                                    </div>
                                </div>
                                <div class="col-sm-1"><center><a href="{{ url("cliente/create") }}" class="btn btn-info btn-sm" target="_blank"><i class="fa fa-user"></i>&nbsp;Nuevo</a></center></div>
                                <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 form-control-label  "
                                    style="margin-bottom : 0px;">
                                    <label>RUC/CI :</label>
                                </div>
                                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3" style="margin-bottom : 0px;">
                                    <div class="form-group">
                                        <div class="form-line">
                                            <input type="text" id="idCedula" name="idCedula" class="form-control "
                                                placeholder="9999999999999" disabled required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row clearfix form-horizontal">
                                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2 form-control-label  "
                                    style="margin-bottom : 0px;">
                                    <label>PAGO :</label>
                                </div>
                                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3" style="margin-bottom : 0px;">
                                    <div class="form-group">
                                        <select id="factura_tipo_pago" name="factura_tipo_pago"
                                            class="form-control show-tick " data-live-search="true" disabled>
                                            <option value="CONTADO">CONTADO</option>
                                            <option value="CREDITO">CREDITO</option>
                                            <option value="EN EFECTIVO">EN EFECTIVO</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 form-control-label  "
                                    style="margin-bottom : 0px;">
                                    <label>FECHA :</label>
                                </div>
                                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3" style="margin-bottom : 0px;">
                                    <div class="form-group">
                                        <div class="form-line">
                                            <input type="date" id="factura_fecha" name="factura_fecha"
                                                class="form-control " placeholder="Seleccione una fecha..."
                                                value='<?php echo(date("Y")."-".date("m")."-".date("d")); ?>' required
                                                disabled />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 form-control-label"
                                    style="margin-bottom : 0px;">
                                    <label>% IVA :</label>
                                </div>
                                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2" style="margin-bottom : 0px;">
                                    <div class="input-group mb-3">
                                        <input type="text" id="idTarifaIva" name="idTarifaIva" class="form-control"
                                            readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4"
                            style="border-radius: 5px; border: 1px solid #ccc9c9;padding-top: 10px;">
                            <div class="row clearfix form-horizontal">
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <div class="form-group">
                                        <center><label>CONSULTA</label></center>
                                    </div>
                                </div>
                            </div>
                            <div class="row clearfix form-horizontal">
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 form-control-label"
                                    style="margin-bottom : 0px;">
                                    <label>Bodega :</label>
                                </div>
                                <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8" style="margin-bottom : 0px;">
                                    <div class="form-group">
                                        <select id="bodega_id" name="bodega_id" class="form-control custom-select"
                                            data-live-search="true">
                                            @foreach($bodegas as $bodega)
                                            <option value="{{ $bodega->bodega_id }}">{{ $bodega->bodega_nombre }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row clearfix form-horizontal">
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 form-control-label"
                                    style="margin-bottom : 0px;">
                                    <label>Tipo Doc :</label>
                                </div>
                                <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8" style="margin-bottom : 0px;">
                                    <div class="form-group">
                                        <select id="tipo_doc" name="tipo_doc" class="form-control custom-select"
                                            data-live-search="true">                                           
                                            <option value="0">FACTURA</option>
                                            <option value="1">NOTA DE DEBITO</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row clearfix form-horizontal">
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 form-control-label"
                                    style="margin-bottom : 0px;">
                                    <label>No Documento :</label>
                                </div>
                                <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8" style="margin-bottom : 0px;">
                                    <div class="form-group">
                                        <input id="factura_id" name="factura_id" type="hidden">
                                        <input type="text" id="buscarFactura" name="buscarFactura" class="form-control"
                                            disabled />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" style="margin-bottom: 0px;">
                            <div class="table-responsive">
                                @include ('admin.ventas.retencionRecibida.itemFactura')
                                <table id="cargarItemnc"
                                    class="table table-striped table-hover boder-sar tabla-item-factura"
                                    style="margin-bottom: 6px;">
                                    <thead>
                                        <tr class="letra-blanca fondo-azul-claro">
                                            <th>Cantidad</th>
                                            <th>Codigo</th>
                                            <th>Producto</th>
                                            <th>Con Iva</th>
                                            <th>Iva</th>
                                            <th>P.U.</th>
                                            <th>Descuento</th>
                                            <th>Total</th>
                                            <th width="40"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-xs-9 col-sm-9 col-md-9 col-lg-9">
                            <div class="card card-body" style="border: 1px solid #b1afaf;">
                                <div class="row">
                                    <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2" style="margin-bottom: 0px;">
                                        <center><label>Fecha Retencion</label></center>
                                    </div>
                                    <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3" style="margin-bottom: 0px;">
                                        <div class="form-group">
                                            <input id="retencion_fecha" name="retencion_fecha" type="date"
                                                class="form-control"
                                                value="<?php echo(date("Y")."-".date("m")."-".date("d")); ?>">
                                        </div>
                                    </div>
                                    <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1" style="margin-bottom: 0px;">
                                        <center><label>Serie</label></center>
                                    </div>
                                    <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2" style="margin-bottom: 0px;">
                                        <div class="form-group">
                                            <input id="retencion_serie" name="retencion_serie" type="text"
                                                class="form-control" value="001001">
                                        </div>
                                    </div>
                                    <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1" style="margin-bottom: 0px;">
                                        <center><label>Numero</label></center>
                                    </div>
                                    <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3" style="margin-bottom: 0px;">
                                        <div class="form-group">
                                            <input id="retencion_secuencial" name="retencion_secuencial" type="text"
                                                class="form-control" value="000000001">
                                        </div>
                                    </div>
                                    <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3" style="margin-bottom: 0px;">
                                        <center>
                                            <div class="demo-checkbox">
                                                <input type="radio" value="ELECTRONICA" id="check1"
                                                    class="with-gap radio-col-deep-orange" name="tipoDoc" checked required />
                                                <label for="check1">Documento Electronico</label>
                                            </div>
                                        </center>
                                    </div>
                                    <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3" style="margin-bottom: 0px;">
                                        <center>
                                            <div class="demo-checkbox">
                                                <input type="radio" value="FISICA" id="check2" class="with-gap radio-col-deep-orange"
                                                    name="tipoDoc" required />
                                                <label for="check2">Documento Fisico</label>
                                            </div>
                                      </center>
                                    </div>
                                </div>
                            </div>
                            <div class="card card-primary card-outline card-tabs">
                                <div class="card-header p-0 pt-1 border-bottom-0">
                                    <ul class="nav nav-tabs" id="custom-tabs-one-tab" role="tablist"
                                        style="border-bottom: 1px solid #c3c4c5;">
                                        <li class="nav-item">
                                            <a class="nav-link active" id="custom-tabs-four-fuente-tab" data-toggle="pill"
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
                                    </ul>
                                </div>
                                <div class="card-body">
                                    <div class="tab-content" id="custom-tabs-four-tabContent">
                                        <div class="tab-pane fade show active" id="custom-tabs-four-fuente" role="tabpanel"
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
                                                            class="form-control" placeholder="0.00" value="0.00"  onkeyup="calcularRF();" onclick="calcularRF();">
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
                                                                class="form-control" placeholder="0.00" value="0.00" >
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
                                                @include ('admin.ventas.retencionRecibida.itemRetencionFuente')
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
                                                                class="form-control" placeholder="Total" value="0.00" onkeyup="calcularRI();" onclick="calcularRI();">
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
                                                                class="form-control" placeholder="Total" value="0.00" >
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
                                                @include ('admin.ventas.retencionRecibida.itemRetencionIva')
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
                            <br>
                            <div class="row" style="padding-left: 10px; padding-right: 10px;">
                                <label class="col-sm-6 col-form-label">Total Retenido : </label>
                                <div class="col-sm-6">
                                    <input id="idTotalRetenido" class="form-control derecha-texto negrita" name="idTotalRetenido" value="0.00" type="text" readonly/>
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
<script src="{{ asset('admin/js/ajax/autocompleteFacturaRetencion.js') }}"></script>
@endsection
<script type="text/javascript"> 

var id_item = 1;
var id_itemRF = 1;
var id_itemRI = 1;
document.getElementById("idTarifa0").value = 0;
document.getElementById("idTarifa12").value = 0;
var porcentajeIva = parseFloat(12) / 100;

function nuevo() {
    $('#bodega_id').css('pointer-events', 'none');
    document.getElementById("guardarID").disabled = false;
    document.getElementById("cancelarID").disabled = false;
    document.getElementById("nuevoID").disabled = true;
    document.getElementById("buscarFactura").disabled = false;
}

function recalcular(id, cantidad, iva, pu, descuento, total) {
    cargarTotales($("input[name='Diva[]']")[id].value, Number($("input[name='Dtotal[]']")[id].value) * (-1), Number($(
        "input[name='Ddescuento[]']")[id].value) * (-1));
    $("input[name='Dtotal[]']")[id].value = Number((Number($("input[name='Dcantidad[]']")[id].value) * Number($(
        "input[name='Dpu[]']")[id].value)) - Number($("input[name='Ddescuento[]']")[id].value)).toFixed(2);
    if ($("input[name='Diva[]']")[id].value == "SI") {
        $("input[name='DViva[]']")[id].value = Number(Number($("input[name='Dtotal[]']")[id].value) * porcentajeIva)
            .toFixed(2);
    } else {
        $("input[name='DViva[]']")[id].value = "0.00";
    }
    cargarTotales($("input[name='Diva[]']")[id].value, Number($("input[name='Dtotal[]']")[id].value), Number($(
        "input[name='Ddescuento[]']")[id].value));
}

function agregarItem(IDPorudcto, codigoProducto, nombreProducto, cantidad, pu, descuento, total, tieneIva,subtotal,descuento1,iva12,tarifa0,tarifa12,total1) {
    if (document.getElementById("nuevoID").disabled) {
        var linea = $("#plantillaItemFactura").html();
        linea = linea.replace(/{ID}/g, id_item);
        linea = linea.replace(/{Dcantidad}/g, cantidad);
        linea = linea.replace(/{Dcodigo}/g, codigoProducto);
        linea = linea.replace(/{DprodcutoID}/g, IDPorudcto);
        linea = linea.replace(/{Dnombre}/g, nombreProducto);
        if (tieneIva) {
            linea = linea.replace(/{Diva}/g, "SI");
            linea = linea.replace(/{DViva}/g, Number((total - descuento) * porcentajeIva).toFixed(2));
            iva = "SI";
        } else {
            linea = linea.replace(/{Diva}/g, "NO");
            linea = linea.replace(/{DViva}/g, "0.00");
            iva = "NO";
        }
        linea = linea.replace(/{Dpu}/g, Number(pu).toFixed(2));
        linea = linea.replace(/{Ddescuento}/g, Number(descuento).toFixed(2));
        linea = linea.replace(/{Dtotal}/g, Number(total - descuento).toFixed(2));
        $("#cargarItemnc tbody").append(linea);
        id_item = id_item + 1;

        document.getElementById("tarifa12").innerHTML = tarifa12;
        document.getElementById("idTarifa12").value = tarifa12;
        document.getElementById("tarifa0").innerHTML = tarifa0;
        document.getElementById("idTarifa0").value = tarifa0;
        document.getElementById("subtotal").innerHTML = subtotal;
        document.getElementById("idSubtotal").value = Number(subtotal);

        document.getElementById("descuento").innerHTML = descuento1;
        document.getElementById("idDescuento").value = descuento1;
        document.getElementById("total").innerHTML = total1;
        document.getElementById("idTotal").value = total1;

        document.getElementById("iva").innerHTML = iva12;
        document.getElementById("idIva").value = iva12;
        document.getElementById("baseIva").value = iva12;
        document.getElementById("baseFuente").value = Number(document.getElementById("idSubtotal").value) - Number(document.getElementById("idDescuento").value);
    }
}

function cargarTotales(iva, total, descuento) {
    total = Number(total);
    var subtotal = Number(Number(document.getElementById("subtotal").innerHTML) + total).toFixed(2);
    document.getElementById("subtotal").innerHTML = subtotal;
    document.getElementById("idSubtotal").value = subtotal;
    document.getElementById("baseFuente").value = subtotal - descuento;
    
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
    document.getElementById("baseIva").value = iva;

    var total = Number(Number(document.getElementById("tarifa12").innerHTML) + Number(document.getElementById("tarifa0")
        .innerHTML) + Number(document.getElementById("iva").innerHTML)).toFixed(2);

    document.getElementById("total").innerHTML = total;
    document.getElementById("idTotal").value = total;
}

function eliminarItem(id, iva, total, descuento) {
    cargarTotales(iva, total * (-1), descuento * (-1));
    $("#row_" + id).remove();

}

function calcularTotal() {
    document.getElementById("buscarProducto").classList.remove('is-invalid');
    document.getElementById("errorStock").classList.add('invisible');
    if (parseFloat(document.getElementById("id_cantidad").value) > parseFloat(document.getElementById("id_disponible")
            .value)) {
        document.getElementById("id_cantidad").value = 1;
        document.getElementById("buscarProducto").classList.add('is-invalid');
        document.getElementById("errorStock").classList.remove('invisible');
    }
    document.getElementById("id_total").value = Number(document.getElementById("id_cantidad").value * document
        .getElementById("id_pu").value).toFixed(2);
}

function ponerCeros(num) {
    num = num + '';
    while (num.length <= 1) {
        num = '0' + num;
    }
    return num;
}

function cargarDetalle(idFactura) {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
        }
    });
    $.ajax({
        async: false,
        url: '{{ url("facturaVentaDetalleRet2/searchN") }}',
        dataType: "json",
        type: "POST",
        data: {
            factura_id: idFactura,
            tipoDocumento : $("#tipo_doc").val(),
        },
        success: function(data) {               
        if($(document.getElementById("tipo_doc").value) == '0'){
            resetearRetenciones();
            var fecha = new Date(); //Fecha actual
            var mes = fecha.getMonth()+1; //obteniendo mes
            var dia = fecha.getDate(); //obteniendo dia
            var ano = fecha.getFullYear(); //obteniendo año
            if(dia<10)
                dia='0'+dia; //agrega cero si el menor de 10
            if(mes<10)
                mes='0'+mes //agrega cero si el menor de 10
            document.getElementById("retencion_fecha").value = ano+"-"+mes+"-"+dia;
            document.getElementById("retencion_serie").value = "001001";
            document.getElementById("retencion_secuencial").value = '000000001';
            for (var i = 0; i < data[0].length; i++) {
               
                bandera = false;
                if (data[0][i].detalle_iva > 0) {
                    bandera = true;
                }
                if(data[3][0]=='1'){
                    agregarItem(data[0][i].producto_id, data[0][i].producto_codigo, data[0][i].producto_nombre, data[0][i]
                    .detalle_cantidad, data[0][i].detalle_precio_unitario, data[0][i].detalle_descuento, 
                    Number(data[0][i].detalle_precio_unitario)*Number(data[0][i].detalle_cantidad), bandera,data[0][i].factura_subtotal,data[0][i].factura_descuento,data[0][i].factura_iva,data[0][i].factura_tarifa0,data[0][i].factura_tarifa12,data[0][i].factura_total);
                }
                if(data[3][0]=='2'){
                    agregarItem(data[0][i].producto_id, data[0][i].producto_codigo, data[0][i].producto_nombre, data[0][i]
                    .detalle_cantidad, data[0][i].detalle_precio_unitario, data[0][i].detalle_descuento, 
                    Number(data[0][i].detalle_precio_unitario)*Number(data[0][i].detalle_cantidad), bandera,data[0][i].nd_subtotal,data[0][i].nd_descuento,data[0][i].nd_iva,data[0][i].nd_tarifa0,data[0][i].nd_tarifa12,data[0][i].nd_total);
                }
            }
            document.getElementById("guardarID").classList.remove('invisible');
            document.getElementById("eliminarID").classList.add('invisible');
            for (var i = 0; i < data[2].length; i++) {
                document.getElementById("guardarID").classList.add('invisible');
                document.getElementById("eliminarID").classList.remove('invisible');
                document.getElementById("retencion_fecha").value = data[2][i].retencion_fecha;
                document.getElementById("retencion_serie").value = data[2][i].retencion_serie;
                document.getElementById("retencion_secuencial").value = data[2][i].retencion_secuencial;
            }
            for (var i = 0; i < data[1].length; i++) {
                document.getElementById("guardarID").classList.add('invisible');
                document.getElementById("eliminarID").classList.remove('invisible');
                document.getElementById("retencion_fecha").value = data[1][i].retencion_fecha;
                document.getElementById("retencion_serie").value = data[1][i].retencion_serie;
                document.getElementById("retencion_secuencial").value = data[1][i].retencion_secuencial;
                if(data[1][i].detalle_tipo == 'IVA'){
                    agregarItemRIAux(data[1][i].detalle_base, data[1][i].concepto_id, 
                    data[1][i].concepto_codigo+' - '+Number(data[1][i].detalle_porcentaje).toFixed(2)+'% - '+data[1][i].concepto_nombre,
                    data[1][i].detalle_porcentaje,data[1][i].detalle_valor);
                }else{
                    agregarItemRFAxu(data[1][i].detalle_base, data[1][i].concepto_id, 
                    data[1][i].concepto_codigo+' - '+Number(data[1][i].detalle_porcentaje).toFixed(2)+'% - '+data[1][i].concepto_nombre,
                    data[1][i].detalle_porcentaje,data[1][i].detalle_valor);
                }                
            }
            document.getElementById("retencion_fecha").value = document.getElementById("factura_fecha").value;
        }else{
            resetearRetenciones();
            var fecha = new Date(); //Fecha actual
            var mes = fecha.getMonth()+1; //obteniendo mes
            var dia = fecha.getDate(); //obteniendo dia
            var ano = fecha.getFullYear(); //obteniendo año
            if(dia<10)
                dia='0'+dia; //agrega cero si el menor de 10
            if(mes<10)
                mes='0'+mes //agrega cero si el menor de 10
            document.getElementById("retencion_fecha").value = ano+"-"+mes+"-"+dia;
            document.getElementById("retencion_serie").value = "001001";
            document.getElementById("retencion_secuencial").value = '000000001';
            for (var i = 0; i < data[0].length; i++) {
               
                bandera = false;
                if (data[0][i].detalle_iva > 0) {
                    bandera = true;
                }
                if(data[3][0]=='1'){
                    agregarItem(data[0][i].producto_id, data[0][i].producto_codigo, data[0][i].producto_nombre, data[0][i]
                    .detalle_cantidad, data[0][i].detalle_precio_unitario, data[0][i].detalle_descuento, 
                    Number(data[0][i].detalle_precio_unitario)*Number(data[0][i].detalle_cantidad), bandera,data[0][i].factura_subtotal,data[0][i].factura_descuento,data[0][i].factura_iva,data[0][i].factura_tarifa0,data[0][i].factura_tarifa12,data[0][i].factura_total);
                }
                if(data[3][0]=='2'){
                    agregarItem(data[0][i].producto_id, data[0][i].producto_codigo, data[0][i].producto_nombre, data[0][i]
                    .detalle_cantidad, data[0][i].detalle_precio_unitario, data[0][i].detalle_descuento, 
                    Number(data[0][i].detalle_precio_unitario)*Number(data[0][i].detalle_cantidad), bandera,data[0][i].nd_subtotal,data[0][i].nd_descuento,data[0][i].nd_iva,data[0][i].nd_tarifa0,data[0][i].nd_tarifa12,data[0][i].nd_total);
                }
            }
            document.getElementById("guardarID").classList.remove('invisible');
            document.getElementById("eliminarID").classList.add('invisible');
            for (var i = 0; i < data[2].length; i++) {
                document.getElementById("guardarID").classList.add('invisible');
                document.getElementById("eliminarID").classList.remove('invisible');
                document.getElementById("retencion_fecha").value = data[2][i].retencion_fecha;
                document.getElementById("retencion_serie").value = data[2][i].retencion_serie;
                document.getElementById("retencion_secuencial").value = data[2][i].retencion_secuencial;
            }
            for (var i = 0; i < data[1].length; i++) {
                document.getElementById("guardarID").classList.add('invisible');
                document.getElementById("eliminarID").classList.remove('invisible');
                document.getElementById("retencion_fecha").value = data[1][i].retencion_fecha;
                document.getElementById("retencion_serie").value = data[1][i].retencion_serie;
                document.getElementById("retencion_secuencial").value = data[1][i].retencion_secuencial;
                if(data[1][i].detalle_tipo == 'IVA'){
                    agregarItemRIAux(data[1][i].detalle_base, data[1][i].concepto_id, 
                    data[1][i].concepto_codigo+' - '+Number(data[1][i].detalle_porcentaje).toFixed(2)+'% - '+data[1][i].concepto_nombre,
                    data[1][i].detalle_porcentaje,data[1][i].detalle_valor);
                }else{
                    agregarItemRFAxu(data[1][i].detalle_base, data[1][i].concepto_id, 
                    data[1][i].concepto_codigo+' - '+Number(data[1][i].detalle_porcentaje).toFixed(2)+'% - '+data[1][i].concepto_nombre,
                    data[1][i].detalle_porcentaje,data[1][i].detalle_valor);
                }                
            }
            document.getElementById("retencion_fecha").value = document.getElementById("factura_fecha").value;        
        }
        },        
    });    
}

function limpiarTabla() {
    for (var i = 1; i < id_item; i++) {
        $("#row_" + i).remove();
    }
    id_item = 1;

    document.getElementById("subtotal").innerHTML = 0.00;
    document.getElementById("idSubtotal").value = 0.00;

    document.getElementById("descuento").innerHTML = 0.00;
    document.getElementById("idDescuento").value = 0.00;

    document.getElementById("tarifa0").innerHTML = "0.00";
    document.getElementById("idTarifa0").value = 0.00;

    document.getElementById("tarifa12").innerHTML = "0.00";
    document.getElementById("idTarifa12").value = 0.00;

    document.getElementById("iva").innerHTML = 0.00;
    document.getElementById("idIva").value = 0.00;

    document.getElementById("total").innerHTML = 0.00;
    document.getElementById("idTotal").value = 0.00;
}
function validarForm(){
    var a = document.getElementById("cargarItemRI");
    var rows = a.rows.length;
    var a2 = document.getElementById("cargarItemRF");
    var rows2 = a2.rows.length;

    if ((rows+rows2) == 2){
        alert('Registre datos de retencion antes de guardar');
        return false
    }
    if((rows+rows2) > 2){
        alert('Elimine los datos de retencion antes de guardar');
        return false
    }
    return true;
}
function resetearRetenciones(){
    for(i = 1; i < id_itemRF; i++){
        $("#rowRF_" + i).remove();
    }
    for(i = 1; i < id_itemRI; i++){
        $("#rowRI_" + i).remove();
    }
   
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
    if (document.getElementById("nuevoID").disabled) {
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
}
function agregarItemRIAux(baseRI, retID, codigoRI,porcentajeRI,valorRI) {
    var linea = $("#plantillaItemRI").html();
    linea = linea.replace(/{ID}/g, id_itemRI);
    linea = linea.replace(/{DbaseRI}/g, round(baseRI));
    linea = linea.replace(/{DcodigoRI}/g, codigoRI);
    linea = linea.replace(/{DRIID}/g, retID);
    linea = linea.replace(/{DporcentajeRI}/g, round(porcentajeRI));
    linea = linea.replace(/{DvalorRI}/g, round(valorRI));
    $("#cargarItemRI tbody").append(linea);
    id_itemRI = id_itemRI + 1;
    totalRI(valorRI);
    resetearCamposRI();
}
function totalRI(valorI) {
    document.getElementById("id_total_iva").value = round(Number(document.getElementById("id_total_iva").value) +
        Number(valorI));
        document.getElementById("idTotalRetenido").value = round(Number(document.getElementById("id_total_fuente").value) + Number(document.getElementById("id_total_iva").value)); 
}

function resetearCamposRI() {
    document.getElementById("baseIva").value = "0.00";
    document.getElementById("valorIva").value = "0.00";
}
function eliminarItemI(id, valorI) {
    $("#rowRI_" + id).remove();
    totalRI(valorI * (-1));
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
    if (document.getElementById("nuevoID").disabled) {
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
        totalRF(valorRF);
        resetearCamposRF();
    }
}
function agregarItemRFAxu(baseRF, retID, codigoRI,porcentajeRF,valorRF) {
    var linea = $("#plantillaItemRF").html();
    linea = linea.replace(/{ID}/g, id_itemRF);
    linea = linea.replace(/{DbaseRF}/g, round(baseRF));
    linea = linea.replace(/{DcodigoRF}/g, codigoRI);
    linea = linea.replace(/{DRFID}/g, retID);
    linea = linea.replace(/{DporcentajeRF}/g, round(porcentajeRF));
    linea = linea.replace(/{DvalorRF}/g, round(valorRF));
    $("#cargarItemRF tbody").append(linea);
    id_itemRF = id_itemRF + 1;
    totalRF(valorRF);
    resetearCamposRF();
}

function totalRF(valorF) {
    document.getElementById("id_total_fuente").value = round(Number(document.getElementById("id_total_fuente").value) +
        Number(valorF));
        document.getElementById("idTotalRetenido").value = round(Number(document.getElementById("id_total_fuente").value) + Number(document.getElementById("id_total_iva").value)); 
}

function resetearCamposRF() {
    document.getElementById("baseFuente").value = "0.00";
    document.getElementById("valorFuente").value = "0.00";
}

function eliminarItemF(id, valorF) {
    $("#rowRF_" + id).remove();
    totalRF(valorF * (-1));
}
function validarForm(){
    countFuente = 0;
    countIva = 0;
    for (var i = 1; i < id_itemRF; i++) {
        if($("input[name='DRFID[]']")[i]){
            countFuente ++;
        }
    }
    for (var i = 1; i < id_itemRI; i++) {
        if($("input[name='DRIID[]']")[i]){
            countIva ++;
        }
    }
    if(countFuente == 0 && countIva == 0){
        bootbox.alert({
            message: "No ha registrado ningun concepto de retencion.",
            size: 'small'
        });
        return false;
    }
    return true;
}
</script>
@endsection