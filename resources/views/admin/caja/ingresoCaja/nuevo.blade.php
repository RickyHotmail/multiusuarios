@extends ('admin.layouts.admin')
@section('principal')
<form class="form-horizontal" method="POST" action="{{ url("ingresoCaja") }}">
    @csrf
    <div class="card card-secondary col-sm-7">
        <div class="card-header">
            <div class="row">
                <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5">
                    <h2 class="card-title">Ingreso de Caja en Efectivo</h2>
                </div>
                <div class="col-xs-7 col-sm-7 col-md-7 col-lg-7">
                    <div class="float-right">
                        <button id="guardarID" type="submit" class="btn btn-success btn-sm"><i
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
                    <label for="idCaja" class="col-sm-2 col-form-label">Caja</label>
                    <div class="col-sm-10">
                        <select class="custom-select select2" id="idCaja" name="idCaja" required>
                            <option value="" label>--Seleccione una caja--</option>
                            @if($cajasxusuario)
                            @foreach($cajas as $caja)
                            @if($caja->caja_id == $cajasxusuario->caja_id)
                            <option value="{{$caja->caja_id}}" selected>{{$caja->caja_nombre}}</option>
                            @endif
                            @endforeach
                            @endif
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="tipoId" class="col-sm-2 col-form-label">Motivo</label>
                    <div class="col-sm-10">
                        <select class="custom-select select2" id="tipoId" name="tipoId" required>
                            <option value="" label>--Seleccione una opcion--</option>
                            @foreach($TipoMovimientoCaja as $TipoMovimientoCajaa)
                            <option value="{{$TipoMovimientoCajaa->tipo_id}}">{{$TipoMovimientoCajaa->tipo_nombre}}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="idBeneficiario" class="col-sm-2 col-form-label">Beneficiario</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="idBeneficiario" name="idBeneficiario" required>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="idValor" class="col-sm-2 col-form-label">Valor</label>
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