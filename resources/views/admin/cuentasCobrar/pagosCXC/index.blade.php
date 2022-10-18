@extends ('admin.layouts.admin')
@section('principal')
<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="row">
    <div class="col-sm-3">
        <div class="card card-secondary">
            <div class="card-header">
                <h3 class="card-title">Clientes</h3>
            </div>
            <div class="card-body">
                <form class="form-horizontal" method="POST"  action="{{ url("pagosCliCXC") }} ">
                @csrf
                    <div class="row">
                        <div class="col-md-10">
                            <div class="form-group">
                                <select class="form-control select2" id="sucursal_id" name="sucursal_id" style="width: 100%;" required>
                                    <option value="" label>--Seleccione una opcion--</option>
                                    <option value="0" @if(isset($sucurslaC)) @if($sucurslaC == 0) selected @endif @endif>Todas</option>
                                    @foreach($sucursales as $sucursal)
                                        <option value="{{$sucursal->sucursal_id}}"  @if(isset($sucurslaC)) @if($sucurslaC == $sucursal->sucursal_id) selected @endif @endif>{{$sucursal->sucursal_nombre}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" id="buscar" name="buscar" class="btn btn-primary btn-sm"><i class="fa fa-search"></i></button>
                        </div>
                    </div>
                </form>
                <table id="exampleBuscar" class="table table-hover table-responsive">
                    <thead class="invisible">
                        <tr class="text-center">
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(isset($clientes))
                            @foreach($clientes as $cliente)
                                <tr>
                                    <td class="filaDelgada20"><div class="custom-control custom-radio"><input type="radio" class="custom-control-input" id="cli{{ $cliente->cliente_id }}" name="radioCliente" onclick="cargarCuentas('{{ $cliente->cliente_id }}');"><label for="cli{{ $cliente->cliente_id }}" class="custom-control-label" style="font-size: 15px; font-weight: normal !important;">{{ $cliente->cliente_nombre }}</label></div></td>
                                </tr>
                            @endforeach
                        @endif  
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-sm-9">
        <form class="form-horizontal" method="POST" action="{{ url("pagosCXC") }}" onsubmit="return validacion()">
            @csrf
            <div class="card card-secondary">
                <div class="card-header">
                    <div class="row">
                        <div class="col-md-10">
                            <h3 class="card-title">Pago de Cuentas por Cobrar</h3>
                        </div>
                        <div class="col-md-2">
                            <div class="float-right">
                                <button id="guardarID" type="submit" class="btn btn-default btn-sm" disabled><i class="fa fa-save"></i><span>&nbsp;&nbsp;Guardar</span></button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <input type="hidden" id="sucursalSeleccionada" name="sucursalSeleccionada" @if(isset($sucurslaC)) value="{{ $sucurslaC }}" @else value="0" @endif />
                    <div class="row">
                        <div class="col-12 table-wrapper1" style="border-radius: 5px; border: 1px solid #ccc9c9;padding-top: 10px;">
                            @include ('admin.cuentasCobrar.pagosCXC.itemCXC')
                            <div class="card-body table-responsive p-0" style="height: 180px;">
                                <table id="cargarItem" class="table table-head-fixed text-nowrap">
                                    <thead>
                                        <tr>
                                            <th class="filaDelgada15"></th>
                                            <th class="filaDelgada15">Documento</th>
                                            <th class="filaDelgada15">Numero</th>
                                            <th class="filaDelgada15">Saldo</th>
                                            <th class="filaDelgada15 text-center">Descontar</th>
                                            <th class="filaDelgada15">Fecha</th>
                                            <th class="filaDelgada15">Vence</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <label class="col-sm-1 col-form-label">Ruc : </label>
                        <div class="col-sm-2">
                            <div class="form-group">
                                <input type="hidden" id="idCliente" name="idCliente" required>
                                <input type="text" id="idRuc" class="form-control" placeholder='0000000000000' readonly>
                            </div>
                        </div>
                        <label class="col-sm-1 col-form-label">Nombre : </label>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <input type="text" id="idNombre" name="idNombre" class="form-control" placeholder='Nombre' readonly>
                            </div>
                        </div>
                        @if(Auth::user()->empresa->empresa_contabilidad == '1')
                        <label class="col-sm-1 col-form-label">Tot. Ant. :</label>
                        <div class="col-sm-2">
                            <div class="form-group">
                                <input type="text" id="idAnticipos" class="form-control derecha-texto" value="0.00" readonly>
                            </div>
                        </div>
                        @endif
                        <div class="col-sm-1"><center><a href="{{ url("descontarAntCli") }}" class="btn btn-info btn-sm nav-link">Antcipos</a></center></div>
                    </div>
                    <div class="row">
                        <label class="col-sm-1 col-form-label">Concepto : </label>
                        <div class="col-sm-11">
                            <div class="form-group">
                                <input type="text" id="idConcepto" name="idConcepto" class="form-control" placeholder="Concepto del pago" value="Pago" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <label class="col-sm-1 col-form-label">Fecha : </label>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <input type="date" class="form-control" id="fechaPago" name="fechaPago" value="<?php echo(date("Y")."-".date("m")."-".date("d")); ?>">
                            </div>
                        </div>
                        <label class="col-sm-2 col-form-label"><center>Saldo Total : </center></label>
                        <div class="col-sm-2">
                            <div class="form-group">
                                <input type="text" id="idSaldoTotal" class="form-control derecha-texto" value="0.00" readonly>
                            </div>
                        </div>
                        <label class="col-sm-2 col-form-label"><center>Valor Seleccionado : </center></label>
                        <div class="col-sm-2">
                            <div class="form-group">
                                <input type="text" id="idValorSeleccionado" name="idValorSeleccionado" class="form-control derecha-texto" value="0.00" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="row" style="border-radius: 5px; border: 1px solid #ccc9c9;padding-top: 15px;">
                        <div class="col-sm-1">
                            <div class="form-group">
                                <div class="custom-control custom-radio">
                                    <center>
                                        <input type="radio" class="custom-control-input" id="pago1" name="radioPago" value="EFECTIVO" onchange="tabEFe();" checked>
                                        <label for="pago1" class="custom-control-label" style="font-size: 15px; font-weight: normal !important;">EFECTIVO</label>
                                    </center>
                                </div>
                            </div>
                        </div>
                        @if(Auth::user()->empresa->empresa_contabilidad == '1')
                        <div class="col-sm-3">
                            <div class="form-group">
                                <div class="custom-control custom-radio">
                                    <center>
                                        <input type="radio" class="custom-control-input" id="pago2" name="radioPago" value="DEPOSITO DE CHEQUE" onchange="tabChe();">
                                        <label for="pago2" class="custom-control-label" style="font-size: 15px; font-weight: normal !important;">DEPOSITO DE CHEQUE</label>
                                    </center>
                                </div>
                            </div>
                        </div>
                        @endif
                        <div class="col-sm-4">
                            <div class="form-group">
                                <div class="custom-control custom-radio">
                                    <center>
                                        <input type="radio" class="custom-control-input" id="pago3" name="radioPago" value="TRANSFERENCIA" onchange="tabDep();">
                                        <label for="pago3" class="custom-control-label" style="font-size: 15px; font-weight: normal !important;">TRANSFERENCIA/DEPOSITO EN EFECTIVO</label>
                                    </center>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <div class="custom-control custom-radio">
                                    <center>
                                        <input type="radio" class="custom-control-input" id="pago4" name="radioPago" value="TARJETA DE CRÉDITO" onchange="tabTar();">
                                        <label for="pago4" class="custom-control-label" style="font-size: 15px; font-weight: normal !important;">TARJETA DE CRÉDITO</label>
                                    </center>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-1">
                            <div class="form-group">
                                <div class="custom-control custom-radio">
                                    <center>
                                        <input type="radio" class="custom-control-input" id="pago5" name="radioPago" value="OTROS" onchange="tabCaj();">
                                        <label for="pago5" class="custom-control-label" style="font-size: 15px; font-weight: normal !important;">OTROS</label>
                                    </center>
                                </div>
                            </div>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-12 col-sm-12">
                            <div class="card card-secondary card-tabs">
                            <div class="card-header p-0 pt-1">
                                <ul class="nav nav-tabs" id="custom-tabs-one-tab" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" id="custom-tabs-efe-tab" data-toggle="pill" href="#custom-tabs-efe" role="tab" aria-controls="custom-tabs-efe" aria-selected="true">Efectivo</a>
                                    </li>
                                    @if(Auth::user()->empresa->empresa_contabilidad == '1')
                                    <li class="nav-item">
                                        <a class="nav-link disabled" id="custom-tabs-che-tab" data-toggle="pill" href="#custom-tabs-che" role="tab" aria-controls="custom-tabs-che" aria-selected="false">Deposito de  Cheque</a>
                                    </li>
                                    @endif
                                    <li class="nav-item">
                                        <a class="nav-link disabled" id="custom-tabs-dep-tab" data-toggle="pill" href="#custom-tabs-dep" role="tab" aria-controls="custom-tabs-dep" aria-selected="false">Transferencia/Deposito en Efectivo</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link disabled" id="custom-tabs-tar-tab" data-toggle="pill" href="#custom-tabs-tar" role="tab" aria-controls="custom-tabs-tar" aria-selected="false">Tarjeta de Credito</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link disabled" id="custom-tabs-caj-tab" data-toggle="pill" href="#custom-tabs-caj" role="tab" aria-controls="custom-tabs-caj" aria-selected="false">Otros</a>
                                    </li>
                                </ul>
                            </div>
                            <div class="card-body">
                                <div class="tab-content" id="custom-tabs-one-tabContent">
                                    <div class="tab-pane fade show active" id="custom-tabs-efe" role="tabpanel" aria-labelledby="custom-tabs-efe-tab">
                                        <div class="form-group row">    
                                            <div id="IdCajaL" class="col-lg-1 col-md-1 col-sm-1 col-xs-1 form-control-label ">
                                                <CENTER><label>Caja : </label></CENTER>
                                            </div>
                                            <div id="IdCajaI" class="col-sm-4">
                                                <div class="form-group">
                                                    <div class="form-line">
                                                            <select id="caja_id" name="caja_id" class="form-control show-tick" data-live-search="true" required>
                                                                @if($cajaAbierta)
                                                                <option value="{{ $cajaAbierta->caja->caja_id }}">{{ $cajaAbierta->caja->caja_nombre }}</option>
                                                                @endif
                                                            </select>
                                                    </div>
                                                </div>
                                            </div> 
                                        </div>  
                                    </div>
                                    <div class="tab-pane fade" id="custom-tabs-che" role="tabpanel" aria-labelledby="custom-tabs-che-tab">
                                        <div class="form-group row">
                                            <label for="banco_id" class="col-sm-1 col-form-label">Banco : </label>
                                            <div class="col-sm-3">
                                                <select class="custom-select" id="banco_id" name="banco_id" onchange="cargarCuentaBanco();">
                                                    <option value="" label>--Seleccione una opcion--</option>
                                                    @foreach($bancos as $banco)
                                                        <option value="{{$banco->banco_id}}">{{$banco->bancoLista->banco_lista_nombre}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <label for="cuenta_bancaria" class="col-sm-1 col-form-label">Cuenta : </label>
                                            <div class="col-sm-3">
                                                <select class="custom-select" id="cuenta_bancaria" name="cuenta_bancaria">
                                                    <option value="" label>--Seleccione una opcion--</option>
                                                </select>
                                            </div>
                                            <label for="numDeposito" class="col-sm-2 col-form-label"># de Deposito : </label>
                                            <div class="col-sm-2">
                                                <input type="text" class="form-control" id="numDeposito" name="numDeposito" value="0">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-sm-12 col-form-label"><center>DATOS DEL CHEQUE</center></label>
                                        </div>
                                        <div class="form-group row">
                                            <label for="banco_cheque" class="col-sm-1 col-form-label">Banco : </label>
                                            <div class="col-sm-4">
                                                <select class="custom-select select2" id="banco_cheque" name="banco_cheque">
                                                    <option value="" label>--Seleccione una opcion--</option>
                                                    @foreach($bancosLista as $bancoLista)
                                                        <option value="{{$bancoLista->banco_lista_id}}">{{$bancoLista->banco_lista_nombre}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <label for="idDueñoCheque" class="col-sm-2 col-form-label">Dueño del cheque : </label>
                                            <div class="col-sm-5">
                                                <input type="text" id="idDueñoCheque" name="idDueñoCheque" class="form-control" placeholder="Dueño">
                                            </div>
                                        </div> 
                                        <div class="form-group row">
                                            <label for="cuentaChequecliente" class="col-sm-1 col-form-label">Cuenta : </label>
                                            <div class="col-sm-3">
                                                <input type="text" class="form-control" id="cuentaChequecliente" name="cuentaChequecliente" placeholder="# cuenta">
                                            </div>
                                            <label for="numero_cheque" class="col-sm-2 col-form-label">Número cheque : </label>
                                            <div class="col-sm-2">
                                                <input type="number" class="form-control" id="numero_cheque" name="numero_cheque"  value="0">
                                            </div>
                                            <label for="idValorCheque" class="col-sm-2 col-form-label">Valor cheque : </label>
                                            <div class="col-sm-2">
                                                <input type="text" class="form-control derecha-texto" id="idValorCheque" value="0.00" readonly>
                                            </div>
                                        </div> 
                                    </div>
                                    <div class="tab-pane fade" id="custom-tabs-dep" role="tabpanel" aria-labelledby="custom-tabs-dep-tab">
                                        <div class="form-group row">
                                            <label for="banco_trans" class="col-sm-1 col-form-label">Banco : </label>
                                            <div class="col-sm-3">
                                                <select class="custom-select" id="banco_trans" name="banco_trans" onchange="cargarCuentaDep();">
                                                    <option value="" label>--Seleccione una opcion--</option>
                                                    @foreach($bancos as $banco)
                                                        <option value="{{$banco->banco_id}}">{{$banco->bancoLista->banco_lista_nombre}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <label for="cuenta_trans" class="col-sm-1 col-form-label">Cuenta : </label>
                                            <div class="col-sm-3">
                                                <select class="custom-select" id="cuenta_trans" name="cuenta_trans">
                                                    <option value="" label>--Seleccione una opcion--</option>
                                                </select>
                                            </div>
                                            <label for="cuenta_id" class="col-sm-2 col-form-label"># de Documento : </label>
                                            <div class="col-sm-2">
                                                <input type="text" class="form-control" id="numDcoumento" name="numDcoumento" value="0">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="custom-tabs-tar" role="tabpanel" aria-labelledby="custom-tabs-tar-tab">
                                        <div class="form-group row">
                                            <label for="tarjeta_id" class="col-sm-1 col-form-label">Tarjeta : </label>
                                            <div class="col-sm-6">
                                                <select class="custom-select" id="tarjeta_id" name="tarjeta_id">
                                                    <option value="" label>--Seleccione una opcion--</option>
                                                    @foreach($tarjetas as $tarjeta)
                                                        <option value="{{$tarjeta->tarjeta_id}}">{{$tarjeta->tarjeta_nombre}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <label for="cuenta_id" class="col-sm-2 col-form-label">Voucher : </label>
                                            <div class="col-sm-3">
                                                <input type="text" class="form-control" id="numVoucher" name="numVoucher" value="0">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="custom-tabs-caj" role="tabpanel" aria-labelledby="custom-tabs-caj-tab">
                                        <div class="form-group row">
                                            <label for="movimiento_id" class="col-sm-2 col-form-label">Movimiento Caja Y Bancos: </label>
                                            <div class="col-sm-6">
                                                <select class="custom-select select2" id="movimiento_id" name="movimiento_id">
                                                    <option value="" label>--Seleccione una opcion--</option>
                                                    @foreach($movimientos as $movimiento)
                                                        <option value="{{$movimiento->tipo_id}}C">CAJA - {{$movimiento->tipo_nombre}}</option>
                                                    @endforeach
                                                    @foreach($movimientosBanco as $movimientobanco)
                                                        <option value="{{$movimientobanco->tipo_id}}B">BANCO - {{$movimientobanco->tipo_nombre}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div> 
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <form>
    </div>
</div>
<script type="text/javascript">
    var id_item = 1;    
    function cargarCuentaBanco(){
        $.ajax({
            url: '{{ url("cuentaBancaria/searchN") }}'+ '/' + document.getElementById("banco_id").value,
            dataType: "json",
            type: "GET",
            data: {
                buscar: document.getElementById("banco_id").value
            },
            success: function(data){
                document.getElementById("cuenta_bancaria").innerHTML = "<option value='' label>--Seleccione una opcion--</option>";
                for (var i=0; i<data.length; i++) {
                    document.getElementById("cuenta_bancaria").innerHTML += "<option value='"+data[i].cuenta_bancaria_id+"'>"+data[i].cuenta_bancaria_numero+"</option>";
                }           
            },
        });
    }
    function cargarCuentaDep(){
        $.ajax({
            url: '{{ url("cuentaBancaria/searchN") }}'+ '/' + document.getElementById("banco_trans").value,
            dataType: "json",
            type: "GET",
            data: {
                buscar: document.getElementById("banco_trans").value
            },
            success: function(data){
                document.getElementById("cuenta_trans").innerHTML = "<option value='' label>--Seleccione una opcion--</option>";
                for (var i=0; i<data.length; i++) {
                    document.getElementById("cuenta_trans").innerHTML += "<option value='"+data[i].cuenta_bancaria_id+"'>"+data[i].cuenta_bancaria_numero+"</option>";
                }           
            },
        });
    }
    function agregarItem(id,documento,numero,saldo,fecha,vence) {
        var linea = $("#plantillaItem").html();
        linea = linea.replace(/{ID}/g, id_item);
        linea = linea.replace(/{Did}/g, id);
        linea = linea.replace(/{Ddocumento}/g, documento);
        linea = linea.replace(/{Dnumero}/g, numero);
        linea = linea.replace(/{Dsaldo}/g, Number(saldo).toFixed(2));
        linea = linea.replace(/{Ddescontar}/g, Number(saldo).toFixed(2));
        linea = linea.replace(/{Dfecha}/g, fecha);
        linea = linea.replace(/{Dvence}/g, vence);
        $("#cargarItem tbody").append(linea);
        id_item = id_item + 1;
    }
    function limpiarTabla() {
        document.getElementById("idRuc").value = '';
        document.getElementById("idNombre").value = '';
        @if(Auth::user()->empresa->empresa_contabilidad == '1')
        document.getElementById("idDueñoCheque").value = ''; 
        document.getElementById("idAnticipos").value = '0.00';
        @endif
        document.getElementById("idSaldoTotal").value = '0.00';
        document.getElementById("idValorSeleccionado").value = '0.00';
        @if(Auth::user()->empresa->empresa_contabilidad == '1')
        document.getElementById("idValorCheque").value = '0.00';
        @endif
        document.getElementById("idConcepto").value = 'Pago';
        for (var i = 1; i < id_item; i++) {
            $("#row_" + i).remove();
        }
        id_item = 1;
    }
    function cargarCuentas(id) {
        limpiarTabla();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            async: false,
            url: '{{ url("facturasCXC/searchN") }}',
            dataType: "json",
            type: "POST",
            data: {
                cliente_id: id,
                sucursal_id: document.getElementById("sucursal_id").value,
            },
            success: function(data) {
                for (var i = 0; i < data.length; i++) {
                    document.getElementById("idCliente").value = data[i].cliente_id;
                    document.getElementById("idRuc").value = data[i].cliente_cedula;
                    document.getElementById("idNombre").value = data[i].cliente_nombre;
                    @if(Auth::user()->empresa->empresa_contabilidad == '1')
                    document.getElementById("idDueñoCheque").value = data[i].cliente_nombre; 
                    document.getElementById("idAnticipos").value = Number(data[i].saldo_cliente).toFixed(2);
                    if(Number(document.getElementById("idAnticipos").value) > 0){
                        document.getElementById("idAnticipos").classList.add('tot-ant');
                    }else{
                        document.getElementById("idAnticipos").classList.remove('tot-ant');
                    }
                    @endif
                    document.getElementById("idSaldoTotal").value = Number(Number(document.getElementById("idSaldoTotal").value)+Number(data[i].cuenta_saldo)).toFixed(2);
                    if(data[i].factura_numero != null){
                        agregarItem(data[i].cuenta_id,'FACTURA', data[i].factura_numero, data[i].cuenta_saldo, data[i].cuenta_fecha, data[i].cuenta_fecha_fin);
                    }
                    else if(data[i].nt_numero != null){
                        agregarItem(data[i].cuenta_id,'NOTA DE ENTREGA', data[i].nt_numero, data[i].cuenta_saldo, data[i].cuenta_fecha, data[i].cuenta_fecha_fin);
                    }
                    else if(data[i].nd_numero != null){
                        agregarItem(data[i].cuenta_id,'NOTA DE DÉBITO', data[i].nd_numero, data[i].cuenta_saldo, data[i].cuenta_fecha, data[i].cuenta_fecha_fin);
                    }else{
                        agregarItem(data[i].cuenta_id,'FACTURA', data[i].cuenta_descripcion.substring(38), data[i].cuenta_saldo, data[i].cuenta_fecha, data[i].cuenta_fecha_fin);
                    }
                }
            },
        });
    }
    function calcularSeleccion(codigo,id){
        if(document.getElementById("check"+codigo).checked){
            $("input[name='Ddescontar[]']")[id].readOnly = false;
            $("input[name='Ddescontar[]']")[id].value = Number(Number($("input[name='Ddescontar[]']")[id].value) + Number($("input[name='Dsaldo[]']")[id].value)).toFixed(2);
        }else{
            $("input[name='Ddescontar[]']")[id].readOnly = true;
            $("input[name='Ddescontar[]']")[id].value = Number(0.00).toFixed(2);
        }
        totalSeleccion(id);
    }
    function totalSeleccion(id){
        if(Number($("input[name='Ddescontar[]']")[id].value) < 0){
            $("input[name='Ddescontar[]']")[id].value = Number(Number($("input[name='Dsaldo[]']")[id].value)).toFixed(2);
        }
        if(Number($("input[name='Ddescontar[]']")[id].value) > Number($("input[name='Dsaldo[]']")[id].value)){
            $("input[name='Ddescontar[]']")[id].value = Number(Number($("input[name='Dsaldo[]']")[id].value)).toFixed(2);
        }
        document.getElementById("idValorSeleccionado").value = 0.00;
        for (var i = 1; i < id_item; i++) {
            document.getElementById("idValorSeleccionado").value = Number(Number(document.getElementById("idValorSeleccionado").value) + Number($("input[name='Ddescontar[]']")[i].value)).toFixed(2);
        }
        document.getElementById("idValorCheque").value = document.getElementById("idValorSeleccionado").value;
        if(document.getElementById("idValorSeleccionado").value > 0){
            document.getElementById("guardarID").disabled = false;
        }else{
            document.getElementById("guardarID").disabled = true;
        }
    }   
    function tabEFe(){
        seleccionarTab();
        document.getElementById("custom-tabs-efe-tab").classList.remove('disabled');
        $('[href="#custom-tabs-efe"]').tab('show');
        $('#caja_id').prop("required", true);
    }
    function tabChe(){
        seleccionarTab();
        document.getElementById("custom-tabs-che-tab").classList.remove('disabled');
        $('[href="#custom-tabs-che"]').tab('show');
        $('#banco_id').prop("required", true);
        $('#cuenta_id').prop("required", true);
        $('#idCuentaContableBanco').prop("required", true);
        $('#banco_cheque').prop("required", true);
        $('#numero_cheque').prop("required", true);
        $('#idValorCheque').prop("required", true);
        $('#idDueñoCheque').prop("required", true);
        $('#cuentaChequecliente').prop("required", true);
        $('#numCheque').prop("required", true);
        document.getElementById("numCheque").min = "1";

    }
    function tabDep(){
        seleccionarTab();
        document.getElementById("custom-tabs-dep-tab").classList.remove('disabled');
        $('[href="#custom-tabs-dep"]').tab('show');
        $('#banco_trans').prop("required", true);
        $('#cuenta_trans').prop("required", true);
        $('#idCuentaContableTrans').prop("required", true);
        $('#numDcoumento').prop("required", true);
    }
    function tabTar(){
        seleccionarTab();
        document.getElementById("custom-tabs-tar-tab").classList.remove('disabled');
        $('[href="#custom-tabs-tar"]').tab('show');
        $('#tarjeta_id').prop("required", true);
        $('#numVoucher').prop("required", true);
        $('#idCuentaTarjeta').prop("required", true);
    }
    function tabCaj(){
        seleccionarTab();
        document.getElementById("custom-tabs-caj-tab").classList.remove('disabled');
        $('[href="#custom-tabs-caj"]').tab('show');
        $('#movimiento_id').prop("required", true);
    }
    function seleccionarTab(){
        $('#caja_id').removeAttr("required");

        $('#banco_id').removeAttr("required");
        $('#cuenta_id').removeAttr("required");
        $('#idCuentaContableBanco').removeAttr("required");
        $('#banco_cheque').removeAttr("required");
        $('#cuentaChequecliente').removeAttr("required");
        $('#numero_cheque').removeAttr("required");
        $('#idValorCheque').removeAttr("required");
        $('#idDueñoCheque').removeAttr("required");
        
        $('#banco_trans').removeAttr("required");
        $('#cuenta_trans').removeAttr("required");
        $('#idCuentaContableTrans').removeAttr("required");
        $('#numDcoumento').removeAttr("required");

        $('#tarjeta_id').removeAttr("required");
        $('#numVoucher').removeAttr("required");
        $('#idCuentaTarjeta').removeAttr("required");

        $('#numCheque').removeAttr("min");
        $('#numCheque').removeAttr("required");

        $('#movimiento_id').removeAttr("required");
                
        document.getElementById("custom-tabs-efe-tab").classList.remove('disabled');
        @if(Auth::user()->empresa->empresa_contabilidad == '1')
        document.getElementById("custom-tabs-che-tab").classList.remove('disabled');
        @endif
        document.getElementById("custom-tabs-dep-tab").classList.remove('disabled');
        document.getElementById("custom-tabs-tar-tab").classList.remove('disabled');
        document.getElementById("custom-tabs-caj-tab").classList.remove('disabled');


        document.getElementById("custom-tabs-efe-tab").classList.add('disabled');
        @if(Auth::user()->empresa->empresa_contabilidad == '1')
        document.getElementById("custom-tabs-che-tab").classList.add('disabled');
        @endif
        document.getElementById("custom-tabs-dep-tab").classList.add('disabled');
        document.getElementById("custom-tabs-tar-tab").classList.add('disabled');
        document.getElementById("custom-tabs-caj-tab").classList.add('disabled');
    }
    function validacion(){
        document.getElementById("guardarID").value = "Enviando...";
	    document.getElementById("guardarID").disabled = true;
        return true;
    }
</script>
@endsection