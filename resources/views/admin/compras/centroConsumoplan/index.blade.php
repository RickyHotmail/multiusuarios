@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">Centros de Consumo</h3>
        <div class="float-right">
        <button class="btn btn-default btn-sm float-right" data-toggle="modal" data-target="#modal-nuevo"><i class="fa fa-plus"></i>&nbsp;Nuevo</button>
        <a class="btn btn-success btn-sm" href="{{ url("excelCentroC")}}"><i class="fas fa-file-excel"></i>&nbsp;Cargar Excel</a> 
        </div>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <table id="example4" class="table table-bordered table-hover table-responsive sin-salto">
            <thead>
                <tr class="text-center neo-fondo-tabla">
                    <th></th>
                    <th>Nombre</th>
                </tr>
            </thead> 
            <tbody>
                @foreach($centroCons as $centroCon)
                <tr style="background: @if($centroCon->centro_consumo_nivel == 1) #C9FABE; @endif @if($centroCon->centro_consumo_nivel == 2) #AFFFFB; @endif  @if($centroCon->centro_consumo_nivel == 3) #D6AEF8; @endif  @if($centroCon->centro_consumo_nivel == 4) #F9FA87; @endif  @if($centroCon->centro_consumo_nivel == 5) #F9D07A; @endif">
                    @if($centroCon->centro_consumo_nivel <=5)
                        <td ><b>
                         <a href="{{ url("plancentroConsumo/{$centroCon->centro_consumo_id}/edit") }}" class="btn btn-xs btn-primary"  data-toggle="tooltip" data-placement="top" title="Editar"><i class="fa fa-edit" aria-hidden="true"></i></a> 
                        <a href="{{ url("plancentroConsumo/{$centroCon->centro_consumo_id}/eliminar") }}" class="btn btn-xs btn-danger"  data-toggle="tooltip" data-placement="top" title="Eliminar"><i class="fa fa-trash" aria-hidden="true"></i></a> 
                            @if($centroCon->detallescontable == 0) <a href="{{ url("plancentroConsumo/{$centroCon->centro_consumo_id}/subcuenta") }}" class="btn btn-xs btn-secondary"  data-toggle="tooltip" data-placement="top" title="Añadir Cuenta"><i class="fa fa-tasks" aria-hidden="true"></i></a>@endif
                        </b></td>
                        <td class="espacio{{$centroCon->centro_consumo_nivel}}"><b>{{ $centroCon->centro_consumo_numero.'  - '.$centroCon->centro_consumo_nombre}}</b></td>   
                    @else
                        <td >
                        <a href="{{ url("plancentroConsumo/{$centroCon->centro_consumo_id}/edit") }}" class="btn btn-xs btn-primary"  data-toggle="tooltip" data-placement="top" title="Editar"><i class="fa fa-edit" aria-hidden="true"></i></a> 
                        <a href="{{ url("plancentroConsumo/{$centroCon->centro_consumo_id}/eliminar") }}" class="btn btn-xs btn-danger"  data-toggle="tooltip" data-placement="top" title="Eliminar"><i class="fa fa-trash" aria-hidden="true"></i></a> 
                            @if($centroCon->detallescontable == 0) <a href="{{ url("plancentroConsumo/{$centroCon->centro_consumo_id}/subcuenta") }}" class="btn btn-xs btn-secondary"  data-toggle="tooltip" data-placement="top" title="Añadir Cuenta"><i class="fa fa-tasks" aria-hidden="true"></i></a>@endif
                        </td>
                        <td class="espacio{{$centroCon->centro_consumo_nivel}}">{{ $centroCon->centro_consumo_numero.'  - '.$centroCon->centro_consumo_nombre}}</td> 
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
            <form class="form-horizontal" method="POST" action="{{ url("plancentroConsumo") }} "> 
                @csrf
                <div class="modal-body">
                    <div class="card-body">
                        <div class="form-group row">
                            <label for="cuenta_nivel" class="col-sm-3 col-form-label">Nivel</label>
                            <div class="col-sm-9">
                                <input type="hidden"id="cuenta_nivel" name="cuenta_nivel" value="1">
                                <label class="form-control">1</label>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="cuenta_numero" class="col-sm-3 col-form-label">Numero</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="cuenta_numero" name="cuenta_numero" placeholder="Ej. 1.1.1.1" value="{{ $secuencial }}" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="centro_consumo_nombre" class="col-sm-3 col-form-label">Nombre</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="centro_consumo_nombre" name="centro_consumo_nombre" placeholder="Nombre" required>
                            </div>
                        </div>
                        <!-- <div class="form-group row">
                            <label for="centro_consumo_fecha_ingreso" class="col-sm-3 col-form-label">Fecha Ingreso</label>
                            <div class="col-sm-9">
                                <input type="date" class="form-control" id="centro_consumo_fecha_ingreso" name="centro_consumo_fecha_ingreso" value='<?php echo(date("Y")."-".date("m")."-".date("d")); ?>' required>
                            </div>
                        </div> -->
                        <div class="form-group row">
                            <label for="centro_consumo_descripcion" class="col-sm-3 col-form-label">Descripcion</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="centro_consumo_descripcion" name="centro_consumo_descripcion" placeholder="Ingrese aqui una descripcion" required>
                            </div>
                        </div>
                        <!-- <div class="form-group row">
                            <label for="idSustento" class="col-sm-3 col-form-label">Sustento Tributario</label>
                            <div class="col-sm-9">
                                <select class="custom-select select2" id="idSustento" name="idSustento" require>
                                    <option value="0">----Seleccione----</option>
                                    @foreach($sustentosTributario25 as $sustentoTributario25)
                                        <option value="{{$sustentoTributario25->sustento_id}}">{{$sustentoTributario25->sustento_codigo.' - '.$sustentoTributario25->sustento_nombre}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div> -->
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