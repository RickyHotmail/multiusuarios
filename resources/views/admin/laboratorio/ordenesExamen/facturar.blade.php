@extends ('admin.layouts.admin')
@section('principal')
<meta name="csrf-token" content="{{ csrf_token() }}">
<form class="form-horizontal" method="POST" action="{{ url("facturarOrdenexamen") }}">
    @csrf
    <div class="card card-primary card-outline">
        <div class="card-header">
            <h3 class="card-title"><b>Facturar Orden de Examen</b></h3>
            <div class="float-right">
                <button type="submit" class="btn btn-success btn-sm"><i class="fa fa-save"></i>&nbsp;Guardar</button>
                <button type="button" onclick='window.location = "{{ url("ordenesExamen") }}";' class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
            </div>
        </div>
        <div class="card-body">
            <h5 class="form-control" style="color:#fff; background:#17a2b8;">Datos de Facturación</h5>
            <div class="form-group row">
                <label for="idFechaFac" class="col-sm-1 col-form-label">Fecha:</label>
                <div class="col-sm-2">
                    <input type="date" id="factura_fecha" name="factura_fecha" class="form-control" value='<?php echo(date("Y")."-".date("m")."-".date("d")); ?>' required readonly>
                </div>
                <label for="buscarCliente" class="col-sm-1 col-form-label">Cliente:</label>
                <div class="col-sm-4">
                    <input id="clienteID" name="clienteID" @if($clienteO) value="{{$clienteO->cliente_id}}"  @endif type="hidden">
                    <input id="buscarCliente" name="buscarCliente" type="text" class="form-control " @if($clienteO) value="{{$clienteO->cliente_nombre}}"  @endif placeholder="Cliente" required>
                </div>
                <label for="idCedulaF" class="col-sm-1 col-form-label">RUC/CI:</label>
                <div class="col-sm-2">
                    <input type="text" id="idCedula" name="idCedula" class="form-control " @if($clienteO) value="{{$clienteO->cliente_cedula}}"  @endif
                                        placeholder="Ruc" disabled required>
                </div>
                <div class="col-sm-1"><center><a href="cliente/create" class="btn btn-info btn-sm" target="_blank"><i class="fa fa-user"></i>&nbsp;Nuevo</a></center></div>
            </div>
            <div class="form-group row">
                <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 form-control-label"
                    style="margin-bottom : 0px;">
                    <label>PAGO :</label>
                </div>
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2" style="margin-bottom : 0px;">
                    <div class="form-group">
                        <select id="factura_tipo_pago" name="factura_tipo_pago" class="form-control show-tick "
                            data-live-search="true">
                            <option value="CONTADO">CONTADO</option>
                            <option value="EN EFECTIVO">EN EFECTIVO</option>
                        </select>
                    </div>
                </div>
                <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 form-control-label  "
                    style="margin-bottom : 0px;">
                    <label>Bodega :</label>
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
                <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 form-control-label  "
                    style="margin-bottom : 0px;">
                    <label>LUGAR :</label>
                </div>
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2" style="margin-bottom : 0px;">
                    <div class="form-group">
                        <div class="form-line">
                            <input type="text" id="factura_lugar" name="factura_lugar" class="form-control "
                                value="Machala" placeholder="Lugar" required>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3" style="margin-bottom : 0px;">
                    <div class="demo-checkbox">
                        <input type="radio" value="ELECTRONICA" id="check1"
                            class="with-gap radio-col-deep-orange" name="tipoDoc" checked required />
                        <label for="check1">Documento Electronico</label>
                    </div>
                </div>
            </div>
            <div class="form-group row">
                <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 form-control-label">
                    <label> FORMA  DE  PAGO :</label>
                </div>
                <div class="col-lg-5 col-md-5 col-sm-5 col-xs-5" style="margin-bottom : 1px;">
                    <div class="form-group">
                        <select class="form-control" id="forma_pago_id" name="forma_pago_id"
                            data-live-search="true">
                            @foreach($formasPago as $formaPago)
                            <option value="{{ $formaPago->forma_pago_id }}" @if($formaPago->forma_pago_nombre ==
                                'OTROS CON UTILIZACION DEL SISTEMA FINANCIERO') selected
                                @endif>{{ $formaPago->forma_pago_nombre }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 form-control-label  "
                    style="margin-bottom : 0px;">
                    <label>Caja :</label>
                </div>   
            
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2 form-control-label  "
                    style="margin-bottom : 0px;">
                        <select id="caja_id" name="caja_id" class="form-control show-tick" data-live-search="true" required>
                            @if($cajaAbierta)
                            <option value="{{ $cajaAbierta->caja->caja_id }}">{{ $cajaAbierta->caja->caja_nombre }}</option>
                            @endif
                        </select>
                </div>
        
                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3" style="margin-bottom : 0px;">
                    <div class="demo-checkbox">
                        <input type="radio" value="FISICA" id="check2" class="with-gap radio-col-deep-orange"
                            name="tipoDoc" required />
                        <label for="check2">Documento Fisico</label>
                    </div>
                </div>
            </div>
            <hr>
            <h5 class="form-control" style="color:#fff; background:#17a2b8;">Datos de Orden de Examen</h5><br>
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                    <div class="row clearfix form-horizontal">
                        <label for="idSucursal" class="col-sm-1 col-form-label">Sucursal :</label>
                        <div class="col-sm-3">
                            <select id="idSucursal" name="idSucursal" class="form-control select2" onchange="cargarOA();"  required>
                                    <option value="" label>--Seleccione una opcion--</option>                                                           
                                @foreach($sucursales as $sucursal)
                                    <option value="{{$sucursal->sucursal_id}}">{{$sucursal->sucursal_nombre}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 form-control-label ">
                            <label>NUMERO</label>
                        </div>

                        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1">
                            <div class="form-group">
                                <div class="form-line">
                                    <input id="punto_id" name="punto_id" value="{{ $rangoDocumento->puntoEmision->punto_id }}" type="hidden">
                                    <input id="rango_id" name="rango_id" value="{{ $rangoDocumento->rango_id }}"type="hidden">
                                   
                                    <input id="orden_id" name="orden_id" value="{{ $ordenAtencion->orden_id }}"type="hidden">
                                    <input type="text" id="Codigo" name="Codigo" value=""
                                        class="form-control derecha-texto negrita " placeholder="OE-" required readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                            <div class="form-group">
                                <div class="form-line">
                                    <input type="text" id="factura_numero" name="factura_numero"
                                        value="{{ $secuencial }}" class="form-control  negrita " placeholder="Numero"
                                        required readonly>
                                </div>
                            </div>
                        </div>
                    
                        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 form-control-label derecha-texto">
                            <label>TOTAL</label>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                            <div class="form-group">
                                <div class="form-line">
                                    <input type="text" id="idTotalFactura" name="idTotalFactura"
                                        class="form-control campo-total-global derecha-texto" placeholder="Total"
                                        disabled style="background-color: black" value="<?php echo number_format($total, 2) ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                    <label for="idPaciente" class="col-sm-1 col-form-label">Paciente : </label>
                    <div class="col-sm-7">
                        <input id="buscarPaciente" name="buscarPaciente" type="text" class="form-control" placeholder="Paciente" value="{{$paciente->paciente_apellidos}} {{$paciente->paciente_nombres}}" required readonly>
                        <input id="idPaciente" name="idPaciente" class="invisible" value="{{$paciente->paciente_id}}" placeholder="Paciente" >
                    </div>
                    <label for="idCedula" class="col-sm-1 col-form-label">Cédula :</label>
                    <div class="col-sm-2">
                        <input id="idCedula" type="text" class="form-control" value="{{$paciente->paciente_cedula}}" placeholder="9999999999" required readonly>
                    </div>
                
                    <div class="col-sm-1"><center><a href="paciente/create" class="btn btn-info btn-sm" target="_blank"><i class="fa fa-user"></i>&nbsp;Nuevo</a></center></div>
                    
                </div>
                <div class="form-group row">
                    <label for="idAseguradora" class="col-sm-1 col-form-label">Aseguradora:</label>
                    <div class="col-sm-3">
                        <input id="Aseguradora" name="Aseguradora" value="{{$paciente->cliente_nombre}}" type="text" class="form-control" required readonly>
                        <input id="idAseguradora" name="idAseguradora" type="hidden" value="{{$paciente->cliente_id}}" >
                    </div>
                    <label for="idEmpresa" class="col-sm-1 col-form-label">Empresa :</label>
                    <div class="col-sm-3">
                        <input id="identidad" name="identidad" type="hidden" value="{{$paciente->entidad_id}}">
                        <input id="idespecialidad" name="idespecialidad" type="hidden" value="{{$especialidad->especialidad_id}}">
                        <input id="idEmpresa" name="idEmpresa" type="text" value="{{$paciente->entidad_nombre}}" class="form-control" required readonly>
                    </div>
                    
                </div> 
                    <hr>
                    <div class="row ">
                        <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5" style="margin-bottom: 0px;">
                            <label>Analisis</label>
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
                            <label>Precio</label>
                            <div class="form-group">
                                <div class="form-line">
                                    <input id="id_Precio" name="id_Precio" type="text" class="form-control"
                                        placeholder="Precio" value="0.00" disabled>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1" style="margin-bottom: 0px;">
                            <label>% Cobertura</label>
                            <div class="form-group">
                                <div class="form-line">
                                <input id="id_por_Cober" name="id_por_Cober" type="text" class="form-control"
                                        placeholder="% Cobertura" value="0.00" disabled>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1" style="margin-bottom: 0px;">
                            <label>Cobertura</label>
                            <div class="form-group">
                                <div class="form-line">
                                    <input id="id_Cobertura" name="id_Cobertura" type="text" class="form-control"
                                        placeholder="Cobertura" value="0.00" disabled>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1" style="margin-bottom: 0px;">
                            <label>Copagos</label>
                            <div class="form-group">
                                <div class="form-line">
                                    <input id="id_Copagos" name="id_Copagos" type="text" class="form-control"
                                        placeholder="Copagos" value="0.00" disabled>
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
                                @include ('admin.laboratorio.ordenesExamen.itemsfacturar')
                                <table id="cargarItemFactura"
                                    class="table table-striped table-hover boder-sar tabla-item-factura"
                                    style="margin-bottom: 6px;">
                                    <thead>
                                        <tr class="letra-blanca fondo-azul-claro">
                                            <th>Cantidad</th>
                                            <th>Codigo</th>
                                            <th>Analisis</th>
                                            <th>Precio</th>
                                            <th>% Cobertura</th>
                                            <th>Cobertura</th>
                                            <th>Copago</th>
                                            
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $count = 1; 
                                            
                                        ?>
                                        @if(isset($datos))
                                            @for ($i = 1; $i <= count($datos); ++$i)  
                                                <tr id="row_{{ $count }}">
                                                    <td>1<input class="invisible" name="Dcantidad[]" value="{{ $datos[$i]['cantidad'] }}" /></td>
                                                    <td>{{ $datos[$i]['codigo'] }}<input class="invisible" name="DprodcutoID[]" value="{{ $datos[$i]['idproducto'] }}" /><input class="invisible" name="Dcodigo[]" value="{{$datos[$i]['codigo']}}" /></td>
                                                    <td>{{ $datos[$i]['nombre']  }}<input class="invisible" name="Dnombre[]" value="{{ $datos[$i]['nombre']  }}" /></td>
                                                    <td><?php echo '$' . number_format($datos[$i]['valor'], 2) ?><input class="invisible" name="Dvalor[]" value="{{ $datos[$i]['valor'] }}" /></td>
                                                    <td>{{ $datos[$i]['%Cobertura'] }}<input class="invisible" name="D%Cobertura[]" value="{{ $datos[$i]['%Cobertura'] }}"/></td>
                                                    <td><?php echo '$' . number_format($datos[$i]['Cobertura'], 2) ?><input class="invisible" name="DCobertura[]" value="{{ $datos[$i]['Cobertura'] }}" /></td>
                                                    <td><?php echo '$' . number_format($datos[$i]['Copago'], 2) ?><input class="invisible" name="DCopago[]" value="{{$datos[$i]['Copago']}}" /></td>
                                                
                                                    <td><a onclick="eliminarItem({{$count}},{{$datos[$i]['Copago']}});" class="btn btn-danger waves-effect" style="padding: 2px 8px;">X</a></td>
                                                </tr>
                                            
                                                <?php $count = $count + 1; ?>
                                            @endfor
                                        @endif
                                    
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
                                                            <textarea id="factura_comentario" name="factura_comentario"
                                                                rows=3 class="form-control "
                                                                placeholder="Escribir aqui..">Orden de Atencion No. {{ $ordenAtencion->orden_numero }}</textarea>
                                                            <input type="hidden" id="otros" name="otros" value="{{$ordenAtencion->orden_otros}}"/>
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
                                    <td class="letra-blanca fondo-azul-claro negrita">Total</td>
                                    <td id="total" class="derecha-texto negrita"><?php echo '$' . number_format($total, 2) ?></td>
                                    <input id="idTotal" name="idTotal" type="hidden" value="{{$total}}"/>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

@section('scriptAjax')
<script src="{{ asset('admin/js/ajax/autocompleteCliente.js') }}"></script>
<script src="{{ asset('admin/js/ajax/autocompleteAnalisiscopago.js') }}"></script>
@endsection
<script type="text/javascript">
id_item = '<?=$count?>';
id_item = Number(id_item);


    function agregarItem() {
        paso=true

        if(document.getElementById("codigoProducto").value==""){
            alert('Busque un examen para agregarlo en la lista')
            paso=false
        }
        else{
            var filas = $("#cargarItemFactura tbody tr").length;

            for(i=1; i<=filas; i++){
                celda= jQuery("#row_"+i).find("td:eq(1)");
                
                id= celda.children().eq(0).val()

                if(id==document.getElementById("idProductoID").value){
                    alert("Este item ya esta en la Lista")
                    return false
                }
                else if(parseFloat(document.getElementById("id_Precio").value) <=0){
                    alert("Este item no está aún configurado, debe tener un Precio establecido")
                    return false
                }
            }
        }
            
        if(paso){
            total = Number(document.getElementById("id_Copagos").value);
           
            var linea = $("#plantillaItemFactura").html();
            linea = linea.replace(/{ID}/g, id_item);
            linea = linea.replace(/{Dcantidad}/g,1);
            linea = linea.replace(/{Dcodigo}/g, document.getElementById("codigoProducto").value);
            linea = linea.replace(/{DprodcutoID}/g, document.getElementById("idProductoID").value);
            linea = linea.replace(/{Dnombre}/g, document.getElementById("buscarProducto").value);
          
            linea = linea.replace(/{valor}/g, document.getElementById("id_Precio").value);
           
            linea = linea.replace(/{%Cobertura}/g, document.getElementById("id_por_Cober").value);
            linea = linea.replace(/{Cobertura}/g, document.getElementById("id_Cobertura").value);
            linea = linea.replace(/{Copago}/g, document.getElementById("id_Copagos").value);
            $("#cargarItemFactura tbody").append(linea);
            id_item = id_item + 1;
            cargarTotales(total);
            resetearCampos();
        }
        
    }

    function eliminarTodo(){

    }

    function cargarOA(){  
        $.ajax({
            url: '{{ url("sucursales/searchN") }}'+ '/' +document.getElementById("idSucursal").value,
            dataType: "json",
            type: "GET",
            data: {
                buscar: document.getElementById("idSucursal").value
            },                      
            success: function(data){    
                for (var i = 0; i < data.length; i++) {                    
                    if(data[i].sucursal_id > 0){                 
                        document.getElementById("Codigo").value = 'OE-' + data[i].sucursal_nombre.replace(/\s+/g, ''); // replace() sirve para quitar los espacios
                    }else{
                        document.getElementById("Codigo").value = 'OE-';
                    }                        
                }
            },
            error: function(data) {
                document.getElementById("Codigo").value = 'OE-';
            },
        });         
    }  
    function cargarTotales (total) {
        var Ttotal=  Number(document.getElementById("idTotal").value);
        Ttotal=total+Ttotal;
        document.getElementById("total").innerHTML = '$ '+(Number(Ttotal)).toFixed(2);
        document.getElementById("idTotal").value = Ttotal;
        document.getElementById("idTotalFactura").value = (Number(Ttotal)).toFixed(2);
    }

    function calculatotales () {
        var total=  Number(document.getElementById("id_Precio").value);
        var cober=  Number(document.getElementById("id_Cobertura").value);
        Ttotal=cober+total;
        document.getElementById("id_Copagos").value = (Number(Ttotal)).toFixed(2);
        //cargarTotales (Ttotal)
        
    }

    function eliminarItem(id, total) {
        cargarTotales(total * (-1));
        $("#row_" + id).remove();

    }

    function resetearCampos() {
        document.getElementById("codigoProducto").value = "";
        document.getElementById("idProductoID").value = "";
        document.getElementById("buscarProducto").value = "";
        document.getElementById("id_Precio").value = "0.00";
        document.getElementById("id_por_Cober").value = "0";
        document.getElementById("id_Cobertura").value = "0.00";
        document.getElementById("id_Copagos").value = "0.00";
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