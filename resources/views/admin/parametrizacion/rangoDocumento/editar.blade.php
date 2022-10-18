@extends ('admin.layouts.admin')
@section('principal')
<form class="form-horizontal" method="POST" action="{{ route('rangoDocumento.update', [$rangoDocumento->rango_id]) }}">
@method('PUT')
@csrf
    <div class="card card-secondary">
        <div class="card-header">
            <h3 class="card-title">Editar Rango de Documento</h3>
            <div class="float-right">
                <button type="submit" class="btn btn-success btn-sm"><i class="fa fa-save"></i>&nbsp;Guardar</button>
                <button type="button" onclick='window.location = "{{ url("rangoDocumento") }}";' class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>
            </div>
        </div>
        <div class="card-body">
            <div class="form-group row">
                <label for="idTipo" class="col-sm-2 col-form-label">Tipo de Comprobante</label>
                <div class="col-sm-10">
                    <select class="custom-select select2" id="idTipo" name="idTipo" require>
                        @foreach($tipoComprobante as $tipoComprobante)
                            @if($tipoComprobante->tipo_comprobante_id == $rangoDocumento->tipo_comprobante_id)
                                <option value="{{$tipoComprobante->tipo_comprobante_id}}" selected>{{$tipoComprobante->tipo_comprobante_nombre}}</option>
                            @else 
                                <option value="{{$tipoComprobante->tipo_comprobante_id}}">{{$tipoComprobante->tipo_comprobante_nombre}}</option>
                            @endif
                        @endforeach
                    </select>
                </div>                    
            </div>
            <div class="form-group row">
                <label for="idPunto" class="col-sm-2 col-form-label">Punto de Emision</label>
                <div class="col-sm-10">
                    <select class="custom-select select2" id="idPunto" name="idPunto" require>
                        @foreach($puntoEmision as $puntoEmision)
                            @if($puntoEmision->punto_id == $rangoDocumento->punto_id)
                                <option value="{{$puntoEmision->punto_id}}" selected>{{$puntoEmision->sucursal->sucursal_codigo}}{{$puntoEmision->punto_serie}}</option>
                            @else 
                                <option value="{{$puntoEmision->punto_id}}">{{$puntoEmision->sucursal->sucursal_codigo}}{{$puntoEmision->punto_serie}}</option>
                            @endif
                        @endforeach
                    </select>
                </div>                    
            </div> 
            <div class="form-group row">
                <label for="bodega_nombre" class="col-sm-2 col-form-label">Descripcion</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="idDescripcion" name="idDescripcion" placeholder="Descripcion" value="{{$rangoDocumento->rango_descripcion}}" required>
                </div>
            </div>
            <div class="form-group row">
                <label for="idRinicio" class="col-sm-2 col-form-label">Rango de Inicio</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="idRinicio" name="idRinicio" placeholder="Descripcion" value="{{$rangoDocumento->rango_inicio}}" required>
                </div>
            </div>
            <div class="form-group row">
                <label for="idRfin" class="col-sm-2 col-form-label">Rango de fin</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="idRfin" name="idRfin" placeholder="Av. Ejemplo" value="{{$rangoDocumento->rango_fin}}" required>
                </div>
            </div>
            <div class="form-group row">
                <label for="idFinicio" class="col-sm-2 col-form-label">Fecha de Inicio</label>
                <div class="col-sm-10">
                    <input type="date" class="form-control" id="idFinicio" name="idFinicio"  value="{{$rangoDocumento->rango_fecha_inicio}}" required>
                </div>
            </div>
            <div class="form-group row">
                <label for="idFfin" class="col-sm-2 col-form-label">Fecha de Fin</label>
                <div class="col-sm-10">
                    <input type="date" class="form-control" id="idFfin" name="idFfin" value="{{$rangoDocumento->rango_fecha_fin}}" required>
                </div>
            </div>
            <div class="form-group row">
                <label for="idAutorizacion" class="col-sm-2 col-form-label">Autorizacion</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="idAutorizacion" name="idAutorizacion" value="{{$rangoDocumento->rango_autorizacion}}" required>
                </div>
            </div>                                                  
            <div class="form-group row">
                <label for="idEstado" class="col-sm-2 col-form-label">Estado</label>
                <div class="col-sm-10">
                    <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                        @if($rangoDocumento->rango_estado=="1")
                            <input type="checkbox" class="custom-control-input" id="idEstado" name="idEstado" checked>
                        @else
                            <input type="checkbox" class="custom-control-input" id="idEstado" name="idEstado">
                        @endif
                        <label class="custom-control-label" for="bodega_estado"></label>
                    </div>
                </div>                
            </div> 
        </div>            
    </div>
</form>
@endsection