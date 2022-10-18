@extends ('admin.layouts.admin')
@section('principal')
<form class="form-horizontal" method="POST" action="{{ route('imagen.update', [$imagen->imagen_id]) }}">
@method('PUT')
@csrf
    <div class="card card-secondary">
        <div class="card-header">
            <h3 class="card-title">Editar Imagen</h3>
            <div class="float-right">
                <button type="submit" class="btn btn-success btn-sm"><i class="fa fa-save"></i>&nbsp;Guardar</button>
                <button type="button" onclick='window.location = "{{ url("imagen") }}";' class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
            </div>
        </div>
        <div class="card-body">
            <div class="form-group row">
                <label for="imagen_nombre" class="col-sm-2 col-form-label">Nombre de la Imagen</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="imagen_nombre" name="imagen_nombre" placeholder="Tipo" value="{{$imagen->imagen_nombre}}" required>
                </div>
            </div>  
            <div class="form-group row">
                <label for="tipo_id" class="col-sm-2 col-form-label">Tipo de Imagen</label>
                <div class="col-sm-10">
                    <select class="custom-select select2" id="tipo_id" name="tipo_id" required>
                        <option value="" label>--Seleccione una opcion--</option>
                        @foreach($tipoImagenes as $tipoImagen)
                            @if($tipoImagen->tipo_id == $imagen->tipo_id)
                                <option value="{{ $tipoImagen->tipo_id }}" selected>{{ $tipoImagen->tipo_nombre }}</option>   
                            @else
                                <option value="{{ $tipoImagen->tipo_id }}">{{ $tipoImagen->tipo_nombre }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
            </div>                                                    
            <div class="form-group row">
                <label for="imagen_estado" class="col-sm-2 col-form-label">Estado</label>
                <div class="col-sm-10">
                    <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                        @if($imagen->imagen_estado=="1")
                            <input type="checkbox" class="custom-control-input" id="imagen_estado" name="imagen_estado" checked>
                        @else
                            <input type="checkbox" class="custom-control-input" id="imagen_estado" name="imagen_estado">
                        @endif
                        <label class="custom-control-label" for="imagen_estado"></label>
                    </div>
                </div>                
            </div> 
        </div>            
    </div>
</form>
@endsection