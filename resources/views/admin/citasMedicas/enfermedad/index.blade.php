@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary">    
    <div class="card-header">
        <h3 class="card-title">Enfermedad</h3>
        <div class="float-right">
            <button class="btn btn-default btn-sm" data-toggle="modal" data-target="#modal-nuevo"><i class="fa fa-plus"></i>&nbsp;Nuevo</button>           
            <a class="btn btn-info btn-sm" href="{{ asset('admin/archivos/FORMATO_ENFERMEDADES.xlsx') }}" download="FORMATO_ENFERMEDADES"><i class="fas fa-file-excel"></i>&nbsp;Formato</a>
            <a class="btn btn-success btn-sm" href="{{ url("excelEnfermedad")}}"><i class="fas fa-file-excel"></i>&nbsp;Cargar Excel</a>           
        </div>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <table id="example1" class="table table-bordered table-hover table-responsive sin-salto">
            <thead>
                <tr class="text-center neo-fondo-tabla">
                    <th></th>
                    <th>Codigo</th>   
                    <th>Nombre</th>   
                </tr>
            </thead>
            <tbody>
                @foreach($enfermedades as $enfermedad)
                <tr class="text-center">
                    <td>                        
                        <a href="{{ url("enfermedad/{$enfermedad->enfermedad_id}/edit")}}" class="btn btn-xs btn-primary" data-toggle="tooltip" data-placement="top" title="Editar"><i class="fa fa-edit" aria-hidden="true"></i></a>
                        <a href="{{ url("enfermedad/{$enfermedad->enfermedad_id}")}}" class="btn btn-xs btn-success" data-toggle="tooltip" data-placement="top" title="Ver"><i class="fa fa-eye"></i></a>                   
                        <a href="{{ url("enfermedad/{$enfermedad->enfermedad_id}/eliminar")}}" class="btn btn-xs btn-danger" data-toggle="tooltip" data-placement="top" title="Eliminar"><i class="fa fa-trash" aria-hidden="true"></i></a>                        
                    </td> 
                    <td>{{ $enfermedad->enfermedad_codigo }}</td>                                          
                    <td>{{ $enfermedad->enfermedad_nombre }}</td>         
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
                <h4 class="modal-title">Nueva Enfermedad</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form-horizontal" method="POST" action="{{ url("enfermedad") }}">
            @csrf
                <div class="modal-body">
                    <div class="card-body">
                        <div class="form-group row">
                            <label for="idCodigoEnfer" class="col-sm-3 col-form-label">Codigo</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="idCodigoEnfer" name="idCodigoEnfer" placeholder="ABC12345 " required> 
                            </div>
                        </div>             
                        <div class="form-group row">
                            <label for="idNombre" class="col-sm-3 col-form-label">Nombre de la enfermedad</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="idNombre" name="idNombre" placeholder="Nombre " required> 
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