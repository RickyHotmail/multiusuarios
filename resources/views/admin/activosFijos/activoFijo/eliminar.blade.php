@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">¿Esta seguro de eliminar este grupo?</h3>
        <div class="float-right">
            <form class="form-horizontal" method="POST" action="{{ route('activoFijo.destroy', [$activoFijo->activo_id]) }}">
                @method('DELETE')
                @csrf
                <button type="submit" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i>&nbsp;Eliminar</button>
            <!--     <button type="button" onclick='window.location = "{{ url("activoFijo") }}";' class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button> -->         
              <button  type="button" onclick="history.back()" class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>     
            </form>
        </div>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
                        <div class="form-group row">
                            <label for="idDesde" class="col-sm-3 col-form-label">Fecha</label>
                            <div class="col-sm-9">
                                <input type="date" class="form-control" id="idDesde" name="idDesde"  value="{{$activoFijo->activo_fecha_inicio}}" readonly required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="idSucursalGrupo" class="col-sm-3 col-form-label">Sucursal</label>
                            <div class="col-sm-9">
                                <select class="custom-select select2" id="idSucursalGrupo" name="idSucursalGrupo" onchange="cargarGrupo();" disabled required>
                                    <option value="" label>--Seleccione una Sucursal--</option>
                                    @foreach($sucursales as $sucursal)
                                        @if($sucursal->sucursal_id == $activoFijo->sucursal_id)
                                            <option value="{{$sucursal->sucursal_id}}" selected>{{$sucursal->sucursal_nombre}}</option>
                                        @else 
                                            <option value="{{$sucursal->sucursal_id}}">{{$sucursal->sucursal_nombre}}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="idProducto" class="col-sm-3 col-form-label">Producto</label>
                            <div class="col-sm-9">
                                <select class="custom-select select2" id="idProducto" name="idProducto" disabled required>
                                    <option value="" label>--Seleccione un prodcuto--</option>                                  
                                    @foreach($productos as $producto)
                                        @if($producto->producto_id == $activoFijo->producto_id)
                                            <option value="{{$producto->producto_id}}" selected>{{$producto->producto_nombre}}</option>
                                        @else 
                                                <option value="{{$producto->producto_id}}">{{$producto->producto_nombre}}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="idGrupo" class="col-sm-3 col-form-label">Tipo de Activo</label>
                            <div class="col-sm-9">
                            <select class="custom-select select2" id="idGrupo" name="idGrupo" onchange="cargarCuentaDepreciacion();" disabled required>
                                <option value="{{$activoFijo->grupo_id}}" selected>{{$activoFijo->grupo_nombre}}</option>    
                                <option value="" label>--Seleccione una opcion--</option>
                            </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="idDepreciacion" class="col-sm-3 col-form-label">Cuenta Depreciacion</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="idDepreciacion" name="idDepreciacion" value="{{$activoFijo->grupoActivo->cuentaDepreciacion->cuenta_nombre}}"  readonly required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="idGasto" class="col-sm-3 col-form-label">Cuenta Gasto</label>
                            <div class="col-sm-9">
                                <label class="form-control" id="idGasto" name="idGasto" value="{{$activoFijo->grupoActivo->cuentaGasto->cuenta_nombre}}">{{$activoFijo->grupoActivo->cuentaGasto->cuenta_nombre}}</label>
                            </div>
                        </div>
                        <div class="form-group row">
                                <label for="porcentaje_depreciacion" class="col-sm-3 col-form-label">% Depreciacion</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="porcentaje_depreciacion" name="porcentaje_depreciacion" value="{{$activoFijo->grupoActivo->grupo_porcentaje}}" readonly required>
                                </div>
                        </div> 
                        <div class="form-group row">
                            <label for="idProveedor" class="col-sm-3 col-form-label">Proveedor</label>
                            <div class="col-sm-9">
                                <select class="custom-select select2" id="idProveedor" name="idProveedor" onchange="cargarFacturas();" disabled @if ($activoFijo->proveedor_id != null) required @endif>
                                    <option value="" label>--Seleccione un Proveedor--</option>                                   
                                    @foreach($proveedores as $proveedor)
                                        @if($proveedor->proveedor_id == $activoFijo->proveedor_id)
                                            <option value="{{$proveedor->proveedor_id}}" selected>{{$proveedor->proveedor_nombre}}</option>
                                        @else 
                                            <option value="{{$proveedor->proveedor_id}}">{{$proveedor->proveedor_nombre}}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="rdDocumento" class="col-sm-3 col-form-label">Seleccione una opcion</label>
                            <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2 col-form-label" style="margin-bottom : 0px;">
                                <div class="demo-checkbox">
                                @if(is_null($activoFijo->transaccion_id))
                                    <input type="radio" value="FACTURA" onclick="activarFactura();" id="rdDocumento"
                                        class="with-gap radio-col-deep-orange" name="rdDocumento"  disabled required />
                                @else
                                    <input type="radio" value="FACTURA" onclick="activarFactura();" id="rdDocumento"
                                        class="with-gap radio-col-deep-orange" name="rdDocumento" disabled checked required />
                                @endif
                                    <label class="form-check-label" for="check1">FACTURA</label>
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2 col-form-label" style="margin-bottom : 0px;">
                                <div class="demo-checkbox">
                                @if(is_null($activoFijo->transaccion_id))
                                    <input type="radio" value="DIARIO" onclick="desactivarFactura();" id="rdDocumento"
                                        class="with-gap radio-col-deep-orange" name="rdDocumento" disabled checked required />
                                @else
                                    <input type="radio" value="DIARIO" onclick="desactivarFactura();" id="rdDocumento"
                                        class="with-gap radio-col-deep-orange" name="rdDocumento"  disabled required />
                                @endif
                                    <label class="form-check-label" for="check1">SOLO DIARIO</label>
                                </div>
                            </div>

                            <label for="idDiario" class="col-sm-1 col-form-label">#Diario</label>
                            <div class="col-sm-4">
                                <select class="custom-select select2" id="idDiario" name="idDiario" disabled onchange="cargarFechaDiario();">
                                    <option value="" label>--Seleccione un Diario--</option>
                                    @foreach($diarios as $diario)
                                        @if($diario->diario_id == $activoFijo->diario_id)
                                            <option value="{{$diario->diario_id}}" selected>{{$diario->diario_codigo}}</option>
                                        @else 
                                            <option value="{{$diario->diario_id}}">{{$diario->diario_codigo}}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="idFactura" class="col-sm-3 col-form-label">Factura</label>
                            <div class="col-sm-4">
                                <select class="custom-select select2" id="idFactura" name="idFactura" onchange="cargarFechaFactura();" disabled @if ($activoFijo->transaccion_id != null) required @endif>
                                @if(is_null($activoFijo->transaccion_id))
                                    <option value="" label>--Seleccione una factura--</option>
                                @else
                                    <option value="{{$activoFijo->transaccion_id}}">{{$activoFijo->transaccionCompra->transaccion_numero}}</option>
                                @endif
                                </select>
                            </div>
                            <label for="idDescripcion" class="col-sm-1 col-form-label">Fecha</label>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control" id="idFecha" name="idFecha" value="{{$activoFijo->activo_fecha_documento}}" readonly required>
                                </div>
                        </div>
                        <div class="form-group row">
                                <label for="idValor" class="col-sm-3 col-form-label">Valor</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="idValor" name="idValor" value="{{number_format($activoFijo->activo_valor,2)}}" readonly required>
                                </div>
                        </div>
                        <div class="form-group row">
                                <label for="idVidaUtil" class="col-sm-3 col-form-label">% Vida Util</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="idVidaUtil" name="idVidaUtil" value="{{$activoFijo->activo_vida_util}}" onchange="calcularValores();" readonly required>
                                </div>
                        </div>                  
                        <div class="form-group row">
                                <label for="idValorUtil" class="col-sm-3 col-form-label">Valor vida Util</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="idValorUtil" name="idValorUtil" value="{{number_format($activoFijo->activo_valor_util,2)}}" readonly required>
                                </div>
                        </div>                                      
                        <div class="form-group row">
                                <label for="idBaseDepreciar" class="col-sm-3 col-form-label">Base a depreciar</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="idBaseDepreciar" name="idBaseDepreciar" value="{{number_format($activoFijo->activo_base_depreciar,2)}}" readonly required>
                                </div>
                        </div>                  
                        <div class="form-group row">
                                <label for="idDepreciacionMensual" class="col-sm-3 col-form-label">Depreciacion Mensual</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="idDepreciacionMensual" name="idDepreciacionMensual" value="{{number_format($activoFijo->activo_depreciacion_mensual,2)}}" readonly required>
                                </div>
                        </div>
                        <div class="form-group row">
                                <label for="idDepreciacionAnual" class="col-sm-3 col-form-label">Depreciacion Anual</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="idDepreciacionAnual" name="idDepreciacionAnual" value="{{number_format($activoFijo->activo_depreciacion_anual,2)}}" readonly required>
                                </div>
                        </div>
                        <div class="form-group row">
                                <label for="idDepreciacionAcumulada" class="col-sm-3 col-form-label">Depreciacion Acumulada</label>
                                <div class="col-sm-9">                                    
                                    <input type="text" class="form-control" id="idDepreciacionAcumulada" name="idDepreciacionAcumulada" value="{{number_format($activoFijo->activo_depreciacion_acumulada,2)}}" readonly required>
                                </div>
                        </div>
                        <div class="form-group row">
                                <label for="idDescripcion" class="col-sm-3 col-form-label">Descripcion</label>
                                <div class="col-sm-9">                                    
                                    <textarea type="text" class="form-control" id="idDescripcion" name="idDescripcion" readonly required>{{$activoFijo->activo_descripcion}}</textarea>
                                </div>
                        </div>
        </div>
    <!-- /.card-body -->
</div>
<!-- /.card -->
@endsection