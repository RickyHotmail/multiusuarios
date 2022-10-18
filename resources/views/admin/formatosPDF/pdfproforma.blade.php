@extends ('admin.layouts.formatoPDF')
@section('contenido')
    @section('titulo')
        <tr><td colspan="2" class="centrar letra22 negrita">PROFORMA N° {{$prof->proforma_numero}}</td></tr>
    @endsection
    <table style="white-space: normal!important; border-collapse: collapse;">
        <tr class="letra14">
            <td class="negrita">CLIENTE:</td>
            <td >{{ $prof->cliente->cliente_nombre}}</td>
            <td class="negrita" >RUC:</td>
            <td>{{$prof->cliente->cliente_cedula}}</td>
        </tr>
       
               
        
        <tr class="letra14">
            <td class="negrita">TIPO PAGO:</td>
            <td  >{{ $prof->proforma_tipo_pago }}</td>
            <td class="negrita" >FECHA:</td>
            <td>{{ $prof->proforma_fecha }}</td>
        </tr>
        @if($prof->proforma_tipo_pago=='CREDITO')
        <tr class="letra14">
            <td class="negrita">PLAZO:</td>
            <td  >{{ $prof->proforma_dias_plazo }}</td>
           
        </tr>
        @endif
        
    </table>
    <br>
    <table >
        <thead>
            <tr  class="centrar letra10"> 
                <th colspan=6 style="border-top: 1px solid black; "></th>   
            </tr>
            <tr  class=" letra14"> 
                <th class="centrar">Cantidad</th>   
                <th class="centrar">Codigo</th>                 
                <th align="left">Producto</th>
                
                <th align="right">P.U.</th>
                <th align="right">Desc.</th>
                <th align="right">TOTAL</th>
            </tr>
            <tr  class="centrar letra14"> 
                <th colspan=6 style="border-top: 1px solid black; "></th>   
            </tr>
        </thead>
        <tbody>                   
           
                @foreach ($prof->detalles as $detalle)    
                    <tr class="letra14" style="border: 1px solid black;">              
                        <td align="center">{{ $detalle->detalle_cantidad }}</td>          
                        <td align="center">{{$detalle->producto->producto_codigo  }}</td>
                        <td align="left" style="white-space: pre-wrap;">{{$detalle->producto->producto_nombre }}</td>
                        <td align="right">{{ number_format($detalle->detalle_precio_unitario,2)  }}</td>   
                        <td align="right">{{ number_format($detalle->detalle_descuento,2)  }}</td>                    
                        <td align="right">{{ number_format($detalle->detalle_total,2) }}</td>
                    </tr>    
                @endforeach
           
           
            <tr  class="centrar letra14"> 
                <th colspan=6 style="border-top: 1px solid black; "></th>   
            </tr>
            <tr class="letra14" >
                <td class="negrita" align="right"  colspan="5">Subtotal 12%</td>     
                <td class="negrita" align="right" >{{ number_format($prof->proforma_subtotal,2) }}</td>
            </tr>
            <tr class="letra14" >
                <td class="negrita" align="right"  colspan="5">Subtotal 0%</td>     
                <td class="negrita" align="right" >{{ number_format($prof->proforma_tarifa0,2) }}</td>
            </tr>
            <tr class="letra14" >
                <td class="negrita" align="right"  colspan="5">Descuento</td>     
                <td class="negrita" align="right" >{{ number_format($prof->proforma_descuento,2) }}</td>
            </tr>

            <tr class="letra14" >
                <td class="negrita" align="right"  colspan="5">IVA 12%</td>     
                <td class="negrita" align="right" >{{ number_format($prof->proforma_tarifa12,2) }}</td>
            </tr>
            <tr class="letra14" >
                <td class="negrita" align="right"  colspan="5">Total</td>     
                <td class="negrita" align="right" >{{ number_format($prof->proforma_total,2) }}</td>
            </tr>    
        </tbody>
    </table>
    <table style="padding-top: 100px;">
        <tr class="letra14">
            <td class="negrita" style="width: 105px;">OBSERVACION: {{$prof->proforma_comentario}} </td>
        </tr>
    </table>
    
    
@endsection