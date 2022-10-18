@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">Orden de Atencion</h3>
        <button onclick='history.back();' class="btn btn-default btn-sm float-right"><i class="fa fa-undo"></i>&nbsp;Atras</button>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <div class="form-group row">
            <label for="idSucursal" class="col-sm-2 col-form-label">Sucursal</label>
            <div class="col-sm-10">
                @foreach($sucursales as $sucursal)
                    @if($sucursal->sucursal_id == $ordenAtencion->sucursal_id)
                    <label class="form-control">{{$sucursal->sucursal_nombre}}</label>
                    @endif
                @endforeach     
            </div>
        </div>          
        <div class="form-group row">
            <label for="Codigo" class="col-sm-2 col-form-label">Orden de Atencion</label>
            <div class="col-sm-5">             
                <label class="form-control">{{$ordenAtencion->orden_codigo}}</label>     
            </div>
            <div class="col-sm-5">                                
                <label class="form-control">{{$secuencial}}</label> 
            </div>
        </div> 
        <div class="form-group row">
            <label for="idPaciente" class="col-sm-2 col-form-label">Paciente</label>
            <div class="col-sm-10">             
                @foreach($pacientes as $paciente)
                    @if($paciente->paciente_id == $ordenAtencion->paciente_id)
                        <label class="form-control">{{$paciente->paciente_apellidos.' '.$paciente->paciente_nombres}}</label>
                    @endif
                @endforeach 
            </div>
        </div>                      
        <div class="form-group row">
            <label for="especialidad_id" class="col-sm-2 col-form-label">Especialidad</label>
            <div class="col-sm-10">
                <input id="mespecialidadAUX" name="mespecialidadAUX" class="invisible" value="{{$ordenAtencion->mespecialidad_id}}">  
                @foreach($especialidades as $especialidad)
                    @if($especialidad->especialidad_id == $ordenAtencion->especialidad_id)
                        <label class="form-control">{{$especialidad->especialidad_nombre}}</label>  
                        <input id="especialidad_id" name="especialidad_id" class="invisible" value="{{$especialidad->especialidad_id}}">                         
                    @endif
                @endforeach  
            </div>
        </div>       
        
        <div class="form-group row">
            <label for="idMespecialidad" class="col-sm-2 col-form-label">Medico</label>
            <div class="col-sm-10">
                @if($ordenAtencion->empleado_id != null )
                    @foreach($empleados as $empleado)
                        @if($ordenAtencion->empleado_id == $empleado->empleado_id)
                            <label class="form-control">{{$empleado->empleado_nombre}}</label>                                                                
                        @endif
                    @endforeach
                @elseif($ordenAtencion->proveedor_id != null )
                    @foreach($proveedores as $proveedor)
                        @if($ordenAtencion->proveedor_id == $proveedor->proveedor_id)
                            <label class="form-control"> {{$proveedor->proveedor_nombre}}</label>                                  
                        @endif
                    @endforeach
                @endif
            </div>
        </div>
        <div class="form-group row">
            <label for="Fecha" class="col-sm-2 col-form-label">Fecha</label>
            <div class="col-sm-5">                                
                <label class="form-control">{{$ordenAtencion->orden_fecha}}</label>
            </div>
            <label for="Hora" class="col-sm-1 col-form-label">Hora</label>
            <div class="col-sm-4">       
                <label class="form-control">{{$ordenAtencion->orden_hora}}</label>
            </div>
        </div>       
        <div class="form-group row">
            <label for="Observacion" class="col-sm-2 col-form-label">Observacion</label>
            <div class="col-sm-10">
                <label class="form-control">{{$ordenAtencion->orden_observacion}}</label>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-3 col-form-label">Estado</label>
            <div class="col-sm-9 col-form-label">
                @if($ordenAtencion->orden_estado=="0")
                    <i class="fa fa-times-circle neo-rojo"></i>                
                @else
                    <i class="fa fa-check-circle neo-verde"></i>
                @endif
            </div>
        </div>
    </div>
    <!-- /.card-body -->
    <!-- /.card-footer -->
</div>
<!-- /.card-body -->
</div>
<!-- /.card -->
@endsection
<script>
    window.onload = function load(){
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
                for (var i = 0; i < data.length; i++) {
                    if(document.getElementById("mespecialidadAUX").value == data[i].mespecialidad_id){
                        if(data[i].proveedor_id != null){
                            document.getElementById("idMespecialidad").innerHTML += "<label>" + cargarMedicosNombresProveedor(data[i].proveedor_id) + "</label>";
                        }else{
                            document.getElementById("idMespecialidad").innerHTML += "<label>" + cargarMedicosNombresEmpleado(data[i].empleado_id) + "</label>";
                        }
                    }
                }    
            },
            error: function(data) {
                alert("error petición ajax");
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
</script>