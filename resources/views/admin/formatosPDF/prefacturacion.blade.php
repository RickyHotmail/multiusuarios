<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>PAGUPASOFT</title>
    <link rel="stylesheet" href="admin/css/pdf/documentosPDF.css" media="all" />
</head>
<body >
    <header>
        <table cellspacing="0" cellpadding="0" border="1" >
            <tr>
                <td colspan="2">
                    <table  >
                        
                        <tr  >
                            <td class="  negrita" ALIGN="right">REPORTE DE VENTA </td>
                            <td class=" negrita" ALIGN="right">N° {{ $prefactura->prefactura_numero }}</td>
                        </tr>
                        
                       
                    </table>
                </td>
            </tr>
            <tr>
                
                <td style="width: 180px;">
                    <table>
                        <tr>
                            <td class="centrar">@if(!empty($empresa->empresa_logo))<img class="logo"
                                    src="logos/{{$empresa->empresa_logo}}">@endif</td>
                        </tr>
                    </table>
                </td>
                <td>
                    <table>
                        
                        <tr>
                            <td class="letra14 negrita">{{ $empresa->empresa_nombreComercial }}</td>
                            <td class="letra14 "><strong>Fecha:</strong>{{DateTime::createFromFormat('Y-m-d', $prefactura->prefactura_fecha)->format('d/m/Y')}} </td>
                        </tr>
                        <tr>
                            <td class="letra14">{{$empresa->empresa_direccion}}</td>
                            <td class="letra14 "></td>
                        </tr>
                        <tr>
                            <td class="letra14"><strong>RUC:</strong> {{ $empresa->empresa_ruc }}</td>
                            <td class="letra14 "></td>
                        </tr>
                    </table>
                
                   
                </td>
            </tr>   
        </table>
    </header>
    <table style="border: solid 1px #000000;">
        
        <tr class="letra12" >
            <td style="white-space: pre-wrap;"  ><strong>CLIENTE:</strong> {{$prefactura->cliente->cliente_nombre}}</td>
           
            <td  ><strong>RUC:</strong> {{$prefactura->cliente->cliente_cedula}}</td>
          
        </tr>
        <tr class="letra12" >  
            <td  style="white-space: pre-wrap; "  align="left"  rowspan="2"><strong>DIRECCION:</strong>  {{$prefactura->cliente->cliente_direccion}}</td>
            <td ><strong>EMAIL:</strong> {{$prefactura->cliente->cliente_email}}</td>
        </tr>
        <tr class="letra12" >
            <td ><strong>TELEFONO:</strong> {{ $prefactura->cliente->cliente_telefono }}</td>
        </tr>
        <tr class="letra12">
            <td><strong>TIPO PAGO:</strong> {{ $prefactura->prefactura_tipo_pago }}</td>
            <td ><strong>PLAZO:</strong>  @if($prefactura->prefactura_tipo_pago=='CREDITO') {{ $prefactura->prefactura_dias_plazo }} @else 0 @endif</td>
            
        </tr>
        
    </table>
    <br>
    <table cellspacing="0" cellpadding="0" border="1">
        <tr class="letra14">
            <td colspan="2" class="centrar"><strong>{{$prefactura->prefactura_tipo}}:</strong> {{$prefactura->prefactura_aguaje}}</td>   
        </tr> 
    </table> 
    <br>
    <table cellspacing="0" cellpadding="0" border="1">
        <thead>
           
            <tr  class="centrar letra12"> 
                <th class="negrita"> Fecha </th>   
                <th class="negrita">Guia</th>   
                <th class="negrita">Transportista</th>   
                <th class="negrita">Placa</th>   
                <th class="negrita">Producto</th>
                <th class="negrita">Cant.</th>   
                <th class="negrita">P.U.</th>          
                <th class="negrita">Subtot.</th>
                <th class="negrita">Iva</th>
                <th class="negrita">Total</th>
            </tr>
          
        </thead>
        <tbody>                   
           
                @foreach ($prefactura->detalles as $detalle)    
                <tr class="centrar">
                        <td style="font-size: 8px ">{{ $detalle->guia->gr_fecha }}</td>  
                        <td style="font-size: 9px ">{{ $detalle->guia->gr_secuencial }}</td>  
                        <td style="font-size: 10px ">{{ $detalle->guia->Transportista->transportista_nombre }}</td>  
                        <td style="font-size: 9px ">{{ $detalle->guia->gr_placa }}</td>  
                        <td style="font-size: 11px ">{{$detalle->producto->producto_nombre }}</td>
                        <td style="font-size: 9px ">{{ $detalle->detalle_cantidad }}</td>          
                       
                        <td style="font-size: 10px ">{{ number_format($detalle->detalle_precio_unitario,2)  }}</td> 
                        <td style="font-size: 10px ">{{ number_format($detalle->detalle_precio_unitario*$detalle->detalle_cantidad,2)  }}</td>    
                        <td style="font-size: 10px ">{{ number_format($detalle->detalle_iva,2)  }}</td>                   
                        <td style="font-size: 10px " >{{ number_format($detalle->detalle_total+$detalle->detalle_iva,2) }}</td>
                    </tr>    
                @endforeach
           
           
            
        </tbody>
    </table>
    <table width="100%">
       <th >
            <td VALIGN="TOP" width="70%">
                <table style="border: solid 1px #000000;" width="width:100%">
                    <tr class="centrar letra12">
                        <td colspan="2" class="negrita" width="100%">Informacion Adicional</td>     
                    </tr>
                 
                    <tr class="centrar letra12">
                        <td  class="negrita"  align="left"   >TOTAL DE SACOS: {{ number_format($prefactura->prefactura_total_sacos,2) }}</td>     
                        
                    
                    </tr>
                    <tr class="centrar letra12">
                        <td class="negrita" align="left" >SACOS PRESTADO: {{number_format($saco,2) }}</td>     
                       
                    
                    </tr>
                    @if(isset($movi))
                        @for ($i = 1; $i <= count($movi); ++$i)    
                        <tr class="centrar letra12">
                            <td class="negrita" align="left" style="white-space: pre-wrap;">{{$movi[$i]['descripcion']}} POR DEVOLVER: {{ number_format($movi[$i]['total'],2) }}</td>     
                        </tr>                                                               
                        @endfor
                    @endif
                   
                   
                    <tr  class="centrar letra12">
                       
                        <td class="negrita" align="left" style="white-space: pre-wrap;" >OBSERVACION: {{$prefactura->prefactura_comentario}} </td>  
                    
                    </tr>

                </table>
            </td>
            <td VALIGN="TOP" width="30%">
                <table cellspacing="0" cellpadding="0" border="1">
                    <tr class="centrar letra12">
                        
                        <td class="negrita"  >SubTotal</td>     
                        <td  align="right" >{{ number_format($prefactura->prefactura_subtotal,2) }}</td>
                    </tr>
                    <tr class="centrar letra12">
                    
                        <td class="negrita"  >Descuento</td>     
                        <td  align="right" >{{ number_format($prefactura->prefactura_descuento,2) }}</td>
                    </tr>
                    <tr class="centrar letra12">
                        
                        <td class="negrita"  >Tarifa 12 %</td>     
                        <td  align="right" >{{ number_format($prefactura->prefactura_tarifa12,2) }}</td>
                    </tr>
                    <tr class="centrar letra12">
                    
                        <td class="negrita"  >Tarifa 0%</td>     
                        <td  align="right" >{{ number_format($prefactura->prefactura_tarifa0,2) }}</td>
                    </tr>
                    <tr class="centrar letra12">
                        
                        <td class="negrita"  >Iva 12 %</td>     
                        <td  align="right" >{{ number_format($prefactura->prefactura_iva,2) }}</td>
                    </tr>
                    <tr class="centrar letra12">
                    
                        <td class="negrita"  >Total</td>     
                        <td  align="right" >{{ number_format($prefactura->prefactura_total,2) }}</td>
                    </tr>
                </table>
            </td>
            
        </th>
        
    </table>
    <footer>
        <table>
            <tr>
                <td class="letra12">
                    <p class="izq">
                        NEOPAGUPA
                    </p>
                </td>
                <td class="letra12">
                    <p class="page">
                        Página
                    </p>
                </td>
            </tr>
        </table>
    </footer>
</body>

</html>
<style>

</style>

