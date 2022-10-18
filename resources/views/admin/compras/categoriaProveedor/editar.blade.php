@extends ('admin.layouts.admin')
@section('principal')
<form class="form-horizontal" method="POST" action="{{ route('categoriaProveedor.update', [$categoriaProv->categoria_proveedor_id]) }}">
@method('PUT')
@csrf
    <div class="card card-secondary">
        <div class="card-header">
            <h3 class="card-title">Editar Categoria de Proveedor</h3>
            <div class="float-right">
                <button type="submit" class="btn btn-success btn-sm"><i class="fa fa-save"></i>&nbsp;Guardar</button>
                 <!--     
                <button type="button" onclick='window.location = "{{ url("categoriaProveedor") }}";' class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
                --> 
                        <button  type="button" onclick="history.back()" class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
            </div>
        </div>
        <div class="card-body">
            <div class="form-group row">
                <label for="categoria_proveedor_nombre" class="col-sm-2 col-form-label">Nombre</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="categoria_proveedor_nombre" name="categoria_proveedor_nombre" placeholder="Categoria" value="{{$categoriaProv->categoria_proveedor_nombre}}" required>
                </div>
            </div>                
            <div class="form-group row">
                <label for="categoria_proveedor_descripcion" class="col-sm-2 col-form-label">Descripcion</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="categoria_proveedor_descripcion" name="categoria_proveedor_descripcion" placeholder="Ingrese aqui una descripcion" value="{{$categoriaProv->categoria_proveedor_descripcion}}" required>
                </div>
            </div>                                                      
            <div class="form-group row">
                <label for="categoria_estado" class="col-sm-2 col-form-label">Estado</label>
                <div class="col-sm-10">
                    <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                        @if($categoriaProv->categoria_proveedor_estado=="1")
                            <input type="checkbox" class="custom-control-input" id="categoria_proveedor_estado" name="categoria_proveedor_estado" checked>
                        @else
                            <input type="checkbox" class="custom-control-input" id="categoria_proveedor_estado" name="categoria_proveedor_estado">
                        @endif
                        <label class="custom-control-label" for="categoria_proveedor_estado"></label>
                    </div>
                </div>                
            </div> 
        </div>            
    </div>
</form>>
@endsection