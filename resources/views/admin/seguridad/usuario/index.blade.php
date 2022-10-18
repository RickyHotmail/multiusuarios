@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">Usuarios</h3>
        <button class="btn btn-default btn-sm float-right" data-toggle="modal" data-target="#modal-nuevo"><i class="fa fa-plus"></i>&nbsp;Nuevo</button>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <table id="example1" class="table table-bordered table-hover table-responsive sin-salto">
            <thead>
                <tr class="text-center neo-fondo-tabla">
                    <th></th>
                    <th>Username</th>
                    <th>Cédula</th>
                    <th>Nombre</th>
                    <th>Correo</th>
                </tr>
            </thead>
            <tbody>
                @foreach($usuarios as $usuario)
                <tr class="text-center">
                    <td>
                        <a href="{{ url("usuario/{$usuario->user_id}/roles") }}" class="btn btn-xs btn-secondary" data-toggle="tooltip" data-placement="top" title="Asignar Roles"><i class="fa fa-tasks" aria-hidden="true"></i></a>
                        <a href="{{ url("usuario/{$usuario->user_id}/puntos") }}" class="btn btn-xs btn-neo-morado" data-toggle="tooltip" data-placement="top" title="Permiso a Puntos de Emisión"><i class="fas fa-list" aria-hidden="true"></i></a>
                        <a href="{{ url("usuario/{$usuario->user_id}/edit") }}" class="btn btn-xs btn-primary" data-toggle="tooltip" data-placement="top" title="Editar"><i class="fa fa-edit" aria-hidden="true"></i></a>
                        <a href="{{ url("usuario/{$usuario->user_id}") }}" class="btn btn-xs btn-success" data-toggle="tooltip" data-placement="top" title="Ver"><i class="fa fa-eye" aria-hidden="true"></i></a>
                        <a href="{{ url("usuario/{$usuario->user_id}/eliminar") }}" class="btn btn-xs btn-danger" data-toggle="tooltip" data-placement="top" title="Eliminar"><i class="fa fa-trash" aria-hidden="true"></i></a>
                        <a href="{{ url("usuario/{$usuario->user_id}/restablecer") }}" class="btn btn-xs btn-info" data-toggle="tooltip" data-placement="top" title="Restablecer Contraseña"><i class="fa fa-key" aria-hidden="true"></i></a>
                    </td>
                    <td>{{ $usuario->user_username}}</td>
                    <td>{{ $usuario->user_cedula}}</td>
                    <td>{{ $usuario->user_nombre}}</td>
                    <td>{{ $usuario->user_correo}}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <!-- /.card-body -->
</div>
<!-- /.card -->
<div class="modal fade" id="modal-nuevo">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-secondary">
                <h4 class="modal-title">Nuevo Usuario</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form-horizontal" method="POST" action="{{ url("usuario") }}">
            @csrf
                <div class="modal-body">
                    <div class="card-body">
                        <div class="form-group row">
                            <label for="idNombre" class="col-sm-3 col-form-label">Username</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="idUsername" name="idUsername" placeholder="Username" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="idNombre" class="col-sm-3 col-form-label">Cédula</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="idCedula" name="idCedula" placeholder="Ej. 999999999" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="idNombre" class="col-sm-3 col-form-label">Nombre</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="idNombre" name="idNombre" placeholder="Nombre" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="idNombre" class="col-sm-3 col-form-label">Correo</label>
                            <div class="col-sm-9">
                                <input type="email" class="form-control" id="idCorreo" name="idCorreo" placeholder="SIN@CORREO" value="SIN@CORREO" required>
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
<!-- /.modal -->
@endsection