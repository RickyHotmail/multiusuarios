@extends ('admin.layouts.admin')
@section('principal')
<form class="form-horizontal" method="POST" action="{{ route('caja.guardarUsuario', [$caja->caja_id]) }}">
@csrf
    <div class="card card-secondary">
        <div class="card-header">
            <h3 class="card-title">Asignar Usuarios</h3>
            <div class="float-right">
                <button type="submit" class="btn btn-success btn-sm"><i class="fa fa-save"></i>&nbsp;Guardar</button>
                <!-- 
                <button type="button" onclick='window.location = "{{ url("caja") }}";' class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
            --> 
            <button  type="button" onclick="history.back()" class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
            </div>
        </div>
        <div class="card-body">
            <div class="form-group">
                <label for="user_username">Nombre de Caja</label>
                <input type="text" class="form-control" id="idname" name="idname" value="{{$caja->caja_nombre}}" disabled>
                <p class="small text-muted font-italic mb-4">Seleccione los usuarios que van a pertenecer a esta caja.</p>
            </div>
            <div class="form-group">
                <label>Seleccionar Usuarios</label>                    
                <div class="well listview-pagupa">
                    <ul class="list-group">
                    @foreach($usuarios as $usuario)
                        @if(true)
                            <?php $puntoEstado=0 ?>
                            @foreach($cajaUsers as $cajaUser)
                                @if($cajaUser->user_id == $usuario->user_id)
                                    <?php $puntoEstado=1 ?> 
                                @endif
                            @endforeach                       
                            @if($puntoEstado==1)
                                <li class="list-group-item"><div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input" id="{{ $usuario->user_id }}" name="{{ $usuario->user_id }}" checked><label for="{{ $usuario->user_id }}" class="custom-control-label"> {{ $usuario->user_nombre }}</label></div></li>
                            @else
                                <li class="list-group-item"><div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input" id="{{ $usuario->user_id }}" name="{{ $usuario->user_id }}"><label for="{{ $usuario->user_id }}" class="custom-control-label"> {{ $usuario->user_nombre }}</label></div></li>
                            
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