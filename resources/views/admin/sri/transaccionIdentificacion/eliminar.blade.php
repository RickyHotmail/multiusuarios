@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">¿Esta seguro de eliminar la transacción?</h3>
        <div class="float-right">
            <form class="form-horizontal" method="POST" action="{{ route('transaccionIdentificacion.destroy', [$transaccionIdentificacion->transaccion_id]) }}">
                @method('DELETE')
                @csrf
                <button type="submit" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i>&nbsp;Eliminar</button>
                 <!--
                <button type="button" onclick='window.location = "{{ url("transaccionIdentificacion") }}";' class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
                -->      
                <button  type="button" onclick="history.back()" class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button> 
            </form>
        </div>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <div class="card-body">
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Codigo</label>
                <div class="col-sm-10">
                    <label class="form-control">{{$transaccionIdentificacion->transaccion_codigo}}</label>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Transaccion</label>
                <div class="col-sm-10">
                    <label class="form-control">{{$transaccionIdentificacion->tipo_transaccion_nombre}}</label>
                </div>
            </div> 
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Identificacion</label>
                <div class="col-sm-10">
                    <label class="form-control">{{$transaccionIdentificacion->tipo_identificacion_nombre}}</label>
                </div>
            </div>                    
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Estado</label>
                <div class="col-sm-10">
                    @if($transaccionIdentificacion->transaccion_estado=="1")
                    <i class="fa fa-check-circle neo-verde"></i>
                    @else
                    <i class="fa fa-times-circle neo-rojo"></i>
                    @endif
                </div>
            </div>
        </div>
        <!-- /.card-body -->    
    </div>
    <!-- /.card-body -->
</div>
<!-- /.card -->
@endsection