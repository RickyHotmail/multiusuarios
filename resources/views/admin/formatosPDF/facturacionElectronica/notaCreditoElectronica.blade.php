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
            <td>
                <table>
                    <tr>
                        <td>@if(!empty($logo))<img class="logo" src="logos/{{ $logo }}">@else <img class="logo"
                                src="logos/NOLOGO.jpg">@endif</td>
                    </tr>
                </table>
            </td>
            <td></td>
            <td rowspan="2">
                <table class="bordered infoFac infoFacAncho">
                    <tr>
                        <td style="padding-top: 15px;"><b>R.U.C: </b> {{ $xml->infoTributaria->ruc }}</td>
                    </tr>
                    <tr>
                        <td><b>
                                NOTA DE CRÉDITO
                            </b></td>
                    </tr>
                    <tr>
                        <td><b>No. </b>
                            {{$xml->infoTributaria->estab}}-{{$xml->infoTributaria->ptoEmi}}-{{$xml->infoTributaria->secuencial}}
                        </td>
                    </tr>
                    <tr>
                        <td class="txt14"><b>NÚMERO AUTORIZACIÓN</b></td>
                    </tr>
                    <tr>
                        <td class="size-claveacceso">{{$xml->infoTributaria->claveAcceso}}</td>
                    </tr>
                    <tr>
                        <td class="txt12"><b class="txt11">FECHA Y HORA DE AUTORIZACION: </b> {{$fechaAutorizacion}}
                            {{$horaAutorizacion}}</td>
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
                        <td>
                            <center><img class="size-codigo-barras"
                                    src="data:image/png;base64,{{DNS1D::getBarcodePNG($xml->infoTributaria->claveAcceso, 'C128')}}"
                                    alt="barcode" /></center>
                        </td>
                    </tr>
                    <tr>
                        <td class="size-claveacceso" style="padding-bottom: 6px;">
                            <center>{{ $xml->infoTributaria->claveAcceso }}</center>
                        </td>
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
                    @if(array_key_exists('contribuyenteEspecial', (array)$xml->infoNotaCredito))
                    <tr>
                        <td class="txt12"><b>Contribuyente Especial Nro: </b>
                            {{$xml->infoNotaCredito->contribuyenteEspecial}}</td>
                    </tr>
                    @endif
                    <tr>
                        <td class="txt12"><b>OBLIGADO A LLEVAR CONTABILIDAD: </b>
                            {{$xml->infoNotaCredito->obligadoContabilidad}}</td>
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
        <tr>
            <td colspan="3"></td>
        </tr>
        <tr>
            <td colspan="3">
                <table class="bordenormal anchoFilaCompleta infoFac infocliente txt12">
                    <tr>
                        <td><b>Razón Social / Nombres y Apellidos: </b>
                            {{ $xml->infoNotaCredito->razonSocialComprador }}</td>
                        <td style="width: 200px;"><b>Identificación: </b>
                            {{ $xml->infoNotaCredito->identificacionComprador }}</td>
                    </tr>
                    <tr>
                        <td><b>Fecha Emisión: </b> {{ $xml->infoNotaCredito->fechaEmision }}</td>
                    </tr>
                    <tr>
                        <td colspan="2" class="linea-nc">
                            <hr>
                        </td>
                    </tr>
                    <tr>
                        <td><b>Comprobante que se modifica </b>
                            <texto style="padding-left: 100px;">@if($xml->infoNotaCredito->codDocModificado == '01')
                                FACTURA @endif {{ $xml->infoNotaCredito->numDocModificado }}</texto>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2"><b>Fecha Emisión (Comprobante a modificar) </b>
                            <texto style="padding-left: 35px;">{{ $xml->infoNotaCredito->fechaEmisionDocSustento }}
                            </texto>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" tyle="padding-bottom: 6px;"><b>Razón de Modificación </b>
                            <texto style="padding-left: 145px;">{{ $xml->infoNotaCredito->motivo }} </texto>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td colspan="3"></td>
        </tr>
        <tr>
            <td colspan="3">
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
                        <td>{{ $detalle->codigoInterno }}</td>
                        <td>{{ $detalle->descripcion }}</td>
                        <td>{{ $detalle->precioUnitario }}</td>
                        <td>{{ $detalle->descuento }}</td>
                        <td>{{ $detalle->precioTotalSinImpuesto }}</td>
                    </tr>
                    @endforeach
                </table>
            </td>
        </tr>
        <tr>
            <td colspan="3">
                <table class="anchoFilaCompleta infoFac2 txt11">
                    <tr>
                        <td VALIGN="TOP" class="infoAdicional">
                            <table class="bordenormal nctotal infoAdicional2">
                                <tr>
                                    <td>
                                        <center><b>Información Adicional</b></center>
                                    </td>
                                </tr>
                                <tr>
                                    <td></td>
                                </tr>
                                @foreach ($xml->infoAdicional->campoAdicional as $adicional)
                                <tr>
                                    <td class="observacion-word"><b>{{$adicional['nombre']}}: </b> {{ $adicional }}</td>
                                </tr>
                                @endforeach
                            </table>
                        </td>
                        <td VALIGN="TOP">
                            <table id="tabladetalle" class="nctotal datosTotalFac">
                                <tr>
                                    <td><b>Subtotal</b></td>
                                    <td class="datosFilaToto">{{ $xml->infoNotaCredito->totalSinImpuestos }}</td>
                                </tr>
                                <tr>
                                    <td><b>IVA 12% </b></td>
                                    <td>{{ $xml->infoNotaCredito->totalConImpuestos->totalImpuesto->valor }}</td>
                                </tr>
                                <tr>
                                    <td><b>Valor Total </b></td>
                                    <td>{{ $xml->infoNotaCredito->valorModificacion }}</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>