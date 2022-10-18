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
        <td><table><tr><td>@if(!empty($logo))<img class="logo" src="logos/{{ $logo }}">@else <img class="logo" src="logos/NOLOGO.jpg">@endif</td></tr></table></td>
        <td></td>
        <td rowspan="2">
          <table class="bordered infoFac infoFacAncho"> 
            <tr>
              <td style="padding-top: 15px;"><b>R.U.C: </b> {{ $xml->infoTributaria->ruc }}</td>
            </tr>
            <tr>
              <td><b>
                LIQUIDACIÓN DE COMPRA DE BIENES Y PRESTACIÓN DE SERVICIOS
              </b></td>
            </tr>
            <tr>
              <td><b>No. </b> {{$xml->infoTributaria->estab}}-{{$xml->infoTributaria->ptoEmi}}-{{$xml->infoTributaria->secuencial}}</td>
            </tr>
            <tr>
              <td class="txt14"><b>NÚMERO AUTORIZACIÓN</b></td>
            </tr>
            <tr>
              <td class="size-claveacceso">{{$xml->infoTributaria->claveAcceso}}</td>
            </tr>
            <tr>
              <td class="txt12"><b class="txt11">FECHA Y HORA DE AUTORIZACION: </b> {{$fechaAutorizacion}} {{$horaAutorizacion}}</td>
            </tr>
            <tr>
              <td class="txt12"><b>AMBIENTE: </b> {{ $ambiente }}</td>
            </tr>
            <tr>
              <td class="txt12"><b>EMISIÓN: </b> NORMAL</td>
            </tr>
            <tr>
              <td class="txt12"><b>CLAVE DE ACCESO</b></td>
            </tr>
            <tr>
              <td><center><img class="size-codigo-barras" src="data:image/png;base64,{{DNS1D::getBarcodePNG($xml->infoTributaria->claveAcceso, 'C128')}}" alt="barcode" /></center></td>
            </tr>
            <tr>
              <td class="size-claveacceso" style="padding-bottom: 6px;"><center>{{ $xml->infoTributaria->claveAcceso }}</center></td>
            </tr>
          </table>
        </td>
      </tr> 
      <tr>
        <td>
          <table class="bordered infoFac infoFactop">
            <tr>
              <td class="txt16"><b>{{$xml->infoTributaria->nombreComercial}}</b></td>
            </tr>
            <tr>
              <td class="txt12"><b>{{$xml->infoTributaria->razonSocial}}</b></td>
            </tr>
            <tr>
              <td class="txt12"><b>Dirección Matriz: </b> {{$xml->infoTributaria->dirMatriz}}</td>
            </tr>
            @if(array_key_exists('contribuyenteEspecial', (array)$xml->infoLiquidacionCompra))
              <tr>
                <td class="txt12"><b>Contribuyente Especial Nro: </b> {{$xml->infoLiquidacionCompra->contribuyenteEspecial}}</td>
              </tr>
            @endif
            <tr>
              <td class="txt12"><b>OBLIGADO A LLEVAR CONTABILIDAD: </b> {{$xml->infoLiquidacionCompra->obligadoContabilidad}}</td>
            </tr>
            @if(array_key_exists('regimenMicroempresas', (array)$xml->infoTributaria))
              <tr>
                <td class="txt12"><b>CONTRIBUYENTE RÉGIMEN MICROEMPRESAS</td>
              </tr>
            @endif
            @if(array_key_exists('agenteRetencion', (array)$xml->infoTributaria))
              <tr>
                <td class="txt12"><b>Agente de retención Resolución No. 1</td>
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
              <td><b>Nombres y Apellidos: </b> {{ $xml->infoLiquidacionCompra->razonSocialProveedor }}</td>
              <td style="width: 200px;"><b>Identificación: </b> {{ $xml->infoLiquidacionCompra->identificacionProveedor }}</td>
            </tr>
            <tr><td><b>Fecha Emisión: </b> {{ $xml->infoLiquidacionCompra->fechaEmision }}</td></tr>
            <tr><td style="padding-bottom: 6px;"><b>Dirección: </b> {{ $xml->infoLiquidacionCompra->direccionProveedor }}</td></tr>
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
      @foreach($xml->detalles->detalle as $detalle)
        <tr>
          <td>{{ $detalle->cantidad }}</td>
          <td>{{ $detalle->codigoPrincipal }}</td>
          <td>{{ $detalle->descripcion }}</td>
          <td>{{ $detalle->precioUnitario }}</td>
          <td>{{ $detalle->descuento }}</td>
          <td>{{ $detalle->precioTotalSinImpuesto }}</td>
        </tr>
      @endforeach
    </table>
    <table>
      <tr>
        <td colspan="3">
          
        </td>
      </tr>
    </table>
    <table class="anchoFilaCompleta infoFac2 txt11">
      <tr>
        <td VALIGN="TOP" class="infoAdicional">
          <table class="bordenormal infoAdicional2">
            <tr>
              <td><center><b>Información Adicional</b></center></td>
            </tr>
            <tr><td></td></tr>
            @foreach ($xml->infoAdicional->campoAdicional as $adicional)
                <tr><td class="observacion-word"><b>{{$adicional['nombre']}}: </b> {{ $adicional }}</td></tr>
            @endforeach
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
                    @if($xml->infoLiquidacionCompra->pagos->pago->formaPago == 20)<td>OTROS CON UTILIZACIÓN DEL SISTEMA FINANCIERO</td>@endif
                    <td>{{ $xml->infoLiquidacionCompra->pagos->pago->total }}</td>
                    <td>{{ $xml->infoLiquidacionCompra->pagos->pago->plazo }}</td>
                    <td>{{ $xml->infoLiquidacionCompra->pagos->pago->unidadTiempo }}</td>
                  </tr>
                </table>
      </td>
      <td VALIGN="TOP">
          <table id="tabladetalle" class="datosTotalFac">
            <tr>
              <td><b>Subtotal 12%</b></td>
              <td class="datosFilaToto">
                @if(count($xml->infoLiquidacionCompra->totalConImpuestos->totalImpuesto) == 1)
                  @if($xml->infoLiquidacionCompra->totalConImpuestos->totalImpuesto->codigoPorcentaje == 2)
                    {{ $xml->infoLiquidacionCompra->totalConImpuestos->totalImpuesto->baseImponible }} 
                  @else
                    0.00
                  @endif
                @elseif(count($xml->infoLiquidacionCompra->totalConImpuestos->totalImpuesto) == 2)
                  {{ $xml->infoLiquidacionCompra->totalConImpuestos->totalImpuesto[1]->baseImponible }} 
                @else
                  0.00
                @endif
              </td>
            </tr>
            <tr>
              <td><b>Subtotal 0%</b></td>
              <td class="datosFilaToto">
                @if(count($xml->infoLiquidacionCompra->totalConImpuestos->totalImpuesto) == 1)
                  @if($xml->infoLiquidacionCompra->totalConImpuestos->totalImpuesto->codigoPorcentaje == 0)
                    {{ $xml->infoLiquidacionCompra->totalConImpuestos->totalImpuesto->baseImponible }} 
                  @else
                    0.00
                  @endif
                @elseif(count($xml->infoLiquidacionCompra->totalConImpuestos->totalImpuesto) == 2)
                  {{ $xml->infoLiquidacionCompra->totalConImpuestos->totalImpuesto[0]->baseImponible }} 
                @else
                  0.00
                @endif
              </td>
            </tr>
            <tr>
              <td><b>Descuento</b></td>
              <td>{{ $xml->infoLiquidacionCompra->totalDescuento }}</td>
            </tr>
            <tr>
              <td><b>IVA 12% </b></td>
              <td>
                @if(count($xml->infoLiquidacionCompra->totalConImpuestos->totalImpuesto) == 1)
                  @if($xml->infoLiquidacionCompra->totalConImpuestos->totalImpuesto->codigoPorcentaje == 2)
                    {{ $xml->infoLiquidacionCompra->totalConImpuestos->totalImpuesto->valor }} 
                  @else
                    0.00
                  @endif
                @elseif(count($xml->infoLiquidacionCompra->totalConImpuestos->totalImpuesto) == 2)
                  {{ $xml->infoLiquidacionCompra->totalConImpuestos->totalImpuesto[1]->valor }} 
                @else
                  0.00
                @endif
              </td>
            </tr>
            <tr>
              <td><b>Total</b></td>
              <td>{{ $xml->infoLiquidacionCompra->importeTotal }}</td>
            </tr>
          </table>
          <br>
          <br>
        </td>
      </tr>
    </table>
  </body>
</html>