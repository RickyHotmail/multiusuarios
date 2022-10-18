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
              <td style="padding-top: 15px;"><b>R.U.C: </b> {{ $empresa->empresa_ruc }}</td>
            </tr>
            <tr>
              <td><b>
                GUIA DE REMISIÓN 
              </b></td>
            </tr>
            <tr>
              <td><b>No. </b> {{substr($guias->gr_serie, 0, -3)}}-{{substr($guias->gr_serie, 3)}}-{{substr(str_repeat(0, 9).$guias->gr_secuencial, - 9)}}</td>
            </tr>
            <tr>
              <td class="txt14"><b>NÚMERO AUTORIZACIÓN</b></td>
            </tr>
            <tr>
              <td class="size-claveacceso">{{$guias->gr_autorizacion}}</td>
            </tr>
            @if($guias->gr_xml_fecha)
                <tr>
                    <td class="txt12"><b class="txt11">FECHA Y HORA DE AUTORIZACION: </b>{{$guias->gr_xml_fecha}}  {{$guias->gr_xml_hora}}</td>
                </tr>
            @endif
            <tr>
              <td class="txt12"><b>AMBIENTE: </b> {{ $guias->gr_ambiente }}</td>
            </tr>
            <tr>
              <td class="txt12"><b>EMISIÓN: </b> NORMAL</td>
            </tr>
            <tr>
              <td class="txt12"><b>CLAVE DE ACCESO</b></td>
            </tr>
            <tr>
              <td><center><img class="size-codigo-barras" src="data:image/png;base64,{{DNS1D::getBarcodePNG($guias->gr_autorizacion, 'C128')}}" alt="barcode" /></center></td>
            </tr>
            <tr>
              <td class="size-claveacceso" style="padding-bottom: 6px;"><center>{{$guias->gr_autorizacion}}</center></td>
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
              <td class="txt12"><b>OBLIGADO A LLEVAR CONTABILIDAD: </b>@if($empresa->empresa_contabilidad=='1')SI @ELSE NO @ENDIF</td>
            </tr>
            @if($empresa->empresa_tipo=='Agente de Retención')
              <tr>
              <td class="txt12"><b>CONTRIBUYENTE RÉGIMEN MICROEMPRESAS</td>
              </tr>
            @endif
            @if($empresa->empresa_tipo=='Microempresas')
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
              <td><b>Identificación (Transportista): </b> {{ $guias->Transportista->transportista_cedula }}</td>
            </tr>
            <tr>
              <td><b>Razón Social / Nombres y Apellidos: </b> {{ $guias->Transportista->transportista_nombre }}</td>
            </tr>  
            <tr>
              <td><b>Placa: </b> {{ $guias->Transportista->transportista_placa }}</td>
            </tr> 
            <tr>
              <td><b>Punto de Partida: </b> {{ $guias->gr_punto_partida }}</td>
            </tr>  
            <tr><td style="padding-bottom: 6px;"><b>Fecha Inicio Transporte: </b> {{ $guias->gr_fecha_inicio }}</td><td style="padding-bottom: 6px;"><b>Fecha Fin Transporte: </b> {{ $guias->gr_fecha_fin }}</td></tr>
          </table>
        </td>
      </tr>
      <tr><td colspan="3"></td></tr>
      <tr>
        <td colspan="3">
          <table class="bordenormal anchoFilaCompleta infoFac infocliente txt12">
            <tr><td></td></tr>
            <tr><td></td></tr> 
            <tr>
              <td colspan="2"><b>Motivo Traslado: </b></td><td colspan="2">{{ $guias->gr_motivo }}</td>
            </tr> 
            <tr>
              <td colspan="2"><b>Destino (Punto de Llegada): </b></td><td colspan="2">{{ $guias->gr_punto_destino }}</td>
            </tr> 
            <tr>
              <td colspan="2"><b>Identificación (Destinatario): </b></td><td colspan="2">{{ $guias->cliente->cliente_cedula }}</td>
            </tr> 
            <tr>
              <td colspan="2"><b>Razón Social / Nombres y Apellidos: </b></td><td colspan="2">{{ $guias->cliente->cliente_nombre }}</td>
            </tr> 
           
            <tr>
              <td colspan="4" style="padding-bottom: 30px; padding-top: 20px;">
                <table id="tabladetalle" class="infoFac2 txt11" style="width:685px;">
                  <tr>
                    <td><b>Cantidad</b></td>
                    <td><b>Código</b></td>
                    <td><b>Descripción</b></td>
                  </tr>
                  @foreach($guias->detalles as $detalle)
                    <tr>
                      <td>{{ $detalle->detalle_cantidad }}</td>
                      <td>{{ $detalle->producto->producto_codigo }}</td>
                      <td>{{ $detalle->producto->producto_nombre }}</td>
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
                            @if($guias->cliente->cliente_email)
                                <tr><td><b>Email: </b> {{ $guias->cliente->cliente_email}}</td></tr>  
                            @endif
                    </table>
                </td>
                <td>
                </td>
            </tr>
          </table>
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