<table>
    <tr>
        <td colspan="17" style="text-align: center;">NEOPAGUPA | Sistema Contable</td>
    </tr>
</table>
<table>
    <thead>
        <tr>
            <th colspan="3"></th>
            <th colspan="3">ENTRADAS</th>
            <th colspan="3">SALIDAS</th>
            <th colspan="3">SALDOS</th>
            <th colspan="5"></th>
        </tr>
        <tr>
            <th>Documento</th>
            <th>Numero</th>
            <th>Fecha</th>
            <th>Cantidad</th>
            <th>Precio</th>
            <th>Total</th>
            <th>Cantidad</th>
            <th>Precio</th>
            <th>Total</th>
            <th>Cantidad</th>
            <th>Costo</th>
            <th>Total</th>
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
                <tr>
                    <td>{{ $datos[$i]['doc'] }}</td>
                    <td>{{ $datos[$i]['num'] }}</td>
                    <td>{{ $datos[$i]['fec'] }}</td>
                    <td>@if($datos[$i]['can1'] <> 0) {{ $datos[$i]['can1'] }} @endif</td>
                    <td>@if($datos[$i]['pre1'] <> 0) {{ round($datos[$i]['pre1'],4) }} @endif</td>
                    <td>@if($datos[$i]['tot1'] <> 0) {{ round($datos[$i]['tot1'],2) }} @endif</td>
                    <td>@if($datos[$i]['can2'] <> 0) {{ $datos[$i]['can2'] }} @endif</td>
                    <td>@if($datos[$i]['pre2'] <> 0) {{ round($datos[$i]['pre2'],4) }} @endif</td>
                    <td>@if($datos[$i]['tot2'] <> 0) {{ round($datos[$i]['tot2'],2) }} @endif</td>
                    <td>{{ $datos[$i]['can3'] }}</td>
                    <td>{{ round($datos[$i]['pre3'],4) }}</td>
                    <td>{{ round($datos[$i]['tot3'],2) }}</td>
                    <td>{{ $datos[$i]['cos'] }}</td>
                    <td>{{ $datos[$i]['dia'] }}</td>
                    <td>{{ $datos[$i]['tra'] }}</td>
                    <td>{{ $datos[$i]['ref'] }}</td>
                    <td>{{ $datos[$i]['des'] }}</td>
                </tr>
            @endfor
        @endif
    </tbody>
</table>