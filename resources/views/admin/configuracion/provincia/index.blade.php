@extends ('admin.layouts.admin')
@section('principal')

<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">Provincia</h3>
        <div class="float-right">
            <button class="btn btn-default btn-sm float-right" data-toggle="modal" data-target="#modal-nuevo"><i class="fa fa-plus"></i>&nbsp;Nuevo</button>
            <a class="btn btn-success btn-sm" href="{{ url("excelProvincia") }}"><i class="fas fa-file-excel"></i>&nbsp;Cargar Excel</a>
        </div>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <table id="example1" class="table table-bordered table-hover table-responsive sin-salto">
            <thead>
                <tr class="text-center">
                    <th></th>
                    <th>Nombre de Provincia</th>
                    <th>Codigo de Provincia</th>
                    <th>Pais</th>
                </tr>
            </thead> 
            <tbody>
                @foreach($provincias as $provincia)
                <tr class="text-center">
                    <td>
                        <a href="{{ url("provincia/{$provincia->provincia_id}/edit") }}" class="btn btn-xs btn-primary"  data-toggle="tooltip" data-placement="top" title="Editar"><i class="fa fa-edit" aria-hidden="true"></i></a>
                        <a href="{{ url("provincia/{$provincia->provincia_id}") }}" class="btn btn-xs btn-success"  data-toggle="tooltip" data-placement="top" title="Ver"><i class="fa fa-eye" aria-hidden="true"></i></a>
                        <a href="{{ url("provincia/{$provincia->provincia_id}/eliminar") }}" class="btn btn-xs btn-danger"  data-toggle="tooltip" data-placement="top" title="Eliminar"><i class="fa fa-trash" aria-hidden="true"></i></a>
                    </td>
                    <td>{{ $provincia->provincia_nombre}}</td>
                    <td>{{ $provincia->provincia_codigo}}</td>
                    <td>{{ $provincia->pais->pais_nombre }}</td>                                     
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
                <h4 class="modal-title">Nueva Provincia</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form-horizontal" method="POST"  action="{{ url("provincia") }} " >
            @csrf
                <div class="modal-body">
                    <div class="card-body">
                        <div class="form-group row">
                            <label for="provincia_nombre" class="col-sm-3 col-form-label">Nombre de Provincia</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="provincia_nombre" name="provincia_nombre" placeholder="Nombre" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="provincia_codigo" class="col-sm-3 col-form-label">Codigo de Provincia</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="provincia_codigo" name="provincia_codigo" placeholder="Ej. 07" required>
                            </div>
                        </div>                        
                        <div class="form-group row">
                            <label for="pais_id" class="col-sm-3 col-form-label">Nombre de Pais</label>
                            <div class="col-sm-9">
                                <select class="custom-select select2" id="pais_id" name="pais_id" require>
                                    @foreach($paises as $pais)
                                        <option value="{{$pais->pais_id}}">{{$pais->pais_nombre}}</option>
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