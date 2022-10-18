@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">¿Esta seguro de eliminar este permiso?</h3>
        <div class="float-right">
            <form class="form-horizontal" method="POST" action="{{ route('permiso.destroy', [$permiso->permiso_id]) }}">
                @method('DELETE')
                @csrf
                <button type="submit" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i>&nbsp;Eliminar</button>
                <!-- 
                <button type="button" onclick='window.location = "{{ url("permiso") }}";' class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
                --> 
                <button  type="button" onclick="history.back()" class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>   
            </form>
        </div>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <div class="card-body">
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Nombre de Permiso</label>
                <div class="col-sm-10">
                    <label class="form-control">{{$permiso->permiso_nombre}}</label>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Ruta</label>
                <div class="col-sm-10">
                    <label class="form-control">{{$permiso->permiso_ruta}}</label>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Tipo Permiso</label>
                <div class="col-sm-10">
                    <label class="form-control">
                        @if($permiso->permiso_tipo == 1) Administrador @endif
                        @if($permiso->permiso_tipo == 2) Cliente @endif
                    </label>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Icono</label>
                <div class="col-sm-10">
                    <label class="form-control">{{$permiso->permiso_icono}}</label>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Orden de Permiso</label>
                <div class="col-sm-10">
                    <label class="form-control">{{$permiso->permiso_orden}}</label>
                </div>
            </div> 
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Grupo</label>
                <div class="col-sm-10">
                    <label class="form-control">{{$permiso->grupo_nombre}}</label>
                </div>
            </div>   
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Tipo Grupo</label>
                <div class="col-sm-10">
                    <label class="form-control">@if(isset($permiso->tipo)){{ $permiso->tipo->tipo_nombre}}@endif</label>
                </div>
            </div>             
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Estado</label>
                <div class="col-sm-10">
                    @if($permiso->permiso_estado=="1")
                    <i class="fa fa-check-circle neo-verde"></i>
                    @else
                    <i class="fa fa-times-circle neo-rojo"></i>
                    @endif
                </div>
            </div>
        </div>
        <!-- /.card-body -->    
    </div>
    <!-- /.card-body -->
</div>
<!-- /.card -->
@endsection