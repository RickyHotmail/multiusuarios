@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">Categoria Rol</h3>
        <button onclick='window.location = "{{ url("categoriaRol") }}";' class="btn btn-default btn-sm float-right"><i class="fa fa-undo"></i>&nbsp;Atras</button>
    </div>
    <!-- /.card-header -->
    <div class="card-body">        
        <div class="form-group row">
            <label class="col-sm-2 col-form-label">Nombre</label>
            <div class="col-sm-10">
                <label class="form-control">{{$categoria->categoria_nombre}}</label>
            </div>
        </div>            
        <div class="form-group row">
            <label class="col-sm-2 col-form-label">Centro Consumo</label>
            <div class="col-sm-10">
                <label class="form-control">{{$categoria->centroconsumo->centro_consumo_nombre}}</label>
            </div>
        </div>         
        <div class="form-group row">
            <label class="col-sm-2 col-form-label">Estado</label>
            <div class="col-sm-10">
                @if($categoria->categoria_estado=="1")
                    <i class="fa fa-check-circle neo-verde"></i>
                @else
                    <i class="fa fa-times-circle neo-rojo"></i>
                @endif
            </div>
        </div> 
        <!-- /.card-footer -->
    </div>
    <!-- /.card-body -->
</div>
<!-- /.card -->
@endsection