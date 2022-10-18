@extends ('admin.layouts.admin')
@section('principal')
<form class="form-horizontal" method="POST" action="{{ url("ingresoPrestamo") }}">
    @csrf
    <div class="card card-secondary col-sm-7">
        <div class="card-header">
            <div class="row">
                <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5">
                    <h2 class="card-title">Ingreso de Producto Prestado</h2>
                </div>
                <div class="col-xs-7 col-sm-7 col-md-7 col-lg-7">
                    <div class="float-right">
                        <button id="guardarID" type="submit" id="guardar" name="guardar" class="btn btn-success btn-sm"><i
                                class="fa fa-save"></i><span> Guardar</span></button>
                        <button type="button" id="cancelarID" name="cancelarID" onclick="javascript:location.reload()"
                            class="btn btn-default btn-sm not-active-neo"><i class="fas fa-times-circle"></i><span>
                                Cancelar</span></button>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.card-header -->
        <div class="card-body">
            <div class="card-body">
                <div class="form-group row">
                    <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2 form-control-label ">
                        <label>Numero</label>
                    </div>
                    <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                        <input id="punto_id" name="punto_id" value="{{ $rangoDocumento->puntoEmision->punto_id }}"
                            type="hidden">
                        <input id="rango_id" name="rango_id" value="{{ $rangoDocumento->rango_id }}" type="hidden">
                        <input type="text" id="ingreso_serie" name="ingreso_serie"
                            value="{{ $rangoDocumento->puntoEmision->sucursal->sucursal_codigo }}{{ $rangoDocumento->puntoEmision->punto_serie }}"
                            class="form-control derecha-texto negrita " placeholder="Serie" required readonly>
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                        <input type="text" id="ingreso_numero" name="ingreso_numero" value="{{ $secuencial }}"
                            class="form-control  negrita " placeholder="Numero" required readonly>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="idFecha" class="col-sm-2 col-form-label">Fecha</label>
                    <div class="col-sm-10">
                        <input type="date" class="form-control" id="idFecha" name="idFecha"
                            value='<?php echo(date("Y")."-".date("m")."-".date("d")); ?>' required>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="idCaja" class="col-sm-2 col-form-label">Cliente</label>
                    <div class="col-sm-10">
                        <select class="custom-select select2" id="cliente_id" name="cliente_id" required>
                            <option value="" label>--Seleccione un Cliente--</option>
                                @foreach($clientes as $cliente)
                                    <option  value="{{$cliente->cliente_id}}">{{$cliente->cliente_nombre}}</option>
                                @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="idCaja" class="col-sm-2 col-form-label">Producto</label>
                    <div class="col-sm-10">
                        <select class="custom-select select2" id="producto_id" name="producto_id" required>
                            <option value="" label>--Seleccione un Producto--</option>
                                @foreach($productos as $producto)
                                    <option  value="{{$producto->producto_id}}">{{$producto->producto_nombre}}</option>
                                @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="Transportista_id" class="col-sm-2 col-form-label">Transportista</label>
                    <div class="col-sm-10">
                        <select class="custom-select select2" id="Transportista_id" name="Transportista_id" required>
                            <option value="" label>--Seleccione un Producto--</option>
                                @foreach($transportistas as $transportista)
                                    <option  value="{{$transportista->transportista_id}}">{{$transportista->transportista_nombre}}</option>
                                @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="idPlaca" class="col-sm-2 col-form-label">Placa</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="idPlaca" name="idPlaca" placeholder="Placa" required>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="idValor" class="col-sm-2 col-form-label">Cantidad</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="idValor" name="idValor" placeholder="0.00" required>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="idMensaje" class="col-sm-2 col-form-label">Comentario</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="idMensaje" name="idMensaje" required>
                    </div>
                </div>

            </div>
</form>
</div>
<!-- /.card-body -->
</div>
<!-- /.card -->
@endsection