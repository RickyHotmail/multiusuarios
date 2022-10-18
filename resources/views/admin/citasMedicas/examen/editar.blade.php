@extends ('admin.layouts.admin')
@section('principal')
<form class="form-horizontal" method="POST" action="{{ url('ordenExamenEditar') }}/{{ $ordenExamen->orden_id }}/editar">

@csrf
    <div class="card card-secondary">
        <div class="card-header">
            <h3 class="card-title">Editar Orden de Examenes</h3>
            <div class="float-right">
                <button type="submit" class="btn btn-success btn-sm"><i class="fa fa-save"></i>&nbsp;Guardar</button>
                <button type="button" onclick='history.back();' class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <label  class="col-sm-2 col-form-label">Exámenes a realizarse</label>
                <div class="select2-purple col-12">
                    <select class="select2 form-check-input" id="select222" name="laboratorio[]" multiple="multiple" data-placeholder="Selecione Examenes" data-dropdown-css-class="select2-purple" style="width: 100%;">
                        @foreach($examenes as $examen)
                            <option 
                                @foreach($examenesDetalle as $examenDet)
                                    @if($examen->examen_id==$examenDet->examen_id) 
                                        selected
                                    @endif
                                @endforeach
                                value="{{$examen->examen_id}}"
                            >{{ $examen->producto_codigo}} - {{$examen->producto_nombre }}</option>
                        @endforeach
                    </select>
                </div>
            </div> 
            <div class="col-sm-12">
                <div class="form-group row">
                    <label for="observacion" class="col-sm-5 col-form-label">Otros:</label>
                    <textarea class="form-control" id="otros_examenes"   name="otros_examenes" >{{ $ordenExamen->orden_otros }}</textarea>
                </div>  
            </div>  
        </div>            
    </div>
</form>
@endsection