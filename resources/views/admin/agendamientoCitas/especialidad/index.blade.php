@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">Especialidad</h3>
        <button class="btn btn-default btn-sm float-right" data-toggle="modal" data-target="#modal-nuevo"><i class="fa fa-plus"></i>&nbsp;Nuevo</button>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <table id="example1" class="table table-bordered table-hover table-responsive sin-salto">
            <thead>
                <tr class="text-center neo-fondo-tabla">  
                    <th></th>
                    <th>Codigo</th>
                    <th>Nombre</th>
                    <th>Tipo</th> 
                    <th>Duración</th>
                    <th>Duración Flexible</th>                                                             
                </tr>
            </thead>            
            <tbody>
                @foreach($especialidades as $especialidad)
                <tr class="text-center">
                    <td>
                        <a href="{{ url("especialidad/{$especialidad->especialidad_id}/edit")}}" class="btn btn-xs btn-primary"  data-toggle="tooltip" data-placement="top" title="Editar"><i class="fa fa-edit" aria-hidden="true"></i></a>
                        <a href="{{ url("especialidad/{$especialidad->especialidad_id}")}}" class="btn btn-xs btn-success"  data-toggle="tooltip" data-placement="top" title="Ver"><i class="fa fa-eye" aria-hidden="true"></i></a>
                        <a href="{{ url("especialidad/{$especialidad->especialidad_id}/eliminar")}}" class="btn btn-xs btn-danger"  data-toggle="tooltip" data-placement="top" title="Eliminar"><i class="fa fa-trash" aria-hidden="true"></i></a>
                        <a href="{{ url("especialidad/configuracionEspecialidad/{$especialidad->especialidad_id}")}}" class="btn btn-xs btn-secondary"  data-toggle="tooltip" data-placement="top" title="Configurar"><i class="fa fa-cogs" aria-hidden="true"></i></a>
                        <a href="{{ url("especialidad/signose/{$especialidad->especialidad_id}")}}" class="btn btn-xs btn-info"  data-toggle="tooltip" data-placement="top" title="Signos Vitales"><i class="fa fa-heartbeat" aria-hidden="true"></i></a>
                    </td>
                    <td>{{ $especialidad->especialidad_codigo }}</td>
                    <td>{{ $especialidad->especialidad_nombre }}</td>
                    <td>
                        @if($especialidad->especialidad_tipo =='1')ESPECIALISTA 
                        @elseif ($especialidad->especialidad_tipo =='2')GENERAL 
                        @elseif ($especialidad->especialidad_tipo =='3')ODONTOLOGIA                            
                        @endif
                    </td>  
                    <td>{{ $especialidad->especialidad_duracion.' min' }}</td> 
                    <td>
                        @if($especialidad->especialidad_flexible=="1")
                            <i class="fa fa-check-circle neo-verde"></i>
                        @else
                            <i class="fa fa-times-circle neo-rojo"></i>
                        @endif
                    </td>                                  
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
                <h4 class="modal-title">Nueva Especiadlidad</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form-horizontal" method="POST" action="{{ url("especialidad") }}">
                @csrf
                <div class="modal-body">
                    <div class="card-body">
                    <div class="form-group row">
                            <label for="idCodigo" class="col-sm-3 col-form-label">Codigo</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="idCodigo" name="idCodigo" placeholder="QWS125" required>
                            </div>
                        </div> 
                        <div class="form-group row">
                            <label for="especialidad_nombre" class="col-sm-3 col-form-label">Nombre</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="especialidad_nombre" name="especialidad_nombre" placeholder="Nombre" required>
                            </div>
                        </div>                        
                        <div class="form-group row">
                            <label for="especialidad_tipo" class="col-sm-3 col-form-label">Tipo</label>
                            <div class="col-sm-9">
                                <select class="custom-select" id="especialidad_tipo" name="especialidad_tipo" required>
                                    <option value="" label>--Seleccione una opcion--</option>
                                    <option value="1">ESPECIALISTA</option>
                                    <option value="2">GENERAL</option>
                                    <option value="3">ODONTOLOGIA</option> 
                                </select>
                            </div>
                        </div> 
                        <div class="form-group row">
                            <label for="especialidad_duracion" class="col-sm-3 col-form-label">Duración</label>
                            <div class="col-sm-3">
                                <select class="custom-select select2" id="especialidad_duracion" name="especialidad_duracion" required>
                                    <?php $count = 5; ?>
                                    @while($count <= 150)
                                    <option value="{{ $count }}">{{$count.' min'}}</option>
                                    <?php $count = $count +5; ?>
                                    @endwhile
                                </select>
                            </div>
                            <label for="especialidad_flexible" class="col-sm-3 col-form-label">Duración Flexible</label>
                            <div class="col-sm-3">
                                <input type="checkbox" name="especialidad_flexible" data-bootstrap-switch data-off-color="danger" data-on-color="success">
                            </div>
                        </div>                            
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