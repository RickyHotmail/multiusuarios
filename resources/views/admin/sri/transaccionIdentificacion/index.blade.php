@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">Transacción</h3>
        <button class="btn btn-default btn-sm float-right" data-toggle="modal" data-target="#modal-nuevo"><i class="fa fa-plus"></i>&nbsp;Nuevo</button>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <table id="example1" class="table table-bordered table-hover table-responsive sin-salto">
            <thead>
                <tr class="text-center neo-fondo-tabla">
                    <th></th>
                    <th>Codigo</th>
                    <th>Tipo de Transaccion</th>
                    <th>Tipo de Identificacion</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transaccionIdentificacion as $transaccionIdentificacion)
                <tr class="text-center">
                    <td>
                        <a href="{{ url("transaccionIdentificacion/{$transaccionIdentificacion->transaccion_id}/edit") }}" class="btn btn-xs btn-primary" data-toggle="tooltip" data-placement="top" title="Editar"><i class="fa fa-edit" aria-hidden="true"></i></a>
                        <a href="{{ url("transaccionIdentificacion/{$transaccionIdentificacion->transaccion_id}") }}" class="btn btn-xs btn-success" data-toggle="tooltip" data-placement="top" title="Ver"><i class="fa fa-eye" aria-hidden="true"></i></a>
                        <a href="{{ url("transaccionIdentificacion/{$transaccionIdentificacion->transaccion_id}/eliminar") }}" class="btn btn-xs btn-danger" data-toggle="tooltip" data-placement="top" title="Eliminar"><i class="fa fa-trash" aria-hidden="true"></i></a>
                    </td>
                    <td> {{$transaccionIdentificacion->transaccion_codigo}}</td>
                    <td> {{$transaccionIdentificacion->tipo_transaccion_nombre}}</td>
                    <td> {{$transaccionIdentificacion->tipo_identificacion_nombre}}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <!-- /.card-body -->
</div>
<!-- /.card -->
<div class="modal fade" id="modal-nuevo">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-secondary">
                <h4 class="modal-title">Nueva Transacción</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form-horizontal" method="POST" action="{{ url("transaccionIdentificacion") }}">
                @csrf
                <div class="modal-body">
                    <div class="card-body">
                        <div class="form-group row">
                            <label for="idCodigo" class="col-sm-3 col-form-label">Codigo</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="idCodigo" name="idCodigo" placeholder="Codigo" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="idTipoTransaccion" class="col-sm-3 col-form-label">Tipo de Transaccion</label>
                            <div class="col-sm-9">
                                <select class="form-control select2" style="width: 100%;" id="idTipoTransaccion" name="idTipoTransaccion" required>
                                    <option value="" label>--Seleccione una opcion--</option>
                                    @foreach($tipoTransaccion as $tipoTransaccion)
                                        <option value="{{$tipoTransaccion->tipo_transaccion_id}}">{{$tipoTransaccion->tipo_transaccion_nombre}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="idTipoIdentificacion" class="col-sm-3 col-form-label">Tipo de Identificacion</label>
                            <div class="col-sm-9">
                                <select class="form-control select2" style="width: 100%;" id="idTipoIdentificacion" name="idTipoIdentificacion" required>
                                    <option value="" label>--Seleccione una opcion--</option>
                                    @foreach($tipoIdentificacion as $tipoIdentificacion)
                                        <option value="{{$tipoIdentificacion->tipo_identificacion_id}}">{{$tipoIdentificacion->tipo_identificacion_nombre}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <!-- /.card-body -->
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Guardar</button>
                </div>
            </form>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->
@endsection