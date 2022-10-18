<table>
    <tr>
        <td colspan="10" style="text-align: center;">NEOPAGUPA | Sistema Contable</td>
    </tr>
    <tr>
        <td colspan="10" style="text-align: center;">ESTADO DE CUENTA DE CLIENTES</td>
    </tr>
</table>
<table>
    <thead>
        <tr>
            <th style="font-weight: bold;">Documento</th>
            <th style="font-weight: bold;">Numero</th>
            <th style="font-weight: bold;">Fecha</th>
            <th style="font-weight: bold;">Monto</th>
            <th style="font-weight: bold;">Saldo</th>
            <th style="font-weight: bold;">Pago</th>
            <th style="font-weight: bold;">Fecha Pago</th>
            <th style="font-weight: bold;">Diario</th>
            <th style="font-weight: bold;">Descripción.</th>
        </tr>
    </thead>
    <tbody>
        <?php
            $anterior_cliente=0;
            $anterior_factura=0;
            $actual_cliente=0;
            $actual_factura=0;

            $cliente=0;
            $factura=0;
            
            $saldo=0;
            $debe=0;
            $haber=0;

            $total_monto=0;
            $total_pago=0;
            $total_saldo=0;
        ?>
        @if(isset($datos))
            @foreach($datos['datos'] as $dato)
                <?php 
                    $actual_cliente=$dato->cliente_nombre;
                    $actual_factura=$dato->cuenta_id;

                    if($anterior_cliente!=$actual_cliente) $cliente=1; else $cliente=0;
                    if($anterior_factura!=$actual_factura) $factura=1; else $factura=0;
                ?>
                
                @if($cliente==1)
                    <?php 
                        $montoCliente=0;
                        $saldoCliente=0;
                        $pagoCliente=0;
                        $pagoMigradoCliente=0;
                        $anticipoCliente=0;

                        $i=array_search($dato->cliente_nombre, array_column($datos['saldos'], 'cliente_nombre'));

                        if($i>=0){
                            $montoCliente=$datos['saldos'][$i]->monto;
                            $saldoCliente=$datos['saldos'][$i]->saldo;
                            $pagoMigradoCliente=$datos['saldos'][$i]->pago_migrado;

                            if ($datos['saldos'][$i]->saldom>0) $saldoCliente=$datos['saldos'][$i]->saldo+$datos['saldos'][$i]->saldom-$pagoMigradoCliente;
                        }

                        $i=array_search($dato->cliente_nombre, array_column($datos['pagos'], 'cliente_nombre'));

                        if($i>=0){
                            $pagoCliente=$datos['pagos'][$i]->pagos? $datos['pagos'][$i]->pagos:0;
                            $anticipoCliente=$datos['pagos'][$i]->anticipos? $datos['pagos'][$i]->anticipos:0;
                        }
                    ?>

                    <tr>
                        <td style="background:  #A7CCF3;" colspan="3">{{ $dato->cliente_nombre }}</td>
                        <td style="background:  #A7CCF3;">{{ number_format($montoCliente,2) }}</td>
                        <td style="background:  #A7CCF3;">{{ number_format($saldoCliente,2) }}</td>
                        <td style="background:  #A7CCF3;">{{ number_format($pagoCliente+$anticipoCliente,2) }}</td>
                        <td style="background:  #A7CCF3;" colspan="3"></td>
                    </tr>

                    <?php 
                        $total_monto+=round($montoCliente,2);
                        $total_pago+=round($pagoCliente+$anticipoCliente,2);
                        $total_saldo+=round($saldoCliente,2);
                    ?>
                @endif
                
                @if($factura==1)
                    <?php $migrada=false; ?>
                    <tr>
                        @if($dato->factura_numero)
                            <td style="background:  #F3DCA7;">FACTURA</td>
                            <td style="background:  #F3DCA7;">{{ $dato->factura_numero }}</td>
                        @elseif($dato->nt_numero)
                            <td style="background:  #F3DCA7;">NOTA DE ENTREGA</td>
                            <td style="background:  #F3DCA7;">{{ $dato->nt_numero }}</td>
                        @elseif($dato->nd_numero)
                            <td style="background:  #F3DCA7;">NOTA DE DEBITO</td>
                            <td style="background:  #F3DCA7;">{{ $dato->nd_numero }}</td>
                        @else
                            <td style="background:  #F3DCA7;">FACTURA</td>
                            <td style="background:  #F3DCA7;">{{ substr($dato->cuenta_descripcion, 38) }}</td>
                            <?php $migrada=true; ?>
                        @endif

                        
                        <?php
                            $pagoFac=0;
                            $montoFac=0;
                            $saldoFac=0;
                            
                            $encontre=false;

                            foreach($datos['totalF'] as $f){
                                if($f->cuenta_id==$dato->cuenta_id){
                                    $encontre=true;
                                    $montoFac=$f->cuenta_monto;
                                    $saldoFac=$f->cuenta_saldo;
                                    $pagoFac+= $f->pagos+$f->anticipos;
                                    if($f->cuenta_saldom>0 && $migrada) $saldoFac=$f->cuenta_saldom-$pagoFac;
                                }
                                else{
                                    if($encontre) break;

                                }
                            }
                        ?>


                        
                        <td style="background:  #F3DCA7;">{{ $dato->cuenta_fecha }} {{ number_format($saldoFac,2) }}</td>
                        <td style="background:  #F3DCA7;">{{ number_format($montoFac,2) }}</td>
                        <td style="background:  #F3DCA7;">{{ number_format($saldoFac,2) }}</td>
                        <td style="background:  #F3DCA7;">{{ number_format($pagoFac,2) }}</td>
                        <td style="background:  #F3DCA7;"></td>

                        @if(Auth::user()->empresa->empresa_contabilidad == '1')
                            <td style="background:  #F3DCA7;"><a href="{{ url("asientoDiario/ver/{$dato->diario_factura}") }}" target="_blank">{{ $dato->diario_factura }}</a></td>   
                        @endif

                        <td style="background:  #F3DCA7;"></td>
                    </tr>
                @endif

                @if(!$dato->retencion_numero)
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td>{{ number_format($dato->detalle_pago_valor,2) }}</td>
                        <td>{{ $dato->pago_fecha }}</td>

                        @if(Auth::user()->empresa->empresa_contabilidad == '1')
                        <td>
                            <a href="{{ url("asientoDiario/ver/{$dato->diario_codigo}") }}" target="_blank">{{ $dato->diario_codigo }}</a>
                        </td>
                        @endif

                        <td>{{ $dato->detalle_pago_descripcion }}</td>
                    </tr>
                @endif

                @if($dato->retencion_numero)
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td>{{ number_format($dato->detalle_pago_valor,2) }}</td>
                        <td>{{ $dato->pago_fecha }}</td>

                        @if(Auth::user()->empresa->empresa_contabilidad == '1')
                        <td><a href="{{ url("asientoDiario/ver/{$dato->diario_codigo}") }}" target="_blank">{{ $dato->diario_codigo }}</a></td>
                        @endif
                        <td>{{ $dato->detalle_pago_descripcion }}</td>
                    </tr>
                @endif

                @if($dato->descuento_valor)
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td>{{ number_format($dato->descuento_valor,2) }}</td>
                        <td>{{ $dato->descuento_fecha }}</td>

                        @if(Auth::user()->empresa->empresa_contabilidad == '1')
                            <td><a href="{{ url("asientoDiario/ver/{$dato->diario_codigo}") }}" target="_blank">{{ $dato->diario_codigo }}</a></td>   
                        @endif
                        <td>DESCUENTO DE ANTICIPO DE CLIENTE</td>
                    </tr>
                @endif

                <?php 
                    $anterior_cliente=$actual_cliente;
                    $anterior_factura=$actual_factura;
                ?>
            @endforeach
        @endif
    </tbody>
</table>