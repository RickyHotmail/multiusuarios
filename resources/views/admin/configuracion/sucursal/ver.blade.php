@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">Sucursal</h3>
         <!--   
        <button onclick='window.location = "{{ url("sucursal") }}";' class="btn btn-default btn-sm float-right"><i class="fa fa-undo"></i>&nbsp;Atras</button>
        --> 
            <button  type="button" onclick="history.back()" class="btn btn-default btn-sm float-right"><i class="fa fa-undo"></i>&nbsp;Atras</button>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <div class="card-body">
        <div class="form-group row">
                <label class="col-sm-2 col-form-label">Nombre Sucursal</label>
                <div class="col-sm-10">
                    <label class="form-control">{{$sucursal->sucursal_nombre}}</label>
                </div>
            </div>       
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Código Sucursal</label>
                <div class="col-sm-10">
                    <label class="form-control">{{$sucursal->sucursal_codigo}}</label>
                </div>
            </div>      
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Dirección</label>
                <div class="col-sm-10">
                    <label class="form-control">{{$sucursal->sucursal_direccion}}</label>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Teléfono</label>
                <div class="col-sm-10">
                    <label class="form-control">{{$sucursal->sucursal_telefono}}</label>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Estado</label>
                <div class="col-sm-10">
                    @if($sucursal->sucursal_estado=="1")
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