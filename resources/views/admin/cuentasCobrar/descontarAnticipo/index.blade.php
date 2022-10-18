@extends ('admin.layouts.admin')
@section('principal')
<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="card card-primary card-outline">
    <form class="form-horizontal" method="POST"  action="{{ url("descontarAntCli") }} " onsubmit="return validacion()">
        @csrf
        <div class="card-header">
            <div class="row">
                <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5">
                    <h2 class="card-title"><b>Cruza Anticipo con Factura de Cliente</b></h2>
                </div>
                <div class="col-xs-7 col-sm-7 col-md-7 col-lg-7">
                    <div class="float-right">
                        <button type="button" id="nuevoID" onclick="nuevo()" class="btn btn-primary btn-sm"><i
                                class="fas fa-receipt"></i><span> Nuevo</span></button>
                        <button id="guardarID" type="submit" class="btn btn-secondary btn-sm" disabled><i
                                class="fa fa-save"></i><span> Cruzar</span></button>
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
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6" style="margin-bottom : 0px;">
                                    <div class="form-group">
                                        <input id="IdCliente" name="IdCliente" type="hidden" class="form-control">    
                                        <input id="nombreCliente" name="nombreCliente" type="text" class="form-control "
                                            placeholder="Cliente" required readonly>
                                    </div>
                                </div>
                                <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 form-control-label  "
                                    style="margin-bottom : 0px;">
                                    <label>RUC/CI :</label>
                                </div>
                                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3" style="margin-bottom : 0px;">
                                    <div class="form-group">
                                        <div class="form-line">
                                            <input type="text" id="idCedula" name="idCedula" class="form-control "
                                                placeholder="9999999999999" readonly required>
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
                                                readonly />
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
                                    <label>No Factura :</label>
                                </div>
                                <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8" style="margin-bottom : 0px;">
                                    <div class="form-group">
                                        <input id="factura_id" name="factura_id" type="hidden">
                                        <input id="tipoDocumento" name="tipoDocumento" type="hidden">
                                        <input type="text" id="buscarFactura" name="buscarFactura" class="form-control"
                                            disabled />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row" id="tablaDetalleId">
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" style="margin-bottom: 0px;">
                            <div class="table-responsive">
                                @include ('admin.cuentasCobrar.descontarAnticipo.item')
                                <table id="cargarItemFactura"
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
                            <div class="card card-primary card-outline">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-lg-7 col-md-7 col-sm-7 col-xs-7">
                                            <div class="row">
                                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                    @include ('admin.cuentasCobrar.descontarAnticipo.itemAnticipo')
                                                    <table id="cargarItemAnticipo" class="table table-bordered">
                                                        <thead>
                                                            <tr>
                                                                <th></th>
                                                                <th>Fecha</th>
                                                                <th>Monto</th>
                                                                <th>Saldo</th>
                                                                <th>Descontar</th>
                                                                <th>Diario</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-5 col-md-5 col-sm-5 col-xs-5">
                                            <div class="row">
                                                <div class="col-lg-6 col-md-6 col-sm-6col-xs-6">
                                                    <label>Fecha Cruce :</label>
                                                </div>
                                                <div class="col-lg-6 col-md-6 col-sm-6col-xs-6">
                                                    <div class="form-group"><input type="date" name="fechaCruce" class="form-control" value='<?php echo(date("Y")."-".date("m")."-".date("d")); ?>'/></div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-lg-6 col-md-6 col-sm-6col-xs-6">
                                                    <label>Total Anticipos :</label>
                                                </div>
                                                <div class="col-lg-6 col-md-6 col-sm-6col-xs-6">
                                                    <div class="form-group"><input type="text" class="form-control derecha-texto" id="totAnt" name="totAnt" value="0.00" readonly/></div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-lg-6 col-md-6 col-sm-6col-xs-6">
                                                    <label>Total Seleccionado :</label>
                                                </div>
                                                <div class="col-lg-6 col-md-6 col-sm-6col-xs-6">
                                                    <div class="form-group"><input type="text" class="form-control derecha-texto" id="idSeleccionado" name="idSeleccionado" value="0.00" readonly/></div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-lg-6 col-md-6 col-sm-6col-xs-6">
                                                    <label>Saldo Factura :</label>
                                                </div>
                                                <div class="col-lg-6 col-md-6 col-sm-6col-xs-6">
                                                    <div class="form-group"><input type="text" class="form-control derecha-texto" id="saldo_factura" name="saldo_factura" value="0.00" readonly/></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3" id="tablaResumenId">
                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12  form-control-label">
                                <table class="table table-totalVenta">
                                    <tr>
                                        <td class="letra-blanca fondo-azul-claro negrita" width="90">Sub-Total
                                        </td>
                                        <td id="subtotal" width="100" class="derecha-texto negrita">0.00</td>
                                    </tr>
                                    <tr>
                                        <td class="letra-blanca fondo-azul-claro negrita">Descuento</td>
                                        <td id="descuento" class="derecha-texto negrita">0.00</td>
                                    </tr>
                                    <tr>
                                        <td id="porcentajeIva" class="letra-blanca fondo-azul-claro negrita">Tarifa 12 %
                                        </td>
                                        <td id="tarifa12" class="derecha-texto negrita">0.00</td>
                                    </tr>
                                    <tr>
                                        <td class="letra-blanca fondo-azul-claro negrita">Tarifa 0%</td>
                                        <td id="tarifa0" class="derecha-texto negrita">0.00</td>
                                    </tr>
                                    <tr>
                                        <td id="iva12" class="letra-blanca fondo-azul-claro negrita">Iva 12 %</td>
                                        <td id="iva" class="derecha-texto negrita">0.00</td>
                                    </tr>
                                    <tr>
                                        <td class="letra-blanca fondo-azul-claro negrita">Total</td>
                                        <td id="total" class="derecha-texto negrita">0.00</td>
                                    </tr>
                                </table>
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
<script src="{{ asset('admin/js/ajax/autocompleteAnticipoCliente.js') }}"></script>
@endsection
<script type="text/javascript">
    var id_item = 1;
    var id_itemA = 1;
    var porcentajeIva = parseFloat(12) / 100;

    function nuevo() {
        $('#bodega_id').css('pointer-events', 'none');
        document.getElementById("cancelarID").disabled = false;
        document.getElementById("nuevoID").disabled = true;
        document.getElementById("buscarFactura").disabled = false;
    }
    function agregarItem(IDPorudcto, codigoProducto, nombreProducto, cantidad, pu, descuento, total, tieneIva) {
        if (document.getElementById("nuevoID").disabled) {
            var linea = $("#plantillaItemFactura").html();
            linea = linea.replace(/{ID}/g, id_item);
            linea = linea.replace(/{Dcantidad}/g, cantidad);
            linea = linea.replace(/{Dcodigo}/g, codigoProducto);
            linea = linea.replace(/{DprodcutoID}/g, IDPorudcto);
            linea = linea.replace(/{Dnombre}/g, nombreProducto);
            if (tieneIva) {
                linea = linea.replace(/{Diva}/g, "SI");
                linea = linea.replace(/{DViva}/g, Number((total-descuento)*porcentajeIva).toFixed(2));
                iva = "SI";
            } else {
                linea = linea.replace(/{Diva}/g, "NO");
                linea = linea.replace(/{DViva}/g, "0.00");
                iva = "NO";
            }
            linea = linea.replace(/{Dpu}/g, Number(pu).toFixed(2));
            linea = linea.replace(/{Ddescuento}/g, Number(descuento).toFixed(2));
            linea = linea.replace(/{Dtotal}/g, Number(total-descuento).toFixed(2));
            $("#cargarItemFactura tbody").append(linea);
            id_item = id_item + 1;
            cargarTotales(iva, total, descuento);
        }
    }

    function cargarTotales(iva, total, descuento) {
        var subtotal = Number(Number(document.getElementById("subtotal").innerHTML) + Number(total)).toFixed(2);
        document.getElementById("subtotal").innerHTML = subtotal;

        var tarifa12 = Number(Number(document.getElementById("tarifa12").innerHTML) + Number(total) - Number(descuento)).toFixed(2);
        var tarifa0 = Number(Number(document.getElementById("tarifa0").innerHTML) + Number(total) - Number(descuento)).toFixed(2);

        var descuento = Number(Number(document.getElementById("descuento").innerHTML) + Number(descuento)).toFixed(2);
        document.getElementById("descuento").innerHTML = descuento;

        if (iva == "SI") {
            document.getElementById("tarifa12").innerHTML = tarifa12;
        } else {
            document.getElementById("tarifa0").innerHTML = tarifa0;
        }
        calcularTotales();
    }

    function calcularTotales() {
        var iva = Number(Number(document.getElementById("tarifa12").innerHTML) * porcentajeIva).toFixed(2);
        document.getElementById("iva").innerHTML = iva;

        var total = Number(Number(document.getElementById("tarifa12").innerHTML) + Number(document.getElementById("tarifa0")
            .innerHTML) + Number(document.getElementById("iva").innerHTML)).toFixed(2);

        document.getElementById("total").innerHTML = total;
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

    function cargarDetalle(idFactura){
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: '{{ url("facturaVentaDetalle/searchN") }}',
            dataType: "json",
            type: "POST",
            data: {
                factura_id: idFactura
            },
            success: function(data){
                for (var i=0; i<data.length; i++) {
                    bandera=false;
                    if(data[i].detalle_iva > 0){
                        bandera=true;
                    }
                    agregarItem(data[i].producto_id,data[i].producto_codigo, data[i].producto_nombre, data[i].detalle_cantidad, data[i].detalle_precio_unitario, data[i].detalle_descuento, data[i].detalle_total, bandera);
                }
            },
        });
    }
    function cargarAnticipos(idCliente){
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: '{{ url("anticiposCliente/searchN") }}',
            dataType: "json",
            type: "POST",
            data: {
                cliente_id: idCliente
            },
            success: function(data){
                for (var i=0; i<data.length; i++) {
                    var linea = $("#plantillaItemAnticipo").html();
                    linea = linea.replace(/{ID}/g, id_itemA);
                    linea = linea.replace(/{AID}/g, data[i].anticipo_id);
                    linea = linea.replace(/{AFecha}/g,  formato(data[i].anticipo_fecha));
                    linea = linea.replace(/{AMonto}/g, Number(data[i].anticipo_valor).toFixed(2));
                    linea = linea.replace(/{ASaldo}/g, Number(data[i].anticipo_saldo).toFixed(2));
                    document.getElementById("totAnt").value = Number(Number(document.getElementById("totAnt").value) + Number(data[i].anticipo_saldo)).toFixed(2);
                    linea = linea.replace(/{ADescontar}/g, Number(data[i].anticipo_saldo).toFixed(2));
                    linea = linea.replace(/{ADiario}/g, data[i].diario_codigo);
                    $("#cargarItemAnticipo tbody").append(linea);
                    id_itemA = id_itemA + 1;
                }
            },
            error: function() {
                alert('There was some error performing the AJAX call!');
            },
        });
    }
    function formato(texto){
    return texto.replace(/^(\d{4})-(\d{2})-(\d{2})$/g,'$3/$2/$1');
    }
    function limpiarTabla() {
        for (var i = 1; i < id_item; i++) {
            $("#row_" + i).remove();
        }
        for (var i = 1; i < id_itemA; i++) {
            $("#rowA_" + i).remove();
        }
        id_item = 1;
        id_itemA = 1;

        document.getElementById("subtotal").innerHTML = "0.00";
        document.getElementById("descuento").innerHTML = "0.00";
        document.getElementById("tarifa0").innerHTML = "0.00";
        document.getElementById("tarifa12").innerHTML = "0.00";
        document.getElementById("iva").innerHTML = "0.00";
        document.getElementById("total").innerHTML = "0.00";
        document.getElementById("saldo_factura").value = Number(0.00).toFixed(2);
        document.getElementById("idSeleccionado").value = Number(0.00).toFixed(2);
        document.getElementById("totAnt").value = Number(0.00).toFixed(2);
    }
    function calcularSeleccion(codigo,id){
        if(document.getElementById("check"+codigo).checked){
            $("input[name='ADescontar[]']")[id].readOnly = false;
            $("input[name='ADescontar[]']")[id].value = Number(Number($("input[name='ADescontar[]']")[id].value) + Number($("input[name='ASaldo[]']")[id].value)).toFixed(2);
        }else{
            $("input[name='ADescontar[]']")[id].readOnly = true;
            $("input[name='ADescontar[]']")[id].value = Number(0.00).toFixed(2);
        }
        totalSeleccion(id);
    }
    function totalSeleccion(id){
        if(Number($("input[name='ADescontar[]']")[id].value) < 0){
            $("input[name='ADescontar[]']")[id].value = Number(Number($("input[name='ASaldo[]']")[id].value)).toFixed(2);
        }
        if(Number($("input[name='ADescontar[]']")[id].value) > Number($("input[name='ASaldo[]']")[id].value)){
            $("input[name='ADescontar[]']")[id].value = Number(Number($("input[name='ASaldo[]']")[id].value)).toFixed(2);
        }
        seleccionAux = 0;
        for (var i = 1; i < id_itemA; i++) {
            if(i != id){
                seleccionAux = Number(seleccionAux) + Number($("input[name='ADescontar[]']")[i].value);
            }
        }
        if(Number($("input[name='ADescontar[]']")[id].value) > Number(document.getElementById("saldo_factura").value) - Number(seleccionAux)){
            $("input[name='ADescontar[]']")[id].value = Number(Number(document.getElementById("saldo_factura").value) - Number(seleccionAux)).toFixed(2);
        }
        document.getElementById("idSeleccionado").value = 0.00;
        for (var i = 1; i < id_itemA; i++) {
            document.getElementById("idSeleccionado").value = Number(Number(idSeleccionado.value) + Number($("input[name='ADescontar[]']")[i].value)).toFixed(2);
        }
    }   
    function validacion(){
        if(document.getElementById("idSeleccionado").value <= 0){
            bootbox.alert({
                message: "Seleccione un anticipo.",
                size: 'small'
            });
            return false
        }
        if(document.getElementById("saldo_factura").value <= 0){
            bootbox.alert({
                message: "La factura no tiene saldo a cruzar",
                size: 'small'
            });
            return false
        }
        document.getElementById("guardarID").value = "Enviando...";
	    document.getElementById("guardarID").disabled = true;
        return true;
    }
</script>
@endsection