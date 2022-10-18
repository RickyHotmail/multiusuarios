@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">Casilleros Tributarios</h3>
        <div class="float-right">
            <button class="btn btn-default btn-sm float-right" data-toggle="modal" data-target="#modal-nuevo"><i class="fa fa-plus"></i>&nbsp;Nuevo</button>
            <a class="btn btn-success btn-sm" href="{{ url("excelCasillero")}}"><i class="fas fa-file-excel"></i>&nbsp;Cargar Excel</a>  
        </div>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <table id="example1" class="table table-bordered table-hover table-responsive sin-salto">
            <thead>
                <tr class="text-center neo-fondo-tabla">
                    <th></th>
                    <th>Codigo</th>
                    <th>Descripcion</th>
                    <th>Tipo</th>                                                            
                </tr>
            </thead>            
            <tbody>
                @foreach($casilleros as $casillero)
                <tr class="text-center">
                    <td>
                        <a href="{{ url("casilleroTributario/{$casillero->casillero_id}/edit") }}" class="btn btn-xs btn-primary"  data-toggle="tooltip" data-placement="top" title="Editar"><i class="fa fa-edit" aria-hidden="true"></i></a>
                        <a href="{{ url("casilleroTributario/{$casillero->casillero_id}") }}" class="btn btn-xs btn-success"  data-toggle="tooltip" data-placement="top" title="Ver"><i class="fa fa-eye" aria-hidden="true"></i></a>
                        <a href="{{ url("casilleroTributario/{$casillero->casillero_id}/eliminar") }}" class="btn btn-xs btn-danger"  data-toggle="tooltip" data-placement="top" title="Eliminar"><i class="fa fa-trash" aria-hidden="true"></i></a>
                    </td>
                    <td>{{ $casillero->casillero_codigo}}</td>  
                    <td>{{ $casillero->casillero_descripcion}}</td> 
                    <td>{{ $casillero->casillero_tipo}}</td>
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
                <h4 class="modal-title">Nuevo Casillero</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form-horizontal" method="POST" action="{{ url("casilleroTributario") }}">
                @csrf
                <div class="modal-body">
                    <div class="card-body">                    
                        <div class="form-group row">
                            <label for="idCajaNombre" class="col-sm-3 col-form-label">Codigo</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="idCasilleroCodigo" name="idCasilleroCodigo" placeholder="Codigo" required>
                            </div>
                        </div>                                          
                        <div class="form-group row">
                            <label for="idCajaNombre" class="col-sm-3 col-form-label">Descripcion</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="idCasilleroDescripcion" name="idCasilleroDescripcion" placeholder="Descripcion" required>
                            </div>
                        </div>
                        <div class="form-group row">
                        <label for="idCasilleroTipo" class="col-sm-3 col-form-label">Tipo</label>
                            <div class="col-sm-9">
                                <select class="custom-select select2" id="idCasilleroTipo" name="idCasilleroTipo" required>
                                    <option value="" label>--Seleccione una opcion--</option>
                                    <option value="COMPRAS 0%" label>Compras 0%</option>
                                    <option value="COMPRAS 12%" label>Compras 12%</option>                           
                                    <option value="VENTAS 0%" label>Ventas 0%</option>    
                                    <option value="VENTAS 12%" label>Ventas 12%</option>
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