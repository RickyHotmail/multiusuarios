@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">Entidad</h3>
        <button onclick='window.location = "{{ url("entidad") }}";' class="btn btn-default btn-sm float-right"><i class="fa fa-undo"></i>&nbsp;Atras</button>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <div class="form-group row">
            <label for="idNombre" class="col-sm-3 col-form-label">Nombre de la Entidad</label>
            <div class="col-sm-9">
                <label class="form-control">{{$entidad->entidad_nombre}}</label>
            </div>
        </div>
        <div class="form-group row">
            <label for="idNombre" class="col-sm-3 col-form-label">Seleccionar Aseguradora</label>
            <div class="col-sm-9">
                <ul class="list-group">
                    @foreach($clientesAseguradoras as $clientesAseguradora)
                        @if($clientesAseguradora->tipo_cliente_nombre == "Aseguradora")
                            @foreach($entidad->aseguradoras as $entidadA)
                                @if($entidadA->cliente_id == $clientesAseguradora->cliente_id)
                                    <li class="list-group-item"><div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input" id="{{ $clientesAseguradora->cliente_id}}" name="{{ $clientesAseguradora->cliente_id}}" checked disabled><label for="{{ $clientesAseguradora->cliente_id}}" class="custom-control-label"> {{ $clientesAseguradora->cliente_nombre}}</label></div></li>
                                @endif
                            @endforeach
                        @endif
                    @endforeach
                </ul>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-3 col-form-label">Estado</label>
            <div class="col-sm-9 col-form-label">
                @if($entidad->entidad_estado=="1")
                <i class="fa fa-check-circle neo-verde"></i>
                @else
                <i class="fa fa-times-circle neo-rojo"></i>
                @endif
            </div>
        </div>
    </div>
    <!-- /.card-body -->
    <!-- /.card-footer -->
</div>
<!-- /.card-body -->
</div>
<!-- /.card -->
@endsection