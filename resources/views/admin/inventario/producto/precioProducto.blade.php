@extends ('admin.layouts.admin')
@section('principal')
<form class="form-horizontal" method="POST" action="{{ url("producto/precio") }}">
@csrf
    <div class="card card-secondary">
        <div class="card-header">
            <h3 class="card-title">Configurar Precios de Producto</h3>
            <div class="float-right">
                <button type="submit" class="btn btn-success btn-sm"><i class="fa fa-save"></i>&nbsp;Guardar</button>
                <!--   
                <button type="button" onclick='window.location = "{{ url("producto") }}";' class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
                --> 
                <button  type="button" onclick="history.back()" class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
            </div>
        </div>
        <div class="card-body">
            <div class="form-group row">
                <label for="producto_nombre" class="col-sm-2 col-form-label">Porducto / Servicio : </label>
                <div class="col-sm-9">
                    <input type="hidden" id="idProducto" name="idProducto" value="{{$producto->producto_id}}" required>
                    <input type="text" class="form-control" id="producto_nombre" name="producto_nombre" value="{{$producto->producto_nombre}}" required>
                </div>
            </div>  
            <hr>
            <div class="form-group row">
                <label for="dias" class="col-sm-1 col-form-label">Días Plazo : </label>
                <div class="col-sm-4">
                    <input type="number" class="form-control" id="dias" name="dias" value="0" placeholder="Días">
                </div>
                <label for="valor" class="col-sm-1 col-form-label">Precio : </label>
                <div class="col-sm-4">
                    <input type="number" class="form-control" id="valor" name="valor" value="0.00" placeholder="Valor">
                </div>
                <div class="col-sm-2">
                    <a onclick="agregarDetalle();" class="btn btn-primary btn"><i class="fas fa-plus"></i></a>
                </div>
            </div>
            <div class="form-group row">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" style="margin-bottom: 0px;">
                @include ('admin.inventario.producto.itemPrecio')
                    <div id="mien" class="table-responsive">
                        <table id="cargarItemPrecio" class="table table-striped table-hover" style="margin-bottom: 6px;">
                            <thead>
                                <tr class="letra-blanca fondo-azul-claro text-center">                                                
                                    <th>Dias Plazo</th>
                                    <th>Precio</th> 
                                    <th width="10"></th>                                          
                                </tr>
                            </thead>
                            <tbody>
                                <?php $count = 1; ?>
                                @foreach($producto->precios as $precio)
                                <tr class="text-center" id="row_{{ $count }}">
                                    <td>{{ $precio->precio_dias }}<input class="invisible" name="DLdias[]" value="{{ $precio->precio_dias }}"/></td>
                                    <td>{{ $precio->precio_valor }}<input class="invisible" name="DLvalor[]" value="{{ $precio->precio_valor }}"/></td>
                                    <td><a onclick="eliminarItem({{ $count }});" class="btn btn-danger waves-effect" style="padding: 2px 8px;">X</a></td>
                                </tr>
                                <?php $count = $count + 1; ?>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div> 
        </div>         
    </div>
</form>

<script type="text/javascript">
    if( <?php echo $count;?> == 1){
        var id_item = 1;
    }else{
        id_item = <?php echo $count;?>;
    }
    function agregarDetalle(){               
        if(document.getElementById("dias").value != '' && document.getElementById("valor").value != '' && document.getElementById("valor").value > 0){     
            var linea = $("#plantillaItemPrecio").html();
            linea = linea.replace(/{ID}/g, id_item);
            linea = linea.replace(/{DLdias}/g, document.getElementById("dias").value);
            linea = linea.replace(/{DLvalor}/g, document.getElementById("valor").value);
            $("#cargarItemPrecio tbody").append(linea);
            id_item = id_item + 1;            
            resetearCampos();
        }else{
            bootbox.alert({
                message: "El precio debe ser mayor a 0.",
                size: 'small'
            });
        }
    }      
    function resetearCampos() {
        document.getElementById("dias").value = "0";
        document.getElementById("valor").value = "0.00";
    }
    function eliminarItem(id) {
        $("#row_" + id).remove();
        id_item = id_item - 1;
    }
</script>
@endsection