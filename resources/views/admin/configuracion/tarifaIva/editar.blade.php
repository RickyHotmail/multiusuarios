@extends ('admin.layouts.admin')
@section('principal')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script type="text/javascript">
    function updateRangeInput(elem) {
        $(elem).next().val($(elem).val());
    }    
</script>
<form class="form-horizontal" method="POST"  action="{{ route('tarifaIva.update', [$tarifaIva->tarifa_iva_id]) }}">
@method('PUT')
@csrf
    <div class="card card-secondary">
        <div class="card-header">
            <h3 class="card-title">Editar Tarifa de Iva</h3>
            <div class="float-right">
                <button type="submit" class="btn btn-success btn-sm"><i class="fa fa-save"></i>&nbsp;Guardar</button>
                 <!--  
                <button type="button" onclick='window.location = "{{ url("tarifaIva") }}";'  class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
                --> 
                <button  type="button" onclick="history.back()" class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
            </div>
        </div>
        <div class="card-body">
            <div class="form-group row">
                <label for="tarifa_iva_codigo" class="col-sm-2 col-form-label">Codigo</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="tarifa_iva_codigo" name="tarifa_iva_codigo" placeholder="Codigo" maxlength="2" value="{{$tarifaIva->tarifa_iva_codigo}}" required>
                </div>
            </div>
            <div class="form-group row">
                <label for="centro_consumo_fecha_ingreso" class="col-sm-2 col-form-label">Porcentaje</label>
                <div class="col-sm-10">
                    <input type="range" class="form-control" value="{{$tarifaIva->tarifa_iva_porcentaje}}" min="0" max="100" step="0.01" oninput="updateRangeInput(this)">
                    <input type="number" class="form-control" id="tarifa_iva_porcentaje" name="tarifa_iva_porcentaje" value="{{$tarifaIva->tarifa_iva_porcentaje}}" min="0" max="100" step="0.01" required>
                </div>
            </div>                                                               
            <div class="form-group row">
                <label for="tarifa_iva_estado" class="col-sm-2 col-form-label">Estado</label>
                <div class="col-sm-10">
                    <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                        @if($tarifaIva->tarifa_iva_estado=="1")
                            <input type="checkbox" class="custom-control-input" id="tarifa_iva_estado" name="tarifa_iva_estado" checked>
                        @else
                            <input type="checkbox" class="custom-control-input" id="tarifa_iva_estado" name="tarifa_iva_estado">
                        @endif
                        <label class="custom-control-label" for="tarifa_iva_estado"></label>
                    </div>
                </div>                
            </div> 
        </div>            
    </div>
</form>
@endsection