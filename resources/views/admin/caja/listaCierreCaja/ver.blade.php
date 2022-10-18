@extends ('admin.layouts.admin')
@section('principal')
    <div class="card card-secondary">
        <div class="card-header">
            <h3 class="card-title">Ver Cierre de Caja</h3>
             <!--  
            <button onclick='window.location = "{{ url("listaCierreCaja") }}";' class="btn btn-default btn-sm float-right"><i
                    class="fa fa-undo"></i>&nbsp;Atras</button>
                    --> 
            <button  type="button" onclick="history.back()" class="btn btn-default btn-sm float-right"><i class="fa fa-undo"></i>&nbsp;Atras</button>
        </div>
        <div class="card-body">
            <h5 class="form-control" style="color:#fff; background:#17a2b8;">Datos de Movimientos de Caja</h5>
            <div class="form-group row">
                <div class="col-sm-6">
                    <h5 class="form-control" style="color:#fff; background:#17a2b8;"><CENTER>MOVIMIENTOS DE CAJA</CENTER></h5>
                    <div class="card-body table-responsive p-0" style="height: 300px;">
                        <table class="table table-head-fixed text-nowrap">
                            <thead>
                                <tr>
                                <th class="text-center">Fecha</th>
                                <th class="text-center">Valor</th>
                                <th class="text-center">Saldo Actual</th>
                                <th class="text-center">Descripcion</th>
                                @if(Auth::user()->empresa->empresa_contabilidad == '1')
                                <th class="text-center">Diario</th>
                                @endif
                                </tr>
                            </thead>
                            <tbody>                           
                                @if(isset($datos))
                                    @for ($i = 1; $i <= count($datos); ++$i)                                                                
                                        <tr class="text-center">                                        
                                            <td class="text-center">{{ $datos[$i]['Fecha'] }}</td>
                                            <td class="text-center">@if(is_numeric($datos[$i]['Valor'])) {{ number_format($datos[$i]['Valor'],2,'.','') }} @endif</td>
                                            <td class="text-rigth">@if(is_numeric($datos[$i]['Saldo'])) {{ number_format($datos[$i]['Saldo'],2,'.','') }} @endif</td>
                                            <td class="text-center">{{ $datos[$i]['Descripcion'] }}</td>  
                                            @if(Auth::user()->empresa->empresa_contabilidad == '1')                                   
                                            <td class="text-center"><a href="{{ url("asientoDiario/ver/{$datos[$i]['Diario']}") }}" target="_blank">{{ $datos[$i]['Diario'] }}</a></td></td>
                                            @endif  
                                        </tr>                         
                                    @endfor
                                @endif
                            </tbody>
                        </table>
                    </div>
                    <div class="form-group row">
                    </div>
                    <div class="form-group row">                        
                        <div class="col-sm-9">                           
                        </div> 
                        <label for="idCaja" class="col-sm-1 col-form-label">Total</label>
                        <div class="col-sm-2">                            
                            <input type="text" id="idSaldoMovimiento" class="form-control" value='{{number_format($saldoActualmovimiento,2,'.','')}}' readonly>
                        </div> 
                    </div>
                </div>
                @if(Auth::user()->empresa->empresa_contabilidad == '1')
                <div class="col-sm-6">
                    <h5 class="form-control" style="color:#fff; background:#17a2b8;"><CENTER>MOVIMIENTOS CONTABLE DE CAJA</CENTER></h5>
                    <div class="card-body table-responsive p-0" style="height: 300px;">
                        <table class="table table-head-fixed text-nowrap">
                            <thead>
                                <tr>
                                <th class="text-center">Fecha</th>
                                <th class="text-center">Debe</th>
                                <th class="text-center">Haber</th>
                                <th class="text-center">Saldo Actual</th>
                                <th class="text-center">Descripcion</th>
                                <th class="text-center">Diario</th>
                                </tr>
                            </thead>
                            <tbody>                           
                                @if(isset($datosDiarios))
                                    @for ($a = 1; $a <= count($datosDiarios); ++$a)                                                                
                                        <tr class="text-center">                                        
                                            <td class="text-center">{{ $datosDiarios[$a]['Fecha'] }}</td>
                                            <td class="text-center">@if(is_numeric($datosDiarios[$a]['Debe'])) {{ number_format($datosDiarios[$a]['Debe'],2,'.','') }} @endif</td>
                                            <td class="text-rigth">@if(is_numeric($datosDiarios[$a]['Haber'])) {{ number_format($datosDiarios[$a]['Haber'],2,'.','') }} @endif</td>
                                            <td class="text-rigth">@if(is_numeric($datosDiarios[$a]['Saldo'])) {{ number_format($datosDiarios[$a]['Saldo'],2,'.','')}} @endif</td> 
                                            <td class="text-center">{{ $datosDiarios[$a]['Descripcion'] }}</td>                                            
                                            <td class="text-center"><a href="{{ url("asientoDiario/ver/{$datosDiarios[$a]['Diario']}") }}" target="_blank">{{ $datosDiarios[$a]['Diario'] }}</a></td></td>
                                        </tr>                         
                                    @endfor
                                @endif
                            </tbody>
                        </table>
                    </div>
                    <div class="form-group row">
                    </div>
                    <div class="form-group row">                        
                        <div class="col-sm-9">                           
                        </div> 
                        <label for="idCaja" class="col-sm-1 col-form-label">Total</label>
                        <div class="col-sm-2">                            
                            <input type="text" id="idSaldoDiario" class="form-control" value='{{number_format($saldoActualdiario,2,'.','')}}' readonly>
                        </div> 
                    </div>
                </div>
                @endif
            </div>            
            <h5 class="form-control" style="color:#fff; background:#17a2b8;">Datos de Cierre</h5>
            <div class="form-group row">
                <div class="col-sm-4">
                    <h5 class="form-control" style="color:#fff; background:#17a2b8;"><CENTER>DATOS</CENTER></h5>
                    <div class="form-group row">
                        <label for="idFecha" class="col-sm-4 col-form-label">Fecha</label>
                        <div class="col-sm-8">
                            <input type="date" class="form-control" id="idFecha" name="idFecha" readonly
                                value='<?php echo(date("Y")."-".date("m")."-".date("d")); ?>' required>
                        </div> 
                    </div>
                    <div class="form-group row">
                        <label for="idCaja" class="col-sm-4 col-form-label">Caja</label>
                        <div class="col-sm-8">
                            <label class="form-control">{{$cierreCaja->caja->caja_nombre}}</label>
                        </div> 
                    </div>
                    <div class="form-group row">
                        <label for="idFecha" class="col-sm-4 col-form-label">Observacion</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="idMensaje" value="{{$cierreCaja->arqueo_observacion}}"
                            name="idMensaje" disabled>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="idCaja" class="col-sm-4 col-form-label">Monto</label>
                        <div class="col-sm-8">
                            <input type="number" class="form-control" id="idMonto" name="idMonto" min="0" step=".01"
                                value="{{ number_format($cierreCaja->arqueo_monto,2,'.','')}}" disabled>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="idSuma" class="col-sm-4 col-form-label">Total Monedas + Billetes</label>
                        <div class="col-sm-8">
                            <input type="number" class="form-control" id="idSuma" name="idSuma" value="{{ number_format($cierreCaja->arqueo_monto,2,'.','')}}" disabled>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <h5 class="form-control" style="color:#fff; background:#17a2b8;"><CENTER>MONEDAS</CENTER></h5>
                    <table id="cargarItem" class="table table-head-fixed text-nowrap">
                            <thead class="thead-blue">
                                <tr class="text-center" style="color:#070707;">
                                    <th>Denominación</th>
                                    <th>Cantidad</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td width="150">
                                        <CENTER>0.01 ctvos<CENTER>
                                    </td>
                                    <td>
                                        <CENTER><input type="number" class="form-controltext" id="moneda01"
                                                name="moneda01" onchange="calcularMonedas();"
                                                onkeyup="calcularMonedas();" min="0" value="{{$cierreCaja->arqueo_moneda01}}" disabled>
                                            <CENTER>
                                    </td>
                                    <td width="150">
                                        <CENTER><input type="text" id="idmoneda01" name="idmoneda01"
                                                class="form-control" value="{{ floatval($cierreCaja->arqueo_moneda01)*0.01 }}" readonly>
                                            <CENTER>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <CENTER>0.05 ctvos<CENTER>
                                    </td>
                                    <td>
                                        <CENTER><input type="number" class="form-controltext" id="moneda05"
                                                name="moneda05" onchange="calcularMonedas();"
                                                onkeyup="calcularMonedas();" min="0" value="{{$cierreCaja->arqueo_moneda05}}" disabled>
                                            <CENTER>
                                    </td>
                                    <td>
                                        <CENTER><input type="text" id="idmoneda05" class="form-control" value="{{ floatval($cierreCaja->arqueo_moneda05)*0.05 }}"
                                                readonly>
                                            <CENTER>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <CENTER>0.10 ctvos<CENTER>
                                    </td>
                                    <td>
                                        <CENTER><input type="number" class="form-controltext" id="moneda10"
                                                name="moneda10" onchange="calcularMonedas();"
                                                onkeyup="calcularMonedas();" min="0" value="{{$cierreCaja->arqueo_moneda10}}" disabled>
                                            <CENTER>
                                    </td>
                                    <td>
                                        <CENTER><input type="text" id="idmoneda10" class="form-control" value="{{ floatval($cierreCaja->arqueo_moneda10)*0.10 }}"
                                                readonly>
                                            <CENTER>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <CENTER>0.25 ctvos<CENTER>
                                    </td>
                                    <td>
                                        <CENTER><input type="number" class="form-controltext" id="moneda25"
                                                name="moneda25" onchange="calcularMonedas();"
                                                onkeyup="calcularMonedas();" min="0" value="{{$cierreCaja->arqueo_moneda25}}" disabled>
                                            <CENTER>
                                    </td>
                                    <td>
                                        <CENTER><input type="text" id="idmoneda25" class="form-control" value="{{ floatval($cierreCaja->arqueo_moneda25)*0.25 }}"
                                                readonly>
                                            <CENTER>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <CENTER>$0.50 ctvos<CENTER>
                                    </td>
                                    <td>
                                        <CENTER><input type="number" class="form-controltext" id="moneda50"
                                                name="moneda50" onchange="calcularMonedas();"
                                                onkeyup="calcularMonedas();" min="0" value="{{$cierreCaja->arqueo_moneda50}}" disabled>
                                            <CENTER>
                                    </td>
                                    <td>
                                        <CENTER><input type="text" id="idmoneda50" class="form-control" value="{{ floatval($cierreCaja->arqueo_moneda50)*0.5 }}"
                                                readonly>
                                            <CENTER>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <CENTER>$1.00 dolar<CENTER>
                                    </td>
                                    <td>
                                        <CENTER><input type="number" class="form-controltext" id="moneda1"
                                                name="moneda1" onchange="calcularMonedas();" onkeyup="calcularMonedas();"
                                                min="0" value="{{$cierreCaja->arqueo_moneda1}}" required>
                                            <CENTER>
                                    </td>
                                    <td>
                                        <CENTER><input type="text" id="idmoneda1" class="form-control" value="{{ floatval($cierreCaja->arqueo_moneda1)*1 }}"
                                                readonly>
                                            <CENTER>
                                    </td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td>
                                        <CENTER>Totales<CENTER>
                                    </td>
                                    <td width="150">
                                        <CENTER><input type="text" id="idTotalMonedas" class="form-control" value="{{ number_format($totM,2,'.','') }}"
                                                readonly>
                                            <CENTER>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                </div>
                <div class="col-sm-4">
                    <h5 class="form-control" style="color:#fff; background:#17a2b8;"><CENTER>BILLETES</CENTER></h5>
                    <table id="cargarItem" class="table table-head-fixed text-nowrap">
                            <thead class="thead-blue">
                                <tr class="text-center" style="color:#070707;">
                                    <th>Denominación</th>
                                    <th>Cantidad</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <CENTER>$1.00<CENTER>
                                    </td>
                                    <td>
                                        <CENTER><input type="number" class="form-controltext" id="billete1"
                                                name="billete1" onchange="calcularBilletes();"
                                                onkeyup="calcularBilletes();" min="0" value="{{$cierreCaja->arqueo_billete1}}" disabled></CENTER>
                                    </td>
                                    <td>
                                        <CENTER><input type="text" id="idbillete1" class="form-control" value="{{ floatval($cierreCaja->arqueo_billete1)*1 }}"
                                                readonly>
                                            <CENTER>
                                    </td>
                                </tr>
                                <tr>

                                    <td>
                                        <CENTER>$5.00<CENTER>
                                    </td>
                                    <td>
                                        <CENTER><input type="number" class="form-controltext" id="billete5"
                                                name="billete5" onchange="calcularBilletes();"
                                                onkeyup="calcularBilletes();" min="0" value="{{$cierreCaja->arqueo_billete5}}" disabled>
                                            <CENTER>
                                    </td>
                                    <td>
                                        <CENTER><input type="text" id="idbillete5" class="form-control" value="{{ floatval($cierreCaja->arqueo_billete5)*5 }}"
                                                readonly>
                                            <CENTER>
                                    </td>
                                </tr>
                                <tr>

                                    <td>
                                        <CENTER>$10.00<CENTER>
                                    </td>
                                    <td>
                                        <CENTER><input type="number" class="form-controltext" id="billete10"
                                                name="billete10" onchange="calcularBilletes();"
                                                onkeyup="calcularBilletes();" min="0" value="{{$cierreCaja->arqueo_billete10}}" disabled>
                                            <CENTER>
                                    </td>
                                    <td>
                                        <CENTER><input type="text" id="idbillete10" class="form-control" value="{{ floatval($cierreCaja->arqueo_billete10)*10 }}"
                                                readonly>
                                            <CENTER>
                                    </td>
                                </tr>
                                <tr>

                                    <td>
                                        <CENTER>$20.00<CENTER>
                                    </td>
                                    <td>
                                        <CENTER><input type="number" class="form-controltext" id="billete20"
                                                name="billete20" onchange="calcularBilletes();"
                                                onkeyup="calcularBilletes();" min="0" value="{{$cierreCaja->arqueo_billete20}}" disabled>
                                            <CENTER>
                                    </td>
                                    <td>
                                        <CENTER><input type="text" id="idbillete20" class="form-control" value="{{ floatval($cierreCaja->arqueo_billete20)*20 }}"
                                                readonly>
                                            <CENTER>
                                    </td>
                                </tr>
                                <tr>

                                    <td>
                                        <CENTER>$50.00<CENTER>
                                    </td>
                                    <td>
                                        <CENTER><input type="number" class="form-controltext" id="billete50"
                                                name="billete50" onchange="calcularBilletes();"
                                                onkeyup="calcularBilletes();" min="0" value="{{$cierreCaja->arqueo_billete50}}" disabled>
                                            <CENTER>
                                    </td>
                                    <td>
                                        <CENTER><input type="text" id="idbillete50" class="form-control" value="{{ floatval($cierreCaja->arqueo_billete50)*50 }}"
                                                readonly>
                                            <CENTER>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <CENTER>$100.00<CENTER>
                                    </td>
                                    <td>
                                        <CENTER><input type="number" class="form-controltext" id="billete100"
                                                name="billete100" onchange="calcularBilletes();"
                                                onkeyup="calcularBilletes();" min="0" value="{{$cierreCaja->arqueo_billete100}}" disabled>
                                            <CENTER>
                                    </td>
                                    <td>
                                        <CENTER><input type="text" id="idbillete100" class="form-control"
                                                value='{{ floatval($cierreCaja->arqueo_billete100)*100 }}' readonly>
                                            <CENTER>
                                    </td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td>
                                        <CENTER>Totales<CENTER>
                                    </td>
                                    <td width="150">
                                        <CENTER><input type="text" id="idTotalBilletes" class="form-control" value='{{ number_format($totB,2,'.','') }}'
                                                readonly>
                                            <CENTER>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                </div>
            </div>
        </div>
    </div>
@endsection