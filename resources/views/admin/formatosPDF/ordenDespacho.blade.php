@extends ('admin.layouts.formatoPDF')
@section('contenido')
    @section('titulo')
        <tr><td colspan="2"><table><tr><td class="centrar letra22 negrita">ORDEN DE DESPACHO N° {{$orden->orden_numero}}</td></tr></table></td>
    @endsection
    <table>
        <tr class="letra12">
            <td class="negrita" style="width: 105px;">FECHA:</td>
            <td>{{ $orden->orden_fecha }}</td>
        </tr>
        <tr class="letra12">
            <td class="negrita" style="width: 125px;">CLIENTE:</td>
            <td>{{ $orden->cliente->cliente_nombre}}</td>
        </tr>                  
    </table>
    <br>
    <table style="white-space: normal!important;" id="tabladetalle">>
        <thead>
            <tr style="border: 1px solid black;" class="centrar letra10"> 
                <th>Cantidad</th>   
                <th>Codigo</th>                 
                <th>Producto</th>
                <th>P.U.</th>
             
                <th>TOTAL</th>
            </tr>
        </thead>
        <tbody>                   
            @if(isset($orden))
             
                @foreach ($orden->detalles as $detalle)    
                    <tr class="letra10" style="border: 1px solid black;">              
                        <td align="right">{{ $detalle->detalle_cantidad }}</td>          
                        <td align="left">{{$detalle->producto->producto_codigo  }}</td>
                        <td align="left">{{$detalle->producto->producto_nombre }}</td>
                        <td align="right">{{$detalle->detalle_precio_unitario  }}</td>                   
                        <td align="right">{{$detalle->detalle_total }}</td>
                    </tr>    
                @endforeach
            @endif
            <tr class="letra10"  style="border: 1px solid black;">
                <td align="right" style="border: 1px solid black;" colspan="4">TOTAL</td>     
                <td align="right" style="border: 1px solid black;">{{ $orden->orden_total}}</td>
            </tr>   
        </tbody>
    </table>
    <table style="padding-top: 100px;">
        <tr class="letra12">
            <td class="centrar" style="border-top: 1px solid black;">Elaborado por: <br> {{ Auth::user()->user_nombre }}</td>
            <td style="padding-right: 15px; padding-left: 15px;"></td>
            <td class="centrar" style="border-top: 1px solid black;">Revisado por:</td>
            <td style="padding-right: 15px; padding-left: 15px;"></td>
        </tr>
    </table>
    <table style="padding-top: 30px;">
        <tr class="letra12">
            <td class="centrar" style="border: 1px solid black;">OBSERVACION: 
            <br>   {{$orden->orden_comentario}} 
            <br>
        </td>
        <br>    
        </tr>
    </table>
@endsection