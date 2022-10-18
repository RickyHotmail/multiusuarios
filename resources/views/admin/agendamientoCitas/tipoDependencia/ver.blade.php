@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">Tipo Dependencia</h3>
        <button onclick='window.location = "{{ url("tipoDependencia") }}";' class="btn btn-default btn-sm float-right"><i class="fa fa-undo"></i>&nbsp;Atras</button>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <div class="form-group row">
            <label for="codigo" class="col-sm-3 col-form-label">Código</label>
            <div class="col-sm-9">
                <label class="form-control">{{$tipoDependencia->tipod_codigo}}</label>
            </div>
        </div>
        <div class="form-group row">
            <label for="nombre" class="col-sm-3 col-form-label">Nombre</label>
            <div class="col-sm-9">
                <label class="form-control">{{$tipoDependencia->tipod_nombre}}</label>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-3 col-form-label">Estado</label>
            <div class="col-sm-9 col-form-label">
                @if($tipoDependencia->tipod_estado=="1")
                <i class="fa fa-check-circle neo-verde"></i>
                @else
                <i class="fa fa-times-circle neo-rojo"></i>
                @endif
            </div>
        </div>
    </div>
</div>
<!-- /.card-body -->
</div>
<!-- /.card -->
@endsection