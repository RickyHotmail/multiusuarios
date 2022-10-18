@extends ('admin.layouts.admin')
@section('principal')
<form class="form-horizontal" method="POST" action="{{ route('formaPago.update', [$formaPago->forma_pago_id]) }}">
@method('PUT')
@csrf
    <div class="card card-secondary">
        <div class="card-header">
            <h3 class="card-title">Editar Forma de Pago</h3>
            <div class="float-right">
                <button type="submit" class="btn btn-success btn-sm"><i class="fa fa-save"></i>&nbsp;Guardar</button>
                 <!--
                <button type="button" onclick='window.location = "{{ url("formaPago") }}";' class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
                -->      
                <button  type="button" onclick="history.back()" class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button> 
            </div>
        </div>
        <div class="card-body">
            <div class="form-group row">
                <label for="forma_pago_codigo" class="col-sm-2 col-form-label">Codigo</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="forma_pago_codigo" name="forma_pago_codigo" placeholder="Codigo" maxlength="3" value="{{$formaPago->forma_pago_codigo}}" required>
                </div>
            </div>
            <div class="form-group row">
                <label for="forma_pago_nombre" class="col-sm-2 col-form-label">Nombre</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="forma_pago_nombre" name="forma_pago_nombre" placeholder="Nombre" value="{{$formaPago->forma_pago_nombre}}" required>
                </div>
            </div>               
            <div class="form-group row">
                <label for="forma_pago_estado" class="col-sm-2 col-form-label">Estado</label>
                <div class="col-sm-10">
                    <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                        @if($formaPago->forma_pago_estado=="1")
                            <input type="checkbox" class="custom-control-input" id="forma_pago_estado" name="forma_pago_estado" checked>
                        @else
                            <input type="checkbox" class="custom-control-input" id="forma_pago_estado" name="forma_pago_estado">
                        @endif
                        <label class="custom-control-label" for="forma_pago_estado"></label>
                    </div>
                </div>                
            </div> 
        </div>            
    </div>
</form>
@endsection