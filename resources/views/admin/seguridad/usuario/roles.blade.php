@extends ('admin.layouts.admin')
@section('principal')
<form class="form-horizontal" method="POST" action="{{ route('usuario.guardarRoles', [$usuario->user_id]) }}">
@csrf
    <div class="card card-secondary">
        <div class="card-header">
            <h3 class="card-title">Asignar Roles</h3>
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
                <label>Seleccionar roles</label>
                <div class="well listview-pagupa">
                    <ul class="list-group">
                    @foreach($roles as $rol)
                        @if($rol->rol_tipo == $usuario->user_tipo)
                            <?php $rolEstado=0 ?>
                            @foreach($usuario->roles as $userrol)
                                @if($userrol->rol_id == $rol->rol_id)
                                    <?php $rolEstado=1 ?>
                                @endif
                            @endforeach
                            @if($rolEstado==1)
                                <li class="list-group-item"><div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input" id="{{ $rol->rol_id }}" name="{{ $rol->rol_id }}" checked><label for="{{ $rol->rol_id }}" class="custom-control-label"> {{ $rol->rol_nombre }}</label></div></li>
                            @else
                                <li class="list-group-item"><div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input" id="{{ $rol->rol_id }}" name="{{ $rol->rol_id }}"><label for="{{ $rol->rol_id }}" class="custom-control-label"> {{ $rol->rol_nombre }}</label></div></li>
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