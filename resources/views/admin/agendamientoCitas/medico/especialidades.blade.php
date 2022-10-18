@extends ('admin.layouts.admin')
@section('principal')
<form class="form-horizontal" method="POST" action="{{ url("medico/especialidades") }}">
@csrf
    <div class="card card-secondary">
        <div class="card-header">
            <h3 class="card-title">Asignar Especialidades a Medico</h3>
            <div class="float-right">
                <button type="submit" class="btn btn-success btn-sm"><i class="fa fa-save"></i>&nbsp;Guardar</button>
                <button type="button" onclick='window.location = "{{ url("medico") }}";' class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
            </div>
        </div>
        <div class="card-body">
            <div class="form-group row">
                <label for="producto_tipo" class="col-sm-2 col-form-label">Nombre</label>
                <div class="col-sm-10">
                    <input type="hidden" name="medico_id" value="{{$medico->medico_id}}"/>
                    @if($medico->empleado_id != '')
                        <label class="form-control">{{$medico->empleado->empleado_nombre}}</label>
                    @else
                        <label class="form-control">{{$medico->proveedor->proveedor_nombre}}</label>
                    @endif
                </div>
            </div>  
            <div class="form-group">
                <label>Seleccionar especialidades</label>
                <div class="well listview-pagupa">
                    <ul class="list-group">
                    @foreach($especialidades as $especialidad)
                            <?php $bandera=0 ?>
                            @foreach($medico->detalles as $detalleEspecialidad)
                                @if($detalleEspecialidad->especialidad_id == $especialidad->especialidad_id)
                                    <?php $bandera=1 ?>
                                @endif
                            @endforeach
                            @if($bandera==1)
                                <li class="list-group-item"><div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input" id="{{ $especialidad->especialidad_id }}" name="{{ $especialidad->especialidad_id }}" checked><label for="{{ $especialidad->especialidad_id }}" class="custom-control-label"> {{ $especialidad->especialidad_nombre }}</label></div></li>
                            @else
                                <li class="list-group-item"><div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input" id="{{ $especialidad->especialidad_id }}" name="{{ $especialidad->especialidad_id }}"><label for="{{ $especialidad->especialidad_id }}" class="custom-control-label"> {{ $especialidad->especialidad_nombre }}</label></div></li>
                            @endif
                       
                    @endforeach
                    </ul>
                </div>
            </div>
        </div>                        
    </div>
</form>
@endsection