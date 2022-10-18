@extends ('admin.layouts.formatoPDF')
@section('contenido')
    @section('titulo')
        <tr><td colspan="2"><table><tr><td class="centrar letra25 negrita">MAYORIZACIÓN DE CUENTAS</td></tr></table></td>
        </tr>
            <tr>
                <td colspan="2">
                <table>
                    <tr>
                        <td class="centrar letra15">DEL {{ $desde }} AL {{ $hasta }}</td>
                        <td class="centrar letra15">PRODUCTOS {{ $producto}}</td>
                        <td class="centrar letra15">CLIENTE {{ $cliente}}</td>
                    </tr>
                </table>
                </td>
            </tr>  
    <br>      
    @endsection
    <br>  
    <table style="table-layout: fixed; white-space: normal!important;" id="tabladetalle">>
        <thead>
            <tr style="border: 1px solid black;" class="centrar letra10">           
                <th>N° de Documento</th>
                <th>Fecha</th>             
                <th>Producto</th>
                <th>Cantidad</th>
                <th>PVP</th>
                <th>Iva</th>
                <th>Sub 12%</th>
                <th>Sub 0%</th>
                <th>Total</th>              
                <th>Cliente</th>
                <th>Peso Libras</th>
                <th>Peso Kilos</th>
                <th>Peso TM</th>
                <th>Factura</th>              
                <th>Comentario</th>
                <th>Embarcacion</th>
            </tr>
        </thead>
        <tbody>                   
            @if(isset($datos))
                @for ($i = 1; $i <= count($datos); ++$i)    
                <tr class="letra10">              
                    <td style="font-size: 7px ">{{ $datos[$i]['orden_numero'] }}</td>
                    <td align="left">{{ $datos[$i]['orden_fecha'] }}</td>                  
                    <td align="left">{{ $datos[$i]['detalle_descripcion'] }}</td>
                    <td align="right">{{ $datos[$i]['detalle_cantidad'] }}</td>
                    <td align="right">{{ $datos[$i]['precio'] }}</td>
                    <td align="right">{{ $datos[$i]['iva'] }}</td>
                    <td align="right">{{ $datos[$i]['sub0'] }}</td>
                    <td align="right">{{ $datos[$i]['sub12'] }}</td>
                    <td align="right">{{ $datos[$i]['total'] }}</td>                 
                    <td align="left">{{ $datos[$i]['cliente_nombre'] }}</td>
                    <td align="right">{{ $datos[$i]['libras'] }}</td>
                    <td align="right">{{ $datos[$i]['kilos'] }}</td>
                    <td align="right">{{ $datos[$i]['tm'] }}</td>
                    <td style="font-size: 7px ">{{ $datos[$i]['factura'] }}</td>
                    <td style="font-size: 7px ">{{ $datos[$i]['orden_comentario'] }}</td>
                    <td style="font-size: 7px ">{{ $datos[$i]['embarcacion'] }}</td>
                    @endfor
            @endif
        </tbody>
    </table>
@endsection