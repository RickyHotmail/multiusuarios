@extends ('admin.layouts.admin')
@section('principal')
<form class="form-horizontal" method="POST" action="{{ url("producto/codigo") }}">
@csrf
    <div class="card card-secondary">
        <div class="card-header">
            <h3 class="card-title">Configurar Codigos de Producto</h3>
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
                    <label for="dias" class="col-form-label">{{$producto->producto_nombre}} </label>
                </div>
            </div>  
            <hr>
            <div class="form-group row">
                <label for="dias" class="col-sm-1 col-form-label">Codigo : </label>
                <div class="col-sm-4">
                    <input type="text" class="form-control" id="nombre" name="nombre" value="" placeholder="Codigo">
                </div>
                <label for="valor" class="col-sm-1 col-form-label">Proveedor : </label>
                <div class="col-sm-4">
                    <select class="custom-select select2" id="proveedor" name="proveedor" require>
                        @foreach($proveedores as $proveedor)
                            <option value="{{$proveedor->proveedor_id}}" >{{$proveedor->proveedor_nombre}}</option>
                        @endforeach
                    </select>
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
                                    <th>Codigo</th>
                                    <th>Proveedor</th> 
                                    <th width="10"></th>                                          
                                </tr>
                            </thead>
                            <tbody>
                                <?php $count = 1; ?>
                                @foreach($producto->codigos as $codigo)
                                <tr class="text-center" id="row_{{ $count }}">
                                    <td>{{ $codigo->codigo_nombre }}<input class="invisible" name="DLdias[]" value="{{ $codigo->codigo_nombre }}"/></td>
                                    <td>{{ $codigo->proveedor->proveedor_nombre }}<input class="invisible" name="DLvalor[]" value="{{ $codigo->proveedor->proveedor_nombre }}"/>  <input class="invisible" name="idpr[]" value="{{ $codigo->proveedor_id }}"/></td>      
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
                    
        if( document.getElementById("nombre").value != '' ){     
            var linea = $("#plantillaItemPrecio").html();
            linea = linea.replace(/{ID}/g, id_item);
            
            linea = linea.replace(/{DLdias}/g, document.getElementById("nombre").value);
            var combo = document.getElementById("proveedor");
            linea = linea.replace(/{DLvalor}/g,  combo.options[combo.selectedIndex].text);
            linea = linea.replace(/{idpro}/g, document.getElementById("proveedor").value);
            $("#cargarItemPrecio tbody").append(linea);
            id_item = id_item + 1;            
            resetearCampos();
        }else{
            bootbox.alert({
                message: "Defina el Codigo",
                size: 'small'
            });
        }
    }      
    function resetearCampos() {
        document.getElementById("nombre").value = "";
      
    }
    function eliminarItem(id) {
        $("#row_" + id).remove();
        id_item = id_item - 1;
    }
</script>
@endsection