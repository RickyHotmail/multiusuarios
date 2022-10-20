
<form class="form-horizontal" method="POST" action="{{ url('/gestionPermisos/guardar') }}">
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
                <label>Seleccionar permisos</label>
                <div class="well listview-pagupa">
                    <div class="row">
                        <?php
                            $grupo_ant=0;
                            $tipo_ant=0;
                            $cant=0;
                        ?>


                        @foreach($permisos as $permiso)
                            @if($grupo_ant!= $permiso->permiso->grupo->grupo_id)
                                @if($grupo_ant!=0) </div>@endif

                                <div class="col-md-4"> 
                                <h4>{{$permiso->permiso->grupo->grupo_nombre}}</h4>
                                <table>
                                    <head>
                                        <tr>
                                        <th></th><th>Gral.</th><th>Médico.</th><th>Camar.</th><th>Factur.</th>
                                        </tr>
                                    </head>
                                    <tbody>
                                {{$tipo_ant=0}}
                            @endif

                            @if($tipo_ant!= $permiso->permiso->tipo_id)
                                <tr>
                                    <td colspan=5>
                                    <h4>&nbsp;&nbsp;&nbsp;{{$permiso->permiso->tipo->tipo_nombre}}</h4>
                                    </td>
                                </tr>
                            @endif

                            <tr>
                                <td>{{$permiso->permiso->permiso_nombre}}</td>
                                <td>
                                    <input type="checkbox" @if($permiso->parametrizacionp_general) checked  @endif value="{{$permiso->permiso->permiso_id}}" id="permiso_1{{$permiso->permiso->permiso_id}}" name="permiso_1[]">
                                </td>
                                <td>
                                    <input type="checkbox" @if($permiso->parametrizacionp_medico) checked @endif value="{{$permiso->permiso->permiso_id}}" id="permiso_2{{$permiso->permiso->permiso_id}}" name="permiso_2[]">
                                </td>
                                <td>
                                    <input type="checkbox" @if($permiso->parametrizacionp_camaronero) checked @endif value="{{$permiso->permiso->permiso_id}}" id="permiso_3{{$permiso->permiso->permiso_id}}" name="permiso_3[]">
                                </td>
                                <td>
                                    <input type="checkbox" @if($permiso->parametrizacionp_facturacion) checked @endif value="{{$permiso->permiso->permiso_id}}" id="permiso_4{{$permiso->permiso->permiso_id}}" name="permiso_4[]">
                                </td>
                            </tr>

                            <?php
                                $grupo_ant=$permiso->permiso->grupo->grupo_id;
                                $tipo_ant=$permiso->permiso->tipo->tipo_id;
                                $cant++;
                            ?>

                            @if($cant>=10) @break @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
