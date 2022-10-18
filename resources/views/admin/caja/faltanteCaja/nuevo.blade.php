@extends ('admin.layouts.admin')
@section('principal')
<form class="form-horizontal" method="POST" action="{{ url("faltanteCaja") }}">
    @csrf
    <div class="card card-secondary col-sm-8">
        <div class="card-header">
            <div class="row">
                <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5">
                    <h2 class="card-title">Nuevo Faltante de Caja</h2>
                </div>
                <div class="col-xs-7 col-sm-7 col-md-7 col-lg-7">
                    <div class="float-right">
                        <button id="guardarID" type="submit" class="btn btn-success btn-sm"><i
                                class="fa fa-save"></i><span> Guardar</span></button>
                        <button type="button" id="cancelarID" name="cancelarID" onclick="javascript:location.reload()"
                            class="btn btn-default btn-sm not-active-neo"><i
                                class="fas fa-times-circle"></i><span> Cancelar</span></button>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.card-header -->
        <div class="card-body">
                <h5 class="form-control" style="color:#fff; background:#17a2b8;">Datos de Cuenta</h5>
                <div class="card-body">
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label">Numero</label>
                    <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                        <input id="punto_id" name="punto_id" value="{{ $rangoDocumento->puntoEmision->punto_id }}"
                            type="hidden">
                        <input id="rango_id" name="rango_id" value="{{ $rangoDocumento->rango_id }}" type="hidden">
                        <input type="text" id="faltante_serie" name="faltante_serie"
                            value="{{ $rangoDocumento->puntoEmision->sucursal->sucursal_codigo }}{{ $rangoDocumento->puntoEmision->punto_serie }}"
                            class="form-control derecha-texto negrita " placeholder="Serie" required readonly>
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                        <input type="text" id="faltante_numero" name="faltante_numero" value="{{ $secuencial }}"
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
                        <select class="custom-select" id="idCaja" name="idCaja" required>                           
                            @if($cajasxusuario)
                                @foreach($cajas as $caja)
                                    @if($caja->caja_id == $cajasxusuario->caja_id)
                                        <option value="{{$caja->caja_id}}" selected>{{$caja->caja_nombre}}</option>                                   
                                    @endif
                                @endforeach
                            @else
                                <option value="" label>--No dispone de una Caja--</option>
                            @endif
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                        <label for="idValor" class="col-sm-2 col-form-label">Valor</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="idValor" name="idValor" placeholder="0.00" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="idMensaje" class="col-sm-2 col-form-label">Motivo</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="idMensaje" name="idMensaje" required>
                        </div>
                    </div>
                </div>     
        </div>
    </div>
</form>
@endsection