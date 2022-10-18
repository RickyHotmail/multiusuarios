@extends ('admin.layouts.admin')
@section('principal')
<form class="form-horizontal" method="POST" action="{{ route('categoriaProducto.update', [$categoria->categoria_id]) }}">
@method('PUT')
@csrf
    <div class="card card-secondary">
        <div class="card-header">
            <h3 class="card-title">Editar Categoria Producto</h3>
            <div class="float-right">
                <button type="submit" class="btn btn-success btn-sm"><i class="fa fa-save"></i>&nbsp;Guardar</button>
                <!--   
                <button type="button" onclick='window.location = "{{ url("categoriaProducto") }}";' class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
                --> 
                <button  type="button" onclick="history.back()" class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
            </div>
        </div> 
        <div class="card-body">
            <div class="form-group row">
                <label for="categoria_nombre" class="col-sm-2 col-form-label">Nombre</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="categoria_nombre" name="categoria_nombre" placeholder="Categoria" value="{{$categoria->categoria_nombre}}" required>
                </div>
            </div>                
            <div class="form-group row">
                <label for="categoria_tipo" class="col-sm-2 col-form-label">Tipo de Categoria</label>
                <div class="col-sm-10">
                    <select class="custom-select" id="categoria_tipo" name="categoria_tipo" value="{{$categoria->categoria_tipo}}" require>
                        @if($categoria->categoria_tipo == 1)<option value="1" selected>Articulo</option>@else <option value="1">Articulo</option>@endif
                        @if($categoria->categoria_tipo == 2)<option value="2" selected>Servicio</option>@else <option value="2">Servicio</option>@endif
                    </select>
                </div>
            </div>                                                             
            <div class="form-group row">
                <label for="categoria_estado" class="col-sm-2 col-form-label">Estado</label>
                <div class="col-sm-10">
                    <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                        @if($categoria->categoria_estado=="1")
                            <input type="checkbox" class="custom-control-input" id="categoria_estado" name="categoria_estado" checked>
                        @else
                            <input type="checkbox" class="custom-control-input" id="categoria_estado" name="categoria_estado">
                        @endif
                        <label class="custom-control-label" for="categoria_estado"></label>
                    </div>
                </div>                
            </div> 
        </div>            
    </div>
</form>
@endsection