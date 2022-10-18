@extends ('admin.layouts.admin')
@section('principal')
<form class="form-horizontal" method="POST" action="{{ url("medico/horario") }}">
@csrf
    <div class="card card-secondary">
        <div class="card-header">
            <h3 class="card-title">Configurar Horario del Medico</h3>
            <div class="float-right">
                <button type="submit" class="btn btn-success btn-sm"><i class="fa fa-save"></i>&nbsp;Guardar</button>
                <button type="button" onclick='window.location = "{{ url("medico") }}";' class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
            </div>
        </div>
        <div class="card-body">
            <div class="form-group row">
                <label for="producto_tipo" class="col-sm-1 col-form-label">Nombre</label>
                <div class="col-sm-5">
                    <input type="hidden" name="medico_id" value="{{$medico->medico_id}}"/>
                    @if($medico->empleado_id != '')
                        <label class="form-control">{{$medico->empleado->empleado_nombre}}</label>
                    @else
                        <label class="form-control">{{$medico->proveedor->proveedor_nombre}}</label>
                    @endif
                </div>
                <label for="producto_tipo" class="col-sm-1 col-form-label">Especialidad</label>
                <div class="col-sm-4">
                    <select class="form-control select2" id="mespecialidad_id" name="mespecialidad_id" style="width: 100%;" onchange="cargarHorario();" required>
                        <option value="" label>--Seleccione una opcion--</option>
                        @foreach($medico->detalles as $especialidad)
                            <option value="{{$especialidad->mespecialidad_id}}">{{$especialidad->especialidad->especialidad_nombre}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-1">
                    <button type="button" class="btn btn-info" data-toggle="modal" data-target="#modal-nuevo"><i class="fas fa-plus"></i>&nbsp;&nbsp;Nuevo</button>
                </div>
            </div>  
            <div class="form-group">
                <table class="table table-bordered table-hover sin-salto">
                    <thead>
                        <tr class="text-center neo-fondo-tabla">  
                            <th>Lunes</th>
                            <th>Martes</th> 
                            <th>Miércoles</th>
                            <th>Jueves</th>
                            <th>Viernes</th>
                            <th>Sábado</th>
                            <th>Domingo</th>                                                                                 
                        </tr>
                    </thead>            
                    <tbody>
                        <tr>
                            <td id="dia1"></td>
                            <td id="dia2"></td>
                            <td id="dia3"></td>
                            <td id="dia4"></td>
                            <td id="dia5"></td>
                            <td id="dia6"></td>
                            <td id="dia7"></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>                        
    </div>
</form>
<div class="modal fade" id="modal-nuevo">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-secondary">
                <h5 class="modal-title">Nuevo Horario</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="card-body">     
                    <div class="row">
                        <div class="col-sm-4">
                            <label>Dia de la semana</label>
                            <div class="form-group">
                                <div class="form-line">
                                    <select id="idDia" name="idDia" class="form-control select2"
                                        data-live-search="true">
                                        <option value="Lunes">Lunes</option>
                                        <option value="Martes">Martes</option>
                                        <option value="Miércoles">Miércoles</option>
                                        <option value="Jueves">Jueves</option>
                                        <option value="Viernes">Viernes</option>
                                        <option value="Sábado">Sábado</option>
                                        <option value="Domingo">Domingo</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <label>Inicio</label>
                            <div class="form-group">
                                <div class="form-line">
                                    <input type="time" id="idIni" class="form-control" value="08:00">
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <label>Fin</label>
                            <div class="form-group">
                                <div class="form-line">
                                    <input type="time" id="idFin" class="form-control" value="17:00">
                                </div>
                            </div>
                        </div>
                    </div>                                  
                </div>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
                <button type="button" onclick="agregar();" class="btn btn-success">Agregar</button>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    function agregar(){
        var linea = "<center style='padding-bottom: 5px;' class='eliminarHorario'><button type='button' class='btn btn-info' onclick='eliminarHorario(this);'><span>"+document.getElementById("idIni").value+" a "+document.getElementById("idFin").value+"</span><span><i class='fa fa-trash' aria-hidden='true'></i></span></button><input type='hidden' name='timeDia[]' value='"+document.getElementById("idDia").value+"'/><input type='hidden' name='timeIni[]' value='"+document.getElementById("idIni").value+"'/><input type='hidden' name='timeFin[]' value='"+document.getElementById("idFin").value+"'/></center>"
        if(document.getElementById("idDia").value == 'Lunes'){
            $("#dia1").append(linea);
        }
        if(document.getElementById("idDia").value == 'Martes'){
            $("#dia2").append(linea);
        }
        if(document.getElementById("idDia").value == 'Miércoles'){
            $("#dia3").append(linea);
        }
        if(document.getElementById("idDia").value == 'Jueves'){
            $("#dia4").append(linea);
        }
        if(document.getElementById("idDia").value == 'Viernes'){
            $("#dia5").append(linea);
        }
        if(document.getElementById("idDia").value == 'Sábado'){
            $("#dia6").append(linea);
        }
        if(document.getElementById("idDia").value == 'Domingo'){
            $("#dia7").append(linea);
        }
        $('#modal-nuevo').modal('hide');
    }
    function cargarHorario() {
        resetearDias();
        $.ajax({
            url: '{{ url("especialidadMedicoHorario/searchN") }}'+ '/' +document.getElementById("mespecialidad_id").value,
            dataType: "json",
            type: "GET",
            data: {
                buscar: document.getElementById("mespecialidad_id").value
            },
            success: function(data) {
                for (var i = 0; i < data.length; i++) {
                    var linea = "<center style='padding-bottom: 5px;' class='eliminarHorario'><button type='button' class='btn btn-info' onclick='eliminarHorario(this);'><span>"+data[i].horario_hora_inicio.substring(0,5)+" a "+data[i].horario_hora_fin.substring(0,5)+"<input type='hidden' name='timeDia[]' value='"+data[i].horario_dia+"'/><input type='hidden' name='timeIni[]' value='"+data[i].horario_hora_inicio.substring(0,5)+"'/><input type='hidden' name='timeFin[]' value='"+data[i].horario_hora_fin.substring(0,5)+"'/></span><span><i class='fa fa-trash' aria-hidden='true'></i></span></button></center>"
                    if(data[i].horario_dia == 'Lunes'){
                        $("#dia1").append(linea);
                    }
                    if(data[i].horario_dia == 'Martes'){
                        $("#dia2").append(linea);
                    }
                    if(data[i].horario_dia == 'Miércoles'){
                        $("#dia3").append(linea);
                    }
                    if(data[i].horario_dia == 'Jueves'){
                        $("#dia4").append(linea);
                    }
                    if(data[i].horario_dia == 'Viernes'){
                        $("#dia5").append(linea);
                    }
                    if(data[i].horario_dia == 'Sábado'){
                        $("#dia6").append(linea);
                    }
                    if(data[i].horario_dia == 'Domingo'){
                        $("#dia7").append(linea);
                    }
                }
            },
        });
    }
    function resetearDias(){
        $("#dia1").html('');
        $("#dia2").html('');
        $("#dia3").html('');
        $("#dia4").html('');
        $("#dia5").html('');
        $("#dia6").html('');
        $("#dia7").html('');
    }
    function eliminarHorario(objeto){
        bootbox.confirm({
        message: "¿Seguro quieres eliminar este horario?",
        buttons: {
            confirm: {
                label: 'Yes',
                className: 'btn-success'
            },
            cancel: {
                label: 'No',
                className: 'btn-danger'
            }
        },
        callback: function (result) {
            if(result){
                objeto.remove();
            }
        }
    });

    }
        
</script>
@endsection