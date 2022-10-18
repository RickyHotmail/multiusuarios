@extends ('admin.layouts.formatoPDF')
@section('contenido')
    @section('titulo')
        <tr><td colspan="2" class="centrar letra15 negrita">LISTA DE ANTICIPOS DE CLIENTES</td></tr>
        <tr><td colspan="2" class="centrar letra15 borde-gris">FECHA DE CORTE : {{ $fCorte }}</td></tr>
    @endsection
    <table>
        <tr class="letra12">
            <td class="negrita" style="width: 105px;">TOTAL MONTO:</td>
            <td>{{ $monto }}</td>
            <td class="negrita" style="width: 125px;">TOTAL PAGADO:</td>
            <td>{{ $pag }}</td>
            <td class="negrita" style="width: 125px;">TOTAL  SALDO:</td>
            <td>{{ $saldo }}</td>
        </tr>
    </table>
    <br>
    <table style="white-space: normal!important;" id="tabladetalle">
        <thead>
            <tr style="border: 1px solid black;" class="centrar letra12">
                <th>Beneficiario</th>
                <th>Monto</th>
                <th>Saldo</th>
                <th style="width: 70px !important;">Fecha</th>
                <th>Pago</th>  
                <th style="width: 70px !important;">Fecha Pago</th>
                <th>Diario</th>                  
                <th>Tipo</th>
                <th>Factura</th>
            </tr>
        </thead>
        <tbody>
            @if(isset($datos))
                @for ($i = 1; $i <= count($datos); ++$i)   
                    <tr  class="letra10">
                        <td @if($datos[$i]['tot'] == '1') style="background:  #70B1F7;" @endif>{{ $datos[$i]['ben'] }}</td>
                        <td class="centrar" @if($datos[$i]['tot'] == '1') style="background:  #70B1F7;" @endif @if($datos[$i]['tot'] == '2') style="background:  #C0C4C5;" @endif>@if($datos[$i]['mon'] <> '') {{ number_format($datos[$i]['mon'],2) }} @endif</td>
                        <td class="centrar" @if($datos[$i]['tot'] == '1') style="background:  #70B1F7;" @endif @if($datos[$i]['tot'] == '2') style="background:  #C0C4C5;" @endif>@if($datos[$i]['sal'] <> '') {{ number_format($datos[$i]['sal'],2) }} @endif</td>
                        <td class="centrar" @if($datos[$i]['tot'] == '1') style="background:  #70B1F7;" @endif @if($datos[$i]['tot'] == '2') style="background:  #C0C4C5;" @endif>{{ $datos[$i]['fec'] }}</td>
                        <td class="centrar" @if($datos[$i]['tot'] == '1') style="background:  #70B1F7;" @endif @if($datos[$i]['tot'] == '2') style="background:  #C0C4C5;" @endif>@if($datos[$i]['pag'] <> '') {{ number_format($datos[$i]['pag'],2) }} @endif</td>
                        <td class="centrar" @if($datos[$i]['tot'] == '1') style="background:  #70B1F7;" @endif @if($datos[$i]['tot'] == '2') style="background:  #C0C4C5;" @endif>{{ $datos[$i]['fep'] }}</td>
                        <td class="centrar" @if($datos[$i]['tot'] == '1') style="background:  #70B1F7;" @endif @if($datos[$i]['tot'] == '2') style="background:  #C0C4C5;" @endif>{{ $datos[$i]['dir'] }}</td>
                        <td @if($datos[$i]['tot'] == '1') style="background:  #70B1F7;" @endif @if($datos[$i]['tot'] == '2') style="background:  #C0C4C5;" @endif>{{ $datos[$i]['tip'] }}</td>
                        <td class="centrar" @if($datos[$i]['tot'] == '1') style="background:  #70B1F7;" @endif @if($datos[$i]['tot'] == '2') style="background:  #C0C4C5;" @endif>{{ $datos[$i]['fac'] }}</td>
                    </tr>
                @endfor
            @endif
        </tbody>
    </table>
@endsection