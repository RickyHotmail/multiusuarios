@extends ('admin.layouts.admin')
@section('principal')
<form class="form-horizontal" method="POST" action="{{ route('ordenRecepecion.update', [$orden->ordenr_id]) }}">
    @method('PUT')
    @csrf

    <div class="card card-secondary">
        <div class="card-header">
            <h3 class="card-title">Editar Orden de Recepción</h3>
            <div class="float-right">
                <button type="submit" class="btn btn-success btn-sm"><i class="fa fa-save"></i>&nbsp;Guardar</button>
                 <!--     
                <button type="button" onclick='window.location = "{{ url("ordenRecepecion") }}";' class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
                --> 
            <button  type="button" onclick="history.back()" class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
            </div>
        </div>
    
            
            <!-- /.card-header -->
            <div class="card-body">
                <div class="row">
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                        <div class="row clearfix form-horizontal">
                            <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2 form-control-label ">
                                <label>NUMERO</label>
                            </div>
                            <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                                <div class="form-group">
                                    <div class="form-line">
                                        <label class="form-control" >{{$orden->ordenr_serie}}</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                                <div class="form-group">
                                    <div class="form-line">
                                        <label class="form-control" >{{$orden->ordenr_numero}}</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 form-control-label" style="padding-left: 55px;">
                            </div>
                            <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 form-control-label  "
                                style="margin-bottom : 0px;">
                                <label>Fecha :</label>
                            </div>
                            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3" style="margin-bottom : 0px;">
                                <div class="form-group">
                                    <div class="form-line">
                                        <input type="date" id="orden_fecha" name="orden_fecha" class="form-control "
                                            placeholder="Seleccione una fecha..."
                                            value='{{$orden->ordenr_fecha}}' required />
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="row clearfix form-horizontal">
                            <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2 form-control-label  "
                                style="margin-bottom : 0px;">
                                <label>PROVEEDOR :</label>
                            </div>
                            <div class="col-lg-7 col-md-7 col-sm-7 col-xs-7" style="margin-bottom : 0px;">
                                <div class="form-group">
                                    <input id="proveedorID" name="proveedorID" type="hidden" value="{{$orden->proveedor->proveedor_id}}">
                                    <input id="buscarProveedor" name="buscarProveedor" type="text" value="{{$orden->proveedor->proveedor_nombre}}" class="form-control "
                                        placeholder="Proveedor" value="{{$orden->proveedor->proveedor_nombre}}" required >
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3  form-control-label  centrar-texto"
                                style="margin-bottom : 0px;">
                                <div class="form-group">
                                    <label>BODEGA</label>
                                </div>
                            </div>
                        </div>
                        <div class="row clearfix form-horizontal">
                            <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2 form-control-label  "
                                style="margin-bottom : 0px;">
                                <label>RUC/CI :</label>
                            </div>
                            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3" style="margin-bottom : 0px;">
                                <div class="form-group">
                                    <div class="form-line">
                                        <input type="text" id="idRUC" name="idRUC" class="form-control "
                                            placeholder="Ruc" value="{{$orden->proveedor->proveedor_ruc}}" disabled required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 form-control-label  "
                                style="margin-bottom : 0px;">
                                <label>TELEFONO :</label>
                            </div>
                            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3" style="margin-bottom : 0px;">
                                <div class="form-group">
                                    <div class="form-line">
                                        <input type="text" id="idTelefono" name="idTelefono" class="form-control "
                                            placeholder="Telefono" value="{{$orden->proveedor->proveedor_telefono}}"  disabled>
                                    </div>
                                </div>
                            </div>  
                            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3 alinear-izquierda" style="margin-bottom : 0px;">
                                <div class="form-group">
                                    <select id="bodega_id" name="bodega_id" class="form-control show-tick"
                                        data-live-search="true">
                                        @foreach($bodegas as $bodega)
                                            <option value="{{ $bodega->bodega_id }}"  @if($bodega->bodega_id==$orden->bodega_id) selected  @endif >{{ $bodega->bodega_nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            
                        </div>
                        <div class="row clearfix form-horizontal">
                            <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2 form-control-label  "
                                style="margin-bottom : 0px;">
                                <label>DIRECCION :</label>
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6" style="margin-bottom : 0px;">
                                <div class="form-group">
                                    <div class="form-line">
                                        <input type="text" id="idDireccion" name="idDireccion" class="form-control "
                                            placeholder="Direccion" value="{{$orden->proveedor->proveedor_direccion}}" disabled required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 alinear-izquierda "
                            style="margin-bottom : 0px;">
                            <label>GUIA :</label>
                            </div>
                            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3" style="margin-bottom : 0px;">
                                <div class="form-group">
                                    <div class="form-line">
                                        <input type="text" id="idGuia" name="idGuia" class="form-control "
                                        value="{{$orden->ordenr_guia}}" placeholder="Guia" >
                                    </div>
                                </div>
                            </div>
                            
                        </div>
                        
                        <div class="row clearfix form-horizontal">
                            <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2 form-control-label  "
                                style="margin-bottom : 0px;">
                                <label>Observacion :</label>
                            </div>
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="margin-bottom : 0px;">
                                <div class="form-group">
                                    <div class="form-line">
                                        <textarea class="form-control" id="orden_comentario" name="orden_comentario" value="{{$orden->ordenr_observacion}}">{{$orden->ordenr_observacion}} </textarea>
                                    </div>
                                </div>
                            </div>            
                        </div>                   
                        <hr>
                        <div class="row">
                            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6" style="margin-bottom: 0px;">
                                <label>Nombre de Producto</label>
                                <div class="form-group">
                                    <div class="form-line">
                                        <input id="codigoProducto" name="idProducto" type="hidden">
                                        <input id="idProductoID" name="idProductoID" type="hidden">
                                        <input id="buscarProducto" name="buscarProducto" type="text" class="form-control"
                                            placeholder="Buscar producto" >
                                        <span id="errorStock" class="text-danger invisible">El producto no tiene stock
                                            disponible.</span>
                                    </div>
                                </div>
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
                        
                            
                            <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
                                <a onclick="agregarItem();" class="btn btn-primary btn-venta"><i
                                        class="fas fa-plus"></i></a>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" style="margin-bottom: 0px;">
                                <div class="table-responsive">
                                    @include ('admin.ventas.guiaremision.itemResmision')
                                    <table id="cargarItemFactura"
                                        class="table table-striped table-hover boder-sar tabla-item-factura"
                                        style="margin-bottom: 6px;">
                                        <thead>
                                            <tr class="letra-blanca fondo-azul-claro">
                                                <th>Cantidad</th>
                                                <th>Codigo</th>
                                                <th>Producto</th>
                                                
                                                <th width="40"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php $cont = 1;?>
                                            @foreach($orden->detalles as $ordenes)
                                            
                                                <tr class="text-center"  id="row_<?php echo $cont; ?>">
                                                <td>{{$ordenes->detalle_cantidad}}<input class="invisible" name="Dcantidad[]" value="{{$ordenes->detalle_cantidad}}" /></td>
                                                <td>{{$ordenes->producto->producto_codigo}}<input class="invisible" name="DprodcutoID[]" value="{{$ordenes->producto->producto_id}}" /><input class="invisible" name="Dcodigo[]" value="{{$ordenes->producto->producto_codigo}}" /></td>
                                                <td>{{$ordenes->producto->producto_nombre}}<input class="invisible" name="Dnombre[]" value="{{$ordenes->producto->producto_nombre}}" /></td>
                                                <td><a onclick="eliminarItem(<?php echo $cont; ?>);" class="btn btn-danger waves-effect"
                                                        style="padding: 2px 8px;">X</a></td>      
                                                    
                                                    <?php $cont = $cont + 1;?>                                   
                                                </tr>
                                                
                                            @endforeach   
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <br>
                    </div>
                </div>
            </div>
        
    </div>
</form>
<!-- /.card -->
@section('scriptAjax')
<script src="{{ asset('admin/js/ajax/autocompleteOrdenProveedor.js') }}"></script>
<script src="{{ asset('admin/js/ajax/autocompleteProductoOrden.js') }}"></script>
@endsection
<script type="text/javascript">
    if( <?php echo $cont;?> == 1){
        var id_item = 1;
    }else{
        id_item = <?php echo $cont;?>;
    }    

function nuevo() {
    $('#bodega_id').css('pointer-events', 'none');
    // document.getElementById("bodega_id").disabled  = true;
    document.getElementById("guardarID").disabled = false;
    document.getElementById("cancelarID").disabled = false;
    document.getElementById("nuevoID").disabled = true;
    
    document.getElementById("buscarProducto").disabled = false;
   
    document.getElementById("buscarProveedor").disabled = false;
}

function agregarItem() {
   
    
      
        var linea = $("#plantillaItemFactura").html();
        linea = linea.replace(/{ID}/g, id_item);
        linea = linea.replace(/{Dcantidad}/g, document.getElementById("id_cantidad").value);
        linea = linea.replace(/{Dcodigo}/g, document.getElementById("codigoProducto").value);
        linea = linea.replace(/{DprodcutoID}/g, document.getElementById("idProductoID").value);
        linea = linea.replace(/{Dnombre}/g, document.getElementById("buscarProducto").value);
      
        $("#cargarItemFactura tbody").append(linea);
        id_item = id_item + 1;
     
       
        resetearCampos();
    
}



function resetearCampos() {
    document.getElementById("id_cantidad").value = 1;
    document.getElementById("codigoProducto").value = "";
    document.getElementById("idProductoID").value = "";
    document.getElementById("buscarProducto").value = "";
    document.getElementById("id_disponible").value = "0";
   
}

function eliminarItem(id) {
   
    $("#row_" + id).remove();

}


function calcularFecha() {
    let hoy = new Date();
    let semMiliSeg = 1000 * 60 * 60 * 24 * document.getElementById("factura_dias_plazo").value;
    let suma = hoy.getTime() + semMiliSeg;
    let fecha = new Date(suma);
    document.getElementById("factura_fecha_termino").value = fecha.getFullYear() + '-' + ponerCeros(fecha.getMonth() +
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