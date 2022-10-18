@extends ('admin.layouts.formatoPDF')
@section('contenido')
    @section('titulo')
        <tr><td colspan="2" class="centrar letra20 negrita">BALANCE DE COMPROBACIÓN</td></tr>
        <tr><td colspan="2" class="centrar letra15">DEL {{ $desde }} AL {{ $hasta }}</td></tr>
    @endsection
    <table id="tabladetalle">
        <thead>
            <tr style="border: 1px solid black;" class="centrar letra10">
                <th>Código</th>
                <th>Nombre</th>
                <th>Saldo Anterior</th>
                <th>Debe</th>
                <th>Haber</th>
                <th>Saldo Deudor</th>
                <th>Saldo Acreedor</th>  
            </tr>
        </thead>
        <tbody>
            @if(isset($datos))
                @for ($i = 1; $i <= count($datos); ++$i)    
                <tr class="letra10">
                    <td align="left">{{ $datos[$i]['numero'] }}</td>
                    <td align="left">{{ strtoupper($datos[$i]['nombre']) }}</td>
                    <td align="right">$ {{ number_format($datos[$i]['saldoAnt'],2) }}</td>
                    <td align="right">$ {{ number_format($datos[$i]['debe'],2) }}</td>
                    <td align="right">$ {{ number_format($datos[$i]['haber'],2) }}</td>
                    <td align="right">$ {{ number_format(abs($datos[$i]['deudor']),2) }}</td>
                    <td align="right">$ {{ number_format(abs($datos[$i]['acreedor']),2) }}</td>
                </tr>
                @endfor
                <tr class="letra10" >
                    <td align="left" style="border-top: 1px solid black;"></td>
                    <td align="left" style="border-top: 1px solid black;"></td>
                    <td align="right" style="border-top: 1px solid black;">$ {{ number_format($totAnt,2) }}</td>
                    <td align="right" style="border-top: 1px solid black;">$ {{ number_format($totDebe,2) }}</td>
                    <td align="right" style="border-top: 1px solid black;">$ {{ number_format($totHaber,2) }}</td>
                    <td align="right" style="border-top: 1px solid black;">$ {{ number_format($totDeu,2) }}</td>
                    <td align="right" style="border-top: 1px solid black;">$ {{ number_format(abs($totAcr),2) }}</td>
                </tr>
            @endif
        </tbody>
    </table>
@endsection