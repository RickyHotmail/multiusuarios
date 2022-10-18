@extends ('admin.layouts.formatoPDFDiario')
@section('contenido')
    @section('titulo')
        <tr><td colspan="2" class="centrar letra15 negrita">
            @if($diario->diario_tipo == 'CEPP' or $diario->diario_tipo == 'CEAP' or $diario->diario_tipo == 'CEAE' 
            or $diario->diario_tipo == 'CEPV' or $diario->diario_tipo == 'CPRE')
                COMPROBANTE DE EGRESO
            @else
                COMPROBANTE DE DIARIO
            @endif
        </td></tr>
        <tr><td colspan="2" class="centrar letra15 borde-gris">No. {{ $diario->diario_codigo }}</td></tr>
    @endsection
    <table style="white-space: normal!important;">
        <tr class="letra12">
            <td class="negrita" style="width: 105px;">FECHA:</td>
            <td>{{ DateTime::createFromFormat('Y-m-d', $diario->diario_fecha)->format('d/m/Y') }}</td>
            <td class="negrita" style="width: 125px;">TIPO DOCUMENTO:</td>
            <td>{{ $diario->diario_tipo_documento }}</td>
        </tr>
        <tr class="letra12">
            <td class="negrita" style="width: 105x;">REFERENCIA :</td>
            <td>{{ $diario->diario_beneficiario }}</td>
            <td class="negrita" style="width: 125px;">DOCUMENTO No:</td>
            <td>{{ $diario->diario_numero_documento }}</td>
        </tr>
        <tr class="letra12">
            <td class="negrita" style="width: 105px;">CONCEPTO:</td>
            <td colspan="3">{{ $diario->diario_comentario }}</td>
        </tr>
    </table>
    <br>
    <table style="white-space: normal!important; border-collapse: collapse;">
        <thead>
            <tr class="centrar letra12">
                <th class="borde-izquierdo cabecera-diario izquierda">CÓDIGO</th>    
                <th class="cabecera-diario izquierda">CUENTA</th>
                <th class="cabecera-diario izquierda">DESCRIPCIÓN</th>
                <th class="cabecera-diario">DEBE</th>
                <th class="borde-derecho cabecera-diario">HABER</th>   
            </tr>
        </thead>
        <tbody>
            @if(isset($diario))
            <?php $debe = 0; $haber = 0; ?>
                <tr><td colspan="5" style="padding-top: 5px;"></td></tr>
                @foreach($diario->detalles->sortBy('detalle_haber') as $detalle)
                    <?php $debe = $debe + $detalle->detalle_debe; $haber = $haber + $detalle->detalle_haber; ?>
                    <tr class="letra10">
                        <td class="detalle-diario"><b>{{ $detalle->cuenta->cuentaPadre->cuenta_numero }}</b></td>
                        <td class="detalle-diario"><b>{{ $detalle->cuenta->cuentaPadre->cuenta_nombre }}</b></td>
                        <td rowspan="2" class="detalle-diario">{{ $detalle->detalle_comentario }}</td>
                        <td rowspan="2" class="detalle-diario dereche">@if($detalle->detalle_debe <> 0) {{ number_format($detalle->detalle_debe,2) }} @endif</td>
                        <td rowspan="2" class="detalle-diario dereche">@if($detalle->detalle_haber <> 0) {{ number_format($detalle->detalle_haber,2) }} @endif</td>
                    </tr>
                    <tr class="letra10">
                        <td class="detalle-diario">{{ $detalle->cuenta->cuenta_numero }}</td>
                        <td class="detalle-diario" style="padding-left: 20px;">{{ $detalle->cuenta->cuenta_nombre }}</td>
                    </tr>
                @endforeach
                <tr><td colspan="5" style="padding-top: 5px;"></td></tr>
                <tr class="letra12">
                    <td style="border-top: 1px solid black;"></td>
                    <td style="border-top: 1px solid black;"></td>
                    <td class="foot-diario centrar">TOTAL</td>
                    <td class="foot-diario dereche">{{ number_format($debe,2) }}</td>
                    <td class="foot-diario dereche">{{ number_format($haber,2) }}</td>
                </tr>
            @endif
        </tbody>
    </table>
    <table style="padding-top: 70px;">
        <tr class="letra12">
            <td class="centrar" style="border-top: 1px solid black; width: 20%;white-space: pre-wrap;">Elaborado por: <br> {{ Auth::user()->user_nombre }}</td>
            <td style="padding-right: 15px; padding-left: 15px;"></td>
            <td class="centrar" style="border-top: 1px solid black;width: 20%;">Revisado por:</td>
            <td style="padding-right: 15px; padding-left: 15px;"></td>
            <td class="centrar" style="border-top: 1px solid black;width: 20%;">Autorizado por:</td>
            <td style="padding-right: 15px; padding-left: 15px;"></td>
            <td class="" style="border-top: 1px solid black;width: 20%;">Beneficiario:<br>C.I.:</td>
        </tr>
    </table>
@endsection