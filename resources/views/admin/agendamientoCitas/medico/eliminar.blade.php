@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">¿Esta seguro de eliminar al Médico?</h3>
        <div class="float-right">
            <form class="form-horizontal" method="POST" action="{{ route('medico.destroy', [$medico->medico_id]) }}">
                @method('DELETE')
                @csrf
                <button type="submit" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i>&nbsp;Eliminar</button>
                <button type="button" onclick='window.location = "{{ url("medico") }}";' class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
            </form>
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
            <label class="col-sm-2 col-form-label">Estado</label>
            <div class="col-sm-10 col-form-label">
                @if($medico->medico_estado=="1")
                    <i class="fa fa-check-circle neo-verde"></i>
                @else
                    <i class="fa fa-times-circle neo-rojo"></i>
                @endif
            </div>
        </div>                                      
    </div>
</div>
@endsection