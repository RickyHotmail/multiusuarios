@extends ('admin.layouts.admin')
@section('principal')
<form class="form-horizontal" method="POST" action="{{ route('detallelaboratorio.update', [$detalleexamen->detalle_id]) }}">
@method('PUT')
@csrf
    <div class="card card-secondary">
        <div class="card-header">
            <h3 class="card-title">Editar Detalle de laboratorio</h3>
            <div class="float-right">
                <button type="submit" class="btn btn-success btn-sm"><i class="fa fa-save"></i>&nbsp;Guardar</button>
                <button type="button"  onclick='window.location = "{{ url("examen/{$detalleexamen->detalle_id}/agregarValores")}}";' class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
            </div>
        </div>
        <div class="card-body">
            <div class="form-group row">
                <label for="examen_nombre" class="col-sm-2 col-form-label">Nombre del Detalle del Laboratorio</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="examen_nombre" name="examen_nombre" placeholder="Nombre" value="{{$detalleexamen->detalle_nombre}}" required>
                    <input type="hidden" id="id_examen" name="id_examen" value="{{$detalleexamen->detalle_id}}">
                </div>
            </div> 
            <div class="form-group row">
                <label for="examen_nombre" class="col-sm-2 col-form-label">Unidad de Medida</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="examen_unidad" name="examen_unidad" placeholder="Unidad de Medida" value="{{$detalleexamen->detalle_medida}}" >
                </div>
            </div> 
            <div class="form-group row">
                <label for="examen_nombre" class="col-sm-2 col-form-label">Abrebiatura</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="examen_abrebiatura" name="examen_abrebiatura" placeholder="TipAbrebiaturao" value="{{$detalleexamen->detalle_abreviatura}}" required>
                </div>
            </div>  
            <div class="form-group row">
                <label for="examen_nombre" class="col-sm-2 col-form-label">Valor Minimo</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="detalle_minimo" name="detalle_minimo" placeholder="Valor Minimo"  step="0.01" min="0" value="{{$detalleexamen->detalle_minimo}}" required>
                </div>
            </div> 
            <div class="form-group row">
                <label for="examen_nombre" class="col-sm-2 col-form-label">Valor Maximo</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="detalle_maximo" name="detalle_maximo" placeholder="Valor Maximo"  step="0.01" min="0" value="{{$detalleexamen->detalle_maximo}}" required>
                </div>
            </div>                                               
            <div class="form-group row">
                <label for="examen_estado" class="col-sm-2 col-form-label">Estado</label>
                <div class="col-sm-10">
                    <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                        @if($detalleexamen->detalle_estado=="1")
                            <input type="checkbox" class="custom-control-input" id="examen_estado" name="examen_estado" checked>
                        @else
                            <input type="checkbox" class="custom-control-input" id="examen_estado" name="examen_estado">
                        @endif
                        <label class="custom-control-label" for="examen_estado"></label>
                    </div>
                </div>                
            </div> 
        </div>            
    </div>
</form>
@endsection