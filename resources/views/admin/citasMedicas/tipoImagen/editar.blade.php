@extends ('admin.layouts.admin')
@section('principal')
<form class="form-horizontal" method="POST" action="{{ route('tipoImagen.update', [$tipoImagen->tipo_id]) }}">
@method('PUT')
@csrf
    <div class="card card-secondary">
        <div class="card-header">
            <h3 class="card-title">Editar Tipo de Imagen</h3>
            <div class="float-right">
                <button type="submit" class="btn btn-success btn-sm"><i class="fa fa-save"></i>&nbsp;Guardar</button>
                <button type="button" onclick='window.location = "{{ url("tipoImagen") }}";' class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
            </div>
        </div>
        <div class="card-body">
            <div class="form-group row">
                <label for="tipo_nombre" class="col-sm-2 col-form-label">Nombre</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="tipo_nombre" name="tipo_nombre" placeholder="Tipo" value="{{$tipoImagen->tipo_nombre}}" required>
                </div>
            </div>                                                     
            <div class="form-group row">
                <label for="tipo_estado" class="col-sm-2 col-form-label">Estado</label>
                <div class="col-sm-10">
                    <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                        @if($tipoImagen->tipo_estado=="1")
                            <input type="checkbox" class="custom-control-input" id="tipo_estado" name="tipo_estado" checked>
                        @else
                            <input type="checkbox" class="custom-control-input" id="tipo_estado" name="tipo_estado">
                        @endif
                        <label class="custom-control-label" for="tipo_estado"></label>
                    </div>
                </div>                
            </div> 
        </div>            
    </div>
</form>
@endsection