@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary" style="position: absolute; width: 100%">
    <div class="card-header">
        <h3 class="card-title">Reporte de Compras</h3>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <form class="form-horizontal" method="POST" action="{{ url("reporteCompras") }}">
        @csrf
            <div class="form-group row">
                <label for="idDesde" class="col-sm-1 col-form-label"><center>Desde:</center></label>
                <div class="col-sm-2">
                    <input type="date" class="form-control" id="idDesde" name="idDesde"  value='<?php if(isset($fecI)){echo $fecI;}else{ echo(date("Y")."-".date("m")."-".date("d"));} ?>' required>
                </div>
                <label for="idHasta" class="col-sm-1 col-form-label"><center>Hasta:</center></label>
                <div class="col-sm-2">
                    <input type="date" class="form-control" id="idHasta" name="idHasta"  value='<?php if(isset($fecF)){echo $fecF;}else{ echo(date("Y")."-".date("m")."-".date("d"));} ?>' required>
                </div>
                <div class="col-sm-1">
                    <center><button onclick="girarGif()" type="submit" class="btn btn-primary"><i class="fa fa-search"></i></button></center>
                </div>
            </div>            
        </form>
        <hr>
        @if(isset($transaccionCompras)) 
        <center><h3 class="neo-fondo-tabla"><b>Compras</b><h3></center>
        <table id="example1" class="table table-bordered table-hover table-responsive sin-salto">
            <thead>
                <tr class="text-center">    
                    <th>Tipo</th>
                    <th>Forma de Pago</th>
                    <th>Proveedor</th>
                    <th>Fecha</th>
                    <th>Serie</th>
                    <th>Secuencial</th>
                    <th>Autorización</th>  
                    <th>Subtotal</th>
                    <th>Tarifa 0%</th>                  
                    <th>Tarifa 12%</th>
                    <th>Descuento</th>
                    <th>Iva</th>
                    <th>Total</th>
                    <th>Iva Bienes</th>
                    <th>Iva Servicios</th>
                    <th>Seire Retención</th>
                    <th>Sec. Retención</th>
                    <th>Fecha Retención</th>
                    <th>Autorizacion Ret.</th>
                    <th>Total Base Fuente</th>
                    <th>Retenido Fuente</th>
                    <th>Iva Bienes</th>
                    <th>% Ret. Bienes</th>
                    <th>Valor Ret. Bienes</th>
                    <th>Iva Servicios</th>
                    <th>% Ret. Servicios</th>
                    <th>Valor Ret. Servicios</th>   
                    <th>Sustento Cod.</th>
                    <th>Casillero Compras 0%</th>
                    <th>Casillero Compras 12%</th>
                </tr>
            </thead>
            <tbody> 
                @foreach($transaccionCompras as $transaccionCompra)
                <tr>
                    <td class="text-center">{{ $transaccionCompra->tipoComprobante->tipo_comprobante_nombre}}</td>
                    <td class="text-center">{{ $transaccionCompra->transaccion_tipo_pago}}</td>
                    <td class="text-center">{{ $transaccionCompra->proveedor->proveedor_nombre}}</td>
                    <td class="text-center">{{ $transaccionCompra->transaccion_fecha}}</td>
                    <td class="text-center">{{ $transaccionCompra->transaccion_serie}}</td>
                    <td class="text-center">{{ substr(str_repeat(0, 9).$transaccionCompra->transaccion_secuencial, - 9) }}</td>
                    <td class="text-center">{{ $transaccionCompra->transaccion_autorizacion}}</td> 
                    <td class="text-rigth">${{ number_format($transaccionCompra->transaccion_subtotal,2)}}</td>
                    <td class="text-rigth">${{ number_format($transaccionCompra->transaccion_tarifa0,2)}}</td>
                    <td class="text-rigth">${{ number_format($transaccionCompra->transaccion_tarifa12,2)}}</td>
                    <td class="text-rigth">${{ number_format($transaccionCompra->transaccion_descuento,2)}}</td>
                    <td class="text-rigth">${{ number_format($transaccionCompra->transaccion_iva,2)}}</td>
                    <td class="text-rigth">${{ number_format($transaccionCompra->transaccion_total,2)}}</td>
                    <td class="text-rigth">${{ number_format($transaccionCompra->transaccion_ivaB,2)}}</td>
                    <td class="text-rigth">${{ number_format($transaccionCompra->transaccion_ivaS,2)}}</td>
                    <td class="text-rigth">@if($transaccionCompra->retencionCompra) {{ $transaccionCompra->retencionCompra->retencion_serie }} @endif</td>
                    <td class="text-rigth">@if($transaccionCompra->retencionCompra) {{ substr(str_repeat(0, 9).$transaccionCompra->retencionCompra->retencion_secuencial, - 9) }} @endif</td>
                    <td class="text-rigth">@if($transaccionCompra->retencionCompra) {{ $transaccionCompra->retencionCompra->retencion_fecha }} @endif</td>
                    <td class="text-rigth">@if($transaccionCompra->retencionCompra) {{ $transaccionCompra->retencionCompra->retencion_autorizacion }} @endif</td>
                    <td class="text-rigth">@if($transaccionCompra->retencionCompra)  <?php $fuenteRetBase = 0; ?> @foreach($transaccionCompra->retencionCompra->detalles as $detalle) <?php if($detalle->detalle_tipo == 'FUENTE') {$fuenteRetBase = $fuenteRetBase + $detalle->detalle_base;} ?>  @endforeach {{ number_format($fuenteRetBase,2) }} @endif</td>
                    <td class="text-rigth">@if($transaccionCompra->retencionCompra)  <?php $fuenteRet = 0; ?> @foreach($transaccionCompra->retencionCompra->detalles as $detalle) <?php if($detalle->detalle_tipo == 'FUENTE') {$fuenteRet = $fuenteRet + $detalle->detalle_valor;} ?>  @endforeach {{ number_format($fuenteRet,2) }} @endif</td>
                    <td class="text-rigth">@if($transaccionCompra->retencionCompra)  <?php $ivaRetB = 0; ?> @foreach($transaccionCompra->retencionCompra->detalles as $detalle) <?php if($detalle->detalle_tipo == 'IVA' and $detalle->conceptoRetencion->concepto_objeto == 'BIENES') {$ivaRetB = $ivaRetB + $detalle->detalle_base;} ?>  @endforeach {{ number_format($ivaRetB,2) }} @endif</td>
                    <td class="text-rigth">@if($transaccionCompra->retencionCompra)  <?php $v = 0; ?> @foreach($transaccionCompra->retencionCompra->detalles as $detalle) @if($detalle->detalle_tipo == 'IVA' and $detalle->conceptoRetencion->concepto_objeto == 'BIENES') <?php $v = $detalle->detalle_porcentaje; ?> @else @endif  @endforeach {{$v}} @endif</td>
                    <td class="text-rigth">@if($transaccionCompra->retencionCompra)  <?php $v = 0; ?>@foreach($transaccionCompra->retencionCompra->detalles as $detalle) @if($detalle->detalle_tipo == 'IVA' and $detalle->conceptoRetencion->concepto_objeto == 'BIENES') <?php $v = $detalle->detalle_valor; ?>  @endif  @endforeach {{ number_format($v,2) }} @endif</td>
                    <td class="text-rigth">@if($transaccionCompra->retencionCompra)  <?php $ivaRetS = 0; ?> @foreach($transaccionCompra->retencionCompra->detalles as $detalle) <?php if($detalle->detalle_tipo == 'IVA' and $detalle->conceptoRetencion->concepto_objeto == 'SERVICIOS') {$ivaRetS = $ivaRetS + $detalle->detalle_base;} ?>  @endforeach {{ number_format($ivaRetS,2) }} @endif</td>
                    <td class="text-rigth">@if($transaccionCompra->retencionCompra)  <?php $v = 0; ?> @foreach($transaccionCompra->retencionCompra->detalles as $detalle) @if($detalle->detalle_tipo == 'IVA' and $detalle->conceptoRetencion->concepto_objeto == 'SERVICIOS') <?php $v = $detalle->detalle_porcentaje; ?> @else @endif  @endforeach {{$v}} @endif</td>
                    <td class="text-rigth">@if($transaccionCompra->retencionCompra)  <?php $v = 0; ?>@foreach($transaccionCompra->retencionCompra->detalles as $detalle) @if($detalle->detalle_tipo == 'IVA' and $detalle->conceptoRetencion->concepto_objeto == 'SERVICIOS') <?php $v = $detalle->detalle_valor; ?>  @endif  @endforeach {{ number_format($v,2) }} @endif</td>
                    <td class="text-center">{{ $transaccionCompra->sustentoTributario->sustento_codigo}}</td>
                    <td class="text-center">{{ $transaccionCompra->sustentoTributario->sustento_compra0}}</td>
                    <td class="text-center">{{ $transaccionCompra->sustentoTributario->sustento_compra12}}</td>
                </tr>                         
                @endforeach
            </tbody>
        </table>
        @endif
        @if(isset($notasVenta)) 
        <center><h3 class="neo-fondo-tabla"><b>NOTAS DE VENTA</b><h3></center>
        <table id="example2" class="table table-bordered table-hover table-responsive sin-salto">
            <thead>
                <tr class="text-center">    
                    <th>Tipo</th>
                    <th>Forma de Pago</th>
                    <th>Proveedor</th>
                    <th>Fecha</th>
                    <th>Serie</th>
                    <th>Secuencial</th>
                    <th>Autorización</th>  
                    <th>Subtotal</th>
                    <th>Tarifa 0%</th>                  
                    <th>Tarifa 12%</th>
                    <th>Descuento</th>
                    <th>Iva</th>
                    <th>Total</th>
                    <th>Iva Bienes</th>
                    <th>Iva Servicios</th>
                    <th>Seire Retención</th>
                    <th>Sec. Retención</th>
                    <th>Fecha Retención</th>
                    <th>Autorizacion Ret.</th>
                    <th>Total Base Fuente</th>
                    <th>Retenido Fuente</th>
                    <th>Sustento Cod.</th>
                    <th>Casillero Compras 0%</th>
                    <th>Casillero Compras 12%</th>
                </tr>
            </thead>
            <tbody> 
                @foreach($notasVenta as $transaccionCompra)
                <tr>
                    <td class="text-center">{{ $transaccionCompra->tipoComprobante->tipo_comprobante_nombre}}</td>
                    <td class="text-center">{{ $transaccionCompra->transaccion_tipo_pago}}</td>
                    <td class="text-center">{{ $transaccionCompra->proveedor->proveedor_nombre}}</td>
                    <td class="text-center">{{ $transaccionCompra->transaccion_fecha}}</td>
                    <td class="text-center">{{ $transaccionCompra->transaccion_serie}}</td>
                    <td class="text-center">{{ substr(str_repeat(0, 9).$transaccionCompra->transaccion_secuencial, - 9) }}</td>
                    <td class="text-center">{{ $transaccionCompra->transaccion_autorizacion}}</td> 
                    <td class="text-rigth">${{ number_format($transaccionCompra->transaccion_subtotal,2)}}</td>
                    <td class="text-rigth">${{ number_format($transaccionCompra->transaccion_tarifa0,2)}}</td>
                    <td class="text-rigth">${{ number_format($transaccionCompra->transaccion_tarifa12,2)}}</td>
                    <td class="text-rigth">${{ number_format($transaccionCompra->transaccion_descuento,2)}}</td>
                    <td class="text-rigth">${{ number_format($transaccionCompra->transaccion_iva,2)}}</td>
                    <td class="text-rigth">${{ number_format($transaccionCompra->transaccion_total,2)}}</td>
                    <td class="text-rigth">${{ number_format($transaccionCompra->transaccion_ivaB,2)}}</td>
                    <td class="text-rigth">${{ number_format($transaccionCompra->transaccion_ivaS,2)}}</td>
                    <td class="text-rigth">@if($transaccionCompra->retencionCompra) {{ $transaccionCompra->retencionCompra->retencion_serie }} @endif</td>
                    <td class="text-rigth">@if($transaccionCompra->retencionCompra) {{ substr(str_repeat(0, 9).$transaccionCompra->retencionCompra->retencion_secuencial, - 9) }} @endif</td>
                    <td class="text-rigth">@if($transaccionCompra->retencionCompra) {{ $transaccionCompra->retencionCompra->retencion_fecha }} @endif</td>
                    <td class="text-rigth">@if($transaccionCompra->retencionCompra) {{ $transaccionCompra->retencionCompra->retencion_autorizacion }} @endif</td>
                    <td class="text-rigth">@if($transaccionCompra->retencionCompra)  <?php $fuenteRetBase = 0; ?> @foreach($transaccionCompra->retencionCompra->detalles as $detalle) <?php if($detalle->detalle_tipo == 'FUENTE') {$fuenteRetBase = $fuenteRetBase + $detalle->detalle_base;} ?>  @endforeach {{ number_format($fuenteRetBase,2) }} @endif</td>
                    <td class="text-rigth">@if($transaccionCompra->retencionCompra)  <?php $fuenteRet = 0; ?> @foreach($transaccionCompra->retencionCompra->detalles as $detalle) <?php if($detalle->detalle_tipo == 'FUENTE') {$fuenteRet = $fuenteRet + $detalle->detalle_valor;} ?>  @endforeach {{ number_format($fuenteRet,2) }} @endif</td>
                    <td class="text-center">{{ $transaccionCompra->sustentoTributario->sustento_codigo}}</td>
                    <td class="text-center">{{ $transaccionCompra->sustentoTributario->sustento_compra0}}</td>
                    <td class="text-center">{{ $transaccionCompra->sustentoTributario->sustento_compra12}}</td>
                </tr>                         
                @endforeach
            </tbody>
        </table>
        @endif
        @if(isset($notasCredito)) 
        <center><h3 class="neo-fondo-tabla"><b>NOTAS DE CRÉDITO</b><h3></center>
        <table id="example3" class="table table-bordered table-hover table-responsive sin-salto">
            <thead>
                <tr class="text-center">    
                    <th>Forma de Pago</th>
                    <th>Proveedor</th>
                    <th>Fecha</th>
                    <th>Serie</th>
                    <th>Secuencial</th>
                    <th>Autorización</th>  
                    <th>Subtotal</th>
                    <th>Tarifa 0%</th>                  
                    <th>Tarifa 12%</th>
                    <th>Descuento</th>
                    <th>Iva</th>
                    <th>Total</th>
                    <th>Iva Bienes</th>
                    <th>Iva Servicios</th>  
                </tr>
            </thead>
            <tbody> 
                @foreach($notasCredito as $transaccionCompra)
                <tr>
                    <td class="text-center">{{ $transaccionCompra->transaccion_tipo_pago}}</td>
                    <td class="text-center">{{ $transaccionCompra->proveedor->proveedor_nombre}}</td>
                    <td class="text-center">{{ $transaccionCompra->transaccion_fecha}}</td>
                    <td class="text-center">{{ $transaccionCompra->transaccion_serie}}</td>
                    <td class="text-center">{{ substr(str_repeat(0, 9).$transaccionCompra->transaccion_secuencial, - 9) }}</td>
                    <td class="text-center">{{ $transaccionCompra->transaccion_autorizacion}}</td> 
                    <td class="text-rigth">${{ number_format($transaccionCompra->transaccion_subtotal,2)}}</td>
                    <td class="text-rigth">${{ number_format($transaccionCompra->transaccion_tarifa0,2)}}</td>
                    <td class="text-rigth">${{ number_format($transaccionCompra->transaccion_tarifa12,2)}}</td>
                    <td class="text-rigth">${{ number_format($transaccionCompra->transaccion_descuento,2)}}</td>
                    <td class="text-rigth">${{ number_format($transaccionCompra->transaccion_iva,2)}}</td>
                    <td class="text-rigth">${{ number_format($transaccionCompra->transaccion_total,2)}}</td>
                    <td class="text-rigth">${{ number_format($transaccionCompra->transaccion_ivaB,2)}}</td>
                    <td class="text-rigth">${{ number_format($transaccionCompra->transaccion_ivaS,2)}}</td>
                </tr>                         
                @endforeach
            </tbody>
        </table>
        @endif
        @if(isset($notasDebito)) 
        <center><h3 class="neo-fondo-tabla"><b>NOTA DE DÉBITO</b><h3></center>
        <table id="example4" class="table table-bordered table-hover table-responsive sin-salto">
            <thead>
                <tr class="text-center">    
                    <th>Forma de Pago</th>
                    <th>Proveedor</th>
                    <th>Fecha</th>
                    <th>Serie</th>
                    <th>Secuencial</th>
                    <th>Autorización</th>  
                    <th>Subtotal</th>
                    <th>Tarifa 0%</th>                  
                    <th>Tarifa 12%</th>
                    <th>Descuento</th>
                    <th>Iva</th>
                    <th>Total</th>
                    <th>Iva Bienes</th>
                    <th>Iva Servicios</th>
                    <th>Seire Retención</th>
                    <th>Sec. Retención</th>
                    <th>Fecha Retención</th>
                    <th>Autorizacion Ret.</th>
                    <th>Retenido Fuente</th>
                    <th>Iva Bienes</th>
                    <th>% Ret. Bienes</th>
                    <th>Valor Ret. Bienes</th>
                    <th>Iva Servicios</th>
                    <th>% Ret. Servicios</th>
                    <th>Valor Ret. Servicios</th>   
                </tr>
            </thead>
            <tbody> 
                @foreach($notasDebito as $transaccionCompra)
                <tr>
                    <td class="text-center">{{ $transaccionCompra->transaccion_tipo_pago}}</td>
                    <td class="text-center">{{ $transaccionCompra->proveedor->proveedor_nombre}}</td>
                    <td class="text-center">{{ $transaccionCompra->transaccion_fecha}}</td>
                    <td class="text-center">{{ $transaccionCompra->transaccion_serie}}</td>
                    <td class="text-center">{{ substr(str_repeat(0, 9).$transaccionCompra->transaccion_secuencial, - 9) }}</td>
                    <td class="text-center">{{ $transaccionCompra->transaccion_autorizacion}}</td> 
                    <td class="text-rigth">${{ number_format($transaccionCompra->transaccion_subtotal,2)}}</td>
                    <td class="text-rigth">${{ number_format($transaccionCompra->transaccion_tarifa0,2)}}</td>
                    <td class="text-rigth">${{ number_format($transaccionCompra->transaccion_tarifa12,2)}}</td>
                    <td class="text-rigth">${{ number_format($transaccionCompra->transaccion_descuento,2)}}</td>
                    <td class="text-rigth">${{ number_format($transaccionCompra->transaccion_iva,2)}}</td>
                    <td class="text-rigth">${{ number_format($transaccionCompra->transaccion_total,2)}}</td>
                    <td class="text-rigth">${{ number_format($transaccionCompra->transaccion_ivaB,2)}}</td>
                    <td class="text-rigth">${{ number_format($transaccionCompra->transaccion_ivaS,2)}}</td>
                    <td class="text-rigth">@if($transaccionCompra->retencionCompra) {{ $transaccionCompra->retencionCompra->retencion_serie }} @endif</td>
                    <td class="text-rigth">@if($transaccionCompra->retencionCompra) {{ substr(str_repeat(0, 9).$transaccionCompra->retencionCompra->retencion_secuencial, - 9) }} @endif</td>
                    <td class="text-rigth">@if($transaccionCompra->retencionCompra) {{ $transaccionCompra->retencionCompra->retencion_fecha }} @endif</td>
                    <td class="text-rigth">@if($transaccionCompra->retencionCompra) {{ $transaccionCompra->retencionCompra->retencion_autorizacion }} @endif</td>
                    <td class="text-rigth">@if($transaccionCompra->retencionCompra)  <?php $fuenteRet = 0; ?> @foreach($transaccionCompra->retencionCompra->detalles as $detalle) <?php if($detalle->detalle_tipo == 'FUENTE') {$fuenteRet = $fuenteRet + $detalle->detalle_valor;} ?>  @endforeach {{ number_format($fuenteRet,2) }} @endif</td>
                    <td class="text-rigth">@if($transaccionCompra->retencionCompra)  <?php $ivaRetB = 0; ?> @foreach($transaccionCompra->retencionCompra->detalles as $detalle) <?php if($detalle->detalle_tipo == 'IVA' and $detalle->conceptoRetencion->concepto_objeto == 'BIENES') {$ivaRetB = $ivaRetB + $detalle->detalle_base;} ?>  @endforeach {{ number_format($ivaRetB,2) }} @endif</td>
                    <td class="text-rigth">@if($transaccionCompra->retencionCompra)  <?php $v = 0; ?> @foreach($transaccionCompra->retencionCompra->detalles as $detalle) @if($detalle->detalle_tipo == 'IVA' and $detalle->conceptoRetencion->concepto_objeto == 'BIENES') <?php $v = $detalle->detalle_porcentaje; ?> @else @endif  @endforeach {{$v}} @endif</td>
                    <td class="text-rigth">@if($transaccionCompra->retencionCompra)  <?php $v = 0; ?>@foreach($transaccionCompra->retencionCompra->detalles as $detalle) @if($detalle->detalle_tipo == 'IVA' and $detalle->conceptoRetencion->concepto_objeto == 'BIENES') <?php $v = $detalle->detalle_valor; ?>  @endif  @endforeach {{ number_format($v,2) }} @endif</td>
                    <td class="text-rigth">@if($transaccionCompra->retencionCompra)  <?php $ivaRetS = 0; ?> @foreach($transaccionCompra->retencionCompra->detalles as $detalle) <?php if($detalle->detalle_tipo == 'IVA' and $detalle->conceptoRetencion->concepto_objeto == 'SERVICIOS') {$ivaRetS = $ivaRetS + $detalle->detalle_base;} ?>  @endforeach {{ number_format($ivaRetS,2) }} @endif</td>
                    <td class="text-rigth">@if($transaccionCompra->retencionCompra)  <?php $v = 0; ?> @foreach($transaccionCompra->retencionCompra->detalles as $detalle) @if($detalle->detalle_tipo == 'IVA' and $detalle->conceptoRetencion->concepto_objeto == 'SERVICIOS') <?php $v = $detalle->detalle_porcentaje; ?> @else @endif  @endforeach {{$v}} @endif</td>
                    <td class="text-rigth">@if($transaccionCompra->retencionCompra)  <?php $v = 0; ?>@foreach($transaccionCompra->retencionCompra->detalles as $detalle) @if($detalle->detalle_tipo == 'IVA' and $detalle->conceptoRetencion->concepto_objeto == 'SERVICIOS') <?php $v = $detalle->detalle_valor; ?>  @endif  @endforeach {{ number_format($v,2) }} @endif</td>
                </tr>                         
                @endforeach
            </tbody>
        </table>
        @endif
        @if(isset($liquidaciones)) 
        <center><h3 class="neo-fondo-tabla"><b>Liquidaciones de Compra</b><h3></center>
        <table id="example11" class="table table-bordered table-hover table-responsive sin-salto">
            <thead>
                <tr class="text-center">    
                    <th>Tipo</th>
                    <th>Forma de Pago</th>
                    <th>Proveedor</th>
                    <th>Fecha</th>
                    <th>Serie</th>
                    <th>Secuencial</th>
                    <th>Autorización</th>  
                    <th>Subtotal</th>
                    <th>Tarifa 0%</th>                  
                    <th>Tarifa 12%</th>
                    <th>Descuento</th>
                    <th>Iva</th>
                    <th>Total</th>
                    <th>Iva Bienes</th>
                    <th>Iva Servicios</th>
                    <th>Seire Retención</th>
                    <th>Sec. Retención</th>
                    <th>Autorizacion Ret.</th>
                    <th>Retenido Fuente</th>
                    <th>Iva Bienes</th>
                    <th>% Ret. Bienes</th>
                    <th>Valor Ret. Bienes</th>
                    <th>Iva Servicios</th>
                    <th>% Ret. Servicios</th>
                    <th>Valor Ret. Servicios</th>   
                </tr>
            </thead>
            <tbody> 
                @foreach($liquidaciones as $liquidacion)
                <tr>
                    <td class="text-center">Liquidación de compra</td>
                    <td class="text-center">{{ $liquidacion->lc_tipo_pago}}</td>
                    <td class="text-center">{{ $liquidacion->proveedor->proveedor_nombre}}</td>
                    <td class="text-center">{{ $liquidacion->lc_fecha}}</td>
                    <td class="text-center">{{ $liquidacion->lc_serie}}</td>
                    <td class="text-center">{{ substr(str_repeat(0, 9).$liquidacion->lc_secuencial, - 9) }}</td>
                    <td class="text-center">{{ $liquidacion->lc_autorizacion}}</td> 
                    <td class="text-rigth">${{ number_format($liquidacion->lc_subtotal,2)}}</td>
                    <td class="text-rigth">${{ number_format($liquidacion->lc_tarifa0,2)}}</td>
                    <td class="text-rigth">${{ number_format($liquidacion->lc_tarifa12,2)}}</td>
                    <td class="text-rigth">${{ number_format($liquidacion->lc_descuento,2)}}</td>
                    <td class="text-rigth">${{ number_format($liquidacion->lc_iva,2)}}</td>
                    <td class="text-rigth">${{ number_format($liquidacion->lc_total,2)}}</td>
                    <td class="text-rigth">${{ number_format($liquidacion->lc_ivaB,2)}}</td>
                    <td class="text-rigth">${{ number_format($liquidacion->lc_ivaS,2)}}</td>
                    <td class="text-rigth">@if($liquidacion->retencionCompra) {{ $liquidacion->retencionCompra->retencion_serie }} @endif</td>
                    <td class="text-rigth">@if($liquidacion->retencionCompra) {{ substr(str_repeat(0, 9).$liquidacion->retencionCompra->retencion_secuencial, - 9) }} @endif</td>
                    <td class="text-rigth">@if($liquidacion->retencionCompra) {{ $liquidacion->retencionCompra->retencion_autorizacion }} @endif</td>
                    <td class="text-rigth">@if($liquidacion->retencionCompra)  <?php $fuenteRet = 0; ?> @foreach($liquidacion->retencionCompra->detalles as $detalle) <?php if($detalle->detalle_tipo == 'FUENTE') {$fuenteRet = $fuenteRet + $detalle->detalle_valor;} ?>  @endforeach {{ number_format($fuenteRet,2) }} @endif</td>
                    <td class="text-rigth">@if($liquidacion->retencionCompra)  <?php $ivaRetB = 0; ?> @foreach($liquidacion->retencionCompra->detalles as $detalle) <?php if($detalle->detalle_tipo == 'IVA' and $detalle->conceptoRetencion->concepto_objeto == 'BIENES') {$ivaRetB = $ivaRetB + $detalle->detalle_base;} ?>  @endforeach {{ number_format($ivaRetB,2) }} @endif</td>
                    <td class="text-rigth">@if($liquidacion->retencionCompra)  <?php $v = 0; ?> @foreach($liquidacion->retencionCompra->detalles as $detalle) @if($detalle->detalle_tipo == 'IVA' and $detalle->conceptoRetencion->concepto_objeto == 'BIENES') <?php $v = $detalle->detalle_porcentaje; ?> @else @endif  @endforeach {{$v}} @endif</td>
                    <td class="text-rigth">@if($liquidacion->retencionCompra)  <?php $v = 0; ?>@foreach($liquidacion->retencionCompra->detalles as $detalle) @if($detalle->detalle_tipo == 'IVA' and $detalle->conceptoRetencion->concepto_objeto == 'BIENES') <?php $v = $detalle->detalle_valor; ?>  @endif  @endforeach {{ number_format($v,2) }} @endif</td>
                    <td class="text-rigth">@if($liquidacion->retencionCompra)  <?php $ivaRetS = 0; ?> @foreach($liquidacion->retencionCompra->detalles as $detalle) <?php if($detalle->detalle_tipo == 'IVA' and $detalle->conceptoRetencion->concepto_objeto == 'SERVICIOS') {$ivaRetS = $ivaRetS + $detalle->detalle_base;} ?>  @endforeach {{ number_format($ivaRetS,2) }} @endif</td>
                    <td class="text-rigth">@if($liquidacion->retencionCompra)  <?php $v = 0; ?> @foreach($liquidacion->retencionCompra->detalles as $detalle) @if($detalle->detalle_tipo == 'IVA' and $detalle->conceptoRetencion->concepto_objeto == 'SERVICIOS') <?php $v = $detalle->detalle_porcentaje; ?> @else @endif  @endforeach {{$v}} @endif</td>
                    <td class="text-rigth">@if($liquidacion->retencionCompra)  <?php $v = 0; ?>@foreach($liquidacion->retencionCompra->detalles as $detalle) @if($detalle->detalle_tipo == 'IVA' and $detalle->conceptoRetencion->concepto_objeto == 'SERVICIOS') <?php $v = $detalle->detalle_valor; ?>  @endif  @endforeach {{ number_format($v,2) }} @endif</td>
                </tr>                         
                @endforeach
            </tbody>
        </table>
        @endif
        <hr>
        <center><h3 class="neo-fondo-tabla"><b>Resumen de Totales</b><h3></center>
        <table id="example22" class="table table-bordered table-hover table-responsive sin-salto">
            <thead>
                <tr class="text-center">
                    <th>Tipo</th>
                    <th>Subtotal</th>
                    <th>Tarifa 0</th>
                    <th>Tarifa 12</th>
                    <th>Iva</th>
                    <th>Total</th>
                    <th>Cantidad</th>
                </tr>
            </thead>
            <tbody>
                @if(isset($resumenTotales))
                    @foreach($resumenTotales as $resumen)
                    <tr>
                        <td class="text-center">{{ $resumen->tipo_comprobante_nombre }}</td>
                        <td class="text-center">{{ number_format($resumen->subtotal,2) }}</td>
                        <td class="text-center">{{ number_format($resumen->tarifa0,2) }}</td>
                        <td class="text-center">{{ number_format($resumen->tarifa12,2) }}</td> 
                        <td class="text-center">{{ number_format($resumen->iva,2) }}</td>
                        <td class="text-center">{{ number_format($resumen->total,2) }}</td>
                        <td class="text-center">{{ $resumen->cantidad }}</td>
                    </tr>                         
                    @endforeach   
                @endif
                @if(isset($resumenTotalesLC))  
                    @foreach($resumenTotalesLC as $resumen)
                    <tr>
                        <td class="text-center">Liquidación de compra</td>
                        <td class="text-center">{{ number_format($resumen->subtotal,2) }}</td>
                        <td class="text-center">{{ number_format($resumen->tarifa0,2) }}</td>
                        <td class="text-center">{{ number_format($resumen->tarifa12,2) }}</td> 
                        <td class="text-center">{{ number_format($resumen->iva,2) }}</td>
                        <td class="text-center">{{ number_format($resumen->total,2) }}</td>
                        <td class="text-center">{{ $resumen->cantidad }}</td>
                    </tr>                         
                    @endforeach       
                @endif
            </tbody>
        </table>
        <center><h3 class="neo-fondo-tabla"><b>Resumen de Retenciones en la fuente</b><h3></center>
        <table id="example33" class="table table-bordered table-hover table-responsive sin-salto">
            <thead>
                <tr class="text-center">
                    <th>Código</th>
                    <th>Concepto</th>
                    <th>Base</th>
                    <th>Valor</th>
                    <th>Cantidad</th>  
                </tr>
            </thead>
            <tbody>
                @if(isset($retencionesF))
                    @foreach($retencionesF as $retencion)
                    <tr>
                        <td class="text-center">{{ $retencion->concepto_codigo}}</td>
                        <td class="text-center">{{ $retencion->concepto_nombre }}</td>
                        <td class="text-center">{{ number_format($retencion->base,2)}}</td>
                        <td class="text-center">{{ number_format($retencion->valor,2)}}</td>
                        <td class="text-center">{{ $retencion->cantidad}}</td> 

                    </tr>                         
                    @endforeach
                @endif
            </tbody>
        </table>
        <center><h3 class="neo-fondo-tabla"><b>Resumen de Retenciones de Iva</b><h3></center>
        <table id="example44" class="table table-bordered table-hover table-responsive sin-salto">
            <thead>
                <tr class="text-center">
                    <th>Código</th>
                    <th>Concepto</th>
                    <th>Base</th>
                    <th>Valor</th>
                    <th>Cantidad</th>  
                </tr>
            </thead>
            <tbody>
                @if(isset($retencionesI))
                    @foreach($retencionesI as $retencion)
                    <tr>
                        <td class="text-center">{{ $retencion->concepto_codigo}}</td>
                        <td class="text-center">{{ $retencion->concepto_nombre }}</td>
                        <td class="text-center">{{ number_format($retencion->base,2)}}</td>
                        <td class="text-center">{{ number_format($retencion->valor,2)}}</td>
                        <td class="text-center">{{ $retencion->cantidad}}</td> 

                    </tr>                         
                    @endforeach
                @endif
            </tbody>
        </table>
    </div>
    <!-- /.card-body -->
</div>
<div id="div-gif" class="col-md-12 text-center" style="position: absolute;height: 300px; margin-top: 150px; display: none">
    <img src="{{ url('img/loading.gif') }}" width=90px height=90px style="align-items: center">
</div>
<script>
    function girarGif(){
        document.getElementById("div-gif").style.display="inline"
        console.log("girando")
    }
</script>
@endsection