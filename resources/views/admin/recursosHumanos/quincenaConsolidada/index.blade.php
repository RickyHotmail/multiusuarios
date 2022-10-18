@extends ('admin.layouts.admin')
@section('principal')

<div class="card">
    <form class="form-horizontal" method="POST" action="{{ url("quincenaConsolidada") }} ">
    @csrf 
        <div class="card-header">
            <h3 class="card-title">Quincena Consolidada</h3>
            <div class="float-right">
                <button type="submit" id="generar" name="generar" class="btn btn-success btn-sm ">Generar Quincena</button>
                <a href="{{ url("quincenaConsolidada") }}" class="btn btn-danger btn-sm">Cancelar</a>
            </div>
        </div>
        <div class="card"> 
        
            <br>     
            <div class="form-group row">
                <label for="fecha" class="col-sm-1 col-form-label"><center>Fecha:</center></label>
                    <div class="col-sm-2">
                        <input type="date" class="form-control" id="fecha" name="fecha"  value='<?php echo(date("Y")."-".date("m")."-".date("d")); ?>' >
                        <input type="hidden"  id="fechaactual" name="fechaactual" value='<?php echo(date("Y")."-".date("m")."-".date("d")); ?>' >
                    </div>
                    <label for="idTipo" class="col-sm-2 col-form-label">Sucursal</label>
                    <div class="col-sm-10">
                        
                        <select class="custom-select" id="idTipo" name="idTipo"  >
                            <option value='' disabled>--Seleccione una opcion--</option>
                            @foreach($sucursales as $sucursal)
                                <option id="sucursal_{{$sucursal->sucursal_id}}" value="{{$sucursal->sucursal_id}}">{{$sucursal->sucursal_nombre}}</option>
                            @endforeach                            
                        </select>
                    </div>
                    <div class="col-sm-1">        
                    <button type="submit" id="extraer" name="extraer" class="btn btn-success float-right"><i class="fa fa-search"></i><span> Buscar Rol</span></button>                   
                    </div>
                  
                   
           
                <br>
            </div>
    
            <div class="card-body">
                <div class="table-responsive">
                    @include ('admin.recursosHumanos.rolPagoConsolidado.items')
                    <table id="example5" name="example5" class="table table-bordered table-hover table-responsive sin-salto">
                        <thead>
                            
                            <tr class="letra-blanca fondo-azul-claro">
                                <th></th>
                                <th></th>
                                <th  class="text-center-encabesado">Cédula </th>
                                <th class="text-center-encabesado">Nombre </th>
                            
                            

                                <th class="text-center-encabesado">Sueldo </th>
                                
                                <th class="text-center-encabesado">Quincena </th>
                                
                            </tr>
                        </thead>
                        <tbody>
                        @if(isset($datos))
                            @for ($i = 1; $i <= count($datos); ++$i)  
                            <tr>
                                <td> <input type="checkbox" name="checkbox[]" checked value="{{ $datos[$i]['ID'] }}">
                                    <input class="invisible" name="ID[]" value="{{ $datos[$i]['ID'] }}" />
                                </td>
                                <td> 
                                <a href="/" class="btn btn-xs btn-success" data-toggle="tooltip" data-placement="top" title="Ver"><i class="fa fa-print"></i></a>               
                                </td>
                                
                                <td width="150">{{ $datos[$i]['Dcedula'] }} <input class="invisible" name="Dcedula[]" value="{{ $datos[$i]['Dcedula'] }}" /></td>
                                <td width="150">{{ $datos[$i]['Dnombre'] }} <input class="invisible" name="Dnombre[]" value="{{ $datos[$i]['Dnombre'] }}" /></td>
                                
                                <td width="150"><input type="number" class="form-controltext"  name="DCsueldo[]"  value="{{ $datos[$i]['Dsueldo'] }}" required readonly><input class="invisible" name="Dsueldo[]" value="{{ $datos[$i]['Dsueldo'] }}" /></td>
                                <td width="150"> <input type="number" class="form-controltext"   name="quincena[]" value="{{ $datos[$i]['quincena'] }}" required >
                                </td>
                                
                            </tr>
                            @endfor
                        @endif   
                        </tbody>
                    </table>
                </div>
            </div>
        </div> 
      
        <h5 class="form-control" style="color:#fff; background:#17a2b8;">Forma de Pago</h5>   
        <div class="card-body">
            <div class="form-group row">
                <label for="idTipo" class="col-sm-2 col-form-label">Tipo de Pago</label>
                <div class="col-sm-10">
                    
                    <select class="custom-select" id="idTipo" name="idTipo"  onchange="cajaActivar();" >
                    <option value='' label>--Seleccione una opcion--</option>
                        <option value="Efectivo">Efectivo</option>
                        <option value="Cheque">Cheque</option> 
                        <option value="Transferencia">Transferencia</option>                                   
                    </select>
                </div>
            </div>
            
            <div class="form-group row">
                    <label for="banco_id" class="col-sm-2 col-form-label">Banco</label>
                    <div class="col-sm-10">
                        <select class="custom-select" id="banco_id" name="banco_id" onchange="cargarCuenta();" >
                            <option value="" label>--Seleccione una opcion--</option>
                            @foreach($bancos as $banco)
                                <option value="{{$banco->banco_id}}">{{$banco->bancoLista->banco_lista_nombre}}</option>
                            @endforeach
                        </select>
                    </div>
            </div>
            <div class="form-group row">
                    <label for="cuenta_id" class="col-sm-2 col-form-label"># de Cuenta</label>
                    <div class="col-sm-10">
                        
                        <select class="custom-select" id="cuenta_id" name="cuenta_id"  onchange="cargarContable();" >
                            
                        </select>
                        <input type="hidden" class="form-control" id="ncuenta" name="ncuenta" >
                    </div>
            </div> 
            <div class="form-group row">
                    <label for="idCuentaContable" class="col-sm-2 col-form-label">Cuenta Contable</label>
                    <div class="col-sm-10">
                        <select class="custom-select" id="idCuentaContable"  name="idCuentaContable" disabled >
                            <option value="--Seleccione una opcion--" label>--Seleccione una opcion--</option>                                   
                        </select>
                    </div>
            </div>             
        </div>  
        <h5 class="form-control" style="color:#fff; background:#17a2b8;">Datos del Cheque</h5>
        <div class="card-body">
            <div class="form-group row">
                        <label for="idFechaCheque" class="col-sm-2 col-form-label">Fecha</label>
                        <div class="col-sm-10">
                            <input type="date" class="form-control" id="idFechaCheque" name="idFechaCheque" value='<?php echo(date("Y")."-".date("m")."-".date("d")); ?>' >
                            
                        </div>
            </div>                
            <div class="form-group row">
                <label for="idNcheque" class="col-sm-2 col-form-label">Inicio # de Cheque</label>
                <div class="col-sm-10">
                    <input type="number" class="form-control" id="idNcheque" name="idNcheque" min="1">
                </div>
            </div>     
        </div>  
    </form>
</div>

    <!-- /.card-body -->



@endsection

<script type="text/javascript">

function cajaActivar(){
    document.getElementById("banco_id").innerHTML = "";
    cargarbanco();
    if(document.getElementById("idTipo").value=="Efectivo"){
        document.getElementById("banco_id").disabled = true;
        document.getElementById("cuenta_id").disabled = true;

        document.getElementById("idFechaCheque").disabled = true;
        document.getElementById("idNcheque").disabled = true;
      

        document.getElementById("idCuentaContable").disabled = false;
        document.getElementById("banco_id").value="";
        cargarCuentaCaja();
    }
    if(document.getElementById("idTipo").value=="Cheque"){

        document.getElementById("banco_id").disabled = false;
        document.getElementById("cuenta_id").disabled = false;
        document.getElementById("idFechaCheque").disabled = false;
        document.getElementById("idNcheque").disabled = false;
       
        document.getElementById("idCuentaContable").disabled = false;
        document.getElementById("idCuentaContable").innerHTML = "";         
       
    }
    if(document.getElementById("idTipo").value=="Transferencia"){
        document.getElementById("banco_id").disabled = false;
        document.getElementById("cuenta_id").disabled = false;
        document.getElementById("idFechaCheque").disabled = false;
        document.getElementById("idNcheque").disabled = true;
        document.getElementById("idCuentaContable").disabled = false;
        document.getElementById("idCuentaContable").innerHTML = "";         

    }
    
    document.getElementById("cuenta_id").innerHTML = "";
}
function cargarbanco(){
    $.ajax({
        url: "{{ url("bancos/searchN") }}",
        dataType: "json",
        type: "GET",
        data: {
           
        },
        success: function(data){
            
            document.getElementById("cuenta_id").innerHTML = "";
            document.getElementById("idCuentaContable").innerHTML="";
            document.getElementById("banco_id").innerHTML = "<option value='' label>--Seleccione una opcion--</option>";
            for (var i=0; i<data.length; i++) {
                document.getElementById("banco_id").innerHTML += "<option value='"+data[i].banco_id+"'>"+data[i].banco_lista_nombre+"</option>";
            }           
        },
    });
}
function cargarCuenta(){
    $.ajax({
        url: '{{ url("cuentaBancaria/searchN") }}'+ '/' +document.getElementById("banco_id").value,
        dataType: "json",
        type: "GET",
        data: {
            buscar: document.getElementById("banco_id").value
        },
        success: function(data){

            document.getElementById("cuenta_id").innerHTML = "<option value='' label>--Seleccione una opcion--</option>";
            document.getElementById("idCuentaContable").innerHTML="";
            for (var i=0; i<data.length; i++) {
                document.getElementById("cuenta_id").innerHTML += "<option value='"+data[i].cuenta_bancaria_id+"'>"+data[i].cuenta_bancaria_numero+"</option>";
            }           
        },
    });
}
function cargarContable(){
    document.getElementById("idCuentaContable").disabled = false;
    $.ajax({
        url: '{{ url("cuentaContable/searchN") }}'+ '/' +document.getElementById("cuenta_id").value,
        dataType: "json",
        type: "GET",
        data: {
            buscar: document.getElementById("cuenta_id").value
        },
        success: function(data){
            
            for (var i=0; i<data.length; i++) {
                document.getElementById("idCuentaContable").innerHTML += "<option value='"+data[i].cuenta_id+"'>"+data[i].cuenta_numero+"-"+data[i].cuenta_nombre+"</option>";
                var combo = document.getElementById("cuenta_id");
                document.getElementById("ncuenta").value= combo.options[combo.selectedIndex].text;
            }           
        },
    });
}
function cargarCuentaCaja(){    
    $.ajax({
        url: '{{ url("cuentasCaja/searchN") }}',
        dataType: "json",
        type: "GET",
        data: {
        
        },
        success: function(data){                  
            for (var i=0; i<data.length; i++) {
                document.getElementById("idCuentaContable").innerHTML += "<option value='"+data[i].cuenta_id+"'>"+data[i].cuenta_numero+"-"+data[i].cuenta_nombre+"</option>";
            }

        },
    });
    cargarCuentaParametrizada();
}
function cargarCuentaParametrizada(){    
    $.ajax({
        url: '{{ url("cuentaParametrizadaCaja/searchN/CAJA") }}',
        dataType: "json",
        type: "GET",
        data: {
            buscar: "CAJA"
        },
        success: function(data){

            for (var i=0; i<data.length; i++) {
                $("#idCuentaContable > option[value="+ data[i].cuenta_id +"]").attr("selected",true);
                $("#idCuentaContable").select2().val(data[i].cuenta_id).trigger("change");    
            }                  
        },
    });
}



</script>
