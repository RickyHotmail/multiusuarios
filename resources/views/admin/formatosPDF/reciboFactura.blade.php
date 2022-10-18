<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>PAGUPASOFT</title>
    <style type="text/css">
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }
    </style>
    <link rel="stylesheet" href="admin/css/pdf/documentosPDF.css" media="all" />
</head>
<body style="width: 330px;">
    <div style="text-align:center; padding-top: 20px;" class="letra17">{{ $empresa->empresa_nombreComercial }} </div>
    <div style="text-align:center;" class="letra17">{{ $empresa->empresa_razonSocial }} </div>
    <div style="text-align:center;" class="letra17">RUC: {{ $empresa->empresa_ruc }} </div>
    <div style="text-align:center;" class="letra17">DIR.: {{ $empresa->empresa_direccion }} </div>
    <div style="text-align:center;" class="letra17">TEL.: {{ $empresa->empresa_telefono }} </div>
    
    <div style="padding-left: 10px;padding-right: 10px; padding-top: 5px;" class="letra17"><b>Fecha : </b>{{ DateTime::createFromFormat('Y-m-d', $factura->factura_fecha)->format('d/m/Y') }}</div>
    <div style="padding-left: 10px;padding-right: 10px;" class="letra15"><b>Cliente : </b>{{$factura->cliente->cliente_nombre}}</div>
    <div style="padding-left: 10px;padding-right: 10px;" class="letra15"><b>Ruc : </b>{{$factura->cliente->cliente_cedula}}</div>
    <div style="padding-left: 10px;padding-right: 10px;" class="letra15"><b>Dirección : </b>{{$factura->cliente->cliente_direccion}}</div>
    <table style="padding: 10px, 7px;white-space: normal; border-collapse: collapse; width: 330px;" class="letra15">
        <thead>    
            <tr>
                <th class="letra14" style="border-top: 1px solid #313233;border-bottom: 1px solid #313233; padding-top: 5px;padding-bottom: 5px;">Descripcion</th>
                <th class="letra14" style="border-top: 1px solid #313233;border-bottom: 1px solid #313233; padding-top: 5px;padding-bottom: 5px;">Cant</th>
                <th class="letra14" style="border-top: 1px solid #313233;border-bottom: 1px solid #313233; padding-top: 5px;padding-bottom: 5px;">P.U.</th>
                <th class="letra14" style="border-top: 1px solid #313233;border-bottom: 1px solid #313233; padding-top: 5px;padding-bottom: 5px;">Total</th>
            </tr>
        </thead>
        <tbody>                   
            @if(isset($factura))
                @foreach ($factura->detalles as $detalle)    
                    <tr>                        
                        <td class="letra11" align="left">{{$detalle->producto->producto_nombre }}</td>
                        <td class="letra11"align="center">{{ $detalle->detalle_cantidad }}</td>
                        <td class="letra11"align="right">{{ number_format($detalle->detalle_precio_unitario,2)  }}</td>                   
                        <td class="letra11"align="right">{{ number_format($detalle->detalle_total,2) }}</td>
                    </tr>    
                @endforeach
            @endif
            <tr  class="centrar letra15"> 
                <th colspan=4 style="border-top: 1px solid black; "></th>   
            </tr>
            <tr class="letra15" >
                <td  align="right"  colspan="3">SUBTOTAL</td>     
                <td  align="right" >{{ number_format($factura->factura_subtotal,2)}}</td>
            </tr>
            <tr class="letra15" >
                <td  align="right"  colspan="3">DESCUENTO</td>     
                <td  align="right" >{{ number_format($factura->factura_descuento,2)}}</td>
            </tr>
            <tr class="letra15" >
                <td  align="right"  colspan="3">TARIFA 12%</td>     
                <td  align="right" >{{ number_format($factura->factura_descuento,2)}}</td>
            </tr>
            <tr class="letra15" >
                <td  align="right"  colspan="3">TAFIRA 0%</td>     
                <td  align="right" >{{ number_format($factura->factura_descuento,2)}}</td>
            </tr>
            <tr class="letra15" >
                <td  align="right"  colspan="3">IVA {{ $factura->factura_porcentaje_iva}}%</td>     
                <td  align="right" >{{ number_format($factura->factura_descuento,2)}}</td>
            </tr>
            <tr class="letra15" >
                <td class="negrita" align="right"  colspan="3">TOTAL</td>     
                <td class="negrita" align="right" >{{ number_format($factura->factura_total,2)}}</td>
            </tr>   
        </tbody>
    </table>
    <div style=" padding-top: 10px;padding-bottom: 2px; padding-left: 5px;" class="letra17"><b>Observacion : {{$factura->factura_comentario}}</b></div>
    <div style=" padding-top: 2px;padding-bottom: 2px; padding-left: 5px;" class="letra17"><b>FACTURA No. {{$factura->factura_numero}}</b></div>
    <div style="text-align:center; padding-top: 2px;padding-bottom: 2px;" class="letra17"><b>Consulte su comprobante electronico en el portal del SRI</b></div>
    <div style="text-align:center; padding-top: 2px;padding-bottom: 2px;" class="letra11"><b>CLAVE DE ACCESO: : {{$factura->factura_autorizacion}}</b></div>
    <div style="text-align:center;" class="letra17">---------------------------------------------------------------------</div>
    <div style="text-align:center;" class="letra17">Retención y Contacto al correo jfarias@costamarket.ec</div>
</body>
</html>