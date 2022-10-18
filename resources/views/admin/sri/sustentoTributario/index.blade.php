@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">Sustento Tributario</h3>
        <button class="btn btn-default btn-sm float-right" data-toggle="modal" data-target="#modal-nuevo"><i class="fa fa-plus"></i>&nbsp;Nuevo</button>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <table id="example1" class="table table-bordered table-hover table-responsive sin-salto">
            <thead>
                <tr class="text-center neo-fondo-tabla">
                    <th></th>
                    <th>Nombre</th>
                    <th>Codigo</th>      
                    <th>Crédito</th>
                    <th>Venta 12%</th>       
                    <th>Venta 0%</th>       
                    <th>Compra 12%</th>       
                    <th>Compra 0%</th>                                                                            
                </tr>
            </thead>
            <tbody>
                @foreach($sustentoTributarios as $sustentoTributario)
                <tr class="text-center">
                    <td>                        
                        <a href="{{ url("sustentoTributario/{$sustentoTributario->sustento_id}/edit") }}" class="btn btn-xs btn-primary" data-toggle="tooltip" data-placement="top" title="Editar"><i class="fa fa-edit" aria-hidden="true"></i></a>
                        <a href="{{ url("sustentoTributario/{$sustentoTributario->sustento_id}") }}" class="btn btn-xs btn-success" data-toggle="tooltip" data-placement="top" title="Ver"><i class="fa fa-eye"></i></a>                   
                        <a href="{{ url("sustentoTributario/{$sustentoTributario->sustento_id}/eliminar") }}" class="btn btn-xs btn-danger" data-toggle="tooltip" data-placement="top" title="Eliminar"><i class="fa fa-trash" aria-hidden="true"></i></a>                        
                    </td>                                        
                    <td>{{ $sustentoTributario->sustento_nombre }}</td>
                    <td>{{ $sustentoTributario->sustento_codigo }}</td>               
                    <td>
                        @if($sustentoTributario->sustento_credito == "1")
                            Con Crédito
                        @elseif($sustentoTributario->sustento_credito == "2")
                            Sin Crédito                                             
                        @endif
                    </td>  
                    <td>{{ $sustentoTributario->sustento_venta12 }}</td>      
                    <td>{{ $sustentoTributario->sustento_venta0 }}</td>       
                    <td>{{ $sustentoTributario->sustento_compra12 }}</td>       
                    <td>{{ $sustentoTributario->sustento_compra0 }}</td>                                                   
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
                <h4 class="modal-title">Nuevo Sustento Tributario</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form-horizontal" method="POST" action="{{ url("sustentoTributario") }}">
            @csrf
                <div class="modal-body">
                    <div class="card-body">
                        <div class="form-group row">
                            <label for="idNombre" class="col-sm-3 col-form-label">Nombre</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="idNombre" name="idNombre" placeholder="Nombre" required>
                            </div>
                        </div>                
                        <div class="form-group row">
                            <label for="idCodigo" class="col-sm-3 col-form-label">Codigo</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="idCodigo" name="idCodigo" placeholder="Codigo " required> 
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="idCredito" class="col-sm-3 col-form-label">Crédito</label>
                            <div class="col-sm-9">
                                <select id="idCredito" name="idCredito" class="form-control show-tick " data-live-search="true" required>
                                    <option value="" label>--Seleccione--</option>
                                    <option value="1">Con Crédito</option>
                                    <option value="2">Sin Crédito</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="idVenta12" class="col-sm-3 col-form-label">Venta 12%</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="idVenta12" name="idVenta12" placeholder="Codigo " required> 
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="idVenta0" class="col-sm-3 col-form-label">Venta 0%</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="idVenta0" name="idVenta0" placeholder="Codigo " required> 
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="idCompra12" class="col-sm-3 col-form-label">Compra 12%</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="idCompra12" name="idCompra12" placeholder="Codigo " required> 
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="idCompra0" class="col-sm-3 col-form-label">Compra 0%</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="idCompra0" name="idCompra0" placeholder="Codigo " required> 
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