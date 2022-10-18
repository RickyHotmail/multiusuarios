@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">Tipo de Empleado</h3>
        <!-- 
        <button onclick='window.location = "{{ url("tipoEmpleado") }}";' class="btn btn-default btn-sm float-right"><i class="fa fa-undo"></i>&nbsp;Atras</button>
        --> 
        <button  type="button" onclick="history.back()" class="btn btn-default btn-sm float-right"><i class="fa fa-undo"></i>&nbsp;Atras</button>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <div class="form-group row">
            <label for="idCodigo" class="col-sm-2 col-form-label">Descripcion</label>
            <div class="col-sm-10">
                <label class="form-control">{{ $tipoEmpleado->tipo_descripcion }}</label>
            </div>
        </div>
        <div class="form-group row">
            <label for="idNombre" class="col-sm-2 col-form-label">Categoría</label>
            <div class="col-sm-10">
                <label class="form-control">
                    @if($tipoEmpleado->tipo_categoria == 'ADMINISTRATIVO') ADMINISTRATIVO @endif
                    @if($tipoEmpleado->tipo_categoria == 'OPERATIVO') OPERATIVO @endif
                    @if($tipoEmpleado->tipo_categoria == 'OPERATIVO CONTROL DIAS') OPERATIVO CONTROL DIAS @endif
                </label>
            </div>
        </div>
        <div class="form-group row">
            <label for="tipo_estado" class="col-sm-2 col-form-label">Estado</label>
            <div class="col-sm-10">
                <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                    @if($tipoEmpleado->tipo_estado=="1")
                    <i class="fa fa-check-circle neo-verde"></i>
                    @else
                    <i class="fa fa-times-circle neo-rojo"></i>
                    @endif
                </div>
            </div>
        </div>
        <div class="form-group row">
            <label for="idNombre" class="col-sm-2 col-form-label">Sucursal</label>
            <div class="col-sm-10">
                <label class="form-control">
                    @if(isset($tipoEmpleado->sucursal_id)) {{$tipoEmpleado->sucursal->sucursal_nombre}} @endif
                </label>
            </div>
        </div>
        <div class="form-group row">
            <div class="col-sm-2"> </div>
            <label class="col-sm-4 col-form-label">
                <CENTER>CUENTA DEBE</CENTER>
            </label>
            <label class="col-sm-4 col-form-label">
                <CENTER>CUENTA HABER</CENTER>
            </label>
            <label class="col-sm-2 col-form-label">
                <CENTER>CATEGORIA</CENTER>
            </label>
        </div>
        <div class="form-group row">
            @foreach($tipoEmpleado->detalles as $detalle)
            <label class="col-sm-2 col-form-label">{{ $detalle->rubro->rubro_descripcion }}</label>
            <div class="col-sm-4">
                <label class="form-control"> @if($detalle->cuentaDebe != null) {{ $detalle->cuentaDebe->cuenta_numero.' - '.$detalle->cuentaDebe->cuenta_nombre}}  @else SIN CUENTA  @endif</label>
            </div>
            <div class="col-sm-4">
                <label class="form-control"> @if($detalle->cuentaHaber != null) {{ $detalle->cuentaHaber->cuenta_numero.' - '.$detalle->cuentaHaber->cuenta_nombre}}  @else SIN CUENTA  @endif</label>
            </div>
            <div class="col-sm-2">
                <label class="form-control"> @if($detalle->categoria_id != null) {{ $detalle->categoria->categoria_nombre}}  @else SIN CATEGORIA  @endif</label>
            </div>
            @endforeach

        </div>
    </div>
    <!-- /.card-body -->
</div>
<!-- /.card -->
@endsection