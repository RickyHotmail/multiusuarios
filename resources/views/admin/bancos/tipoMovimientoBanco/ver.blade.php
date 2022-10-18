@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">Tipo de Movimiento Banco</h3>
         <!--
        <button onclick='window.location = "{{ url("tipoMovimientoBanco") }}";' class="btn btn-default btn-sm float-right"><i class="fa fa-undo"></i>&nbsp;Atras</button>
        --> 
        <button  type="button" onclick="history.back()" class="btn btn-default btn-sm float-right"><i class="fa fa-undo"></i>&nbsp;Atras</button>
    </div>
    <!-- /.card-header -->
    <div class="card-body">           
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Sucursal</label>
                <div class="col-sm-10">
                    <label class="form-control">{{$tipoMovimientoBanco->sucursal->sucursal_nombre}}</label>
                </div>
            </div> 
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Nombre</label>
                <div class="col-sm-10">
                    <label class="form-control">{{$tipoMovimientoBanco->tipo_nombre}}</label>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Tipo Movimiento</label>
                <div class="col-sm-10">
                    <label class="form-control">{{$tipoMovimientoBanco->tipo_movimiento}}</label>
                </div>
            </div>
            @if(Auth::user()->empresa->empresa_contabilidad == '1')
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Cuenta</label>
                <div class="col-sm-10">
                    <label class="form-control">{{$tipoMovimientoBanco->cuenta->cuenta_numero. '-' .$tipoMovimientoBanco->cuenta->cuenta_nombre}}</label>
                </div>
            </div>   
            @endif                    
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Estado</label>
                <div class="col-sm-10">
                    @if($tipoMovimientoBanco->tipo_estado=="1")
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