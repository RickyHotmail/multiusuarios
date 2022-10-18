@extends ('admin.layouts.admin')
@section('principal')
<form class="form-horizontal" method="POST" action="{{ route('grupo.update', [$grupo->grupo_id]) }}">
@method('PUT')
@csrf
    <div class="card card-secondary">
        <div class="card-header">
            <h3 class="card-title">Editar Grupo</h3>
            <div class="float-right">
                <button type="submit" class="btn btn-success btn-sm"><i class="fa fa-save"></i>&nbsp;Guardar</button>
                <!-- 
                <button type="button" onclick='window.location = "{{ url("grupo") }}";' class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
                --> 
                <button  type="button" onclick="history.back()" class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
            </div>
        </div>
        <div class="card-body">
            <div class="card-body">
                <div class="form-group row">
                    <label for="idNombre" class="col-sm-2 col-form-label">Nombre de Grupo</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="idNombre" name="idNombre" placeholder="Nombre" value="{{$grupo->grupo_nombre}}" required>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="idIcono" class="col-sm-2 col-form-label">Icono</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="idIcono" name="idIcono" placeholder="Ej. fa fa-circle" value="{{$grupo->grupo_icono}}" required>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="idOrden" class="col-sm-2 col-form-label">Orden de Grupo</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="idOrden" name="idOrden" value="{{$grupo->grupo_orden}}" required>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="idEstado" class="col-sm-2 col-form-label">Estado</label>
                    <div class="col-sm-10">
                        <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                            @if($grupo->grupo_estado=="1") 
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
    </div>
</form>
@endsection