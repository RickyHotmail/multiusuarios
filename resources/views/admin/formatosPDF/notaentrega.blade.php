@extends ('admin.layouts.formatoPDF')
@section('contenido')
    @section('titulo')
        <tr><td colspan="2" class="centrar letra22 negrita">NOTA DE ENTREGA N° {{$nt->nt_numero}}</td></tr>
    @endsection
    <table style="white-space: normal!important; border-collapse: collapse;">
        <tr class="letra14">
            <td class="negrita" style="width: 125px;">CLIENTE:</td>
            <td>{{ $nt->cliente->cliente_nombre}}</td>
        </tr>
       
        <tr class="letra14">
            <td class="negrita" style="width: 125px;">RUC:</td>
            <td>{{$nt->cliente->cliente_cedula}}</td>
        </tr>       
        
        <tr class="letra14">
            <td class="negrita" style="width: 105px;">FECHA:</td>
            <td>{{ $nt->nt_fecha }}</td>
        </tr>
        <tr class="letra14">
            <td class="negrita" style="width: 125px;">TELEFONO:</td>
            <td>{{$nt->cliente->cliente_telefono}}</td>
        </tr>  
    </table>
    <br>
    <table >
        <thead>
            <tr  class="centrar letra10"> 
                <th colspan=5 style="border-top: 1px solid black; "></th>   
            </tr>
            <tr  class=" letra14"> 
                <th class="centrar">Cantidad</th>   
                <th class="centrar">Codigo</th>                 
                <th align="left">Producto</th>
                <th align="right">P.U.</th>
             
                <th align="right">TOTAL</th>
            </tr>
            <tr  class="centrar letra14"> 
                <th colspan=5 style="border-top: 1px solid black; "></th>   
            </tr>
        </thead>
        <tbody>                   
            @if(isset($nt))
             
                @foreach ($nt->detalle as $detalle)    
                    <tr class="letra14" style="border: 1px solid black;">              
                        <td align="center">{{ $detalle->detalle_cantidad }}</td>          
                        <td align="center">{{$detalle->producto->producto_codigo  }}</td>
                        <td align="left" style="white-space: pre-wrap;">{{$detalle->producto->producto_nombre }}</td>
                        <td align="right">{{ number_format($detalle->detalle_precio_unitario,2)  }}</td>                   
                        <td align="right">{{ number_format($detalle->detalle_total,2) }}</td>
                    </tr>    
                @endforeach
            @endif
           
            <tr  class="centrar letra14"> 
                <th colspan=5 style="border-top: 1px solid black; "></th>   
            </tr>
            <tr class="letra14" >
                <td class="negrita" align="right"  colspan="4">TOTAL</td>     
                <td class="negrita" align="right" >{{ number_format($nt->nt_total,2) }}</td>
            </tr>   
        </tbody>
    </table>
    <table style="padding-top: 100px;">
           
        <tr class="letra14">
            <td class="negrita" style="width: 105px;">TIPO DE PAGO: {{$nt->nt_tipo_pago}} </td>
        </tr>
        <tr class="letra14">
            <td class="negrita" style="width: 105px;">OBSERVACION: {{$nt->nt_comentario}} </td>
        </tr>
    </table>
    <table style="padding-top: 100px;">
        <tr class="letra14 ">
            <td class="centrar" style="border-top: 1px solid black;white-space: pre-wrap;">Elaborado por: <br> {{ Auth::user()->user_nombre }}</td>
            <td style="padding-right: 15px; padding-left: 15px;"></td>
            <td class="centrar" style="border-top: 1px solid black;white-space: pre-wrap;">Recibido por: <br> {{ $nt->cliente->cliente_nombre}} <br> {{ $nt->cliente->cliente_cedula}}</td>
            <td style="padding-right: 15px; padding-left: 15px;"></td>
        </tr>
    </table>
    
@endsection