@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">Quincena</h3>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <h5 class="form-control" style="color:#fff; background:#17a2b8;">Datos</h5>
            <form class="form-horizontal" method="POST" action="{{ url("pquincena") }} ">
            @csrf
            <div class="card-body">
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
                                        <input type="text" id="quincena_serie" name="quincena_serie"
                                            value="{{ $rangoDocumento->puntoEmision->sucursal->sucursal_codigo }}{{ $rangoDocumento->puntoEmision->punto_serie }}"
                                            class="form-control derecha-texto negrita " placeholder="Serie" required readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                                <div class="form-group">
                                    <div class="form-line">
                                        <input type="text" id="quincena_numero" name="quincena_numero"
                                            value="{{ $secuencial }}" class="form-control  negrita " placeholder="Numero"
                                            required readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="idSucursal" class="col-sm-2 col-form-label">Empleado</label>
                            <div class="col-sm-10">
                                <select class="custom-select select2" id="idEmpleado" name="idEmpleado" onchange="empleado();" required>
                                    <option value="" label>--Seleccione una opcion--</option>
                                    @foreach($empleados as $empleado)                                 
                                            <option value="{{$empleado->empleado_id}}">{{$empleado->empleado_nombre}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <input type="hidden" class="form-control" id="nempleado" name="nempleado" value="">
                        </div>
                        <div class="form-group row">
                            <label for="idFecha" class="col-sm-2 col-form-label">Fecha de Ingreso</label>
                            <div class="col-sm-10">
                                <input type="date" class="form-control" id="idFecha" name="idFecha" value='<?php echo(date("Y")."-".date("m")."-".date("d")); ?>' required>
                            </div>
                        </div>                
                        <div class="form-group row">
                            <label for="idValor" class="col-sm-2 col-form-label">Valor</label>
                            <div class="col-sm-10">
                                <input type="number" class="form-control" value="0.00" min="0.01" step="any" id="idValor" name="idValor" placeholder = "0.00" required>
                            </div>
                        </div>  
                        <div class="form-group row">
                            <label for="idMensaje" class="col-sm-2 col-form-label">Descripcion</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="idMensaje" name="idMensaje" value="Quincena de Empleado : " required>
                                <input type="hidden" class="form-control" id="descripcion" name="descripcion" value="Quincena de Empleado : " required>
                            </div>
                        </div>  
                        
                
            </div>
                
            <h5 class="form-control" style="color:#fff; background:#17a2b8;">Forma de Pago</h5>   
            <div class="card-body">
            <div class="form-group row">
                    <label for="idTipo" class="col-sm-2 col-form-label">Tipo de Pago</label>
                    <div class="col-sm-10">
                       
                        <select class="custom-select" id="idTipo" name="idTipo"  onchange="cajaActivar();" require>
                        <option value='' label>--Seleccione una opcion--</option>
                            <option value="Efectivo" style="display:none;">Efectivo</option>
                            <option value="Cheque">Cheque</option> 
                            <option value="Transferencia">Transferencia</option>                                   
                        </select>
                    </div>
                </div>
               
                    <div class="form-group row">
                            <label for="banco_id" class="col-sm-2 col-form-label">Banco</label>
                            <div class="col-sm-10">
                                <select class="custom-select" id="banco_id" name="banco_id" onchange="cargarCuenta();" required>
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
                                
                                <select class="custom-select" id="cuenta_id" name="cuenta_id"  onchange="cargarContable();" required>
                                  
                                </select>
                                <input type="hidden" class="form-control" id="ncuenta" name="ncuenta" required>
                            </div>
                    </div> 
                    <div class="form-group row">
                            <label for="idCuentaContable" class="col-sm-2 col-form-label">Cuenta Contable</label>
                            <div class="col-sm-10">
                                <select class="custom-select" id="idCuentaContable"  name="idCuentaContable" disabled required>
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
                                <input type="date" class="form-control" id="idFechaCheque" name="idFechaCheque" value='<?php echo(date("Y")."-".date("m")."-".date("d")); ?>' required>
                            </div>
                </div>                
                <div class="form-group row">
                    <label for="idNcheque" class="col-sm-2 col-form-label"># de Cheque</label>
                    <div class="col-sm-10">
                        <input type="number" class="form-control" id="idNcheque" name="idNcheque" min="1" required>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="idBeneficiario" class="col-sm-2 col-form-label">Beneficiario</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="idBeneficiario" name="idBeneficiario" disable required>
                    </div>
                </div>             
                 
            </div>                   
            <!-- /.card-body -->
            <div class="card-footer">
                <button type="submit" class="btn btn-success">Guardar</button>                
            </div>            
            <!-- /.card-footer -->
        </form>
    </div>
    <!-- /.card-body -->
</div>
<!-- /.card -->
@endsection
<script type="text/javascript">


function cargarmetodo() {
   // empelados();
}
function empleado(){
     var combo = document.getElementById("idEmpleado");
     document.getElementById("idBeneficiario").value="";
     if(combo.options[combo.selectedIndex].text != '--Seleccione una opcion--')    {
        document.getElementById("idBeneficiario").value =  combo.options[combo.selectedIndex].text;
     }
     document.getElementById("nempleado").value = combo.options[combo.selectedIndex].text;
     document.getElementById("idMensaje").value ="Quincena de Empleado : "+ combo.options[combo.selectedIndex].text;
     document.getElementById("descripcion").value ="Quincena de Empleado : "+ combo.options[combo.selectedIndex].text;
     
    
}

function cajaActivar(){
    
  
    if(document.getElementById("idTipo").value=="Efectivo"){
        document.getElementById("banco_id").disabled = true;
        document.getElementById("cuenta_id").disabled = true;

        document.getElementById("idFechaCheque").disabled = true;
        document.getElementById("idNcheque").disabled = true;
        document.getElementById("idBeneficiario").disabled = true;

        document.getElementById("idCuentaContable").disabled = false;
        document.getElementById("banco_id").value="";
        cargarCuentaCaja();
    }
    if(document.getElementById("idTipo").value=="Cheque"){

        document.getElementById("banco_id").disabled = false;
        document.getElementById("cuenta_id").disabled = false;
        document.getElementById("idFechaCheque").disabled = false;
        document.getElementById("idNcheque").disabled = false;
        document.getElementById("idBeneficiario").disabled = false;
       
        document.getElementById("idCuentaContable").disabled = false;
        document.getElementById("idCuentaContable").innerHTML = "";         
       
    }
    if(document.getElementById("idTipo").value=="Transferencia"){
        document.getElementById("banco_id").disabled = false;
        document.getElementById("cuenta_id").disabled = false;
        document.getElementById("idFechaCheque").disabled = true;
        document.getElementById("idNcheque").disabled = true;
        document.getElementById("idBeneficiario").disabled = true;
        document.getElementById("idCuentaContable").disabled = false;
        document.getElementById("idCuentaContable").innerHTML = "";         

    }
    document.getElementById("cuenta_id").innerHTML = "";
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
        url: "{{ url("cuentasCaja/searchN") }}",
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
        url: "{{ url("cuentaParametrizadaCaja/searchN/CAJA") }}",
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
function empelados(){    
    $.ajax({
        url: '{{ url("quincena/searchN") }}',
        dataType: "json",
        type: "GET",
        data: {  
        },
        success: function(data){                  
            for (var i=0; i<data.length; i++) {
                $("#idEmpleado option[value='"+data[i].empleado_id+"']").remove();
                //document.getElementById("idEmpleado").innerHTML += "<option value='"+data[i].empleado_id+"'>"+data[i].empleado_id+"-"+data[i].empleado_id+"</option>";
            }

        },
    });
   
}

</script>