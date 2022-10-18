@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">Grupo de Permisos</h3>
        <button onclick='window.location = "{{ url("grupo") }}";' class="btn btn-default btn-sm float-right"><i class="fa fa-undo"></i>&nbsp;Atras</button>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <div class="card-body">
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Nombre de Grupo</label>
                <div class="col-sm-10">
                    <label class="form-control">{{$grupo->grupo_nombre}}</label>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Icono</label>
                <div class="col-sm-10">
                    <label class="form-control">{{$grupo->grupo_icono}}</label>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Orden de Grupo</label>
                <div class="col-sm-10">
                    <label class="form-control">{{$grupo->grupo_orden}}</label>
                </div>
            </div>            
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Estado</label>
                <div class="col-sm-10">
                    @if($grupo->grupo_estado=="1")
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