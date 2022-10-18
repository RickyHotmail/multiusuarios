@extends ('admin.layouts.admin')
@section('principal')
<form class="form-horizontal" method="POST" action="{{ route('sucursal.update', [$sucursal->sucursal_id]) }}">
@method('PUT')
@csrf
    <div class="card card-secondary">
        <div class="card-header">
            <h3 class="card-title"> Editar sucursal</h3>
            <div class="float-right">
                <button type="submit" class="btn btn-success btn-sm"><i class="fa fa-save"></i>&nbsp;Guardar</button>
                 <!--     
                <button type="button" onclick='window.location = "{{ url("sucursal") }}";' class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
                --> 
            <button  type="button" onclick="history.back()" class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
            </div>
        </div>
        <div class="card-body">               
            <div class="form-group row">
                <label for="sucursal_nombre" class="col-sm-2 col-form-label">Nombre Sucursal</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="sucursal_nombre" name="sucursal_nombre" placeholder="Nombre Sucursal" value="{{$sucursal->sucursal_nombre}}" required>
                </div>
            </div>  
            <div class="form-group row">
                <label for="sucursal_codigo" class="col-sm-2 col-form-label">Código Sucursal</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="sucursal_codigo" name="sucursal_codigo" placeholder="001" value="{{$sucursal->sucursal_codigo}}" required>
                </div>
            </div>                
            <div class="form-group row">
                <label for="sucursal_direccion" class="col-sm-2 col-form-label">Dirección</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="sucursal_direccion" name="sucursal_direccion" placeholder="Direccion " value="{{$sucursal->sucursal_direccion}}" required> 
                </div>
            </div>
            <div class="form-group row">
                <label for="sucursal_telefono" class="col-sm-2 col-form-label">Teléfono</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="sucursal_telefono" name="sucursal_telefono" placeholder="Ej. 022999999" value="{{$sucursal->sucursal_telefono}}" required>
                </div>
            </div>
            <div class="form-group row">
                <label for="sucursal_estado" class="col-sm-2 col-form-label">Estado</label>
                <div class="col-sm-10">
                    <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                        @if($sucursal->sucursal_estado=="1") 
                            <input type="checkbox" class="custom-control-input" id="sucursal_estado" name="sucursal_estado" checked>
                        @else
                            <input type="checkbox" class="custom-control-input" id="sucursal_estado" name="sucursal_estado">
                        @endif
                        <label class="custom-control-label" for="sucursal_estado"></label>
                    </div>
                </div>
            </div>             
        </div>
    </div>
</form>
@endsection