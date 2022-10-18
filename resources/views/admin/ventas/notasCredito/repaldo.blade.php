@extends ('admin.layouts.admin')
@section('principal')
<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="card card-primary card-outline">
    <form class="form-horizontal" method="POST" action="{{ url("notaCredito") }}">
        @csrf
        <div class="card-header">
            <div class="row">
                <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5">
                    <h2 class="card-title"><b>Nueva Nota de Crédito</b></h2>
                </div>
                <div class="col-xs-7 col-sm-7 col-md-7 col-lg-7">
                    <div class="float-right">
                        <button type="button" id="nuevoID" onclick="nuevo()" class="btn btn-primary btn-sm"><i
                                class="fas fa-receipt"></i><span> Nuevo</span></button>
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
                        <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8" style="padding-top: 10px;">
                            <div class="row clearfix form-horizontal">
                                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2 form-control-label ">
                                    <label>NUMERO</label>
                                </div>
                                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                                    <div class="form-group">
                                        <div class="form-line">
                                            <input id="punto_id" name="punto_id"
                                                value="{{ $rangoDocumento->puntoEmision->punto_id }}" type="hidden">
                                            <input id="rango_id" name="rango_id" value="{{ $rangoDocumento->rango_id }}"
                                                type="hidden">
                                            <input type="text" id="nc_serie" name="nc_serie"
                                                value="{{ $rangoDocumento->puntoEmision->sucursal->sucursal_codigo }}{{ $rangoDocumento->puntoEmision->punto_serie }}"
                                                class="form-control derecha-texto negrita " placeholder="Serie"
                                                required readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-7 col-md-7 col-sm-7 col-xs-7">
                                    <div class="form-group">
                                        <div class="form-line">
                                            <input type="text" id="nc_numero" name="nc_numero" value="{{ $secuencial }}"
                                                class="form-control  negrita " placeholder="Numero" required readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row clearfix form-horizontal">
                                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2 form-control-label  "
                                    style="margin-bottom : 0px;">
                                    <label>CLIENTE :</label>
                                </div>
                                <div class="col-lg-9 col-md-9 col-sm-9 col-xs-9" style="margin-bottom : 0px;">
                                    <div class="form-group">
                                        <input id="clienteID" name="clienteID" type="hidden" value="">
                                        <input id="buscarCliente" name="buscarCliente" type="text" class="form-control "
                                            placeholder="Cliente" required disabled>
                                            
                                    </div>
                                </div>
                                <div class="col-sm-1"><center><a href="{{ url("cliente/create") }}" class="btn btn-info btn-sm" target="_blank"><i class="fa fa-user"></i>&nbsp;Nuevo</a></center></div>
                           
                            </div>
                            <div class="row clearfix form-horizontal">
                                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2 form-control-label  "
                                    style="margin-bottom : 0px;">
                                    <label>RUC/CI :</label>
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4" style="margin-bottom : 0px;">
                                    <div class="form-group">
                                        <div class="form-line">
                                            <input type="text" id="idCedula" name="idCedula" class="form-control "
                                                placeholder="Ruc" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 form-control-label  "
                                    style="margin-bottom : 0px;">
                                    <label>TIPO :</label>
                                </div>
                                <div class="col-lg-5 col-md-5 col-sm-5 col-xs-5" style="margin-bottom : 0px;">
                                    <div class="form-group">
                                        <div class="form-line">
                                            <input type="text" id="idTipoCliente" name="idTipoCliente"
                                                class="form-control " placeholder="Tipo" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row clearfix form-horizontal">
                                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2 form-control-label  "
                                    style="margin-bottom : 0px;">
                                    <label>DIRECCION :</label>
                                </div>
                                <div class="col-lg-10 col-md-10 col-sm-10 col-xs-10" style="margin-bottom : 0px;">
                                    <div class="form-group">
                                        <div class="form-line">
                                            <input type="text" id="idDireccion" name="idDireccion" class="form-control "
                                                placeholder="Direccion" required>
                                            <input type="hidden" id="idTelefono" name="idTelefono" class="form-control "
                                        placeholder="Telefono" disabled>
                                        <input type="hidden" id="saldoPendienteID" name="saldoPendienteID" class="form-control "
                                        placeholder="Telefono" disabled>
                                        <input type="hidden" id="idMontoCredito" name="idMontoCredito" class="form-control "
                                        placeholder="Telefono" disabled>
                                        <input type="hidden" id="idTieneCredito" name="idTieneCredito" class="form-control "
                                        placeholder="Telefono" disabled>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row clearfix form-horizontal">
                                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2 form-control-label  "
                                    style="margin-bottom : 0px;">
                                    <label>VENDEDOR :</label>
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4" style="margin-bottom : 0px;">
                                    <div class="form-group">
                                    
                                        <select class="form-control" id="idVendedor" name="idVendedor"
                                        data-live-search="true">
                                        @foreach($vendedores as $vendedor)
                                        <option value="{{ $vendedor->vendedor_id }}">{{ $vendedor->vendedor_nombre }}
                                        </option>
                                        @endforeach
                                    </select>
                                    </div>
                                </div>
                                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2 form-control-label"
                                    style="margin-bottom : 0px;">
                                    <label>MOTIVO :</label>
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4" style="margin-bottom : 0px;">
                                    <div class="form-group">
                                        <select id="nc_comentario" name="nc_comentario"
                                            class="form-control custom-select" data-live-search="true">
                                            <option value="DEVOLUCION">DEVOLUCION</option>
                                            <option value="DESCUENTO">DESCUENTO</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                            <div class="row clearfix form-horizontal">
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 form-control-label">
                                    <label>TOTAL</label>
                                </div>
                                <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
                                    <div class="form-group">
                                        <div class="form-line">
                                            <input type="text" id="idTotalnc" name="idTotalnc"
                                                class="form-control campo-total-global derecha-texto"
                                                placeholder="Total" readonly style="background-color: black"
                                                value="0.00">
                                        </div>
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
                                    <label>FECHA :</label>
                                </div>
                                <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8" style="margin-bottom : 0px;">
                                        <div class="form-line">
                                            <input type="date" id="nc_fecha" name="nc_fecha" class="form-control "
                                                placeholder="Seleccione una fecha..."
                                                value='<?php echo(date("Y")."-".date("m")."-".date("d")); ?>'
                                                required />
                                        </div>
                                    
                                </div>
                            </div>
                            <div class="row clearfix form-horizontal">
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="margin-bottom : 0px;">
                                        <div class="demo-checkbox">
                                            <input type="radio" value="ELECTRONICA" id="check1"
                                                class="with-gap radio-col-deep-orange" name="tipoDoc" checked required />
                                            <label for="check1">Documento Electronico</label>
                                        </div>
                                    </div>
                                    
                            </div>
                            <div class="row clearfix form-horizontal">
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="margin-bottom : 0px;">
                                    <div class="demo-checkbox">
                                        <input type="radio" value="FISICA" id="check2"
                                            class="with-gap radio-col-deep-orange" name="tipoDoc" required />
                                        <label for="check2">Documento Fisico</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row clearfix form-horizontal">
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 form-control-label"
                                    style="margin-bottom : 0px;">
                                    <label>Tarifa de IVA :</label>
                                </div>
                                <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8" style="margin-bottom : 0px;">
                                   
                                    <select class="form-control" id="idTarifaIva" name="idTarifaIva"
                                        data-live-search="true" onclick="seleccionarIva()">
                                        @foreach($tarifasIva as $iva)
                                            <option value="{{$iva->tarifa_iva_porcentaje}}">{{$iva->tarifa_iva_porcentaje}}%
                                            </option>
                                        @endforeach
                                    </select>
                                       
                                   
                                </div>
                            </div>
                        
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5" style="margin-bottom: 0px;">
                            <label>Nombre de Producto</label>
                            <div class="form-group">
                                <div class="form-line">
                                    <input id="codigoProducto" name="idProducto" type="hidden">
                                    <input id="idProductoID" name="idProductoID" type="hidden">
                                    <input id="idtipoProducto" name="idtipoProducto" type="hidden">
                                    <input id="idCV" name="idCV" type="hidden">
                                    <input id="buscarProducto" name="buscarProducto" type="text" class="form-control"
                                        placeholder="Buscar producto" disabled>
                                    <span id="errorStock" class="text-danger invisible">El producto no tiene stock
                                        disponible.</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1" style="margin-bottom: 0px;">
                            <center>
                                <label>Iva</label>
                                <div class="form-group" style="margin-bottom: 0px;">
                                    <div class="custom-control custom-checkbox">
                                        <input id="tieneIva" name="tieneIva" type="checkbox"
                                            class="custom-control-input" disabled />
                                        <label for="tieneIva" class="custom-control-label"></label>
                                    </div>
                                </div>
                            </center>
                        </div>
                        <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1" style="margin-bottom: 0px;">
                            <label>Disponible</label>
                            <div class="form-group">
                                <div class="form-line">
                                    <input id="id_disponible" name="id_disponible" type="number" class="form-control"
                                        placeholder="Disponible" value="0" disabled>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1" style="margin-bottom: 0px;">
                            <label>Cantidad</label>
                            <div class="form-group">
                                <div class="form-line">
                                    <input onchange="calcularTotal()" onkeyup="calcularTotal()" id="id_cantidad"
                                        name="id_cantidad" type="number" class="form-control" placeholder="Cantidad"
                                        value="1">
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1" style="margin-bottom: 0px;">
                            <label>Precio</label>
                            <div class="form-group">
                                <div class="form-line">
                                    <input onchange="calcularTotal()" onkeyup="calcularTotal()" id="id_pu" name="id_pu"
                                        type="text" class="form-control" placeholder="Precio" value="0.00" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1" style="margin-bottom: 0px;">
                            <label>Desc. %</label>
                            <div class="form-group">
                                <div class="form-line">
                                    <input id="id_descuento" name="id_descuento" type="text" class="form-control"
                                        placeholder="Descuento" value="0.00">
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1" style="margin-bottom: 0px;">
                            <label>Total</label>
                            <div class="form-group">
                                <div class="form-line">
                                    <input id="id_total" name="id_total" type="text" class="form-control"
                                        placeholder="Total" value="0.00" disabled>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
                            <a onclick="agregarItem();" class="btn btn-primary btn-venta"><i
                                    class="fas fa-plus"></i></a>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" style="margin-bottom: 0px;">
                            <div class="table-responsive">
                                @include ('admin.ventas.notasCredito.itemNCN')
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
                        </div>
                        <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
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
                                    <input id="porcentaje" name="porcentaje" type="hidden" />
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
<!-- /.card -->
@section('scriptAjax')
<script src="{{ asset('admin/js/ajax/autocompleteCliente.js') }}"></script>
<script src="{{ asset('admin/js/ajax/autocompleteProducto.js') }}"></script>

@endsection
<script type="text/javascript">
var id_item = 1;
document.getElementById("idTarifa0").value = 0;
document.getElementById("idTarifa12").value = 0;
var porcentajeIva =  0.12;

function nuevo() {
    $('#bodega_id').css('pointer-events', 'none');
    document.getElementById("guardarID").disabled = false;
    document.getElementById("cancelarID").disabled = false;
    document.getElementById("nuevoID").disabled = true;
    document.getElementById("buscarProducto").disabled = false;

    document.getElementById("buscarCliente").disabled = false;
}

function recalcular(id) {
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
    document.getElementById("idTotalnc").value = 0.00;

    $("input[name='Dtotal[]']")[id].value = Number((Number($("input[name='Dcantidad[]']")[id].value) * Number($(
        "input[name='Dpu[]']")[id].value)) ).toFixed(2);
   
    if ($("input[name='Diva[]']")[id].value == "SI") {
        $("input[name='DViva[]']")[id].value = Number(Number($("input[name='Dtotal[]']")[id].value) * porcentajeIva)
            .toFixed(2);
    } else {
        $("input[name='DViva[]']")[id].value = "0.00";
    }
    alert('1');
    $('#cargarItemnc tbody tr').each(function() {
        var total = Number($(this).find('td').eq(0).children().eq(0).val())*Number($(this).find('td').eq(5).children().eq(0).val());
        var iva = $(this).find('td').eq(3).text();
        var descuento = $(this).find('td').eq(6).children().eq(0).val();
       
        cargarTotales(iva, total, descuento);
    });

    alert('8');
   
    
}

function agregarItem() {
    if (document.getElementById("nuevoID").disabled && document.getElementById("id_total").value > 0) {
        total = Number(document.getElementById("id_total").value);
        descuento = Number(total * (document.getElementById("id_descuento").value / 100));
        var linea = $("#plantillaItemnc").html();
        linea = linea.replace(/{ID}/g, id_item);
        linea = linea.replace(/{Dcantidad}/g, document.getElementById("id_cantidad").value);
        linea = linea.replace(/{Dcodigo}/g, document.getElementById("codigoProducto").value);
        linea = linea.replace(/{DprodcutoID}/g, document.getElementById("idProductoID").value);
        linea = linea.replace(/{Dnombre}/g, document.getElementById("buscarProducto").value);
        linea = linea.replace(/{Dtotal2}/g, Number(total).toFixed(2));
        if (document.getElementById("tieneIva").checked) {
            linea = linea.replace(/{Diva}/g, "SI");
            linea = linea.replace(/{DViva}/g, Number((total - descuento) * porcentajeIva).toFixed(2));
            iva = "SI";
        } else {
            linea = linea.replace(/{Diva}/g, "NO");
            linea = linea.replace(/{DViva}/g, "0.00");
            iva = "NO";
        }
        linea = linea.replace(/{Dpu}/g, document.getElementById("id_pu").value);
        linea = linea.replace(/{Ddescuento}/g, Number(descuento).toFixed(2));
        linea = linea.replace(/{Dtotal}/g, Number(total - descuento).toFixed(2));
        $("#cargarItemnc tbody").append(linea);
        id_item = id_item + 1;
        cargarTotales(iva, total, descuento);
        resetearCampos();
    }
}

function cargarTotales(iva, total, descuento) {
   
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
    document.getElementById("idTotalnc").value = total;
}

function eliminarItem(id, iva, total, descuento) {
    cargarTotales(iva, total * (-1), descuento * (-1));
    $("#row_" + id).remove();

}

function calcularTotal() {
    document.getElementById("buscarProducto").classList.remove('is-invalid');
    document.getElementById("errorStock").classList.add('invisible');
    if(document.getElementById("idtipoProducto").value  == '1' && document.getElementById("idCV").value  == '3'){
        if (parseFloat(document.getElementById("id_cantidad").value) > parseFloat(document.getElementById("id_disponible")
            .value)) {
            document.getElementById("id_cantidad").value = 1;
            document.getElementById("buscarProducto").classList.add('is-invalid');
            document.getElementById("errorStock").classList.remove('invisible');
        }
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

function seleccionarIva() {
    var combo = document.getElementById("factura_porcentaje_iva");
    porcentajeIva = combo.options[combo.selectedIndex].text;
    porcentajeIva = parseFloat(porcentajeIva) / 100;
    document.getElementById("porcentajeIva").innerHTML = "Tarifa " + combo.options[combo.selectedIndex].text;
    document.getElementById("iva12").innerHTML = "Iva " + combo.options[combo.selectedIndex].text;
}

function cargarDetalle(idFactura) {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
        }
    });
    $.ajax({
        url: '{{ url("facturaVentaDetalle2/searchN") }}',
        dataType: "json",
        type: "POST",
        data: {
            factura_id: idFactura
        },
        success: function(data) {
            for (var i = 0; i < data.length; i++) {
                bandera = false;
                if (data[i].detalle_iva > 0) {
                    bandera = true;
                }
                agregarItem(data[i].producto_id, data[i].producto_codigo, data[i].producto_nombre, data[i]
                    .detalle_cantidad, data[i].detalle_precio_unitario, data[i].detalle_descuento, data[
                        i].detalle_total, bandera,data[i].factura_subtotal,data[i].factura_descuento,data[i].factura_iva,data[i].factura_tarifa0,data[i].factura_tarifa12,data[i].factura_total);
                document.getElementById("porcentaje").value=data[i].factura_porcentaje_iva;
                porcentajeIva=(data[i].factura_porcentaje_iva/100);
            }
        },
    });
}
function round(num) {
    var m = Number((Math.abs(num) * 100).toPrecision(15));
        m =Math.round(m) / 100 * Math.sign(num);
        return (m).toFixed(2);
}

function eliminarTodo() {
    for(i = 1; i < id_item; i++){
        $("#row_" + i).remove();
    }
    document.getElementById("idSubtotal").value = "0.00";
    document.getElementById("idDescuento").value = "0.00";
    document.getElementById("idTarifa0").value = "0.00";
    document.getElementById("idTarifa12").value = "0.00";
    document.getElementById("idIva").value = "0.00";
    document.getElementById("idTotal").value = "0.00";

    document.getElementById("subtotal").innerHTML = "0.00";
    document.getElementById("descuento").innerHTML = "0.00";
    document.getElementById("tarifa0").innerHTML = "0.00";
    document.getElementById("tarifa12").innerHTML = "0.00";
    document.getElementById("iva").innerHTML = "0.00";
    document.getElementById("total").innerHTML = "0.00";
    resetearCampos();
    document.getElementById("idTotalnc").value = "0.00";
}
function resetearCampos() {
    document.getElementById("id_cantidad").value = 1;
    document.getElementById("codigoProducto").value = "";
    document.getElementById("idProductoID").value = "";
    document.getElementById("buscarProducto").value = "";
    document.getElementById("id_disponible").value = "0";
    document.getElementById("id_pu").value = "0.00";
    document.getElementById("id_descuento").value = "0.00";
    document.getElementById("id_total").value = "0.00";
}
</script>
@endsection