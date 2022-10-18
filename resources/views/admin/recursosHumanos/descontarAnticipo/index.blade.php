@extends ('admin.layouts.admin')
@section('principal')
<div class="row">
    <div class="col-sm-3">
        <div class="card card-secondary">
            <div class="card-header">
                <h3 class="card-title">Empelados</h3>
            </div>
            <div class="card-body">
                <form class="form-horizontal" method="POST" action="{{ url("descontarAntEmpDep") }} ">
                @csrf
                    <div class="row">
                        <div class="col-md-10">
                            <div class="form-group">
                                <select class="form-control select2" id="departamento_id" name="departamento_id" style="width: 100%;" required>
                                    <option value="" label>--Seleccione una opcion--</option>
                                    @foreach($departamentos as $departamento)
                                        <option value="{{$departamento->departamento_id}}"  @if(isset($departamentoC)) @if($departamentoC == $departamento->departamento_id) selected @endif @endif>{{$departamento->departamento_nombre}}</option>
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
                        @if(isset($empleados))
                            @foreach($empleados as $empleado)
                            <tr>
                                <td class="filaDelgada20"><div class="custom-control custom-radio"><input type="radio" class="custom-control-input" id="{{ $empleado->empleado_id}}" name="radioEmpleado" onclick="cargarAnticipos('{{ $empleado->empleado_id}}');"><label for="{{ $empleado->empleado_id}}" class="custom-control-label" style="font-size: 15px; font-weight: normal !important;">{{ strtoupper($empleado->empleado_nombre) }}</label></div></td>
                            </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-sm-9">
        <form class="form-horizontal" method="POST" action="{{ url("descontarAntEmp") }} " onsubmit="return validacion()">
            @csrf
            <div class="card card-secondary">
                <div class="card-header">
                    <div class="row">
                        <div class="col-md-10">
                            <h3 class="card-title">Anticipos Pendientes</h3>
                            <input type="hidden" id="nombreEmpleado" name="nombreEmpleado"/>
                            <input type="hidden" id="idEmpleado" name="idEmpleado"/>
                        </div>
                        <div class="col-md-2">
                            <div class="float-right">
                                <button id="guardarID" type="submit" class="btn btn-default btn-sm" disabled><i class="fa fa-save"></i><span>&nbsp;&nbsp;Guardar</span></button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12 table-wrapper1" style="border-radius: 5px; border: 1px solid #ccc9c9;padding-top: 10px;">
                            @include ('admin.recursosHumanos.descontarAnticipo.item')
                            <div class="card-body table-responsive p-0" style="height: 180px;">
                                <table id="cargarItem" class="table table-head-fixed text-nowrap">
                                    <thead>
                                        <tr>
                                            <th class="filaDelgada15"></th>
                                            <th class="filaDelgada15">Monto</th>
                                            <th class="filaDelgada15">Saldo</th>
                                            <th class="filaDelgada15 text-center">Descontar</th>
                                            <th class="filaDelgada15">Fecha</th>
                                            <th class="filaDelgada15">Diario</th>
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
                        <label for="idDesde" class="col-sm-2 col-form-label"><center>Monto Total : </center></label>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <input type="text" id="idMonto" name="idMonto" class="form-control derecha-texto" value="0.00" required>
                            </div>
                        </div>
                        <label for="idDesde" class="col-sm-2 col-form-label"><center>Saldo Total : </center></label>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <input type="text" id="idSaldo" name="idSaldo" class="form-control derecha-texto" value="0.00" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <label for="idDesde" class="col-sm-2 col-form-label"><center>Descontar Total: </center></label>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <input type="text" id="idDescontar" name="idDescontar" class="form-control derecha-texto" value="0.00" required>
                            </div>
                        </div>
                        <label for="idDesde" class="col-sm-2 col-form-label"><center>Fecha de Pago: </center></label>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <input type="date" id="fechaCruce" name="fechaCruce" class="form-control" value='<?php echo(date("Y")."-".date("m")."-".date("d")); ?>' required>
                            </div>
                        </div>
                    </div>
                    <div class="card card-secondary">
                        <div class="card-header">
                            <h3 class="card-title">Forma de Pago</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12 table-wrapper1" style="border-radius: 5px; border: 1px solid #ccc9c9;padding-top: 10px;">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <div class="custom-control custom-radio">
                                                    <center>
                                                        <input type="radio" class="custom-control-input" id="pago1" name="radioPago" value="EFECTIVO" onchange="descactivarPago();">
                                                        <label for="pago1" class="custom-control-label" style="font-size: 15px; font-weight: normal !important;">EFECTIVO</label>
                                                    </center>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <div class="custom-control custom-radio">
                                                    <center>
                                                        <input type="radio" class="custom-control-input" id="pago2" name="radioPago" value="DEPOSITO" onchange="activarPago();" checked>
                                                        <label for="pago2" class="custom-control-label" style="font-size: 15px; font-weight: normal !important;">DEPOSITO</label>
                                                    </center>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <br>
                            <div id="idPago2"  style="display:none;">
                                <div class="row">
                                    <label for="idDesde" class="col-sm-1 col-form-label"><center>Caja : </center></label>
                                    <div class="col-5">
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
                            <div id="idPago">
                                <div class="row">
                                    <label for="idDesde" class="col-sm-1 col-form-label"><center>Banco : </center></label>
                                    <div class="col-sm-5">
                                        <select class="custom-select" id="banco_id" name="banco_id" onclick="cargarCuenta();" required>
                                            <option value="" label>--Seleccione una opcion--</option>
                                            @foreach($bancos as $banco)
                                                <option value="{{$banco->banco_id}}">{{$banco->bancoLista->banco_lista_nombre}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <label for="idDesde" class="col-sm-1 col-form-label"><center>Cuenta : </center></label>
                                    <div class="col-5">
                                        <select class="custom-select" id="cuenta_id" name="cuenta_id" required>
                                            <option value="" label>--Seleccione una opcion--</option>
                                        </select>
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
    function agregarItem(id,monto,saldo,fecha,diario) {
        var linea = $("#plantillaItem").html();
        linea = linea.replace(/{ID}/g, id_item);
        linea = linea.replace(/{Did}/g, id);
        linea = linea.replace(/{Dmonto}/g, Number(monto).toFixed(2));
        linea = linea.replace(/{Dsaldo}/g, Number(saldo).toFixed(2));
        linea = linea.replace(/{Ddescontar}/g, "0.00");
        linea = linea.replace(/{Dfecha}/g, fecha);
        linea = linea.replace(/{Ddiario}/g, diario);
        $("#cargarItem tbody").append(linea);
        id_item = id_item + 1;
    }
    function descactivarPago(){
        document.getElementById("idPago").classList.add('invisible');
        var x = document.getElementById("idPago2");
        if (x.style.display === "none") {
            x.style.display = "block";
        } else {
            x.style.display = "none";
        }        
        efectivo();
    }
    function activarPago(){
        deposito();
        var x = document.getElementById("idPago2");
        if (x.style.display === "none") {
            x.style.display = "block";
        } else {
            x.style.display = "none";
        }
        document.getElementById("idPago").classList.remove('invisible');
    }
    function limpiarTabla() {
        document.getElementById("idMonto").value = '0.00';
        document.getElementById("idSaldo").value = '0.00';
        document.getElementById("idDescontar").value = '0.00'; 
        for (var i = 1; i < id_item; i++) {
            $("#row_" + i).remove();
        }
        id_item = 1;
    }
    function cargarAnticipos(id) {
        limpiarTabla();
        $.ajax({
            url: '{{ url("buscarAnticipos/searchN") }}'+ '/' +id,
            dataType: "json",
            type: "GET",
            data: {
                ide: id
            },
            success: function(data){
                for (var i = 0; i < data.length; i++) {
                    document.getElementById("nombreEmpleado").value = data[i].empleado_nombre;
                    document.getElementById("idEmpleado").value = id;
                    document.getElementById("idMonto").value = Number(Number(document.getElementById("idMonto").value) + Number(data[i].anticipo_valor)).toFixed(2);
                    document.getElementById("idSaldo").value = Number(Number(document.getElementById("idSaldo").value) + Number(data[i].anticipo_saldo)).toFixed(2);
                    agregarItem(data[i].anticipo_id,data[i].anticipo_valor, data[i].anticipo_saldo, data[i].anticipo_fecha, data[i].diario_codigo);
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
        document.getElementById("idDescontar").value = 0.00;
        for (var i = 1; i < id_item; i++) {
            document.getElementById("idDescontar").value = Number(Number(document.getElementById("idDescontar").value) + Number($("input[name='Ddescontar[]']")[i].value)).toFixed(2);
        }
        if(document.getElementById("idDescontar").value > 0){
            document.getElementById("guardarID").disabled = false;
        }else{
            document.getElementById("guardarID").disabled = true;
        }
    }   
    function deposito() {
            $('#banco_id').prop("required", true);
            $('#cuenta_id').prop("required", true);
            $('#caja_id').removeAttr("required");

        }
        function efectivo() {
            $('#banco_id').removeAttr("required");
            $('#cuenta_id').removeAttr("required");
            $('#caja_id').prop("required", true);

        }
        function validacion(){ 
            document.getElementById("guardarID").value = "Enviando...";
            document.getElementById("guardarID").disabled = true;
            return true;
    }
</script>
@endsection