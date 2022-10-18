@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-primary card-outline">
    <form class="form-horizontal"  method="POST" action="{{ url("ingresoBodega") }}" onsubmit="return validarTotal()">
        @csrf
        <div class="card-header">
            <div class="row">
                <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5">
                    <h2 class="card-title"><b>Nueva ingreso de Bodega</b></h2>
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
                        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 form-control-label ">
                            <label>NUMERO</label>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                            <div class="form-group">
                                <div class="form-line">
                                    <input id="punto_id" name="punto_id"
                                        value="{{ $rangoDocumento->puntoEmision->punto_id }}" type="hidden">
                                    <input id="rango_id" name="rango_id" value="{{ $rangoDocumento->rango_id }}"
                                        type="hidden">
                                    <input type="text" id="ingreso_serie" name="ingreso_serie"
                                        value="{{ $rangoDocumento->puntoEmision->sucursal->sucursal_codigo }}{{ $rangoDocumento->puntoEmision->punto_serie }}"
                                        class="form-control derecha-texto negrita " placeholder="Serie" required readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                            <div class="form-group">
                                <div class="form-line">
                                    <input type="text" id="ingreso_numero" name="ingreso_numero"
                                        value="{{ $secuencial }}" class="form-control  negrita " placeholder="Numero"
                                        required readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 form-control-label  "
                            style="margin-bottom : 0px;">
                            <label>FECHA :</label>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2" style="margin-bottom : 0px;">
                            <div class="form-group">
                                <div class="form-line">
                                    <input type="date" id="ingreso_fecha" name="ingreso_fecha" class="form-control "
                                        placeholder="Seleccione una fecha..."
                                        value='<?php echo(date("Y")."-".date("m")."-".date("d")); ?>' required />
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 form-control-label  "
                            style="margin-bottom : 0px;">
                            <label>BODEGA :</label>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2 alinear-izquierda" style="margin-bottom : 0px;">
                            <div class="form-group">
                                <select id="bodega_id" name="bodega_id" class="form-control show-tick"
                                    data-live-search="true">
                                    @foreach($bodegas as $bodega)
                                    <option value="{{ $bodega->bodega_id }}">{{ $bodega->bodega_nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row clearfix form-horizontal">
                        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 form-control-label  "
                            style="margin-bottom : 0px;">
                            <label>PROVEEDOR</label>
                        </div>
                        <div class="col-lg-5 col-md-5 col-sm-5 col-xs-5" style="margin-bottom : 0px;">
                            <div class="form-group">
                                <input id="proveedorID" name="proveedorID" type="hidden">
                                <input id="buscarProveedor" name="buscarProveedor" type="text" class="form-control"
                                    placeholder="Proveedor" required disabled>
                            </div>
                        </div>
                        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 form-control-label  "
                            style="margin-bottom : 0px;">
                            <label>RUC/CI</label>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2" style="margin-bottom : 0px;">
                            <div class="form-group">
                                <input id="idRUC" name="idRUC" type="text" class="form-control"
                                    placeholder="9999999999999" required disabled>
                            </div>
                        </div>
                        <div class="col-sm-1 form-control-label"
                            style="margin-bottom : 0px;">
                            <label>MOVIMIENTO:</label>
                        </div>
                        <div class="col-sm-2" style="margin-bottom : 0px;">
                            <div class="form-group">
                                <select id="tipo" name="tipo" class="form-control show-tick"
                                    data-live-search="true" required>
                                    @foreach($tipo as $tipo)
                                    <option value="{{$tipo->tipo_id}}">{{$tipo->tipo_nombre}}
                                    </option>
                                    @endforeach
                                </select>
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
                        
                        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 form-control-label"
                            style="margin-bottom : 0px;">
                        <label>PAGO :</label>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2" style="margin-bottom : 0px;">
                            <div class="form-group">
                                <select id="ingreso_tipo_pago" name="ingreso_tipo_pago" class="form-control show-tick "
                                    data-live-search="true" onchange="cambioPago();" required>
                                    @if(Auth::user()->empresa->empresa_contabilidad == '1')
                                    <option value="CREDITO">CREDITO</option>
                                    @endif
                                    <option value="OTRO" selected>OTRO</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 form-control-label  "
                            style="margin-bottom : 0px;">
                            <label>PLAZO :</label>
                        </div>
                        <div class="col-sm-2" style="margin-bottom : 0px;">
                            <div class="form-group">
                                <div class="form-line">
                                    <input type="number" id="ingreso_dias_plazo" name="ingreso_dias_plazo" class="form-control " placeholder="0" value="0" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row clearfix form-horizontal">
                        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 form-control-label  "
                            style="margin-bottom : 0px;">
                            <label>MOTIVO :</label>
                        </div>
                        <div class="col-sm-11" style="margin-bottom : 0px;">
                            <div class="form-group">
                                <div class="form-line">
                                    <input type="text" id="ingresomotivo" name="ingresomotivo" class="form-control "
                                        placeholder="MOTIVO" value="Ingreso de Bodega" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row" style="background: #dadada; padding-top: 20px;margin-top: 5px;">
                        <div class="col-sm-6" style="margin-bottom: 0px;">
                            <label>Nombre de Producto</label>
                            <div class="form-group">
                                <div class="form-line">
                                    <input id="codigoProducto" name="idProducto" type="hidden">
                                    <input id="idProductoID" name="idProductoID" type="hidden">
                                    <input id="buscarProducto" name="buscarProducto" type="text" class="form-control"
                                        placeholder="Buscar producto" disabled>
                                    
                                </div>
                            </div>
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
                        <div class="col-sm-6" style="margin-bottom: 0px;">
                            <div class="form-group">
                                <input id="descripcionProducto" name="descripcionProducto" type="text"
                                    class="form-control" placeholder="Descripcion">
                            </div>
                        </div>
                        <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1" style="margin-bottom: 0px;">
                            <label>C. Consumo</label>
                        </div>
                        <div class="col-sm-4" style="margin-bottom: 0px;">
                            <div class="form-group">
                                <select class="form-control select2" id="idconsumo" name="idconsumo"
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
                                @include ('admin.inventario.ingresoBodega.itemingreso')
                                <table id="cargarItemingreso"
                                    class="table table-striped table-hover boder-sar tabla-item-orden"
                                    style="margin-bottom: 6px;">
                                    <thead>
                                        <tr class="letra-blanca fondo-azul-claro">
                                            <th>Cantidad</th>
                                            <th>Codigo</th>
                                            <th>Producto</th>
                                            <th>Descripcion</th>
                                            <th>Cuenta</th>
                                            <th>C. Consumo</th>
                                            <th>P.U.</th>
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
                            <div class="card card-primary card-outline card-tabs">
                                <div class="card-header p-0 pt-1 border-bottom-0">
                                    <ul class="nav nav-tabs" id="custom-tabs-one-tab" role="tablist"
                                        style="border-bottom: 1px solid #c3c4c5;">
                                        <li class="nav-item">
                                            <a class="nav-link active" id="custom-tabs-four-otros-tab"
                                                data-toggle="pill" href="#custom-tabs-four-otros" role="tab"
                                                aria-controls="custom-tabs-four-otros"
                                                aria-selected="false"><b>Otros</b></a>
                                        </li>
                                      
                                    </ul>
                                </div>
                                <div class="card-body">
                                    <div class="tab-content" id="custom-tabs-four-tabContent">
                                        <div class="tab-pane fade show active" id="custom-tabs-four-otros"
                                            role="tabpanel" aria-labelledby="custom-tabs-four-otros-tab">
                                            <div class="row clearfix form-horizontal">
                                                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2 form-control-label  "
                                                    style="margin-bottom : 0px;">
                                                    <label>Comentario:</label>
                                                </div>
                                                <div class="col-lg-10 col-md-10 col-sm-10 col-xs-10"
                                                    style="margin-bottom : 0px;">
                                                    <div class="form-group">
                                                        <div class="form-line">
                                                            <textarea id="ingreso_comentario" name="ingreso_comentario"
                                                                rows=3 class="form-control "
                                                                placeholder="Escribir aqui.."></textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                     
                                    </div>
                                </div>
                            </div>
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
                                    <td class="letra-blanca fondo-azul-claro negrita">Total</td>
                                    <td id="total" class="derecha-texto negrita">0.00</td>
                                    <input id="idTotal" name="idTotal" type="hidden" />
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
<script src="{{ asset('admin/js/ajax/autocompleteProveedorLC.js') }}"></script>
<script src="{{ asset('admin/js/ajax/autocompleteProductoingreso.js') }}"></script>
@endsection
<script type="text/javascript">
var id_item = 1;

function cambioPago(){
    if(document.getElementById("ingreso_tipo_pago").value == 'OTRO'){
        document.getElementById("ingreso_dias_plazo").value = '0'; 
        document.getElementById("ingreso_dias_plazo").readOnly = true;
    }else{
        document.getElementById("ingreso_dias_plazo").readOnly = false;
    }
}

function nuevo() {
   
    $('#bodega_id').css('pointer-events', 'none');
  
    document.getElementById("guardarID").disabled = false;
    document.getElementById("cancelarID").disabled = false;
    document.getElementById("nuevoID").disabled = true;
    document.getElementById("buscarProducto").disabled = false;
    
    document.getElementById("buscarProveedor").disabled = false;
}

function agregarItem() {
   
    if ( document.getElementById("id_total").value > 0) {
        var combo2 = document.getElementById("idconsumo");
        total = Number(document.getElementById("id_total").value);
        
        var linea = $("#plantillaItemingreso").html();
        linea = linea.replace(/{ID}/g, id_item);
        linea = linea.replace(/{Dcantidad}/g, document.getElementById("id_cantidad").value);
        linea = linea.replace(/{Dcodigo}/g, document.getElementById("codigoProducto").value);
        linea = linea.replace(/{DprodcutoID}/g, document.getElementById("idProductoID").value);
        linea = linea.replace(/{Dnombre}/g, document.getElementById("buscarProducto").value);
       
        linea = linea.replace(/{Dconsumo}/g, combo2.options[combo2.selectedIndex].text);
        
        linea = linea.replace(/{Didconsumo}/g, document.getElementById("idconsumo").value);
        linea = linea.replace(/{Ddescripcion}/g, document.getElementById("descripcionProducto").value);
        linea = linea.replace(/{Dpu}/g, document.getElementById("id_pu").value);
      
        linea = linea.replace(/{Dtotal}/g, Number(total).toFixed(2));
        $("#cargarItemingreso tbody").append(linea);
        id_item = id_item + 1;
        cargarTotales(total);
        resetearCampos();
    }
}

function cargarTotales(total) {
    var subtotal = Number(Number(document.getElementById("subtotal").innerHTML) + total).toFixed(2);
    
    document.getElementById("subtotal").innerHTML = subtotal;
    document.getElementById("idSubtotal").value = subtotal;
    document.getElementById("total").innerHTML = subtotal;
    document.getElementById("idTotal").value = subtotal;
}

function validarTotal(){
    if(parseFloat(document.getElementById("idTotal").value)<=0 || document.getElementById("idTotal").value==""){
        alert("No se puede guardar esta orden sin ningún item o en valor 0")
        return false;
    }

    console.log(document.getElementById("idTotal").value)

    return true;
}


    
function resetearCampos() {
    document.getElementById("id_cantidad").value = 1;
    document.getElementById("codigoProducto").value = "";
    document.getElementById("idProductoID").value = "";
    document.getElementById("descripcionProducto").value = "";
    document.getElementById("buscarProducto").value = "";
    document.getElementById("id_disponible").value = "0";
    document.getElementById("id_pu").value = "0.00";
   
    document.getElementById("id_total").value = "0.00";
}

function eliminarItem(id,  total) {
    cargarTotales( total * (-1));
    $("#row_" + id).remove();

}

function calcularTotal() {
    
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
</script>
@endsection