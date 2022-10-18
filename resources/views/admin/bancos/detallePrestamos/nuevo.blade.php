@extends ('admin.layouts.admin')
@section('principal')
<form class="form-horizontal" method="POST" action="{{ url("detalleprestamos") }}">
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
                        <button type="button" onclick='window.location = "{{ url("prestamos") }}";' class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <h5 class="form-control" style="color:#fff; background:#17a2b8;">Datos del Prestamo</h5>
            <div class="card-body">
                <div class="form-group row">
                    <label for="tipo_movimiento" class="col-sm-2 col-form-label">Banco</label>
                    <div class="col-sm-10">
                        <input type="hidden" class="form-control" id="idFechaini" name="idFechaini" value="{{$prestamos->prestamo_inicio}}" >
                        <input type="hidden" class="form-control" id="idinteres" name="idinteres" value="{{$prestamos->prestamo_interes}}" >
                        <input type="hidden" class="form-control" id="idprestamo" name="idprestamo" value="{{$prestamos->prestamo_id}}" >
                        <label  class="form-control">{{$prestamos->banco->bancoLista->banco_lista_nombre}}</label>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="tipo_movimiento" class="col-sm-2 col-form-label">Monto</label>
                    <div class="col-sm-10">
                        <label  class="form-control">{{$prestamos->prestamo_monto}}</label>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="tipo_movimiento" class="col-sm-2 col-form-label">Interes</label>
                    <div class="col-sm-10">
                        <label  class="form-control">{{$prestamos->prestamo_interes}}</label>
                    </div>
                </div>
               
            </div>
            <h5 class="form-control" style="color:#fff; background:#17a2b8;">Datos del Interes</h5>
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
