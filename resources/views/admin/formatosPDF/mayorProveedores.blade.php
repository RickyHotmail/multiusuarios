@extends ('admin.layouts.formatoPDF')
@section('contenido')
    @section('titulo')
        <tr><td colspan="2" class="centrar letra20 negrita">MAYOR DE PROVEEDORES</td></tr>
        <tr><td colspan="2" class="centrar letra15">DEL {{ $desde }} AL {{ $hasta }}</td></tr>
    @endsection
    <table style="table-layout: fixed; white-space: normal!important;" id="tabladetalle">
        <thead>
            <tr style="border: 1px solid black;" class="centrar letra10">
                <th>Fecha</th>    
                <th>Dcoumento</th>
                <th>Numero</th>
                <th>Debe</th>
                <th>Haber</th>
                <th>Saldo</th>
                <th>Diario</th>
                <th>Comentario</th>
                <th>Sucursal</th>   
            </tr>
        </thead>
        <tbody>
            @if(isset($datos))
                @for ($i = 1; $i <= count($datos); ++$i)    
                <tr class="letra10">
                    @if($datos[$i]['tot'] == '1') 
                        <td colspan="5" @if($datos[$i]['tot'] == '1') style="background:  #70B1F7; padding-top: 8px; padding-bottom: 8px;" @endif @if($datos[$i]['tot'] == '2') style="background:  #C0C4C5;" @endif>SALDO ANTERIOR</td>
                    @else
                        <td @if($datos[$i]['tot'] == '2') style="background:  #C0C4C5;" @endif><center> {{ $datos[$i]['fec'] }} </center></td>
                        <td @if($datos[$i]['tot'] == '2') style="background:  #C0C4C5;" @endif><center>  {{ $datos[$i]['doc'] }} </center></td>
                        <td @if($datos[$i]['tot'] == '2') style="background:  #C0C4C5;" @endif><center> {{ $datos[$i]['num'] }} </center></td>  
                    @endif
                    @if($datos[$i]['tot'] == '1') 
                        <td @if($datos[$i]['tot'] == '1') style="background:  #70B1F7;" @endif @if($datos[$i]['tot'] == '2') style="background:  #C0C4C5;" @endif>@if($datos[$i]['act'] <> '') <center> {{ number_format($datos[$i]['act'],2) }} </center> @endif</td>
                        <td colspan="3" @if($datos[$i]['tot'] == '1') style="background:  #70B1F7;" @endif></td>
                        @else
                        <td @if($datos[$i]['tot'] == '2') style="background:  #C0C4C5;" @endif>@if($datos[$i]['deb'] <> '') <center> {{ number_format($datos[$i]['deb'],2) }} </center> @endif</td>
                        <td @if($datos[$i]['tot'] == '2') style="background:  #C0C4C5;" @endif>@if($datos[$i]['hab'] <> '') <center> {{ number_format($datos[$i]['hab'],2) }} </center> @endif</td>
                        <td @if($datos[$i]['tot'] == '2') style="background:  #C0C4C5;" @endif>@if($datos[$i]['act'] <> '') <center> {{ number_format($datos[$i]['act'],2) }} </center> @endif</td>
                        <td @if($datos[$i]['tot'] == '2') style="background:  #C0C4C5;" @endif>{{ $datos[$i]['dia'] }}</td>
                        <td @if($datos[$i]['tot'] == '2') style="background:  #C0C4C5;" @endif>{{ $datos[$i]['com'] }}</td>
                        <td @if($datos[$i]['tot'] == '2') style="background:  #C0C4C5;" @endif>{{ $datos[$i]['suc'] }}</td>
                    @endif
                </tr>
                @endfor
            @endif
        </tbody>
    </table>
@endsection