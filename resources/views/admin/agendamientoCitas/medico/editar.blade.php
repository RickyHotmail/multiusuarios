@extends ('admin.layouts.admin')
@section('principal')
<form class="form-horizontal" method="POST" action="{{ route('medico.update', [$medico->medico_id]) }}">
@method('PUT')
@csrf
    <div class="card card-secondary">
        <div class="card-header">
            <h3 class="card-title">Editar Medico</h3>
            <div class="float-right">
                <button type="submit" class="btn btn-success btn-sm"><i class="fa fa-save"></i>&nbsp;Guardar</button>
                <button type="button" onclick='window.location = "{{ url("medico") }}";' class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
            </div>
        </div>
        <div class="card-body">
            <div class="form-group row">
                <label for="producto_tipo" class="col-sm-2 col-form-label">Nombre</label>
                <div class="col-sm-10">
                    @if($medico->empleado_id != '')
                        <label class="form-control">{{$medico->empleado->empleado_nombre}}</label>
                    @else
                        <label class="form-control">{{$medico->proveedor->proveedor_nombre}}</label>
                    @endif
                </div>
            </div>  
            <div class="form-group row">
                <label for="usuario_id" class="col-sm-2 col-form-label">Usuario</label>
                <div class="col-sm-10">
                    <label class="form-control">{{$medico->usuario->user_nombre}}</label>
                </div>
            </div>                                              
            <div class="form-group row">
                <label for="especialidad_estado" class="col-sm-2 col-form-label">Estado</label>
                <div class="col-sm-10">
                    <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                        @if($medico->medico_estado=="1")
                            <input type="checkbox" class="custom-control-input" id="medico_estado" name="medico_estado" checked>
                        @else
                            <input type="checkbox" class="custom-control-input" id="medico_estado" name="medico_estado">
                        @endif
                        <label class="custom-control-label" for="medico_estado"></label>
                    </div>
                </div>                
            </div> 
        </div>                        
    </div>
</form>
@endsection