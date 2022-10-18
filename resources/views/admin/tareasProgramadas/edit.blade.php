@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">Tareas Automatizadas - Editar</h3>
        <div class="float-right">
            <!--
            <button onclick="window.history.back()" class="btn btn-default btn-sm float-right" data-toggle="modal" data-target="#modal-nuevo"><i class="fa fa-undo"></i>&nbsp;Atrás</button>
            -->      
            <button  type="button" onclick="history.back()" class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button> 
        </div>
    </div>
    <div class="card-body">
        <form class="form-horizontal" method="POST" action="{{ url('tareasProgramadas/actualizar') }}">
            @csrf
            <input type="hidden" name="tarea_id" value="{{ $tarea->tarea_id }}"></input>
            <div class="modal-body">
                <div class="card-body">
                    <div class="form-group row">
                        <label for="cuenta_nivel" class="col-sm-3 col-form-label">Nombre del Proceso</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="tarea_nombre_proceso" value="<?php if(isset($tarea)) echo $tarea->tarea_nombre_proceso ?>" name="tarea_nombre_proceso" placeholder="Nombre del Proceso" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="cuenta_nivel" class="col-sm-3 col-form-label">Metodo del Proceso</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="tarea_procedimiento" value="<?php if(isset($tarea)) echo $tarea->tarea_procedimiento ?>" name="tarea_procedimiento" placeholder="Ejm.: enviarCorreosAutomaticos" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="cuenta_numero" class="col-sm-3 col-form-label">Tiempo:</label>
                        <div class="col-sm-9">
                            <select name="tarea_tipo_tiempo" id="tarea_tipo_tiempo" onchange="llenarHora()">
                                <option value=1 <?php if(isset($tarea)){ if($tarea->tarea_tipo_tiempo==1) echo "selected"; }?>>Cada 1 minuto</option>
                                <option value=2 <?php if(isset($tarea)){ if($tarea->tarea_tipo_tiempo==2) echo "selected"; } ?>>Cada 5 minutos</option>
                                <option value=3 <?php if(isset($tarea)){ if($tarea->tarea_tipo_tiempo==3) echo "selected"; } ?>>Cada 15 minutos</option>
                                <option value=4 <?php if(isset($tarea)){ if($tarea->tarea_tipo_tiempo==4) echo "selected"; } ?>>Cada hora</option>
                                <option value=5 <?php if(isset($tarea)){ if($tarea->tarea_tipo_tiempo==5) echo "selected"; } ?>>Cada 6 horas</option>
                                <option value=6 <?php if(isset($tarea)){ if($tarea->tarea_tipo_tiempo==6) echo "selected"; } ?>>Lunes</option>
                                <option value=7 <?php if(isset($tarea)){ if($tarea->tarea_tipo_tiempo==7) echo "selected"; } ?>>Martes</option>
                                <option value=8 <?php if(isset($tarea)){ if($tarea->tarea_tipo_tiempo==8) echo "selected"; } ?>>Miércoles</option>
                                <option value=9 <?php if(isset($tarea)){ if($tarea->tarea_tipo_tiempo==9) echo "selected"; } ?>>Jueves</option>
                                <option value=10 <?php if(isset($tarea)){ if($tarea->tarea_tipo_tiempo==10) echo "selected"; } ?>>Viernes</option>
                                <option value=11 <?php if(isset($tarea)){ if($tarea->tarea_tipo_tiempo==11) echo "selected"; } ?>>Sábado</option>
                                <option value=12 <?php if(isset($tarea)){ if($tarea->tarea_tipo_tiempo==12) echo "selected"; } ?>>Domingo</option>
                                <option value=13 <?php if(isset($tarea)){ if($tarea->tarea_tipo_tiempo==13) echo "selected"; } ?>>Cada día</option>
                                <option value=14 <?php if(isset($tarea)){ if($tarea->tarea_tipo_tiempo==14) echo "selected"; } ?>>Último día del mes</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="cuenta_numero" class="col-sm-3 col-form-label">Hora de ejecución:</label>
                        <div class="col-sm-9">
                            @if(isset($tarea))
                                @if($tarea->tarea_tipo_tiempo>5)
                                    <input type="time" name="tarea_hora_ejecucion" id="tarea_hora_ejecucion" enabled value="{{$tarea->tarea_hora_ejecucion}}">
                                @else
                                    <input type="time" name="tarea_hora_ejecucion" iid="tarea_hora_ejecucion" value="" disabled>
                                @endif
                            @else
                                <input type="time" name="tarea_hora_ejecucion" i id="tarea_hora_ejecucion" value="" disabled>
                            @endif
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Estado:</label>
                        <div class="col-sm-10">
                            <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                                <input type="checkbox" class="custom-control-input" id="tarea_estado" name="tarea_estado" <?php if(isset($tarea)){ if($tarea->tarea_estado==1) echo 'checked'; } ?>>
                                <label class="custom-control-label" for="tarea_estado"></label>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.card-body -->
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-danger" style="visibility:hiddenph" data-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-success">Guardar</button>
            </div>
        </form>
    </div>
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