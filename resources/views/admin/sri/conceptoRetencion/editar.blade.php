@extends ('admin.layouts.admin')
@section('principal')
<form class="form-horizontal" method="POST" action="{{ route('conceptoRetencion.update', [$conceptoRetencion->concepto_id]) }}">
@method('PUT')
@csrf
    <div class="card card-secondary">
        <div class="card-header">
            <h3 class="card-title">Editar Concepto de Retencion</h3>
            <div class="float-right">
                <button type="submit" class="btn btn-success btn-sm"><i class="fa fa-save"></i>&nbsp;Guardar</button>
                 <!--
                <button type="button" onclick='window.location = "{{ url("conceptoRetencion") }}";' class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
                -->      
                <button  type="button" onclick="history.back()" class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button> 
            </div>
        </div>
    <div class="card-body">
            <div class="form-group row">
                <label for="forma_pago_codigo" class="col-sm-2 col-form-label">Nombre</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="forma_pago_codigo" name="idNombre" placeholder="Nombre" value="{{$conceptoRetencion->concepto_nombre}}" required>
                </div>
            </div>
            <div class="form-group row">
                <label for="forma_pago_nombre" class="col-sm-2 col-form-label">Codigo</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="forma_pago_nombre" name="idCodigo" placeholder="Codigo" value="{{$conceptoRetencion->concepto_codigo}}" required>
                </div>
            </div>
            <div class="form-group row">
                <label for="idPorcentaje" class="col-sm-2 col-form-label">Porcenjate</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="idPorcentaje" name="idPorcentaje" placeholder="%" value="{{$conceptoRetencion->concepto_porcentaje}}" required>
                </div>
            </div> 
            <div class="form-group row">
                <label for="idTipo" class="col-sm-2 col-form-label">Tipo</label>
                <div class="col-sm-10">
                    <select class="custom-select" id="idTipo" name="idTipo" disabled>
                        <option value="1" @if($conceptoRetencion->concepto_tipo == '1') selected @endif>Retencion en la fuente</option>
                        <option value="2" @if($conceptoRetencion->concepto_tipo == '2') selected @endif>IVA</option>                           
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <label for="idObjeto" class="col-sm-2 col-form-label">Objeto</label>
                <div class="col-sm-10">
                    <select class="custom-select" id="idObjeto" name="idObjeto" require>
                        <option value="FUENTE" @if($conceptoRetencion->concepto_objeto == 'FUENTE') selected @endif>FUENTE</option>
                        <option value="BIENES" @if($conceptoRetencion->concepto_objeto == 'BIENES') selected @endif>BIENES</option>
                        <option value="SERVICIOS" @if($conceptoRetencion->concepto_objeto == 'SERVICIOS') selected @endif>SERVICIOS</option>                                    
                    </select>
                </div>
            </div>
            <div class="form-group row">
                        <label for="idEmitida" class="col-sm-2 col-form-label">Cuenta Emitida</label>
                        <div class="col-sm-10">
                            <select class="form-control select2" id="idEmitida" name="idEmitida" require>
                                @foreach($cuentas as $cuenta)
                                    @if($cuenta->cuenta_id == $conceptoRetencion->concepto_emitida_cuenta)
                                        <option value="{{$cuenta->cuenta_id}}" selected>{{$cuenta->cuenta_numero.' - '.$cuenta->cuenta_nombre}}</option>
                                    @else 
                                        <option value="{{$cuenta->cuenta_id}}">{{$cuenta->cuenta_numero.' - '.$cuenta->cuenta_nombre}}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>                    
                </div>
                <div class="form-group row">
                        <label for="idRecibida" class="col-sm-2 col-form-label">Cuenta Recibida</label>
                        <div class="col-sm-10">
                            <select class="form-control select2" id="idRecibida" name="idRecibida" require>
                                @foreach($cuentas as $cuenta)
                                    @if($cuenta->cuenta_id == $conceptoRetencion->concepto_recibida_cuenta)
                                        <option value="{{$cuenta->cuenta_id}}" selected>{{$cuenta->cuenta_numero.' - '.$cuenta->cuenta_nombre}}</option>
                                    @else 
                                        <option value="{{$cuenta->cuenta_id}}">{{$cuenta->cuenta_numero.' - '.$cuenta->cuenta_nombre}}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>                    
                </div>                
            <div class="form-group row">
                <label for="idEstado" class="col-sm-2 col-form-label">Estado</label>
                <div class="col-sm-10">
                    <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                        @if($conceptoRetencion->concepto_estado=="1")
                            <input type="checkbox" class="custom-control-input" id="idEstado" name="idEstado" checked>
                        @else
                            <input type="checkbox" class="custom-control-input" id="idEstado" name="idEstado">
                        @endif
                        <label class="custom-control-label" for="idEstado"></label>
                    </div>
                </div>                
            </div> 
        </div>            
    </div>
</form>
@endsection