@extends ('admin.layouts.formatoPDF')
@section('contenido')
    @section('titulo')
        <tr><td colspan="2" class="centrar letra15 negrita">TALÓN RESUMEN - ANEXO TRANSACCIONAL SIMPLIFICADO</td></tr>
        <tr><td colspan="2" class="centrar letra15 borde-gris">DEL {{ $desde }} AL {{ $hasta }}</td></tr>
    @endsection
    <table id="tabladetalle">
        <thead>
            <tr class="letra12"><th  class="centrar letra-blanca fondo-azul-claro"colspan="7">COMPRAS</th></tr>
            <tr class="letra10 centrar fondo-celeste">
                <th>Cod.</th>
                <th>Transacción</th>
                <th>No. Registros</th>
                <th>Tarifa 0%</th>
                <th>Tarifa diferente 0%</th>
                <th>No Objeto IVA</th>
                <th>Valor IVA</th>
            </tr>
        </thead>
        <tbody>
            @if(isset($tabla1))
                @for ($i = 1; $i <= count($tabla1); ++$i)    
                <tr class="letra10 centrar">
                    @if($tabla1[$i]['tra'] == 'TOTAL')
                        <td style="border-top: 1px solid black;" colspan="3"><b>{{ $tabla1[$i]['tra'] }}</b></td>
                        <td style="border-top: 1px solid black;"><b>{{ number_format($tabla1[$i]['0'],2) }}</b></td>
                        <td style="border-top: 1px solid black;"><b>{{ number_format($tabla1[$i]['12'],2) }}</b></td>
                        <td style="border-top: 1px solid black;"><b>0.00</b></td>
                        <td style="border-top: 1px solid black;"><b> {{ number_format($tabla1[$i]['iva'],2) }}</b></td>
                    @else
                        <td>{{ $tabla1[$i]['cod'] }}</td>
                        <td>{{ $tabla1[$i]['tra'] }}</td>
                        <td>{{ $tabla1[$i]['can'] }}</td>
                        <td>{{ number_format($tabla1[$i]['0'],2) }}</td>
                        <td>{{ number_format($tabla1[$i]['12'],2) }}</td>
                        <td>0.00</td>
                        <td> {{ number_format($tabla1[$i]['iva'],2) }}</td>
                    @endif
                </tr>
                @endfor
            @endif
        </tbody>
    </table>
    <br>
    <table id="tabladetalle">
        <thead>
            <tr class="letra12"><th  class="centrar letra-blanca fondo-azul-claro"colspan="7">VENTAS</th></tr>
            <tr class="letra10 centrar fondo-celeste">
                <th>Cod.</th>
                <th>Transacción</th>
                <th>No. Registros</th>
                <th>Tarifa 0%</th>
                <th>Tarifa diferente 0%</th>
                <th>No Objeto IVA</th>
                <th>Valor IVA</th>
            </tr>
        </thead>
        <tbody>
            @if(isset($tabla2))
                @for ($i = 1; $i <= count($tabla2); ++$i)    
                <tr class="letra10 centrar">
                    @if($tabla2[$i]['tra'] == 'TOTAL')
                        <td style="border-top: 1px solid black;" colspan="3"><b>{{ $tabla2[$i]['tra'] }}</b></td>
                        <td style="border-top: 1px solid black;"><b>{{ number_format($tabla2[$i]['0'],2) }}</b></td>
                        <td style="border-top: 1px solid black;"><b>{{ number_format($tabla2[$i]['12'],2) }}</b></td>
                        <td style="border-top: 1px solid black;"><b>0.00</b></td>
                        <td style="border-top: 1px solid black;"><b> {{ number_format($tabla2[$i]['iva'],2) }}</b></td>
                    @else
                        <td>{{ $tabla2[$i]['cod'] }}</td>
                        <td>{{ $tabla2[$i]['tra'] }}</td>
                        <td>{{ $tabla2[$i]['can'] }}</td>
                        <td>{{ number_format($tabla2[$i]['0'],2) }}</td>
                        <td>{{ number_format($tabla2[$i]['12'],2) }}</td>
                        <td>0.00</td>
                        <td> {{ number_format($tabla2[$i]['iva'],2) }}</td>
                    @endif
                </tr>
                @endfor
            @endif
        </tbody>
    </table>
    <br>
    <div class="letra12" style="background: #C2C1C1; color: #000; text-align: center;"><b>RESUMEN DE RETENCIONES</b></div>
    <br>
    <table id="tabladetalle">
        <thead>
            <tr class="letra12"><th  class="centrar letra-blanca fondo-azul-claro"colspan="5">RETENCION EN LA FUENTE DE IMPUESTO A LA RENTA</th></tr>
            <tr class="letra10 centrar fondo-celeste">
                <th>Cod.</th>
                <th>Concepto de Retención</th>
                <th>No. Registros</th>
                <th>Base Imponible</th>
                <th>Valor Retenido</th>
            </tr>
        </thead>
        <tbody>
            @if(isset($tabla3))
                @for ($i = 1; $i <= count($tabla3); ++$i)    
                <tr class="letra10 centrar">
                    @if($tabla3[$i]['tra'] == 'TOTAL')
                        <td style="border-top: 1px solid black;" colspan="3"><b>{{ $tabla3[$i]['tra'] }}</b></td>
                        <td style="border-top: 1px solid black;"><b>{{ number_format($tabla3[$i]['base'],2) }}</b></td>
                        <td style="border-top: 1px solid black;"><b>{{ number_format($tabla3[$i]['valor'],2) }}</b></td>
                    @else
                        <td>{{ $tabla3[$i]['cod'] }}</td>
                        <td style='white-space: pre-wrap;'>{{ $tabla3[$i]['tra'] }}</td>
                        <td>{{ $tabla3[$i]['can'] }}</td>
                        <td>{{ number_format($tabla3[$i]['base'],2) }}</td>
                        <td>{{ number_format($tabla3[$i]['valor'],2) }}</td>
                    @endif
                </tr>
                @endfor
            @endif
        </tbody>
    </table>
    <br>
    <table id="tabladetalle">
        <thead>
            <tr class="letra12"><th  class="centrar letra-blanca fondo-azul-claro"colspan="3">RETENCION EN LA FUENTE DE IVA</th></tr>
            <tr class="letra10 centrar fondo-celeste">
                <th>Operación</th>
                <th>Concepto de Retención</th>
                <th>Valor Retenido</th>
            </tr>
        </thead>
        <tbody>
            @if(isset($tabla4))
                @for ($i = 1; $i <= count($tabla4); ++$i)    
                <tr class="letra10 centrar">
                    @if($tabla4[$i]['tra'] == 'TOTAL')
                        <td style="border-top: 1px solid black;" colspan="2"><b>{{ $tabla4[$i]['tra'] }}</b></td>
                        <td style="border-top: 1px solid black;"><b>{{ number_format($tabla4[$i]['valor'],2) }}</b></td>
                    @else
                        <td>COMPRA</td>
                        <td>{{ $tabla4[$i]['tra'] }}</td>
                        <td>{{ number_format($tabla4[$i]['valor'],2) }}</td>
                    @endif
                </tr>
                @endfor
            @endif
        </tbody>
    </table>
    <br>
    <table id="tabladetalle">
        <thead>
            <tr class="letra12"><th  class="centrar letra-blanca fondo-azul-claro"colspan="3">RESUMEN DE RETENCIONES QUE LE EFECTUARON EN EL PERIODO</th></tr>
            <tr class="letra10 centrar fondo-celeste">
                <th>Operación</th>
                <th>Concepto de Retención</th>
                <th>Valor Retenido</th>
            </tr>
        </thead>
        <tbody>
            @if(isset($tabla5))
                @for ($i = 1; $i <= count($tabla5); ++$i)    
                <tr class="letra10 centrar">
                    @if($tabla5[$i]['tra'] == 'TOTAL')
                        <td style="border-top: 1px solid black;" colspan="2"><b>{{ $tabla5[$i]['tra'] }}</b></td>
                        <td style="border-top: 1px solid black;"><b>{{ number_format($tabla5[$i]['valor'],2) }}</b></td>
                    @else   
                        <td>VENTA</td>
                        <td>{{ $tabla5[$i]['tra'] }}</td>
                        <td>{{ number_format($tabla5[$i]['valor'],2) }}</td>
                    @endif
                </tr>
                @endfor
            @endif
        </tbody>
    </table>
@endsection