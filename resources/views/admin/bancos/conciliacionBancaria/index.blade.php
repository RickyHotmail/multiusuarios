@extends ('admin.layouts.admin')
@section('principal')
<form id="idForm" class="form-horizontal" method="POST" action="{{ url("conciliacionBancaria") }}" onsubmit="return verificarFecha();">

@csrf
    <div class="card card-secondary"  style="position: absolute; width: 100%">
        <div class="card-header">
            <h3 class="card-title">Conciliación Bancaria</h3>
            <div class="float-right">
                <button type="submit"  id="guardar" name="guardar" class="invisible"><i class="fa fa-trash"></i></button>
                <button type="button" onclick="verificarFechaGuardar();" id="guardarAux" name="guardarAux" class="btn btn-success btn-sm" data-toggle="tooltip" data-placement="top" title="Guardar Conciliación"><i class="fa fa-save"></i>&nbsp;Guardar</button>
            </div>
        </div>
        
        <div class="card-body">
            <div class="row">
                <div class="col-lg-12 col-md-12">
                    <div class="form-group row">
                        <label for="idBanco" class="col-lg-1 col-md-1 col-form-label">Banco :</label>
                        <div class="col-lg-2 col-md-2">
                            <select class="custom-select" id="banco_id" name="banco_id" onclick="cargarCuenta();"
                                required>
                                <option value="" label>--Seleccione una opcion--</option>
                                @foreach($bancos as $banco)
                                <option value="{{$banco->banco_id}}" @if(isset($bancoC))  @if($banco->banco_id == $bancoC->banco_id) selected @endif @endif>{{$banco->bancoLista->banco_lista_nombre}}</option>
                                @endforeach
                            </select>
                        </div>
                        <label for="idBanco" class="col-lg-1 col-md-1 col-form-label">Cuenta :</label>
                        <div class="col-lg-2 col-md-2">
                            <select class="custom-select" id="cuenta_id" name="cuenta_id"
                                required>
                                <option value="" label>--Seleccione una opcion--</option>
                                @if(isset($cuentaBancaria)) <option value="{{ $cuentaBancaria->cuenta_bancaria_id }}" selected>{{ $cuentaBancaria->cuenta_bancaria_numero }}</option> @endif
                            </select>
                        </div>
                        <div class="col-md-4">
                            <div class="row">
                                <label for="idDesde" class="col-lg-2 col-md-2 col-form-label">
                                    <center>Desde :</center>
                                </label>
                                <div class="col-lg-4 col-md-4">
                                    <input type="date" class="form-control" id="idDesde" name="idDesde" 
                                    @if(isset($fechaI)) value='{{ $fechaI }}'  @else 
                                    value='<?php echo(date("Y")."-".date("m")."-".date("d")); ?>' @endif required>
                                </div>
                                <label for="idHasta" class="col-lg-2 col-md-2 col-form-label">
                                    <center>Hasta :</center>
                                </label>
                                <div class="col-lg-4 col-md-4">
                                    <input type="date" class="form-control" id="idHasta" name="idHasta"
                                    @if(isset($fechaF)) value='{{ $fechaF }}'  @else 
                                    value='<?php echo(date("Y")."-".date("m")."-".date("d")); ?>' @endif required>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" id="validarRango" value="@if(isset($isIncomplete)) @if($isIncomplete) 0 @else 1 @endif @else 1 @endif">
                        <input type="hidden" id="chequesMissing" value="@if(isset($chequesMissing)) {{$chequesMissing}} @endif">
                        <div class="col-md-2 centrar-texto">
                            <button type="submit" id="buscar" name="buscar" class="btn btn-primary" data-toggle="tooltip" data-placement="top" title="Buscar"><i class="fa fa-search"></i></button>
                            <button onclick="setTipo('&excel=descarga')" type="submit" id="excel" name="excel" class="btn btn-success" data-toggle="tooltip" data-placement="top" title="Enviar a Excel" value="fdds"><i class="fas fa-file-excel"></i></button>
                            <button onclick="setTipo('&pdf=descarga')" type="submit" id="pdf" name="pdf" class="btn btn-secondary"><i class="fas fa-print"></i></button>
                        </div>
                    </div>
                    <div class=" row">
                        <div class="col-lg-5 col-md-5">
                            <div class=" row"><label for="idDesde" class="col-lg-7 col-md-7 col-form-label">&nbsp;&nbsp;</label></div>
                            <div class=" row">
                                <label for="idDesde" class="col-lg-7 col-md-7 col-form-label">&nbsp;&nbsp;Saldo Anterior Contable :</label>
                                <div class="col-lg-5 col-md-5">
                                    <input type="text" class="form-control derecha-texto" @if(isset($saldoAnteriorContable)) value='{{ number_format($saldoAnteriorContable,2) }}' @else value='0.00' @endif readonly>
                                </div>
                            </div>
                            <div class=" row">
                                <label for="idDesde" class="col-lg-7 col-md-7 col-form-label">&nbsp;&nbspSaldo inicial estado de cuenta :</label>
                                <div class="col-lg-5 col-md-5">
                                    <input type="text" class="form-control derecha-texto" @if(isset($saldoanterior)) value='{{ number_format($saldoanterior,2) }}' @else value='0.00' @endif readonly>
                                </div>
                            </div>
                            <hr>
                            <div class=" row">
                                <label for="idDesde" class="col-lg-7 col-md-7 col-form-label">&nbsp;&nbsp;Cheques Girados y No Cobrados :</label>
                                <div class="col-lg-5 col-md-5">
                                    <input type="text" class="form-control derecha-texto" @if(isset($chequeGiradoNoCobrado)) value='{{ number_format($chequeGiradoNoCobrado,2) }}' @else value='0.00' @endif readonly>
                                </div>
                            </div>
                            <hr>
                            <div class=" row">
                                <label for="idDesde" class="col-lg-7 col-md-7 col-form-label">&nbsp;&nbsp;DEPOSTIOS / CREDITOS :</label>
                                <div class="col-lg-5 col-md-5">
                                    <input type="text" class="form-control derecha-texto"@if(isset($credito)) value='{{ number_format($credito,2) }}' @else value='0.00' @endif readonly>
                                </div>
                            </div>
                            <div class=" row">
                                <label for="idDesde" class="col-lg-7 col-md-7 col-form-label">&nbsp;&nbsp;CHEQUES / DEBITOS :</label>
                                <div class="col-lg-5 col-md-5">
                                    <input type="text" class="form-control derecha-texto"@if(isset($debito))  value='{{ number_format($debito,2) }}' @else value='0.00' @endif  readonly>
                                </div>
                            </div>
                            <hr>
                            <div class=" row">
                                <label for="idDesde" class="col-lg-7 col-md-7 col-form-label">&nbsp;&nbsp;Saldo Contable Actual :</label>
                                <div class="col-lg-5 col-md-5">
                                    <input type="text" class="form-control derecha-texto" @if(isset($saldoContableActual)) value='{{ number_format($saldoContableActual,2) }}' @else value='0.00' @endif readonly>
                                </div>
                            </div>
                            <div class=" row">
                                <label for="idDesde" class="col-lg-7 col-md-7 col-form-label">&nbsp;&nbsp;Saldo Estado de Cuenta :</label>
                                <div class="col-lg-5 col-md-5">
                                    <input type="text" class="form-control derecha-texto" @if(isset($saldoEstadoCuenta)) value='{{ number_format($saldoEstadoCuenta,2) }}' @else value='0.00' @endif readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-7 col-md-7">
                            <div class="row">
                                <div class="col-lg-12 col-md-12">
                                    <div class="row">
                                        <label for="idDesde" class="col-lg-4 col-md-4 col-form-label"></label>
                                        <div class="col-lg-8 col-md-8">
                                            <div class="row">
                                                <div class="col-lg-4 col-md-4"><center><label for="idDesde">Conciliado</label></center></div>
                                                <div class="col-lg-4 col-md-4"><center><label for="idDesde">No Conciliado</label></center></div>
                                                <div class="col-lg-4 col-md-4"><center><label for="idDesde">Conciliado Otros</label></center></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12 col-md-12">
                                    <div class="row">
                                        <div class="col-lg-1 col-md-1">
                                        </div>
                                        <label for="idDesde" class="col-lg-3 col-md-3 col-form-label">&nbsp;&nbsp;+ Depositos :</label>
                                        <div class="col-lg-8 col-md-8">
                                            <div class="row">
                                                <div class="col-lg-4 col-md-4">
                                                    <input type="text" class="form-control derecha-texto" @if(isset($depositosConciliados)) value='{{ number_format($depositosConciliados,2) }}' @else value='0.00' @endif readonly>
                                                </div>
                                                <div class="col-lg-4 col-md-4">
                                                    <input type="text" class="form-control derecha-texto" @if(isset($depositosNoConciliados)) value='{{ number_format($depositosNoConciliados,2) }}' @else value='0.00' @endif readonly>
                                                </div>
                                                <div class="col-lg-4 col-md-4">
                                                    <input type="text" class="form-control derecha-texto" @if(isset($depositosConciliadosOtros)) value='{{ number_format($depositosConciliadosOtros,2) }}' @else value='0.00' @endif readonly>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12 col-md-12">
                                    <div class="row">
                                        <div class="col-lg-1 col-md-1">
                                        </div>
                                        <label for="idDesde" class="col-lg-3 col-md-3 col-form-label">&nbsp;&nbsp;+ Notas de Crédito :</label>
                                        <div class="col-lg-8 col-md-8">
                                            <div class="row">
                                                <div class="col-lg-4 col-md-4">
                                                    <input type="text" class="form-control derecha-texto" @if(isset($ncConciliado)) value='{{ number_format($ncConciliado,2) }}' @else value='0.00' @endif readonly>
                                                </div>
                                                <div class="col-lg-4 col-md-4">
                                                    <input type="text" class="form-control derecha-texto" @if(isset($ncNoConciliado)) value='{{ number_format($ncNoConciliado,2) }}' @else value='0.00' @endif readonly>
                                                </div>
                                                <div class="col-lg-4 col-md-4">
                                                <input type="text" class="form-control derecha-texto" @if(isset($ncConciliadoOtros)) value='{{ number_format($ncConciliadoOtros,2) }}' @else value='0.00' @endif readonly>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12 col-md-12">
                                    <div class="row">
                                        <div class="col-lg-1 col-md-1">
                                        </div>
                                        <label for="idDesde" class="col-lg-3 col-md-3 col-form-label">&nbsp;&nbsp;- Notas de Débito :</label>
                                        <div class="col-lg-8 col-md-8">
                                            <div class="row">
                                                <div class="col-lg-4 col-md-4">
                                                    <input type="text" class="form-control derecha-texto" @if(isset($ndConciliado)) value='{{ number_format($ndConciliado,2) }}' @else value='0.00' @endif readonly>
                                                </div>
                                                <div class="col-lg-4 col-md-4">
                                                    <input type="text" class="form-control derecha-texto" @if(isset($ndNoConciliado)) value='{{ number_format($ndNoConciliado,2) }}' @else value='0.00' @endif readonly>    
                                                </div>
                                                <div class="col-lg-4 col-md-4">
                                                    <input type="text" class="form-control derecha-texto" @if(isset($ndConciliadoOtros)) value='{{ number_format($ndConciliadoOtros,2) }}' @else value='0.00' @endif readonly>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12 col-md-12">
                                    <div class="row">
                                        <div class="col-lg-1 col-md-1">
                                        </div>
                                        <label for="idDesde" class="col-lg-3 col-md-3 col-form-label">&nbsp;&nbsp;- Cheques Egresos :</label>
                                        <div class="col-lg-8 col-md-8">
                                            <div class="row">
                                                <div class="col-lg-4 col-md-4">
                                                    <input type="text" class="form-control derecha-texto" @if(isset($chequesConciliados)) value='{{ number_format($chequesConciliados,2) }}' @else value='0.00' @endif readonly>
                                                </div>
                                                <div class="col-lg-4 col-md-4">
                                                    <input type="text" class="form-control derecha-texto" @if(isset($chequesNoConciliados)) value='{{ number_format($chequesNoConciliados,2) }}' @else value='0.00' @endif readonly>
                                                </div>
                                                <div class="col-lg-4 col-md-4">
                                                    <input type="text" class="form-control derecha-texto" @if(isset($chequesConciliadosOtros)) value='{{ number_format($chequesConciliadosOtros,2) }}' @else value='0.00' @endif readonly>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12 col-md-12">
                                    <div class="row">
                                        <div class="col-lg-1 col-md-1">
                                        </div>
                                        <label for="idDesde" class="col-lg-3 col-md-3 col-form-label">&nbsp;&nbsp;- Transferencias Egresos :</label>
                                        <div class="col-lg-8 col-md-8">
                                            <div class="row">
                                                <div class="col-lg-4 col-md-4">
                                                    <input type="text" class="form-control derecha-texto" @if(isset($transferenciasEgresosConciliadas)) value='{{ number_format($transferenciasEgresosConciliadas,2) }}' @else value='0.00' @endif readonly>
                                                </div>
                                                <div class="col-lg-4 col-md-4">
                                                    <input type="text" class="form-control derecha-texto" @if(isset($transferenciasEgresosNoConciliadas)) value='{{ number_format($transferenciasEgresosNoConciliadas,2) }}' @else value='0.00' @endif readonly>
                                                </div>
                                                <div class="col-lg-4 col-md-4">
                                                    <input type="text" class="form-control derecha-texto" @if(isset($transferenciasEgresosConciliadasOtros)) value='{{ number_format($transferenciasEgresosConciliadasOtros,2) }}' @else value='0.00' @endif readonly>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12 col-md-12">
                                    <div class="row">
                                        <div class="col-lg-1 col-md-1">
                                        </div>
                                        <label for="idDesde" class="col-lg-3 col-md-3 col-form-label">&nbsp;&nbsp;+ Transferencias Ingresos :</label>
                                        <div class="col-lg-8 col-md-8">
                                            <div class="row">
                                                <div class="col-lg-4 col-md-4">
                                                    <input type="text" class="form-control derecha-texto" @if(isset($transferenciaIngresosConciliados)) value='{{ number_format($transferenciaIngresosConciliados,2) }}' @else value='0.00' @endif readonly>
                                                </div>
                                                <div class="col-lg-4 col-md-4">
                                                    <input type="text" class="form-control derecha-texto" @if(isset($transferenciaIngresosNoConciliados)) value='{{ number_format($transferenciaIngresosNoConciliados,2) }}' @else value='0.00' @endif readonly>
                                                </div>
                                                <div class="col-lg-4 col-md-4">
                                                    <input type="text" class="form-control derecha-texto" @if(isset($transferenciaIngresosConciliadosOtros)) value='{{ number_format($transferenciaIngresosConciliadosOtros,2) }}' @else value='0.00' @endif readonly>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <hr>
            <?php 
                $idCol = 0;
                $idCol2 = 0;
                $saldo = 0; 
                if(isset($saldoAnteriorContable)){
                    $saldo = $saldoAnteriorContable;
                }
            ?>               
            <div class="card-body table-responsive p-0" style="height: 400px;">
                <table id="tree-table" class="table table-head-fixed text-nowrap">
                    <thead>
                        <tr class="text-center">                            
                            <th>Fecha</th>                           
                            <th>Tipo</th>
                            <th>Numero</th>
                            <th>Crédito</th>
                            <th>Débito</th>
                            <th>Conc.</th>
                            <th>Fecha Conc.</th>
                            <th>Saldo</th>
                            <th>Diario</th>                            
                            <th class="text-left">Referencia</th>                                               
                        </tr>
                    </thead>
                    <tbody>                   
                        <tr> 
                            @if(isset($saldoAnteriorContable))
                                <td colspan="7" class="text-center">SALDO ANTERIOR</td>                           
                                <td class="text-right">{{ number_format($saldoAnteriorContable,2) }}</td>                            
                                <td colspan="2"></td>
                            @endif
                        </tr>
                        @if(isset($conciliacionBancariaMatriz))
                            @for ($i = 0; $i < count($conciliacionBancariaMatriz); ++$i)    
                                @if(empty($conciliacionBancariaMatriz[$i]['idCol']) == false)
                                <?php $idCol = $i+1; ?>
                                <tr data-id="{{$i+1}}" data-parent="0" data-level="1">
                                    <td data-column="name" colspan="10">
                                        {{$conciliacionBancariaMatriz[$i]['idCol']}}
                                    </td>
                                </tr>
                                @endif
                                <?php $saldo = $saldo + $conciliacionBancariaMatriz[$i]['credito'] - $conciliacionBancariaMatriz[$i]['debito']; ?>       
                                <tr data-parent="{{$idCol}}" data-level="2" class="text-center" @if($conciliacionBancariaMatriz[$i]['conciliacion']) style="background:  #c7f0e4;" @endif>                                
                                    <td>{{ $conciliacionBancariaMatriz[$i]['fecha'] }}</td>                                
                                    <td>{{ $conciliacionBancariaMatriz[$i]['tipo'] }}</td>
                                    <td>{{ $conciliacionBancariaMatriz[$i]['numero']}}</td>
                                    <td class="text-right">{{ number_format($conciliacionBancariaMatriz[$i]['credito'],2)}}</td>
                                    <td class="text-right">{{ number_format($conciliacionBancariaMatriz[$i]['debito'],2)}}</td>
                                    <td>
                                        <input type="hidden" name="idonciliacion[]" value="{{$conciliacionBancariaMatriz[$i]['id'].'-'.$conciliacionBancariaMatriz[$i]['tabla']}}"/>
                                        @if($conciliacionBancariaMatriz[$i]['bloqueo'] == false) 
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="chk-{{ $conciliacionBancariaMatriz[$i]['tabla'] }}-{{ $conciliacionBancariaMatriz[$i]['id'] }}" name="chkConciliacion[]" value="{{$conciliacionBancariaMatriz[$i]['id'].'-'.$conciliacionBancariaMatriz[$i]['tabla']}}" @if($conciliacionBancariaMatriz[$i]['conciliacion'] == true) checked @endif>
                                            <label for="chk-{{ $conciliacionBancariaMatriz[$i]['tabla'] }}-{{ $conciliacionBancariaMatriz[$i]['id'] }}" class="custom-control-label"></label>
                                        </div>
                                        @endif
                                    </td> 
                                    <td>{{ $conciliacionBancariaMatriz[$i]['fechaConsiliacion']}}</td>   
                                    <td class="text-right">$ {{ number_format($saldo,2)}}</td>                           
                                    <td>
                                        @if(is_array($conciliacionBancariaMatriz[$i]['diario']))
                                            @for($cd = 0; $cd < count($conciliacionBancariaMatriz[$i]['diario']); $cd++)
                                                <a href="{{ url("asientoDiario/ver/{$conciliacionBancariaMatriz[$i]['diario'][$cd]}")}}" target="_blank">{{ $conciliacionBancariaMatriz[$i]['diario'][$cd]}}</a> - 
                                            @endfor
                                        @else
                                            <a href="{{ url("asientoDiario/ver/{$conciliacionBancariaMatriz[$i]['diario']}")}}" target="_blank">{{ $conciliacionBancariaMatriz[$i]['diario']}}</a>
                                        @endif
                                    </td>                                
                                    <td class="text-left">{{ $conciliacionBancariaMatriz[$i]['referencia']}}</td>                            
                                </tr>
                            @endfor
                        @endif
                    </tbody>
                </table>
            </div>
            <hr>
            <center><h3><b>POR CONCILIAR EN OTROS MESES</b><h3></center>
            <div class="card-body table-responsive p-0" style="height: 400px;">
            <table id="tree-table2" class="table table-head-fixed text-nowrap">
                <thead>
                    <tr class="text-center">
                        <th>Fecha</th>
                        <th>Tipo</th>
                        <th>Numero</th>
                        <th>Crédito</th>
                        <th>Débito</th>
                        <th>Cons.</th>
                        <th>Fecha Conc.</th>
                        <th>Saldo</th>
                        <th>Diario</th>                        
                        <th class="text-left">Referencia</th>                                               
                    </tr>
                </thead>
                <tbody>
                @if(isset($otrasconciliacionesBancariaMatriz))
                        @for ($c = 0; $c < count($otrasconciliacionesBancariaMatriz); ++$c) 
                            @if(empty($otrasconciliacionesBancariaMatriz[$c]['idCol']) == false)
                                <?php $idCol2 = $c+1; ?>
                                <tr data-id="{{$c+1}}" data-parent="0" data-level="1">
                                    <td data-column="name" colspan="10" style="cursor: pointer;">
                                        {{$otrasconciliacionesBancariaMatriz[$c]['idCol']}}
                                    </td>
                                </tr>
                            @endif     
                        <?php $saldo = $saldo + $otrasconciliacionesBancariaMatriz[$c]['credito'] - $otrasconciliacionesBancariaMatriz[$c]['debito']; ?>         
                        <tr data-parent="{{$idCol2}}" data-level="2" class="text-center" @if($otrasconciliacionesBancariaMatriz[$c]['conciliacion']) style="background:  #c7f0e4;" @endif>                            
                            <td>{{ $otrasconciliacionesBancariaMatriz[$c]['fecha'] }}</td>                            
                            <td>{{ $otrasconciliacionesBancariaMatriz[$c]['tipo'] }}</td>
                            <td>{{ $otrasconciliacionesBancariaMatriz[$c]['numero']}}</td>
                            <td class="text-right">{{ number_format($otrasconciliacionesBancariaMatriz[$c]['credito'],2)}}</td>
                            <td class="text-right">{{ number_format($otrasconciliacionesBancariaMatriz[$c]['debito'],2)}}</td>
                            <td>
                                <input type="hidden" name="idonciliacionOtros[]" value="{{$otrasconciliacionesBancariaMatriz[$c]['id'].'-'.$otrasconciliacionesBancariaMatriz[$c]['tabla']}}"/>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="chk2-{{ $otrasconciliacionesBancariaMatriz[$c]['tabla'] }}-{{ $otrasconciliacionesBancariaMatriz[$c]['id'] }}" name="chkConciliacionOtros[]" value="{{$otrasconciliacionesBancariaMatriz[$c]['id'].'-'.$otrasconciliacionesBancariaMatriz[$c]['tabla']}}" @if($otrasconciliacionesBancariaMatriz[$c]['conciliacion'] == true) checked @endif >
                                    <label for="chk2-{{ $otrasconciliacionesBancariaMatriz[$c]['tabla'] }}-{{ $otrasconciliacionesBancariaMatriz[$c]['id'] }}" class="custom-control-label"></label>
                                </div>
                            </td>
                            <td>{{ $otrasconciliacionesBancariaMatriz[$c]['fechaConsiliacion']}}</td>    
                            <td class="text-right">$ {{ number_format($saldo,2)}}</td>                                  
                            <td>
                                @if(is_array($otrasconciliacionesBancariaMatriz[$c]['diario']))
                                    @for($cd = 0; $cd < count($otrasconciliacionesBancariaMatriz[$c]['diario']); $cd++)
                                        <a href="{{ url("asientoDiario/ver/{$otrasconciliacionesBancariaMatriz[$c]['diario'][$cd]}")}}" target="_blank">{{ $otrasconciliacionesBancariaMatriz[$c]['diario'][$cd]}}</a> - 
                                    @endfor
                                @else
                                    <a href="{{ url("asientoDiario/ver/{$otrasconciliacionesBancariaMatriz[$c]['diario']}")}}" target="_blank">{{ $otrasconciliacionesBancariaMatriz[$c]['diario']}}</a>
                                @endif
                            </td>                    
                            <td class="text-left">{{ $otrasconciliacionesBancariaMatriz[$c]['referencia']}}</td>                            
                        </tr>
                        @endfor
                    @endif                    
                </tbody>
            </table>
            </div>
            <hr>
            <center><h3><b>CHEQUES ANULADOS</b><h3></center>
            <div class="card-body table-responsive p-0" style="height: 400px;">
            <table id="tree-table2" class="table table-head-fixed text-nowrap">
                <thead>
                    <tr class="text-center">
                        <th>Fecha</th>                        
                        <th>Numero</th>
                        <th>Valor</th>
                        <th>Beneficiario</th>                                         
                        <th class="text-left">Referencia</th>                                               
                    </tr>
                </thead>
                <tbody>
                @if(isset($chequesAnulados))
                    @for ($c = 0; $c < count($chequesAnulados); ++$c) 
                    <tr class="text-center">                       
                        <td >{{ $chequesAnulados[$c]['fecha']}}</td>
                        <td >{{ $chequesAnulados[$c]['numero']}}</td>
                        <td >{{ number_format($chequesAnulados[$c]['valor'],2)}}</td>
                        <td >{{ $chequesAnulados[$c]['beneficiario']}}</td> 
                        <td class="text-left">{{ $chequesAnulados[$c]['referencia']}}</td>
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
                if(tipo=="") return
                var form = $(this);
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
    function verificarFecha() {
        let fechaHasta = new Date(document.getElementById("idHasta").value+"T00:00:00");
        let lastDay = new Date(fechaHasta.getFullYear(), fechaHasta.getMonth() + 1, 0).getDate();
        if(fechaHasta.getDate() != lastDay){
            bootbox.alert({
                message: "La fecha hasta debe ser el ultimo dia del mes.",
                size: 'small'
            });
            return false;
        }
        //calculate_load_times();
        girarGif();
        return true;
    }

    function verificarFechaGuardar() {
        $("#guardar").click();
        /*if(document.getElementById("validarRango").value.trim() == '0'){
            bootbox.alert({
                message: "Los siguientes numero de cheques no se encuentran registrados, registre los cheques faltantes para poder conciliar. <br>"+document.getElementById("chequesMissing").value,
                size: 'large'
            });
        }else{
            $("#guardar").click();
        }*/
    }

    function cargarCuenta(){
        $.ajax({
            url: '{{ url("cuentaBancaria/searchN") }}'+ '/' +document.getElementById("banco_id").value,
            dataType: "json",
            type: "GET",
            data: {
                buscar: document.getElementById("banco_id").value
            },
            success: function(data){
                document.getElementById("cuenta_id").innerHTML = "<option value='' label>--Seleccione una opcion--</option>";
                for (var i=0; i<data.length; i++) {
                    document.getElementById("cuenta_id").innerHTML += "<option value='"+data[i].cuenta_bancaria_id+"'>"+data[i].cuenta_bancaria_numero+"</option>";
                }           
            },
        });
    }
</script>
@endsection