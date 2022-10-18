@extends ('admin.layouts.admin')
@section('principal')
<form class="form-horizontal" method="POST" action="{{ route('permiso.update', [$permiso->permiso_id]) }}">
@method('PUT')
@csrf
    <div class="card card-secondary">
        <div class="card-header">
            <h3 class="card-title">Editar Permiso</h3>
            <div class="float-right">
                <button type="submit" class="btn btn-success btn-sm"><i class="fa fa-save"></i>&nbsp;Guardar</button>
                <!-- 
                <button type="button" onclick='window.location = "{{ url("permiso") }}";' class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
                --> 
                <button  type="button" onclick="history.back()" class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
            </div>
        </div>
        <!-- /.card-header -->
        <div class="card-body">
            <div class="card-body">
                <div class="form-group row">
                    <label for="idNombre" class="col-sm-2 col-form-label">Nombre de Permiso</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="idNombre" name="idNombre" placeholder="Nombre" value="{{$permiso->permiso_nombre}}" required>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="idRuta" class="col-sm-2 col-form-label">Ruta</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="idRuta" name="idRuta" placeholder="Ej. fa fa-circle" value="{{$permiso->permiso_ruta}}" required>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="idTipo" class="col-sm-2 col-form-label">Tipo de Permiso</label>
                    <div class="col-sm-10">
                        <select class="custom-select" id="idTipo" name="idTipo" value="{{$permiso->permiso_tipo}}" require>
                            @if($permiso->permiso_tipo == 1)<option value="1" selected>Administrador</option>@else <option value="1">Administrador</option>@endif
                            @if($permiso->permiso_tipo == 0)<option value="0" selected>Cliente</option>@else <option value="0">Cliente</option>@endif
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="idIcono" class="col-sm-2 col-form-label">Icono</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="idIcono" name="idIcono" value="{{$permiso->permiso_icono}}" required> 
                    </div>
                </div>
                <div class="form-group row">
                    <label for="idOrden" class="col-sm-2 col-form-label">Orden de Permiso</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="idOrden" name="idOrden" value="{{$permiso->permiso_orden}}" required> 
                    </div>
                </div>
                <div class="form-group row">
                    <label for="idEstado" class="col-sm-2 col-form-label">Estado</label>
                    <div class="col-sm-10">
                        <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                            @if($permiso->permiso_estado=="1") 
                                <input type="checkbox" class="custom-control-input" id="idEstado" name="idEstado" checked>
                            @else
                                <input type="checkbox" class="custom-control-input" id="idEstado" name="idEstado">
                            @endif
                            <label class="custom-control-label" for="idEstado"></label>
                        </div>
                    </div>                
                </div> 
                <div class="form-group row">
                    <label for="idGrupo" class="col-sm-2 col-form-label">Grupo</label>
                    <div class="col-sm-10">
                        <select class="form-control select2" id="idGrupo" name="idGrupo" require>
                            @foreach($gruposPers as $grupo)
                                @if($grupo->grupo_id == $permiso->grupo_id)
                                    <option value="{{$grupo->grupo_id}}" selected>{{$grupo->grupo_nombre}}</option>
                                @else 
                                    <option value="{{$grupo->grupo_id}}">{{$grupo->grupo_nombre}}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div> 
                <div class="form-group row">
                    <label for="Tipo_grupo" class="col-sm-2 col-form-label">Tipo Grupo</label>
                    <div class="col-sm-10">
                        <select class="form-control select2" id="Tipo_grupo" name="Tipo_grupo" required>
                                <option value="null" disabled>--Seleccione una opcion--</option>
                                <option value="Mantenimientos" @if(isset($permiso->tipo)) @if($permiso->tipo->tipo_nombre == 'Mantenimientos') selected @endif @endif>Mantenimientos</option>
                                <option value="Transacciones" @if(isset($permiso->tipo)) @if($permiso->tipo->tipo_nombre == 'Transacciones') selected @endif @endif>Transacciones</option>
                                <option value="Reportes y Consultas" @if(isset($permiso->tipo)) @if($permiso->tipo->tipo_nombre == 'Reportes y Consultas') selected @endif @endif>Reportes y Consultas</option>
                               
                        </select>
                    </div>
                </div>              
            </div>            
        </div>
        <!-- /.card-body -->
    </div>
    <!-- /.card -->
</form>
@endsection