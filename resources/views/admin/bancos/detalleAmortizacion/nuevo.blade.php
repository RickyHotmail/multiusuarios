@extends ('admin.layouts.admin')
@section('principal')
<form class="form-horizontal" method="POST" action="{{ url("detalleamortizacion") }}">
    @csrf
    <div class="card card-secondary col-sm-8">
        <!-- /.card-header -->
        <div class="card-header">
            <div class="row">
                <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5">
                    <h2 class="card-title">Nuevo Valor de amortizacion</h2>
                </div>
                <div class="col-xs-7 col-sm-7 col-md-7 col-lg-7">
                    <div class="float-right">
                        <button id="guardarID" type="submit" class="btn btn-success btn-sm"><i class="fa fa-save"></i><span> Guardar</span></button>
                        <button type="button" onclick='window.location = "{{ url("amortizacion") }}";' class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <h5 class="form-control" style="color:#fff; background:#17a2b8;">Datos del Amortizacion de Seguro</h5>
            <div class="card-body">
                <div class="form-group row">
                    <label for="tipo_movimiento" class="col-sm-2 col-form-label">Factura</label>
                    <div class="col-sm-10">
                        <input type="hidden" class="form-control" id="idseguro" name="idseguro" value="{{$seguro->amortizacion_id}}" >
                        <label  class="form-control">{{$seguro->transaccionCompra->transaccion_numero}}</label>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="tipo_movimiento" class="col-sm-2 col-form-label">Fecha</label>
                    <div class="col-sm-10">
                        <label  class="form-control">{{$seguro->amortizacion_fecha}}</label>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="tipo_movimiento" class="col-sm-2 col-form-label">Total</label>
                    <div class="col-sm-10">
                        <label  class="form-control">{{$seguro->amortizacion_total}}</label>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="tipo_movimiento" class="col-sm-2 col-form-label">Periodo</label>
                    <div class="col-sm-10">
                        <label  class="form-control">{{$seguro->amortizacion_periodo}}</label>
                    </div>
                </div>
               
            </div>
            <h5 class="form-control" style="color:#fff; background:#17a2b8;">Datos del Amortizacion</h5>
            <div class="card-body ">
                <div class="form-group row">
                    <label for="idFecha" class="col-sm-2 col-form-label">Fecha</label>
                    <div class="col-sm-10">
                        <input type="date" class="form-control" id="idFecha" name="idFecha" value='<?php echo (date("Y") . "-" . date("m") . "-" . date("d")); ?>' required>
                    </div>
                </div>
                
                <div class="form-group row">
                    <label for="idValor" class="col-sm-2 col-form-label">Valor</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="idValor" name="idValor" placeholder="0.00" required>
                    </div>
                </div>

            </div>
        </div>
        <!-- /.card-body -->
    </div>
    <!-- /.card -->
</form>

@endsection
