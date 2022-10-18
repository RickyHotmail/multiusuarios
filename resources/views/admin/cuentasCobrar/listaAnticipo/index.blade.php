@extends ('admin.layouts.admin')
@section('principal')
<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">Lista de Anticipos de Clientes</h3>
        <div class="float-right">
            <a class="btn btn-info btn-sm" href="{{ asset('admin/archivos/FORMATO_ANTICIPO_CLIENTE.xlsx') }}" download="FORMATO ANTICIPO"><i class="fas fa-file-excel"></i>&nbsp;Formato</a>
            <a class="btn btn-success btn-sm" href="{{ url("excelAnticipoCliente") }}"><i class="fas fa-file-excel"></i>&nbsp;Cargar Excel</a>
        </div>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <form class="form-horizontal" method="POST"  action="{{ url("listaAnticipoCliente") }} ">
        @csrf
            <div class="form-group row">
                <label for="idCorte" class="col-sm-1 col-form-label"><center>Fecha Corte:</center></label>
                <div class="col-sm-2">
                    <input type="date" class="form-control" id="idCorte" name="idCorte"  value='<?php if(isset($fCorte)){echo $fCorte;}else{ echo(date("Y")."-".date("m")."-".date("d"));} ?>' required>
                </div>
                <label for="nombre_cuenta" class="col-sm-1 col-form-label"><center>Cliente:</center></label>
                <div class="col-sm-5">
                    <select class="custom-select select2" id="clienteID" name="clienteID" require>
                        <option value="0" @if(isset($clienteC)) @if($clienteC == 0) selected @endif @endif>Todos</option>
                        @foreach($clientes as $cliente)
                            <option value="{{$cliente->cliente_id}}" @if(isset($clienteC)) @if($clienteC == $cliente->cliente_id) selected @endif @endif>{{$cliente->cliente_nombre}}</option>
                        @endforeach
                    </select>                    
                </div> 
                <div class="col-sm-2">
                    <div class="icheck-secondary">
                        <input type="checkbox" id="saldo_cero" name="saldo_cero" @if(isset($saldo_cero)) @if($saldo_cero == 1) checked @endif @endif>
                        <label for="saldo_cero" class="custom-checkbox"><center>Saldo Cero</center></label>
                    </div>                    
                </div>                       
                <div class="col-sm-1">
                    <button type="submit" id="buscar" name="buscar" class="btn btn-primary"><i class="fa fa-search"></i></button>
                    <button type="submit" id="pdf" name="pdf" class="btn btn-secondary"><i class="fas fa-print"></i></button>
                </div>
            </div>     
            <div class="form-group row">
                <label for="idDesde" class="col-sm-1 col-form-label"><center>Total Monto : </center></label>
                <div class="col-sm-3">
                    <input type="text" class="form-control derecha-texto" id="idMonto" name="idMonto"  value='@if(isset($monto)) {{ number_format($monto,2) }} @else 0.00 @endif' readonly>
                </div>
                <label for="idDesde" class="col-sm-1 col-form-label"><center>Total Pagado : </center></label>
                <div class="col-sm-3">
                    <input type="text" class="form-control derecha-texto" id="idPago" name="idPago"  value='@if(isset($pag)) {{ number_format($pag,2) }} @else 0.00 @endif' readonly>
                </div>
                <label for="idDesde" class="col-sm-1 col-form-label"><center>Total Saldo : </center></label>
                <div class="col-sm-3">
                    <input type="text" class="form-control derecha-texto" id="idSaldo" name="idSaldo"  value='@if(isset($saldo)) {{ number_format($saldo,2) }} @else 0.00 @endif' readonly>
                </div>
            </div>       
            <hr>
            <table id="example4" class="table table-bordered table-hover table-responsive sin-salto">
                <thead>
                    <tr class="text-center neo-fondo-tabla">
                        <th>Beneficiario</th>
                        <th>Monto</th>
                        <th>Saldo</th>
                        <th>Fecha</th>
                        <th>Pago</th>  
                        <th>Fecha Pago</th>
                        @if(Auth::user()->empresa->empresa_llevaContabilidad == '1')
                        <th>Diario</th>    
                        @endif              
                        <th>Tipo</th>
                        <th>Factura</th>
                    </tr>
                </thead>
                <tbody>
                    @if(isset($datos))
                        @for ($i = 1; $i <= count($datos); ++$i)   
                            <tr>
                                <td @if($datos[$i]['tot'] == '1') style="background:  #70B1F7;" @endif>{{ $datos[$i]['ben'] }} <input type="hidden" name="idBen[]" value="{{ $datos[$i]['ben'] }}"/><input type="hidden" name="idTot[]" value="{{ $datos[$i]['tot'] }}"/></td>
                                <td class="text-center" @if($datos[$i]['tot'] == '1') style="background:  #70B1F7;" @endif @if($datos[$i]['tot'] == '2') style="background:  #C0C4C5;" @endif>@if($datos[$i]['mon'] <> '') {{ number_format($datos[$i]['mon'],2) }} @endif <input type="hidden" name="idMon[]" value="{{ $datos[$i]['mon'] }}"/></td>
                                <td class="text-center" @if($datos[$i]['tot'] == '1') style="background:  #70B1F7;" @endif @if($datos[$i]['tot'] == '2') style="background:  #C0C4C5;" @endif>@if($datos[$i]['sal'] <> '') {{ number_format($datos[$i]['sal'],2) }} @endif <input type="hidden" name="idSal[]" value="{{ $datos[$i]['sal'] }}"/></td>
                                <td class="text-center" @if($datos[$i]['tot'] == '1') style="background:  #70B1F7;" @endif @if($datos[$i]['tot'] == '2') style="background:  #C0C4C5;" @endif>{{ $datos[$i]['fec'] }} <input type="hidden" name="idFec[]" value="{{ $datos[$i]['fec'] }}"/></td>
                                <td class="text-center" @if($datos[$i]['tot'] == '1') style="background:  #70B1F7;" @endif @if($datos[$i]['tot'] == '2') style="background:  #C0C4C5;" @endif>@if($datos[$i]['pag'] <> '') {{ number_format($datos[$i]['pag'],2) }} @endif <input type="hidden" name="idPag[]" value="{{ $datos[$i]['pag'] }}"/></td>
                                <td class="text-center" @if($datos[$i]['tot'] == '1') style="background:  #70B1F7;" @endif @if($datos[$i]['tot'] == '2') style="background:  #C0C4C5;" @endif>{{ $datos[$i]['fep'] }} <input type="hidden" name="idFep[]" value="{{ $datos[$i]['fep'] }}"/></td>
                                @if(Auth::user()->empresa->empresa_llevaContabilidad == '1')
                                <td class="text-center" @if($datos[$i]['tot'] == '1') style="background:  #70B1F7;" @endif @if($datos[$i]['tot'] == '2') style="background:  #C0C4C5;" @endif><a href="{{ url("asientoDiario/ver/{$datos[$i]['dir']}") }}" target="_blank">{{ $datos[$i]['dir'] }}</a> </td>
                                @endif     
                                <td @if($datos[$i]['tot'] == '1') style="background:  #70B1F7;" @endif @if($datos[$i]['tot'] == '2') style="background:  #C0C4C5;" @endif>{{ $datos[$i]['tip'] }} <input type="hidden" name="idTip[]" value="{{ $datos[$i]['tip'] }}"/> <input type="hidden" name="idDir[]" value="{{ $datos[$i]['dir'] }}"/></td>
                                <td class="text-center" @if($datos[$i]['tot'] == '1') style="background:  #70B1F7;" @endif @if($datos[$i]['tot'] == '2') style="background:  #C0C4C5;" @endif>{{ $datos[$i]['fac'] }} <input type="hidden" name="idFac[]" value="{{ $datos[$i]['fac'] }}"/></td>
                            </tr>
                        @endfor
                    @endif
                </tbody>
            </table>
        </form>
    </div>
    <!-- /.card-body -->
</div>
<!-- /.card -->
@endsection