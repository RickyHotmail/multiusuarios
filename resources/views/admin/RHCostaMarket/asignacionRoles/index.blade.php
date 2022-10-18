@extends ('admin.layouts.admin')
@section('principal')
<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="card card-primary card-outline">
    <form class="form-horizontal" method="POST"  action="{{ url("asignacionRol") }}" >
        @csrf
        <div class="card-header">
            <div class="row">
                <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5">
                    <h2 class="card-title"><b>Registro de Ingresos y Egresos Roles de Empleados</b></h2>
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
                    <div class="form-group row">
                        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 form-control-label  "
                            style="margin-bottom : 0px;">
                            <label >Mes y año</label>
                        </div>
                        <div class="col-lg-3 col-md-2 col-sm-3 col-xs-3" style="margin-bottom : 0px;">
                            <input type="month" name="fechames" id="fechames" class="form-control" value='<?php echo(date("Y")."-".date("m")); ?>' onchange="cargarrubro();">
                            
                        </div>

                        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 form-control-label  "
                            style="margin-bottom : 0px;">
                            <label >Sucursal</label>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3" style="margin-bottom : 0px;">
                                <select class="custom-select" id="sucursal" name="sucursal" onchange="cargarrubro();" required>
                                        <option value="0" disabled selected>--Seleccione una opcion--</option>
                                    @foreach($sucursales as $sucursal)
                                        <option value="{{$sucursal->sucursal_id}}">{{$sucursal->sucursal_nombre}}</option>  
                                    @endforeach
                                </select>
                            
                        </div>
                       
                    </div>  
                    <div class="form-group row">
                        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 form-control-label  "
                            style="margin-bottom : 0px;">
                            <label >Tipo de Rubro</label>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3" style="margin-bottom : 0px;">
                                <select class="custom-select" id="rubro_tipo" name="rubro_tipo" onchange="cargarrubro();" required>
                                    <option value="0" disabled selected>--Seleccione una opcion--</option>
                                    <option value="2">INGRESOS</option>
                                    <option value="1">EGRESOS</option>
                                </select>
                            
                        </div>
                        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 form-control-label" style="margin-bottom : 0px;">
                            <label>Rubro:</label>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3" style="margin-bottom : 0px;">
                            <div class="form-group">
                                <select class="custom-select" id="rubro_id" name="rubro_id" onchange="cargarempleado();" required>
                                    <option value="0" disabled selected>--Seleccione una opcion--</option>
                   
                                </select>
                            </div>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" style="margin-bottom: 0px;">
                        <div class="card-body table-responsive p-0" style="height: 450px;">
                            
                            @include ('admin.RHCostaMarket.asignacionRoles.items')           
                           
                            <table id="tablaalimentacion"
                                class="table table-striped table-hover boder-sar tabla-item-factura"
                                style="margin-bottom: 6px;">
                                    <thead>
                                        <tr class="letra-blanca fondo-azul-claro">
                                            <th width="40"></th>
                                            <th width="90">Cedula</th>
                                            <th width="90">Empleado</th>
                                            <th width="120">Valor</th>  
                                            <th width="40">Estado</th>  
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                    
                   
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
<!-- /.card -->
@section('scriptAjax')


@endsection
<script type="text/javascript">
var id_item = 1;

    function cargarrubro() {
        $("#tablaalimentacion > tbody").empty();
        $.ajax({
            url: '{{ url("rubro/searchN") }}'+ '/' + document.getElementById("rubro_tipo").value,
            dataType: "json",
            async: false,
            type: "GET",
            data: {
                buscar: document.getElementById("rubro_tipo").value
            },
            success: function(data) {
                document.getElementById("rubro_id").innerHTML = "";
                document.getElementById("rubro_id").innerHTML = "<option value=''  disabled selected>--Seleccione una opcion--</option>";   
                for (var i = 0; i < data.length; i++) {
                    document.getElementById("rubro_id").innerHTML += "<option value='"+data[i].rubro_id+"'>"+data[i].rubro_descripcion+"</option>";
                }
            },
        });
    }
    function cargarempleado(){ 
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            }
        });
        $("#tablaalimentacion > tbody").empty();
    $.ajax({
        url: '{{ url("empleadosrubro/searchN") }}',
        dataType: "json",
        async: false,
        type: "POST",
        data: {
            buscar: document.getElementById("rubro_id").value,
            fecha: document.getElementById("fechames").value,
            sucursal: document.getElementById("sucursal").value
        },
        success: function(data){
            document.getElementById("guardarID").disabled = false;	
            for (var i=0; i<data.length; i++) {
                var linea = $("#plantillaItemAlimentacion").html();
                    linea = linea.replace(/{ID}/g, id_item);
                    linea = linea.replace(/{DIDE}/g, data[i]["ide"]);
                    linea = linea.replace(/{DCedula}/g, data[i]["cedula"]);
                    linea = linea.replace(/{DNombre}/g, data[i]["nombre"]);
                    linea = linea.replace(/{nombre}/g, data[i]["nombre"]);
                    linea = linea.replace(/{valor}/g, data[i]["valor"]);
                    linea = linea.replace(/{idalimento}/g, data[i]["idalim"]);
                    linea = linea.replace(/{idrol}/g, data[i]["idrol"]);
                    if((data[i]["idrol"])!='0'){	
                        linea = linea.replace(/{rol}/g, '<span class="badge bg-danger"> Asignado </span>');
                        linea = linea.replace(/{editable}/g, 'readonly');
                    }
                    if((data[i]["idrol"])=='0'){
                    linea = linea.replace(/{rol}/g, '<span class="badge bg-success">Generado</span>');
                    linea = linea.replace(/{editable}/g, ' ');
                    }
                    
                    $("#tablaalimentacion tbody").append(linea);
                    id_item= id_item+1;
            } 
                
            id_item = 1;  
          
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