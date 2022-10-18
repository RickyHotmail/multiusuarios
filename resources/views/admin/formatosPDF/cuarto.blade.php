@extends ('admin.layouts.formatoPDF')
@section('contenido')
    @section('titulo')
        <tr><td colspan="2" class="centrar letra15 negrita">DECIMO CUARTO</td></tr>
    @endsection
    <table >
        <tr class="letra18">
                <td>{{ Auth::user()->empresa->empresa_ciudad }}, {{ $fecha}}</td>       
        </tr>
    </table>
    <br>
    <table >
        <tr class="letra12"> 
            <td class="negrita" style="width:10px;" >PARA:</td>
            <td style="width:40px;">{{ $cuarto->empleado->empleado_nombre }} </td>
        </tr>
        <tr class="letra12"> 
        <td class="negrita" style="width:10px;" >DE:</td>
            <td style="width:40px;">{{ Auth::user()->empresa->empresa_razonSocial }} </td>
        </tr>
        
    </table>
    <br>
    <table style="white-space: normal!important; border-collapse: collapse;">
    <tr class="letra12"> 
        <td class="negrita" >ASUNTO: </td>
    </tr>
    <tr class="letra12"> 
             <td class=" letra15 " style="width: 105px;">PAGO DECIMO CUARTO REMUNERACION, PERIODO REPORTADO: 01/03/{{ DateTime::createFromFormat('Y-m-d', $cuarto->decimo_fecha)->format('Y')-1 }} AL  28/02/{{ DateTime::createFromFormat('Y-m-d', $cuarto->decimo_fecha)->format('Y') }}, CON 
             @IF($cuarto->decimo_tipo == 'Efectivo') EFECTIVO @ENDIF 
             @IF($cuarto->decimo_tipo == 'Cheque') CHEQUE N°  
                @foreach($cuarto->diario->detalles as $detalle)
                    @if(isset($detalle->cheque)) 
                        {{$detalle->cheque->cheque_numero}}
                    @endif
                @endforeach
            @ENDIF
             @IF($cuarto->decimo_tipo == 'Transferencia') TRANFERENCIA @ENDIF 
              POR EL VALOR DE $  {{ $cuarto->decimo_valor}}
            </td>
    </tr>
    </table>
  
   <br>
   <br>
   <br>
   <br>
   <br>
   <br>
   <br>
   <br>
   <br>
   <br>
   <br>
   <br>
   <br>
   <br>
   <br>
   <br>
   <br>
   <br>
   <br>
   <br>
   <br>
   <br>
   <br>
    <table style="padding-top: 100px;">
        <tr class="letra14">
            <td ></td>
            <td ></td>
            <td  ></td>
            <td  ></td>
            <td style="border-top: 1px solid black; border-left: 1px solid black; border-right: 1px solid black; padding: 6px 6px;" ></td>
        </tr>
        <tr class="letra14">
            <td ></td>
            <td ></td>
            <td  ></td>
            <td  ></td>
            <td style="border-left: 1px solid black; border-right: 1px solid black; padding: 6px 6px;" ></td>
        </tr>
        <tr class="letra14">
            <td ></td>
            <td ></td>
            <td  ></td>
            <td  ></td>
            <td style="border-left: 1px solid black; border-right: 1px solid black; padding: 6px 6px;" ></td>
        </tr>
        <tr class="letra14">
            <td ></td>
            <td ></td>
            <td  ></td>
            <td  ></td>
            <td style="border-left: 1px solid black; border-right: 1px solid black; padding: 6px 6px;" ></td>
        </tr>
        <tr class="letra14">
            <td ></td>
            <td ></td>
            <td ></td>
            <td  ></td>
            <td style="border-left: 1px solid black; border-right: 1px solid black; padding: 6px 6px;"></td>
        </tr>
        <tr class="letra14">
            <td class="centrar negrita" style=" border-top: 1px solid black; width: 30%;">Recibi Conforme: <br> C.I: {{ $cuarto->empleado->empleado_cedula }}</td>
            <td  style="padding-right: 15px; padding-left: 15px;"></td>
            <td  ></td>
            <td  ></td>
            <td style="border-left: 1px solid black; border-right: 1px solid black; padding: 6px 6px;"></td>
        </tr>
        <tr class="letra12">
            <td ></td>
            <td ></td>
            <td ></td>
            <td  ></td>
            <td style="border-top: 1px solid black; "></td>
        </tr>    
    </table>

  
@endsection