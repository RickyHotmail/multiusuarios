@extends ('admin.layouts.formatoPDF')
@section('contenido')
    @section('titulo')
        <tr><td colspan="2" class="centrar letra15 negrita">REPORTE TRIBUTARIO</td></tr>
        <tr><td colspan="2" class="centrar letra15 borde-gris">DEL {{ $desde }}  AL {{ $hasta }}</td></tr>
    @endsection
    <br>
    <table style="white-space: normal!important;">           
            <tbody>
            @if(isset($datos))
                <tr><td colspan="7" style="text-align:center;background: #FF8747; font-size: 15px;"><b>VENTAS</b></td></tr>
                <tr>
                    <td style="background: #3755B0; color: #FFFFFF; font-size: 12px;">DETALLE</td>
                    <td style="text-align:center;background: #3755B0; color: #FFFFFF;font-size: 12px;">TIPO</td>
                    <td style="text-align:center;background: #3755B0; color: #FFFFFF;font-size: 12px;">CASILLERO</td>
                    <td style="text-align:center;background: #3755B0; color: #FFFFFF;font-size: 12px;">VENTAS BRUTAS</td>
                    <td style="text-align:center;background: #3755B0; color: #FFFFFF;font-size: 12px;">NC EN VENTAS</td>
                    <td style="text-align:center;background: #3755B0; color: #FFFFFF;font-size: 12px;">VENTAS  NETAS</td>
                    <td style="text-align:center;background: #3755B0; color: #FFFFFF;font-size: 12px;">IMP GENERADO IVA</td>
                </tr>
                @if(count($datos[0]) > 0)
                    <tr><td colspan="7" style="background: #E9D65B; font-size: 12px;"><b>VENTAS CON IVA 12%</b></td></tr>
                    @for ($i = 0; $i < count($datos[0]); ++$i)
                        @if(round($datos[0][$i]['compraBruta'], 2) > 0 or $i > 0)
                        <tr>
                            <td style="white-space: pre-wrap; font-size: 12px;">{{ $datos[0][$i]['sustento'] }}</td>
                            <td style="text-align:center; font-size: 12px;">{{ $datos[0][$i]['porcentaje'] }}</td>
                            <td style="text-align:center; font-size: 12px; background: #8BBDC7;">{{ $datos[0][$i]['casillero'] }}</td>
                            <td style="text-align:center; font-size: 12px;">{{ '$ '.number_format($datos[0][$i]['compraBruta'],2) }}</td>
                            <td style="text-align:center; font-size: 12px;">{{ '$ '.number_format($datos[0][$i]['nc'],2) }}</td>
                            <td style="text-align:center; font-size: 12px;">{{ '$ '.number_format($datos[0][$i]['compraNeta'],2) }}</td>
                            <td style="text-align:center; font-size: 12px;">{{ '$ '.number_format($datos[0][$i]['iva'],2) }}</td>
                        </tr>
                        @endif
                    @endfor
                @endif
                @if(count($datos[1]) > 0)
                    <tr><td colspan="7" style="background: #E9D65B; font-size: 12px;"><b>VENTAS CON IVA 0%</b></td></tr>
                    @for ($i = 0; $i < count($datos[1]); ++$i)
                        @if(round($datos[1][$i]['compraBruta'], 2) > 0 or $i > 0)
                        <tr>
                            <td style="white-space: pre-wrap; font-size: 12px;">{{ $datos[1][$i]['sustento'] }}</td>
                            <td style="text-align:center; font-size: 12px;">{{ $datos[1][$i]['porcentaje'] }}</td>
                            <td style="text-align:center; font-size: 12px; background: #8BBDC7;">{{ $datos[1][$i]['casillero'] }}</td>
                            <td style="text-align:center; font-size: 12px;">{{ '$ '.number_format($datos[1][$i]['compraBruta'],2) }}</td>
                            <td style="text-align:center; font-size: 12px;">{{ '$ '.number_format($datos[1][$i]['nc'],2) }}</td>
                            <td style="text-align:center; font-size: 12px;">{{ '$ '.number_format($datos[1][$i]['compraNeta'],2) }}</td>
                            <td style="text-align:center; font-size: 12px;">{{ '$ '.number_format($datos[1][$i]['iva'],2) }}</td>
                        </tr>
                        @endif
                    @endfor
                @endif
                @if(count($datos[2]) > 0)
                    <tr>
                        <td colspan="3" style="white-space: pre-wrap; background: #3755B0; color: #FFFFFF;font-size: 12px;">TOTAL DE VENTAS Y OTRAS OPERACIONES</td>
                        <td style="text-align:center;background: #3755B0; color: #FFFFFF;font-size: 12px;">{{ '$ '.number_format($datos[2][1]['compraBruta'],2) }}</td>
                        <td style="text-align:center;background: #3755B0; color: #FFFFFF;font-size: 12px;">{{ '$ '.number_format($datos[2][1]['nc'],2) }}</td>
                        <td style="text-align:center;background: #3755B0; color: #FFFFFF;font-size: 12px;">{{ '$ '.number_format($datos[2][1]['compraNeta'],2) }}</td>
                        <td style="text-align:center;background: #3755B0; color: #FFFFFF;font-size: 12px;">{{ '$ '.number_format($datos[2][1]['iva'],2) }}</td>
                    </tr>
                @endif
                @if(count($datos[3]) > 0)
                    @for ($i = 1; $i <= count($datos[3]); ++$i)
                    <tr>
                        <td style="white-space: pre-wrap;font-size: 12px;">{{ $datos[3][$i]['sustento'] }}</td>
                        <td style="text-align:center;font-size: 12px;">{{ $datos[3][$i]['porcentaje'] }}</td>
                        <td style="text-align:center;font-size: 12px;">{{ $datos[3][$i]['casillero'] }}</td>
                        <td style="text-align:center;font-size: 12px;">{{ '$ '.number_format($datos[3][$i]['compraBruta'],2) }}</td>
                        <td style="text-align:center;font-size: 12px;">{{ '$ '.number_format($datos[3][$i]['nc'],2) }}</td>
                        <td style="text-align:center;font-size: 12px;">{{ '$ '.number_format($datos[3][$i]['compraNeta'],2) }}</td>
                        <td style="text-align:center;font-size: 12px;">{{ '$ '.number_format($datos[3][$i]['iva'],2) }}</td>
                    </tr>
                    @endfor
                @endif
                @if(count($datos[4]) > 0)
                    <tr>
                        <td colspan="2" style="white-space: pre-wrap; background: #3755B0; color: #FFFFFF;font-size: 12px;">TOTAL Ingresos por reembolso como intermediario</td>
                        <td style="text-align:center;background: #3755B0; color: #FFFFFF;font-size: 12px;">434</td>
                        <td style="text-align:center;background: #3755B0; color: #FFFFFF;font-size: 12px;">{{ '$ '.number_format($datos[4][1]['compraBruta'],2) }}</td>
                        <td style="text-align:center;background: #3755B0; color: #FFFFFF;font-size: 12px;">{{ '$ '.number_format($datos[4][1]['nc'],2) }}</td>
                        <td style="text-align:center;background: #3755B0; color: #FFFFFF;font-size: 12px;">{{ '$ '.number_format($datos[4][1]['compraNeta'],2) }}</td>
                        <td style="text-align:center;background: #3755B0; color: #FFFFFF;font-size: 12px;">{{ '$ '.number_format($datos[4][1]['iva'],2) }}</td>
                    </tr>
                @endif
                @if(count($datos[20]) > 0)
                    <tr>
                        <td colspan="3" style="white-space: pre-wrap; background: #3755B0; color: #FFFFFF;font-size: 12px;">TOTAL DE VENTAS</td>
                        <td style="text-align:center;background: #3755B0; color: #FFFFFF;font-size: 12px;">{{ '$ '.number_format($datos[20][1]['compraBruta'],2) }}</td>
                        <td style="text-align:center;background: #3755B0; color: #FFFFFF;font-size: 12px;">{{ '$ '.number_format($datos[20][1]['nc'],2) }}</td>
                        <td style="text-align:center;background: #3755B0; color: #FFFFFF;font-size: 12px;">{{ '$ '.number_format($datos[20][1]['compraNeta'],2) }}</td>
                        <td style="text-align:center;background: #3755B0; color: #FFFFFF;font-size: 12px;">{{ '$ '.number_format($datos[20][1]['iva'],2) }}</td>
                    </tr>
                @endif
                @if(count($datos[21]) > 0)
                    <tr>
                        <td colspan="2" style="white-space: pre-wrap; background: #3755B0; color: #FFFFFF;font-size: 12px;">Impuesto a liquidar en este mes</td>
                        <td style="text-align:center;white-space: pre-wrap; background: #3755B0; color: #FFFFFF;font-size: 12px;">499</td>
                        <td colspan="3" style="white-space: pre-wrap; background: #3755B0; color: #FFFFFF;font-size: 12px;"></td>
                        <td style="text-align:center;background: #3755B0; color: #FFFFFF;font-size: 12px;">{{ '$ '.number_format($datos[21][1]['iva'],2) }}</td>
                    </tr>
                @endif
                <tr>
                    <td colspan="1" style="white-space: pre-wrap; background: #FFF; color: #000;font-size: 12px;">Total de Comprobantes de venta emitidas</td>
                    <td class="centrar-texto" style="background: #FFF; color: #000;font-size: 12px;">{{ $cantidad_venta }}</td>
                
                    <td colspan="4" style="white-space: pre-wrap; background: #FFF; color: #000;font-size: 12px;">Total de Comprobantes de venta Anuladas</td>
                    <td class="centrar-texto" style="background: #FFF; color: #000;font-size: 12px;">{{ $cantidad_venta_anulada }}</td>
                </tr>
                <tr><td colspan="7" style="text-align:center;background: #FF8747; font-size: 15px;"><b>COMPRAS</b></td></tr>
                <tr>
                    <td style="background: #3755B0; color: #FFFFFF;font-size: 12px;">DETALLE</td>
                    <td style="text-align:center;background: #3755B0; color: #FFFFFF;font-size: 12px;">TIPO</td>
                    <td style="text-align:center;background: #3755B0; color: #FFFFFF;font-size: 12px;">CASILLERO</td>
                    <td style="text-align:center;background: #3755B0; color: #FFFFFF;font-size: 12px;">COMPRAS BRUTAS</td>
                    <td style="text-align:center;background: #3755B0; color: #FFFFFF;font-size: 12px;">NC EN COMPRAS</td>
                    <td style="text-align:center;background: #3755B0; color: #FFFFFF;font-size: 12px;">COMPRAS NETAS</td>
                    <td style="text-align:center;background: #3755B0; color: #FFFFFF;font-size: 12px;">IMP GENERADO IVA</td>
                </tr>
                @if(count($datos[5]) > 0)
                    <tr><td colspan="7" style="background: #E9D65B; font-size: 12px;"><b>COMPRAS CON IVA 12%</b></td></tr>
                    @for ($i = 1; $i <= count($datos[5]); ++$i)
                    <tr>
                        <td style="white-space: pre-wrap;font-size: 12px;">{{ $datos[5][$i]['sustento'] }}</td>
                        <td style="text-align:center;font-size: 12px;">{{ $datos[5][$i]['porcentaje'] }}</td>
                        <td style="text-align:center;font-size: 12px; background: #8BBDC7;">{{ $datos[5][$i]['casillero'] }}</td>
                        <td style="text-align:center;font-size: 12px;">{{ '$ '.number_format($datos[5][$i]['compraBruta'],2) }}</td>
                        <td style="text-align:center;font-size: 12px;">{{ '$ '.number_format($datos[5][$i]['nc'],2) }}</td>
                        <td style="text-align:center;font-size: 12px;">{{ '$ '.number_format($datos[5][$i]['compraNeta'],2) }}</td>
                        <td style="text-align:center;font-size: 12px;">{{ '$ '.number_format($datos[5][$i]['iva'],2) }}</td>
                    </tr>
                    @endfor
                @endif
                @if(count($datos[6]) > 0)
                    <tr><td colspan="7" style="background: #E9D65B; font-size: 12px;"><b>COMPRAS CON IVA 0%</b></td></tr>
                    @for ($i = 1; $i <= count($datos[6]); ++$i)
                    <tr>
                        <td style="white-space: pre-wrap;font-size: 12px;">{{ $datos[6][$i]['sustento'] }}</td>
                        <td style="text-align:center;font-size: 12px;">{{ $datos[6][$i]['porcentaje'] }}</td>
                        <td style="text-align:center;font-size: 12px; background: #8BBDC7;">{{ $datos[6][$i]['casillero'] }}</td>
                        <td style="text-align:center;font-size: 12px;">{{ '$ '.number_format($datos[6][$i]['compraBruta'],2) }}</td>
                        <td style="text-align:center;font-size: 12px;">{{ '$ '.number_format($datos[6][$i]['nc'],2) }}</td>
                        <td style="text-align:center;font-size: 12px;">{{ '$ '.number_format($datos[6][$i]['compraNeta'],2) }}</td>
                        <td style="text-align:center;font-size: 12px;">{{ '$ '.number_format($datos[6][$i]['iva'],2) }}</td>
                    </tr>
                    @endfor
                @endif
                @if(count($datos[7]) > 0)
                    <tr>
                        <td colspan="3" style="white-space: pre-wrap; background: #3755B0; color: #FFFFFF;font-size: 12px;">TOTAL ADQUISICIONES Y PAGOS</td>
                        <td style="text-align:center;background: #3755B0; color: #FFFFFF;font-size: 12px;">{{ '$ '.number_format($datos[7][1]['compraBruta'],2) }}</td>
                        <td style="text-align:center;background: #3755B0; color: #FFFFFF;font-size: 12px;">{{ '$ '.number_format($datos[7][1]['nc'],2) }}</td>
                        <td style="text-align:center;background: #3755B0; color: #FFFFFF;font-size: 12px;">{{ '$ '.number_format($datos[7][1]['compraNeta'],2) }}</td>
                        <td style="text-align:center;background: #3755B0; color: #FFFFFF;font-size: 12px;">{{ '$ '.number_format($datos[7][1]['iva'],2) }}</td>
                    </tr>
                @endif
                @if(count($datos[8]) > 0)
                    @for ($i = 1; $i <= count($datos[8]); ++$i)
                    <tr>
                        <td style="white-space: pre-wrap;font-size: 12px;">{{ $datos[8][$i]['sustento'] }}</td>
                        <td style="text-align:center;font-size: 12px;">{{ $datos[8][$i]['porcentaje'] }}</td>
                        <td style="text-align:center;font-size: 12px;">{{ $datos[8][$i]['casillero'] }}</td>
                        <td style="text-align:center;font-size: 12px;">{{ '$ '.number_format($datos[8][$i]['compraBruta'],2) }}</td>
                        <td style="text-align:center;font-size: 12px;">{{ '$ '.number_format($datos[8][$i]['nc'],2) }}</td>
                        <td style="text-align:center;font-size: 12px;">{{ '$ '.number_format($datos[8][$i]['compraNeta'],2) }}</td>
                        <td style="text-align:center;font-size: 12px;">{{ '$ '.number_format($datos[8][$i]['iva'],2) }}</td>
                    </tr>
                    @endfor
                @endif
                @if(count($datos[9]) > 0)
                    <tr>
                        <td colspan="2" style="white-space: pre-wrap; background: #3755B0; color: #FFFFFF;font-size: 12px;">TOTAL pagos por reembolso como intermediario</td>
                        <td style="text-align:center;background: #3755B0; color: #FFFFFF;font-size: 12px;">534</td>
                        <td style="text-align:center;background: #3755B0; color: #FFFFFF;font-size: 12px;">{{ '$ '.number_format($datos[9][1]['compraBruta'],2) }}</td>
                        <td style="text-align:center;background: #3755B0; color: #FFFFFF;font-size: 12px;">{{ '$ '.number_format($datos[9][1]['nc'],2) }}</td>
                        <td style="text-align:center;background: #3755B0; color: #FFFFFF;font-size: 12px;">{{ '$ '.number_format($datos[9][1]['compraNeta'],2) }}</td>
                        <td style="text-align:center;background: #3755B0; color: #FFFFFF;font-size: 12px;">{{ '$ '.number_format($datos[9][1]['iva'],2) }}</td>
                    </tr>
                @endif
                @if(count($datos[10]) > 0)
                    <tr>
                        <td colspan="3" style="white-space: pre-wrap; background: #3755B0; color: #FFFFFF;font-size: 12px;">TOTAL DE COMPRAS</td>
                        <td style="text-align:center;background: #3755B0; color: #FFFFFF;font-size: 12px;">{{ '$ '.number_format($datos[10][1]['compraBruta'],2) }}</td>
                        <td style="text-align:center;background: #3755B0; color: #FFFFFF;font-size: 12px;">{{ '$ '.number_format($datos[10][1]['nc'],2) }}</td>
                        <td style="text-align:center;background: #3755B0; color: #FFFFFF;font-size: 12px;">{{ '$ '.number_format($datos[10][1]['compraNeta'],2) }}</td>
                        <td style="text-align:center;background: #3755B0; color: #FFFFFF;font-size: 12px;">{{ '$ '.number_format($datos[10][1]['iva'],2) }}</td>
                    </tr>
                @endif
                @if(count($datos[11]) > 0)
                <tr>
                    <td style="white-space: pre-wrap; background: #E9D65B; font-size: 12px;"><b>Crédito tributario aplicable en este período de acuerdo al :</b></td>
                    <td style="text-align:center;background: #E9D65B; font-size: 15px;"><b>factor de proporcionalidad</b></td>
                    <td style="text-align:center;background: #E9D65B; font-size: 15px;"></td>
                    <td style="text-align:center;background: #E9D65B; font-size: 15px;"><b>o a su contabilidad</b></td>
                    <td style="text-align:center;background: #E9D65B; font-size: 15px;"><b>X</b></td>
                    <td style="text-align:center;background: #E9D65B; font-size: 15px;"></td>
                    <td style="text-align:center;background: #E9D65B; font-size: 15px;"><b>{{ '$ '.number_format($datos[11][1]['iva'],2) }}</b></td>
                </tr>
                @endif
                <tr>
                    <td colspan="1" style="white-space: pre-wrap; background: #FFF; color: #000; font-size: 12px;">Total de Comprobantes de Venta Recibidas</td>
                    <td class="centrar-texto" style="background: #FFF; color: #000; font-size: 12px;">{{ $cantidad_compra }}</td>

                    <td colspan="4" style="white-space: pre-wrap; background: #FFF; color: #000; font-size: 12px;">Total de Notas de Venta Recibidas</td>
                    <td class="centrar-texto" style="background: #FFF; color: #000; font-size: 12px;">{{ $cantidad_compra_boleta }}</td>
                </tr>
                <tr><td colspan="7" style="text-align:center;background: #FF8747; font-size: 15px;"><b>RESUMEN IMPOSITIVO: AGENTE DE PERCEPCIÓN DEL IMPUESTO AL VALOR AGREGADO</b></td></tr>
                        <tr>
                            <td style="white-space: pre-wrap;font-size: 12px;">Impuesto causado (si la diferencia de los campos 499-564 es mayor que cero)</td>
                            <td style="text-align:center;font-size: 12px;"></td>
                            <td style="text-align:center;background: #8BBDC7;font-size: 12px;">601</td>
                            <td style="text-align:center;font-size: 12px;"></td>
                            <td style="text-align:center;font-size: 12px;"></td>
                            <td style="text-align:center;font-size: 12px;"></td>
                            <td style="text-align:center;font-size: 12px;">{{'$ '.number_format($datos[22],2)}}</td>
                        </tr>
                        <tr>
                            <td style="white-space: pre-wrap;font-size: 12px;">Crédito tributario aplicable en este período (si la diferencia de los campos 499-564 es menor que cero)</td>
                            <td style="text-align:center;font-size: 12px;"></td>
                            <td style="text-align:center;font-size: 12px;background: #8BBDC7;">602</td>
                            <td style="text-align:center;font-size: 12px;"></td>
                            <td style="text-align:center;font-size: 12px;"></td>
                            <td style="text-align:center;font-size: 12px;"></td>
                            <td style="text-align:center;font-size: 12px;">{{'$ '.number_format($datos[23],2)}}</td>
                        </tr>
                        <tr>
                            <td style="white-space: pre-wrap;font-size: 12px;">(-) Saldo crédito tributario del mes anterior Por adquisiciones e importaciones (trasládese el campo 615 de la declaración del período anterior)</td>
                            <td style="text-align:center;font-size: 12px;"></td>
                            <td style="text-align:center;font-size: 12px;background: #8BBDC7;">605</td>
                            <td style="text-align:center;font-size: 12px;"></td>
                            <td style="text-align:center;font-size: 12px;"></td>
                            <td style="text-align:center;font-size: 12px;"></td>
                            <td style="text-align:center;font-size: 12px;">{{'$ '.number_format($valor1,2)}}</td>
                        </tr>
                        <tr>
                            <td style="white-space: pre-wrap;font-size: 12px;">(-) Saldo crédito tributario del mes anterior Por retenciones en la fuente de IVA que le han sido efectuadas (trasládese el campo 617 de la declaración del período anterior)</td>
                            <td style="text-align:center;font-size: 12px;"></td>
                            <td style="text-align:center;font-size: 12px;background: #8BBDC7;">606</td>
                            <td style="text-align:center;font-size: 12px;"></td>
                            <td style="text-align:center;font-size: 12px;"></td>
                            <td style="text-align:center;font-size: 12px;"></td>
                            <td style="text-align:center;font-size: 12px;">{{'$ '.number_format($valor2,2)}}</td>
                        </tr>
                        <tr>
                            <td style="white-space: pre-wrap;font-size: 12px;">(-) Retenciones en la fuente de IVA que le han sido efectuadas en este período</td>
                            <td style="text-align:center;font-size: 12px;"></td>
                            <td style="text-align:center;font-size: 12px;background: #8BBDC7;">609</td>
                            <td style="text-align:center;font-size: 12px;"></td>
                            <td style="text-align:center;font-size: 12px;"></td>
                            <td style="text-align:center;font-size: 12px;"></td>
                            <td style="text-align:center;font-size: 12px;">{{'$ '.number_format($valor3,2)}}</td>
                        </tr>
                        <tr>
                            <td style="white-space: pre-wrap;font-size: 12px;">(+) Ajuste por IVA devuelto e IVA rechazado (por concepto de devoluciones de IVA), ajuste de IVA por procesos de control y otros (adquisiciones en importaciones), imputables al crédito tributario</td>
                            <td style="text-align:center;font-size: 12px;"></td>
                            <td style="text-align:center;font-size: 12px;background: #8BBDC7;">612</td>
                            <td style="text-align:center;font-size: 12px;"></td>
                            <td style="text-align:center;font-size: 12px;"></td>
                            <td style="text-align:center;font-size: 12px;"></td>
                            <td style="text-align:center;font-size: 12px;">{{'$ '.number_format($valor4,2)}}</td>
                        </tr>
                        <tr>
                            <td style="white-space: pre-wrap;font-size: 12px;">Saldo crédito tributario para el próximo mes Por adquisiciones e importaciones</td>
                            <td style="text-align:center;font-size: 12px;"></td>
                            <td style="text-align:center;font-size: 12px;background: #8BBDC7;">615</td>
                            <td style="text-align:center;font-size: 12px;"></td>
                            <td style="text-align:center;font-size: 12px;"></td>
                            <td style="text-align:center;font-size: 12px;"></td>
                            <td style="text-align:center;font-size: 12px;">{{'$ '.number_format($valor5,2)}}</td>
                        </tr>
                        <tr>
                            <td style="white-space: pre-wrap;font-size: 12px;">Saldo crédito tributario para el próximo mes Por retenciones en la fuente de IVA que le han sido efectuadas</td>
                            <td style="text-align:center;font-size: 12px;"></td>
                            <td style="text-align:center;font-size: 12px;background: #8BBDC7;">617</td>
                            <td style="text-align:center;font-size: 12px;"></td>
                            <td style="text-align:center;font-size: 12px;"></td>
                            <td style="text-align:center;font-size: 12px;"></td>
                            <td style="text-align:center;font-size: 12px;">{{'$ '.number_format($valor6,2)}}</td>
                        </tr>
                @if(count($datos[12]) > 0)
                    <tr><td colspan="7" style="text-align:center;background: #FF8747; font-size: 15px;"><b>AGENTE DE RETENCIÓN DEL IMPUESTO AL VALOR AGREGADO</b></td></tr>
                    <tr>
                        <td style="background: #3755B0; color: #FFFFFF;font-size: 12px;">Concepto de Retención</td>
                        <td style="text-align:center;background: #3755B0; color: #FFFFFF;font-size: 12px;">No. Registros</td>
                        <td style="text-align:center;background: #3755B0; color: #FFFFFF;font-size: 12px;">Cod.</td>
                        <td style="text-align:center;background: #3755B0; color: #FFFFFF;font-size: 12px;" colspan="2"></td>
                        <td style="text-align:center;background: #3755B0; color: #FFFFFF;font-size: 12px;">Base Imponible</td>
                        <td style="text-align:center;background: #3755B0; color: #FFFFFF;font-size: 12px;">Valor Retenido</td>
                    </tr>
                    @for ($i = 1; $i <= count($datos[12]); ++$i)
                    <tr>
                        <td style="white-space: pre-wrap;font-size: 12px;">{{ $datos[12][$i]['nombre'] }}</td>
                        <td style="text-align:center;font-size: 12px;">{{ $datos[12][$i]['cantidad'] }}</td>
                        <td style="text-align:center;font-size: 12px;">{{ $datos[12][$i]['codigo'] }}</td>
                        <td colspan="2"></td>
                        <td style="text-align:center;font-size: 12px;">{{ '$ '.number_format($datos[12][$i]['base'],2) }}</td>
                        <td style="text-align:center;font-size: 12px;">{{ '$ '.number_format($datos[12][$i]['valor'],2) }}</td>
                    </tr>
                    @endfor
                    <tr>
                        <td colspan="5" class="derecha-texto" style="background: #3755B0; color: #FFFFFF;font-size: 12px;">TOTAL</td>
                        <td style="text-align:center;background: #3755B0; color: #FFFFFF;font-size: 12px;">{{ '$ '.number_format($datos[13][1]['base'],2) }}</td>
                        <td style="text-align:center;background: #3755B0; color: #FFFFFF;font-size: 12px;">{{ '$ '.number_format($datos[13][1]['valor'],2) }}</td>
                    </tr>
                @endif
                @if(count($datos[14]) > 0)
                    <tr><td colspan="7" style="text-align:center;background: #FF8747; font-size: 15px;"><b>AGENTE DE RETENCIÓN DEL IMPUESTO A LA RENTA</b></td></tr>
                    <tr>
                        <td style="background: #3755B0; color: #FFFFFF;font-size: 12px;">Concepto de Retención</td>
                        <td style="text-align:center;background: #3755B0; color: #FFFFFF;font-size: 12px;">No. Registros</td>
                        <td style="text-align:center;background: #3755B0; color: #FFFFFF;font-size: 12px;">Cod.</td>
                        <td style="text-align:center;background: #3755B0; color: #FFFFFF;font-size: 12px;" colspan="2"></td>
                        <td style="text-align:center;background: #3755B0; color: #FFFFFF;font-size: 12px;">Base Imponible</td>
                        <td style="text-align:center;background: #3755B0; color: #FFFFFF;font-size: 12px;">Valor Retenido</td>
                    </tr>
                    <tr>
                        <td  style="white-space: pre-wrap;font-size: 12px;">En relación de dependencia que supera o nó la base desgravada</td>
                        <td class="centrar-texto"></td>
                        <td  style="text-align:center;font-size: 12px;">302</td>
                        <td colspan="2"></td>
                        <td  style="text-align:center;font-size: 12px;">{{ '$ '.number_format($base_imponible,2) }}</td>
                        <td  style="text-align:center;font-size: 12px;">{{ '$ '.number_format($valor_retenido,2) }}</td>
                    </tr>
                    @for ($i = 1; $i <= count($datos[14]); ++$i)
                    <tr>
                        <td style="white-space: pre-wrap;font-size: 12px;">{{ $datos[14][$i]['nombre'] }}</td>
                        <td style="text-align:center;font-size: 12px;">{{ $datos[14][$i]['cantidad'] }}</td>
                        <td style="text-align:center;font-size: 12px;">{{ $datos[14][$i]['codigo'] }}</td>
                        <td colspan="2"></td>
                        <td style="text-align:center;font-size: 12px;">{{ '$ '.number_format($datos[14][$i]['base'],2) }}</td>
                        <td style="text-align:center;font-size: 12px;">{{ '$ '.number_format($datos[14][$i]['valor'],2) }}</td>
                    </tr>
                    @endfor
                    <tr>
                        <td colspan="5" class="derecha-texto" style="background: #3755B0; color: #FFFFFF;font-size: 12px;">TOTAL</td>
                        <td style="text-align:center;background: #3755B0; color: #FFFFFF;font-size: 12px;">{{ '$ '.number_format($datos[15][1]['base'],2) }}</td>
                        <td style="text-align:center;background: #3755B0; color: #FFFFFF;font-size: 12px;">{{ '$ '.number_format($datos[15][1]['valor'],2) }}</td>
                    </tr>
                @endif
                
                @if(count($datos[16]) > 0)
                    <tr><td colspan="7" style="text-align:center;background: #FF8747; font-size: 15px;"><b>AGENTE DE RETENCIÓN DEL IMPUESTO AL VALOR AGREGADO (RECIBIDO)</b></td></tr>
                    <tr>
                        <td style="background: #3755B0; color: #FFFFFF;font-size: 12px;">Concepto de Retención</td>
                        <td style="text-align:center;background: #3755B0; color: #FFFFFF;font-size: 12px;">No. Registros</td>
                        <td style="text-align:center;background: #3755B0; color: #FFFFFF;font-size: 12px;">Cod.</td>
                        <td style="text-align:center;background: #3755B0; color: #FFFFFF;font-size: 12px;" colspan="2"></td>
                        <td style="text-align:center;background: #3755B0; color: #FFFFFF;font-size: 12px;">Base Imponible</td>
                        <td style="text-align:center;background: #3755B0; color: #FFFFFF;font-size: 12px;">Valor Retenido</td>
                    </tr>
                    @for ($i = 1; $i <= count($datos[16]); ++$i)
                    <tr>
                        <td style="white-space: pre-wrap;font-size: 12px;">{{ $datos[16][$i]['nombre'] }}</td>
                        <td style="text-align:center;font-size: 12px;">{{ $datos[16][$i]['cantidad'] }}</td>
                        <td style="text-align:center;font-size: 12px;">{{ $datos[16][$i]['codigo'] }}</td>
                        <td colspan="2"></td>
                        <td style="text-align:center;font-size: 12px;">{{ '$ '.number_format($datos[16][$i]['base'],2) }}</td>
                        <td style="text-align:center;font-size: 12px;">{{ '$ '.number_format($datos[16][$i]['valor'],2) }}</td>
                    </tr>
                    @endfor
                    <tr>
                        <td colspan="5" class="derecha-texto" style="background: #3755B0; color: #FFFFFF;font-size: 12px;">TOTAL</td>
                        <td style="text-align:center;background: #3755B0; color: #FFFFFF;font-size: 12px;">{{ '$ '.number_format($datos[17][1]['base'],2) }}</td>
                        <td style="text-align:center;background: #3755B0; color: #FFFFFF;font-size: 12px;">{{ '$ '.number_format($datos[17][1]['valor'],2) }}</td>
                    </tr>
                @endif
                @if(count($datos[18]) > 0)
                    <tr><td colspan="7" style="text-align:center;background: #FF8747; font-size: 15px;"><b>AGENTE DE RETENCIÓN DEL IMPUESTO A LA RENTA (RECIBIDO)</b></td></tr>
                    <tr>
                        <td style="background: #3755B0; color: #FFFFFF;font-size: 12px;">Concepto de Retención</td>
                        <td style="text-align:center;background: #3755B0; color: #FFFFFF;font-size: 12px;">No. Registros</td>
                        <td style="text-align:center;background: #3755B0; color: #FFFFFF;font-size: 12px;">Cod.</td>
                        <td style="text-align:center;background: #3755B0; color: #FFFFFF;font-size: 12px;" colspan="2"></td>
                        <td style="text-align:center;background: #3755B0; color: #FFFFFF;font-size: 12px;">Base Imponible</td>
                        <td style="text-align:center;background: #3755B0; color: #FFFFFF;font-size: 12px;">Valor Retenido</td>
                    </tr>
                    @for ($i = 1; $i <= count($datos[18]); ++$i)
                    <tr>
                        <td style="white-space: pre-wrap;font-size: 12px;">{{ $datos[18][$i]['nombre'] }}</td>
                        <td style="text-align:center;font-size: 12px;">{{ $datos[18][$i]['cantidad'] }}</td>
                        <td style="text-align:center;font-size: 12px;">{{ $datos[18][$i]['codigo'] }}</td>
                        <td colspan="2"></td>
                        <td style="text-align:center;font-size: 12px;">{{ '$ '.number_format($datos[18][$i]['base'],2) }}</td>
                        <td style="text-align:center;font-size: 12px;">{{ '$ '.number_format($datos[18][$i]['valor'],2) }}</td>
                    </tr>
                    @endfor
                    <tr>
                        <td colspan="5" class="derecha-texto" style="background: #3755B0; color: #FFFFFF;font-size: 12px;">TOTAL</td>
                        <td style="text-align:center;background: #3755B0; color: #FFFFFF;font-size: 12px;">{{ '$ '.number_format($datos[19][1]['base'],2) }}</td>
                        <td style="text-align:center;background: #3755B0; color: #FFFFFF;font-size: 12px;">{{ '$ '.number_format($datos[19][1]['valor'],2) }}</td>
                    </tr>
                @endif
            @endif               
            </tbody>
        </table>
        <table style="padding-top: 90px;">
        <tr class="letra12">
            <td class="centrar" style="border-top: 1px solid black; width: 20%;white-space: pre-wrap;">Elaborado por: <br> {{ Auth::user()->user_nombre }}</td>
            <td style="padding-right: 15px; padding-left: 15px;"></td>
            <td class="centrar" style="border-top: 1px solid black;width: 20%;">GERENTE:</td>
            <td style="padding-right: 15px; padding-left: 15px;"></td>
            <td class="centrar" style="border-top: 1px solid black;width: 20%;">CONTADOR:</td>
        </tr>
    </table>
@endsection