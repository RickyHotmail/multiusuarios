@extends ('admin.layouts.admin')
@section('principal')
<form id="idForm" class="form-horizontal" method="POST" action="{{ url("reporteTributario") }} "> 
@csrf
    <div class="card card-secondary" style="position: absolute; width: 100%">
        <div class="card-header">
            <h3 class="card-title">Reporte Tributario</h3>
        </div>
        <!-- /.card-header -->
        <div class="card-body">
            <div class="form-group row">
                <label for="fecha_desde" class="col-sm-1 col-form-label">Desde :</label>
                <div class="col-sm-3">
                    <input type="date" class="form-control" id="fecha_desde" name="fecha_desde"  value='<?php if(isset($fecI)){echo $fecI;}else{ echo(date("Y")."-".date("m")."-".date("d"));} ?>' required>
                </div>
                <label for="idHasta" class="col-sm-1 col-form-label">Hasta :</label>
                <div class="col-sm-3">
                    <input type="date" class="form-control" id="fecha_hasta" name="fecha_hasta"  value='<?php if(isset($fecF)){echo $fecF;}else{ echo(date("Y")."-".date("m")."-".date("d"));} ?>' required>
                </div>
                <div class="col-sm-2">
                    <button onclick="girarGif()" type="submit" name="consultar" class="btn btn-primary"><i class="fa fa-search"></i></button>
                    <button onclick="girarGif()" type="submit" name="guardar" class="btn btn-success"><i class="fa fa-save"></i></button>
                    <button onclick="setTipo('&pdf=descarga')" type="submit" name="pdf" class="btn btn-secondary"><i class="fa fa-print"></i></button>
                </div>
            </div>            
        
            <hr>
            <table id="example1" class="table table-bordered table-hover table-responsive sin-salto">           
                <tbody>
                @if(isset($datos))
                    <tr><td colspan="7" class="centrar-texto" style="background: #FF8747; font-size: 20px;"><b>VENTAS</b></td></tr>
                    <tr>
                        <td style="background: #3755B0; color: #FFFFFF;">DETALLE</td>
                        <td class="centrar-texto" style="background: #3755B0; color: #FFFFFF;">TIPO</td>
                        <td class="centrar-texto" style="background: #3755B0; color: #FFFFFF;">CASILLERO</td>
                        <td class="centrar-texto" style="background: #3755B0; color: #FFFFFF;">VENTAS BRUTAS</td>
                        <td class="centrar-texto" style="background: #3755B0; color: #FFFFFF;">NC EN VENTAS</td>
                        <td class="centrar-texto" style="background: #3755B0; color: #FFFFFF;">VENTAS  NETAS</td>
                        <td class="centrar-texto" style="background: #3755B0; color: #FFFFFF;">IMP GENERADO IVA</td>
                    </tr>
                    @if(count($datos[0]) > 0)
                        <tr><td colspan="7" style="background: #E9D65B; font-size: 15px;"><b>VENTAS CON IVA 12%</b></td></tr>
                        @for ($i = 0; $i < count($datos[0]); ++$i)
                            @if(round($datos[0][$i]['compraBruta'], 2) > 0 or $i > 0)
                            <tr>
                                <td style="white-space: pre-wrap;">{{ $datos[0][$i]['sustento'] }}</td>
                                <td class="centrar-texto">{{ $datos[0][$i]['porcentaje'] }}</td>
                                <td class="centrar-texto" style="background: #8BBDC7;">{{ $datos[0][$i]['casillero'] }}</td>
                                <td class="centrar-texto">{{ '$ '.number_format($datos[0][$i]['compraBruta'],2) }}</td>
                                <td class="centrar-texto">{{ '$ '.number_format($datos[0][$i]['nc'],2) }}</td>
                                <td class="centrar-texto">{{ '$ '.number_format($datos[0][$i]['compraNeta'],2) }}</td>
                                <td class="centrar-texto">{{ '$ '.number_format($datos[0][$i]['iva'],2) }}</td>
                            </tr>
                            @endif
                        @endfor
                    @endif
                    @if(count($datos[1]) > 0)
                        <tr><td colspan="7" style="background: #E9D65B; font-size: 15px;"><b>VENTAS CON IVA 0%</b></td></tr>
                        @for ($i = 0; $i < count($datos[1]); ++$i)
                            @if(round($datos[1][$i]['compraBruta'], 2) > 0 or $i > 0)
                            <tr>
                                <td style="white-space: pre-wrap;">{{ $datos[1][$i]['sustento'] }}</td>
                                <td class="centrar-texto">{{ $datos[1][$i]['porcentaje'] }}</td>
                                <td class="centrar-texto" style="background: #8BBDC7;">{{ $datos[1][$i]['casillero'] }}</td>
                                <td class="centrar-texto">{{ '$ '.number_format($datos[1][$i]['compraBruta'],2) }}</td>
                                <td class="centrar-texto">{{ '$ '.number_format($datos[1][$i]['nc'],2) }}</td>
                                <td class="centrar-texto">{{ '$ '.number_format($datos[1][$i]['compraNeta'],2) }}</td>
                                <td class="centrar-texto">{{ '$ '.number_format($datos[1][$i]['iva'],2) }}</td>
                            </tr>
                            @endif
                        @endfor
                    @endif
                    @if(count($datos[2]) > 0)
                        <tr>
                            <td colspan="3" style="white-space: pre-wrap; background: #3755B0; color: #FFFFFF;">TOTAL DE VENTAS Y OTRAS OPERACIONES</td>
                            <td class="centrar-texto" style="background: #3755B0; color: #FFFFFF;">{{ '$ '.number_format($datos[2][1]['compraBruta'],2) }}</td>
                            <td class="centrar-texto" style="background: #3755B0; color: #FFFFFF;">{{ '$ '.number_format($datos[2][1]['nc'],2) }}</td>
                            <td class="centrar-texto" style="background: #3755B0; color: #FFFFFF;">{{ '$ '.number_format($datos[2][1]['compraNeta'],2) }}</td>
                            <td class="centrar-texto" style="background: #3755B0; color: #FFFFFF;">{{ '$ '.number_format($datos[2][1]['iva'],2) }}</td>
                        </tr>
                    @endif
                    @if(count($datos[3]) > 0)
                        @for ($i = 1; $i <= count($datos[3]); ++$i)
                        <tr>
                            <td style="white-space: pre-wrap;">{{ $datos[3][$i]['sustento'] }}</td>
                            <td class="centrar-texto">{{ $datos[3][$i]['porcentaje'] }}</td>
                            <td class="centrar-texto">{{ $datos[3][$i]['casillero'] }}</td>
                            <td class="centrar-texto">{{ '$ '.number_format($datos[3][$i]['compraBruta'],2) }}</td>
                            <td class="centrar-texto">{{ '$ '.number_format($datos[3][$i]['nc'],2) }}</td>
                            <td class="centrar-texto">{{ '$ '.number_format($datos[3][$i]['compraNeta'],2) }}</td>
                            <td class="centrar-texto">{{ '$ '.number_format($datos[3][$i]['iva'],2) }}</td>
                        </tr>
                        @endfor
                    @endif
                    @if(count($datos[4]) > 0)
                        <tr>
                            <td colspan="2" style="white-space: pre-wrap; background: #3755B0; color: #FFFFFF;">TOTAL Ingresos por reembolso como intermediario</td>
                            <td class="centrar-texto" style="background: #3755B0; color: #FFFFFF;">434</td>
                            <td class="centrar-texto" style="background: #3755B0; color: #FFFFFF;">{{ '$ '.number_format($datos[4][1]['compraBruta'],2) }}</td>
                            <td class="centrar-texto" style="background: #3755B0; color: #FFFFFF;">{{ '$ '.number_format($datos[4][1]['nc'],2) }}</td>
                            <td class="centrar-texto" style="background: #3755B0; color: #FFFFFF;">{{ '$ '.number_format($datos[4][1]['compraNeta'],2) }}</td>
                            <td class="centrar-texto" style="background: #3755B0; color: #FFFFFF;">{{ '$ '.number_format($datos[4][1]['iva'],2) }}</td>
                        </tr>
                    @endif
                    @if(count($datos[20]) > 0)
                        <tr>
                            <td colspan="3" style="white-space: pre-wrap; background: #3755B0; color: #FFFFFF;">TOTAL DE VENTAS</td>
                            <td class="centrar-texto" style="background: #3755B0; color: #FFFFFF;">{{ '$ '.number_format($datos[20][1]['compraBruta'],2) }}</td>
                            <td class="centrar-texto" style="background: #3755B0; color: #FFFFFF;">{{ '$ '.number_format($datos[20][1]['nc'],2) }}</td>
                            <td class="centrar-texto" style="background: #3755B0; color: #FFFFFF;">{{ '$ '.number_format($datos[20][1]['compraNeta'],2) }}</td>
                            <td class="centrar-texto" style="background: #3755B0; color: #FFFFFF;">{{ '$ '.number_format($datos[20][1]['iva'],2) }}</td>
                        </tr>
                    @endif
                    @if(count($datos[21]) > 0)
                        <tr>
                            <td colspan="2" style="white-space: pre-wrap; background: #3755B0; color: #FFFFFF;">Impuesto a liquidar en este mes</td>
                            <td class="centrar-texto" style="white-space: pre-wrap; background: #3755B0; color: #FFFFFF;">499</td>
                            <td colspan="3" style="white-space: pre-wrap; background: #3755B0; color: #FFFFFF;"></td>
                            <td class="centrar-texto" style="background: #3755B0; color: #FFFFFF;">{{ '$ '.number_format($datos[21][1]['iva'],2) }}</td>
                        </tr>
                    @endif
                    <tr>
                        <td colspan="1" style="white-space: pre-wrap; background: #FFF; color: #000;">Total de Comprobantes de venta emitidas</td>
                        <td class="centrar-texto" style="background: #FFF; color: #000;">{{ $cantidad_venta }}</td>
                    
                        <td colspan="4" style="white-space: pre-wrap; background: #FFF; color: #000;">Total de Comprobantes de venta Anuladas</td>
                        <td class="centrar-texto" style="background: #FFF; color: #000;">{{ $cantidad_venta_anulada }}</td>
                    </tr>
                    
                    <tr><td colspan="7" class="centrar-texto" style="background: #FF8747; font-size: 20px;"><b>COMPRAS</b></td></tr>
                    <tr>
                        <td style="background: #3755B0; color: #FFFFFF;">DETALLE</td>
                        <td class="centrar-texto" style="background: #3755B0; color: #FFFFFF;">TIPO</td>
                        <td class="centrar-texto" style="background: #3755B0; color: #FFFFFF;">CASILLERO</td>
                        <td class="centrar-texto" style="background: #3755B0; color: #FFFFFF;">COMPRAS BRUTAS</td>
                        <td class="centrar-texto" style="background: #3755B0; color: #FFFFFF;">NC EN COMPRAS</td>
                        <td class="centrar-texto" style="background: #3755B0; color: #FFFFFF;">COMPRAS NETAS</td>
                        <td class="centrar-texto" style="background: #3755B0; color: #FFFFFF;">IMP GENERADO IVA</td>
                    </tr>
                    @if(count($datos[5]) > 0)
                        <tr><td colspan="7" style="background: #E9D65B; font-size: 15px;"><b>COMPRAS CON IVA 12%</b></td></tr>
                        @for ($i = 1; $i <= count($datos[5]); ++$i)
                        <tr>
                            <td style="white-space: pre-wrap;">{{ $datos[5][$i]['sustento'] }}</td>
                            <td class="centrar-texto">{{ $datos[5][$i]['porcentaje'] }}</td>
                            <td class="centrar-texto" style="background: #8BBDC7;">{{ $datos[5][$i]['casillero'] }}</td>
                            <td class="centrar-texto">{{ '$ '.number_format($datos[5][$i]['compraBruta'],2) }}</td>
                            <td class="centrar-texto">{{ '$ '.number_format($datos[5][$i]['nc'],2) }}</td>
                            <td class="centrar-texto">{{ '$ '.number_format($datos[5][$i]['compraNeta'],2) }}</td>
                            <td class="centrar-texto">{{ '$ '.number_format($datos[5][$i]['iva'],2) }}</td>
                        </tr>
                        @endfor
                    @endif
                    @if(count($datos[6]) > 0)
                        <tr><td colspan="7" style="background: #E9D65B; font-size: 15px;"><b>COMPRAS CON IVA 0%</b></td></tr>
                        @for ($i = 1; $i <= count($datos[6]); ++$i)
                        <tr>
                            <td style="white-space: pre-wrap;">{{ $datos[6][$i]['sustento'] }}</td>
                            <td class="centrar-texto">{{ $datos[6][$i]['porcentaje'] }}</td>
                            <td class="centrar-texto" style="background: #8BBDC7;">{{ $datos[6][$i]['casillero'] }}</td>
                            <td class="centrar-texto">{{ '$ '.number_format($datos[6][$i]['compraBruta'],2) }}</td>
                            <td class="centrar-texto">{{ '$ '.number_format($datos[6][$i]['nc'],2) }}</td>
                            <td class="centrar-texto">{{ '$ '.number_format($datos[6][$i]['compraNeta'],2) }}</td>
                            <td class="centrar-texto">{{ '$ '.number_format($datos[6][$i]['iva'],2) }}</td>
                        </tr>
                        @endfor
                    @endif
                    @if(count($datos[7]) > 0)
                        <tr>
                            <td colspan="3" style="white-space: pre-wrap; background: #3755B0; color: #FFFFFF;">TOTAL ADQUISICIONES Y PAGOS</td>
                            <td class="centrar-texto" style="background: #3755B0; color: #FFFFFF;">{{ '$ '.number_format($datos[7][1]['compraBruta'],2) }}</td>
                            <td class="centrar-texto" style="background: #3755B0; color: #FFFFFF;">{{ '$ '.number_format($datos[7][1]['nc'],2) }}</td>
                            <td class="centrar-texto" style="background: #3755B0; color: #FFFFFF;">{{ '$ '.number_format($datos[7][1]['compraNeta'],2) }}</td>
                            <td class="centrar-texto" style="background: #3755B0; color: #FFFFFF;">{{ '$ '.number_format($datos[7][1]['iva'],2) }}</td>
                        </tr>
                    @endif
                    @if(count($datos[8]) > 0)
                        @for ($i = 1; $i <= count($datos[8]); ++$i)
                        <tr>
                            <td style="white-space: pre-wrap;">{{ $datos[8][$i]['sustento'] }}</td>
                            <td class="centrar-texto">{{ $datos[8][$i]['porcentaje'] }}</td>
                            <td class="centrar-texto">{{ $datos[8][$i]['casillero'] }}</td>
                            <td class="centrar-texto">{{ '$ '.number_format($datos[8][$i]['compraBruta'],2) }}</td>
                            <td class="centrar-texto">{{ '$ '.number_format($datos[8][$i]['nc'],2) }}</td>
                            <td class="centrar-texto">{{ '$ '.number_format($datos[8][$i]['compraNeta'],2) }}</td>
                            <td class="centrar-texto">{{ '$ '.number_format($datos[8][$i]['iva'],2) }}</td>
                        </tr>
                        @endfor
                    @endif
                    @if(count($datos[9]) > 0)
                        <tr>
                            <td colspan="2" style="white-space: pre-wrap; background: #3755B0; color: #FFFFFF;">TOTAL pagos por reembolso como intermediario</td>
                            <td class="centrar-texto" style="background: #3755B0; color: #FFFFFF;">534</td>
                            <td class="centrar-texto" style="background: #3755B0; color: #FFFFFF;">{{ '$ '.number_format($datos[9][1]['compraBruta'],2) }}</td>
                            <td class="centrar-texto" style="background: #3755B0; color: #FFFFFF;">{{ '$ '.number_format($datos[9][1]['nc'],2) }}</td>
                            <td class="centrar-texto" style="background: #3755B0; color: #FFFFFF;">{{ '$ '.number_format($datos[9][1]['compraNeta'],2) }}</td>
                            <td class="centrar-texto" style="background: #3755B0; color: #FFFFFF;">{{ '$ '.number_format($datos[9][1]['iva'],2) }}</td>
                        </tr>
                    @endif
                    @if(count($datos[10]) > 0)
                        <tr>
                            <td colspan="3" style="white-space: pre-wrap; background: #3755B0; color: #FFFFFF;">TOTAL DE COMPRAS</td>
                            <td class="centrar-texto" style="background: #3755B0; color: #FFFFFF;">{{ '$ '.number_format($datos[10][1]['compraBruta'],2) }}</td>
                            <td class="centrar-texto" style="background: #3755B0; color: #FFFFFF;">{{ '$ '.number_format($datos[10][1]['nc'],2) }}</td>
                            <td class="centrar-texto" style="background: #3755B0; color: #FFFFFF;">{{ '$ '.number_format($datos[10][1]['compraNeta'],2) }}</td>
                            <td class="centrar-texto" style="background: #3755B0; color: #FFFFFF;">{{ '$ '.number_format($datos[10][1]['iva'],2) }}</td>
                        </tr>
                    @endif
                    @if(count($datos[11]) > 0)
                    <tr>
                        <td style="white-space: pre-wrap; background: #E9D65B; font-size: 15px;"><b>Crédito tributario aplicable en este período de acuerdo al :</b></td>
                        <td class="centrar-texto" style="background: #E9D65B; font-size: 15px;"><b>factor de proporcionalidad</b></td>
                        <td class="centrar-texto" style="background: #E9D65B; font-size: 15px;"></td>
                        <td class="centrar-texto" style="background: #E9D65B; font-size: 15px;"><b>o a su contabilidad</b></td>
                        <td class="centrar-texto" style="background: #E9D65B; font-size: 15px;"><b>X</b></td>
                        <td class="centrar-texto" style="background: #E9D65B; font-size: 15px;"></td>
                        <td class="centrar-texto" style="background: #E9D65B; font-size: 15px;"><b>{{ '$ '.number_format($datos[11][1]['iva'],2) }}</b></td>
                    </tr>
                    @endif
                    <tr>
                        <td colspan="1" style="white-space: pre-wrap; background: #FFF; color: #000;">Total de Comprobantes de Venta Recibidas</td>
                        <td class="centrar-texto" style="background: #FFF; color: #000;">{{ $cantidad_compra }}</td>

                        <td colspan="4" style="white-space: pre-wrap; background: #FFF; color: #000;">Total de Notas de Venta Recibidas</td>
                        <td class="centrar-texto" style="background: #FFF; color: #000;">{{ $cantidad_compra_boleta }}</td>
                    </tr>
                    <tr><td colspan="7" class="centrar-texto" style="background: #FF8747; font-size: 20px;"><b>RESUMEN IMPOSITIVO: AGENTE DE PERCEPCIÓN DEL IMPUESTO AL VALOR AGREGADO</b></td></tr>
                        <tr>
                            <td style="white-space: pre-wrap;">Impuesto causado (si la diferencia de los campos 499-564 es mayor que cero)</td>
                            <td class="centrar-texto"></td>
                            <td class="centrar-texto" style="background: #8BBDC7;">601</td>
                            <td class="centrar-texto"></td>
                            <td class="centrar-texto"></td>
                            <td class="centrar-texto"></td>
                            <td class="centrar-texto"><input id="casill601" name="casill601" type="hidden" value="{{number_format($datos[22],2)}}">{{'$ '.number_format($datos[22],2)}}</td>
                        </tr>
                        <tr>
                            <td style="white-space: pre-wrap;">Crédito tributario aplicable en este período (si la diferencia de los campos 499-564 es menor que cero)</td>
                            <td class="centrar-texto"></td>
                            <td class="centrar-texto" style="background: #8BBDC7;">602</td>
                            <td class="centrar-texto"></td>
                            <td class="centrar-texto"></td>
                            <td class="centrar-texto"></td>
                            <td class="centrar-texto"><input type="hidden" id="valor0" name="valor0" value="{{number_format($datos[23],2, '.', '')}}" required/>{{'$ '.number_format($datos[23],2)}}</td>
                        </tr>
                        <tr>
                            <td style="white-space: pre-wrap;">(-) Saldo crédito tributario del mes anterior Por adquisiciones e importaciones (trasládese el campo 615 de la declaración del período anterior)</td>
                            <td class="centrar-texto"></td>
                            <td class="centrar-texto" style="background: #8BBDC7;">605</td>
                            <td class="centrar-texto"></td>
                            <td class="centrar-texto"></td>
                            <td class="centrar-texto"></td>
                            <td class="centrar-texto"><input class="form-control derecha-texto" id="valor1" name="valor1" value="{{number_format($ant605,2, '.', '')}}" onkeyup="calculos();" required/></td>
                        </tr>
                        <tr>
                            <td style="white-space: pre-wrap;">(-) Saldo crédito tributario del mes anterior Por retenciones en la fuente de IVA que le han sido efectuadas (trasládese el campo 617 de la declaración del período anterior)</td>
                            <td class="centrar-texto"></td>
                            <td class="centrar-texto" style="background: #8BBDC7;">606</td>
                            <td class="centrar-texto"></td>
                            <td class="centrar-texto"></td>
                            <td class="centrar-texto"></td>
                            <td class="centrar-texto"><input class="form-control derecha-texto" id="valor2" name="valor2" value="{{number_format($ant606,2, '.', '')}}" onkeyup="calculos();" required/></td>
                        </tr>
                        <tr>
                            <td style="white-space: pre-wrap;">(-) Retenciones en la fuente de IVA que le han sido efectuadas en este período</td>
                            <td class="centrar-texto"></td>
                            <td class="centrar-texto" style="background: #8BBDC7;">609</td>
                            <td class="centrar-texto"></td>
                            <td class="centrar-texto"></td>
                            <td class="centrar-texto"></td>
                            <!--@if(count($datos[17])>0)
                                <td class="centrar-texto"><input class="form-control derecha-texto" id="valor3" name="valor3" value="{{number_format($datos[17][1]['valor'],2, '.', '') }}" onkeyup="calculos();" required/></td>
                            @else
                                <td class="centrar-texto"><input class="form-control derecha-texto" id="valor3" name="valor3" value="{{number_format($ant609,2, '.', '') }}" onkeyup="calculos();" required/></td>
                            @endif
                            !-->
                            <?php $varacum = 0;?> 
                            @if(count($datos[16]) > 0)                                
                                <td class="centrar-texto"> <input class="form-control derecha-texto" id="valor3" name="valor3" value="{{number_format($datos[17][1]['valor'],2, '.', '')}}" onkeyup="calculos();" required/> </td>
                            @else
                                <td class="centrar-texto"> <input class="form-control derecha-texto" id="valor3" name="valor3" value="0" onkeyup="calculos();" required/> </td>
                  
                            @endif
                        </tr>
                        <tr>
                            <td style="white-space: pre-wrap;">(+) Ajuste por IVA devuelto e IVA rechazado (por concepto de devoluciones de IVA), ajuste de IVA por procesos de control y otros (adquisiciones en importaciones), imputables al crédito tributario</td>
                            <td class="centrar-texto"></td>
                            <td class="centrar-texto" style="background: #8BBDC7;">612</td>
                            <td class="centrar-texto"></td>
                            <td class="centrar-texto"></td>
                            <td class="centrar-texto"></td>
                            <td class="centrar-texto"><input class="form-control derecha-texto" id="valor4" name="valor4" value="{{number_format($ant612,2, '.', '')}}" onkeyup="calculos();" required/></td>
                        </tr>
                        <tr>
                            <td style="white-space: pre-wrap;">Saldo crédito tributario para el próximo mes Por adquisiciones e importaciones</td>
                            <td class="centrar-texto"></td>
                            <td class="centrar-texto" style="background: #8BBDC7;">615</td>
                            <td class="centrar-texto"></td>
                            <td class="centrar-texto"></td>
                            <td class="centrar-texto"></td>
                            <td class="centrar-texto"><input class="form-control derecha-texto" id="valor5" name="valor5" value="{{number_format($ant615,2)}}" required/></td>
                             <!--
                            <td class="centrar-texto"><input class="form-control derecha-texto" id="valor5" name="valor5" value="{{number_format($datos[23],2)}}" required/></td>
                            !-->                        
                        </tr>
                        <tr>
                            <td style="white-space: pre-wrap;">Saldo crédito tributario para el próximo mes Por retenciones en la fuente de IVA que le han sido efectuadas</td>
                            <td class="centrar-texto"></td>
                            <td class="centrar-texto" style="background: #8BBDC7;">617</td>
                            <td class="centrar-texto"></td>
                            <td class="centrar-texto"></td>
                            <td class="centrar-texto"></td>

                            @if(count($datos[17])>0)
                                <td class="centrar-texto"><input class="form-control derecha-texto" id="valor6" name="valor6" value="{{number_format($datos[17][1]['valor'],2, '.', '') }}" required/></td>
                            @else
                                <td class="centrar-texto"><input class="form-control derecha-texto" id="valor6" name="valor6" value="0.00" required readonly/></td>
                            @endif
                        </tr>
                    @if(count($datos[12]) > 0)
                        <tr><td colspan="7" class="centrar-texto" style="background: #FF8747; font-size: 20px;"><b>AGENTE DE RETENCIÓN DEL IMPUESTO AL VALOR AGREGADO</b></td></tr>
                        <tr>
                            <td style="background: #3755B0; color: #FFFFFF;">Concepto de Retención</td>
                            <td class="centrar-texto" style="background: #3755B0; color: #FFFFFF;">No. Registros</td>
                            <td class="centrar-texto" style="background: #3755B0; color: #FFFFFF;">Cod.</td>
                            <td class="centrar-texto" style="background: #3755B0; color: #FFFFFF;" colspan="2"></td>
                            <td class="centrar-texto" style="background: #3755B0; color: #FFFFFF;">Base Imponible</td>
                            <td class="centrar-texto" style="background: #3755B0; color: #FFFFFF;">Valor Retenido</td>
                        </tr>
                        @for ($i = 1; $i <= count($datos[12]); ++$i)
                        <tr>
                            <td style="white-space: pre-wrap;">{{ $datos[12][$i]['nombre'] }}</td>
                            <td class="centrar-texto">{{ $datos[12][$i]['cantidad'] }}</td>
                            <td class="centrar-texto">{{ $datos[12][$i]['codigo'] }}</td>
                            <td colspan="2"></td>
                            <td class="centrar-texto">{{ '$ '.number_format($datos[12][$i]['base'],2) }}</td>
                            <td class="centrar-texto">{{ '$ '.number_format($datos[12][$i]['valor'],2) }}</td>
                        </tr>
                        @endfor
                        <tr>
                            <td colspan="5" class="derecha-texto" style="background: #3755B0; color: #FFFFFF;">TOTAL</td>
                            <td class="centrar-texto" style="background: #3755B0; color: #FFFFFF;">{{ '$ '.number_format($datos[13][1]['base'],2) }}</td>
                            <td class="centrar-texto" style="background: #3755B0; color: #FFFFFF;">{{ '$ '.number_format($datos[13][1]['valor'],2) }}</td>
                        </tr>
                    @endif
                    @if(count($datos[14]) > 0)
                        <tr><td colspan="7" class="centrar-texto" style="background: #FF8747; font-size: 20px;"><b>AGENTE DE RETENCIÓN DEL IMPUESTO A LA RENTA</b></td></tr>
                        <tr>
                            <td style="background: #3755B0; color: #FFFFFF;">Concepto de Retención</td>
                            <td class="centrar-texto" style="background: #3755B0; color: #FFFFFF;">No. Registros</td>
                            <td class="centrar-texto" style="background: #3755B0; color: #FFFFFF;">Cod.</td>
                            <td class="centrar-texto" style="background: #3755B0; color: #FFFFFF;" colspan="2"></td>
                            <td class="centrar-texto" style="background: #3755B0; color: #FFFFFF;">Base Imponible</td>
                            <td class="centrar-texto" style="background: #3755B0; color: #FFFFFF;">Valor Retenido</td>
                        </tr>
                        <tr>
                            <td style="white-space: pre-wrap;">En relación de dependencia que supera o nó la base desgravada</td>
                            <td class="centrar-texto"></td>
                            <td class="centrar-texto">302</td>
                            <td colspan="2"></td>
                            <td class="centrar-texto"><input class="text-right" type="number" id="valor7" name="base_imponible" min="0" value="{{number_format($ant302vneto,2, '.', '')}}" step="any"></input></td>
                            <td class="centrar-texto"><input class="text-right" type="number" id="valor8" name="valor_retenido" min="0" value="{{number_format($ant302iva,2, '.', '')}}" step="any"></input></td>
                        </tr>
                        @for ($i = 1; $i <= count($datos[14]); ++$i)
                        <tr>
                            <td style="white-space: pre-wrap;">{{ $datos[14][$i]['nombre'] }}</td>
                            <td class="centrar-texto">{{ $datos[14][$i]['cantidad'] }}</td>
                            <td class="centrar-texto">{{ $datos[14][$i]['codigo'] }}</td>
                            <td colspan="2"></td>
                            <td class="centrar-texto">{{ '$ '.number_format($datos[14][$i]['base'],2) }}</td>
                            <td class="centrar-texto">{{ '$ '.number_format($datos[14][$i]['valor'],2) }}</td>
                        </tr>
                        @endfor
                        <tr>
                            <td colspan="5" class="derecha-texto" style="background: #3755B0; color: #FFFFFF;">TOTAL</td>
                            <td class="centrar-texto" style="background: #3755B0; color: #FFFFFF;">{{ '$ '.number_format($datos[15][1]['base'],2) }}</td>
                            <td class="centrar-texto" style="background: #3755B0; color: #FFFFFF;">{{ '$ '.number_format($datos[15][1]['valor'],2) }}</td>
                        </tr>
                    @endif
                    
                    @if(count($datos[16]) > 0)
                        <tr><td colspan="7" class="centrar-texto" style="background: #FF8747; font-size: 20px;"><b>AGENTE DE RETENCIÓN DEL IMPUESTO AL VALOR AGREGADO (RECIBIDO)</b></td></tr>
                        <tr>
                            <td style="background: #3755B0; color: #FFFFFF;">Concepto de Retención</td>
                            <td class="centrar-texto" style="background: #3755B0; color: #FFFFFF;">No. Registros</td>
                            <td class="centrar-texto" style="background: #3755B0; color: #FFFFFF;">Cod.</td>
                            <td class="centrar-texto" style="background: #3755B0; color: #FFFFFF;" colspan="2"></td>
                            <td class="centrar-texto" style="background: #3755B0; color: #FFFFFF;">Base Imponible</td>
                            <td class="centrar-texto" style="background: #3755B0; color: #FFFFFF;">Valor Retenido</td>
                        </tr>
                        @for ($i = 1; $i <= count($datos[16]); ++$i)
                        <tr>
                            <td style="white-space: pre-wrap;">{{ $datos[16][$i]['nombre'] }}</td>
                            <td class="centrar-texto">{{ $datos[16][$i]['cantidad'] }}</td>
                            <td class="centrar-texto">{{ $datos[16][$i]['codigo'] }}</td>
                            <td colspan="2"></td>
                            <td class="centrar-texto">{{ '$ '.number_format($datos[16][$i]['base'],2) }}</td>
                            <td class="centrar-texto">{{ '$ '.number_format($datos[16][$i]['valor'],2) }}</td>
                        </tr>
                        @endfor
                        <tr>
                            <td colspan="5" class="derecha-texto" style="background: #3755B0; color: #FFFFFF;">TOTAL</td>
                            <td class="centrar-texto" style="background: #3755B0; color: #FFFFFF;">{{ '$ '.number_format($datos[17][1]['base'],2) }}</td>
                            <td class="centrar-texto" style="background: #3755B0; color: #FFFFFF;">{{ '$ '.number_format($datos[17][1]['valor'],2) }}</td>
                        </tr>
                    @endif
                    @if(count($datos[18]) > 0)
                        <tr><td colspan="7" class="centrar-texto" style="background: #FF8747; font-size: 20px;"><b>AGENTE DE RETENCIÓN DEL IMPUESTO A LA RENTA (RECIBIDO)</b></td></tr>
                        <tr>
                            <td style="background: #3755B0; color: #FFFFFF;">Concepto de Retención</td>
                            <td class="centrar-texto" style="background: #3755B0; color: #FFFFFF;">No. Registros</td>
                            <td class="centrar-texto" style="background: #3755B0; color: #FFFFFF;">Cod.</td>
                            <td class="centrar-texto" style="background: #3755B0; color: #FFFFFF;" colspan="2"></td>
                            <td class="centrar-texto" style="background: #3755B0; color: #FFFFFF;">Base Imponible</td>
                            <td class="centrar-texto" style="background: #3755B0; color: #FFFFFF;">Valor Retenido</td>
                        </tr>
                        @for ($i = 1; $i <= count($datos[18]); ++$i)
                        <tr>
                            <td style="white-space: pre-wrap;">{{ $datos[18][$i]['nombre'] }}</td>
                            <td class="centrar-texto">{{ $datos[18][$i]['cantidad'] }}</td>
                            <td class="centrar-texto">{{ $datos[18][$i]['codigo'] }}</td>
                            <td colspan="2"></td>
                            <td class="centrar-texto">{{ '$ '.number_format($datos[18][$i]['base'],2) }}</td>
                            <td class="centrar-texto">{{ '$ '.number_format($datos[18][$i]['valor'],2) }}</td>
                        </tr>
                        @endfor
                        <tr>
                            <td colspan="5" class="derecha-texto" style="background: #3755B0; color: #FFFFFF;">TOTAL</td>
                            <td class="centrar-texto" style="background: #3755B0; color: #FFFFFF;">{{ '$ '.number_format($datos[19][1]['base'],2) }}</td>
                            <td class="centrar-texto" style="background: #3755B0; color: #FFFFFF;">{{ '$ '.number_format($datos[19][1]['valor'],2) }}</td>
                        </tr>
                    @endif
                @endif               
                </tbody>
            </table>
            <center><h3 class="neo-fondo-tabla"><b>PRODUCTOS POR CASILLERO Y VENTA</b><h3></center>
            <table id="example2" class="table table-bordered table-hover table-responsive sin-salto">
                <thead>
                    <tr class="text-center">
                        <th>Producto</th>
                        <th>Casillero</th>
                        <th>Valor</th>
                        <th>Factura</th>  
                    </tr>
                </thead>
                <tbody>
                    @if(isset($datos))
                        @for ($i = 1; $i <= count($datos[24]); ++$i)
                        <tr>
                            <td class="text-center">{{ $datos[24][$i]['producto'] }}</td>
                            <td class="text-center">{{ $datos[24][$i]['codigo'] }}</td>
                            <td class="text-center">{{ number_format( $datos[24][$i]['valor'],2) }}</td>
                            <td class="text-center">{{ $datos[24][$i]['factura'] }}</td> 

                        </tr>                         
                        @endfor
                    @endif
                </tbody>
            </table>
        </div>
    </div>
    <div id="div-gif" class="col-md-12 text-center" style="position: absolute;height: 300px; margin-top: 150px; display: none">
        <img src="{{ url('img/loading.gif') }}" width=90px height=90px style="align-items: center">
    </div>
    <script>
        function girarGif(){
            document.getElementById("div-gif").style.display="inline"
            console.log("girando")
        }
        function ocultarGif(){
            document.getElementById("div-gif").style.display="none"
            console.log("no girando")
        }

        tipo=""

        function setTipo(t){
            tipo=t
        }

        setTimeout(function(){
            console.log("registro de la funcion")
            $("#idForm").submit(function(e) {
                if(tipo=="")  return
                var form = $(this);
                form.append("excel", "descargar excel");
                var actionUrl = form.attr('action');


                console.log("submit "+actionUrl)
                console.log(form.serialize())
                console.log(form)
                girarGif()
                $.ajax({
                    type: "POST",
                    url: actionUrl,
                    data: form.serialize()+tipo,
                    success: function(data) {
                        setTimeout(function(){
                            ocultarGif()
                            tipo=""
                        }, 1000)
                    }
                });
            });
        }, 1200)
    </script>

</form>
<script type="text/javascript">
    function round(num) {
        var m = Number((Math.abs(num) * 100).toPrecision(15));
         m =Math.round(m) / 100 * Math.sign(num);
         return (m).toFixed(2);
    }
    function cargarmetodo(){        
         //VALOR5 = CASILLERO 615
        //VALOR6 = CASILLERO 617
        //VALOR0 = CASILLERO 602
        //VALOR1 = CASILLERO 605
        //VALOR2 = CASILLERO 606
        //VALOR3 = CASILLERO 609
        //document.getElementById("valor5").value = Number(Number(document.getElementById("valor1").value)+Number(document.getElementById("valor0").value)).toFixed(2);
        console.log(parseFloat(document.getElementById("casill601").value.replace(/,/g, '')));
        
        if(parseFloat(Number(document.getElementById("casill601").value.replace(/,/g, ''))) > 0){
                
                if (parseFloat(Number(document.getElementById("casill601").value.replace(/,/g, ''))) > parseFloat(document.getElementById("valor1").value)){ 
                    document.getElementById("valor5").value = 0
                    document.getElementById("valor6").value = round(parseFloat(document.getElementById("valor2").value) - ( parseFloat(Number(document.getElementById("casill601").value.replace(/,/g, ''))) - parseFloat(document.getElementById("valor1").value) ) + parseFloat(document.getElementById("valor3").value));
                }
                if (parseFloat(Number(document.getElementById("casill601").value.replace(/,/g, ''))) < parseFloat(document.getElementById("valor1").value)){ 
                    console.log("601 si es menor");
                    document.getElementById("valor5").value = round(parseFloat(document.getElementById("valor1").value) - parseFloat(Number(document.getElementById("casill601").value.replace(/,/g, ''))));
                    document.getElementById("valor6").value = round(parseFloat(document.getElementById("valor2").value) + parseFloat(document.getElementById("valor3").value));
                }
                if (parseFloat(Number(document.getElementById("casill601").value.replace(/,/g, ''))) == parseFloat(document.getElementById("valor1").value)){ 
                    //CASILLERO 615
                    document.getElementById("valor5").value = round(parseFloat(document.getElementById("valor1").value) - parseFloat(Number(document.getElementById("casill601").value.replace(/,/g, ''))));
                    //CASILLERO 617
                    document.getElementById("valor6").value = round(parseFloat(document.getElementById("valor2").value) + parseFloat(document.getElementById("valor3").value));
                }
            }
            //CASILLERO 602
            if(parseFloat(document.getElementById("valor0").value.replace(/,/g, '')) > 0){            
                document.getElementById("valor5").value = round(parseFloat(document.getElementById("valor0").value.replace(/,/g, '')) + parseFloat(document.getElementById("valor1").value)-parseFloat(document.getElementById("valor4").value));
                document.getElementById("valor6").value = round(parseFloat(document.getElementById("valor2").value) + parseFloat(document.getElementById("valor3").value));            
            }
            //CASILLERO 617
            //document.getElementById("valor6").value = Number(Number(document.getElementById("valor2").value)+Number(document.getElementById("valor3").value)).toFixed(2);
    
    

    }
    function calculos(){
         //VALOR5 = CASILLERO 615
        //VALOR6 = CASILLERO 617
        //VALOR0 = CASILLERO 602
        //VALOR1 = CASILLERO 605
        //VALOR2 = CASILLERO 606
        //VALOR3 = CASILLERO 609
        //document.getElementById("valor5").value = Number(Number(document.getElementById("valor1").value)+Number(document.getElementById("valor0").value)).toFixed(2);
        console.log(parseFloat(document.getElementById("casill601").value.replace(/,/g, '')));
        
        if(parseFloat(Number(document.getElementById("casill601").value.replace(/,/g, ''))) > 0){
                
                if (parseFloat(Number(document.getElementById("casill601").value.replace(/,/g, ''))) > parseFloat(document.getElementById("valor1").value)){ 
                    document.getElementById("valor5").value = 0
                    document.getElementById("valor6").value = round(parseFloat(document.getElementById("valor2").value) - ( parseFloat(Number(document.getElementById("casill601").value.replace(/,/g, ''))) - parseFloat(document.getElementById("valor1").value) ) + parseFloat(document.getElementById("valor3").value));
                }
                if (parseFloat(Number(document.getElementById("casill601").value.replace(/,/g, ''))) < parseFloat(document.getElementById("valor1").value)){ 
                    console.log("601 si es menor");
                    document.getElementById("valor5").value = round(parseFloat(document.getElementById("valor1").value) - parseFloat(Number(document.getElementById("casill601").value.replace(/,/g, ''))));
                    document.getElementById("valor6").value = round(parseFloat(document.getElementById("valor2").value) + parseFloat(document.getElementById("valor3").value));
                }
                if (parseFloat(Number(document.getElementById("casill601").value.replace(/,/g, ''))) == parseFloat(document.getElementById("valor1").value)){ 
                    //CASILLERO 615
                    document.getElementById("valor5").value = round(parseFloat(document.getElementById("valor1").value) - parseFloat(Number(document.getElementById("casill601").value.replace(/,/g, ''))));
                    //CASILLERO 617
                    document.getElementById("valor6").value = round(parseFloat(document.getElementById("valor2").value) + parseFloat(document.getElementById("valor3").value));
                }
            }
            //CASILLERO 602
            if(parseFloat(document.getElementById("valor0").value.replace(/,/g, '')) > 0){            
                document.getElementById("valor5").value = round(parseFloat(document.getElementById("valor0").value.replace(/,/g, '')) + parseFloat(document.getElementById("valor1").value)-parseFloat(document.getElementById("valor4").value));
                document.getElementById("valor6").value = round(parseFloat(document.getElementById("valor2").value) + parseFloat(document.getElementById("valor3").value));            
            }
            //CASILLERO 617
            //document.getElementById("valor6").value = Number(Number(document.getElementById("valor2").value)+Number(document.getElementById("valor3").value)).toFixed(2);
    
    
    }
</script>
@endsection