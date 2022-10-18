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
                GUIA DE REMISIÓN 
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
            @if(array_key_exists('contribuyenteEspecial', (array)$xml->infoGuiaRemision))
              <tr>
                <td class="txt12"><b>Contribuyente Especial Nro: </b> {{$xml->infoGuiaRemision->contribuyenteEspecial}}</td>
              </tr>
            @endif
            <tr>
              <td class="txt12"><b>OBLIGADO A LLEVAR CONTABILIDAD: </b> {{$xml->infoGuiaRemision->obligadoContabilidad}}</td>
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
              <td><b>Identificación (Transportista): </b> {{ $xml->infoGuiaRemision->rucTransportista }}</td>
            </tr>
            <tr>
              <td><b>Razón Social / Nombres y Apellidos: </b> {{ $xml->infoGuiaRemision->razonSocialTransportista }}</td>
            </tr>  
            <tr>
              <td><b>Placa: </b> {{ $xml->infoGuiaRemision->placa }}</td>
            </tr> 
            <tr>
              <td><b>Punto de Partida: </b> {{ $xml->infoGuiaRemision->dirPartida }}</td>
            </tr>  
            <tr><td style="padding-bottom: 6px;"><b>Fecha Inicio Transporte: </b> {{ $xml->infoGuiaRemision->fechaIniTransporte }}</td><td style="padding-bottom: 6px;"><b>Fecha Fin Transporte: </b> {{ $xml->infoGuiaRemision->fechaFinTransporte }}</td></tr>
          </table>
        </td>
      </tr>
      <tr><td colspan="3"></td></tr>
      <tr>
        <td colspan="3">
          <table class="bordenormal anchoFilaCompleta infoFac infocliente txt12">
            @if(array_key_exists('codDocSustento',(array)$xml->destinatarios->destinatario[0]))
              <tr>
                <td><b>Comprobante de Venta: </b></td><td>@if($xml->destinatarios->destinatario[0]->codDocSustento == '01') FACTURA @endif {{ $xml->destinatarios->destinatario[0]->numDocSustento }}</td>
                <td style="width: 200px;"><b>Fecha Emisión: </b></td><td>{{ $xml->destinatarios->destinatario[0]->fechaEmisionDocSustento }}</td>
              </tr>
            @endif
            @if(array_key_exists('numAutDocSustento',(array)$xml->destinatarios->destinatario[0]))
            <tr>
              <td colspan="2"><b>Número de Autorización: </b></td><td colspan="2">{{ $xml->destinatarios->destinatario[0]->numAutDocSustento }}</td>
            </tr> 
            @endif
            <tr><td></td></tr>
            <tr><td></td></tr> 
            <tr>
              <td colspan="2"><b>Motivo Traslado: </b></td><td colspan="2">{{ $xml->destinatarios->destinatario[0]->motivoTraslado }}</td>
            </tr> 
            <tr>
              <td colspan="2"><b>Destino (Punto de Llegada): </b></td><td colspan="2">{{ $xml->destinatarios->destinatario[0]->dirDestinatario }}</td>
            </tr> 
            <tr>
              <td colspan="2"><b>Identificación (Destinatario): </b></td><td colspan="2">{{ $xml->destinatarios->destinatario[0]->identificacionDestinatario }}</td>
            </tr> 
            <tr>
              <td colspan="2"><b>Razón Social / Nombres y Apellidos: </b></td><td colspan="2">{{ $xml->destinatarios->destinatario[0]->razonSocialDestinatario }}</td>
            </tr> 
            @if(array_key_exists('docAduaneroUnico',(array)$xml->destinatarios->destinatario[0]))
              <tr>
                <td colspan="2"><b>Documento Aduanero: </b></td><td colspan="2">{{ $xml->destinatarios->destinatario[0]->docAduaneroUnico }}</td>
              </tr> 
            @endif
            @if(array_key_exists('codEstabDestino',(array)$xml->destinatarios->destinatario[0]))
              <tr>
                <td colspan="2"><b>Código Establecimiento Destino: </b></td><td colspan="2">{{ $xml->destinatarios->destinatario[0]->codEstabDestino }}</td>
              </tr>  
            @endif
            @if(array_key_exists('ruta',(array)$xml->destinatarios->destinatario[0]))
              <tr>
                <td colspan="2"><b>Ruta: </b></td><td colspan="2">{{ $xml->destinatarios->destinatario[0]->ruta }}</td>
              </tr> 
            @endif
            <tr>
              <td colspan="4" style="padding-bottom: 30px; padding-top: 20px;">
                <table id="tabladetalle" class="infoFac2 txt11" style="width:685px;">
                  <tr>
                    <td><b>Cantidad</b></td>
                    <td><b>Código</b></td>
                    <td><b>Descripción</b></td>
                  </tr>
                  @foreach($xml->destinatarios->destinatario->detalles->detalle as $detalle)
                    <tr>
                      <td>{{ $detalle->cantidad }}</td>
                      <td>{{ $detalle->codigoInterno }}</td>
                      <td>{{ $detalle->descripcion }}</td>
                    </tr>
                  @endforeach
                </table>
              </td>
            </tr>
          </table>
        </td>
      </tr>
      <tr><td colspan="3"></td></tr>
      <tr>
        <td colspan="3">
          <table class="bordenormal anchoFilaCompleta infoFac infocliente txt12">
            <tr><td><br></td></tr>  
            <tr><td><br></td></tr> 
            <tr><td><br></td></tr>
            <tr>
              <td style="padding-bottom: 6px; border-top: black 1px solid;padding-right: 10px;padding-left: 10px;"><center><b>Elaborado por </b></center></td>
              <td style="padding-right: 10px;padding-left: 10px;"></td>
              <td style="padding-bottom: 6px; border-top: black 1px solid;padding-right: 10px;padding-left: 10px;"><center><b>Recibi Conforme</b></center></td>
              <td></td>
            </tr>
          </table>
        </td>
      </tr>
      <tr>
        <td colspan="3">
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
                </td>
                <td>
                </td>
            </tr>
          </table>
        </td>
      </tr>
    </table>
  </body>
</html>