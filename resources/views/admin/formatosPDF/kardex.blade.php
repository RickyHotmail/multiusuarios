@extends ('admin.layouts.formatoPDF')
@section('contenido')
    @section('titulo')
        <tr><td colspan="2" class="centrar letra20 negrita">KARDEX</td></tr>
        <tr><td colspan="2" class="centrar letra15">DEL {{ $desde }} AL {{ $hasta }}</td></tr>
    @endsection
    <table style="white-space: normal!important;" id="tabladetalle">
        <thead>
            <tr style="border: 1px solid black;" class="centrar letra10">
                @if(isset($tipo))
                    @if($tipo <> 1)
                    <th colspan="2"></th>
                    @else
                        <th></th>
                    @endif
                @else   
                    <th colspan="2"></th>
                @endif
                <th colspan="3">ENTRADAS</th>
                <th colspan="3">SALIDAS</th>
                @if(isset($tipo))
                    @if($tipo <> 1)
                    <th colspan="7"></th>
                    @else
                    <th colspan="3"></th>
                    @endif
                @endif
            </tr>
            <tr style="border: 1px solid black;" class="centrar letra10">
                <th>Producto</th>
                @if(isset($tipo))
                    @if($tipo <> 1)
                    <th style="width: 70px !important;">Fecha</th>
                    @endif
                @else   
                    <th style="width: 70px !important;">Fecha</th>
                @endif
                <th>Cantidad</th>
                <th>Precio</th>
                <th>Total</th>
                <th>Cantidad</th>
                <th>Precio</th>
                <th>Total</th>
                <th>Saldo</th>
                @if(isset($tipo))
                    @if($tipo <> 1)
                        <th>Transaccion</th>
                        <th>Documento</th>
                        <th>Documento No.</th>
                        <th>Cliente/Proveedor</th> 
                        <th>Descripción</th> 
                        <th>Bodega</th> 
                    @else
                        <th>Costo Inv.</th>
                        <th>Utilidad</th>
                    @endif
                @else
                    <th>Transaccion</th>
                    <th>Documento</th>
                    <th>Documento No.</th>
                    <th>Cliente/Proveedor</th> 
                    <th>Descripción</th> 
                    <th>Bodega</th> 
                @endif
            </tr>
        </thead>
        <tbody>
            @if(isset($datos))
                @for ($i = 1; $i <= count($datos); ++$i)    
                    @if($datos[$i]['col'] == "0")
                    <tr style="background: #6DC0CD;" class="letra10">
                        <td colspan="15"><b>{{ $datos[$i]['nom'] }}</b></td>
                    </tr>
                    @elseif($datos[$i]['col'] == "1")
                    <tr style="background: #EDC28B;" class="centrar letra10">
                        <td colspan="8">SALDO ANTERIOR</td>
                        <td>{{ $datos[$i]['can3'] }}</td>
                        <td colspan="6"></td>
                    </tr>
                    @elseif($datos[$i]['col'] == "3")
                    <tr class="centrar letra10">
                        <td>{{ $datos[$i]['nom'] }}</td>
                        <td class="centrar">{{ $datos[$i]['can1'] }}</td>
                        <td class="centrar">{{ number_format($datos[$i]['pre1'],2) }}</td>
                        <td class="centrar">{{ number_format($datos[$i]['tot1'],2) }}</td>
                        <td class="centrar">{{ $datos[$i]['can2'] }}</td>
                        <td class="centrar">{{ number_format($datos[$i]['pre2'],2) }}</td>
                        <td class="centrar">{{ number_format($datos[$i]['tot2'],2) }}</td>
                        <td class="centrar">{{ $datos[$i]['can3'] }}</td>
                        <td class="centrar">{{ number_format($datos[$i]['pre3'],2) }}</td>
                        <td class="centrar">{{ number_format($datos[$i]['tot3'],2) }}</td>
                    </tr>
                    @else
                    <tr class="centrar letra10">
                        <td>{{ $datos[$i]['nom'] }}</td>
                        <td>{{ $datos[$i]['fec'] }}</td>
                        <td>@if($datos[$i]['can1'] <> 0) {{ $datos[$i]['can1'] }} @endif</td>
                        <td>@if($datos[$i]['pre1'] <> 0) {{ number_format($datos[$i]['pre1'],2) }} @endif</td>
                        <td>@if($datos[$i]['tot1'] <> 0) {{ number_format($datos[$i]['tot1'],2) }} @endif</td>
                        <td>@if($datos[$i]['can2'] <> 0) {{ $datos[$i]['can2'] }} @endif</td>
                        <td>@if($datos[$i]['pre2'] <> 0) {{ number_format($datos[$i]['pre2'],2) }} @endif</td>
                        <td>@if($datos[$i]['tot2'] <> 0) {{ number_format($datos[$i]['tot2'],2) }} @endif</td>
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
@endsection