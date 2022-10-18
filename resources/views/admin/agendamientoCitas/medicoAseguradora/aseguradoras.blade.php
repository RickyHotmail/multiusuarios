@extends ('admin.layouts.admin')
@section('principal')
<form class="form-horizontal" method="POST" action="{{ route('medicoAseguradora.guardarAseguradoras', [$medico->medico_id]) }}">
@csrf
    <div class="card card-secondary">
        <div class="card-header">
            <h3 class="card-title">Asignar Aseguradoras</h3>
            <div class="float-right">
                <button type="submit" class="btn btn-success btn-sm"><i class="fa fa-save"></i>&nbsp;Guardar</button>
                <button type="button" onclick='window.location = "{{ url("medico") }}";' class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
            </div>
        </div>
        <div class="card-body">
            <div class="form-group">
                <label for="Medico">Medico</label>
                <input type="text" class="form-control" name="Medico" placeholder="Ingrese nombre" value="{{$nombreMedico}}" disabled>
            </div>
            <div class="form-group">
                <label>Seleccionar Aseguradora</label>
                <div class="well listview-pagupa">
                    <div class="">
                        <ul class="list-group">
                            @foreach($clientesAseguradoras as $clientesAseguradora)
                                @if($clientesAseguradora->tipo_cliente_nombre == "Aseguradora")
                                    <?php $aseguradoraM_estado = 0 ?>
                                    @foreach($medico->aseguradoras as $aseguradoraM)
                                        @if($aseguradoraM->cliente_id == $clientesAseguradora->cliente_id)
                                            <?php $aseguradoraM_estado = 1 ?>
                                        @endif
                                    @endforeach
                                    @if($aseguradoraM_estado==1)
                                        <li class="list-group-item"><div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input" id="{{ $clientesAseguradora->cliente_id}}" name="{{ $clientesAseguradora->cliente_id}}" checked><label for="{{ $clientesAseguradora->cliente_id}}" class="custom-control-label"> {{ $clientesAseguradora->cliente_nombre}}</label></div></li>
                                    @else
                                        <li class="list-group-item"><div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input" id="{{ $clientesAseguradora->cliente_id}}" name="{{ $clientesAseguradora->cliente_id}}"><label for="{{ $clientesAseguradora->cliente_id}}" class="custom-control-label"> {{ $clientesAseguradora->cliente_nombre}}</label></div></li>
                                    @endif
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