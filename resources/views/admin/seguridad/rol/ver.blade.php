@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">Rol</h3>
        <!-- 
        <button onclick='window.location = "{{ url("rol") }}";' class="btn btn-default btn-sm float-right"><i class="fa fa-undo"></i>&nbsp;Atras</button>
        --> 
        <button  type="button" onclick="history.back()" class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <div class="card-body">
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Nombre</label>
                <div class="col-sm-10">
                    <label class="form-control">{{$rol->rol_nombre}}</label>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Tipo</label>
                <div class="col-sm-10">
                    <label class="form-control">
                    @if($rol->rol_tipo == 1) Administrador @endif
                    @if($rol->rol_tipo == 2) Cliente @endif
                    </label>
                </div>
            </div>     
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Estado</label>
                <div class="col-sm-10">
                    @if($rol->rol_estado=="1")
                    <i class="fa fa-check-circle neo-verde"></i>
                    @else
                    <i class="fa fa-times-circle neo-rojo"></i>
                    @endif
                </div>
            </div>   
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Permisos</label>
            </div>              
            <div class="form-group row">
                <div class="col-sm-12">
                    <div class="well listview-pagupa">
                        <div class="">
                            <ul class="list-group">
                                @foreach($rol->permisos as $permiso)
                                <li class="list-group-item"><i class="fa fa-check-square neo-azul fa-lg"></i>&nbsp;&nbsp;<label> {{ $permiso->permiso_nombre}}</label></li>
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