@extends ('admin.layouts.admin')
@section('principal')
<form class="form-horizontal" method="POST" action="{{ route('tipoCliente.update', [$tipoClien->tipo_cliente_id]) }}">
@method('PUT')
@csrf
    <div class="card card-secondary">
        <div class="card-header">
            <h3 class="card-title">Editar Tipo Cliente</h3>
            <div class="float-right">
                <button type="submit" class="btn btn-success btn-sm"><i class="fa fa-save"></i>&nbsp;Guardar</button>
                <!--
                <button type="button" onclick='window.location = "{{ url("tipoCliente") }}";' class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
                -->      
                <button  type="button" onclick="history.back()" class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button> 
            </div>
        </div>
        <div class="card-body">
            <div class="form-group row">
                <label for="tipo_cliente_nombre" class="col-sm-2 col-form-label">Nombre</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="tipo_cliente_nombre" name="tipo_cliente_nombre" placeholder="Tipo" value="{{$tipoClien->tipo_cliente_nombre}}" required>
                </div>
            </div>                                              
            <div class="form-group row">
                <label for="tipo_cliente_estado" class="col-sm-2 col-form-label">Estado</label>
                <div class="col-sm-10">
                    <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                        @if($tipoClien->tipo_cliente_estado=="1")
                            <input type="checkbox" class="custom-control-input" id="tipo_cliente_estado" name="tipo_cliente_estado" checked>
                        @else
                            <input type="checkbox" class="custom-control-input" id="tipo_cliente_estado" name="tipo_cliente_estado">
                        @endif
                        <label class="custom-control-label" for="tipo_cliente_estado"></label>
                    </div>
                </div>                
            </div> 
        </div>            
    </div>
</form>
@endsection