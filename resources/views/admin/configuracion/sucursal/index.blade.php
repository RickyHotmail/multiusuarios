@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">Sucursales</h3>
        <button class="btn btn-default btn-sm float-right" data-toggle="modal" data-target="#modal-nuevo"><i class="fa fa-plus"></i>&nbsp;Nuevo</button>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <table id="example1" class="table table-bordered table-hover table-responsive sin-salto">
            <thead>
                <tr class="text-center neo-fondo-tabla">
                    <th></th>
                    <th>Nombre</th>
                    <th>Establecimiento</th>
                    <th>Direccion</th>
                    <th>Telefono</th>                                                            
                </tr>
            </thead>
            <tbody>
                @foreach($sucursales as $sucursal)
                <tr class="text-center">
                    <td>                        
                        <a href="{{ url("sucursal/{$sucursal->sucursal_id}/edit") }}" class="btn btn-xs btn-primary" data-toggle="tooltip" data-placement="top" title="Editar"><i class="fa fa-edit" aria-hidden="true"></i></a>
                        <a href="{{ url("sucursal/{$sucursal->sucursal_id}") }}" class="btn btn-xs btn-success" data-toggle="tooltip" data-placement="top" title="Ver"><i class="fa fa-eye"></i></a>                   
                        <a href="{{ url("sucursal/{$sucursal->sucursal_id}/eliminar") }}" class="btn btn-xs btn-danger" data-toggle="tooltip" data-placement="top" title="Eliminar"><i class="fa fa-trash" aria-hidden="true"></i></a>                        
                    </td>                                        
                    <td>{{ $sucursal->sucursal_nombre }}</td>
                    <td>{{ $sucursal->sucursal_codigo }}</td>
                    <td>{{ $sucursal->sucursal_direccion }}</td>
                    <td>{{ $sucursal->sucursal_telefono }}</td>                                    
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
                <h4 class="modal-title">Nueva Sucursal</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form-horizontal" method="POST" action="{{ url("sucursal") }} " >
            @csrf
                <div class="modal-body">
                    <div class="card-body">
                        <div class="form-group row">
                            <label for="sucursal_nombre" class="col-sm-3 col-form-label">Nombre Sucursal</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="sucursal_nombre" name="sucursal_nombre" placeholder="Nombre Sucursal" required>
                            </div>
                        </div>    
                        <div class="form-group row">
                            <label for="sucursal_codigo" class="col-sm-3 col-form-label">Código Sucursal</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="sucursal_codigo" name="sucursal_codigo" placeholder="001" required>
                            </div>
                        </div>              
                        <div class="form-group row">
                            <label for="sucursal_direccion" class="col-sm-3 col-form-label">Dirección</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="sucursal_direccion" name="sucursal_direccion" placeholder="Direccion " value="S/D" required> 
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="sucursal_telefono" class="col-sm-3 col-form-label">Teléfono</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="sucursal_telefono" name="sucursal_telefono" placeholder="Ej. 0999999999" value="0" required>
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