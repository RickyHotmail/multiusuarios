@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">Editar Valor de Párametro</h3>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <form class="form-horizontal" method="POST" action="{{ url("parametrizacionDetalleOrden") }}/{{$valor->parametrizard_id}}/editar">
            @csrf
            <div class="modal-body">
                <div class="card-body">  
                    <div class="form-group row">
                        <label for="producto_id" class="col-sm-3 col-form-label">Descripción:</label>
                        <div class="col-sm-9">
                            <input type="text" name="descripcion" id="descripcion" class="form-control" value="{{$valor->parametrizard_descripcion}}">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="producto_id" class="col-sm-3 col-form-label">Valor:</label>
                        <div class="col-sm-9">
                            <input type="number" step=1 min=1 max=100 name="valor" id="valor" class="form-control" value="{{$valor->parametrizard_valor}}">
                        </div>
                    </div>
                </div>
                <!-- /.card-body -->
            </div>
            <div class="modal-footer justify-content-between">
                <a href="{{url('/parametrizacionDetalleOrden')}}/{{$valor->parametrizar_id}}" class="btn btn-danger">Cancelar</a>
                <button type="submit" class="btn btn-success">Guardar</button>
            </div>
        </form>
    </div>
    <!-- /.card-body -->
</div>
<!-- /.modal -->
@endsection