@extends ('admin.layouts.formatoPDF')
@section('contenido')
    @section('titulo')
        <tr><td colspan="2" class="centrar letra15 negrita">DECIMO TERCERO</td></tr>
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
            <td style="width:40px;">{{ $tercero->empleado->empleado_nombre }} </td>
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
             <td class=" letra15 " style="width: 105px;">PAGO DECIMO TERCERO REMUNERACION, PERIODO REPORTADO: 01/12/{{ DateTime::createFromFormat('Y-m-d', $tercero->decimo_fecha)->format('Y')-1 }} AL  01/11/{{ DateTime::createFromFormat('Y-m-d', $tercero->decimo_fecha)->format('Y') }}, CON 
             @IF($tercero->decimo_tipo == 'Efectivo') EFECTIVO @ENDIF 
             @IF($tercero->decimo_tipo == 'Cheque') CHEQUE N°  
                @foreach($tercero->diario->detalles as $detalle)
                    @foreach($detalle->cheque as $cheque)
                        {{$cheque->cheque_numero}}
                    @endforeach
                @endforeach
            @ENDIF 
             @IF($tercero->decimo_tipo == 'Transferencia') TRANFERENCIA @ENDIF 
             POR EL VALOR DE $  {{ $tercero->decimo_valor}}
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
            <td class="centrar negrita" style=" border-top: 1px solid black; width: 30%;">Recibi Conforme: <br> C.I: {{ $tercero->empleado->empleado_cedula }}</td>
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