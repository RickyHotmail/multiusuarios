@extends ('admin.layouts.admin')
@section('principal')
<form class="form-horizontal" method="POST" action="{{ route('categoriaCliente.update', [$categoriaClien->categoria_cliente_id]) }}">
@method('PUT')
@csrf
    <div class="card card-secondary">
        <div class="card-header">
            <h3 class="card-title">Editar Categoria Cliente</h3>
            <div class="float-right">
                <button type="submit" class="btn btn-success btn-sm"><i class="fa fa-save"></i>&nbsp;Guardar</button>
                <!--
                <button type="button" onclick='window.location = "{{ url("categoriaCliente") }}";' class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
                -->      
                <button  type="button" onclick="history.back()" class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>  
            </div>
        </div>
        <div class="card-body">
            <div class="form-group row">
                <label for="categoria_cliente_nombre" class="col-sm-2 col-form-label">Nombre</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="categoria_cliente_nombre" name="categoria_cliente_nombre" placeholder="Categoria" value="{{$categoriaClien->categoria_cliente_nombre}}" required>
                </div>
            </div>                
            <div class="form-group row">
                <label for="categoria_cliente_descripcion" class="col-sm-2 col-form-label">Descripcion</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="categoria_cliente_descripcion" name="categoria_cliente_descripcion" placeholder="Ingrese aqui una descripcion" value="{{$categoriaClien->categoria_cliente_descripcion}}" required>
                </div>
            </div>                                                      
            <div class="form-group row">
                <label for="categoria_cliente_estado" class="col-sm-2 col-form-label">Estado</label>
                <div class="col-sm-10">
                    <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                        @if($categoriaClien->categoria_cliente_estado=="1")
                            <input type="checkbox" class="custom-control-input" id="categoria_cliente_estado" name="categoria_cliente_estado" checked>
                        @else
                            <input type="checkbox" class="custom-control-input" id="categoria_cliente_estado" name="categoria_cliente_estado">
                        @endif
                        <label class="custom-control-label" for="categoria_cliente_estado"></label>
                    </div>
                </div>                
            </div> 
        </div>            
    </div>
</form>
@endsection