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
    <div style="text-align:center; padding-top: 20px;">
        @if(!empty($empresa->empresa_logo))<img style="width: auto; height: 100px;"
            src="logos/{{ $empresa->empresa_logo }}">@endif
    </div>
    <div style="text-align:center;" class="letra17">{{ $empresa->empresa_nombreComercial }} </div>
    <div style="text-align:center;" class="letra17">{{ $empresa->empresa_razonSocial }} </div>
    <div style="text-align:center;" class="letra17">{{ $empresa->empresa_ruc }} </div>
    <div style="text-align:center; padding-top: 10px;padding-bottom: 10px;" class="letra17"><b>Nota de Entrega No. {{$nt->nt_numero}}</b></div>
    <div style="padding-left: 10px;padding-right: 10px;" class="letra17"><b>Fecha : </b>{{ DateTime::createFromFormat('Y-m-d', $nt->nt_fecha)->format('d/m/Y') }}</div>
    <div style="padding-left: 10px;padding-right: 10px;" class="letra17"><b>Cliente : </b>{{$nt->cliente->cliente_nombre}}</div>
    <div style="padding-left: 10px;padding-right: 10px;" class="letra17"><b>Cédula : </b>{{$nt->cliente->cliente_cedula}}</div>
    <table style="padding: 10px, 7px;white-space: normal; border-collapse: collapse; width: 330px;" class="letra15">
        <thead>    
            <tr>
                <th style="border-top: 1px solid #313233;border-bottom: 1px solid #313233; padding-top: 5px;padding-bottom: 5px;">Descripcion</th>
                <th style="border-top: 1px solid #313233;border-bottom: 1px solid #313233; padding-top: 5px;padding-bottom: 5px;">Cantidad</th>
                <th style="border-top: 1px solid #313233;border-bottom: 1px solid #313233; padding-top: 5px;padding-bottom: 5px;">P.U.</th>
                <th style="border-top: 1px solid #313233;border-bottom: 1px solid #313233; padding-top: 5px;padding-bottom: 5px;">Total</th>
            </tr>
        </thead>
        <tbody>                   
            @if(isset($nt))
                @foreach ($nt->detalle as $detalle)    
                    <tr>                        
                        <td style="white-space: nowrap" align="left">{{$detalle->producto->producto_nombre }}</td>
                        <td style="white-space: nowrap" align="center">{{ $detalle->detalle_cantidad }}</td>
                        <td style="white-space: nowrap" align="right">{{ number_format($detalle->detalle_precio_unitario,2)  }}</td>                   
                        <td style="white-space: nowrap" align="right">{{ number_format($detalle->detalle_total,2) }}</td>
                    </tr>    
                @endforeach
            @endif
            <tr  class="centrar letra15"> 
                <th colspan=4 style="border-top: 1px solid black; "></th>   
            </tr>
            <tr class="letra15" >
                <td class="negrita" align="right"  colspan="3">TOTAL</td>     
                <td class="negrita" align="right" >{{ $nt->nt_total}}</td>
            </tr>   
        </tbody>
    </table>
    <div style="text-align:center;" class="letra17">¡GRACIAS POR SU COMPRA!</div>
</body>
</html>