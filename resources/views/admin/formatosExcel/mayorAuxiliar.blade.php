<table>
    <tr>
        <td colspan="10" style="text-align: center;">NEOPAGUPA | Sistema Contable</td>
    </tr>
    <tr>
        <td colspan="10" style="text-align: center;">Mayor Auxiliar</td>
    </tr>
</table>
<table>
    <thead>
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
            <?php 
                $anterior=0;
                $actual=0;
                $tfila=0;
                
                $saldo=0;
                $debe=0;
                $haber=0;
            ?>
            
            @for($i=0; $i<count($datos["datos"]); $i++)
                <?php $dato=$datos["datos"][$i] ?>
                {{ $actual=$dato->cuenta_numero }}
                @if($anterior!=$actual) {{$tfila=0}} @endif
                @if($anterior==$actual) {{$tfila=1}} @endif

                @if($tfila==0)
                    @if($anterior>0)
                        <tr>
                            <td style="background:  #BCC0C4;"></td>
                            <td style="background:  #BCC0C4;"></td>
                            <td style="background:  #BCC0C4;"></td>
                            <td class="derecha-texto" style="background:  #BCC0C4;">{{ number_format($debe,2,'.','') }}</td>
                            <td class="derecha-texto" style="background:  #BCC0C4;">{{ number_format($haber,2,'.','') }}</td>
                            <td  style="background:  #BCC0C4;"></td>
                            <td style="background:  #BCC0C4;"></td>
                            <td style="background:  #BCC0C4;"></td>
                            <td style="background:  #BCC0C4;"></td>
                            <td style="background:  #BCC0C4;"></td>
                        </tr>

                        <?php 
                            $debe=0;
                            $haber=0;
                        ?>
                    @endif
                    <tr>
                        <td colspan="5" style="background:  #70B1F7;">
                            {{ $dato->cuenta_numero }}   -   {{ $dato->cuenta_nombre }}
                        </td>

                        <?php $saldo=0 ?>
                        @for($j=0; $j<count($datos["saldos"]); $j++)
                            @if($datos["saldos"][$j]->cuenta_id==$dato->cuenta_id)
                                <?php $saldo=$datos["saldos"][$j]->saldo ?>
                                @break
                            @endif
                        @endfor
                        
                        <td class="derecha-texto" style="background:  #70B1F7;">{{ number_format($saldo,2,'.','') }}</td>
                        <td style="background:  #70B1F7;"></td>
                        <td style="background:  #70B1F7;"></td>
                        <td style="background:  #70B1F7;"></td>
                        <td style="background:  #70B1F7;"></td>
                    </tr>
                @endif
                
                {{$debe=$debe+$dato->detalle_debe}}
                {{$haber=$haber+$dato->detalle_haber}}
            
                {{$saldo+=$dato->detalle_debe}}
                {{$saldo-=$dato->detalle_haber}}
               
                <tr>
                    <td></td>
                    <td style="width:13px" >{{ $dato->diario_fecha }}</td>
                    <td style="width:57px" class="text-center">{{ $dato->detalle_tipo_documento }} # {{ $dato->detalle_numero_documento }}</td>
                    <td style="width:13px" class="derecha-texto">@if($dato->detalle_debe) {{ number_format($dato->detalle_debe,2,'.','') }} @endif</td>
                    <td style="width:13px" class="derecha-texto">@if($dato->detalle_haber) {{ number_format($dato->detalle_haber,2,'.','') }} @endif</td>
                    <td style="width:13px" class="derecha-texto">{{ number_format($saldo,2,'.','') }}</td>
                    <td style="width:55px" class="text-center">{{ $dato->diario_beneficiario }}</td>
                    <td style="width:17px" class="text-center">{{ $dato->diario_codigo }}</td>
                    <td style="width:70px" class="text-center">{{ $dato->diario_comentario }}</td>
                    <td style="width:24px" class="text-center">{{ $dato->sucursal_nombre }}</td>
                </tr>

                {{ $anterior=$actual }}
            @endfor

            <tr>
                <td style="background:  #BCC0C4;"></td>
                <td style="background:  #BCC0C4;"></td>
                <td style="background:  #BCC0C4;"></td>
                <td class="derecha-texto" style="background:  #BCC0C4;">{{ number_format($debe,2,'.','') }}</td>
                <td class="derecha-texto" style="background:  #BCC0C4;">{{ number_format($haber,2,'.','') }}</td>
                <td  style="background:  #BCC0C4;"></td>
                <td style="background:  #BCC0C4;"></td>
                <td style="background:  #BCC0C4;"></td>
                <td style="background:  #BCC0C4;"></td>
                <td style="background:  #BCC0C4;"></td>
            </tr>
        @endif
    </tbody>
</table>