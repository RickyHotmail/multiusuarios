@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">Entidades</h3>
        <button class="btn btn-default btn-sm float-right" data-toggle="modal" data-target="#modal-nuevo"><i class="fa fa-plus"></i>&nbsp;Nuevo</button>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <table id="example1" class="table table-bordered table-hover table-responsive sin-salto">
            <thead>
                <tr class="text-center neo-fondo-tabla">
                    <th></th>
                    <th>Entidad</th>
                </tr>
            </thead>
            <tbody>
                @foreach($entidades as $entidad)
                <tr class="text-center">
                    <td>
                        <a href="{{ url("entidad/{$entidad->entidad_id}/edit")}}" class="btn btn-xs btn-primary" data-toggle="tooltip" data-placement="top" title="Editar"><i class="fa fa-edit" aria-hidden="true"></i></a>
                        <a href="{{ url("entidad/{$entidad->entidad_id}")}}" class="btn btn-xs btn-success" data-toggle="tooltip" data-placement="top" title="Ver"><i class="fa fa-eye"></i></a>
                        <a href="{{ url("entidad/{$entidad->entidad_id}/eliminar")}}" class="btn btn-xs btn-danger" data-toggle="tooltip" data-placement="top" title="Eliminar"><i class="fa fa-trash"></i></a>
                    </td>
                    <td>{{ $entidad->entidad_nombre }}</td>
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
                <h4 class="modal-title">Nueva Entidad</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form-horizontal" method="POST" action="{{ url("entidad") }}">
                @csrf
                <div class="modal-body">
                    <div class="card-body">
                        <div class="form-group row">
                            <label for="idNombre" class="col-sm-2 col-form-label">Entidad</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="idNombre" name="idNombre" placeholder="Nombre de la Entidad" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Seleccionar Aseguradora</label>
                            <div class="well listview-pagupa">
                                <div class="">
                                    <ul class="list-group">
                                        @foreach($clientesAseguradoras as $clientesAseguradora)
                                            @if($clientesAseguradora->tipo_cliente_nombre == "Aseguradora")
                                                <?php $aseguradoraM_estado = 0 ?>
                                                @if($aseguradoraM_estado==1)
                                                <li class="list-group-item"><div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input" id="{{ $clientesAseguradora->cliente_id}}" name="{{ $clientesAseguradora->cliente_id}}" checked><label for="{{ $clientesAseguradora->cliente_id}}" class="custom-control-label"> {{ $clientesAseguradora->cliente_nombre}}</label></div></li>
                                                @else
                                                <li class="list-group-item"><div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input" id="{{ $clientesAseguradora->cliente_id}}" name="{{ $clientesAseguradora->cliente_id}}"><label for="{{ $clientesAseguradora->cliente_id}}" class="custom-control-label"> {{ $clientesAseguradora->cliente_nombre}}</label></div></li>
                                                @endif
                                            @endif
                                        @endforeach
                                    </ul>
                                </div>
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