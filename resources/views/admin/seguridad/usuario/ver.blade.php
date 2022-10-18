@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">Usuario</h3>
        <!--
        <button onclick='window.location = "{{ url("usuario") }}";' class="btn btn-default btn-sm float-right"><i class="fa fa-undo"></i>&nbsp;Atras</button>
        --> 
        <button  type="button" onclick="history.back()" class="btn btn-default btn-sm float-right"><i class="fa fa-undo"></i>&nbsp;Atras</button>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <div class="card-body">
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Username</label>
                <div class="col-sm-10">
                    <label class="form-control">{{$usuario->user_username}}</label>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Cédula</label>
                <div class="col-sm-10">
                    <label class="form-control">{{$usuario->user_cedula}}</label>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Nombre</label>
                <div class="col-sm-10">
                    <label class="form-control">{{$usuario->user_nombre}}</label>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Correo</label>
                <div class="col-sm-10">
                    <label class="form-control">{{$usuario->user_correo}}</label>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Estado</label>
                <div class="col-sm-10">
                    @if($usuario->user_estado=="1")
                    <i class="fa fa-check-circle neo-verde"></i>
                    @else
                    <i class="fa fa-times-circle neo-rojo"></i>
                    @endif
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Roles</label>
            </div>
            <div class="form-group row">
                <div class="col-sm-12">
                    <div class="well listview-pagupa">
                        <div class="">
                            <ul class="list-group">
                                @foreach($usuario->roles as $rol)
                                <li class="list-group-item"><i
                                        class="fa fa-check-square neo-azul fa-lg"></i>&nbsp;&nbsp;<label>
                                        {{ $rol->rol_nombre}}</label></li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.card-body -->
    </div>
    <!-- /.card-body -->
</div>
<!-- /.card -->
@endsection