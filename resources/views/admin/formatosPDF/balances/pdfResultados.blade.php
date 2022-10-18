@extends ('admin.layouts.formatoPDF')
@section('contenido')
    @section('titulo')
        <tr><td colspan="2" class="centrar letra20 negrita">ESTADO DE RESULTADOS INTEGRALES</td></tr>
        <tr><td colspan="2" class="centrar letra15">DEL {{ $desde }} AL {{ $hasta }}</td></tr>
    @endsection
    <hr>
    <table>
        <tbody>
            @if(isset($datos))
                @for ($i = 1; $i <= count($datos); ++$i)    
                <tr class="letra12">
                    <td align="left" class="espacio{{$datos[$i]['nivel']}}">@if($datos[$i]['nivel'] <= 5) <b> {{ $datos[$i]['numero'] }} {{ $datos[$i]['nombre'] }} </b> @else {{ $datos[$i]['numero'] }} {{ strtoupper($datos[$i]['nombre']) }} @endif</td>
                    <td align="right">@if($datos[$i]['nivel'] > 1) $ {{ number_format($datos[$i]['total'],2) }} @endif</td>
                    <td align="right"><b>@if($datos[$i]['nivel'] == 1) $ {{ number_format($datos[$i]['total'],2) }} @endif</b></td>
                </tr>
                @endfor
               
            @endif
        </tbody>
    </table>
    <hr>
    <br>
    <br>
    <table>
        <tr><td class="interlinea" width="30%"><b>TOTAL INGRESOS : </td><td class="interlinea2" width="15%">{{ $totIng }}</td><td ></td></tr>
        <tr><td class="interlinea"><b>TOTAL EGRESOS : </td><td class="interlinea2" width="15%">{{ $totEgr }}</td><td ></td></tr>
        <tr><td class="interlinea"><b>RESULTADO : </td><td class="interlinea2" width="15%">{{ $total }}</td><td ></td></tr>
    </table>

    <br><br><br><br><br>
    <table >
        <tr class="letra12">
            
            <td ></td>
            <td class="" style="border-top: 1px solid black;font-weight: bold;;width: 40%;">GERENTE GENERAL<br>{{$empresa->empresa_representante}}<br>C.I.{{$empresa->empresa_cedula_representante}}</td>
            <td ></td>
            <td class="" style="border-top: 1px solid black;font-weight: bold;width: 40%;">CONTADOR:<br>{{$empresa->empresa_contador}}<br>R.U.C.{{$empresa->empresa_cedula_contador}}</td>
            <td ></td>
        </tr>
    </table>
@endsection