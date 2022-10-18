@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">¿Esta seguro de eliminar la tipo de movimiento de Empleado?</h3>
        <div class="float-right">
            <form class="form-horizontal" method="POST" action="{{ route('tipoMovimientoEmpleado.destroy', [$tipoMovimientoEmpleado->tipo_id]) }}">
                @method('DELETE')
                @csrf
                <button type="submit" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i>&nbsp;Eliminar</button>
                <!-- 
                <button type="button" onclick='window.location = "{{ url("tipoMovimientoEmpleado") }}";' class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
                --> 
                <button  type="button" onclick="history.back()" class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
            </form>
        </div>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <div class="card-body">
           
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Nombre</label>
                <div class="col-sm-10">
                    <label class="form-control">{{$tipoMovimientoEmpleado->tipo_nombre}}</label>
                </div>
            </div>
            @if(Auth::user()->empresa->empresa_contabilidad == '1')
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Cuenta Contable</label>
                <div class="col-sm-10">
                    <label class="form-control">@if($tipoMovimientoEmpleado->cuenta) {{$tipoMovimientoEmpleado->cuenta->cuenta_numero.'  -  '.$tipoMovimientoEmpleado->cuenta->cuenta_nombre}} @else SIN CUENTA @endif</label>
                </div>
            </div>  
            @endif  
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Estado</label>
                <div class="col-sm-10">
                    @if($tipoMovimientoEmpleado->tipo_estado=="1")
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