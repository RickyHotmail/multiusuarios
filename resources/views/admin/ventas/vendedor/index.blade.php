@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">Vendedores</h3>
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
                    <th>Comision %</th>
                    <th>Fecha de Ingreso</th>
                    <th>Fecha de Salida</th>                 
                    <th>Zona Asignada</th>
                </tr>
            </thead> 
            <tbody>
                @foreach($vendedores as $vendedor)
                <tr class="text-center">
                    <td>
                        <a href="{{ url("vendedor/{$vendedor->vendedor_id}/edit") }}" class="btn btn-xs btn-primary"  data-toggle="tooltip" data-placement="top" title="Editar"><i class="fa fa-edit" aria-hidden="true"></i></a>
                        <a href="{{ url("vendedor/{$vendedor->vendedor_id}") }}" class="btn btn-xs btn-success"  data-toggle="tooltip" data-placement="top" title="Ver"><i class="fa fa-eye" aria-hidden="true"></i></a>
                        <a href="{{ url("vendedor/{$vendedor->vendedor_id}/eliminar") }}" class="btn btn-xs btn-danger"  data-toggle="tooltip" data-placement="top" title="Eliminar"><i class="fa fa-trash" aria-hidden="true"></i></a>
                    </td>
                    <td>{{ $vendedor->vendedor_cedula}}</td>
                    <td>{{ $vendedor->vendedor_nombre}}</td>
                    <td>{{ $vendedor->vendedor_direccion}}</td>
                    <td>{{ $vendedor->vendedor_telefono}}</td>   
                    <td>{{ $vendedor->vendedor_email}}</td>
                    <td>{{ $vendedor->vendedor_comision_porcentaje}}</td>
                    <td>{{ $vendedor->vendedor_fecha_ingreso}}</td>
                    <td>{{ $vendedor->vendedor_fecha_salida}}</td>
                    <td>{{ $vendedor->zona->zona_nombre}}</td> 
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
                <h4 class="modal-title">Nuevo Vendedor</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form-horizontal" method="POST" action="{{ url("vendedor") }}">
            @csrf
                <div class="modal-body">
                    <div class="card-body">
                        <div class="form-group row">
                            <label for="idCedula" class="col-sm-3 col-form-label">Cedula</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="idCedula" name="idCedula" placeholder="#Cedula" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="idNombre" class="col-sm-3 col-form-label">Nombre</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="idNombre" name="idNombre" placeholder="Nombre" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="idDireccion" class="col-sm-3 col-form-label">Direccion</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="idDireccion" name="idDireccion" placeholder="Direccion" value="S/D" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="idTelefono" class="col-sm-3 col-form-label">Telefono</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="idTelefono" name="idTelefono" placeholder="Telefono" value="0" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="idEmail" class="col-sm-3 col-form-label">Email</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="idEmail" name="idEmail" placeholder="SIN@COREEO" value="SIN@COREEO" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="idPorcentaje" class="col-sm-3 col-form-label">Porcentaje de comision</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="idPorcentaje" name="idPorcentaje" placeholder="0" value="0" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="idFecha" class="col-sm-3 col-form-label">Fecha de Ingreso</label>
                            <div class="col-sm-9">
                                <input type="date" class="form-control" id="idFecha" name="idFecha" value='<?php echo(date("Y")."-".date("m")."-".date("d")); ?>' required>
                            </div>
                        </div>                                                                                       
                        <div class="form-group row">
                            <label for="idZona" class="col-sm-3 col-form-label">Zonas</label>
                            <div class="col-sm-9">
                                <select class="custom-select select2" id="idZona" name="idZona" require>
                                    @foreach($zonas as $zona)
                                        <option value="{{$zona->zona_id}}">{{$zona->zona_nombre}}</option>
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