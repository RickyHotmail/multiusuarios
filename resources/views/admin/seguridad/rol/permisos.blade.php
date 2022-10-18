@extends ('admin.layouts.admin')
@section('principal')
<form class="form-horizontal" method="POST" action="{{ route('rol.guardarPermisos', [$rol->rol_id]) }}">
@csrf
    <div class="card card-secondary">
        <div class="card-header">
            <h3 class="card-title">Asignar Permisos</h3>
            <div class="float-right">
                <button type="submit" class="btn btn-success btn-sm"><i class="fa fa-save"></i>&nbsp;Guardar</button>
                <!-- 
                <button type="button" onclick='window.location = "{{ url("rol") }}";' class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
                --> 
                <button  type="button" onclick="history.back()" class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
            </div>
        </div>
        <div class="card-body">
            <div class="form-group">
                <label for="rol_nombre">Nombre del Rol</label>
                <input type="text" class="form-control" name="rol_nombre" placeholder="Ingrese nombre" value="{{$rol->rol_nombre}}" disabled>
            </div>
            <div class="form-group">
                <label>Seleccionar permisos</label>
                <div class="well listview-pagupa">
                    <div class="">
                        <table class="table table-hover">
                            <tbody>
                                @foreach($grupos as $grupo)
                                <tr data-widget="expandable-table" aria-expanded="false">
                                    <td>
                                        <i class="fas fa-angle-left right"></i>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i class="nav-icon {{$grupo->grupo_icono}}"></i>&nbsp;&nbsp;{{$grupo->grupo_nombre}}
                                    </td>
                                </tr>
                                
                                <tr class="expandable-body">
                                    <td>
                                    <div class="p-0">
                                    <table class="table table-hover">
                                        <tbody>   
                                            @foreach($grupo->detalles as $tipopermiso)
                                                <tr data-widget="expandable-table" aria-expanded="false">
                                                    <td>
                                                        <i class="fas fa-angle-left right"></i>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i class="nav-icon {{$tipopermiso->tipo_icono}}"></i>&nbsp;&nbsp;{{$tipopermiso->tipo_nombre}}
                                                    </td>
                                                </tr>
                                        
                                            
                                                <tr class="expandable-body">
                                                    <td>
                                                        <div class="p-0">
                                                            <table class="table table-hover">
                                                                <tbody>
                                                                @foreach($tipopermiso->permisos as $permiso)
                                                                    @if($permiso->permiso_estado == '1')
                                                                        @if($rol->rol_tipo == $permiso->permiso_tipo)
                                                                            <?php $permisoEstado=0 ?>
                                                                            @foreach($rol->permisos as $rolpermiso)
                                                                                @if($rolpermiso->permiso_id == $permiso->permiso_id)
                                                                                    <?php $permisoEstado=1 ?>
                                                                                @endif
                                                                            @endforeach
                                                                            @if($permisoEstado==1)
                                                                                <tr><td><div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input" id="{{ $permiso->permiso_id}}" name="{{ $permiso->permiso_id}}" checked><label for="{{ $permiso->permiso_id}}" class="custom-control-label"> {{ $permiso->permiso_nombre}}</label></div></td></tr>
                                                                            @else
                                                                                <tr><td><div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input" id="{{ $permiso->permiso_id}}" name="{{ $permiso->permiso_id}}"><label for="{{ $permiso->permiso_id}}" class="custom-control-label"> {{ $permiso->permiso_nombre}}</label></div></td></tr>
                                                                            @endif
                                                                        @endif
                                                                    @endif
                                                                @endforeach
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </td>
                                                </tr>
                                            
                                            @endforeach
                                        </tbody>
                                    </table>
                                    </div>
                      
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection