<table>
    <tr>
        <td colspan="10" style="text-align: center;">NEOPAGUPA | Sistema Contable</td>
    </tr>
</table>
<table>
    <thead>
        <tr>
            @if(isset($datos['tipo'][count($datos)]))
                @if($datos['tipo'][count($datos)] <> 1)
                <th style="font-weight: bold;" colspan="2"></th>
                @else
                    <th></th>
                @endif
            @else   
                <th colspan="2"></th>
            @endif
            <th style="font-weight: bold;" colspan="3">ENTRADAS</th>
            <th style="font-weight: bold;" colspan="3">SALIDAS</th>
            @if(isset($datos['tipo'][count($datos)]))
                @if($datos['tipo'][count($datos)] <> 1)
                <th colspan="7"></th>
                @else
                <th colspan="3"></th>
                @endif
            @endif
        </tr>
        <tr>
            <th style="font-weight: bold;">Codigo</th>
            <th style="font-weight: bold;">Producto</th>
            @if(isset($datos['tipo'][count($datos)]))
                @if($datos['tipo'][count($datos)] <> 1)
                <th style="font-weight: bold;">Fecha</th>
                @endif
            @else   
                <th style="font-weight: bold;">Fecha</th>
            @endif
            <th style="font-weight: bold;">Cantidad</th>
            <th style="font-weight: bold;">Precio</th>
            <th style="font-weight: bold;">Total</th>
            <th style="font-weight: bold;">Cantidad</th>
            <th style="font-weight: bold;">Precio</th>
            <th style="font-weight: bold;">Total</th>
            <th style="font-weight: bold;">Saldo</th>
            @if(isset($datos['tipo'][count($datos)]))
                @if($datos['tipo'][count($datos)] <> 1)
                    <th style="font-weight: bold;">Transaccion</th>
                    <th style="font-weight: bold;">Documento</th>
                    <th style="font-weight: bold;">Documento No.</th>
                    <th style="font-weight: bold;">Cliente/Proveedor</th> 
                    <th style="font-weight: bold;">Descripción</th> 
                    <th style="font-weight: bold;">Bodega</th> 
                @else
                    <th style="font-weight: bold;">Costo Inv.</th>
                    <th style="font-weight: bold;">Utilidad</th>
                @endif
            @else
                <th style="font-weight: bold;">Transaccion</th>
                <th style="font-weight: bold;">Documento</th>
                <th style="font-weight: bold;">Documento No.</th>
                <th style="font-weight: bold;">Cliente/Proveedor</th> 
                <th style="font-weight: bold;">Descripción</th> 
                <th style="font-weight: bold;">Bodega</th> 
            @endif
        </tr>
    </thead>
    <tbody>
        @if(isset($datos))
            @for ($i = 1; $i < count($datos); ++$i)    
                @if($datos[$i]['col'] == "0")
                <tr>
                    <td colspan="15"><b>{{ $datos[$i]['nom'] }}</b></td>
                </tr>
                @elseif($datos[$i]['col'] == "1")
                <tr>
                    <td colspan="8">SALDO ANTERIOR</td>
                    <td>{{ $datos[$i]['can3'] }}</td>
                    <td colspan="6"></td>
                </tr>
                @elseif($datos[$i]['col'] == "3")
                <tr>
                    <td>{{ $datos[$i]['cod'] }}</td>
                    <td>{{ $datos[$i]['nom'] }}</td>
                    <td>{{ $datos[$i]['can1'] }}</td>
                    <td>{{ round($datos[$i]['pre1'],2) }}</td>
                    <td>{{ round($datos[$i]['tot1'],2) }}</td>
                    <td>{{ $datos[$i]['can2'] }}</td>
                    <td>{{ round($datos[$i]['pre2'],2) }}</td>
                    <td>{{ round($datos[$i]['tot2'],2) }}</td>
                    <td>{{ $datos[$i]['can3'] }}</td>
                    <td>{{ round($datos[$i]['pre3'],2) }}</td>
                    <td>{{ round($datos[$i]['tot3'],2) }}</td>
                </tr>
                @else
                <tr>
                    <td>{{ $datos[$i]['cod'] }}</td>
                    <td>{{ $datos[$i]['nom'] }}</td>
                    <td>{{ $datos[$i]['fec'] }}</td>
                    <td>@if($datos[$i]['can1'] <> 0) {{ $datos[$i]['can1'] }} @endif</td>
                    <td>@if($datos[$i]['pre1'] <> 0) {{ round($datos[$i]['pre1'],2) }} @endif</td>
                    <td>@if($datos[$i]['tot1'] <> 0) {{ round($datos[$i]['tot1'],2) }} @endif</td>
                    <td>@if($datos[$i]['can2'] <> 0) {{ $datos[$i]['can2'] }} @endif</td>
                    <td>@if($datos[$i]['pre2'] <> 0) {{ round($datos[$i]['pre2'],2) }} @endif</td>
                    <td>@if($datos[$i]['tot2'] <> 0) {{ round($datos[$i]['tot2'],2) }} @endif</td>
                    <td>{{ $datos[$i]['can3']}}</td>
                    <td>{{ $datos[$i]['tra'] }}</td>
                    <td>{{ $datos[$i]['doc'] }}</td>
                    <td>{{ $datos[$i]['num'] }}</td>
                    <td>{{ $datos[$i]['ref'] }}</td>
                    <td>{{ $datos[$i]['des'] }}</td>
                    <td>{{ $datos[$i]['bod'] }}</td>
                </tr>
                @endif
            @endfor
        @endif
    </tbody>
</table>