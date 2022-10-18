@extends ('admin.layouts.admin')
@section('principal')
<form class="form-horizontal" onsubmit="return verificarDato();" method="POST" action="{{ url("arqueoCaja") }}">
@csrf
    <div class="card card-secondary">
        <div class="card-header">
            <h3 class="card-title">Apertura de Caja</h3>
            <button type="submit" class="btn btn-success btn-sm float-right"><i
                    class="fa fa-save"></i>&nbsp;Guardar</button>
        </div>
        <div class="card-body">
            <h5 class="form-control" style="color:#fff; background:#17a2b8;">Datos de Apertura</h5>
            <div class="card-body">
                <div class="form-group row">
                    <label for="idFecha" class="col-sm-1 col-form-label">Fecha de Ingreso</label>
                    <div class="col-sm-5">
                        <input type="date" class="form-control" id="idFecha" name="idFecha" readonly
                            value='<?php echo(date("Y")."-".date("m")."-".date("d")); ?>' required>
                    </div>
                    <label for="idCaja" class="col-sm-1 col-form-label">Caja</label>
                    <div class="col-sm-5">
                        <select class="custom-select select2" id="idCaja" name="idCaja" required>
                            <option value="" label>--Seleccione una caja--</option>
                            @foreach($cajas as $caja)
                            @foreach($cajasxusuarios as $cajasxusuario)
                            @if($caja->caja_id == $cajasxusuario->caja_id)
                            <option value="{{$caja->caja_id}}">{{$caja->caja_nombre}}</option>
                            @endif
                            @endforeach
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="idFecha" class="col-sm-1 col-form-label">Observacion</label>
                    <div class="col-sm-5">
                        <input type="text" class="form-control" id="idMensaje" value='Apertura de Caja sin Novedad !!'
                            name="idMensaje" required>
                    </div>
                    <label for="idCaja" class="col-sm-1 col-form-label">Monto</label>
                    <div class="col-sm-1">
                        <input type="number" class="form-control" id="idMonto" name="idMonto" min="0" step=".01"
                            value="0" required>
                    </div>
                    <label for="idSuma" class="col-sm-2 col-form-label">Total Monedas + Billetes</label>
                    <div class="col-sm-1">
                        <input type="number" class="form-control" id="idSuma" name="idSuma" value="0" disabled>
                    </div>
                </div>
            </div>
            <h5 class="form-control" style="color:#fff; background:#17a2b8;">Denominaciones</h5>
            <div class="card-body">
                <div class="form-group row">
                    <div class="col-sm-2">
                    </div>
                    <div class="col-sm-4">
                        <h5 class="form-control" style="color:#fff; background:#17a2b8;">
                            <CENTER>MONEDAS</CENTER>
                        </h5>
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
                                                name="moneda01" onclick="calcularMonedas();"
                                                onkeyup="calcularMonedas();" min="0" value="0" required>
                                            <CENTER>
                                    </td>
                                    <td width="150">
                                        <CENTER><input type="text" id="idmoneda01" name="idmoneda01"
                                                class="form-control" placeholder='0' readonly>
                                            <CENTER>
                                    </td>
                                </tr>
                                <tr>

                                    <td>
                                        <CENTER>0.05 ctvos<CENTER>
                                    </td>
                                    <td>
                                        <CENTER><input type="number" class="form-controltext" id="moneda05"
                                                name="moneda05" onclick="calcularMonedas();"
                                                onkeyup="calcularMonedas();" min="0" value="0" required>
                                            <CENTER>
                                    </td>
                                    <td>
                                        <CENTER><input type="text" id="idmoneda05" class="form-control" placeholder='0'
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
                                                name="moneda10" onclick="calcularMonedas();"
                                                onkeyup="calcularMonedas();" min="0" value="0" required>
                                            <CENTER>
                                    </td>
                                    <td>
                                        <CENTER><input type="text" id="idmoneda10" class="form-control" placeholder='0'
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
                                                name="moneda25" onclick="calcularMonedas();"
                                                onkeyup="calcularMonedas();" min="0" value="0" required>
                                            <CENTER>
                                    </td>
                                    <td>
                                        <CENTER><input type="text" id="idmoneda25" class="form-control" placeholder='0'
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
                                                name="moneda50" onclick="calcularMonedas();"
                                                onkeyup="calcularMonedas();" min="0" value="0" required>
                                            <CENTER>
                                    </td>
                                    <td>
                                        <CENTER><input type="text" id="idmoneda50" class="form-control" placeholder='0'
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
                                                name="moneda1" onclick="calcularMonedas();" onkeyup="calcularMonedas();"
                                                min="0" value="0" required>
                                            <CENTER>
                                    </td>
                                    <td>
                                        <CENTER><input type="text" id="idmoneda1" class="form-control" placeholder='0'
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
                                        <CENTER><input type="text" id="idTotalMonedas" class="form-control" value='0'
                                                readonly>
                                            <CENTER>
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                    </div>
                    <div class="col-sm-4">
                        <h5 class="form-control" style="color:#fff; background:#17a2b8;">
                            <CENTER>BILLETES</CENTER>
                        </h5>
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
                                                name="billete1" onclick="calcularBilletes();"
                                                onkeyup="calcularBilletes();" min="0" value="0" required></CENTER>
                                    </td>
                                    <td>
                                        <CENTER><input type="text" id="idbillete1" class="form-control" placeholder='0'
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
                                                name="billete5" onclick="calcularBilletes();"
                                                onkeyup="calcularBilletes();" min="0" value="0" required>
                                            <CENTER>
                                    </td>
                                    <td>
                                        <CENTER><input type="text" id="idbillete5" class="form-control" placeholder='0'
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
                                                name="billete10" onclick="calcularBilletes();"
                                                onkeyup="calcularBilletes();" min="0" value="0" required>
                                            <CENTER>
                                    </td>
                                    <td>
                                        <CENTER><input type="text" id="idbillete10" class="form-control" placeholder='0'
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
                                                name="billete20" onclick="calcularBilletes();"
                                                onkeyup="calcularBilletes();" min="0" value="0" required>
                                            <CENTER>
                                    </td>
                                    <td>
                                        <CENTER><input type="text" id="idbillete20" class="form-control" placeholder='0'
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
                                                name="billete50" onclick="calcularBilletes();"
                                                onkeyup="calcularBilletes();" min="0" value="0" required>
                                            <CENTER>
                                    </td>
                                    <td>
                                        <CENTER><input type="text" id="idbillete50" class="form-control" placeholder='0'
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
                                                name="billete100" onclick="calcularBilletes();"
                                                onkeyup="calcularBilletes();" min="0" value="0" required>
                                            <CENTER>
                                    </td>
                                    <td>
                                        <CENTER><input type="text" id="idbillete100" class="form-control"
                                                placeholder='0' readonly>
                                            <CENTER>
                                    </td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td>
                                        <CENTER>Totales<CENTER>
                                    </td>
                                    <td width="150">
                                        <CENTER><input type="text" id="idTotalBilletes" class="form-control" value='0'
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
    </div>
</form>
<script type="text/javascript">
    function calcularMonedas() {
        if (document.getElementById("moneda01").value != "") {
            document.getElementById("idmoneda01").value = parseFloat(document.getElementById("moneda01").value) *
                parseFloat(0.01);
        } else {
            document.getElementById("idmoneda01").value = 0
        }
        if (document.getElementById("moneda05").value != "") {
            document.getElementById("idmoneda05").value = parseFloat(document.getElementById("moneda05").value) *
                parseFloat(0.05);
        } else {
            document.getElementById("idmoneda05").value = 0
        }
        if (document.getElementById("moneda10").value != "") {
            document.getElementById("idmoneda10").value = parseFloat(document.getElementById("moneda10").value) *
                parseFloat(0.10);
        } else {
            document.getElementById("idmoneda10").value = 0
        }
        if (document.getElementById("moneda25").value != "") {
            document.getElementById("idmoneda25").value = parseFloat(document.getElementById("moneda25").value) *
                parseFloat(0.25);
        } else {
            document.getElementById("idmoneda25").value = 0
        }
        if (document.getElementById("moneda50").value != "") {
            document.getElementById("idmoneda50").value = parseFloat(document.getElementById("moneda50").value) *
                parseFloat(0.50);
        } else {
            document.getElementById("idmoneda50").value = 0
        }
        if (document.getElementById("moneda1").value != "") {
            document.getElementById("idmoneda1").value = parseFloat(document.getElementById("moneda1").value) * parseFloat(
                1.00);
        } else {
            document.getElementById("idmoneda1").value = 0
        }
        //document.getElementById("idMonto").value = parseFloat(document.getElementById("idTotalBilletes").value) + parseFloat(document.getElementById("idTotalMonedas").value);

        calcularTotalMonedas();

    }

    function calcularTotalMonedas() {
        if (document.getElementById("idTotalMonedas").value != "") {
            var totalMonedas = parseFloat(document.getElementById("idmoneda01").value) +
                parseFloat(document.getElementById("idmoneda05").value) + parseFloat(document.getElementById("idmoneda10")
                    .value) + parseFloat(document.getElementById("idmoneda25").value) + parseFloat(document.getElementById(
                    "idmoneda50").value) + parseFloat(document.getElementById("idmoneda1").value);
                    document.getElementById("idTotalMonedas").value = (Number(totalMonedas).toFixed(2));
        } else {
            document.getElementById("idTotalMonedas").value = 0
        }
        asignar();
    }

    function calcularTotalBiletes() {
        if (document.getElementById("idTotalBilletes").value != "") {
            var totalBiletes = Number(document.getElementById("idbillete1").value) + Number(
                    document.getElementById("idbillete5").value) + Number(document.getElementById("idbillete10").value) +
                Number(document.getElementById("idbillete20").value) + Number(document.getElementById("idbillete50")
                .value) + Number(document.getElementById("idbillete100").value);
                document.getElementById("idTotalBilletes").value = Number(totalBiletes).toFixed(2);
        } else {
            document.getElementById("idTotalBilletes").value = 0
        }
        asignar();
    }

    function calcularBilletes() {
        if (document.getElementById("billete1").value != "") {
            document.getElementById("idbillete1").value = parseFloat(document.getElementById("billete1").value) *
                parseFloat(1);
        } else {
            document.getElementById("idbillete1").value = 0
        }
        if (document.getElementById("billete5").value != "") {
            document.getElementById("idbillete5").value = parseFloat(document.getElementById("billete5").value) *
                parseFloat(5);
        } else {
            document.getElementById("idbillete5").value = 0
        }
        if (document.getElementById("billete10").value != "") {
            document.getElementById("idbillete10").value = parseFloat(document.getElementById("billete10").value) *
                parseFloat(10);
        } else {
            document.getElementById("idbillete10").value = 0
        }
        if (document.getElementById("billete20").value != "") {
            document.getElementById("idbillete20").value = parseFloat(document.getElementById("billete20").value) *
                parseFloat(20);
        } else {
            document.getElementById("idbillete20").value = 0
        }
        if (document.getElementById("billete50").value != "") {
            document.getElementById("idbillete50").value = parseFloat(document.getElementById("billete50").value) *
                parseFloat(50);
        } else {
            document.getElementById("idbillete50").value = 0
        }
        if (document.getElementById("billete100").value != "") {
            document.getElementById("idbillete100").value = parseFloat(document.getElementById("billete100").value) *
                parseFloat(100);
        } else {
            document.getElementById("idbillete100").value = 0
        }
        calcularTotalBiletes();
    }

    function asignar() {
        if (document.getElementById("idTotalBilletes").value != "" || document.getElementById("idTotalMonedas").value !=
            "") {
            document.getElementById("idSuma").value = parseFloat(document.getElementById("idTotalMonedas").value) +
                parseFloat(document.getElementById("idTotalBilletes").value)
        } else {
            document.getElementById("idSuma").value = 0;
        }
    }

    function verificarDato() {
        if (document.getElementById("idSuma").value == document.getElementById("idMonto").value) {           
        } else {
            alert('El Monto ingresado es diferente de la sumatoria en denominaciones');
            return false;
        }
        return true;

    }
</script>
@endsection