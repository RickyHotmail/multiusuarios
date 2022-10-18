@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">Centros de Consumo</h3>
        <button class="btn btn-default btn-sm float-right" data-toggle="modal" data-target="#modal-nuevo"><i class="fa fa-plus"></i>&nbsp;Nuevo</button>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <table id="example1" class="table table-bordered table-hover table-responsive sin-salto">
            <thead>
                <tr class="text-center neo-fondo-tabla">
                    <th></th>
                    <th>Nombre</th>
                    <th>Fecha Ingreso</th>
                    <th>Descripcion</th> 
                    <th>Sustento Tributario</th>                        
                </tr>
            </thead> 
            <tbody>
                @foreach($centroCons as $centroCon)
                <tr class="text-center">
                    <td>
                        <a href="{{ url("centroConsumo/{$centroCon->centro_consumo_id}/edit") }}" class="btn btn-xs btn-primary"  data-toggle="tooltip" data-placement="top" title="Editar"><i class="fa fa-edit" aria-hidden="true"></i></a>
                        <a href="{{ url("centroConsumo/{$centroCon->centro_consumo_id}") }}" class="btn btn-xs btn-success"  data-toggle="tooltip" data-placement="top" title="Ver"><i class="fa fa-eye" aria-hidden="true"></i></a>
                        <a href="{{ url("centroConsumo/{$centroCon->centro_consumo_id}/eliminar") }}" class="btn btn-xs btn-danger"  data-toggle="tooltip" data-placement="top" title="Eliminar"><i class="fa fa-trash" aria-hidden="true"></i></a>
                    </td>
                    <td>{{ $centroCon->centro_consumo_nombre}}</td>
                    <td>{{ $centroCon->centro_consumo_fecha_ingreso}}</td>
                    <td>{{ $centroCon->centro_consumo_descripcion }}</td>
                    @if(isset($centroCon->sustento->sustento_codigo))
                        <td>{{ $centroCon->sustento->sustento_codigo.' - '.$centroCon->sustento->sustento_nombre}}</td>
                    @endif  
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
                <h4 class="modal-title">Nuevo Centro de Consumo</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form-horizontal" method="POST" action="{{ url("centroConsumo") }} "> 
                @csrf
                <div class="modal-body">
                    <div class="card-body">                        
                        <div class="form-group row">
                            <label for="centro_consumo_nombre" class="col-sm-3 col-form-label">Nombre</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="centro_consumo_nombre" name="centro_consumo_nombre" placeholder="Nombre" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="centro_consumo_fecha_ingreso" class="col-sm-3 col-form-label">Fecha Ingreso</label>
                            <div class="col-sm-9">
                                <input type="date" class="form-control" id="centro_consumo_fecha_ingreso" name="centro_consumo_fecha_ingreso" value='<?php echo(date("Y")."-".date("m")."-".date("d")); ?>' required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="centro_consumo_descripcion" class="col-sm-3 col-form-label">Descripcion</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="centro_consumo_descripcion" name="centro_consumo_descripcion" placeholder="Ingrese aqui una descripcion" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="idSustento" class="col-sm-3 col-form-label">Sustento Tributario</label>
                            <div class="col-sm-9">
                                <select class="custom-select select2" id="idSustento" name="idSustento" require>
                                    <option value="0">----Seleccione----</option>
                                    @foreach($sustentosTributario25 as $sustentoTributario25)
                                        <option value="{{$sustentoTributario25->sustento_id}}">{{$sustentoTributario25->sustento_codigo.' - '.$sustentoTributario25->sustento_nombre}}</option>
                                    @endforeach
                                </select>
                            </div>
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