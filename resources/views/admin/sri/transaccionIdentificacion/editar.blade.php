@extends ('admin.layouts.admin')
@section('principal')
<form class="form-horizontal" method="POST" action="{{ route('transaccionIdentificacion.update', [$transaccionIdentificacion->transaccion_id]) }}">
@method('PUT')
@csrf
    <div class="card card-secondary">
        <div class="card-header">
            <h3 class="card-title">Editar Transacción</h3>
            <div class="float-right">
                <button type="submit" class="btn btn-success btn-sm"><i class="fa fa-save"></i>&nbsp;Guardar</button>
                 <!--
                <button type="button" onclick='window.location = "{{ url("transaccionIdentificacion") }}";' class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
                -->      
                <button  type="button" onclick="history.back()" class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button> 
            </div>
        </div>
        <div class="card-body">
            <div class="form-group row">
                <label for="idCodigo" class="col-sm-2 col-form-label">Codigo</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="idCodigo" name="idCodigo" value="{{$transaccionIdentificacion->transaccion_codigo}}" required>
                </div>
            </div>
            <div class="form-group row">
                <label for="idTipoTransaccion" class="col-sm-2 col-form-label">Transaccion</label>
                <div class="col-sm-10">
                    <select class="form-control select2" style="width: 100%;" id="idTipoTransaccion" name="idTipoTransaccion" require>
                        @foreach($tipoTransaccion as $tipoTransaccion)
                            @if($tipoTransaccion->tipo_transaccion_id == $transaccionIdentificacion->tipo_transaccion_id)
                                <option value="{{$tipoTransaccion->tipo_transaccion_id}}" selected>{{$tipoTransaccion->tipo_transaccion_nombre}}</option>
                            @else
                                <option value="{{$tipoTransaccion->tipo_transaccion_id}}">{{$tipoTransaccion->tipo_transaccion_nombre}}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <label for="idTipoIdentificacion" class="col-sm-2 col-form-label">Identificacion</label>
                <div class="col-sm-10">
                    <select class="form-control select2" style="width: 100%;" id="idTipoIdentificacion" name="idTipoIdentificacion" require>
                        @foreach($tipoIdentificacion as $tipoIdentificacion)
                            @if($tipoIdentificacion->tipo_identificacion_id == $transaccionIdentificacion->tipo_identificacion_id)
                                <option value="{{$tipoIdentificacion->tipo_identificacion_id}}" selected>{{$tipoIdentificacion->tipo_identificacion_nombre}}</option>
                            @else
                                <option value="{{$tipoIdentificacion->tipo_identificacion_id}}">{{$tipoIdentificacion->tipo_identificacion_nombre}}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <label for="idEstado" class="col-sm-2 col-form-label">Estado</label>
                <div class="col-sm-10">
                    <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                        @if($transaccionIdentificacion->transaccion_estado=="1")
                        <input type="checkbox" class="custom-control-input" id="idEstado" name="idEstado" checked>
                        @else
                        <input type="checkbox" class="custom-control-input" id="idEstado" name="idEstado">
                        @endif
                        <label class="custom-control-label" for="idEstado"></label>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection