@extends ('admin.layouts.admin')
@section('principal')
<form class="form-horizontal" method="POST" action="{{ route('categoriaRol.update', [$categoria->categoria_id]) }}">
@method('PUT')
@csrf
    <div class="card card-secondary">
        <div class="card-header">
            <h3 class="card-title">Editar Categoria Rol</h3>
            <div class="float-right">
                <button type="submit" class="btn btn-success btn-sm"><i class="fa fa-save"></i>&nbsp;Guardar</button>
                <button type="button" onclick='window.location = "{{ url("categoriaRol") }}";' class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
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
                <label for="categoria_nombre" class="col-sm-2 col-form-label">Centro Cosumo</label>
                <div class="col-sm-10">
                    <select class="custom-select" id="consumo" name="consumo" required>
                        @foreach($consumos as $consumo)
                            <option value="{{$consumo->centro_consumo_id}}"  @if($consumo->centro_consumo_id==$categoria->centro_consumo_id) selected @endif >{{$consumo->centro_consumo_nombre}}</option>
                        @endforeach
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