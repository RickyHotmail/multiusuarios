@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">Rubro</h3>
        <div class="float-right">
            <button class="btn btn-default btn-sm float-right" data-toggle="modal" data-target="#modal-nuevo"><i class="fa fa-plus"></i>&nbsp;Nuevo</button>
            <a class="btn btn-info btn-sm" href="{{ asset('admin/archivos/FORMATO_RUBRO.xlsx') }}" download="FORMATO RUBRO"><i class="fas fa-file-excel"></i>&nbsp;Formato</a>
            <a class="btn btn-success btn-sm" href="{{ url("excelRubro") }}"><i class="fas fa-file-excel"></i>&nbsp;Cargar Excel</a>
        </div>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <table id="example1" class="table table-bordered table-hover table-responsive sin-salto">
            <thead>
                <tr class="text-center neo-fondo-tabla">
                    <th></th>
                    <th>Nombre</th>     
                    <th>Tipo</th>    
                    <th>Descripcion</th>    
                                                    
                </tr>
            </thead>            
            <tbody>
                @foreach($rubros as $rubro)
                <tr class="text-center">
                    <td>
                        <a href="{{ url("rubro/{$rubro->rubro_id}/edit") }}" class="btn btn-xs btn-primary"  data-toggle="tooltip" data-placement="top" title="Editar"><i class="fa fa-edit" aria-hidden="true"></i></a>
                        <a href="{{ url("rubro/{$rubro->rubro_id}") }}" class="btn btn-xs btn-success"  data-toggle="tooltip" data-placement="top" title="Ver"><i class="fa fa-eye" aria-hidden="true"></i></a>
                        <a href="{{ url("rubro/{$rubro->rubro_id}/eliminar") }}" class="btn btn-xs btn-danger"  data-toggle="tooltip" data-placement="top" title="Eliminar"><i class="fa fa-trash" aria-hidden="true"></i></a>
                    </td>
                    <td>{{ $rubro->rubro_nombre}}</td>   
                    <td>@if($rubro->rubro_tipo =='1')EGRESOS 
                            @elseif ($rubro->rubro_tipo =='2')INGRESOS 
                            @elseif ($rubro->rubro_tipo =='3')PROVISIONES  
                        @endif</td>                   
                    <td>{{ $rubro->rubro_descripcion}}</td>     
                    
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
                <h4 class="modal-title">Nuevo Rubro</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form-horizontal" method="POST" action="{{ url("rubro") }} "> 
                @csrf
                <div class="modal-body">
                    <div class="card-body">                        
                        <div class="form-group row">
                            <label for="rubro_nombre" class="col-sm-3 col-form-label">Identificador</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="rubro_nombre" name="rubro_nombre" placeholder="Rubro" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="rubro_descripcion" class="col-sm-3 col-form-label">Descripcion</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="rubro_descripcion" name="rubro_descripcion" placeholder="Ingrese aqui una descripcion" required>
                            </div>
                        </div>  
                        <div class="form-group row">
                            <label for="rubro_tipo" class="col-sm-3 col-form-label">Tipo de Rubro</label>
                            <div class="col-sm-9">
                                <select class="custom-select" id="rubro_tipo" name="rubro_tipo" required>
                                    <option value="" label>--Seleccione una opcion--</option>
                                    <option value="1">EGRESOS</option>
                                    <option value="2">INGRESOS</option>
                                    <option value="3">PROVISIONES</option>
                                    <option value="4">OTROS</option>
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