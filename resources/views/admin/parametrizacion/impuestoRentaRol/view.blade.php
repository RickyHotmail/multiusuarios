@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">Impuesto Renta Rol</h3>
        <button class="btn btn-default btn-sm float-right" data-toggle="modal" data-target="#modal-nuevo"><i class="fa fa-plus"></i>&nbsp;Nuevo</button>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <table id="example1" class="table table-bordered table-hover table-responsive sin-salto">
            <thead>
                <tr class="text-center neo-fondo-tabla">  
                    <th></th>
                    <th>fraccion basica </th>
                    <th>exceso hasta</th>  
                    <th>Impuesto sobre la Fraccion Basica</th>  
                    <th>Impuesto sobre la Fraccion Excedente</th>      
                                                  
                </tr>
            </thead>            
            <tbody>
                @foreach($impuesto as $impuesto)
                <tr class="text-center">
                    <td>
                        <a href="{{ url("impuestoRentaRol/{$impuesto->impuestos_id}/edit") }}" class="btn btn-xs btn-primary"  data-toggle="tooltip" data-placement="top" title="Editar"><i class="fa fa-edit" aria-hidden="true"></i></a>
                        <a href="{{ url("impuestoRentaRol/{$impuesto->impuestos_id}") }}" class="btn btn-xs btn-success"  data-toggle="tooltip" data-placement="top" title="Ver"><i class="fa fa-eye" aria-hidden="true"></i></a>
                        <a href="{{ url("impuestoRentaRol/{$impuesto->impuestos_id}/eliminar") }}" class="btn btn-xs btn-danger"  data-toggle="tooltip" data-placement="top" title="Eliminar"><i class="fa fa-trash" aria-hidden="true"></i></a>
                    </td>
                    <td>{{ $impuesto->impuesto_fraccion_basica}}</td>
                    <td>{{ $impuesto->impuesto_exceso_hasta}}</td> 
                    <td>{{ $impuesto->impuesto_fraccion_excede}}</td> 
                    <td>{{ $impuesto->impuesto_sobre_fraccion}}</td>
                   
                                    
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
                <h4 class="modal-title">Nuevo Impuesto Renta Rol</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form-horizontal" method="POST" action="{{ url("impuestoRentaRol") }}">
                @csrf
                <div class="modal-body">
                    <div class="card-body">                        
                        <div class="form-group row">
                            <label for="idFraccion" class="col-sm-3 col-form-label">Fraccion Basica</label>
                            <div class="col-sm-9">
                            <input type="number" class="form-control" id="idFraccion" name="idFraccion" value="0.00"  step="0.01" placeholder="Fraccion Basica" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="idExceso" class="col-sm-3 col-form-label">Exceso Hasta</label>
                            <div class="col-sm-9">
                                <input type="number" class="form-control" id="idExceso" name="idExceso" value="0.00"  step="0.01" placeholder="Exceso Hasta" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="idExcede" class="col-sm-3 col-form-label">Impuesto sobre la Fraccion Basica</label>
                            <div class="col-sm-9">
                                <input type="number" class="form-control" id="idExcede" name="idExcede" value="0.00"  step="0.01"  placeholder="Fraccion excede" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="idSobre" class="col-sm-3 col-form-label">Impuesto sobre la Fraccion Excedente</label>
                            <div class="col-sm-9">
                                <input type="number" class="form-control" id="idSobre" name="idSobre" value="0.00" min="0" max="100"  step="0.01" placeholder="Sobre Fraccion" required>
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