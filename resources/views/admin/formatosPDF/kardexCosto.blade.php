@extends ('admin.layouts.formatoPDF')
@section('contenido')
    @section('titulo')
        <tr><td colspan="2" class="centrar letra20 negrita">KARDEX COSTO</td></tr>
        @if($sin_fecha == 0)
        <tr><td colspan="2" class="centrar letra15">DEL {{ $desde }} AL {{ $hasta }}</td></tr>
        @else
        <tr><td colspan="2" class="centrar letra15">AL {{ $hasta }}</td></tr>
        @endif
    @endsection
    <table style="white-space: normal!important;" id="tabladetalle">
        <thead>
            <tr class="centrar letra10">
                <th colspan="3"></th>
                <th class="fondo-naranja letra-blanca" colspan="3">ENTRADAS</th>
                <th class="fondo-azul-claro letra-blanca" colspan="3">SALIDAS</th>
                <th class="fondo-verde letra-blanca" colspan="3">SALDOS</th>
                <th colspan="4"></th>
            </tr>
            <tr class="centrar letra10">
                <th>Documento</th>
                <th>Numero</th>
                <th style="width: 70px;">Fecha</th>
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
            </tr>
        </thead>
        <tbody>
            @if(isset($datos))
                @for ($i = 1; $i <= count($datos); ++$i)    
                    <tr class="letra10">
                        <td>{{ $datos[$i]['doc'] }}</td>
                        <td>{{ $datos[$i]['num'] }}</td>
                        <td>{{ $datos[$i]['fec'] }}</td>
                        <td class="centrar">@if($datos[$i]['can1'] <> 0) {{ $datos[$i]['can1'] }} @endif</td>
                        <td class="centrar">@if($datos[$i]['pre1'] <> 0) {{ number_format($datos[$i]['pre1'],4) }} @endif</td>
                        <td class="centrar">@if($datos[$i]['tot1'] <> 0) {{ number_format($datos[$i]['tot1'],2) }} @endif</td>
                        <td class="centrar">@if($datos[$i]['can2'] <> 0) {{ $datos[$i]['can2'] }} @endif</td>
                        <td class="centrar">@if($datos[$i]['pre2'] <> 0) {{ number_format($datos[$i]['pre2'],4) }} @endif</td>
                        <td class="centrar">@if($datos[$i]['tot2'] <> 0) {{ number_format($datos[$i]['tot2'],2) }} @endif</td>
                        <td class="centrar">{{ $datos[$i]['can3'] }}</td>
                        <td class="centrar">{{ number_format($datos[$i]['pre3'],4) }}</td>
                        <td class="centrar">{{ number_format($datos[$i]['tot3'],2) }}</td>
                        <td>{{ $datos[$i]['cos'] }}</td>
                        <td>{{ $datos[$i]['dia'] }}</td>
                        <td>{{ $datos[$i]['tra'] }}</td>
                        <td>{{ $datos[$i]['ref'] }}</td>
                    </tr>
                @endfor
            @endif
        </tbody>
    </table>
@endsection