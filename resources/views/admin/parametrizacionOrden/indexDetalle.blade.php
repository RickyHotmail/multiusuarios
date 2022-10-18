@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">Valor de Parametros</h3>

        <div class="float-right">
            <button type="button" onclick="location.href='{{url('parametrizacionOrden')}}'" class="btn btn-default btn-sm mr-1"><i class="fa fa-undo"></i>&nbsp;Atras</button>
            <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modal-nuevo"><i class="fa fa-plus"></i>&nbsp;Nuevo</button>
        </div>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <table id="example1" class="table table-bordered table-hover table-responsive sin-salto">
            <thead>
                <tr class="text-center neo-fondo-tabla">
                    <th></th>
                    <th>Descripción</th> 
                    <th>Valor</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            @foreach($valores as $valor)
                <tr class="text-center">
                    <td>
                        <a href="{{url('parametrizacionDetalleOrden')}}/{{$valor->parametrizard_id}}/editar" class="btn btn-xs btn-primary " style="padding: 2px 8px;" data-toggle="tooltip" data-placement="top" title="Editar"><i class="fas fa-edit"></i></a>
                    </td>
                    <td>{{ $valor->parametrizard_descripcion }}</td>
                    <td>{{ $valor->parametrizard_valor}}</td>
                    <td><a class="btn btn-xs btn-inline-warning " style="padding: 2px 8px;" data-toggle="tooltip" data-placement="top" title="Configurar">ACTIVO</a></td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <!-- /.card-body -->
</div>
<div class="modal fade" id="modal-nuevo">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-secondary">
                <h4 class="modal-title">Nuevo Valor </h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form-horizontal" method="POST" action="{{ url("parametrizacionDetalleOrden") }}">
                @csrf
                <input type="hidden" value="{{ $parametrizar_id }}" name="id">
                <div class="modal-body">
                    <div class="card-body">  
                        <div class="form-group row">
                            <label for="producto_id" class="col-sm-3 col-form-label">Descripción:</label>
                            <div class="col-sm-9">
                                <input type="text" name="descripcion" id="descripcion" class="form-control">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="producto_id" class="col-sm-3 col-form-label">Valor:</label>
                            <div class="col-sm-9">
                                <input type="number" step=1 min=1 max=100 name="valor" id="valor" class="form-control" >
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
@endsection