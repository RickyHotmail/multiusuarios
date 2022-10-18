@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">Transportista</h3>
        <div class="float-right">
            <button class="btn btn-default btn-sm" data-toggle="modal" data-target="#modal-nuevo"><i class="fa fa-plus"></i>&nbsp;Nuevo</button>
            <a class="btn btn-info btn-sm" href="{{ asset('admin/archivos/FORMATO_TRANSPORTISTA.xlsx') }}" download="FORMATO TRANSPORTISTA"><i class="fas fa-file-excel"></i>&nbsp;Formato</a>
            <a class="btn btn-success btn-sm" href="{{ url("excelTransportista") }}"><i class="fas fa-file-excel"></i>&nbsp;Cargar Excel</a>
        </div>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <table id="example1" class="table table-bordered table-hover table-responsive sin-salto">
            <thead>
                <tr class="text-center neo-fondo-tabla">
                    <th></th>
                    <th>Cedula</th>
                    <th>Nombre</th>
                    <th>Placa</th>
                    <th>Embarcacion</th>         
                </tr>
            </thead> 
            <tbody>
                @foreach($transportistas as $transportista)
                <tr class="text-center">
                    <td>
                        <a href="{{ url("transportista/{$transportista->transportista_id}/edit") }}" class="btn btn-xs btn-primary"  data-toggle="tooltip" data-placement="top" title="Editar"><i class="fa fa-edit" aria-hidden="true"></i></a>
                        <a href="{{ url("transportista/{$transportista->transportista_id}") }}" class="btn btn-xs btn-success"  data-toggle="tooltip" data-placement="top" title="Ver"><i class="fa fa-eye" aria-hidden="true"></i></a>
                        <a href="{{ url("transportista/{$transportista->transportista_id}/eliminar") }}" class="btn btn-xs btn-danger"  data-toggle="tooltip" data-placement="top" title="Eliminar"><i class="fa fa-trash" aria-hidden="true"></i></a>
                    </td>
                    <td>{{ $transportista->transportista_cedula}}</td>
                    <td>{{ $transportista->transportista_nombre}}</td>
                    <td>{{ $transportista->transportista_placa}}</td>  
                    <td>{{ $transportista->transportista_embarcacion}}</td>
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
                <h4 class="modal-title">Nuevo Transportista</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form-horizontal" method="POST" action="{{ url("transportista") }}">
            @csrf
                <div class="modal-body">
                    <div class="card-body">
                        <div class="form-group row">
                            <label for="transportista_cedula" class="col-sm-3 col-form-label">Cedula</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="transportista_cedula" name="transportista_cedula" placeholder="9999999999 " required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="transportista_nombre" class="col-sm-3 col-form-label">Nombre</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="transportista_nombre" name="transportista_nombre" placeholder="Nombre" required>
                            </div>
                        </div>    
                        <div class="form-group row">
                            <label for="transportista_placa" class="col-sm-3 col-form-label">Placa</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="transportista_placa" name="transportista_placa" placeholder="Ej. AAA-0000" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="transportista_embarcacion" class="col-sm-3 col-form-label">Embarcacion</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="transportista_embarcacion" name="transportista_embarcacion" placeholder="Embarcacion">
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