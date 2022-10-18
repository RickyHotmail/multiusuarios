@extends ('admin.layouts.admin')
@section('principal')
<form class="form-horizontal" method="POST" action="{{ route('ordenAtencion.update', [$ordenAtencion->orden_id]) }}">
@method('PUT')
@csrf
    <div class="card card-secondary">
        <div class="card-header">
            <h3 class="card-title">Editar Orden de Atencion</h3>
            <div class="float-right">
                <button type="submit" class="btn btn-success btn-sm"><i class="fa fa-save"></i>&nbsp;Guardar</button>
                <button type="button" onclick='window.location = "{{ url("ordenAtencion") }}";' class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
            </div>
        </div>
        <div class="card-body">
            <div class="form-group row">
                <label for="idSucursal" class="col-sm-2 col-form-label">Sucursal</label>
                <div class="col-sm-10">
                    <select id="idSucursal" name="idSucursal" class="form-control select2"   required>
                            <option value="" label>--Seleccione una opcion--</option>
                        @foreach($sucursales as $sucursal)
                            @if($sucursal->sucursal_id == $ordenAtencion->sucursal_id)
                                <option value="{{$sucursal->sucursal_id}}" selected>{{$sucursal->sucursal_nombre}}</option>
                            @else
                                <option value="{{$sucursal->sucursal_id}}">{{$sucursal->sucursal_nombre}}</option>
                            @endif
                        @endforeach     
                    </select>
                </div>
            </div>          
            <div class="form-group row">
                <label for="Codigo" class="col-sm-2 col-form-label">Orden de Atencion</label>
                <div class="col-sm-5">                                                          
                    <input type="text" class="form-control negrita" id="Codigo" name="Codigo" value="{{$ordenAtencion->orden_codigo}}" placeholder="OA-" readonly required>                  
                </div>
                <div class="col-sm-5">                                
                    <input type="text" class="form-control negrita" id="Secuencial" name="Secuencial" value="{{$secuencial}}" placeholder="000000001" readonly required>
                </div>
            </div> 
            <div class="form-group row">
                <label for="idPaciente" class="col-sm-2 col-form-label">Paciente</label>
                <div class="col-sm-10">
                    @foreach($pacientes as $paciente)
                        @if($paciente->paciente_id == $ordenAtencion->paciente_id)                                   
                            <input id="buscarPaciente" name="buscarPaciente" type="text" class="form-control" value="{{$paciente->paciente_apellidos.' '.$paciente->paciente_nombres}}" placeholder="Paciente" required>
                            <input id="idPaciente" name="idPaciente" class="invisible" value="{{$paciente->paciente_id}}"  placeholder="Paciente">
                        @endif
                    @endforeach                                                
                </div>
            </div>                      
            <div class="form-group row">
                <label for="especialidad_id" class="col-sm-2 col-form-label">Especialidad</label>
                <div class="col-sm-10">
                    <input id="mespecialidadAUX" name="mespecialidadAUX" class="invisible" value="{{$ordenAtencion->mespecialidad_id}}">  
                    <select id="especialidad_id" name="especialidad_id" class="form-control select2"  onchange="cargarMedicos();"  required>
                        <option value="" label>--Seleccione una opcion--</option>
                        @foreach($especialidades as $especialidad)
                            @if($especialidad->especialidad_id == $ordenAtencion->especialidad_id)
                                <option value="{{$especialidad->especialidad_id}}" selected>{{$especialidad->especialidad_nombre}}</option>
                            @else
                                <option value="{{$especialidad->especialidad_id}}">{{$especialidad->especialidad_nombre}}</option>
                            @endif
                        @endforeach                                                       
                    </select>
                </div>
            </div>       
            
            <div class="form-group row">
                <label for="idMespecialidad" class="col-sm-2 col-form-label">Medico</label>
                <div class="col-sm-10">
                    <select id="idMespecialidad" name="idMespecialidad" class="form-control select2" data-live-search="true" disabled required>
                        <option value="" label>--Seleccione una opcion--</option>
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <label for="Fecha" class="col-sm-2 col-form-label">Fecha</label>
                <div class="col-sm-5">                                
                    <input type="date" class="form-control" id="Fecha" name="Fecha" value="{{$ordenAtencion->orden_fecha}}" onchange="horas();" required>
                </div>
                <label for="Hora" class="col-sm-1 col-form-label">Hora</label>
                <div class="col-sm-4">   
                <input id="horaAUX" name="horaAUX" class="invisible" value="{{$ordenAtencion->orden_hora}}">    
                    <select id="Hora" name="Hora" class="form-control select2" data-live-search="true"  required>  
                            <option value="" label>--Seleccione una opcion--</option>
                    </select>
                </div>
            </div>       
            <div class="form-group row">
                <label for="Observacion" class="col-sm-2 col-form-label">Observacion</label>
                <div class="col-sm-10">
                    <textarea class="form-control" id="Observacion" placeholder="Observaciones" name="Observacion" required>{{$ordenAtencion->orden_observacion}}</textarea>  
                </div>
            </div>
            <div class="form-group row">
                <label for="idEstado" class="col-sm-2 col-form-label">Estado</label>
                <div class="col-sm-9">
                    <div class="col-form-label custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                        @if($ordenAtencion->orden_estado=="1")
                        <input type="checkbox" class="custom-control-input" id="idEstado" name="idEstado" checked>
                        @else
                        <input type="checkbox" class="custom-control-input" id="idEstado" name="idEstado">
                        @endif
                        <label class="custom-control-label" for="idEstado"></label>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
@section('scriptAjax')
<script src="{{ asset('admin/js/ajax/autocompletePaciente.js') }}"></script>
@endsection
<script>
    window.onload = function load(){
        horas();
        cargarMedicos();
    }
    function cargarMedicos(){   
        $.ajax({
            url: '{{ url("horarioFijo/searchN") }}'+ '/' +document.getElementById("especialidad_id").value,
            dataType: "json",
            type: "GET",
            data: {
                buscar: document.getElementById("especialidad_id").value
            },                      
            success: function(data){    
                if(data.length == 0){
                    document.getElementById("idMespecialidad").innerHTML = "<option value='' label>--Seleccione una opcion--</option>";
                }else{
                    document.getElementById("idMespecialidad").disabled = false;
                    document.getElementById("idMespecialidad").innerHTML = "<option value='' label>--Seleccione una opcion--</option>";
                    for (var i = 0; i < data.length; i++) {
                        if(document.getElementById("mespecialidadAUX").value == data[i].mespecialidad_id){
                            if(data[i].proveedor_id != null){
                                document.getElementById("idMespecialidad").innerHTML += "<option value='" + data[i].mespecialidad_id + "'selected>" + cargarMedicosNombresProveedor(data[i].proveedor_id) + "</option>";
                            }else{
                                document.getElementById("idMespecialidad").innerHTML += "<option value='" + data[i].mespecialidad_id + "'selected>" + cargarMedicosNombresEmpleado(data[i].empleado_id) + "</option>";
                            }
                        }
                        else{
                            if(data[i].proveedor_id != null){
                                document.getElementById("idMespecialidad").innerHTML += "<option value='" + data[i].mespecialidad_id + "'>" + cargarMedicosNombresProveedor(data[i].proveedor_id) + "</option>";
                            }else{
                                document.getElementById("idMespecialidad").innerHTML += "<option value='" + data[i].mespecialidad_id + "'>" + cargarMedicosNombresEmpleado(data[i].empleado_id) + "</option>";
                            }
                        }
                    }    
                }
            },
            error: function(data) {
                document.getElementById("idMespecialidad").innerHTML = "<option value='' label>--Seleccione una opcion--</option>";
            },
        });
    }  
    function cargarMedicosNombresProveedor(id){       
        var auxiliar = "0";
        $.ajax({
            url: '{{ url("nombreMedicoP/searchN") }}'+ '/' +id,
            dataType: "json",
            async: false,    
            type: "GET",
            data: {
                buscar: id
            },                      
            success: function(data){                    
                for (var i = 0; i < data.length; i++) {
                    if(data[0].proveedor_id > 0){
                        auxiliar = data[0].proveedor_nombre;                      
                    }                    
                }
            },
            error: function(){ 
                alert("error petición ajax");
            }, 
        });
        return auxiliar;  
    }        

    function cargarMedicosNombresEmpleado(id){       
        var auxiliar = "0";
        $.ajax({
            url: '{{ url("nombreMedicoE/searchN") }}'+ '/' +id,
            dataType: "json",
            async: false,    
            type: "GET",
            data: {
                buscar: id
            },                      
            success: function(data){                    
                for (var i = 0; i < data.length; i++) {
                    if(data[0].empleado_id > 0){
                        auxiliar = data[0].empleado_nombre;                      
                    }                    
                }
            },
            error: function(){ 
                alert("error petición ajax");
            }, 
        });
        return auxiliar;  
    }

      function horas(){   
        var numeroDia = new Date(document.getElementById("Fecha").value);
        var semanaDia = new Array(7);
        semanaDia[0] = "Lunes";
        semanaDia[1] = "Martes";
        semanaDia[2] = "Miercoles";
        semanaDia[3] = "Jueves";
        semanaDia[4] = "Viernes";
        semanaDia[5] = "Sabado";
        semanaDia[6] = "Domingo";
        var dia = semanaDia[numeroDia.getDay()];

        $.ajax({
            url: '{{ url("horas/searchN") }}'+ '/' +dia,
            dataType: "json",
            type: "GET",
            data: {
                buscar: dia
            },                      
            success: function(data){     
                document.getElementById("Hora").innerHTML = "<option value='' label>--Seleccione una opcion--</option>";
                             
                for (var i = 0; i < data.length; i++) {                    
                    if(data[i].horario_dia == dia){    
                        if(document.getElementById("horaAUX").value == data[i].horario_hora_inicio){
                            document.getElementById("Hora").innerHTML += "<option value='" + data[i].horario_hora_inicio + "' selected>" + data[i].horario_hora_inicio + "</option>";
                        }
                        else{
                            document.getElementById("Hora").innerHTML += "<option value='" + data[i].horario_hora_inicio + "'>" + data[i].horario_hora_inicio + "</option>";
                        }
                    }else{
                        document.getElementById("Hora").innerHTML = "<option value='' label>--Seleccione una opcion--</option>";
                    }               
                }
            },
            error: function(data) {
                document.getElementById("Hora").innerHTML = "<option value='' label>--Seleccione una opcion--</option>";
            },
        });
    }  
</script>
@endsection