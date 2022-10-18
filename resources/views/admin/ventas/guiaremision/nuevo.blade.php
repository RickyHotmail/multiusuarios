
@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-primary card-outline">
    <form class="form-horizontal" method="POST" action="{{ url("guiaremision") }}">
        @csrf
        <div class="card-header">
            <div class="row">
                <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5">
                    <h2 class="card-title"><b>Nueva Guia de Remision</b></h2>
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
                        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2 form-control-label ">
                            <label>NUMERO</label>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                            <div class="form-group">
                                <div class="form-line">
                                    <input id="punto_id" name="punto_id"
                                        value="{{ $rangoDocumento->puntoEmision->punto_id }}" type="hidden">
                                    <input id="rango_id" name="rango_id" value="{{ $rangoDocumento->rango_id }}"
                                        type="hidden">
                                    <input type="text" id="guia_serie" name="guia_serie"
                                        value="{{ $rangoDocumento->puntoEmision->sucursal->sucursal_codigo }}{{ $rangoDocumento->puntoEmision->punto_serie }}"
                                        class="form-control derecha-texto negrita " placeholder="Serie" required readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                            <div class="form-group">
                                <div class="form-line">
                                    <input type="text" id="guia_numero" name="guia_numero"
                                        value="{{ $secuencial }}" class="form-control  negrita " placeholder="Numero"
                                        required readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 form-control-label" style="padding-left: 55px;">
                        </div>
                        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 form-control-label derecha-texto "
                            style="margin-bottom : 0px;">
                            <label>FECHA :</label>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2" style="margin-bottom : 0px;">
                            <div class="form-group">
                                <div class="form-line">
                                    <input type="date" id="guia_fecha" name="guia_fecha" class="form-control "
                                        placeholder="Seleccione una fecha..."
                                        value='<?php echo(date("Y")."-".date("m")."-".date("d")); ?>' required />
                                </div>
                            </div>
                        </div>
                       
                    </div>
                    <div class="row clearfix form-horizontal">
                        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2 form-control-label "
                            style="margin-bottom : 0px;">
                            <label>CLIENTE :</label>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6" style="margin-bottom : 0px;">
                            <div class="form-group">
                                <input id="clienteID" name="clienteID" type="hidden">
                                <input id="buscarCliente" name="buscarCliente" type="text" class="form-control "
                                    placeholder="Cliente" required disabled>
                            </div>
                        </div>
                        <div class="col-sm-1"><center><a href="{{ url("cliente/create") }}" class="btn btn-info btn-sm" target="_blank"><i class="fa fa-user"></i>&nbsp;Nuevo</a></center></div>
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3" style="margin-bottom : 0px;">
                            <div class="demo-checkbox">
                                <input type="radio" value="ELECTRONICA" id="check1"
                                    class="with-gap radio-col-deep-orange" name="tipoDoc" checked  onchange="documentoFisico();" required />
                                <label for="check1">Documento Electronico</label>
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
                                    <input type="text" id="idCedula" name="idCedula" class="form-control "
                                        placeholder="Ruc" disabled required>
                                </div>
                            </div>
                        </div> 
                       
                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 form-control-label "
                            style="margin-bottom : 0px;">
                          
                        </div>
                       
                        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2" style="margin-bottom : 0px;">
                            <div class="demo-checkbox">
                                <input type="radio" value="FISICA" id="check2" class="with-gap radio-col-deep-orange"
                                    name="tipoDoc" onchange="documentoFisico();" required />
                                <label for="check2">Documento Fisico</label>
                            </div>
                        </div>         
                    </div>                  
                    <div class="row clearfix form-horizontal">
                        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2 form-control-label ">
                            <label>TRANSPORTISTAS :</label>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3" style="margin-bottom : 0px;">
                            <div class="form-group">
                                <select class="form-control select2" id="transportistas" name="transportistas"
                                    data-live-search="true" onchange="extraertransportista()" disabled required>
                                    <option value="" label>--Seleccione una opcion--</option>
                                    @foreach($trasportistas as $transportista)
                                    <option value="{{$transportista->transportista_id}}">{{$transportista->transportista_nombre}}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>  
                        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2 form-control-label"
                            style="margin-bottom : 0px;">
                            <label>FECHA INICIO DE TRASLADO :</label>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2" style="margin-bottom : 0px;">
                            <div class="form-group">
                                <div class="form-line">
                                    <input type="date" id="traslado_fecha" name="traslado_fecha" class="form-control "
                                        placeholder="Seleccione una fecha..."
                                        value='<?php echo(date("Y")."-".date("m")."-".date("d")); ?>' disabled required />
                                </div>
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
                        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2 form-control-label ">
                                <label>CI / RUC :</label>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3" style="margin-bottom : 0px;">
                            <div class="form-group">
                                <div class="form-line">
                                    <input type="text" id="idRucT" name="idRucT" class="form-control "
                                        placeholder="Ruc" disabled required>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2 form-control-label "
                            style="margin-bottom : 0px;">
                            <label>FECHA FIN DE TRASLADO :</label>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2" style="margin-bottom : 0px;">
                            <div class="form-group">
                                <div class="form-line">
                                    <input type="date" id="traslado_fecha_fin" name="traslado_fecha_fin" class="form-control "
                                        placeholder="Seleccione una fecha..."
                                        value='<?php echo(date("Y")."-".date("m")."-".date("d")); ?>' disabled required />
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3 alinear-izquierda" style="margin-bottom : 0px;">
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
                        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2 form-control-label  "
                            style="margin-bottom : 0px;">
                            <label>PLACA :</label>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3" style="margin-bottom : 0px;">
                            <div class="form-group">
                                <div class="form-line">
                                    <input type="text" id="guia_placa" name="guia_placa" class="form-control "
                                    placeholder="Placa" disabled required>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2 form-control-label  "
                            style="margin-bottom : 0px;">
                            <label>PUNTO DE PARTIDA :</label>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2" style="margin-bottom : 0px;">
                            <div class="form-group">
                                <div class="form-line">
                                    <input type="text" id="partida" name="partida" class="form-control "
                                        value="MACHALA" placeholder="Lugar" disabled required>
                                </div>
                            </div>
                        </div>
                    
                        
                    </div>
                    
                    <div class="row clearfix form-horizontal">
                        
                        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2 form-control-label  "
                            style="margin-bottom : 0px;">
                            <label>DECLARACION ADUANA :</label>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3" style="margin-bottom : 0px;">
                            <div class="form-group">
                                <div class="form-line">
                                    <input type="text" id="aduana" name="aduana" class="form-control" disabled>
                                </div>
                            </div>
                        </div>
                       
                        
                        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2 form-control-label  "
                            style="margin-bottom : 0px;">
                            <label>PUNTO DE LLEGADA :</label>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2" style="margin-bottom : 0px;">
                            <div class="form-group">
                                <div class="form-line">
                                    <input type="text" id="llegada" name="llegada" class="form-control "
                                        value="MACHALA" placeholder="Lugar" disabled required>
                                </div>
                            </div>
                        </div>
                   
                    </div>
                    <div class="row clearfix form-horizontal">
                        
                    <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2 form-control-label  "
                            style="margin-bottom : 0px;">
                            <label>MOTIVO:</label>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3" style="margin-bottom : 0px;">
                            <div class="form-group">
                                <div class="form-line">
                                    <input type="text" id="motivo" name="motivo" class="form-control "
                                        value="Despacho de mercaderia" placeholder="Motivo" disabled required>
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
                                        placeholder="Buscar producto" disabled>
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
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2 form-control-label" style="margin-bottom : 0px;">
                            <label>Comentario:</label>
                        </div>
                        <div class="col-lg-10 col-md-10 col-sm-10 col-xs-10"
                            style="margin-bottom : 0px;">
                            <div class="form-group">
                                <div class="form-line">
                                    <textarea id="gr_comentario" name="gr_comentario"
                                        rows=3 class="form-control "
                                        placeholder="Escribir aqui.." maxlength="300"></textarea>
                                </div>
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
<script src="{{ asset('admin/js/ajax/autocompleteCliente.js') }}"></script>
<script src="{{ asset('admin/js/ajax/autocompleteProductoGuia.js') }}"></script>
@endsection
<script type="text/javascript">
var id_item = 1;

function nuevo() {
    $('#bodega_id').css('pointer-events', 'none');
    // document.getElementById("bodega_id").disabled  = true;
    document.getElementById("guardarID").disabled = false;
    document.getElementById("cancelarID").disabled = false;
    document.getElementById("nuevoID").disabled = true;
    
    document.getElementById("buscarProducto").disabled = false;
    document.getElementById("transportistas").disabled = false;
    document.getElementById("traslado_fecha").disabled = false;
    document.getElementById("traslado_fecha_fin").disabled = false;

    document.getElementById("partida").disabled = false;
    document.getElementById("aduana").disabled = false;


    document.getElementById("llegada").disabled = false;
    document.getElementById("motivo").disabled = false;

    //document.getElementById("factura_porcentaje_iva").disabled  = true;
    document.getElementById("buscarCliente").disabled = false;
}

function agregarItem() {
   
    if (document.getElementById("nuevoID").disabled) {
      
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
}


function extraertransportista(){  
  
        $.ajax({
           
            url: '{{ url("guiaRemision/searchN") }}'+'/'+document.getElementById("transportistas").value,
            dataType: "json",
            type: "GET",
            data: {
                buscar: document.getElementById("transportistas").value
            },                      
            success: function(data){    
                for (var i = 0; i < data.length; i++) {                                             
                     
                        document.getElementById("guia_placa").value = data[i].transportista_placa; 
                        document.getElementById("idRucT").value = data[i].transportista_cedula; 
                                         
                }
            },
            error: function(data) {
               alert('vuelva a elegir el transportista')
            },
        });         
    
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
function eliminarTodo() {
    for(i = 1; i < id_item; i++){
        $("#row_" + i).remove();
    }
    resetearCampos();
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
function documentoFisico(){
    if(document.getElementById("check2").checked){
        document.getElementById("guia_serie").readOnly = false;
        document.getElementById("guia_numero").readOnly = false;
    }else{
        document.getElementById("guia_serie").readOnly = true;
        document.getElementById("guia_numero").readOnly = true;
    }  
}
</script>
@endsection