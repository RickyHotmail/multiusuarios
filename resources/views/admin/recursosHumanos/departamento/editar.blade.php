@extends ('admin.layouts.admin')
@section('principal')
<form class="form-horizontal" method="POST" action="{{ route('departamento.update', [$departamento->departamento_id]) }}">
@method('PUT')
@csrf
    <div class="card card-secondary">
        <div class="card-header">
            <h3 class="card-title">Editar Departamento</h3>
            <div class="float-right">
                <button type="submit" class="btn btn-success btn-sm"><i class="fa fa-save"></i>&nbsp;Guardar</button>
                <button type="button" onclick="window.location = '/departamento';" class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
            </div>
        </div>
        <div class="card-body">
            <div class="form-group row">
                <label for="departamento_nombre" class="col-sm-2 col-form-label">Nombre</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="departamento_nombre" name="departamento_nombre" placeholder="Departamento" value="{{$departamento->departamento_nombre}}" required>
                </div>
            </div>
            <div class="form-group row">
                <label for="sucursal_id" class="col-sm-2 col-form-label">Sucursal</label>
                <div class="col-sm-10">
                    <select class="custom-select select2" id="sucursal_id" name="sucursal_id" require>
                        @foreach($sucursales as $sucursal)
                            @if($sucursal->sucursal_id == $departamento->sucursal_id)
                                <option value="{{$sucursal->sucursal_id}}" selected>{{$sucursal->sucursal_nombre}}</option>
                            @else 
                                <option value="{{$sucursal->sucursal_id}}">{{$sucursal->sucursal_nombre}}</option>
                            @endif
                        @endforeach
                    </select>
                </div>                    
            </div>                                                   
            <div class="form-group row">
                <label for="departamento_estado" class="col-sm-2 col-form-label">Estado</label>
                <div class="col-sm-10">
                    <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                        @if($departamento->departamento_estado=="1")
                            <input type="checkbox" class="custom-control-input" id="departamento_estado" name="departamento_estado" checked>
                        @else
                            <input type="checkbox" class="custom-control-input" id="departamento_estado" name="departamento_estado">
                        @endif
                        <label class="custom-control-label" for="departamento_estado"></label>
                    </div>
                </div>                
            </div> 
        </div>            
    </div>
</form>
@endsection