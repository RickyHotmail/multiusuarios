@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">DECIMO CUARTO</h3>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <h5 class="form-control" style="color:#fff; background:#17a2b8;">Datos</h5>
            <form class="form-horizontal" method="POST" action="{{ url("individualdecimoCuarto") }} ">
            @csrf
            <div class="card-body">     
                                        <input id="punto_id" name="punto_id"
                                            value="{{ $rangoDocumento->puntoEmision->punto_id }}" type="hidden">
                                        <input id="rango_id" name="rango_id" value="{{ $rangoDocumento->rango_id }}"
                                            type="hidden">
                                        
                                        <input  type="hidden" id="sucursal_id" name="sucursal_id"
                                            value="{{ $rangoDocumento->puntoEmision->sucursal->sucursal_id }}"
                                            required readonly>   
                        <div class="form-group row">
                            <label for="idSucursal" class="col-sm-2 col-form-label">Mes y Año</label>
                            <div class="col-sm-2">
                                <input type="month" name="fecha_desde" id="fecha_desde" class="form-control" value='<?php echo((date("Y")-1)."-".date("03")); ?>'>
                                
                            </div>
                            <div class="col-sm-2">
                            <input type="month" name="fecha_hasta" id="fecha_desde" class="form-control" value='<?php echo((date("Y"))."-".date("02")); ?>'>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="empleado_id" class="col-sm-2 col-form-label">Empleado</label>
                            <div class="col-sm-10">
                                <select class="custom-select select2" id="empleado_id" name="empleado_id" onchange="empleado();" required>
                                    <option value="" label>--Seleccione una opcion--</option>
                                    @foreach($empleados as $empleado)                                 
                                            <option value="{{$empleado->empleado_id}}">{{$empleado->empleado_nombre}}</option>
                                    @endforeach
                                </select>
                            </div>
                           
                        </div>
                        <div class="form-group row">
                            <label for="" class="col-sm-2 col-form-label">Fecha de Emision</label>
                            <div class="col-sm-10">
                                <input type="date" class="form-control" id="idFechaemision" name="idFechaemision" value='<?php echo(date("Y")."-".date("m")."-".date("d")); ?>' required>

                               
                            </div>
                        </div>                
                        <div class="form-group row">
                            <label for="idValor" class="col-sm-2 col-form-label">Valor</label>
                            <div class="col-sm-10">
                                <input type="number" class="form-control" value="0.00"  step="any" id="idValor" name="idValor" placeholder = "0.00" required>
                            </div>
                        </div>  
                        <div class="form-group row">
                            <label for="idMensaje" class="col-sm-2 col-form-label">Descripcion</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="idMensaje" name="idMensaje" value="Decimo Cuarto de Empleado : " required>
                                <input type="hidden" class="form-control" id="descripcion" name="descripcion" value="Decimo Cuarto de Empleado : " required>
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
                        <input type="number" class="form-control" id="idNcheque" name="idNcheque" required>
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
     var combo = document.getElementById("empleado_id");
     document.getElementById("idBeneficiario").value="";
     if(combo.options[combo.selectedIndex].text != '--Seleccione una opcion--')    {
        document.getElementById("idBeneficiario").value =  combo.options[combo.selectedIndex].text;
     }
    
     document.getElementById("idMensaje").value ="Decimo Cuarto de Empleado : "+ combo.options[combo.selectedIndex].text;
     document.getElementById("descripcion").value ="Decimo Cuarto Empleado : "+ combo.options[combo.selectedIndex].text;
     
    
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
        document.getElementById("idFechaCheque").disabled = false;
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


</script>