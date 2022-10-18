@extends ('admin.layouts.admin')
@section('principal')
<form class="form-horizontal" method="POST" action="{{ route('usuario.update', [$usuario->user_id]) }}">
@method('PUT')
@csrf
    <div class="card card-secondary">
        <div class="card-header">
            <h3 class="card-title">Editar Usuario</h3>
            <div class="float-right">
                <button type="submit" class="btn btn-success btn-sm"><i class="fa fa-save"></i>&nbsp;Guardar</button>
                <!-- 
                <button type="button" onclick='window.location = "{{ url("usuario") }}";' class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
                --> 
                <button  type="button" onclick="history.back()" class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
            </div>
        </div>
        <!-- /.card-header -->
        <div class="card-body">
            <div class="form-group row">
                <label for="idNombre" class="col-sm-3 col-form-label">Username</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" id="idUsername" name="idUsername" placeholder="Username" value="{{$usuario->user_username}}" required>
                </div>
            </div>
            <div class="form-group row">
                <label for="idNombre" class="col-sm-3 col-form-label">Cédula</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" id="idCedula" name="idCedula" placeholder="Ej. 999999999" value="{{$usuario->user_cedula}}" required>
                </div>
            </div>
            <div class="form-group row">
                <label for="idNombre" class="col-sm-3 col-form-label">Nombre</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" id="idNombre" name="idNombre" placeholder="Nombre" value="{{$usuario->user_nombre}}" required>
                </div>
            </div>
            <div class="form-group row">
                <label for="idNombre" class="col-sm-3 col-form-label">Correo</label>
                <div class="col-sm-9">
                    <input type="email" class="form-control" id="idCorreo" name="idCorreo" placeholder="Email" value="{{$usuario->user_correo}}" required>
                </div>
            </div>
            <div class="form-group row">
                <label for="idEstado" class="col-sm-2 col-form-label">Estado</label>
                <div class="col-sm-10">
                    <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                        @if($usuario->user_estado=="1")
                        <input type="checkbox" class="custom-control-input" id="idEstado" name="idEstado" checked>
                        @else
                        <input type="checkbox" class="custom-control-input" id="idEstado" name="idEstado">
                        @endif
                        <label class="custom-control-label" for="idEstado"></label>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.card-body -->
    </div>
<!-- /.card -->
</form>
@endsection