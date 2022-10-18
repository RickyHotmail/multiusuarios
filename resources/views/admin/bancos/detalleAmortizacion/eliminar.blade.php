@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">¿Esta seguro de eliminar el Interes?</h3>
        <div class="float-right">
            <form class="form-horizontal" method="POST" action="{{ route('detalleamortizacion.destroy', [$detalle->detalle_id]) }}">
                @method('DELETE')
                @csrf
                <button type="submit" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i>&nbsp;Eliminar</button>
                <!--
                <button type="button" onclick='window.location = "{{ url("detalleprestamos/{$detalle->prestamo_id}") }}";' class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
                -->   
                <button  type="button" onclick="history.back()" class="btn btn-default btn-sm "><i class="fa fa-undo"></i>&nbsp;Atras</button>   
            </form>
        </div>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <h5 class="form-control" style="color:#fff; background:#17a2b8;">Datos del Amortizacion de Seguro</h5>
        <div class="card-body">
        
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Factura</label>
                <div class="col-sm-10">
                    <label class="form-control">{{$detalle->seguro->transaccionCompra->transaccion_numero}}</label>
                </div>
            </div>  
            <div class="form-group row">
                <label for="pais_id" class="col-sm-2 col-form-label">Fecha</label>
                <div class="col-sm-10">                        
                    <label class="form-control">{{$detalle->seguro->amortizacion_fecha}}</label>                          
                </div>
            </div>
            <div class="form-group row">
                <label for="pais_id" class="col-sm-2 col-form-label">Total</label>
                <div class="col-sm-10">                        
                    <label class="form-control">{{$detalle->seguro->amortizacion_total}}</label>                          
                </div>
            </div>
           
            <div class="form-group row">
                <label for="ciudad_id" class="col-sm-2 col-form-label">Periodo</label>
                <div class="col-sm-10">                        
                    <label class="form-control">{{ $detalle->seguro->amortizacion_periodo}}</label>                          
                </div>
            </div>
                     
        </div>
        <h5 class="form-control" style="color:#fff; background:#17a2b8;">Datos del Amortizacion</h5>
        <div class="card-body">
        
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Fecha</label>
                <div class="col-sm-10">
                    <label class="form-control">{{$detalle->detalle_fecha}}</label>
                </div>
            </div>  
            
            <div class="form-group row">
                <label for="pais_id" class="col-sm-2 col-form-label">Monto</label>
                <div class="col-sm-10">                        
                    <label class="form-control">{{$detalle->detalle_valor}}</label>                          
                </div>
            </div>
            
        </div>
        <!-- /.card-body -->    
    </div>
    <!-- /.card-body -->
</div>
<!-- /.card -->
@endsection