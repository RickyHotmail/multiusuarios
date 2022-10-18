@extends ('admin.layouts.admin')
@section('principal')
<form class="form-horizontal" method="POST" action="{{ route('procedimientoEspecialidad.guardarEspecialidades', [$producto->producto_id]) }}">
@csrf
    <div class="card card-secondary">
        <div class="card-header">
            <h3 class="card-title">Asignar Especialidades</h3>
            <div class="float-right">
                <button type="submit" class="btn btn-success btn-sm"><i class="fa fa-save"></i>&nbsp;Guardar</button>
                <button type="button" onclick='window.location = "{{ url("procedimientoEspecialidad") }}";' class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
            </div>
        </div>
        <div class="card-body">

            <div class="form-group">
            <label for="rol_nombre">Nombre del Producto</label>
            <input type="text" class="form-control" name="rol_nombre" value="{{$producto->producto_nombre}}" disabled>
            </div>
            <div class="form-group">
                <label>Seleccionar especialidades</label>
                <div class="well listview-pagupa">
                    <div class="">
                        <ul class="list-group">
                        @foreach($especialidades as $especialidad)                                
                            <?php $estado = 0 ?>
                            @foreach($procedimientoEspecialidad as $procedimiento)                                    
                                @if($procedimiento->producto_id == $producto->producto_id && $procedimiento->especialidad_id == $especialidad->especialidad_id)
                                    <?php $estado = 1 ?>
                                @endif
                            @endforeach
                            @if($estado == 1)
                                @foreach ($procedimientoEspecialidad as $procedimiento) 
                                    <?php $existeEnAseguradora = false ?>
                                    <?php $esProducto = false ?>
                                    @foreach ($aseguradoras as $aseguradora) 
                                        @if($procedimiento->producto_id == $producto->producto_id && $procedimiento->especialidad_id == $especialidad->especialidad_id)
                                            @if($aseguradora->procedimiento_id == $procedimiento->procedimiento_id)
                                                <?php $existeEnAseguradora = true ?>
                                            @endif
                                            <?php $esProducto = true ?>
                                        @endif
                                    @endforeach
                                    @if($existeEnAseguradora) 
                                        <li class="list-group-item"><i class="fa fa-check-circle neo-verde" style="padding-right: 7px;"></i><label for="{{ $especialidad->especialidad_id}}"> {{ $especialidad->especialidad_nombre}}</label></li>
                                    @elseif($esProducto == true && $existeEnAseguradora == false)
                                        <li class="list-group-item"><div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input" id="{{ $especialidad->especialidad_id}}" name="{{ $especialidad->especialidad_id}}" checked><label for="{{ $especialidad->especialidad_id}}" class="custom-control-label"> {{ $especialidad->especialidad_nombre}}</label></div></li>
                                    @endif
                                @endforeach
                            @else
                                <li class="list-group-item"><div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input" id="{{ $especialidad->especialidad_id}}" name="{{ $especialidad->especialidad_id}}"><label for="{{ $especialidad->especialidad_id}}" class="custom-control-label">{{ $especialidad->especialidad_nombre}}</label></div></li>
                            @endif
                        @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
	</div>
</form>
@endsection