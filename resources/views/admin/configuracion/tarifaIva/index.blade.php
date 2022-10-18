@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">Tarifa de Iva</h3>
        <button class="btn btn-default btn-sm float-right" data-toggle="modal" data-target="#modal-nuevo"><i class="fa fa-plus"></i>&nbsp;Nuevo</button>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <table id="example1" class="table table-bordered table-hover table-responsive sin-salto">
            <thead>
                <tr class="text-center neo-fondo-tabla">
                    <th></th>
                    <th>Codigo</th>
                    <th>Porcentaje</th>
                </tr>
            </thead> 
            <tbody>
                @foreach($tarifaIvas as $tarifaIva)
                <tr class="text-center">
                    <td>
                        <a href="{{ url("tarifaIva/{$tarifaIva->tarifa_iva_id}/edit") }}" class="btn btn-xs btn-primary"  data-toggle="tooltip" data-placement="top" title="Editar"><i class="fa fa-edit" aria-hidden="true"></i></a>
                        <a href="{{ url("tarifaIva/{$tarifaIva->tarifa_iva_id}") }}" class="btn btn-xs btn-success"  data-toggle="tooltip" data-placement="top" title="Ver"><i class="fa fa-eye" aria-hidden="true"></i></a>
                        <a href="{{ url("tarifaIva/{$tarifaIva->tarifa_iva_id}/eliminar") }}" class="btn btn-xs btn-danger"  data-toggle="tooltip" data-placement="top" title="Eliminar"><i class="fa fa-trash" aria-hidden="true"></i></a>
                    </td>
                    <td>{{ $tarifaIva->tarifa_iva_codigo}}</td>
                    <td>{{ $tarifaIva->tarifa_iva_porcentaje }}</td>  
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <!-- /.card-body -->
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script type="text/javascript">
    function updateRangeInput(elem) {
        $(elem).next().val($(elem).val());
    }    
</script>
<!-- /.card -->
<div class="modal fade" id="modal-nuevo">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-secondary">
                <h4 class="modal-title">Nueva Tarifa de Iva</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form-horizontal" method="POST"  action="{{ url("tarifaIva") }} " >
                @csrf
                <div class="modal-body">
                    <div class="card-body">                        
                        <div class="form-group row">
                            <label for="tarifa_iva_codigo" class="col-sm-3 col-form-label">Codigo</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="tarifa_iva_codigo" name="tarifa_iva_codigo" maxlength="2" placeholder="Codigo" required>
                            </div>
                        </div>                        
                        <div class="form-group row">
                            <label for="tarifa_iva_porcentaje" class="col-sm-3 col-form-label">Porcentaje</label>
                            <div class="col-sm-9">                                
                                <input type="range" class="form-control" value="0.00" min="0" max="100" step="0.01" oninput="updateRangeInput(this)">
                                <input type="number" class="form-control" id="tarifa_iva_porcentaje" name="tarifa_iva_porcentaje" value="0.00" min="0" max="100" step="0.01" required>
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