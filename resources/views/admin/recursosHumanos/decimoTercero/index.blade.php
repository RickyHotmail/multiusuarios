@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary">
    <div class="card-header">
    <h3 class="card-title">Pago Decimo Tercero</h3>
    </div>
    <form class="form-horizontal" method="POST" action="{{ url("decimoT") }} ">
    @csrf
    <div class="card-body">
        <div class="form-group row">
            <label for="fecha_desde" class="col-sm-1 col-form-label"><center>Fecha:</center></label>
                <div class="col-sm-2">
                    <input type="month" name="fecha_desde" id="fecha_desde" class="form-control" value='<?php echo(date("Y")."-".date("12")); ?>'>
                </div>
                <div class="col-sm-2">
                    <input type="month" name="fecha_hasta" id="fecha_hasta" class="form-control" value='<?php echo((date("Y")+1)."-".date("11")); ?>'> 
                </div>
                <div>            
                <button type="submit" id="extraer" name="extraer" class="btn btn-success float-right"><i class="fa fa-search"></i><span> Buscar Rol</span></button>                   
                </div>
            <br>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="example5" class="table table-bordered table-hover table-responsive sin-salto">
                    <thead>
                   
                        <tr class="letra-blanca fondo-azul-claro">
                            <th></th>
                            <th  class="text-center-encabesado">Cédula </th>
                            <th class="text-center-encabesado">Nombre </th>
                        
                            <th class="text-center-encabesado">Dias trabajados</th>

                            <th class="text-center-encabesado">Sueldo </th>
                            
                            <th class="text-center-encabesado">Horas Extras </th>
                            <th class="text-center-encabesado">Horas Suplementarias </th>                        
                            <th class="text-center-encabesado">otros Ingresos </th>
                            <th class="text-center-encabesado">Liquido a Recibir </th>
                        </tr>
                    </thead>
                    <tbody>
                    @if(isset($datos))
                        @for ($i = 1; $i <= count($datos); ++$i)  
                        <tr>
                            <td width="150">{{ $datos[$i]['IDE'] }} <input type="hidden"   name="ide[]" value="{{ $datos[$i]['IDE'] }}" > </td>
                            <td width="150">{{ $datos[$i]['cedula'] }} <input type="hidden"   name="cedula[]" value="{{ $datos[$i]['cedula'] }}" ></td>
                            <td width="150">{{ $datos[$i]['nombre'] }} <input type="hidden"   name="nombre[]" value="{{ $datos[$i]['nombre'] }}" ></td>
                            <td width="150">{{ $datos[$i]['dias'] }} <input type="hidden"   name="dias[]" value="{{ $datos[$i]['dias'] }}" ></td>
                            <td width="150">{{ $datos[$i]['sueldo'] }} <input type="hidden"   name="sueldo[]" value="{{ $datos[$i]['sueldo'] }}" ></td>
                            <td width="150">{{ $datos[$i]['he'] }} <input type="hidden"   name="he[]" value="{{ $datos[$i]['he'] }}" ></td>
                            <td width="150">{{ $datos[$i]['bonificaciones'] }} <input type="hidden"   name="bonificaciones[]" value="{{ $datos[$i]['bonificaciones'] }}" ></td>
                            <td width="150">{{ $datos[$i]['otros'] }} <input type="hidden"   name="otros[]" value="{{ $datos[$i]['otros'] }}" ></td>
                            <td width="150">{{ $datos[$i]['decimo'] }} <input type="hidden"   name="decimo[]" value="{{ $datos[$i]['decimo'] }}" ></td>
                           
                        </tr>
                        @endfor
                    @endif   
                    </tbody>
                </table>
            </div>
        </div>
    </div> 

    <div class="card-header">   
            <div class="card-body">
                <div class="float-right">
                    <button type="submit" id="enviar" name="enviar" class="btn btn-success btn-sm ">Guardar</button>
                    <a href="{{ url("decimoT") }}" class="btn btn-danger btn-sm">Cancelar</a>
                </div>
            </div>
           
            <h5 class="form-control" style="color:#fff; background:#17a2b8;">Forma de Pago</h5>   
            <div class="card-body">
                <div class="form-group row">
                            <label for="idFechaemision" class="col-sm-2 col-form-label">Fecha Emision</label>
                            <div class="col-sm-10">
                                <input type="date" class="form-control" id="idFechaemision" name="idFechaemision" value='<?php echo(date("Y")."-".date("m")."-".date("d")); ?>' >
                            </div>
                </div>
                <div class="form-group row">
                    <label for="idTipo" class="col-sm-2 col-form-label">Tipo de Pago</label>
                    <div class="col-sm-10">
                        
                        <select class="custom-select" id="idTipo" name="idTipo"  onchange="cajaActivar();" >
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
                            <select class="custom-select" id="banco_id" name="banco_id" onchange="cargarCuenta();" >
                                <option value="" label>--Seleccione una opcion--</option>
                            
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
                        <input type="number" class="form-control" id="idNcheque" name="idNcheque" min="1" >
                    </div>
                </div>     
            </div> 
    </div>
   
    </form>
    <!-- /.card-body -->
</div>

@endsection

<script type="text/javascript">


function cajaActivar(){
   
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
        document.getElementById("banco_id").innerHTML = "";
        cargarbanco();
    }
    if(document.getElementById("idTipo").value=="Transferencia"){
        document.getElementById("banco_id").disabled = false;
        document.getElementById("cuenta_id").disabled = false;
        document.getElementById("idFechaCheque").disabled = true;
        document.getElementById("idNcheque").disabled = true;
        document.getElementById("idCuentaContable").disabled = false;
        document.getElementById("idCuentaContable").innerHTML = "";         
        document.getElementById("banco_id").innerHTML = "";
        cargarbanco();
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
        url: '{{ url("cuentaBancaria/searchN") }}'+ '/' + document.getElementById("banco_id").value,
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




function cargarmetodo() {
    <?php
    if(isset($fecha_hasta)){  
        ?>
         document.getElementById("fecha_hasta").value='<?php echo($fecha_hasta); ?>';
         <?php
    }
    if(isset($fecha_desde)){  
        ?>
         document.getElementById("fecha_desde").value='<?php echo($fecha_desde); ?>';
         <?php
    }
    ?>
}



</script>
