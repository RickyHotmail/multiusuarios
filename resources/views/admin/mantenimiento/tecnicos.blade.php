@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">Técnicos de Mantenimiento</h3>
        <button class="btn btn-default btn-sm float-right" data-toggle="modal" data-target="#modal-nuevo"><i class="fa fa-plus"></i>&nbsp;Nuevo</button>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <table id="example1" class="table table-bordered table-hover table-responsive sin-salto">
            <thead>
                <tr class="text-center neo-fondo-tabla">  
                    <th></th>
                    <th>Nombres</th>
                    <th>Correo</th>
                    <th>Teléfono</th>  
                    <th>Estado</th>
                </tr>
            </thead>            
            <tbody>
                @foreach($tecnicos as $tecnico)
                <tr class="text-center">
                    <td>
                        
                    </td>
                    @if($tecnico->empleado!=null)
                        <td>{{ $tecnico->empleado->empleado_nombre }}</td>
                        <td>{{ $tecnico->user->user_correo }}</td> 
                        <td>{{ $tecnico->empleado->empleado_telefono }}</td> 
                        <td>@if($tecnico->empleado->empleado_estado==1) ACTIVO @else INACTIVO @endif</td>
                    @else
                        <td>{{ $tecnico->responsable_user_apellido }} {{ $tecnico->responsable_user_nombre }}</td>
                        <td>{{ $tecnico->user->user_correo }}</td> 
                        <td>{{ $tecnico->responsable_user_telefono }}</td> 
                        <td>@if($tecnico->user->user_estado==1) ACTIVO @else INACTIVO @endif</td>
                    @endif
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <!-- /.card-body -->
</div>
<div class="modal fade" id="modal-nuevo">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-secondary">
                <h4 class="modal-title">Nuevo Técnico</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form-horizontal" method="POST" action="{{ url("agregartecnicomantenimiento") }}">
                @csrf
                <div class="modal-body">
                    <div class="card-body">
                        <div class="form-group row">
                            <label for="usuario_id" class="col-sm-2 col-form-label">Usuario</label>
                            <div class="col-sm-8">
                                <select class="custom-select select2" id="usuario_id" name="usuario_id" required>
                                    <option value="" label>--Seleccione una opcion--</option>
                                    @foreach($usuarios as $usuario)
                                        <?php $existe = false ?>
                                        @foreach($tecnicos as $tecnico)
                                            @if($tecnico->user->user_id == $usuario->user_id)
                                                <?php $existe = true ?>
                                            @endif
                                        @endforeach
                                        @if(!$existe)
                                            <option value="{{$usuario->user_id}}">{{$usuario->user_username}}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="empleado_id" class="col-sm-2 col-form-label">Empleado</label>
                            <div class="col-sm-6">
                                <select class="custom-select select2" id="empleado_id" name="empleado_id">
                                    <option value="" label>--Seleccione una opcion--</option>                                    
                                    @foreach($empleados as $empleado)                                                                                      
                                        <option value="{{$empleado->empleado_id}}">{{$empleado->empleado_nombre}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-sm-1">
                                <div class="icheck-success">
                                    <input checked onchange="aparecerCampos(this)" type="checkbox" name="usar_empleado" id="usar_empleado">
                                    <label for="usar_empleado" class="custom-checkbox">Usar</label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group" id="form_datos" style="display: none">
                            <div class="form-group row">
                                <label for="nombre" class="col-sm-2 col-form-label">Nombre</label>
                                <div class="col-sm-9">
                                    <input class="form-control" type="text" name="nombre" id="nombre">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="apellido" class="col-sm-2 col-form-label">Apellidos</label>
                                <div class="col-sm-9">
                                    <input class="form-control" type="text" name="apellido" id="apellido">
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-md-6 row">
                                    <label for="cedula" class="col-sm-4 col-form-label">Cédula</label>
                                    <div class="col-sm-8">
                                        <input class="form-control" type="text" name="cedula" id="cedula">
                                    </div>
                                </div>
                                <div class="col-md-6 row">
                                    <label for="telefono" class="col-sm-4 col-form-label">Teléfono</label>
                                    <div class="col-sm-8">
                                        <input class="form-control" type="text" name="telefono" id="telefono">
                                    </div>
                                </div>
                            </div>
                        </div>
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

<script>
    function aparecerCampos(cb){
        if(cb.checked)
            document.getElementById("form_datos").style.display='none'
        else
            document.getElementById("form_datos").style.display='initial'

    }
</script>
@endsection