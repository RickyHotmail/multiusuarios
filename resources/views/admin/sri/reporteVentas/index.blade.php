@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary" style="position: absolute; width: 100%">
    <div class="card-header">
        <h3 class="card-title">Reporte de Ventas</h3>      
    </div>
    <div class="card-body">  
        <form class="form-horizontal" method="POST" action="{{ url("reporteVentas") }}">
        @csrf             
            <div class="form-group row">
                <label for="fecha_desde" class="col-sm-1 col-form-label"><center>Desde:</center></label>
                <div class="col-sm-2">
                    <input type="date" class="form-control" id="fecha_desde" name="fecha_desde"  value='<?php if(isset($fecI)){echo $fecI;}else{ echo(date("Y")."-".date("m")."-".date("d"));} ?>' required>
                </div>
                <label for="fecha_desde" class="col-sm-1 col-form-label"><center>Hasta:</center></label>
                <div class="col-sm-2">
                    <input type="date" class="form-control" id="fecha_hasta" name="fecha_hasta"  value='<?php if(isset($fecF)){echo $fecF;}else{ echo(date("Y")."-".date("m")."-".date("d"));} ?>' required>
                </div>
                <div class="col-sm-1">
                    <center><button onclick="girarGif()" type="submit" class="btn btn-primary"><i class="fa fa-search"></i></button></center>
                </div>
            </div>               
        </form>    
        @if(isset($facturas)) 
        <center><h3 class="neo-fondo-tabla"><b>Facturas de Venta</b><h3></center>
        <table id="example1" class="table table-bordered table-hover table-responsive sin-salto">
            <thead>           
                <tr class="text-center">               
                    <th>Forma Pago</th>
                    <th>Cliente</th>
                    <th>Fecha</th>
                    <th>Serie</th>
                    <th>Secuencial</th>
                    <th>Autorización</th>  
                    <th>Sub-Total</th>
                    <th>Tarifa 0%</th>
                    <th>Tarifa 12%</th>
                    <th>Descuento</th>
                    <th>IVA</th> 
                    <th>Total</th>
                    <th>Seire Retención</th>
                    <th>Sec. Retención</th>
                    <th>Retenido Fuente</th>
                    <th>Retenido Iva</th>    
                </tr>
            </thead>     
            <tbody>                                                                        
                @foreach($facturas as $factura)
                    <tr class="text-center">
                        <td>{{ $factura->factura_tipo_pago}}</td>                        
                        <td>{{ $factura->cliente->cliente_nombre}}</td>
                        <td>{{ $factura->factura_fecha}}</td>
                        <td>{{ $factura->factura_serie}}</td>
                        <td>{{ substr(str_repeat(0, 9).$factura->factura_secuencial, - 9) }}</td>
                        <td>{{ $factura->factura_autorizacion}}</td>
                        <td>{{ number_format($factura->factura_subtotal, 2) }}</td>
                        <td>{{ number_format($factura->factura_tarifa0, 2) }}</td>
                        <td>{{ number_format($factura->factura_tarifa12, 2) }}</td>
                        <td>{{ number_format($factura->factura_descuento, 2) }}</td>
                        <td>{{ number_format($factura->factura_iva, 2) }}</td>
                        <td>{{ number_format($factura->factura_total, 2) }}</td>    
                        <td class="text-rigth">@if($factura->retencion) {{ $factura->retencion->retencion_serie }} @endif</td>
                        <td class="text-rigth">@if($factura->retencion) {{ substr(str_repeat(0, 9).$factura->retencion->retencion_secuencial, - 9) }} @endif</td>
                        <td class="text-rigth">@if($factura->retencion)  <?php $fuenteRet = 0; ?> @foreach($factura->retencion->detalles as $detalle) <?php if($detalle->detalle_tipo == 'FUENTE') {$fuenteRet = $fuenteRet + $detalle->detalle_valor;} ?>  @endforeach {{ number_format($fuenteRet,2) }} @endif</td>
                        <td class="text-rigth">@if($factura->retencion)  <?php $ivaRet = 0; ?> @foreach($factura->retencion->detalles as $detalle) <?php if($detalle->detalle_tipo == 'IVA') {$ivaRet = $ivaRet + $detalle->detalle_valor;} ?>  @endforeach {{ number_format($ivaRet,2) }} @endif</td>                        
                    </tr>
                @endforeach
            </tbody>
        </table>
        @endif
        @if(isset($notasC)) 
        <center><h3 class="neo-fondo-tabla"><b>Notas de Crédito</b><h3></center>
        <table id="example2" class="table table-bordered table-hover table-responsive sin-salto">
            <thead>           
                <tr class="text-center">                 
                    <th>Forma Pago</th>
                    <th>Cliente</th>
                    <th>Fecha</th>
                    <th>Serie</th>
                    <th>Secuencial</th>
                    <th>Autorización</th>  
                    <th>Sub-Total</th>
                    <th>Tarifa 0%</th>
                    <th>Tarifa 12%</th>
                    <th>Descuento</th>
                    <th>IVA</th> 
                    <th>Total</th> 
                </tr>
            </thead>
            <tbody>
                @foreach($notasC as $nc)
                    <tr class="text-center">
                        <td>{{ $nc->factura->factura_tipo_pago}}</td>                        
                        <td>{{ $nc->factura->cliente->cliente_nombre}}</td>
                        <td>{{ $nc->nc_fecha}}</td>
                        <td>{{ $nc->nc_serie}}</td>
                        <td>{{ substr(str_repeat(0, 9).$nc->nc_secuencial, - 9) }}</td>
                        <td>{{ $nc->nc_autorizacion}}</td>
                        <td>{{ number_format($nc->nc_subtotal, 2) }}</td>
                        <td>{{ number_format($nc->nc_tarifa0, 2) }}</td>
                        <td>{{ number_format($nc->nc_tarifa12, 2) }}</td>
                        <td>{{ number_format($nc->nc_descuento, 2) }}</td>
                        <td>{{ number_format($nc->nc_iva, 2) }}</td>
                        <td>{{ number_format($nc->nc_total, 2) }}</td>                                                                         
                    </tr>
                @endforeach
            </tbody>
        </table>
        @endif
        @if(isset($notasD)) 
        <center><h3 class="neo-fondo-tabla"><b>Notas de Débito</b><h3></center>
        <table id="example3" class="table table-bordered table-hover table-responsive sin-salto">
            <thead>           
                <tr class="text-center">               
                    <th>Forma Pago</th>
                    <th>Cliente</th>
                    <th>Fecha</th>
                    <th>Serie</th>
                    <th>Secuencial</th>
                    <th>Autorización</th>  
                    <th>Sub-Total</th>
                    <th>Tarifa 0%</th>
                    <th>Tarifa 12%</th>
                    <th>Descuento</th>
                    <th>IVA</th> 
                    <th>Total</th>
                    <th>Seire Retención</th>
                    <th>Sec. Retención</th>
                    <th>Retenido Fuente</th>
                    <th>Retenido Iva</th>    
                </tr>
            </thead>
            <tbody>
                @foreach($notasD as $nd)
                    <tr class="text-center">
                        <td>{{ $nd->nd_tipo_pago}}</td>                        
                        <td>{{ $nd->factura->cliente->cliente_nombre}}</td>
                        <td>{{ $nd->nd_fecha}}</td>
                        <td>{{ $nd->nd_serie}}</td>
                        <td>{{ substr(str_repeat(0, 9).$nd->nd_secuencial, - 9) }}</td>
                        <td>{{ $nd->nd_autorizacion}}</td>
                        <td>{{ number_format($nd->nd_subtotal, 2) }}</td>
                        <td>{{ number_format($nd->nd_tarifa0, 2) }}</td>
                        <td>{{ number_format($nd->nd_tarifa12, 2) }}</td>
                        <td>{{ number_format($nd->nd_descuento, 2) }}</td>
                        <td>{{ number_format($nd->nd_iva, 2) }}</td>
                        <td>{{ number_format($nd->nd_total, 2) }}</td>     
                        <td class="text-rigth">@if($nd->retencion) {{ $nd->retencion->retencion_serie }} @endif</td>
                        <td class="text-rigth">@if($nd->retencion) {{ substr(str_repeat(0, 9).$nd->retencion->retencion_secuencial, - 9) }} @endif</td>
                        <td class="text-rigth">@if($nd->retencion)  <?php $fuenteRet = 0; ?> @foreach($nd->retencion->detalles as $detalle) <?php if($detalle->detalle_tipo == 'FUENTE') {$fuenteRet = $fuenteRet + $detalle->detalle_valor;} ?>  @endforeach {{ number_format($fuenteRet,2) }} @endif</td>
                        <td class="text-rigth">@if($nd->retencion)  <?php $ivaRet = 0; ?> @foreach($nd->retencion->detalles as $detalle) <?php if($detalle->detalle_tipo == 'IVA') {$ivaRet = $ivaRet + $detalle->detalle_valor;} ?>  @endforeach {{ number_format($ivaRet,2) }} @endif</td>                                               
                    </tr>
                @endforeach
            </tbody>
        </table> 
        @endif       
        <hr>
        <center><h3 class="neo-fondo-tabla"><b>Resumen de Totales</b><h3></center>
        <table id="example4" class="table table-bordered table-hover table-responsive sin-salto">
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
                <?php $sub = 0; $t0 = 0; $t12 = 0; $iva = 0; $t = 0;?>
                @if(isset($resumenTotales))
                    @foreach($resumenTotales as $resumen)
                    <?php $sub = $sub+$resumen->subtotal; $t0 = $t0+$resumen->tarifa0; $t12 = $t12+$resumen->tarifa12; $iva = $iva+$resumen->iva; $t = $t+$resumen->total;?>
                    <tr>
                        <td class="text-center">Factura</td>
                        <td class="text-center">{{ number_format($resumen->subtotal,2) }}</td>
                        <td class="text-center">{{ number_format($resumen->tarifa0,2) }}</td>
                        <td class="text-center">{{ number_format($resumen->tarifa12,2) }}</td> 
                        <td class="text-center">{{ number_format($resumen->iva,2) }}</td>
                        <td class="text-center">{{ number_format($resumen->total,2) }}</td>
                        <td class="text-center">{{ $resumen->cantidad }}</td>
                    </tr>                         
                    @endforeach   
                @endif
                @if(isset($resumenTotalesNC))  
                    @foreach($resumenTotalesNC as $resumen)
                    <?php $sub = $sub-$resumen->subtotal; $t0 = $t0-$resumen->tarifa0; $t12 = $t12-$resumen->tarifa12; $iva = $iva-$resumen->iva; $t = $t-$resumen->total;?>
                    <tr>
                        <td class="text-center">Nota de crédito</td>
                        <td class="text-center">{{ number_format($resumen->subtotal,2) }}</td>
                        <td class="text-center">{{ number_format($resumen->tarifa0,2) }}</td>
                        <td class="text-center">{{ number_format($resumen->tarifa12,2) }}</td> 
                        <td class="text-center">{{ number_format($resumen->iva,2) }}</td>
                        <td class="text-center">{{ number_format($resumen->total,2) }}</td>
                        <td class="text-center">{{ $resumen->cantidad }}</td>
                    </tr>                         
                    @endforeach       
                @endif
                @if(isset($resumenTotalesND))  
                    @foreach($resumenTotalesND as $resumen)
                    <?php $sub = $sub+$resumen->subtotal; $t0 = $t0+$resumen->tarifa0; $t12 = $t12+$resumen->tarifa12; $iva = $iva+$resumen->iva; $t = $t+$resumen->total;?>
                    <tr>
                        <td class="text-center">Nota de débito</td>
                        <td class="text-center">{{ number_format($resumen->subtotal,2) }}</td>
                        <td class="text-center">{{ number_format($resumen->tarifa0,2) }}</td>
                        <td class="text-center">{{ number_format($resumen->tarifa12,2) }}</td> 
                        <td class="text-center">{{ number_format($resumen->iva,2) }}</td>
                        <td class="text-center">{{ number_format($resumen->total,2) }}</td>
                        <td class="text-center">{{ $resumen->cantidad }}</td>
                    </tr>                         
                    @endforeach     
                @endif
                <tr>
                    <td class="text-center">TOTALES</td>
                    <td class="text-center">{{ number_format($sub,2) }}</td>
                    <td class="text-center">{{ number_format($t0,2) }}</td>
                    <td class="text-center">{{ number_format($t12,2) }}</td> 
                    <td class="text-center">{{ number_format($iva,2) }}</td>
                    <td class="text-center">{{ number_format($t,2) }}</td>
                    <td class="text-center"></td>
                </tr>  
            </tbody>
        </table>
        <center><h3 class="neo-fondo-tabla"><b>Resumen de Retenciones en la fuente</b><h3></center>
        <table id="example11" class="table table-bordered table-hover table-responsive sin-salto">
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
        <table id="example22" class="table table-bordered table-hover table-responsive sin-salto">
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