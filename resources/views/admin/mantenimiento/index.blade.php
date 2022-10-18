@extends ('admin.layouts.admin')
@section('principal')

<style>
    ul.ui-autocomplete.ui-menu {
        z-index: 2000;
    }
</style>
<div class="card card-secondary">
    <div class="card-header text-right p-2">
        <h3 class="card-title">Ordenes de Mantenimientos</h3>
        <button onclick="setearHora();" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#modal-nuevo"><i class="fa fa-plus"></i>&nbsp;Agregar</button>
    </div>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <table id="example1" class="table table-bordered table-hover table-responsive sin-salto">
            <thead>
                <tr class="text-center neo-fondo-tabla">  
                    <th></th>
                    <th>Fecha</th>
                    <th>Numero</th>
                    <th>Tipo</th>  
                    <th>Cliente</th>
                    <th>Estado</th>
                    <th>Acción</th>
                </tr>
            </thead>            
            <tbody>
                @foreach($ordenes as $orden)
                <tr class="text-center">
                    <td>
                        @if($orden->orden_estado==1)
                            <a href="{{ url("orden/{$orden->orden_id}/comprobarStock") }}" class="btn btn-xs btn-primary"  data-toggle="tooltip" data-placement="top" title="Comprobar Stock"><i class="fa fa-edit" aria-hidden="true"></i></a>
                        @endif
                    </td>
                    
                    <td>{{ $orden->orden_fecha_inicio}}</td>
                    <td>{{ $orden->orden_id}}</td> 
                    <td>{{ $orden->tipo->tipo_nombre}}</td> 
                    <td>{{ $orden->cliente->cliente_nombre}}</td>
                    <td>
                        @if($orden->orden_estado==0)  ANULADA @endif
                        @if($orden->orden_estado==1)  CREADA @endif
                        @if($orden->orden_estado==2)  GENERADA @endif
                        @if($orden->orden_estado==3)  EN PROCESO @endif
                        @if($orden->orden_estado==4)  FINALIZADA @endif
                    </td>
                    <td><a class="btn btn-xs btn-primary" href="{{ url('ordenmantenimiento') }}/{{ $orden->orden_id }}/ver"><i class="fas fa-eye"></i> ver</a></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <!-- /.card-body -->
</div>
<!-- /.card -->
<div class="modal fade" id="modal-nuevo">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-secondary">
                <h4 class="modal-title">Nuevo Orden Mantenimiento</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form-horizontal" method="POST" action="{{ url("guardarordenmantenimiento") }}">
                @csrf
                <input type="hidden" name="web" value="enviado desde la web">
                <div class="modal-body">
                    <div class="card-body">
                        <div class="form-group row">
                            <div class="col-md-7 row">
                                <label for="tipo_id" class="col-sm-2 col-form-label">Tipo: </label>
                                <div class="col-sm-8">
                                    <select class="custom-select select2" id="tipo_id" name="tipo_id" required>
                                        <option value=1>Rutas Técnicas</option>
                                        <option value=2>Sistemas</option>
                                        <option value=3>Soporte Técnico</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row mb-5">
                            <label for="cliente" class="col-sm-1 col-form-label">Cliente: </label>
                            <div class="col-sm-10">
                                <select class="custom-select select2" id="cliente" name="cliente" required>
                                    <option value="">--Seleccione un Cliente--</option>
                                    @foreach($clientes as $cli)
                                        <option value={{ $cli->cliente_id }}>{{ $cli->cliente_nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>             

                        <?php $cant=0 ?>
                        <div class="form-group row">
                            @foreach($parametrizacion as $param)
                                <?php $cant++ ?>
                                <div class="col-md-6 row mb-1">
                                    <label for="tipo_id" class="col-sm-4 col-form-label">{{ $param->parametrizar_descripcion }}: </label>
                                    <input type="hidden" id="parametro_{{$cant}}" name="parametro[]" value={{$param->parametrizar_id}}>
                                    <input type="hidden" id="porcentaje_{{$cant}}" name="porcentaje[]" value={{$param->parametrizar_porcentaje}}>
                                    <div class="col-sm-8">
                                        <select class="custom-select select2" id="valor_{{$cant}}" name="valor[]" onChange="calcular()" required>
                                            @foreach($param->valores as $valor)
                                                <option value={{$valor->parametrizard_valor}}>{{$valor->parametrizard_valor}} - {{$valor->parametrizard_descripcion}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <input type="hidden" id="cantidad_parametro"  value={{$cant}}>

                        <div class="col-md-6 row">
                            <label for="prioridad" class="col-sm-4 col-form-label">Prioridad: </label>
                            <div class="col-sm-8">
                                <select class="custom-select select2" id="prioridad" name="prioridad" required>
                                    @foreach($escala as $esc)
                                        <option value={{$esc->prioridad_desde}}>{{$esc->prioridad_descripcion}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row mt-3 mt-2 mb-0">
                            <div class="col-md-11 row" style="margin-bottom: 0px;">
                                <label class="pt-1">Descripción de Orden: </label>
                                <div class="form-group col-md-7">
                                    <div class="form-line">
                                        <input id="detalleDescripcion" name="detalleDescripcion" type="text" class="form-control" placeholder="digite una descripción">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-1">
                                <a onclick="agregarDetalleOrden()" class="btn btn-primary"><i class="fas fa-plus"></i></a>
                            </div> 
                        </div>
                        <div class="table-responsive">
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
                                <tbody style="height: 100px;">
                                </tbody>
                            </table>
                        </div>
                        <div class="form-group row mb-1">
                            <div class="col-md-6 row">
                                <label for="lugar" class="col-sm-3 col-form-label">Lugar:</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="lugar" name="lugar" placeholder="Lugar" value="" required>
                                </div>
                            </div>
                            <div class="col-md-6 row">
                                <label for="fecha_inicio" class="col-sm-4 col-form-label">Fecha Inicio:</label>
                                <div class="col-sm-8">
                                    <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" required>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row mb-1">
                            <div class="col-md-6 row">
                                <label for="logistica" class="col-sm-3 col-form-label">Logistica:</label>
                                <div class="col-sm-9">
                                    @foreach($logisticas as $logistica)
                                        <input type="text" class="form-control" id="logistica" name="logistica" required>
                                    @endforeach
                                </div>
                            </div>
                            <div class="col-md-6 row">
                                <label for="logistica" class="col-sm-3 col-form-label">Logistica:</label>
                                <i Sclass="fa fa-plus"></i>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="logistica" name="logistica" style="display:none" required>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row mt-4 mb-5">
                            <label for="responsable" class="col-sm-2 col-form-label">Técnicos:</label>
                            <div class="select2-purple col-sm-10">
                                <select class="select2" id="select22" name="responsable[]" multiple="multiple" data-placeholder="Selecione los técnicos" data-dropdown-css-class="select2-purple" style="width: 100%;">
                                    @foreach($tecnicos as $tecn)
                                        @if($tecn->empleado!=null)
                                            <option value="{{$tecn->responsable_user_id}}">{{ $tecn->empleado->empleado_nombre }}</option>
                                        @else
                                            <option value="{{$tecn->responsable_user_id}}">{{ $tecn->responsable_user_apellido }} {{ $tecn->responsable_user_nombre }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <hr>
                        <div class="form-group row mt-5 mb-0">
                            <div class="col-xs-6 col-sm-6 col-md-5 col-lg-6" style="margin-bottom: 0px;">
                                <label>Nombre de Producto</label>
                                <div class="form-group">
                                    <div class="form-line">
                                        <input id="codigoProducto" name="idProducto" type="hidden">
                                        <input id="idProductoID" name="idProductoID" type="hidden">
                                        <input id="idmedicamento" name="idmedicamento" type="hidden">
                                        <input id="buscarProducto" name="buscarProducto" type="text" class="form-control" placeholder="Buscar producto">
                                        <span id="errorStock" class="text-danger invisible">El producto no tiene stock disponible.</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2" style="margin-bottom: 0px;">
                                <label>Disponible</label>
                                <div class="form-group">
                                    <div class="form-line">
                                        <input id="id_disponible" name="id_disponible" type="number" class="form-control" placeholder="Disponible" value="0" disabled>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2" style="margin-bottom: 0px;">
                                <label>Cantidad</label>
                                <div class="form-group">
                                    <div class="form-line">
                                        <input id="id_cantidad" name="id_cantidad" type="number" class="form-control" placeholder="Cantidad" value="1" min="1">
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
                                <a onclick="agregarItemProducto()" class="btn btn-primary btn-venta"><i class="fas fa-plus"></i></a>
                            </div> 
                        </div>
                        <hr>

                        <div class="table-responsive">
                            <table id="cargarItemProducto" class="table table-striped table-hover" style="margin-bottom: 6px;">
                                <thead>
                                    <tr class="letra-blanca fondo-azul-claro text-center">
                                        <th></th>
                                        <th>Productos</th>
                                        <th>Inventario</th>
                                        <th>Cant. Requerida</th>
                                        <th></th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Guardar</button>
                </div>
            </form>
        </div>
        <!-- /.modal-content -->
    </div>
</div>
<!-- /.modal -->
@endsection

@section('scriptAjax')
<script>
    var app = @json($escala);

    function calcular(){
        console.log('calculando')

        cant=$("#cantidad_parametro").val()

        total=0
        for(i=1; i<=cant; i++){
            valor=parseFloat($("#valor_"+i).val())
            porcentaje=parseFloat($("#porcentaje_"+i).val())
            
            max=300*porcentaje/100
            total+=(max/3)*valor
            console.log("valor "+valor+"    porc "+porcentaje)
        }

        ultimo=0
        $("#prioridad option").each(function(){
            
            option=$(this).attr('value')
            if(total>option) ultimo=option
        });

        $("#prioridad").val(ultimo).change();
        console.log(ultimo+"  "+total)
    }
</script>
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

        if(descripcion.length>0){
            texto="     <tr class='text-center' id='row_"+id_descripcion+"'>"
            texto+="        <td><a onclick='eliminarDescripcion("+id_descripcion+");' class='btn btn-danger waves-effect' style='padding: 2px 8px;'>X</a></td>"
            texto+=`        <td class="text-left">${descripcion}<input class='invisible' name='detalle[]' value='${descripcion}'></td>`
            texto+="       <td></td>"
            texto+="       <td></td>"
            texto+="       <td></td>"
            texto+="    </tr>"


            $("#cargarItemDescripcion tbody").append(texto);
            id_descripcion++;

            document.getElementById("detalleDescripcion").value = "";
        }
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