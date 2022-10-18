@extends ('admin.layouts.formatoPDF')
@section('contenido')
    @section('titulo')
        <tr><td colspan="2" class="centrar letra15 negrita">ESTADO DE CUENTA DE CLIENTES @if($tipo == '0') (PAGOS) @endif @if($tipo == '1') (PENDIENTES DE PAGO)  @endif</td></tr>
        <tr><td colspan="2" class="centrar letra15 borde-gris">@if($todo == 1) FECHA DE CORTE : @if($tipo == '0') {{ $actual }} @endif @if($tipo == '1') {{ $fecC }} @endif @else DEL {{ $desde }}  AL {{ $hasta }} @endif</td></tr>
    @endsection
    <table>
        <tr class="letra12">
            <td class="negrita" style="width: 105px;">TOTAL MONTO : </td>
            <td>{{ $mon }}</td>
            <td class="negrita" style="width: 105px;">TOTAL PAGO : </td>
            <td>{{ $pag }}</td>
            <td class="negrita" style="width: 110px;">TOTAL SALDO : </td>
            <td>{{ $sal }}</td>
        </tr>
    </table>
    <br>
    <table style="white-space: normal!important;" id="tabladetalle">
        <thead>
            <tr style="border: 1px solid black;" class="centrar letra12">
                <th>Documento</th>
                <th>Numero</th>
                <th>Fecha</th>
                <th>Monto</th>
                <th>Saldo</th>
                <th>Pago</th>  
                <th>Fecha Pago</th>
                <th>Diario</th>                  
                <th>Descripción</th>
            </tr>
        </thead>
        <tbody>
            @if(isset($datos))
                @for ($i = 1; $i <= count($datos); ++$i)   
                    <tr class="letra10">
                        @if($datos[$i]['tot'] == '1')
                            <td style="background:  #A7CCF3;" colspan="3">{{ $datos[$i]['nom'] }}</td>
                            <td style="background:  #A7CCF3;">{{ number_format($datos[$i]['mon'],2) }}</td>
                            <td style="background:  #A7CCF3;">{{ number_format($datos[$i]['sal'],2) }}</td>
                            <td style="background:  #A7CCF3;">{{ number_format($datos[$i]['pag'],2) }}</td>
                            <td style="background:  #A7CCF3;" colspan="3"></td>  
                        @endif
                        @if($datos[$i]['tot'] == '2')    
                            <td style="background:  #F3DCA7;">{{ $datos[$i]['doc'] }}</td>
                            <td style="background:  #F3DCA7;">{{ $datos[$i]['num'] }}</td>
                            <td style="background:  #F3DCA7;">{{ $datos[$i]['fec'] }}</td>
                            <td style="background:  #F3DCA7;">{{ number_format($datos[$i]['mon'],2) }}</td>
                            <td style="background:  #F3DCA7;">{{ number_format($datos[$i]['sal'],2) }}</td>
                            <td style="background:  #F3DCA7;">{{ number_format($datos[$i]['pag'],2) }}</td>
                            <td style="background:  #F3DCA7;">{{ $datos[$i]['fep'] }}</td>
                            <td style="background:  #F3DCA7;">{{ $datos[$i]['dia'] }}</td>   
                            <td style="background:  #F3DCA7;">{{ $datos[$i]['tip'] }}</td>   
                        @endif 
                        @if($datos[$i]['tot'] == '3')    
                            <td>{{ $datos[$i]['doc'] }}</td>
                            <td>{{ $datos[$i]['num'] }}</td>
                            <td>{{ $datos[$i]['fec'] }}</td>
                            <td>{{ $datos[$i]['mon'] }}</td>
                            <td>{{ $datos[$i]['sal'] }}</td>
                            <td>{{ number_format($datos[$i]['pag'],2) }}</td>
                            <td>{{ $datos[$i]['fep'] }}</td>
                            <td>{{ $datos[$i]['dia'] }}</td>   
                            <td>{{ $datos[$i]['tip'] }}</td>   
                        @endif                      
                    </tr>
                @endfor
            @endif
        </tbody>
    </table>
@endsection