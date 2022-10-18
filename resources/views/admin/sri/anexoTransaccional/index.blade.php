@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">Anexo Transaccional</h3>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <form class="form-horizontal" method="POST" action="{{ url("atsSRI") }} ">
        @csrf
            <div class="form-group row">
                <label for="idDesde" class="col-sm-1 col-form-label"><center>Periodo:</center></label>
                <div class="col-sm-2">
                    <input type="date" class="form-control" id="idPeriodo" name="idPeriodo"  value='<?php if(isset($fecha)){echo $fecha;}else{ echo(date("Y")."-".date("m")."-".date("d"));} ?>' required>
                </div>                          
                <div class="col-sm-3">
                    <button type="submit" id="ver" name="ver" class="btn btn-info"><i class="fas fa-eye"></i> Ver</button>
                    <button type="submit" id="generar" name="generar" class="btn btn-primary"><i class="fas fa-cog"></i> Generar XML</button>
                    <button type="submit" id="pdf" name="pdf" class="btn btn-secondary"><i class="fas fa-print"></i> Imprimir</button>
                </div>
            </div>            
        </form>
        <hr>
        <table class="table table-bordered table-hover sin-salto">
            <thead>
                <tr><th  class="text-center letra-blanca fondo-azul-claro"colspan="7">COMPRAS</th></tr>
                <tr class="text-center fondo-celeste">
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
                    <tr class="text-center">
                        @if($tabla1[$i]['tra'] == 'TOTAL')
                            <td colspan="3"><b>{{ $tabla1[$i]['tra'] }}</b></td>
                            <td><b>{{ number_format($tabla1[$i]['0'],2) }}</b></td>
                            <td><b>{{ number_format($tabla1[$i]['12'],2) }}</b></td>
                            <td><b>0.00</b></td>
                            <td><b> {{ number_format($tabla1[$i]['iva'],2) }}</b></td>
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
        <table class="table table-bordered table-hover sin-salto">
            <thead>
                <tr><th  class="text-center letra-blanca fondo-azul-claro"colspan="7">VENTAS</th></tr>
                <tr class="text-center fondo-celeste">
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
                    <tr class="text-center">
                        @if($tabla2[$i]['tra'] == 'TOTAL')
                            <td colspan="3"><b>{{ $tabla2[$i]['tra'] }}</b></td>
                            <td><b>{{ number_format($tabla2[$i]['0'],2) }}</b></td>
                            <td><b>{{ number_format($tabla2[$i]['12'],2) }}</b></td>
                            <td><b>0.00</b></td>
                            <td><b> {{ number_format($tabla2[$i]['iva'],2) }}</b></td>
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
        <div style="background: #C2C1C1; color: #000;" class="form-control centrar-texto"><b>RESUMEN DE RETENCIONES</b></div>
        <br>
        <table class="table table-bordered table-hover sin-salto">
            <thead>
                <tr><th  class="text-center letra-blanca fondo-azul-claro"colspan="7">RETENCION EN LA FUENTE DE IMPUESTO A LA RENTA</th></tr>
                <tr class="text-center fondo-celeste">
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
                    <tr class="text-center">
                        @if($tabla3[$i]['tra'] == 'TOTAL')
                            <td colspan="3"><b>{{ $tabla3[$i]['tra'] }}</b></td>
                            <td><b>{{ number_format($tabla3[$i]['base'],2) }}</b></td>
                            <td><b>{{ number_format($tabla3[$i]['valor'],2) }}</b></td>
                        @else
                            <td>{{ $tabla3[$i]['cod'] }}</td>
                            <td>{{ $tabla3[$i]['tra'] }}</td>
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
        <table class="table table-bordered table-hover sin-salto">
            <thead>
                <tr><th  class="text-center letra-blanca fondo-azul-claro"colspan="7">RETENCION EN LA FUENTE DE IVA</th></tr>
                <tr class="text-center fondo-celeste">
                    <th>Operación</th>
                    <th>Concepto de Retención</th>
                    <th>Valor Retenido</th>
                </tr>
            </thead>
            <tbody>
                @if(isset($tabla4))
                    @for ($i = 1; $i <= count($tabla4); ++$i)    
                    <tr class="text-center">
                        @if($tabla4[$i]['tra'] == 'TOTAL')
                            <td colspan="2"><b>{{ $tabla4[$i]['tra'] }}</b></td>
                            <td><b>{{ number_format($tabla4[$i]['valor'],2) }}</b></td>
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
        <table class="table table-bordered table-hover sin-salto">
            <thead>
                <tr><th  class="text-center letra-blanca fondo-azul-claro"colspan="7">RESUMEN DE RETENCIONES QUE LE EFECTUARON EN EL PERIODO</th></tr>
                <tr class="text-center fondo-celeste">
                    <th>Operación</th>
                    <th>Concepto de Retención</th>
                    <th>Valor Retenido</th>
                </tr>
            </thead>
            <tbody>
                @if(isset($tabla5))
                    @for ($i = 1; $i <= count($tabla5); ++$i)    
                    <tr class="text-center">
                        @if($tabla5[$i]['tra'] == 'TOTAL')
                            <td colspan="2"><b>{{ $tabla5[$i]['tra'] }}</b></td>
                            <td><b>{{ number_format($tabla5[$i]['valor'],2) }}</b></td>
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
    </div>
    <!-- /.card-body -->
</div>
<!-- /.card -->
@endsection