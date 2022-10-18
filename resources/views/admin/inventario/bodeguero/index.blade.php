@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">Bodeguero</h3>
        <button class="btn btn-default btn-sm float-right" data-toggle="modal" data-target="#modal-nuevo"><i class="fa fa-plus"></i>&nbsp;Nuevo</button>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <table id="example1" class="table table-bordered table-hover table-responsive sin-salto">
            <thead>
                <tr class="text-center neo-fondo-tabla">
                    <th></th>
                    <th>Cedula</th>
                    <th>Nombre</th>
                    <th>Direccion</th>
                    <th>Telefono</th>
                    <th>Email</th>
                    <th>Fecha Ingreso</th>
                    <th>Fecha Salida</th>
                    <th>Bodega</th>                    
                </tr>
            </thead> 
            <tbody>
                @foreach($bodegueros as $bodeguero)
                <tr class="text-center">
                    <td>
                        <a href="{{ url("bodeguero/{$bodeguero->bodeguero_id}/edit") }}" class="btn btn-xs btn-primary"  data-toggle="tooltip" data-placement="top" title="Editar"><i class="fa fa-edit" aria-hidden="true"></i></a>
                        <a href="{{ url("bodeguero/{$bodeguero->bodeguero_id}") }}" class="btn btn-xs btn-success"  data-toggle="tooltip" data-placement="top" title="Ver"><i class="fa fa-eye" aria-hidden="true"></i></a>
                        <a href="{{ url("bodeguero/{$bodeguero->bodeguero_id}/eliminar") }}" class="btn btn-xs btn-danger"  data-toggle="tooltip" data-placement="top" title="Eliminar"><i class="fa fa-trash" aria-hidden="true"></i></a>
                    </td>
                    <td>{{ $bodeguero->bodeguero_cedula}}</td>
                    <td>{{ $bodeguero->bodeguero_nombre}}</td>
                    <td>{{ $bodeguero->bodeguero_direccion}}</td>  
                    <td>{{ $bodeguero->bodeguero_telefono}}</td>  
                    <td>{{ $bodeguero->bodeguero_email}}</td>  
                    <td>{{ $bodeguero->bodeguero_fecha_ingreso}}</td> 
                    <td>{{ $bodeguero->bodeguero_fecha_salida}}</td>
                    <td>{{ $bodeguero->bodega_nombre}}</td>                                
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
                <h4 class="modal-title">Nueva Bodeguero</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form-horizontal" method="POST" action="{{ url("bodeguero") }}">
            @csrf
                <div class="modal-body">
                    <div class="card-body">
                        <div class="form-group row">
                            <label for="bodeguero_cedula" class="col-sm-3 col-form-label">Cedula</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="bodeguero_cedula" name="bodeguero_cedula" placeholder="9999999999 " required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="bodeguero_nombre" class="col-sm-3 col-form-label">Nombre</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="bodeguero_nombre" name="bodeguero_nombre" placeholder="Nombre" required>
                            </div>
                        </div>    
                        <div class="form-group row">
                            <label for="bodeguero_direccion" class="col-sm-3 col-form-label">Direccion</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="bodeguero_direccion" name="bodeguero_direccion" placeholder="Direccion" value="S/D" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="bodeguero_telefono" class="col-sm-3 col-form-label">Telefono</label>
                            <div class="col-sm-9">
                                <input type="tel" class="form-control" id="bodeguero_telefono" name="bodeguero_telefono" placeholder="02345678" value="0" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="bodeguero_email" class="col-sm-3 col-form-label">Email</label>
                            <div class="col-sm-9">
                                <input type="email" class="form-control" id="bodeguero_email" name="bodeguero_email" placeholder="SIN@CORREO" value="SIN@CORREO" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="bodeguero_fecha_ingreso" class="col-sm-3 col-form-label">Fecha Ingreso</label>
                            <div class="col-sm-9">
                                <input type="date" class="form-control" id="bodeguero_fecha_ingreso" name="bodeguero_fecha_ingreso" value='<?php echo(date("Y")."-".date("m")."-".date("d")); ?>' required>
                            </div>
                        </div>                                                              
                        <div class="form-group row">
                            <label for="bodega_id" class="col-sm-3 col-form-label">Bodega</label>
                            <div class="col-sm-9">
                                <select class="custom-select select2" id="bodega_id" name="bodega_id" require>
                                    <option value="" label>--Seleccione una opcion--</option>
                                    @foreach($bodegas as $bodega)
                                        <option value="{{$bodega->bodega_id}}">{{$bodega->bodega_nombre}}</option>
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