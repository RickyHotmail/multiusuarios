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
                                COMPROBANTE DE RETENCIÓN
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
                    @if(array_key_exists('contribuyenteEspecial', (array)$xml->infoCompRetencion))
                    <tr>
                        <td class="txt12"><b>Contribuyente Especial Nro: </b>
                            {{$xml->infoCompRetencion->contribuyenteEspecial}}</td>
                    </tr>
                    @endif
                    <tr>
                        <td class="txt12"><b>OBLIGADO A LLEVAR CONTABILIDAD: </b>
                            {{$xml->infoCompRetencion->obligadoContabilidad}}</td>
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
                            {{ $xml->infoCompRetencion->razonSocialSujetoRetenido }}</td>
                        <td style="width: 200px;"><b>Identificación: </b>
                            {{ $xml->infoCompRetencion->identificacionSujetoRetenido }}</td>
                    </tr>
                    <tr>
                        <td><b>Fecha Emisión: </b> {{ $xml->infoCompRetencion->fechaEmision }}</td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td colspan="3"></td>
        </tr>
        <tr>
            <td colspan="3">
                <table id="tabladetalle" class="anchoFilaCompleta infoFac2 txt11 infoRet">
                    <tr>
                        <td><b>Comprobante</b></td>
                        <td><b>Numero</b></td>
                        <td><b>Fecha Emisión</b></td>
                        <td><b>Ejercicio Fiscal</b></td>
                        <td><b>Base imponible para la retención</b></td>
                        <td><b>Código de Retención</b></td>
                        <td><b>IMPUESTO</b></td>
                        <td><b>Porcentaje Retenido</b></td>
                        <td><b>Valor Retenido</b></td>
                    </tr>
                    <?php $total = 0; ?>
                    @foreach($xml->impuestos->impuesto as $impuesto)
                    <?php $total = (double)$total + (double)$impuesto->valorRetenido; ?>
                    <tr>
                        <td>@if($impuesto->codDocSustento == '01') FACTURA @endif @if($impuesto->codDocSustento == '19')
                            COMPROBANTES DE PAGO DE CUOTAS O APORTES @endif @if($impuesto->codDocSustento == '03')
                            LIQUIDACIÓN DE COMPRA @endif </td>
                        <td>{{ $impuesto->numDocSustento }}</td>
                        <td>{{ $impuesto->fechaEmisionDocSustento }}</td>
                        <td>{{ $xml->infoCompRetencion->periodoFiscal }}</td>
                        <td>{{ $impuesto->baseImponible }}</td>
                        <td>@if($impuesto->codigoRetencion == '1') 725 @elseif($impuesto->codigoRetencion == '2') 729
                            @elseif($impuesto->codigoRetencion == '3') 731 @elseif($impuesto->codigoRetencion == '11')
                            727 @elseif($impuesto->codigoRetencion == '9') 721 @elseif($impuesto->codigoRetencion ==
                            '10') 723 @else {{ $impuesto->codigoRetencion }} @endif </td>
                        <td>@if($impuesto->codigo == '1') RENTA @endif @if($impuesto->codigo == '2') IVA @endif
                            @if($impuesto->codigo == '6') ISD @endif</td>
                        <td>{{ $impuesto->porcentajeRetener }}</td>
                        <td>{{ $impuesto->valorRetenido }}</td>
                    </tr>
                    @endforeach
                    <tr>
                        <td colspan="8" style="border:none !important;"></td>
                        <td>{{ number_format($total, 2) }}</td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td colspan="3">
                <table class="anchoFilaCompleta infoFac2 txt11">
                    <tr>
                        <td class="infoAdicional">
                            <table class="bordenormal infoAdicional2 infoRet">
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
                        <td>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>