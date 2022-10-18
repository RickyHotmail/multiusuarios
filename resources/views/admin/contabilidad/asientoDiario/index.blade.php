@extends ('admin.layouts.admin')
@section('principal')
<div class="row">
    <div class="col-sm-3">
        <form class="form-horizontal" method="POST" action="{{ url("asientoDiario") }} ">
        @csrf
            <div class="card card-secondary">
                <div class="card-header">
                    <h3 class="card-title">Asiento Diario</h3>
                    <button type="submit" name="buscar" class="btn btn-info btn-sm float-right"><i class="fa fa-search"></i></button>
                </div>
                <div class="card-body">
                    <div class="form-group row">
                        <label for="fecha_desde" class="col-sm-3 col-form-label">Sucursal</label>
                        <div class="col-sm-9">
                            <select class="form-control select2" id="sucursal_id" name="sucursal_id" style="width: 100%;" required>
                                <option value="" label>--Seleccione--</option>
                                @foreach($sucursales as $sucursal)
                                    <option value="{{$sucursal->sucursal_id}}"  @if(isset($sucurslaC)) @if($sucurslaC == $sucursal->sucursal_id) selected @endif @endif>{{$sucursal->sucursal_nombre}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="fecha_desde" class="col-sm-3 col-form-label">Desde</label>
                        <div class="col-sm-9">
                            <input type="date" class="form-control" id="fecha_desde" name="fecha_desde"  value='<?php if(isset($fecI)){echo $fecI;}else{ echo(date("Y")."-".date("m")."-".date("d"));} ?>'>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="fecha_desde" class="col-sm-3 col-form-label">Hasta</label>
                        <div class="col-sm-9">
                            <input type="date" class="form-control" id="fecha_hasta" name="fecha_hasta"  value='<?php if(isset($fecF)){echo $fecF;}else{ echo(date("Y")."-".date("m")."-".date("d"));} ?>'>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="fecha_desde" class="col-sm-3 col-form-label">Descrip.</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="IdBuscar" name="IdBuscar"  placeholder="...." @if(isset($b)) value="{{ $b }}" @endif>
                        </div>
                    </div>
                    <hr style="background: #a3aab3;">
                    @if(isset($diarios))
                    <table id="exampleBuscar" class="table table-hover table-responsive">
                        <thead class="invisible">
                            <tr class="text-center">
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($diarios as $diario)
                                <tr>
                                    <td class="filaDelgada20"><div class="custom-control custom-radio"><input type="radio" class="custom-control-input" id="dia{{ $diario->diario_id }}" name="radioDiario" value="{{ $diario->diario_id }}" onchange="this.form.submit()" @if(isset($diarioS)) @if($diarioS->diario_id == $diario->diario_id) checked @endif @endif><label for="dia{{ $diario->diario_id }}" class="custom-control-label" style="font-size: 15px; font-weight: normal !important;">@if($diario->diario_tipo=='CDCO') {{ $diario->diario_codigo }} (MANUAL) @ELSE {{ $diario->diario_codigo }} (AUTOMATICO) @ENDIF</label></div></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @endif 
                </div>
            </div>
        </form>
    </div>
    <div class="col-sm-9">
        <div class="card card-secondary">
            <div class="card-header">
                <div class="float-right">
                    @if(isset($diarioS))
                    @if($diarioS->diario_tipo=='CDCO')
                    <a href="{{ url("asientoDiario/editar/{$diarioS->diario_id}") }}" id="btnImprimir"><button type="button" class="btn btn-primary btn-sm not-active-neo"><i class="fas fa-edit"></i><span> Editar</span></button></a>
                    <a href="{{ url("asientoDiario/eiminar/{$diarioS->diario_id}") }}" id="btnImprimir"><button type="button" class="btn btn-danger btn-sm not-active-neo"><i class="fas fa-trash"></i><span> Eliminar</span></button></a>
                    @endif
                    <a href="{{ url("asientoDiario/imprimir/{$diarioS->diario_id}") }}" target="_blank" id="btnImprimir"><button type="button" class="btn btn-default btn-sm not-active-neo"><i class="fas fa-print"></i><span> Imprimir</span></button></a>
                   
                    @endif
                </div>
            </div>
            <div class="card-body">
                <input type="hidden" id="IdDiario" name="IdDiario" @if(isset($diarioS)) value="{{$diarioS->diario_id}}" @endif/>
                <div class="form-group row">
                    <label for="fecha_desde" class="col-sm-1 col-form-label">Código :</label>
                    <div class="col-sm-3">
                        <input class="form-control" id="IdCodigo" @if(isset($diarioS)) value="{{$diarioS->diario_codigo}}" @endif readonly/>
                    </div>
                    <label for="fecha_desde" class="col-sm-1 col-form-label">Fecha :</label>
                    <div class="col-sm-3">
                        <input class="form-control" id="IdFecha" @if(isset($diarioS)) value="{{DateTime::createFromFormat('Y-m-d', $diarioS->diario_fecha)->format('d/m/Y')}}" @endif readonly/>
                    </div>
                    <label for="fecha_desde" class="col-sm-1 col-form-label">Sucursal :</label>
                    <div class="col-sm-3">
                        <input class="form-control" id="IdSucursal" @if(isset($diarioS)) value="{{$diarioS->sucursal->sucursal_nombre}}" @endif readonly/>
                    </div>
                    
                </div>
                <div class="form-group row">
                    <label for="fecha_desde" class="col-sm-2 col-form-label">Número documento :</label>
                    <div class="col-sm-2">
                        <input class="form-control" id="IdNumero" @if(isset($diarioS)) value="{{$diarioS->diario_numero_documento}}" @endif readonly/>
                    </div>
                    <label for="fecha_desde" class="col-sm-2 col-form-label">Referencia :</label>
                    <div class="col-sm-6">
                        <input class="form-control" id="IdReferencia" @if(isset($diarioS)) value="{{$diarioS->diario_referencia}}" @endif readonly/>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group row">
                            <label for="fecha_desde" class="col-sm-4 col-form-label">Tipo documento :</label>
                            <div class="col-sm-8">
                                <input class="form-control" id="IdTipoDocumento" @if(isset($diarioS)) value="{{$diarioS->diario_tipo_documento}}" @endif readonly/>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="fecha_desde" class="col-sm-4 col-form-label">Beneficiario :</label>
                            <div class="col-sm-8">
                                <input class="form-control" id="IdBeneficiario" @if(isset($diarioS)) value="{{$diarioS->diario_beneficiario}}" @endif readonly/>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group row">
                            <label for="fecha_desde" class="col-sm-3 col-form-label" >Comentario :</label>
                            <div class="col-sm-9">
                                <textarea class="form-control" id="IdComentario" rows="3" readonly>@if(isset($diarioS)) {{$diarioS->diario_comentario}} @endif</textarea>
                            </div>
                            
                        </div>
                    </div>
                </div>
                @if(isset($diarioS))
                    @foreach($diarioS->detalles as $detalle)
                    @if($detalle->cheque)
                    <hr style="background: #a3aab3;">
                    <div class="form-group row">
                    <label class="col-sm-12 col-form-label"><center>DATOS DE CHEQUE</center></label>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-1 col-form-label">Banco :</label>
                        <div class="col-sm-2">
                            <input class="form-control" value="{{ $detalle->cheque->cuentaBancaria->banco->bancoLista->banco_lista_nombre }}" readonly/>
                        </div>
                        <label class="col-sm-1 col-form-label">Cuenta :</label>
                        <div class="col-sm-2">
                            <input class="form-control" value="{{ $detalle->cheque->cuentaBancaria->cuenta_bancaria_numero }}" readonly/>
                        </div>
                        <label class="col-sm-1 col-form-label">Fecha :</label>
                        <div class="col-sm-2">
                            <input class="form-control"  value="{{ DateTime::createFromFormat('Y-m-d', $detalle->cheque->cheque_fecha_pago)->format('d/m/Y') }}" readonly/>
                        </div>
                        <label  class="col-sm-1 col-form-label">Numero :</label>
                        <div class="col-sm-2">
                            <input class="form-control"  value="{{ $detalle->cheque->cheque_numero }}" readonly/>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-1 col-form-label">Valor :</label>
                        <div class="col-sm-2">
                            <input class="form-control" value="{{ number_format($detalle->cheque->cheque_valor,2) }}" readonly/>
                        </div>
                        <label class="col-sm-2 col-form-label">Descripcion :</label>
                        <div class="col-sm-7">
                            <input class="form-control" value="{{ $detalle->cheque->cheque_descripcion }}" readonly/>
                        </div>
                    </div>
                    @endif 
                    @if($detalle->transferencia)
                    <hr style="background: #a3aab3;">
                    <div class="form-group row">
                    <label class="col-sm-12 col-form-label"><center>DATOS DE TRANSFERENCIA</center></label>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-1 col-form-label">Banco :</label>
                        <div class="col-sm-2">
                            <input class="form-control" value="{{ $detalle->transferencia->cuentaBancaria->banco->bancoLista->banco_lista_nombre }}" readonly/>
                        </div>
                        <label class="col-sm-1 col-form-label">Cuenta :</label>
                        <div class="col-sm-2">
                            <input class="form-control" value="{{ $detalle->transferencia->cuentaBancaria->cuenta_bancaria_numero }}" readonly/>
                        </div>
                        <label class="col-sm-1 col-form-label">Fecha :</label>
                        <div class="col-sm-2">
                            <input class="form-control"  value="{{ DateTime::createFromFormat('Y-m-d', $detalle->transferencia->transferencia_fecha)->format('d/m/Y') }}" readonly/>
                        </div>
                        <label  class="col-sm-1 col-form-label">Numero :</label>
                        <div class="col-sm-2">
                            <input class="form-control"  value="0" readonly/>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-1 col-form-label">Valor :</label>
                        <div class="col-sm-2">
                            <input class="form-control" value="{{ number_format($detalle->transferencia->transferencia_valor,2) }}" readonly/>
                        </div>
                        <label class="col-sm-2 col-form-label">Descripcion :</label>
                        <div class="col-sm-7">
                            <input class="form-control" value="{{ $detalle->transferencia->transferencia_descripcion }}" readonly/>
                        </div>
                    </div>
                    @endif 
                    @if($detalle->deposito)
                    <hr style="background: #a3aab3;">
                    <div class="form-group row">
                    <label class="col-sm-12 col-form-label"><center>DATOS DE DEPOSITO</center></label>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-1 col-form-label">Banco :</label>
                        <div class="col-sm-2">
                            <input class="form-control" value="{{ $detalle->deposito->cuentaBancaria->banco->bancoLista->banco_lista_nombre }}" readonly/>
                        </div>
                        <label class="col-sm-1 col-form-label">Cuenta :</label>
                        <div class="col-sm-2">
                            <input class="form-control" value="{{ $detalle->deposito->cuentaBancaria->cuenta_bancaria_numero }}" readonly/>
                        </div>
                        <label class="col-sm-1 col-form-label">Fecha :</label>
                        <div class="col-sm-2">
                            <input class="form-control"  value="{{ DateTime::createFromFormat('Y-m-d', $detalle->deposito->deposito_fecha)->format('d/m/Y') }}" readonly/>
                        </div>
                        <label  class="col-sm-1 col-form-label">Numero :</label>
                        <div class="col-sm-2">
                            <input class="form-control"  value="{{ $detalle->deposito->deposito_numero }}" readonly/>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-1 col-form-label">Valor :</label>
                        <div class="col-sm-2">
                            <input class="form-control" value="{{ number_format($detalle->deposito->deposito_valor,2) }}" readonly/>
                        </div>
                        <label class="col-sm-2 col-form-label">Descripcion :</label>
                        <div class="col-sm-7">
                            <input class="form-control" value="{{ $detalle->deposito->deposito_descripcion }}" readonly/>
                        </div>
                    </div>
                    @endif 
                    @endforeach
                @endif
                <hr style="background: #a3aab3;">
                <div class="row">
                    <div class="col-sm-12">
                        <table id="item" class="table table-striped table-hover boder-sar tabla-item-factura" style="white-space: normal!important; border-collapse: collapse;">
                            <thead>
                                <tr class="letra-blanca" style="background-color: #0c7181;">
                                    <th>CÓDIGO</th>
                                    <th>CUENTA</th>
                                    <th>DESCRIPCIÓN </th>
                                    <th class="centrar-texto">DEBE</th>
                                    <th class="centrar-texto">HABER</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(isset($diarioS))
                                <?php $debe = 0; $haber = 0; ?>
                                @foreach($diarioS->detalles as $detalle)
                                <?php $debe = $debe + $detalle->detalle_debe; $haber = $haber + $detalle->detalle_haber; ?>
                                <tr>
                                    <td class="sin-salto"><b>{{ $detalle->cuenta->cuentaPadre->cuenta_numero }}</b></td>
                                    <td class="sin-salto"><b>{{ $detalle->cuenta->cuentaPadre->cuenta_nombre }}</b></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td class="sin-salto">{{ $detalle->cuenta->cuenta_numero }}</td>
                                    <td class="sin-salto">&emsp;{{ $detalle->cuenta->cuenta_nombre }}</td>
                                    <td>{{ $detalle->detalle_tipo_documento.' - '.$detalle->detalle_comentario }}</td>
                                    <td class="centrar-texto">{{ number_format($detalle->detalle_debe,2) }}</td>
                                    <td class="centrar-texto">{{ number_format($detalle->detalle_haber,2) }}</td>
                                </tr>
                                @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-8"></div>
                    <div class="col-sm-2">
                        <div class="form-group">
                        <center><label class="col-form-label">DEBE</label></center>
                            <input type="text" class="form-control centrar-texto" id="IdDebe" name="IdDebe"  @if(isset($diarioS)) value="{{number_format($debe,2)}}" @endif placeholder="0.00" readonly>
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="form-group">
                        <center><label class="col-form-label">HABER</label></center>
                            <input type="text" class="form-control centrar-texto" id="IdHaber" name="IdHaber"  @if(isset($diarioS)) value="{{number_format($haber,2)}}" @endif placeholder="0.00" readonly>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-8"></div>
                    <div class="col-sm-2">
                        <center><label class="col-form-label">DIFERENCIA</label></center>  
                    </div>
                    <div class="col-sm-2">
                        <input type="text" class="form-control centrar-texto" id="IdDIF" name="IdDIF" @if(isset($diarioS)) value="{{number_format($debe-$haber,2)}}" @endif placeholder="0.00" readonly>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection