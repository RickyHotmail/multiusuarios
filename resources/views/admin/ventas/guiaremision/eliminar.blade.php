
@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-primary card-outline">
    <form class="form-horizontal" method="POST" action="{{ route('listaGuias.destroy', [$guias->gr_id]) }}">
        @method('DELETE')
        @csrf
        <div class="card-header">
            <div class="row">
                <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5">
                    <h2 class="card-title"><b>Guias de Remision</b></h2>
                </div>
                <div class="col-xs-7 col-sm-7 col-md-7 col-lg-7">
                    <div class="float-right">
                            <button id="guardarID" type="submit" class="btn btn-success btn-sm" ><i
                                    class="fa fa-save"></i><span> Eliminar</span></button>
                            <!--
                            <a href="{{ url("listaGuias") }}" class="btn btn-danger btn-sm not-active-neo"><i
                                class="fas fa-times-circle"></i><span> Cancelar</span></a> 
                            -->      
                            <button  type="button" onclick="history.back()" class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
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
                                    

                                    <input id="proforma_id" name="proforma_id" value="{{ $guias->gr_id }}" type="hidden">
                                    
                                    <input id="proforma_estado" name="proforma_estado" value="{{ $guias->gr_estado }}" type="hidden">
                                   
                                    <label class="form-control" id="proforma_serie" name="proforma_serie">{{$guias->gr_serie}}</label>
                                   
                                  
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                            <div class="form-group">
                                <div class="form-line">
                                   
                                    <label class="form-control" id="guia_numero" name="guia_numero"  value="{{substr(str_repeat(0, 9). $guias->gr_numero , - 9)}}">{{substr(str_repeat(0, 9). $guias->gr_numero , - 9)}}</label>
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
                                    <label class="form-control" id="guia_fecha" name="guia_fecha"  value="{{$guias->gr_fecha}}">{{$guias->gr_fecha}}</label>   
                                </div>
                            </div>
                        </div>
                       
                    </div>
                    <div class="row clearfix form-horizontal">
                        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2 form-control-label "
                            style="margin-bottom : 0px;">
                            <label>CLIENTE :</label>
                        </div>
                        <div class="col-lg-7 col-md-7 col-sm-7 col-xs-7" style="margin-bottom : 0px;">
                            <div class="form-group">
                                <input id="clienteID" name="clienteID" type="hidden">
                                <label class="form-control" id="buscarCliente" name="buscarCliente"  value="{{$guias->cliente->cliente_nombre}}">{{$guias->cliente->cliente_nombre}}</label>     
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3" style="margin-bottom : 0px;">
                            @if($guias->gr_emision =='ELECTRONICA')
                                <label id="buscarCliente" name="buscarCliente"  value="{{$guias->gr_emision}}">Tipo de Documento Electronico</label> 
                            @else
                                <label class="form-control" id="buscarCliente" name="buscarCliente"  value="{{$guias->gr_emision}}">Tipo de Documento Fisico</label>
                            @endif
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
                                <label class="form-control" id="idCedula" name="idCedula"  value="{{$guias->cliente->cliente_cedula}}">{{$guias->cliente->cliente_cedula}}</label>           
                                </div>
                            </div>
                        </div>           
                    </div>                  
                    <div class="row clearfix form-horizontal">
                        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2 form-control-label ">
                            <label>TRANSPORTISTAS :</label>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3" style="margin-bottom : 0px;">
                            <div class="form-group">                
                                <label class="form-control" id="transportistas" name="transportistas"  value="{{$guias->Transportista->transportista_nombre}}">{{$guias->Transportista->transportista_nombre}}</label> 
                            </div>
                        </div>  
                        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2 form-control-label"
                            style="margin-bottom : 0px;">
                            <label>FECHA DE TRASLADO :</label>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2" style="margin-bottom : 0px;">
                            <div class="form-group">
                                <div class="form-line">
                                    <label class="form-control" id="traslado_fecha" name="traslado_fecha"  value="{{$guias->gr_fecha_inicio}}">{{$guias->gr_fecha_inicio}}</label>
                                
                                    
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
                                    <label class="form-control" id="idRucT" name="idRucT"  value="{{$guias->Transportista->transportista_cedula}}">{{$guias->Transportista->transportista_cedula}}</label>
                                
                                   
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
                                    <label class="form-control" id="traslado_fecha_fin" name="traslado_fecha_fin"  value="{{$guias->gr_fecha_fin}}">{{$guias->gr_fecha_fin}}</label>
                                    
                                   
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3 alinear-izquierda" style="margin-bottom : 0px;">
                            <div class="form-group">
                                <label class="form-control" id="bodega_id" name="bodega_id"  value="{{$guias->bodega->bodega_nombre}}">{{$guias->bodega->bodega_nombre}}</label>
                                
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
                                    <label class="form-control" id="guia_placa" name="guia_placa"  value="{{$guias->gr_placa}}">{{$guias->gr_placa}}</label>
                                
                                   
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
                                    <label class="form-control" id="partida" name="partida"  value="{{$guias->gr_punto_partida}}">{{$guias->gr_punto_partida}}</label>
                                
                                    
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
                                    <label class="form-control" id="aduana" name="aduana"  value="{{$guias->gr_doc_aduanero}}">{{$guias->gr_doc_aduanero}}</label>
                                
                                    
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
                                    <label class="form-control" id="llegada" name="llegada"  value="{{$guias->gr_punto_destino}}">{{$guias->gr_punto_destino}}</label>
                                    
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
                                    <label class="form-control" id="motivo" name="motivo"  value="{{$guias->gr_motivo}}">{{$guias->gr_motivo}}</label>
                                 
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row" >
                        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6" style="margin-bottom: 0px;">
                            
                            <div class="form-group">
                                <div class="form-line">
                                    <input id="codigoProducto" name="idProducto" type="hidden">
                                    <input id="idProductoID" name="idProductoID" type="hidden">
                                    <input id="buscarProducto" name="buscarProducto" type="hidden" class="form-control"
                                        placeholder="Buscar producto" disabled>
                                    <span id="errorStock" class="text-danger invisible">El producto no tiene stock
                                        disponible.</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1" style="margin-bottom: 0px;">
                            
                            <div class="form-group">
                                <div class="form-line">
                                    <input id="id_disponible" name="id_disponible" type="hidden" class="form-control"
                                        placeholder="Disponible" value="0" disabled>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1" style="margin-bottom: 0px;">
                            
                            <div class="form-group">
                                <div class="form-line">
                                    <input onchange="calcularTotal()" onkeyup="calcularTotal()" id="id_cantidad"
                                        name="id_cantidad" type="hidden" class="form-control" placeholder="Cantidad"
                                        value="1" disabled>
                                </div>
                            </div>
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
                                            <th  class="text-center">Cantidad</th>
                                            <th  class="text-center">Codigo</th>
                                            <th  class="text-center">Producto</th>
                                            
                                            <th width="40"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    @if(isset($guias->detalles))
                                        @foreach($guias->detalles as $x)
                                        <tr>
                                        <td class="text-center">{{ $x->detalle_cantidad}}</td>
                                        <td class="text-center">{{ $x->producto->producto_codigo}}</td>
                                   
                                        <td class="text-center">{{ $x->producto->producto_nombre}}</td>
                                        </tr>      
                                        @endforeach
                                    @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <br>
                  
                </div>
            </div>
        </div>
    </form>
</div>
<!-- /.card -->
@section('scriptAjax')
<script src="{{ asset('admin/js/ajax/autocompleteCliente.js') }}"></script>
<script src="{{ asset('admin/js/ajax/autocompleteProducto.js') }}"></script>
@endsection
<script type="text/javascript">
var id_item = 1;
document.getElementById("idTarifa0").value = 0;
document.getElementById("idTarifa12").value = 0;
var combo = document.getElementById("factura_porcentaje_iva");
var porcentajeIva = combo.options[combo.selectedIndex].text;
porcentajeIva = parseFloat(porcentajeIva) / 100;

function nuevo() {
    $('#factura_porcentaje_iva').css('pointer-events', 'none');
    $('#bodega_id').css('pointer-events', 'none');
    // document.getElementById("bodega_id").disabled  = true;
    document.getElementById("guardarID").disabled = false;
    document.getElementById("cancelarID").disabled = false;
   
    
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