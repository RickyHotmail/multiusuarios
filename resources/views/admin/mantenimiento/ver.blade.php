@extends ('admin.layouts.admin')
@section('principal')

<style>
    ul.ui-autocomplete.ui-menu {
        z-index: 2000;
    }
</style>
<div class="card card-secondary">
    <div class="card-header text-right p-2">
        <h3 class="card-title">Orden de Mantenimiento</h3>
        <button onclick="history.back()" class="btn btn-xs btn-default" data-toggle="modal" data-target="#modal-nuevo"><i class="fa fa-undo"></i>&nbsp;&nbsp;Regresar</button>
    </div>
    </div>
    <!-- /.card-header -->
    <div class="card-body offset-md-1 col-md-10">
        <div class="form-group row mb-1">
            <label for="cliente" class="col-sm-1 col-form-label">Cédula: </label>
            <input type="text" class="form-control col-sm-2" value="{{ $orden->cliente->cliente_cedula }}">
        </div>
        <div class="form-group row mb-1">
            <label for="cliente" class="col-sm-1 col-form-label">Cliente: </label>
            <input type="text" class="col-sm-10 form-control" value="{{ $orden->cliente->cliente_nombre }}">
        </div>    
        <div class="form-group row mb-1">
            <div class="col-sm-6 row">
                <label for="lugar" class="col-sm-2 col-form-label">Teléfono 1:</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" name="" value="{{ $orden->cliente->cliente_telefono }}">
                </div>
            </div>
            <div class="col-sm-6 row">
                <label for="fecha_inicio" class="col-sm-2 col-form-label">Teléfono 2:</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control"  name="" value="{{ $orden->cliente->cliente_celular }}">
                </div>
            </div>
        </div>
        
        <div class="form-group row mb-1">
            <div class="col-sm-6 row">
                <label for="tipo_id" class="col-sm-2 col-form-label">Tipo de Orden: </label>
                <div class="col-sm-10">
                    <select class="custom-select select2" id="tipo_id" name="tipo_id" required readonly>
                        <option value=1 @if($orden->tipo_id==1) selected @endif>Rutas Técnicas</option>
                        <option value=2 @if($orden->tipo_id==2) selected @endif>Sistemas</option>
                        <option value=3 @if($orden->tipo_id==3) selected @endif>Soporte Técnico</option>
                    </select>
                </div>
            </div>
            <div class="col-sm-6 row">
                <label for="prioridad" class="col-sm-2 col-form-label">Prioridad: </label>
                <div class="col-sm-10">
                    <select class="custom-select select2" id="prioridad" name="prioridad" required>
                        <option value=1 @if($orden->tipo_id==1) selected @endif>Normal</option>
                        <option value=2 @if($orden->tipo_id==2) selected @endif>Importante</option>
                        <option value=3 @if($orden->tipo_id==3) selected @endif>Urgente</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="form-group row mb-5">
            <div class="col-sm-6 row">
                <label for="fecha_inicio" class="col-sm-3 col-form-label">Estado de la Orden:</label>
                <div class="col-sm-9">
                    <select class="custom-select select2" id="" name="" readonly>
                        <option value=0 @if($orden->orden_estado==0) selected @endif>Anulada</option>
                        <option value=1 @if($orden->orden_estado==1) selected @endif>Creada</option>
                        <option value=2 @if($orden->orden_estado==2) selected @endif>Generada</option>
                        <option value=3 @if($orden->orden_estado==3) selected @endif>En Proceso</option>
                        <option value=4 @if($orden->orden_estado==4) selected @endif>Finalizada</option>
                    </select>
                </div>
            </div>
        </div>
        
        <div class="form-group row mt-3 mt-2 mb-5">
            <div class="col-md-12" style="margin-bottom: 0px;">
                <label class="pt-1">Descripción de Orden: </label>
                <table id="cargarItemDescripcion" class="table table-striped table-hover" style="margin-bottom: 6px; background-color: #eee">
                    <thead>
                        <tr class="letra-blanca fondo-azul-claro text-center">
                            <th style="width: 5%"></th>
                            <th style="width: 60%">Descripcion</th>
                            <th></th>
                            <th></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orden->detalles as $detOrden)
                            <tr>
                                <th></th>
                                <td>{{ $detOrden->detalle_descripcion }}</td>
                                <th></th>
                                <th></th>
                                <th></th>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="form-group row mb-1">
            <div class="col-md-6 row">
                <label for="lugar" class="col-sm-3 col-form-label text-right">Lugar:</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" name="lugar"  value="{{ $orden->orden_lugar }}">
                </div>
            </div>
            <div class="col-md-6 row">
                <label for="fecha_inicio" class="col-sm-3 col-form-label text-right">Fecha Inicio:</label>
                <div class="col-sm-9">
                    <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" value="{{ $orden->orden_fecha_inicio }}">
                </div>
            </div>
        </div>
        <div class="form-group row mb-1">
            <div class="col-md-6 row">
                <label for="logistica" class="col-sm-3 col-form-label text-right">Logistica:</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" id="logistica" name="logistica" value="{{ $orden->orden_logistica }}">
                </div>
            </div>

            <div class="col-md-6 row">
                <label for="asignacion" class="col-sm-3 col-form-label text-right">Asignación:</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" id="asignacion" name="asignacion" value="{{ $orden->orden_asignacion }}">
                </div>
            </div>
        </div>
        <div class="form-group row mt-4 mb-5">
            <label for="responsable" class="col-sm-2 col-form-label text-right">Técnicos:</label>
            <div class="select2-purple">
                <select class="select2" id="select22" name="responsable[]" multiple="multiple" data-placeholder="Selecione los técnicos" data-dropdown-css-class="select2-purple" style="width: 100%;">
                    @foreach($tecnicos as $tecn)
                        <option value="{{$tecn->empleado_id}}"
                            @foreach($orden->responsables as $resp)
                                @if($resp->tecnico!=null)
                                    @if($resp->tecnico->empleado!=null)
                                        @if($resp->tecnico->empleado->empleado_id==$tecn->empleado_id) 
                                            selected
                                        @endif
                                    @else
                                        @if($resp->tecnico->responsable_user_id==$tecn->responsable_user_id) 
                                            selected
                                        @endif
                                    @endif
                                @endif
                            @endforeach
                        >
                            @if($tecn->empleado!=null)
                                {{ $tecn->empleado->empleado_nombre }}
                            @else
                                {{ $tecn->responsable_user_apellido }} {{ $tecn->responsable_user_nombre }}
                            @endif
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <br><br><br>
        <div class="table-responsive">
            <table id="cargarItemProducto" class="table table-striped table-hover" style="margin-bottom: 6px;">
                <thead>
                    <tr class="letra-blanca fondo-azul-claro text-center">
                        <th></th>
                        <th>Productos</th>
                        <th>Cantidad</th>
                        <th></th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orden->detallesOrden as $det)
                        <tr>
                            <td></td>
                            <td>{{ $det->producto->producto_nombre }}</td>
                            <td>{{ $det->detalle_orden_cantidad }}</td>
                            <td></td>
                            <td></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <br><br><br>
    </div>
</div>
<!-- /.modal -->
@endsection

@section('scriptAjax')
<script>
	(function() {
		$("#buscarProducto").autocomplete({
			source: function(request, response){
				var localObj = window.location;
				var contextPath = localObj.pathname.split("/")[1];
				if(contextPath=='public'){
					contextPath="/"+contextPath;
				}else{
					contextPath='';
				}
				$.ajax({
					url: contextPath+"/producto/searchN/"+request.term,
					dataType: "json",
					type: "GET",
					data: {
						buscar: request.term
					},
					success: function(data){
						response($.map(data, function(producto){
							return {
								nombre: producto.producto_nombre,
								label: producto.producto_nombre,
								precio : producto.producto_precio1,
								tieneIva : producto.producto_tiene_iva,
								stock : producto.producto_stock,
								codigo: producto.producto_codigo,
								id:producto.producto_id,
								idmedicamento:producto.medicamento_id
							};
						}));
					},
				});
			},
			select: function(event, ui){
                $("#codigoProducto").val(ui.item.codigo);
                $("#idProductoID").val(ui.item.id);
                $("#buscarProducto").val(ui.item.nombre)
                $("#id_disponible").val(ui.item.stock)
                $("#idmedicamento").val(ui.item.idmedicamento)

				if(parseFloat(ui.item.stock) > 0){
					document.getElementById("buscarProducto").classList.remove('is-invalid');
					document.getElementById("errorStock").classList.add('invisible');
				}else{
					document.getElementById("buscarProducto").classList.add('is-invalid');
					document.getElementById("errorStock").classList.remove('invisible');
				}
				return false;
			}
		});
	})();

    var id_item = 0;  
    function agregarItemProducto() {
        idProducto=document.getElementById("idProductoID").value;
        nombreProducto=document.getElementById("buscarProducto").value;
        cantidadProducto=document.getElementById("id_cantidad").value;
        stockProducto=document.getElementById("id_disponible").value;

        texto="     <tr class='text-center' id='row_"+id_item+"'>"
        texto+="        <td><a onclick='eliminarItem("+id_item+");' class='btn btn-danger waves-effect' style='padding: 2px 8px;'>X</a></td>"
        texto+=`        <td>${nombreProducto}<input class='invisible' name='id_detalle_orden[]' value='${idProducto}'></td>`
        texto+=`        <td><input class='form-control2 text-center' value='${stockProducto}' type='text' readonly name='stock_detalle_orden[]' required></td>`
        texto+=`        <td><input class='form-control2 text-center' value='${cantidadProducto}' min='1' type='number' name='cantidad_detalle_orden[]' required></td>`
                        
        texto+="       <td></td>"
        texto+="       <td></td>"
        texto+="    </tr>"


        $("#cargarItemProducto tbody").append(texto);
        id_item++;

        document.getElementById("id_cantidad").value = "";
        document.getElementById("idProductoID").value = "";
        document.getElementById("buscarProducto").value = "";
        document.getElementById("id_disponible").value = "";
    }

    var id_descripcion = 0;  
    function agregarDetalleOrden() {
        descripcion=document.getElementById("detalleDescripcion").value;

        texto="     <tr class='text-center' id='row_"+id_descripcion+"'>"
        texto+="        <td><a onclick='eliminarDescripcion("+id_descripcion+");' class='btn btn-danger waves-effect' style='padding: 2px 8px;'>X</a></td>"
        texto+=`        <td class="text-left">${descripcion}<input class='invisible' name='descripcion[]' value='${descripcion}'></td>`
        texto+="       <td></td>"
        texto+="       <td></td>"
        texto+="       <td></td>"
        texto+="    </tr>"


        $("#cargarItemDescripcion tbody").append(texto);
        id_descripcion++;

        document.getElementById("detalleDescripcion").value = "";
    }

    function eliminarItem(id) {
        $("#row_" + id).remove();
    }
    function eliminarDescripcion(id) {
        $("#row_" + id).remove();
    }

    function setearHora(){
        var fecha = new Date();
        var mes = fecha.getMonth()+1;
        var dia = fecha.getDate();
        var ano = fecha.getFullYear();
        if(dia<10) dia='0'+dia
        if(mes<10) mes='0'+mes

        document.getElementById('fecha_inicio').value=ano+"-"+mes+"-"+dia;
    }
</script>
@endsection