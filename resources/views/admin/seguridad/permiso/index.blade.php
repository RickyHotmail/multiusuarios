@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">Permisos</h3>
        <button class="btn btn-default btn-sm float-right" data-toggle="modal" data-target="#modal-nuevo"><i class="fa fa-plus"></i>&nbsp;Nuevo</button>
        <a class="btn btn-success btn-sm float-right" href="{{ url("excelpermisos") }}"><i class="fas fa-file-excel"></i>&nbsp;Cargar Excel</a>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <table id="example1" class="table table-bordered table-hover table-responsive sin-salto">
            <thead>
                <tr class="text-center neo-fondo-tabla">
                    <th></th>
                    <th>Nombre Permiso</th>
                    <th>Ruta Permiso</th>
                    <th>Tipo Permiso</th>
                    <th>Icono</th>
                    <th>Orden</th>   
                    <th>Tipo</th>                       
                    <th>Grupo</th>
                    
                </tr>
            </thead> 
            <tbody>
                @foreach($permisos as $permisos)
                <tr class="text-center">
                    <td>
                        <a href="{{ url("permiso/{$permisos->permiso_id}/edit") }}" class="btn btn-xs btn-primary"  data-toggle="tooltip" data-placement="top" title="Editar"><i class="fa fa-edit" aria-hidden="true"></i></a>
                        <a href="{{ url("permiso/{$permisos->permiso_id}") }}" class="btn btn-xs btn-success"  data-toggle="tooltip" data-placement="top" title="Ver"><i class="fa fa-eye" aria-hidden="true"></i></a>
                        <a href="{{ url("permiso/{$permisos->permiso_id}/eliminar") }}" class="btn btn-xs btn-danger"  data-toggle="tooltip" data-placement="top" title="Eliminar"><i class="fa fa-trash" aria-hidden="true"></i></a>
                    </td>
                    <td>{{ $permisos->permiso_nombre}}</td>
                    <td>{{ $permisos->permiso_ruta}}</td>
                    <td>@if($permisos->permiso_tipo=='1') Administrador @endif @if($permisos->permiso_tipo=='2') Cliente @endif</td>
                    <td>{{ $permisos->permiso_icono}}</td>
                    <td>{{ $permisos->permiso_orden}}</td>    
                    <td>@if(isset($permisos->tipo)){{ $permisos->tipo->tipo_nombre}}@endif</td>                   
                    <td>{{ $permisos->grupo->grupo_nombre}}</td>                    
                                     
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
                <h4 class="modal-title">Nuevo Permiso</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form-horizontal" method="POST" action="{{ url("permiso") }}">
            @csrf
                <div class="modal-body">
                    <div class="card-body">
                        <div class="form-group row">
                            <label for="idNombre" class="col-sm-3 col-form-label">Nombre de Permiso</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="idNombre" name="idNombre" placeholder="Nombre" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="idRuta" class="col-sm-3 col-form-label">Nombre de Ruta</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="idRuta" name="idRuta" placeholder="Ruta" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="idTipo" class="col-sm-3 col-form-label">Tipo de Permiso</label>
                            <div class="col-sm-9">
                                <select class="custom-select" id="idTipo" name="idTipo" require>
                                    <option value="1">Administrador</option>
                                    <option value="2">Cliente</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="idIcono" class="col-sm-3 col-form-label">Icono</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="idIcono" name="idIcono" placeholder="Ej. fa fa-circle" required> 
                            </div>
                        </div> 
                        <div class="form-group row">
                            <label for="idOrden" class="col-sm-3 col-form-label">Orden</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="idOrden" name="idOrden" value="1" required> 
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="idGrupo" class="col-sm-3 col-form-label">Grupo</label>
                            <div class="col-sm-9">
                                <select class="form-control select2" id="idGrupo" name="idGrupo" require>
                                    @foreach($gruposPers as $grupo)
                                    <option value="{{$grupo->grupo_id}}">{{$grupo->grupo_nombre}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="Tipo_grupo" class="col-sm-3 col-form-label">Tipo Grupo</label>
                            <div class="col-sm-9">
                                <select class="form-control select2" id="Tipo_grupo" name="Tipo_grupo" required>
                                    <option value="null" disabled>--Seleccione una opcion--</option>         
                                    <option value="Mantenimientos">Mantenimientos</option>
                                    <option value="Transacciones">Transacciones</option>
                                    <option value="Reportes y Consultas">Reportes y Consultas</option>
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