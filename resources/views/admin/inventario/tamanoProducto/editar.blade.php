@extends ('admin.layouts.admin')
@section('principal')
<form class="form-horizontal" method="POST" action="{{ route('tamanoProducto.update', [$tamano->tamano_id]) }}">
@method('PUT')
@csrf
    <div class="card card-secondary">
        <div class="card-header">
            <h3 class="card-title">Editar Tamaño de Producto</h3>
            <div class="float-right">
                <button type="submit" class="btn btn-success btn-sm"><i class="fa fa-save"></i>&nbsp;Guardar</button>
                <button type="button" onclick='window.location = "{{ url("tamanoProducto") }}";' class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
            </div>
        </div>
        <div class="card-body">               
            <div class="form-group row">
                <label for="tamano_nombre" class="col-sm-2 col-form-label">Valor</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="tamano_nombre" name="tamano_nombre" placeholder="Tamaño" value="{{$tamano->tamano_nombre}}" required>
                </div>
            </div>                                               
            <div class="form-group row">
                <label for="tamano_estado" class="col-sm-2 col-form-label">Estado</label>
                <div class="col-sm-10">
                    <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                        @if($tamano->tamano_estado=="1")
                            <input type="checkbox" class="custom-control-input" id="tamano_estado" name="tamano_estado" checked>
                        @else
                            <input type="checkbox" class="custom-control-input" id="tamano_estado" name="tamano_estado">
                        @endif
                        <label class="custom-control-label" for="tamano_estado"></label>
                    </div>
                </div>                
            </div> 
        </div>            
    </div>
</form>
@endsection