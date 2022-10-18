@extends ('admin.layouts.formatoPDF')
@section('contenido')
    @section('titulo')
        <tr><td colspan="2" class="centrar letra15 negrita">SALDO DE CLIENTES</td></tr>
        <tr><td colspan="2" class="centrar letra15 borde-gris">@if($todo == 1) FECHA DE CORTE : {{ $actual }} @else DEL {{ $desde }}  AL {{ $hasta }} @endif</td></tr>
    @endsection
    <table>
        <tr class="letra12">
            <td class="negrita" style="width: 110px;">TOTAL SALDO : </td>
            <td>{{ $sal }}</td>
        </tr>
    </table>
    <br>
    <table style="white-space: normal!important;" id="tabladetalle">
        <thead>
            <tr style="border: 1px solid black;" class="centrar letra12">
                <th>Ruc</th>
                <th>Nombre</th>
                <th>Saldo Anterior</th>
                <th>Debe</th>
                <th>Haber</th>
                <th>Saldo Actual</th>
            </tr>
        </thead>
        <tbody>
            @if(isset($datos))
                @for ($i = 1; $i <= count($datos); ++$i)   
                    <tr class="letra10">
                        <td>{{ $datos[$i]['ruc'] }}</td>
                        <td>{{ $datos[$i]['nom'] }}</td>
                        <td class="centrar">{{ number_format($datos[$i]['ant'],2) }}</td>
                        <td class="centrar">{{ number_format($datos[$i]['deb'],2) }}</td>
                        <td class="centrar">{{ number_format($datos[$i]['hab'],2) }}</td>
                        <td class="centrar">{{ number_format($datos[$i]['sal'],2) }}</td>                   
                    </tr>
                @endfor
            @endif
        </tbody>
    </table>
@endsection