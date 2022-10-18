@extends ('admin.layouts.formatoPDF')
@section('contenido')
    @section('titulo')
        <tr><td colspan="2" class="centrar letra15 negrita">LISTA DE CARTERA DE CLIENTES</td></tr>
        <tr><td colspan="2" class="centrar letra15 borde-gris">@if($todo == 1) FECHA DE CORTE : {{ $actual }} @else DEL {{ $desde }}  AL {{ $hasta }} @endif</td></tr>
    @endsection
    <table>
        <tr class="letra12">
            <td class="negrita" style="width: 105px;">TOTAL MONTO : </td>
            <td>{{ $monto }}</td>
            <td class="negrita" style="width: 110px;">TOTAL  SALDO : </td>
            <td>{{ $saldo }}</td>
            <td class="negrita" style="width: 145px;">FACTURAS VENCIDAS : </td>
            <td>{{ $vencidas }}</td>
            <td class="negrita" style="width: 145px;">FACTURAS A VENCER : </td>
            <td>{{ $vencer }}</td>
        </tr>
    </table>
    <br>
    <table style="white-space: normal!important;" id="tabladetalle">
        <thead>
            @if($tipo == 3)
            <tr style="border: 1px solid black;" class="centrar letra12">
                <th style="width: 100px;">Ruc</th>
                <th>Nombre</th>
                <th style="width: 100px;">Documento</th>
                <th style="width: 110px;">Monto</th>
                <th style="width: 110px;">Saldo</th>  
                <th style="width: 70px;">Fecha</th>
                <th style="width: 70px;">Termino</th>                  
                <th style="width: 70px;">Plazo</th>
                <th style="width: 70px;">Transc.</th>
                <th style="width: 70px;">Ret.</th>
                <th style="width: 70px;">N° Cheq.</th>
                <th style="width: 70px;">Banco.</th>

            </tr>
            @else
            <tr style="border: 1px solid black;" class="centrar letra12">
                <th style="width: 90px;">Ruc</th>
                <th>Nombre</th>
                <th style="width: 90px;">Documento</th>
                <th style="width: 90px;">Monto</th>
                <th style="width: 90px;">Pagos</th>
                <th style="width: 90px;">Saldo</th>  
                <th style="width: 70px;">Fecha</th>
                <th style="width: 70px;">Termino</th>                  
                <th style="width: 50px;">Plazo</th>
                <th style="width: 50px;">Transc.</th>
                <th style="width: 40px;">Ret.</th>
                <th style="width: 70px;">N° Cheq.</th>
                <th style="width: 70px;">Banco.</th>

            </tr>
            @endif
        </thead>
        <tbody>
            @if(isset($datos))
                @for ($i = 1; $i <= count($datos); ++$i)   
                    <tr class="letra10">
                        <td @if($datos[$i]['tot'] == '1') style="background:  #70B1F7;" @endif  @if($datos[$i]['tot'] == '2') style="background:  #B1E2DD;" @endif>{{ $datos[$i]['ruc'] }}</td>
                        <td @if($datos[$i]['tot'] == '1') style="background:  #70B1F7;" @endif  @if($datos[$i]['tot'] == '2') style="background:  #B1E2DD;" @endif>{{ $datos[$i]['nom'] }}</td>
                        <td @if($datos[$i]['tot'] == '1') style="background:  #70B1F7;" @endif  @if($datos[$i]['tot'] == '2') style="background:  #B1E2DD;" @endif>{{ $datos[$i]['doc'] }}</td>
                        <td class="centrar" @if($datos[$i]['tot'] == '1') style="background:  #70B1F7;" @endif  @if($datos[$i]['tot'] == '2') style="background:  #B1E2DD;" @endif>@if($datos[$i]['mon'] <> '') {{ number_format($datos[$i]['mon'],2) }} @endif</td>
                        @if($tipo != 3)<td class="centrar" @if($datos[$i]['tot'] == '1') style="background:  #70B1F7;" @endif  @if($datos[$i]['tot'] == '2') style="background:  #B1E2DD;" @endif>@if($datos[$i]['pag'] <> '') {{ number_format($datos[$i]['pag'],2) }} @endif</td>@endif
                        <td class="centrar" @if($datos[$i]['tot'] == '1') style="background:  #70B1F7;" @endif  @if($datos[$i]['tot'] == '2') style="background:  #B1E2DD;" @endif>@if($datos[$i]['sal'] <> '') {{ number_format($datos[$i]['sal'],2) }} @endif</td>
                        <td class="centrar" @if($datos[$i]['tot'] == '1') style="background:  #70B1F7;" @endif  @if($datos[$i]['tot'] == '2') style="background:  #B1E2DD;" @endif>{{ $datos[$i]['fec'] }}</td>
                        @if($datos[$i]['tot'] == '2')
                            <td colspan="4" @if($datos[$i]['tot'] == '1') style="background:  #70B1F7;" @endif  @if($datos[$i]['tot'] == '2') style="background:  #B1E2DD;" @endif>{{ $datos[$i]['ter'] }}</td>
                            
                        @else
                            <td class="centrar" @if($datos[$i]['tot'] == '1') style="background:  #70B1F7;" @endif  @if($datos[$i]['tot'] == '2') style="background:  #B1E2DD;" @endif>{{ $datos[$i]['ter'] }}</td>
                            <td class="centrar" @if($datos[$i]['tot'] == '1') style="background:  #70B1F7;" @endif  @if($datos[$i]['tot'] == '2') style="background:  #B1E2DD;" @endif>{{ $datos[$i]['pla'] }}</td>
                            <td class="centrar" @if($datos[$i]['tot'] == '1') style="background:  #70B1F7;" @endif  @if($datos[$i]['tot'] == '2') style="background:  #B1E2DD;" @endif>{{ $datos[$i]['tra'] }}</td>
                            <td class="centrar" @if($datos[$i]['tot'] == '1') style="background:  #70B1F7;" @endif  @if($datos[$i]['tot'] == '2') style="background:  #B1E2DD;" @endif>{{ $datos[$i]['ret'] }}</td>
                        @endif
                        <td class="centrar" @if($datos[$i]['tot'] == '1') style="background:  #70B1F7;" @endif  @if($datos[$i]['tot'] == '2') style="background:  #B1E2DD;" @endif>{{ $datos[$i]['cheque'] }}</td>
                        <td class="centrar" @if($datos[$i]['tot'] == '1') style="background:  #70B1F7;" @endif  @if($datos[$i]['tot'] == '2') style="background:  #B1E2DD;" @endif>{{ $datos[$i]['banco'] }}</td>
                        
                    </tr>
                @endfor
            @endif
        </tbody>
    </table>
@endsection