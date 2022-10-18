@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">Tareas Automatizadas</h3>
        <div class="float-right">
            <button class="btn btn-default btn-sm float-right" data-toggle="modal" data-target="#modal-nuevo"><i class="fa fa-plus"></i>&nbsp;Nuevo</button>
        </div>
    </div>
    <div class="card-body">
        <div class="card-body table-responsive p-0" style="height: 540px;">
            <table id="example4" class="table table-head-fixed text-nowrap">       
                <thead>
                    <tr>
                        <th>Nombre de la Tarea</th>
                        <th>Metodo de la Tarea</th>
                        <th>Rango de tiempo</th>
                        <th>Hora personalizada</th>
                        <th>Estado</th>
                    </tr>
                </thead>            
                <tbody>
                @if(isset($tareas))
                    @for ($i = 0; $i < count($tareas); ++$i)               
                        <tr class="text-center">
                            <td>{{ $tareas[$i]['tarea_nombre_proceso'] }}</td>
                            <td>{{ $tareas[$i]['tarea_procedimiento'] }}</td>
                            <td>
                                @if($tareas[$i]['tarea_tipo_tiempo']==1) Cada 1 minuto @endif
                                @if($tareas[$i]['tarea_tipo_tiempo']==2) Cada 5 minutos @endif
                                @if($tareas[$i]['tarea_tipo_tiempo']==3) Cada 15 minutos @endif
                                @if($tareas[$i]['tarea_tipo_tiempo']==4) Cada Hora @endif
                                @if($tareas[$i]['tarea_tipo_tiempo']==5) Cada 6 Horas @endif
                                @if($tareas[$i]['tarea_tipo_tiempo']==6) Lunes @endif
                                @if($tareas[$i]['tarea_tipo_tiempo']==7) Martes @endif
                                @if($tareas[$i]['tarea_tipo_tiempo']==8) Miércoles @endif
                                @if($tareas[$i]['tarea_tipo_tiempo']==9) Jueves @endif
                                @if($tareas[$i]['tarea_tipo_tiempo']==10) Viernes @endif
                                @if($tareas[$i]['tarea_tipo_tiempo']==11) Sábado @endif
                                @if($tareas[$i]['tarea_tipo_tiempo']==12) Domingo @endif
                                @if($tareas[$i]['tarea_tipo_tiempo']==13) Cada día @endif
                                @if($tareas[$i]['tarea_tipo_tiempo']==14) Cada último día del mes @endif
                            </td>

                                @if($tareas[$i]['tarea_tipo_tiempo']>5)
                                    <td>{{ $tareas[$i]['tarea_hora_ejecucion']}}</td>
                                @else
                                    <td>--:--</td>
                                @endif
                                <td>
                                    @if($tareas[$i]['tarea_estado']==0)
                                        <button class="btn btn-xs btn-outline-warning">DESHABILITADO</button>
                                    @else
                                        <button class="btn btn-xs btn-outline-success">HABILITADO</button>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ url('tareasProgramadas') }}/{{ $tareas[$i]['tarea_id'] }}/edit" class="btn btn-xs btn-primary"><i class="fa fa-edit"></i> </a>
                                </td>
                            </td>
                        </tr>
                    @endfor
                @endif
                </tbody>
            </table>
        </div>  
    </div>
</div>

<div class="modal fade" id="modal-nuevo">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-secondary">
                <h4 class="modal-title">Nueva Tarea</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form-horizontal" method="POST" action="{{ url('tareasProgramadas') }}">
                @csrf
                <div class="modal-body">
                    <div class="card-body">
                        <div class="form-group row">
                            <label for="cuenta_nivel" class="col-sm-3 col-form-label">Nombre del Proceso</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="tarea_nombre_proceso" name="tarea_nombre_proceso" placeholder="Nombre del Proceso" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="cuenta_nivel" class="col-sm-3 col-form-label">Metodo del Proceso</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="tarea_procedimiento" name="tarea_procedimiento" placeholder="Ejm.: enviarCorreosAutomaticos" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="cuenta_numero" class="col-sm-3 col-form-label">Tiempo:</label>
                            <div class="col-sm-9">
                                <select name="tarea_tipo_tiempo"  id="tarea_tipo_tiempo" onchange="llenarHora()">
                                    <option value=1 selected>Cada 1 minuto</option>
                                    <option value=2>Cada 5 minuto</option>
                                    <option value=3>Cada 15 minuto</option>
                                    <option value=4>Cada hora</option>
                                    <option value=5>Cada 6 horas</option>
                                    <option value=6>Lunes</option>
                                    <option value=7>Martes</option>
                                    <option value=8>Miércoles</option>
                                    <option value=9>Jueves</option>
                                    <option value=10>Viernes</option>
                                    <option value=11>Sábado</option>
                                    <option value=12>Domingo</option>
                                    <option value=13>Cada día</option>
                                    <option value=14>Cada último día del mes</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="cuenta_numero" class="col-sm-3 col-form-label">Hora de ejecución:</label>
                            <div class="col-sm-9">
                                <input type="time" name="tarea_hora_ejecucion" id="tarea_hora_ejecucion" value="--:--" disabled>
                            </div>
                        </div>                      
                    </div>
                    <!-- /.card-body -->
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Guardar</button>
                </div>
            </form>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
@endsection


<script>
    function llenarHora(){
        var select = document.getElementById('tarea_tipo_tiempo');
        console.log('ssfdsfsd '+select.value)

        if(select.value>5){
            document.getElementById('tarea_hora_ejecucion').value="09:00"
            document.getElementById('tarea_hora_ejecucion').disabled=false
        }else{
            document.getElementById('tarea_hora_ejecucion').value="--:--"
            document.getElementById('tarea_hora_ejecucion').disabled=true
        }
    }
</script>