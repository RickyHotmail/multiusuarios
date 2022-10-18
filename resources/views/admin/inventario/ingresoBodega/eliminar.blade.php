@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-primary card-outline">
    <form class="form-horizontal"  method="POST" action="{{ route('ingresoBodega.destroy', [$ingreso->cabecera_ingreso_id]) }}">
        @method('DELETE')
        @csrf
        <div class="card-header">
            <div class="row">
                <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5">
                    <h2 class="card-title"><b>Ingreso de Bodega</b></h2>
                </div>
                <div class="col-xs-7 col-sm-7 col-md-7 col-lg-7">
                    <div class="float-right">
                      
                    <button id="guardarID" type="submit" class="btn btn-danger btn-sm" ><i
                                class="fa fa-save"></i><span> Eliminar</span></button>
                        <button type="button" id="cancelarID" name="cancelarID" onclick='window.location = "{{ url("ingresoBodega") }}";'
                            class="btn btn-light btn-sm not-active-neo" ><i
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
                                    <input id="ingreso_id" name="ingreso_id" value="{{$ingreso->cabecera_ingreso_id}}" type="hidden">   
                                    <label class="form-control" id="ingreso_serie" name="ingreso_serie">{{$ingreso->cabecera_ingreso_serie}}</label>
                                   
                                    
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                            <div class="form-group">
                                <div class="form-line">
                                    <label class="form-control" id="ingreso_numero" name="ingreso_numero"  value="{{substr(str_repeat(0, 9). $ingreso->cabecera_ingreso_numero , - 9)}}">{{substr(str_repeat(0, 9). $ingreso->cabecera_ingreso_numero , - 9)}}</label>
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
                                    <label class="form-control" id="ingreso_fecha" name="ingreso_fecha"  value="{{$ingreso->cabecera_ingreso_fecha}}">{{$ingreso->cabecera_ingreso_fecha}}</label>
                                
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 form-control-label  "
                            style="margin-bottom : 0px;">
                            <label>BODEGA :</label>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2 alinear-izquierda" style="margin-bottom : 0px;">
                            <div class="form-group">
                                <label class="form-control"   value="{{$ingreso->bodega->bodega_nombre}}">{{$ingreso->bodega->bodega_nombre}}</label>  
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
                                <label class="form-control"  value="{{$ingreso->proveedor->proveedor_nombre}}">{{$ingreso->proveedor->proveedor_nombre}}</label>
                            </div>
                        </div>
                        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 form-control-label  "
                            style="margin-bottom : 0px;">
                            <label>RUC/CI</label>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2" style="margin-bottom : 0px;">
                            <div class="form-group">
                                <label class="form-control"  value="{{$ingreso->proveedor->proveedor_ruc}}">{{$ingreso->proveedor->proveedor_ruc}}</label>
                            </div>
                        </div>
                        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 form-control-label  "
                            style="margin-bottom : 0px;">
                            <label>MOVIMIENTO:</label>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2" style="margin-bottom : 0px;">
                            <div class="form-group">
                                <label class="form-control"   value="{{$ingreso->tipo->tipo_nombre}}">{{$ingreso->tipo->tipo_nombre}}</label>  
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
                                    <label class="form-control"  value="{{$ingreso->proveedor->proveedor_direccion}}">{{$ingreso->proveedor->proveedor_direccion}}</label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 form-control-label  "
                            style="margin-bottom : 0px;">
                        <label>PAGO :</label>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2" style="margin-bottom : 0px;">
                            <div class="form-group">
                                <label class="form-control"  value="{{$ingreso->cabecera_ingreso_pago}}">{{$ingreso->cabecera_ingreso_pago}}</label>
                            </div>
                        </div>
                    </div>
                    <div class="row clearfix form-horizontal">
                        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 form-control-label  "
                            style="margin-bottom : 0px;">
                            <label>MOTIVO :</label>
                        </div>
                        <div class="col-lg-5 col-md-5 col-sm-5 col-xs-5" style="margin-bottom : 0px;">
                            <div class="form-group">
                                <div class="form-line">
                                <label class="form-control"  value="{{$ingreso->cabecera_ingreso_motivo}}">{{$ingreso->cabecera_ingreso_motivo}}</label>
                                </div>
                            </div>
                        </div>
                        @if(Auth::user()->empresa->empresa_contabilidad == '1')
                        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 form-control-label  "
                            style="margin-bottom : 0px;">
                            <label>N° DIARIO:</label>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2" style="margin-bottom : 0px;">
                            <div class="form-group">
                                <label class="form-control"   value="{{$ingreso->diario->diario_codigo}}">{{$ingreso->diario->diario_codigo}}</label>  
                            </div>
                        </div>
                        @endif
                    </div>
                    <div class="row" style="display: none;">
                        <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3" style="margin-bottom: 0px;">
                            <div class="form-group">
                                <div class="form-line">
                                    <input id="codigoProducto" name="idProducto" type="hidden">
                                    <input id="idProductoID" name="idProductoID" type="hidden">
                                    <input id="buscarProducto" name="buscarProducto" type="text" class="form-control"
                                        placeholder="Buscar producto" disabled>
                                    <span id="errorStock" class="text-danger invisible">El producto no tiene stock
                                        disponible.</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1" style="margin-bottom: 0px;">
                        <center><label>Disponible</label></center>
                        </div>
                        <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1" style="margin-bottom: 0px;">
                            <div class="form-group">
                                <div class="form-line">
                                    <input id="id_disponible" name="id_disponible" type="number" class="form-control"
                                        placeholder="Disponible" value="0" disabled>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1" style="margin-bottom: 0px;">
                            <center><label>Cantidad</label></center>
                        </div>
                        <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1" style="margin-bottom: 0px;">
                            <div class="form-group">
                                <div class="form-line">
                                    <input onchange="calcularTotal()" onkeyup="calcularTotal()" id="id_cantidad"
                                        name="id_cantidad" type="number" class="form-control" placeholder="0" value="1">
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1" style="margin-bottom: 0px;">
                            <center><label>Precio</label></center>
                        </div>
                        <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1" style="margin-bottom: 0px;">
                            <div class="form-group">
                                <div class="form-line">
                                    <input onchange="calcularTotal()" onkeyup="calcularTotal()" id="id_pu" name="id_pu"
                                        type="text" class="form-control" placeholder="Precio" value="0.00">
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1" style="margin-bottom: 0px;">
                            <center><label>Total</label></center>
                        </div>
                        <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1" style="margin-bottom: 0px;">
                            <div class="form-group">
                                <div class="form-line">
                                    <input id="id_total" name="id_total" type="text" class="form-control"
                                        placeholder="Total" value="0.00" disabled>
                                </div>
                            </div>
                        </div>
                        
                    </div>
                    <div class="row" style="display: none;">
                        <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3" style="margin-bottom: 0px;">
                            <div class="form-group">
                                <input id="descripcionProducto" name="descripcionProducto" type="text"
                                    class="form-control" placeholder="Descripcion">
                            </div>
                        </div>
                        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2" style="margin-bottom: 0px;">
                            <div class="form-group">
                                <select class="form-control select2" id="cuentaProductoID" name="cuentaProductoID"
                                    data-live-search="true">
                                    @foreach($cuentas as $cuenta)
                                    <option value="{{$cuenta->cuenta_id}}">{{$cuenta->cuenta_numero}}
                                        {{$cuenta->cuenta_nombre}}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1" style="margin-bottom: 0px;">
                            <label>C. Consumo</label>
                        </div>
                        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2" style="margin-bottom: 0px;">
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
                                @include ('admin.inventario.ingresoBodega.itemingresoPresentar')
                                <table id="cargarItemingreso"
                                    class="table table-striped table-hover boder-sar tabla-item-orden"
                                    style="margin-bottom: 6px;">
                                    <thead>
                                    <tr class="letra-blanca fondo-azul-claro">
                                        <th>Cantidad</th>
                                            <th>Codigo</th>
                                            <th>Producto</th>
                                           
                                            <th>P.U.</th>
                                            <th>Total</th>
                                           
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
                                                            <div class="form-control ">{{ $ingreso->cabecera_ingreso_comentario}}</div>
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

function cargarmetodo() {
    
    <?php
    
        foreach ($ingreso->detalles as $x) {
            ?>  
       
            document.getElementById("id_total").value=Number('<?php echo($x->detalle_ingreso_precio_unitario * $x->detalle_ingreso_cantidad); ?>');
            document.getElementById("id_cantidad").value='<?php echo $x->detalle_ingreso_cantidad; ?>';
            document.getElementById("codigoProducto").value='<?php echo $x->producto->producto_codigo; ?>';
            document.getElementById("idProductoID").value='<?php echo $x->producto->producto_id; ?>';
            document.getElementById("buscarProducto").value='<?php echo $x->producto->producto_nombre; ?>';
            document.getElementById("id_pu").value=Number('<?php echo $x->detalle_ingreso_precio_unitario; ?>');

            agregarItem();

    <?php
        }

    ?>
}

function nuevo() {
   
    $('#bodega_id').css('pointer-events', 'none');
  
   
}

function agregarItem() {
   
        var combo2 = document.getElementById("idconsumo");
            total = Number(document.getElementById("id_total").value);
            
            var linea = $("#plantillaItemingresoprese").html();
            linea = linea.replace(/{ID}/g, id_item);
            linea = linea.replace(/{Dcantidad}/g, document.getElementById("id_cantidad").value);
            linea = linea.replace(/{Dcodigo}/g, document.getElementById("codigoProducto").value);
            linea = linea.replace(/{DprodcutoID}/g, document.getElementById("idProductoID").value);
            linea = linea.replace(/{Dnombre}/g, document.getElementById("buscarProducto").value);
            linea = linea.replace(/{Dpu}/g, document.getElementById("id_pu").value);
        
            linea = linea.replace(/{Dtotal}/g, Number(total).toFixed(2));
            $("#cargarItemingreso tbody").append(linea);
            id_item = id_item + 1;
           
            cargarTotales(total);
           
            resetearCampos();

}

function cargarTotales(total) {
    var subtotal = Number(Number(document.getElementById("subtotal").innerHTML) + total).toFixed(2);
     
        document.getElementById("subtotal").innerHTML = subtotal;
        document.getElementById("idSubtotal").value = subtotal;
       
        document.getElementById("total").innerHTML = subtotal;
        document.getElementById("idTotal").value = subtotal;
}


    
function resetearCampos() {
        document.getElementById("id_cantidad").value = 1;
        document.getElementById("codigoProducto").value = "";
        document.getElementById("idProductoID").value = "";
        document.getElementById("descripcionProducto").value = "";
        document.getElementById("buscarProducto").value = "";
        document.getElementById("id_disponible").value = "0";
        document.getElementById("id_pu").value = "0.00";
}

function eliminarItem(id,  total) {
    cargarTotales( total * (-1));
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


function calcularFecha() {
    let hoy = new Date();
    let semMiliSeg = 1000 * 60 * 60 * 24 * document.getElementById("ingreso_dias_plazo").value;
    let suma = hoy.getTime() + semMiliSeg;
    let fecha = new Date(suma);
    document.getElementById("ingreso_fecha_termino").value = fecha.getFullYear() + '-' + ponerCeros(fecha.getMonth() +
        1) + '-' + ponerCeros(fecha.getDate());
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