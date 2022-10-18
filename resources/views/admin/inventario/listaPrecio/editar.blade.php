@extends ('admin.layouts.admin')
@section('principal')
<meta name="csrf-token" content="{{ csrf_token() }}">
<form class="form-horizontal" method="POST" action="{{ route('listaPrecio.update', [$lista->lista_id]) }}">
@method('PUT')
@csrf
    <div class="card card-secondary">
        <div class="card-header">
            <h3 class="card-title">Editar lista de precios</h3>
            <div class="float-right">
                <button type="submit" class="btn btn-success btn-sm"><i class="fa fa-save"></i>&nbsp;Guardar</button>
                <!--   
                <button type="button" onclick='window.location = "{{ url("listaPrecio") }}";' class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
                --> 
                <button  type="button" onclick="history.back()" class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
            </div>
        </div>
        <div class="card-body">        
            <div class="form-group row">
                <label for="idNombre" class="col-sm-2 col-form-label">Nombre</label>
                <div class="col-sm-10">                                                          
                    <input type="text" class="form-control" id="idNombre" name="idNombre" value="{{$lista->lista_nombre}}" placeholder="" required>                  
                </div>
            </div>                   
            <div class="form-group row">
                <label for="idProducto" class="col-sm-2 col-form-label">Producto</label>
                <div class="col-sm-10">  
                    <input class="invisible" id="auxLista" name="auxLista" value="{{$lista->lista_id}}" >            
                    <select id="idProducto" name="idProducto" class="form-control select2"  onchange="cargarDetalles();"  required>
                        <option value="" label>--Seleccione una opcion--</option>
                        @foreach($productos as $producto)
                            <option value="{{$producto->producto_id}}">{{$producto->producto_nombre}}</option>
                        @endforeach                                                       
                    </select>
                </div>
            </div>    
            <hr>
            <div class="form-group row">
                <label for="dias" class="col-sm-2 col-form-label">Días : </label>
                <div class="col-sm-4">
                    <input type="number" class="form-control" id="dias" name="dias" value="0" placeholder="Días" disabled>
                </div>
                <label for="valor" class="col-sm-1 col-form-label">Valor: </label>
                <div class="col-sm-4">
                    <input type="number" class="form-control" id="valor" name="valor" value="0.00" placeholder="Valor" disabled>
                </div>
                <div class="col-sm-1">
                    <a onclick="agregarDetalle();" class="btn btn-primary btn"><i class="fas fa-plus"></i></a>
                </div>
            </div>
            <div class="form-group row">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" style="margin-bottom: 0px;">
                @include ('admin.inventario.listaPrecio.itemDetalleLista')
                    <div id="mien" class="table-responsive">
                        <table id="cargarItemDetalleLista" class="table table-striped table-hover" style="margin-bottom: 6px;">
                            <thead>
                                <tr class="letra-blanca fondo-azul-claro text-center">                                                
                                    <th>Dias</th>
                                    <th>Valor</th>  
                                    <th width="10"></th>                                        
                                </tr>
                            </thead>
                            <tbody> 

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>   
            <hr>
            <div class="form-group row">
                <label for="idEstado" class="col-sm-2 col-form-label">Estado</label>
                <div class="col-sm-9">
                    <div class="col-form-label custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                        @if($lista->lista_estado=="1")
                        <input type="checkbox" class="custom-control-input" id="idEstado" name="idEstado" checked>
                        @else
                        <input type="checkbox" class="custom-control-input" id="idEstado" name="idEstado">
                        @endif
                        <label class="custom-control-label" for="idEstado"></label>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
var count = 0;
    

    function cargarDetalles(){   
        $('#cargarItemDetalleLista tbody').empty();
        desbloqueo();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            }
        });        
        //console.log(document.getElementById("auxLista").value +" "+ document.getElementById("idProducto").value);
        $.ajax({
            async: false,
            url: '{{ url("listaPrecio/searchN") }}',
            dataType: "json",            
            type: "POST",
            data: {
                idLista: document.getElementById("auxLista").value,
                producto: document.getElementById("idProducto").value
            },                       
            success: function(data){           
                var id_item = 1;
                for (var i=0; i<data.length; i++) {
                    var linea = $("#plantillaItemPDetalleLista").html();
                    
                    linea = linea.replace(/{ID}/g, id_item);
                    linea = linea.replace(/{DLdias}/g,  data[i].detallel_dias);
                    linea = linea.replace(/{DLvalor}/g,data[i].detallel_valor);
                    linea = linea.replace(/{DLproductoID}/g, document.getElementById("idProducto").value);

                    id_item = id_item + 1;  
                    $("#cargarItemDetalleLista tbody").append(linea);    

                }   
                count = data.length; 
            },
            error: function(data) {
                alert("error");  
            },
        });
    }

    function agregarDetalle(){               
        if(document.getElementById("dias").value != '' && document.getElementById("valor").value != '' && document.getElementById("valor").value > 0){ 
            var id_item = 1;
            if( count > 0){
                id_item = count;
            }
            var linea = $("#plantillaItemPDetalleLista").html();
            id_item = id_item + 1;  
            linea = linea.replace(/{ID}/g, id_item);
            linea = linea.replace(/{DLdias}/g, document.getElementById("dias").value);
            linea = linea.replace(/{DLvalor}/g, document.getElementById("valor").value);
            linea = linea.replace(/{DLproductoID}/g, document.getElementById("idProducto").value);
            $("#cargarItemDetalleLista tbody").append(linea);
            count = id_item;
                    
            resetearCampos();
        }else{
            bootbox.alert({
                message: "El valor debe ser mayor a 0.",
                size: 'small'
            });
        }
    }      

    function resetearCampos() {
        document.getElementById("dias").value = "0";
        document.getElementById("valor").value = "0.00";
    }

    function desbloqueo(){
        if(document.getElementById("idProducto").value != ""){
            document.getElementById("dias").disabled = false;
            document.getElementById("valor").disabled = false;
        }else{
            document.getElementById("dias").disabled = true;
            document.getElementById("valor").disabled = true;
        }
        
    }  
    function eliminarItem(id) {
        $("#row_" + id).remove();
        id_item = id_item - 1;
    }

</script>
@endsection