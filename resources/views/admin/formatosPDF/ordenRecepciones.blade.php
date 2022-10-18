@extends ('admin.layouts.formatoPDF')
@section('contenido')
    @section('titulo')
        <tr><td colspan="2"><table><tr><td class="centrar letra22 negrita">ORDEN DE RECEPCION N° {{$orden->ordenr_numero}}</td></tr></table></td>
    @endsection
    <table>
        <tr class="letra12">
            <td class="negrita" style="width: 125px;">PROVEEDOR:</td>
            <td>{{ $orden->proveedor->proveedor_nombre}}</td>
        </tr> 
        <tr class="letra12">
            <td class="negrita" style="width: 105px;">FECHA:</td>
            <td>{{ $orden->ordenr_fecha }}</td>
        </tr>
        <tr class="letra12">
            <td class="negrita" style="width: 105px;">GUIA:</td>
            <td>{{ $orden->ordenr_guia }}</td>
        </tr>     
    </table>
    <br>
    <table style="white-space: normal!important;" id="tabladetalle">
        <thead>
            <tr style="border: 1px solid black;" class="centrar letra10"> 
                <th>Cantidad</th>   
                <th>Codigo</th>                 
                <th>Producto</th>
              
            </tr>
        </thead>
        <tbody>                   
            @if(isset($orden))
             
                @foreach ($orden->detalles as $detalle)    
                    <tr class="letra10" style="border: 1px solid black;">              
                        <td align="right">{{ $detalle->detalle_cantidad }}</td>          
                        <td align="left">{{$detalle->producto->producto_codigo  }}</td>
                        <td align="left">{{$detalle->producto->producto_nombre }}</td>
                        
                    </tr>    
                @endforeach
            @endif
           
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
    <table style="padding-top: 100px;">
        <tr class="letra12">
            <td class="centrar" style="border: 1px solid black;">OBSERVACION: 
            <br>   {{$orden->ordenr_observacion}} 
            <br>
        </td>
        <br>    
        </tr>
    </table>
@endsection