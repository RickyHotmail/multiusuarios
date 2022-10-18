@extends ('admin.layouts.admin')
@section('principal')
<form class="form-horizontal" method="POST" action="{{ route('rol.update', [$rol->rol_id]) }}">
@method('PUT')
@csrf
    <div class="card card-secondary">
        <div class="card-header">
            <h3 class="card-title">Editar Rol</h3>
            <div class="float-right">
                <button type="submit" class="btn btn-success btn-sm"><i class="fa fa-save"></i>&nbsp;Guardar</button>
                <!-- 
                <button type="button" onclick='window.location = "{{ url("rol") }}";' class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
                --> 
                <button  type="button" onclick="history.back()" class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
            </div>
        </div>
        <!-- /.card-header -->
        <div class="card-body">
            <div class="card-body">
                <div class="form-group row">
                    <label for="idNombre" class="col-sm-2 col-form-label">Nombre del Rol</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="idNombre" name="idNombre" placeholder="Nombre"
                            value="{{$rol->rol_nombre}}" required>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="idTipo" class="col-sm-2 col-form-label">Tipo de Rol</label>
                    <div class="col-sm-10">
                        <select class="custom-select" id="idTipo" name="idTipo" value="{{$rol->rol_tipo}}" require>
                            @if($rol->rol_tipo == 1)<option value="1" selected>Administrador</option>@else <option value="1">Administrador</option>@endif
                            <!--@if($rol->rol_tipo == 0)<option value="0" selected>Cliente</option>@else <option value="0">Cliente</option>@endif-->
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="idEstado" class="col-sm-2 col-form-label">Estado</label>
                    <div class="col-sm-10">
                        <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                            @if($rol->rol_estado=="1")
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
        <!-- /.card-body -->
    </div>
    <!-- /.card -->
</form>
@endsection