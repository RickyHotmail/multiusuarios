@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary">
    <div class="card-header">
        <div class="float-right"> 
                @csrf
                <!-- 
                <button type="button" onclick='window.location = "{{ url("prestamos") }}";' class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
                --> 
                <button  type="button" onclick="history.back()" class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
        </div>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <div class="card-body">
        <div class="form-group row">
                <label class="col-sm-2 col-form-label">Sucursal</label>
                <div class="col-sm-10">
                    <label class="form-control">{{$seguro->sucursal->sucursal_nombre}}</label>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Proveedor</label>
                <div class="col-sm-10">
                    <label class="form-control">{{$seguro->transaccionCompra->proveedor->proveedor_nombre }}</label>
                </div>
            </div>  
            <div class="form-group row">
                <label for="pais_id" class="col-sm-2 col-form-label">Factura</label>
                <div class="col-sm-10">                        
                    <label class="form-control">{{$seguro->transaccionCompra->transaccion_numero}}</label>                          
                </div>
            </div>
            <div class="form-group row">
                <label for="pais_id" class="col-sm-2 col-form-label">Fecha</label>
                <div class="col-sm-10">                        
                    <label class="form-control">{{$seguro->amortizacion_fecha}}</label>                          
                </div>
            </div>
            <div class="form-group row">
                <label for="pais_id" class="col-sm-2 col-form-label">Valor</label>
                <div class="col-sm-10">                        
                    <label class="form-control">{{ $seguro->amortizacion_total }}</label>                          
                </div>
            </div>
            <div class="form-group row">
                <label for="ciudad_id" class="col-sm-2 col-form-label">Cuenta Debe</label>
                <div class="col-sm-10">                        
                    <label class="form-control">{{ $seguro->cuentadebe->cuenta_numero.' -  '.$seguro->cuentadebe->cuenta_nombre }}</label>                          
                </div>
            </div>
            <div class="form-group row">
                <label for="sucursal_id" class="col-sm-2 col-form-label">Observación</label>
                <div class="col-sm-10">                        
                    <label class="form-control">{{ $seguro->amortizacion_observacion }}</label>                          
                </div>
            </div>
           
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Estado</label>
                <div class="col-sm-10">
                    @if($seguro->amortizacion_estado=="1")
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