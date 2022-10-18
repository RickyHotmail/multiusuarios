@extends ('admin.layouts.admin')
@section('principal')
<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="card card-primary card-outline">
    <form class="form-horizontal" method="POST"  action="{{ url("alimentacion") }} " onsubmit="return validarForm();">
        @csrf
        <div class="card-header">
            <div class="row">
                <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5">
                    <h2 class="card-title"><b>Registrar Alimentacion</b></h2>
                </div>
                <div class="col-xs-7 col-sm-7 col-md-7 col-lg-7">
                    <div class="float-right">
                        <button id="guardarID" type="submit" class="btn btn-success btn-sm" disabled><i
                                class="fa fa-save"></i><span> Guardar</span></button>
                        <button type="button" id="cancelarID" name="cancelarID" onclick="javascript:location.reload()"
                            class="btn btn-danger btn-sm not-active-neo" ><i
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
                   
                    
                  
                        <div class="col-lg-5 col-md-5 col-sm-5 col-xs-5 form-control-label  "
                            style="margin-bottom : 0px;">
                           
                        </div>
                       
                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                            <div class="form-group">
                                <center><label>CONSULTA</label></center>
                            </div>
                        </div>
                        
                    </div>
                    
                    <div class="row clearfix form-horizontal">
                      
                        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 form-control-label  "
                            style="margin-bottom : 0px;">
                            <label>PROVEEDOR</label>
                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-5" style="margin-bottom : 0px;">
                            <select class="custom-select select2" id="proveedor_id" name="proveedor_id" onchange="cargarProveedor();" required>
                                <option value="" label>--Seleccione una opcion--</option>
                                @foreach($proveedor as $proveedores)
                                <option value="{{$proveedores->proveedor_id}}">{{$proveedores->proveedor_nombre}}</option>
                                @endforeach
                            </select>
                            
                        </div>
                       
                       
                        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 form-control-label  "
                            style="margin-bottom : 0px;">
                            <label>No Factura :</label>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3" style="margin-bottom : 0px;">
                            <div class="form-group">
                                <input id="transaccion_id" name="transaccion_id" type="hidden">
                                <input type="text" id="buscartransaccion" name="buscartransaccion" class="form-control"
                                        />
                            </div>
                        </div>
                    </div>
                    <div class="row clearfix form-horizontal">
                        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 form-control-label  "
                            style="margin-bottom : 0px;">
                            <CENTER><label>RUC/CI</label></CENTER>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2" style="margin-bottom : 0px;">
                            <div class="form-group">
                                <input class="form-control" id="idRUC" name="idRUC" value="" disabled/>
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                        </div>  
                        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 form-control-label  "
                                    style="margin-bottom : 0px;">
                            <label>FECHA :</label>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2" style="margin-bottom : 0px;">
                            <div class="form-group">
                                <div class="form-line">
                                    <label class="form-control" id="ltransaccion_fecha" ></label>
                                    <input type="hidden" id="transaccion_fecha" name="transaccion_fecha"
                                        class="form-control " placeholder="Seleccione una fecha..."
                                        value='' 
                                         />
                                </div>
                            </div>
                        </div> 
                    </div>
                 
                
                    <br>
                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" style="margin-bottom: 0px;">
                        <div class="card-body table-responsive p-0" style="height: 450px;">
                            
                            @include ('admin.recursosHumanos.alimentacion.item')           
                           
                            <table id="tablaalimentacion"
                                class="table table-striped table-hover boder-sar tabla-item-factura"
                                style="margin-bottom: 6px;">
                                    <thead>
                                        <tr class="letra-blanca fondo-azul-claro">
                                            <th width="40"></th>
                                            <th width="90">Cedula</th>
                                            <th width="90">Empleado</th>
                                            <th width="120">Valor</th>
                                            <th width="120">Estado</th>  
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                    <div class="col-xs-9 col-sm-9 col-md-9 col-lg-9" style="margin-bottom: 0px;">
                    </div>
                    <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
                        <table class="table table-totalVenta">
                            <tr>
                                <td class="letra-blanca fondo-azul-claro negrita" width="90">Valor Alimentación
                                </td>
                                <td id="talimentacion" width="100" class="derecha-texto negrita">0.00</td>
                                <input id="tvalimentacion" name="tvalimentacion" type="hidden" />
                            </tr>
                            <tr>
                                <td class="letra-blanca fondo-azul-claro negrita">Valor Factura</td>
                                <td id="tfactura" class="derecha-texto negrita">0.00</td>
                                <input id="tvfactura" name="tvfactura" type="hidden" />
                                <input id="saldo_transaccion" name="saldo_transaccion" type="hidden" />
                            </tr>
                            
                        </table>
                    </div>
                  
                </div>
            </div>
        </div>
    </form>
</div>
<!-- /.card -->
@section('scriptAjax')
<script src="{{ asset('admin/js/ajax/autocompleteAlimentacion.js') }}"></script>

@endsection
<script type="text/javascript">
var id_item = 1;

    function cargarProveedor() {
        $.ajax({
            url: '{{ url("proveedores/searchN") }}'+ '/' + document.getElementById("proveedor_id").value,
            dataType: "json",
            type: "GET",
            data: {
                buscar: document.getElementById("proveedor_id").value
            },
            success: function(data) {
              
                for (var i = 0; i < data.length; i++) {
                    document.getElementById("idRUC").value = data[i].proveedor_ruc;
                }
            },
        });
    }
    function cargarempleado(id){ 
        document.getElementById("talimentacion").innerHTML =0.00;	   
        document.getElementById("tvalimentacion").value = 0.00;	   
    $.ajax({
        url: '{{ url("empleadosalimentos/searchN") }}'+'/'+id,
        dataType: "json",
        type: "GET",
        data: {
           
        },
        success: function(data){
            var total=0;;
            for (var i=0; i<data.length; i++) {
                
                var linea = $("#plantillaItemAlimentacion").html();
                    linea = linea.replace(/{ID}/g, id_item);
                    linea = linea.replace(/{DIDE}/g, data[i]["ide"]);
                    linea = linea.replace(/{DCedula}/g, data[i]["cedula"]);
                    linea = linea.replace(/{DNombre}/g, data[i]["nombre"]);
                    linea = linea.replace(/{nombre}/g, data[i]["nombre"]);
                    linea = linea.replace(/{valor}/g, data[i]["valor"]);
                    linea = linea.replace(/{idalimento}/g, data[i]["idalim"]);
                   
                    total=total+Number(data[i]["valor"]);
                    if (data[i]["rol"]!=null) {
                        linea = linea.replace(/{editable}/g, 'readonly');
                        linea = linea.replace(/{rol}/g, '<span class="badge bg-danger"> Asignado </span>');
                    }
                    else{
                        if(data[i]["rolcm"]!=null){
                            linea = linea.replace(/{editable}/g, 'readonly');
                            linea = linea.replace(/{rol}/g, '<span class="badge bg-danger"> Asignado </span>');
                        }
                        else{
                            linea = linea.replace(/{editable}/g, ' ');
                            linea = linea.replace(/{rol}/g, '<span class="badge bg-success">Generado</span>');
                        }
                    }
                    
                    $("#tablaalimentacion tbody").append(linea);
                    id_item= id_item+1;
            }       
            id_item = 1;  
            document.getElementById("talimentacion").innerHTML =Number(total).toFixed(2);		   
            document.getElementById("tvalimentacion").value = Number(total).toFixed(2);	   
        },
    });
    }

    function totalSeleccion(){
        var suma=0;
        for (let step = 1; step < document.getElementById("tablaalimentacion").rows.length; step++) {
            suma+=Number($("input[name='Valor[]']")[step].value);
        }
        document.getElementById("talimentacion").innerHTML =Number(suma).toFixed(2);
        document.getElementById("tvalimentacion").value =Number(suma).toFixed(2);
    }
    function validarForm(){
  
    
    if(Number(document.getElementById("talimentacion").innerHTML)>Number(document.getElementById("tfactura").innerHTML) ){
        alert('el total de la alimentacion no debe ser mayor que el total de la factura ');
        return false
    }
   
    return true;
}
</script>
@endsection