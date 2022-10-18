@extends ('admin.layouts.admin')
@section('principal')
<form class="form-horizontal" method="POST" action="{{ route('usuario.guardarPuntosE', [$usuario->user_id]) }}">
@csrf
    <div class="card card-secondary">
        <div class="card-header">
            <h3 class="card-title">Asignar Permiso a Puntos de Emision</h3>
            <div class="float-right">
                <button type="submit" class="btn btn-success btn-sm"><i class="fa fa-save"></i>&nbsp;Guardar</button>
                <!-- 
                <button type="button" onclick='window.location = "{{ url("usuario") }}";' class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
                --> 
                <button  type="button" onclick="history.back()" class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
            </div>
        </div>
        <div class="card-body">
            <div class="form-group">
                <label for="user_username">Nombre del Usuario</label>
                <input type="text" class="form-control" name="user_username" placeholder="Ingrese nombre" value="{{$usuario->user_username}}" disabled>
            </div>
            <div class="form-group">
                <label>Seleccionar Puntos de Emisión</label>
                <div class="well listview-pagupa">
                    <ul class="list-group">
                    @foreach($puntosE as $punto)
                        @if(true)
                            <?php $puntoEstado=0 ?>
                            @foreach($usuario->puntosEmision as $puntosUser)
                                @if($puntosUser->punto_id == $punto->punto_id)
                                    <?php $puntoEstado=1 ?>
                                @endif
                            @endforeach
                            @if($puntoEstado==1)
                                <li class="list-group-item"><div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input" id="{{ $punto->punto_id}}" name="{{ $punto->punto_id}}" checked><label for="{{ $punto->punto_id}}" class="custom-control-label"> {{ $punto->sucursal->sucursal_codigo}}{{ $punto->punto_serie}} - Punto de emisión</label></div></li>
                            @else
                                <li class="list-group-item"><div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input" id="{{ $punto->punto_id}}" name="{{ $punto->punto_id}}"><label for="{{ $punto->punto_id}}" class="custom-control-label"> {{ $punto->sucursal->sucursal_codigo}}{{ $punto->punto_serie}} - Punto de emisión</label></div></li>
                            @endif
                        @endif
                    @endforeach
                    </ul>
                </div>
            </div>	
        </div>
    </div>
</form>
@endsection