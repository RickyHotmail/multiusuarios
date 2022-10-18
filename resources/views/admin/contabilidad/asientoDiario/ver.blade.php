@extends ('admin.layouts.admin')
@section('principal')
<div class="row">
    <div class="col-sm-8">
        <div class="card card-secondary">
            <div class="card-header">
                <h3 class="card-title centrar-texto">
                    DIARIO
                </h3>
                <div class="float-right">
                    <a target="_blank" href="{{ url("asientoDiario/imprimir/{$diario->diario_id}") }}"><button type="button" class="btn btn-default btn-sm"><i class="fa fa-print"></i>&nbsp;Imprimir</button></a>
                    <button type="button" onclick='history.back();'  class="btn btn-default btn-sm"><i class="fa fa-undo"></i>&nbsp;Atras</button>                
                </div>
            </div>
            <div class="card-body letra-arial">
                <div class="row">
                    <div class="col-sm-12 centrar-texto letra20"><b>COMPROBANTE DE DIARIO</b></div>
                </div>
                <div class="row">
                    <div class="col-sm-12 centrar-texto letra20">No {{ $diario->diario_codigo }}</div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-sm-6 letra15 ">
                        <div class="row">
                            <div class="col-sm-4">
                                <b>Fecha :</b>
                            </div>
                            <div class="col-sm-8">
                                {{ DateTime::createFromFormat('Y-m-d', $diario->diario_fecha)->format('d/m/Y') }}
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 letra15">
                        <div class="row">
                            <div class="col-sm-5">
                                <b>Tipo Documento :</b>
                            </div>
                            <div class="col-sm-7">
                                {{ $diario->diario_tipo_documento }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6 letra15 ">
                        <div class="row">
                            <div class="col-sm-4">
                                <b>Referencia :</b>
                            </div>
                            <div class="col-sm-8">
                                {{ $diario->diario_beneficiario }}
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 letra15">
                        <div class="row">
                            <div class="col-sm-5">
                                <b>Documento No:</b>
                            </div>
                            <div class="col-sm-7">
                                {{ $diario->diario_numero_documento }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12 letra15 ">
                        <div class="row">
                            <div class="col-sm-2">
                                <b>Concepto :</b>
                            </div>
                            <div class="col-sm-10">
                                {{ $diario->diario_comentario }}
                            </div>
                        </div>
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-sm-12 letra13">
                        <table class="table" style="white-space: normal!important; border-collapse: collapse;">
                            <thead>
                                <tr class="neo-fondo-tabla">
                                    <th class="borde-celda-top borde-celda-bottom borde-celda-left">CÓDIGO</th>
                                    <th class="borde-celda-top borde-celda-bottom">CUENTA</th>
                                    <th class="borde-celda-top borde-celda-bottom">DESCRIPCIÓN </th>
                                    <th class="centrar-texto borde-celda-top borde-celda-bottom">DEBE</th>
                                    <th class="centrar-texto borde-celda-top borde-celda-bottom borde-celda-right">HABER</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $debe = 0; $haber = 0; ?>
                                @foreach($diario->detalles->sortBy('detalle_haber') as $detalle)
                                <?php $debe = $debe + $detalle->detalle_debe; $haber = $haber + $detalle->detalle_haber; ?>
                                <tr>
                                    <td class="sin-salto">{{ $detalle->cuenta->cuenta_numero }}</td>
                                    <td class="sin-salto">{{ $detalle->cuenta->cuenta_nombre }}</td>
                                    <td>{{ $detalle->detalle_comentario }}</td>
                                    <td class="centrar-texto">{{ number_format($detalle->detalle_debe,2) }}</td>
                                    <td class="centrar-texto">{{ number_format($detalle->detalle_haber,2) }}</td>
                                </tr>
                                @endforeach
                                <tr class="letra13">
                                    <td class="borde-celda-top" colspan="2"></td>
                                    <td class="neo-fondo-tabla borde-celda-top borde-celda centrar-texto">TOTAL</td>
                                    <td class="neo-fondo-tabla borde-celda-top borde-celda centrar-texto">{{ number_format($debe,2) }}</td>
                                    <td class="neo-fondo-tabla borde-celda-top borde-celda centrar-texto">{{ number_format($haber,2) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection