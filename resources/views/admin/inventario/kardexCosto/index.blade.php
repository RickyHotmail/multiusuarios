@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">Kardex Costo</h3>
    </div>
    <div class="card-body">
        <form class="form-horizontal" method="POST" action="{{ url("kardexCosto") }}">
        @csrf
            <div class="form-group row">
                <label for="nombre_cuenta" class="col-sm-1 col-form-label">Producto :</label>
                <div class="col-sm-3">
                    <select class="custom-select select2" id="productoID" name="productoID" require>
                        @foreach($productos as $producto)
                            <option value="{{$producto->producto_id}}" @if(isset($productoC)) @if($productoC == $producto->producto_id) selected @endif @endif>{{$producto->producto_nombre}}</option>
                        @endforeach
                    </select>                    
                </div>
                <div class="col-sm-5">
                    <div class="row">
                        <label for="fecha_desde" class="col-sm-2 col-form-label"><center>Desde :</center></label>
                        <div class="col-sm-4">
                            <input type="date" class="form-control" id="fecha_desde" name="fecha_desde"  value='<?php if(isset($fDesde)){echo $fDesde;}else{ echo(date("Y")."-".date("m")."-".date("d"));} ?>' required>
                        </div>
                        <label for="fecha_hasta" class="col-sm-2 col-form-label"><center>Hasta :</center></label>
                        <div class="col-sm-4">
                            <input type="date" class="form-control" id="fecha_hasta" name="fecha_hasta"  value='<?php if(isset($fHasta)){echo $fHasta;}else{ echo(date("Y")."-".date("m")."-".date("d"));} ?>' required>
                        </div>
                    </div>
                </div>
                <div class="col-sm-1">
                    <div class="icheck-info">
                        <input type="checkbox" id="sin_fecha" name="sin_fecha" @if(isset($sin_fecha)) @if($sin_fecha == 1) checked @endif @endif>
                        <label for="sin_fecha" class="custom-checkbox"><center>Sin Fecha</center></label>
                    </div>                    
                </div>
                <div class="col-sm-2 centrar-texto">
                    <button type="submit" id="buscar" name="buscar" class="btn btn-primary"><i class="fa fa-search"></i></button>
                    <button type="submit" id="excel" name="excel" class="btn btn-success"><i class="fas fa-file-excel   "></i></button>
                    <button type="submit" id="pdf" name="pdf" class="btn btn-secondary"><i class="fas fa-print"></i></button>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-1 col-form-label">Stock Actual : </label>
                <div class="col-sm-1">
                    <label class="form-control derecha-texto">@if(isset($datos)) {{ $datos[count($datos)]['can3'] }}  @else 0.00 @endif</label>                 
                </div>
                <label class="col-sm-1 col-form-label">Precio Costo : </label>
                <div class="col-sm-1">
                    <label class="form-control derecha-texto">@if(isset($datos)) {{ number_format($datos[count($datos)]['pre3'],4) }}  @else 0.00 @endif</label>                 
                </div>
                <div class="col-sm-8">
                    <div class="row">
                        <label class="col-sm-2 col-form-label centrar-texto">Total Entradas :</label>
                        <div class="col-sm-2">
                            <label class="form-control derecha-texto">@if(isset($totalE)) {{ number_format($totalE,2) }} @else 0.00 @endif</label>                 
                        </div>
                        <label class="col-sm-2 col-form-label centrar-texto">Total Salida : </label>
                        <div class="col-sm-2">
                            <label class="form-control derecha-texto">@if(isset($totalS)) {{ number_format($totalS,2) }} @else 0.00 @endif</label>                 
                        </div>
                        <label class="col-sm-2 col-form-label centrar-texto">Inventario Actual : </label>
                        <div class="col-sm-2">
                            <label class="form-control derecha-texto">@if(isset($datos)) {{ number_format($datos[count($datos)]['tot3'],2) }}  @else 0.00 @endif</label>                 
                        </div>
                    </div>
                </div>
            </div>
            <hr>
            <table id="example5" class="table table-bordered table-hover table-responsive sin-salto">
                <thead>
                    <tr class="text-center">
                        <th colspan="3"></th>
                        <th class="fondo-naranja letra-blanca" colspan="3">ENTRADAS</th>
                        <th class="fondo-azul-claro letra-blanca" colspan="3">SALIDAS</th>
                        <th class="fondo-verde letra-blanca" colspan="3">SALDOS</th>
                        <th colspan="5"></th>
                    </tr>
                    <tr class="text-center">
                        <th>Documento</th>
                        <th>Numero</th>
                        <th>Fecha</th>
                        <th class="fondo-naranja-claro">Cantidad</th>
                        <th class="fondo-naranja-claro">Precio</th>
                        <th class="fondo-naranja-claro">Total</th>
                        <th class="fondo-celeste">Cantidad</th>
                        <th class="fondo-celeste">Precio</th>
                        <th class="fondo-celeste">Total</th>
                        <th class="fondo-verde-claro">Cantidad</th>
                        <th class="fondo-verde-claro">Costo</th>
                        <th class="fondo-verde-claro">Total</th>
                        <th>Diario Costo</th> 
                        <th>Diario</th>
                        <th>Transaccion</th>
                        <th>Cliente/Proveedor</th> 
                        <th>Descripción</th> 
                    </tr>
                </thead>
                <tbody>
                    @if(isset($datos))
                        @for ($i = 1; $i <= count($datos); ++$i)    
                            <tr class="centrar-texto">
                                <td>{{ $datos[$i]['doc'] }}<input type="hidden" name="idDoc[]" value="{{ $datos[$i]['doc'] }}"/></td>
                                <td>{{ $datos[$i]['num'] }}<input type="hidden" name="idNum[]" value="{{ $datos[$i]['num'] }}"/></td>
                                <td>{{ $datos[$i]['fec'] }}<input type="hidden" name="idFec[]" value="{{ $datos[$i]['fec'] }}"/></td>
                                <td>@if($datos[$i]['can1'] <> 0) {{ $datos[$i]['can1'] }} @endif<input type="hidden" name="idCan1[]" value="{{ $datos[$i]['can1'] }}"/></td>
                                <td>@if($datos[$i]['pre1'] <> 0) {{ number_format($datos[$i]['pre1'],4) }} @endif<input type="hidden" name="idPre1[]" value="{{ $datos[$i]['pre1'] }}"/></td>
                                <td>@if($datos[$i]['tot1'] <> 0) {{ number_format($datos[$i]['tot1'],2) }} @endif<input type="hidden" name="idTot1[]" value="{{ $datos[$i]['tot1'] }}"/></td>
                                <td>@if($datos[$i]['can2'] <> 0) {{ $datos[$i]['can2'] }} @endif<input type="hidden" name="idCan2[]" value="{{ $datos[$i]['can2'] }}"/></td>
                                <td>@if($datos[$i]['pre2'] <> 0) {{ number_format($datos[$i]['pre2'],4) }} @endif<input type="hidden" name="idPre2[]" value="{{ $datos[$i]['pre2'] }}"/></td>
                                <td>@if($datos[$i]['tot2'] <> 0) {{ number_format($datos[$i]['tot2'],2) }} @endif<input type="hidden" name="idTot2[]" value="{{ $datos[$i]['tot2'] }}"/></td>
                                <td>{{ $datos[$i]['can3'] }}<input type="hidden" name="idCan3[]" value="{{ $datos[$i]['can3'] }}"/></td>
                                <td>{{ number_format($datos[$i]['pre3'],4) }}<input type="hidden" name="idPre3[]" value="{{ $datos[$i]['pre3'] }}"/></td>
                                <td>{{ number_format($datos[$i]['tot3'],2) }}<input type="hidden" name="idTot3[]" value="{{ $datos[$i]['tot3'] }}"/></td>
                                <td><a href="{{ url("asientoDiario/ver/{$datos[$i]['cos']}") }}" target="_blank">{{ $datos[$i]['cos'] }}</a><input type="hidden" name="idCos[]" value="{{ $datos[$i]['cos'] }}"/></td>
                                <td><a href="{{ url("asientoDiario/ver/{$datos[$i]['dia']}") }}" target="_blank">{{ $datos[$i]['dia'] }}</a><input type="hidden" name="idDia[]" value="{{ $datos[$i]['dia'] }}"/></td>
                                <td>{{ $datos[$i]['tra'] }}<input type="hidden" name="idTra[]" value="{{ $datos[$i]['tra'] }}"/></td>
                                <td>{{ $datos[$i]['ref'] }}<input type="hidden" name="idRef[]" value="{{ $datos[$i]['ref'] }}"/></td>
                                <td>{{ $datos[$i]['des'] }}<input type="hidden" name="idDes[]" value="{{ $datos[$i]['des'] }}"/></td>
                            </tr>
                        @endfor
                    @endif
                </tbody>
            </table>
        </form>
    </div>
</div>
@endsection