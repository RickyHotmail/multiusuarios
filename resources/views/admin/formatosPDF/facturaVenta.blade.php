<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>PAGUPASOFT</title>
    <link rel="stylesheet" href="admin/css/pdf/docElectronicos.css" media="all" />
  </head>
  <body>
    <table>
      <tr>
        <td><table><tr><td>@if(!empty($empresa->empresa_logo))<img class="logo" src="logos/{{ $empresa->empresa_logo }}">@else <img class="logo" src="logos/NOLOGO.jpg">@endif</td></tr></table></td>
        <td></td>
        <td rowspan="2">
          <table class="bordered infoFac infoFacAncho"> 
            <tr>
              <td style="padding-top: 15px;"><b>R.U.C: </b> {{$empresa->empresa_ruc  }}</td>
            </tr>
            <tr>
              <td><b>
                F A C T U R A 
              </b></td>
            </tr>
            <tr>
              <td><b>No. </b> {{substr($factura->factura_serie, 0, -3)}}-{{substr($factura->factura_serie, 3)}}-{{substr(str_repeat(0, 9).$factura->factura_secuencial, - 9)}}</td>
            </tr>
            <tr>
              <td class="txt14"><b>NÚMERO AUTORIZACIÓN</b></td>
            </tr>
            <tr>
              <td class="size-claveacceso">{{$factura->factura_autorizacion}}</td>
            </tr>
            @if($factura->factura_xml_fecha)
            <tr>
              <td class="txt12"><b class="txt11">FECHA Y HORA DE AUTORIZACION: </b> {{$factura->factura_xml_fecha}} {{$factura->factura_xml_hora}}</td>
            </tr>
            @endif
            <tr>
              <td class="txt12"><b>AMBIENTE: </b> {{ $factura->factura_ambiente }}</td>
            </tr>
            <tr>
              <td class="txt12"><b>EMISIÓN: </b> NORMAL</td>
            </tr>
            <tr>
              <td class="txt12"><b>CLAVE DE ACCESO</b></td>
            </tr>
            <tr>
              <td><center><img class="size-codigo-barras" src="data:image/png;base64,{{DNS1D::getBarcodePNG($factura->factura_autorizacion, 'C128')}}" alt="barcode" /></center></td>
            </tr>
            <tr>
              <td class="size-claveacceso" style="padding-bottom: 6px;"><center>{{ $factura->factura_autorizacion }}</center></td>
            </tr>
          </table>
        </td>
      </tr> 
      <tr>
        <td>
          <table class="bordered infoFac infoFactop">
            <tr>
              <td class="txt16"><b>{{$empresa->empresa_nombreComercial}}</b></td>
            </tr>
            <tr>
              <td class="txt12"><b>{{$empresa->empresa_razonSocial}}</b></td>
            </tr>
            <tr>
              <td class="txt12"><b>Dirección Matriz: </b> {{$empresa->empresa_direccion}}</td>
            </tr>
            @if($empresa->empresa_tipo=='Contribuyente Especial')
              <tr>
                <td class="txt12"><b>Contribuyente Especial Nro: </b> {{$empresa->empresa_contribuyenteEspecial}}</td>
              </tr>
            @endif
            <tr>
              <td class="txt12"><b>OBLIGADO A LLEVAR CONTABILIDAD: </b> @if($empresa->empresa_contabilidad=='1')SI @ELSE NO @ENDIF</td>
            </tr>
            @if($empresa->empresa_tipo=='Microempresas')
              <tr>
              <td class="txt12"><b>Agente de retención Resolución No. 1</td>
              </tr>
            @endif
            @if($empresa->empresa_tipo=='Agente de Retención')
              <tr>
              <td class="txt12"><b>CONTRIBUYENTE RÉGIMEN MICROEMPRESAS</td>
              </tr>
            @endif
          </table>
        </td>
        <td></td>
      </tr>
      <tr><td colspan="3"></td></tr>
      <tr>
        <td colspan="3">
          <table class="bordenormal anchoFilaCompleta infoFac infocliente txt12">
            <tr>
              <td><b>Razón Social / Nombres y Apellidos: </b> {{ $factura->cliente->cliente_nombre }}</td>
              <td style="width: 200px;"><b>Identificación: </b> {{ $factura->cliente->cliente_cedula }}</td>
            </tr>
            <tr><td><b>Fecha Emisión: </b> {{$factura->factura_fecha }}</td></tr>
            <tr><td style="padding-bottom: 6px;"><b>Dirección: </b> {{ $factura->cliente->cliente_direccion }}</td></tr>
          </table>
        </td>
      </tr>
      <tr><td colspan="3"></td></tr>
    </table>
    <table id="tabladetalle" class="anchoFilaCompleta infoFac2 txt11">
      <tr>
        <td><b>Cant.</b></td>
        <td><b>Código</b></td>
        <td><b>Descripción</b></td>
        <td><b>P. Unitario</b></td>
        <td><b>Descuento</b></td>
        <td class="datosFilaToto"><b>Precio Total</b></td>
      </tr>
      @foreach($factura->detalles as $detalle)
        <tr>
          <td>{{ $detalle->detalle_cantidad }}</td>
          <td>{{ $detalle->producto->producto_codigo }}</td>
          <td>{{ $detalle->detalle_descripcion }}</td>
          <td>{{ $detalle->detalle_precio_unitario }}</td>
          <td>{{ $detalle->detalle_descuento }}</td>
          <td>{{ number_format($detalle->detalle_total,2) }}</td>
        </tr>
      @endforeach
    </table>
    <table>
      <tr>
        <td colspan="3">
          
        </td>
      </tr>
    </table><br><br><br>
    <table class="anchoFilaCompleta infoFac2 txt11">
      <tr>
        <td VALIGN="TOP" class="infoAdicional">
          <table class="bordenormal infoAdicional2">
            <tr>
              <td><center><b>Información Adicional</b></center></td>
            </tr>
            <tr><td></td></tr>
           
                <tr><td><b>Email: </b> {{ str_replace(';','; ',$factura->cliente->cliente_email) }}</td></tr>
                <tr><td><b>Observacion: </b> {{ $factura->factura_comentario }}</td></tr>
           
          </table>
          <br>
          <table id="tabladetalle" class="infoAdicional2">
            <tr>
              <td colspan="4"><center><b>Forma de Pago</b></center></td>
            </tr>
            <tr>
              <td><b>Descripción: </b></td>
              <td><b>Valor: </b></td>
              <td><b>Plazo: </b></td>
              <td><b>Tiempo: </b></td>
            </tr>
            <tr>
              @if($factura->formaPago->forma_pago_codigo == 20)<td>OTROS CON UTILIZACIÓN DEL SISTEMA FINANCIERO</td>@endif
              <td>{{ number_format($factura->factura_total,2) }}</td>
              <td>{{$factura->factura_dias_plazo }}</td>
              <td> dias </td>
            </tr>
          </table>
        </td>
        <td VALIGN="TOP">
          <table id="tabladetalle" class="datosTotalFac" >
            <tr>
              <td><b>Subtotal 12%</b></td>
              <td  class="datosFilaToto" >
              {{number_format($factura->factura_tarifa12,2) }}
              </td>
            </tr>
            <tr>
              <td><b>Subtotal 0%</b></td>
              <td class="datosFilaToto">
              {{number_format($factura->factura_tarifa0,2) }}
              </td>
            </tr>
            <tr>
              <td><b>Descuento</b></td>
              <td>{{number_format($factura->factura_descuento,2) }}</td>
            </tr>
            <tr>
              <td><b>IVA 12% </b></td>
              <td>
              {{number_format($factura->factura_iva,2) }}
              </td>
            </tr>
            <tr>
              <td><b>Total Factura</b></td>
              <td> {{number_format($factura->factura_total,2) }}</td>
            </tr>
          </table>
          <br>
          <br>
        </td>
      </tr>
    </table>
    <div id="footer"> Este documento no tiene valor tributario, consulte su comprobante electrónico en www.sri.gob.ec </div>
  </body>
</html>

<style>

#footer {
    position: absolute;
    bottom: 0;
}
</style>