@extends ('admin.layouts.admin')
@section('principal')
<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="card card-primary ">
    <form method="POST"  action="{{ url("controldiario") }} "> 
    @csrf
        <div class="row">
            <!-- Tabla de empelados -->
            <div class="col-sm-2">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">Empleados</h3>
                    </div>
                    <div class="card-body" >
                        <table id="exampleBuscar" class="table table-hover table-responsive">
                            <thead class="invisible">
                                <tr class="text-center">
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($empleados as $empleado)
                                <tr>
                                    <td class="filaDelgada20"><div class="custom-control custom-radio"><input type="radio" class="custom-control-input" id="{{ $empleado->empleado_id}}"  name="radioempleado" value="{{ $empleado->empleado_id}}" ><label for="{{ $empleado->empleado_id}}" class="custom-control-label" style="font-size: 15px; font-weight: normal !important;">{{$empleado->empleado_nombre}}</label></div>                                
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                
            </div>
            <!-- Tabla de detalles -->
            <div class="col-sm-10">
                <div  class="row">  
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                    <div class="row clearfix form-horizontal">
                    <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 form-control-label ">
                        <label>NUMERO</label>
                    </div>
                    <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                        <div class="form-group">
                            <div class="form-line">
                                <input id="punto_id" name="punto_id"
                                    value="{{ $rangoDocumento->puntoEmision->punto_id }}" type="hidden">
                                <input id="rango_id" name="rango_id" value="{{ $rangoDocumento->rango_id }}"
                                    type="hidden">
                                <input type="text" id="control_serie" name="control_serie"
                                    value="{{ $rangoDocumento->puntoEmision->sucursal->sucursal_codigo }}{{ $rangoDocumento->puntoEmision->punto_serie }}"
                                    class="form-control derecha-texto negrita " placeholder="Serie" required readonly>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                        <div class="form-group">
                            <div class="form-line">
                                <input type="text" id="control_numero" name="control_numero"
                                    value="{{ $secuencial }}" class="form-control  negrita " placeholder="Numero"
                                    required readonly>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                    <div class="float-right">
                        <button type="button" id="nuevoID" onclick="nuevo()" class="btn btn-primary btn-sm"><i
                                class="fas fa-receipt"></i><span> Nuevo</span></button>
                        <button id="guardarID" type="submit" class="btn btn-success btn-sm" disabled><i
                                class="fa fa-save"></i><span> Guardar</span></button>
                        <button type="button" id="cancelarID" name="cancelarID" onclick="javascript:location.reload()"
                            class="btn btn-danger btn-sm not-active-neo" disabled><i
                                class="fas fa-times-circle"></i><span> Cancelar</span></button>
                        <br>

                    </div>
                    </div>
                </div>
                        
                       
                    </div>  
                </div> 
                
                <div id="ulprueba" class="row">  
                <!-- Tabla de ingresos -->
                    <div  class="col-md-4">
                        <br>
                        <div class="card card-secondary">  
                            <div class="card-header">
                                <h3 class="card-title ">Ingreso Mes y Año
                                </h3>
                                <button type="button" id="add" name="add" onclick="dias();" class="btn btn-default btn-sm float-right"><i
                                        class="fas fa-plus"></i>Cargar Mes</button > 
                            </div>
                            
                            <div class="card-body " >  
                                <div class="row clearfix form-horizontal">
                                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3 form-control-label  "
                                       >
                                        <label >Mes y año</label>
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6" >  
                                        <div class="form-group">
                                            <div class="form-line">
                                                <input type="month" name="fechames" id="fechames" class="form-control" value='<?php echo(date("Y")."-".date("m")); ?>'>
                                            </div>
                                        </div> 
                                    </div> 
                                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3" >  
                                        <button type="button" id="addcolumn" name="addcolumn" onclick="agregarcolumna();" class="btn btn-default btn-sm float-right" disabled><i
                                        class="fas fa-plus"></i>Cosechas</button > 
                                    </div> 
                                </div> 
                                                       
                            </div>      
                        </div>
                    </div>
                    <div  class="col-md-8">
                        <br>
                        <div class="card card-secondary">  
                            <div class="card-header">
                                <h3 class="card-title ">Ingrese los datos
                                </h3>
                                <button type="button" id="add" name="add" onclick="cargardias();" class="btn btn-default btn-sm float-right"><i
                                        class="fas fa-plus"></i>Cargar Dias</button >       
                            </div>
                            
                            <div class="card-body " >  
                                <div class="row clearfix form-horizontal">
                                    <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 form-control-label  "
                                        style="margin-bottom : 0px;">
                                        <label >Desde</label>
                                    </div>
                                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3" >  
                                        <div class="form-group">
                                            <div class="form-line">
                                                <input type="date" id="fecha_desde" name="fecha_desde" class="form-control "
                                                    placeholder="Seleccione una fecha..."
                                                    value='<?php echo(date("Y")."-".date("m")."-".date("d")); ?>' required />
                                            </div>
                                        </div> 
                                    </div>     
                                    <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 form-control-label  "
                                        style="margin-bottom : 0px;">
                                        <label >Hasta</label>
                                    </div>   
                                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3" >  
                                        <div class="form-group">
                                            <div class="form-line">
                                                <input type="date" id="fecha_hasta" name="fecha_hasta" class="form-control "
                                                    placeholder="Seleccione una fecha..."
                                                    value='<?php echo(date("Y")."-".date("m")."-".date("d")); ?>' required />
                                            </div>
                                        </div> 
                                    </div>
                                    <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 form-control-label   "
                                        >
                                        <label >Tipo dia</label>
                                    </div>
                                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3" >  
                                        <div class="form-group">
                                            <select id="bodega_id" name="bodega_id" class="form-control show-tick"
                                                data-live-search="true">
                                            
                                                <option value="T">Dia Normal</option>
                                                <option value="D">Descanso</option>
                                                <option value="V">Vacaciones</option>
                                                <option value="P">Permisos</option>
                                                <option value="A">Ausente</option>
                                                <option value="C">Cosecha</option>
                                                <option value="X">Dia Extra</option>
                                            
                                            </select>
                                        </div> 
                                    </div>  
                                     
                                    
                                </div> 
                                 
                            </div>      
                        </div>
                    </div>
                    <div  class="col-md-12">  
                        <div class="card">       
                            <div class="card-body table-responsive p-0" style="height: 150px;" >       
                                <table id="tabla" class="table table-head-fixed text-nowrap">
                                    <thead id="plantilla">
                                       
                                    </thead>
                                    <tbody id="plantillaItem">
                                        
                                    </tbody>
                                </table>
                            </div> 
                        </div>   
                    </div> 
                    <div class="col-md-6">
                    </div>   
                    <div class="col-md-3">
                        <table  class="table table-head-fixed text-nowrap">
                            <thead >
                                <tr class="text-center">
                                    <th>Tipo</th>
                                    <th> Simbolo</th>
                                    <th>Color</th>
                                </tr>
                            </thead>
                            <tbody>
                                
                                <tr>
                                    <td class="filaDelgada20"> Dia Normal                                 
                                    </td>
                                    <td class="filaDelgada20 text-center"> T                                
                                    </td>
                                    <td class="filaDelgada20 bg-success color-palette">                          
                                    </td>
                                </tr>
                                <tr>
                                    <td class="filaDelgada20"> Descanso                                
                                    </td>
                                    <td class="filaDelgada20 text-center"> D                               
                                    </td>
                                    <td class="filaDelgada20 bg-warning  color-palette">                        
                                    </td>
                                </tr>
                                <tr>
                                    <td class="filaDelgada20"> Vacaciones                                 
                                    </td>
                                    <td class="filaDelgada20 text-center"> V                               
                                    </td>
                                    <td class="filaDelgada20 bg-info color-palette">          
                                    </td>
                                </tr>
                                <tr>
                                    <td class="filaDelgada20"> Permisos                                 
                                    </td>
                                    <td class="filaDelgada20 text-center"> P                               
                                    </td>
                                    <td class="filaDelgada20 bg-primary color-palette">             
                                    </td>
                                </tr>
                                <tr>
                                    <td class="filaDelgada20"> Ausente                                
                                    </td>
                                    <td class="filaDelgada20 text-center"> A                               
                                    </td>
                                    <td class="filaDelgada20 bg-gray color-palette">                            
                                    </td>
                                </tr>
                                <tr>
                                    <td class="filaDelgada20"> Cosecha                                 
                                    </td>
                                    <td class="filaDelgada20 text-center"> C                                
                                    </td>
                                    <td class="filaDelgada20 bg-orange color-palette">                             
                                    </td>
                                </tr>
                                <tr>
                                    <td class="filaDelgada20"> Dia Extra                                
                                    </td>
                                    <td class="filaDelgada20 text-center"> X                                 
                                    </td>
                                    <td class="filaDelgada20 bg-danger color-palette">                     
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div> 
                    <div class="col-md-3">
                        <div class="card card-primary">  
                            <table class="table table-totalVenta">
                                <tr>
                                    <th  class="text-center-encabesado letra-blanca fondo-gris-oscuro negrita" colspan="2">Totales </th>
                                </tr>
                                <tr>
                                    <td  class="letra-blanca fondo-gris-oscuro negrita" width="90">Total Dia Normal
                                    </td>
                                    <td id="Totalt" name="Totalt" width="100" class="derecha-texto negrita">0
                                    </td>
                                    <input type="hidden"   name="ndias"  id="ndias" value="0" required readonly> 
                                    <input type="hidden"   name="DTotalt"  id="DTotalt" value="0" >                                 
                                </tr>
                                <tr>
                                    <td class="letra-blanca fondo-gris-oscuro negrita">Total Descanso</td>
                                    <td id="Totald"  name="Totald" class="derecha-texto negrita">0</td>
                                    <input type="hidden"   name="mes"  id="mes" value="0" required readonly>
                                    <input type="hidden"   name="DTotald"  id="DTotald" value="0" >     
                                </tr>
                                <tr>
                                    <td class="letra-blanca fondo-gris-oscuro negrita">Total Vacaciones
                                    </td>
                                    <td id="Totalv" name="Totalv" class="derecha-texto negrita">0</td>
                                    <input type="hidden"   name="anio"  id="anio" value="0" required readonly>
                                    <input type="hidden"   name="DTotalv"  id="DTotalv" value="0" >     
                                </tr>
                                <tr>
                                    <td class="letra-blanca fondo-gris-oscuro negrita">Total Permisos</td>
                                    <td id="Totalp"  name="Totalp" class="derecha-texto negrita">0</td>
                                    <input type="hidden"   name="fecha"  id="fecha" value="0" required readonly> 
                                    <input type="hidden"   name="DTotalp"  id="DTotalp" value="0" >                 
                                </tr>
                                <tr>
                                    <td class="letra-blanca fondo-gris-oscuro negrita">Total Ausente</td>
                                    <td id="Totala"  name="Totala" class="derecha-texto negrita">0</td>
                                    <input type="hidden"   name="empleado_id"  id="empleado_id" value="0" required readonly>
                                    <input type="hidden"   name="DTotala"  id="DTotala" value="0" >        
                                  
                                <tr>
                                    <td class="letra-blanca fondo-gris-oscuro negrita">Total Cosecha</td>
                                    <td id="Totalc"  name="Totalc" class="derecha-texto negrita">0</td>
                                    <input type="hidden"   name="DTotalc"  id="DTotalc" value="0" >     
                                   
                                </tr>
                                <tr>
                                    <td  class="letra-blanca fondo-gris-oscuro negrita">Total Dia Extra
                                    </td>
                                    <td id="Totalx"  name="Totalx" class="derecha-texto negrita">0</td>
                                    <input type="hidden"   name="DTotalx"  id="DTotalx" value="0" >     
                                  
                                                
                                </tr>
                               
                            </table>
                        </div>   
                    </div>
                </div>
            </div>
        </div>
        
        
    </form>
</div>

<script type="text/javascript">
function cargarmetodo() {
    $("#ulprueba").find("*").prop('disabled', true);
    
}
function agregarcolumna() {
   
    
    let fecha2 = new Date(document.getElementById("fechames").value);
    fecha2.setMinutes(fecha2.getMinutes() + fecha2.getTimezoneOffset());
    
    var anioactual = fecha2.getFullYear();
    var _mesactual = fecha2.getMonth()+1;
    
    var diasMes = new Date(anioactual, _mesactual, 0).getDate();
    diasMes=62-(31-diasMes);

    var linea = $("#plantillaItem").html();

    linea   = '<tr>';
    
    linea   +=' <td class="text-center">'+anioactual+'</td> <td class="text-center">'+obtenerNombreMes(_mesactual)+'</td>';
    for (let step = (document.getElementById("tabla").rows[1].cells.length-1); step <= 62; step++) {
        if(diasMes>=step){
        linea   +=' <td class="text-center"><input  class="form-controltext2" type="text" id="Dia'+step+'"  name="Dia[]" maxlength="1"  onclick="accion('+step+');" onchange="accion('+step+');" onkeyup="accion('+step+');" value=""></td>';
        }else{
            linea   +=' <td class="invisible"><input  class="invisible" type="text" id="Dia'+step+'"  name="Dia[]" maxlength="1"  value="0"></td>';  
        }
    }  
    linea   += '</tr>';
    $("#tabla tbody").append(linea); 
    document.getElementById("addcolumn").disabled = true;
}
function nuevo() {  
    document.getElementById("guardarID").disabled = false;
    document.getElementById("cancelarID").disabled = false;
    document.getElementById("nuevoID").disabled = true;

    $("#ulprueba").find("*").prop('disabled', false); 
   
    document.getElementById("empleado_id").value = $('input:radio[name=radioempleado]:checked').val()
    document.getElementById("addcolumn").disabled = true;
    
}
function existe(diasMes) {
    var activa=false;
   
    $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            }
        });
       
    $.ajax({
        url: '{{ url("cargarControldias") }}',
        dataType: "json",
        async: false,
        type: "POST",
        data: {
            buscar:  document.getElementById("empleado_id").value,
            aniorubro : document.getElementById("anio").value,
            mesrubro : document.getElementById("mes").value,
        },
        success: function(data){
            var linea = $("#plantillaItem").html();
            linea   = '<tr>';
            linea   +=' <td class="text-center">'+data[0]+'</td> <td class="text-center">'+data[1]+'</td>';
            for (var i=2; i<data.length; i++) {
                    if((diasMes+1)>=i){
                    linea   +=' <td class="text-center"><input  class="form-controltext2" type="text" id="Dia'+(i-1)+'"  name="Dia[]" maxlength="1"  onclick="accion('+(i-1)+');" onchange="accion('+(i-1)+');" onkeyup="accion('+(i-1)+');" value="'+data[i]+'"></td>';
                    }else{
                        linea   +=' <td class="invisible"><input  class="invisible" type="text" id="Dia'+(i-1)+'"  name="Dia[]" maxlength="1"  value="0"></td>';  
                    }
                activa=true; 
            }  
           
            linea   += '</tr>';
            $("#tabla tbody").append(linea); 
            for (var i=2; i<data.length; i++) {
              if ((diasMes+2)>=i) {
                  accion(i-1);
              }            
            }
           
           
           
            
           
        },
    });
    return activa;
}

function obtenerNombreMes (numero) {
  let miFecha = new Date();
  if (0 < numero && numero <= 12) {
    miFecha.setMonth(numero - 1);
    return new Intl.DateTimeFormat('es-ES', { month: 'long'}).format(miFecha);
  } else {
    return null;
  }
}


function accion(id){
    
    var activar=false;
    if(document.getElementById("Dia"+id).value == 'T'){
        document.getElementById("Dia"+id).style.backgroundColor = "#28a745";
        document.getElementById("Dia"+id).style.color = "#fff";
        activar=true;
    }
    if(document.getElementById("Dia"+id).value == 'D'){
        document.getElementById("Dia"+id).style.backgroundColor = "#ffc107";
        document.getElementById("Dia"+id).style.color = "#fff";
        activar=true;
    }
    if(document.getElementById("Dia"+id).value == 'V'){
        document.getElementById("Dia"+id).style.backgroundColor = "#17a2b8";
        document.getElementById("Dia"+id).style.color = "#fff";
        activar=true;
    }
    if(document.getElementById("Dia"+id).value == 'P'){
        document.getElementById("Dia"+id).style.backgroundColor = "#007bff";
        document.getElementById("Dia"+id).style.color = "#fff";
        activar=true;
    }
    if(document.getElementById("Dia"+id).value == 'A'){
        document.getElementById("Dia"+id).style.backgroundColor = "#6c757d";
        document.getElementById("Dia"+id).style.color = "#fff";
        activar=true;
    }
    if(document.getElementById("Dia"+id).value == 'C'){
        document.getElementById("Dia"+id).style.backgroundColor = "#fd7e14";
        document.getElementById("Dia"+id).style.color = "#fff";
        activar=true;
    }
    if(document.getElementById("Dia"+id).value == 'X'){
        document.getElementById("Dia"+id).style.backgroundColor = "#dc3545";
        document.getElementById("Dia"+id).style.color = "#fff";
        activar=true;
    }
    if(id>31){
        if(document.getElementById("Dia"+id).value != 'C'){
            activar=false;
            document.getElementById("Dia"+id).style.backgroundColor = "#fff";
            document.getElementById("Dia"+id).style.color = "#000000";
        }
    }
    if(document.getElementById("Dia"+id).value==""){
        document.getElementById("Dia"+id).style.backgroundColor = "#fff";
        document.getElementById("Dia"+id).style.color = "#000000";
        activar=true;
    }
    if( activar==false){
        document.getElementById("Dia"+id).value="";
    }
   
    Total();
    if(Number(document.getElementById("Totalt").innerHTML)>22){
        document.getElementById("Dia"+id).style.backgroundColor = "#ffffff";
        document.getElementById("Dia"+id).style.color = "#000000";
        document.getElementById("Dia"+id).value="";
        alert('No puede asiginar mas de 22 dias Normales');
        document.getElementById("Totalt").innerHTML=(Number(document.getElementById("Totalt").innerHTML)-1);
        document.getElementById("Dia"+id).style.backgroundColor = "#ffffff";
        document.getElementById("Dia"+id).style.color = "#000000";
        document.getElementById("Dia"+id).value="";
        
    }
    if(Number(document.getElementById("Totald").innerHTML)>8){
        document.getElementById("Dia"+id).style.backgroundColor = "#ffffff";
        document.getElementById("Dia"+id).style.color = "#000000";
        alert('No puede asiginar mas de 8 Dias Descanso');
        document.getElementById("Totalt").innerHTML=(Number(document.getElementById("Totald").innerHTML)-1);
        document.getElementById("Dia"+id).value="";
        document.getElementById("Dia"+id).style.backgroundColor = "#ffffff";
        document.getElementById("Dia"+id).style.color = "#000000";
       
    }
    Total();
}
function cargardias(){

    let fecha1 = new Date(document.getElementById("fecha_desde").value);
    fecha1.setMinutes(fecha1.getMinutes() + fecha1.getTimezoneOffset());
    var diaactual1 = fecha1.getDate();


    let fecha2 = new Date(document.getElementById("fecha_hasta").value);
    fecha2.setMinutes(fecha2.getMinutes() + fecha2.getTimezoneOffset());
    var diaactual2 = fecha2.getDate();
    
    var activador="1";
    for (let step = diaactual1; step <= diaactual2; step++) {
            if(activador=="1"){
                if (document.getElementById("bodega_id").value == "T") {
                    if (Number(document.getElementById("Totalt").innerHTML)==22) {
                        activador="0";
                        alert('Ya tiene asignado los 22 dias Normales');
                    }
                }
                if (document.getElementById("bodega_id").value == "D") {
                    if (Number(document.getElementById("Totald").innerHTML)==8) {
                        activador="0";
                        alert('Ya tiene asignado los 8 Dias Descanso');
                    }
                }
            }
            if(activador=="1"){
             
                if(document.getElementById("tabla").rows.length>2){
                    if(document.getElementById("Dia"+step).value==""){
                        document.getElementById("Dia"+step).value =document.getElementById("bodega_id").value;
                        accion(step);
                    }else{
                        if(document.getElementById("bodega_id").value=="C"){
                            document.getElementById("Dia"+(step+31)).value=document.getElementById("bodega_id").value;
                            accion((step+31));
                        }
                        else{
                            document.getElementById("Dia"+step).value =document.getElementById("bodega_id").value;
                            accion(step);
                        }
                    }
                }
                else{
                    document.getElementById("Dia"+step).value =document.getElementById("bodega_id").value;
                    accion(step);
                }
            }
            
    }
    Total();
   
}

function Total(){
    document.getElementById("Totalt").innerHTML='0';
    document.getElementById("Totald").innerHTML='0';
    document.getElementById("Totalv").innerHTML='0';
    document.getElementById("Totalp").innerHTML='0';
    document.getElementById("Totala").innerHTML='0';
    document.getElementById("Totalc").innerHTML='0';
    document.getElementById("Totalx").innerHTML='0';
    var limit=(document.getElementById("tabla").rows[1].cells.length)-2;
    if(document.getElementById("addcolumn").disabled ==true){
        var limit=(((document.getElementById("tabla").rows[1].cells.length)-2)*2);
    }

    for (let step = 1; step <= limit; step++) {
    
       
        if(document.getElementById("Dia"+step).value == 'T'){

            document.getElementById("Totalt").innerHTML=Number( document.getElementById("Totalt").innerHTML)+1;
        }
        if(document.getElementById("Dia"+step).value == 'D'){
            document.getElementById("Totald").innerHTML=Number( document.getElementById("Totald").innerHTML)+1;
        }
        if(document.getElementById("Dia"+step).value == 'V'){
            document.getElementById("Totalv").innerHTML=Number( document.getElementById("Totalv").innerHTML)+1;
        }
        if(document.getElementById("Dia"+step).value == 'P'){
            document.getElementById("Totalp").innerHTML=Number( document.getElementById("Totalp").innerHTML)+1;
        }
        if(document.getElementById("Dia"+step).value == 'A'){
            document.getElementById("Totala").innerHTML=Number( document.getElementById("Totala").innerHTML)+1;
        }
        if(document.getElementById("Dia"+step).value == 'C'){
            document.getElementById("Totalc").innerHTML=Number( document.getElementById("Totalc").innerHTML)+1;
        }
        if(document.getElementById("Dia"+step).value == 'X'){
            document.getElementById("Totalx").innerHTML=Number( document.getElementById("Totalx").innerHTML)+1;
        }
    
    }
    document.getElementById("DTotalt").value=document.getElementById("Totalt").innerHTML;
    document.getElementById("DTotald").value=document.getElementById("Totald").innerHTML;
    document.getElementById("DTotalv").value=document.getElementById("Totalv").innerHTML;
    document.getElementById("DTotalp").value=document.getElementById("Totalp").innerHTML;
    document.getElementById("DTotala").value=document.getElementById("Totala").innerHTML;
    document.getElementById("DTotalc").value=document.getElementById("Totalc").innerHTML;
    document.getElementById("DTotalx").value=document.getElementById("Totalx").innerHTML;
}
function dias(){
    document.getElementById("addcolumn").disabled = false;

    document.getElementById('plantillaItem').innerHTML = '';
    document.getElementById('plantilla').innerHTML = '';

    let fecha2 = new Date(document.getElementById("fechames").value);
    fecha2.setMinutes(fecha2.getMinutes() + fecha2.getTimezoneOffset());
    
    var anioactual = fecha2.getFullYear();
    var _mesactual = fecha2.getMonth()+1;
    
    var diasMes = new Date(anioactual, _mesactual, 0).getDate();
    document.getElementById("ndias").value=diasMes;
    document.getElementById("mes").value=obtenerNombreMes(_mesactual);
    document.getElementById("anio").value=anioactual
    var linea = $("#plantilla").html();
    linea   = '<tr>';
    linea   += '<th  class="text-center">Año </th><th class="text-center">Mes </th>';
    for (let step = 1; step <= 31; step++) {
        if(diasMes>=step){
            linea   +='<th  class="text-center">Dia '+step+' </th>';
        }else{
            linea   +=' <th  class="invisible">Dia '+step+' </th>';
        }
    }
    linea   += '</tr>';
    $("#tabla thead").append(linea); 
    
    if(existe(diasMes)==false){
        var linea = $("#plantillaItem").html();

        linea   = '<tr>';
        
        linea   +=' <td class="text-center">'+anioactual+'</td> <td class="text-center">'+obtenerNombreMes(_mesactual)+'</td>';
        for (let step = 1; step <= 31; step++) {
            if(diasMes>=step){
            linea   +=' <td class="text-center"><input  class="form-controltext2" type="text" id="Dia'+step+'"  name="Dia[]" maxlength="1"  onclick="accion('+step+');" onchange="accion('+step+');" onkeyup="accion('+step+');" value=""></td>';
            }else{
                linea   +=' <td class="invisible"><input  class="invisible" type="text" id="Dia'+step+'"  name="Dia[]" maxlength="1"  value="0"></td>';  
            }
        }  
        linea   += '</tr>';
        $("#tabla tbody").append(linea); 
    }
    if (_mesactual < 10) //ahora le agregas un 0 para el formato date
    {
    var mesactual = "0" + _mesactual;
    } else {
    var mesactual = _mesactual;
    }
    var ultimoDia = new Date(anioactual,_mesactual, 0).getDate(); 
   
    let fecha_minimo = anioactual + '-' + mesactual + '-01'; 
    let fecha_maximo = anioactual + '-' + mesactual + '-' + diasMes; 

    document.getElementById("fecha_desde").setAttribute('min',fecha_minimo);
    document.getElementById("fecha_desde").setAttribute('max',fecha_maximo);

    document.getElementById("fecha_hasta").setAttribute('min',fecha_minimo);
    document.getElementById("fecha_hasta").setAttribute('max',fecha_maximo);
    
    document.getElementById("fecha_desde").value=fecha_minimo;
    document.getElementById("fecha_hasta").value=fecha_maximo;

    document.getElementById("fecha").value=fecha_maximo;

}

</script>
@endsection