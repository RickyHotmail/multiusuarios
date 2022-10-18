@extends ('admin.layouts.admin')
@section('principal')
<form class="form-horizontal" method="POST" action="{{ route('tipoDependencia.update', [$tipoDependencia->tipod_id]) }}">
@method('PUT')
@csrf
    <div class="card card-secondary">
        <div class="card-header">
            <h3 class="card-title">Editar tipo de dependencia</h3>
            <div class="float-right">
                <button type="submit" class="btn btn-success btn-sm"><i class="fa fa-save"></i>&nbsp;Guardar</button>
                <button type="button" onclick='window.location = "{{ url("tipoDependencia") }}";' class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
            </div>
        </div>
        <div class="card-body">
            <div class="form-group row">
                <label for="codigo" class="col-sm-2 col-form-label">Código:</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="codigo" name="codigo" value="{{$tipoDependencia->tipod_codigo}}" placeholder="Código del tipo de dependencia" required>
                </div>
            </div>
            <div class="form-group row">
                <label for="nombre" class="col-sm-2 col-form-label">Nombre:</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="nombre" name="nombre" value="{{$tipoDependencia->tipod_nombre}}" placeholder="Nombre del tipo de dependencia" required>
                </div>
            </div>
            <div class="form-group row">
                <label for="tipod_estado" class="col-sm-3 col-form-label">Estado</label>
                <div class="col-sm-9">
                    <div class="col-form-label custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                        @if($tipoDependencia->tipod_estado=="1")
                        <input type="checkbox" class="custom-control-input" id="tipod_estado" name="tipod_estado" checked>
                        @else
                        <input type="checkbox" class="custom-control-input" id="tipod_estado" name="tipod_estado">
                        @endif
                        <label class="custom-control-label" for="tipod_estado"></label>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection