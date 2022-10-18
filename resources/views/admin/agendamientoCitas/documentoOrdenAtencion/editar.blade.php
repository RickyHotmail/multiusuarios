@extends ('admin.layouts.admin')
@section('principal')
<form class="form-horizontal" method="POST" action="{{ route('documentoOrdenAtencion.update', [$documentoOrdenA->documento_id]) }}">
@method('PUT')
@csrf
    <div class="card card-secondary">
        <div class="card-header">
            <h3 class="card-title">Editar Documento Orden Atencion</h3>
            <div class="float-right">
                <button type="submit" class="btn btn-success btn-sm"><i class="fa fa-save"></i>&nbsp;Guardar</button>
                <button type="button" onclick='window.location = "{{ url("documentoOrdenAtencion") }}";' class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
            </div>
        </div>
        <div class="card-body">
            <div class="form-group row">
                <label for="nombre" class="col-sm-3 col-form-label">Nombre del tipo de dependencia</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" id="nombre" name="nombre" value="{{$documentoOrdenA->documento_nombre}}" placeholder="Nombre de la Entidad" required>
                </div>
            </div>
            <div class="form-group row">
                <label for="estado" class="col-sm-3 col-form-label">Estado</label>
                <div class="col-sm-9">
                    <div class="col-form-label custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                        @if($documentoOrdenA->documento_estado=="1")
                        <input type="checkbox" class="custom-control-input" id="estado" name="estado" checked>
                        @else
                        <input type="checkbox" class="custom-control-input" id="estado" name="estado">
                        @endif
                        <label class="custom-control-label" for="estado"></label>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection