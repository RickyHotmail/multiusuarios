
<form class="form-horizontal" method="POST" action="{{ url('/gestionPermisos/guardarPermisosGrupo') }}">
    <input name="grupoId" type="hidden" value="{{$parmetrizacionGrupoId}}">
    @csrf
    <div class="card card-secondary">
        <div class="card-header">
            <h3 class="card-title">Asignar Permisos Generales</h3>
            <div class="float-right">
                <button type="submit" class="btn btn-success btn-sm"><i class="fa fa-save"></i>&nbsp;Guardar</button>
                <!-- 
                <button type="button" onclick='window.location = "{{ url("rol") }}";' class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
                --> 
                <button  type="button" onclick="history.back()" class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
            </div>
        </div>
        <div class="card-body">
            <div class="form-group">
               
            </div>
            <div class="form-group">
                <label>Seleccionar permisos para Grupo de Permisos: <strong>{{$grupoGeneral->parametrizaciong_nombre}}</strong></label>
                <div class="well listview-pagupa">
                    <div class="row">
                        <?php
                            $grupo_ant=0;
                            $tipo_ant=0;
                            $cant=0;
                        ?>

                        @foreach($permisos as $permiso)
                            <?php
                                $encontrada=false;
                                foreach($permisosParam as $param) {
                                    if($param->permiso_id==$permiso->permiso_id){
                                        $encontrada=true;
                                        break;
                                    }
                                }
                            ?>

                            @if($grupo_ant!= $permiso->grupo->grupo_id)
                                @if($grupo_ant!=0)
                                            </table>
                                        </tbody>
                                    </div>
                                @endif

                                <div class="col-md-4" style="margin-top: 40px"> 
                                    <div style="display:flex">
                                        <h4 style="width:250px; margin:0px">{{mb_strtoupper($permiso->grupo->grupo_nombre)}}</h4>
                                        <input onchange="marcarGrupo(this, {{$permiso->grupo->grupo_id}})" type="checkbox" id="check_{{$permiso->grupo->grupo_id}}">
                                        <label for="check_{{$permiso->grupo->grupo_id}}">Todos</label>
                                    </div>
                                <table style="margin-top: 20px">
                                    <head>
                                    </head>
                                    <tbody>
                                @php $tipo_ant=0 @endphp
                            @endif

                            @if($tipo_ant!= $permiso->tipo_id)
                                <tr>
                                    <td colspan=2>
                                        <h4 style="margin: 0px; padding-top: 20px" >&nbsp;&nbsp;&nbsp;{{$permiso->tipo->tipo_nombre}}</h4>
                                    </td>
                                </tr>
                            @endif

                            <tr>
                                <td  width="250px">&nbsp;&nbsp;&nbsp;{{$permiso->permiso_nombre}}</td>
                                <td>
                                    <input type="checkbox" @if($encontrada) checked  @endif value="{{$permiso->permiso_id}}" id="permiso_{{$permiso->grupo_id}}_{{$permiso->permiso_id}}" name="permiso[]">
                                </td>
                            </tr>

                            <?php
                                $grupo_ant=$permiso->grupo->grupo_id;
                                $tipo_ant=$permiso->tipo->tipo_id;
                                $cant++;
                            ?>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
    function marcarGrupo(control, id){
        controles=document.querySelectorAll('*[id^="permiso_'+id+'_"]');

        for(var i=0; i< controles.length; i++){
            if(control.checked)
                controles[i].checked=true
            else
                controles[i].checked=false
        }
    }
</script>