@extends ('admin.layouts.admin')
@section('principal')
<form class="form-horizontal" method="POST" action="{{ route('entidad.update', [$entidad->entidad_id]) }}">
@method('PUT')
@csrf
    <div class="card card-secondary">
        <div class="card-header">
            <h3 class="card-title">Editar Entidad</h3>
            <div class="float-right">
                <button type="submit" class="btn btn-success btn-sm"><i class="fa fa-save"></i>&nbsp;Guardar</button>
                <button type="button" onclick='window.location = "{{ url("entidad") }}";' class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
            </div>
        </div>
        <div class="card-body">
            <div class="form-group row">
                <label for="idNombre" class="col-sm-3 col-form-label">Nombre de la Entidad</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" id="idNombre" name="idNombre" value="{{$entidad->entidad_nombre}}" placeholder="Nombre de la Entidad" required>
                </div>
            </div>
            <div class="form-group row">
                <label for="idNombre" class="col-sm-3 col-form-label">Seleccionar Aseguradora</label>
                <div class="col-sm-9">
                    <ul class="list-group">
                        @foreach($clientesAseguradoras as $clientesAseguradora)
                            @if($clientesAseguradora->tipo_cliente_nombre == "Aseguradora")
                                <?php $entidada_estado = 0 ?>
                                @foreach($entidad->aseguradoras as $entidadA)
                                    @if($entidadA->cliente_id == $clientesAseguradora->cliente_id)
                                    <?php $entidada_estado = 1 ?>
                                    @endif
                                    @endforeach
                                @if($entidada_estado==1)
                                    <li class="list-group-item"><div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input" id="{{ $clientesAseguradora->cliente_id}}" name="{{ $clientesAseguradora->cliente_id}}" checked><label for="{{ $clientesAseguradora->cliente_id}}" class="custom-control-label"> {{ $clientesAseguradora->cliente_nombre}}</label></div></li>
                                @else
                                    <li class="list-group-item"><div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input" id="{{ $clientesAseguradora->cliente_id}}" name="{{ $clientesAseguradora->cliente_id}}"><label for="{{ $clientesAseguradora->cliente_id}}" class="custom-control-label"> {{ $clientesAseguradora->cliente_nombre}}</label></div></li>
                                @endif
                            @endif
                        @endforeach
                    </ul>
                </div>
            </div>
            <div class="form-group row">
                <label for="idEntidadEstado" class="col-sm-3 col-form-label">Estado</label>
                <div class="col-sm-9">
                    <div class="col-form-label custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                        @if($entidad->entidad_estado=="1")
                        <input type="checkbox" class="custom-control-input" id="idEntidadEstado" name="idEntidadEstado" checked>
                        @else
                        <input type="checkbox" class="custom-control-input" id="idEntidadEstado" name="idEntidadEstado">
                        @endif
                        <label class="custom-control-label" for="idEntidadEstado"></label>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection