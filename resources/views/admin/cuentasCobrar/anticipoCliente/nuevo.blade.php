@extends ('admin.layouts.admin')
@section('principal')
<form class="form-horizontal"  onsubmit="return verificarDato();" method="POST" action="{{ url("anticipoCliente") }} ">
    @csrf
<div class="card card-secondary col-sm-8">
    <div class="card-header">
        <div class="row">
                <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5">
                    <h2 class="card-title">Nuevo Anticipo de Cliente</h2>
                </div>
                <div class="col-xs-7 col-sm-7 col-md-7 col-lg-7">
                    <div class="float-right">
                        <button id="guardarID" type="submit" class="btn btn-success btn-sm"><i class="fa fa-save"></i><span> Guardar</span></button>
                        <button type="button" id="cancelarID" name="cancelarID" onclick="javascript:location.reload()" class="btn btn-default btn-sm not-active-neo"><i class="fas fa-times-circle"></i><span> Cancelar</span></button>
                    </div>
                </div>
            </div>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        <h5 class="form-control" style="color:#fff; background:#17a2b8;">Datos de Cuenta</h5>        
            <div class="card-body">
                <div class="form-group row">
                    <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2 form-control-label ">
                        <label>Numero</label>
                    </div>
                    <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                        <div class="form-group">
                            <div class="form-line">
                                <input id="punto_id" name="punto_id" value="{{ $rangoDocumento->puntoEmision->punto_id }}" type="hidden">
                                <input id="rango_id" name="rango_id" value="{{ $rangoDocumento->rango_id }}" type="hidden">
                                <input type="text" id="anticipo_serie" name="anticipo_serie" value="{{ $rangoDocumento->puntoEmision->sucursal->sucursal_codigo }}{{ $rangoDocumento->puntoEmision->punto_serie }}" class="form-control derecha-texto negrita " placeholder="Serie" required readonly>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                        <div class="form-group">
                            <div class="form-line">
                                <input type="text" id="anticipo_numero" name="anticipo_numero" value="{{ $secuencial }}" class="form-control  negrita " placeholder="Numero" required readonly>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="idFecha" class="col-sm-2 col-form-label">Fecha</label>
                    <div class="col-sm-10">
                        <input type="date" class="form-control" id="idFecha" name="idFecha" value='<?php echo (date("Y") . "-" . date("m") . "-" . date("d")); ?>' required>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="idTipo" class="col-sm-2 col-form-label">Tipo de Anticipo</label>
                    <div class="col-sm-10">
                        <select class="custom-select" id="idTipo" name="idTipo" onchange="bancoActivar();" require>
                            <option value="Deposito" >DEPOSITO</option>
                            <option value="Efectivo" >EFECTIVO</option>
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="idCaja" class="col-sm-2 col-form-label">Caja</label>
                    <div class="col-sm-10">
                        <select class="custom-select select2" id="idCaja" name="idCaja" disabled required>                           
                            @if($cajasxusuario)
                                @foreach($cajas as $caja)
                                    @if($caja->caja_id == $cajasxusuario->caja_id)
                                        <option value="{{$caja->caja_id}}" selected>{{$caja->caja_nombre}}</option>                                   
                                    @endif
                                @endforeach
                            @else
                                <option value="" label>--No dispone de una Caja--</option>
                            @endif
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="banco_id" class="col-sm-2 col-form-label">Banco</label>
                    <div class="col-sm-10">
                        <select class="custom-select" id="banco_id" name="banco_id" onclick="cargarCuenta();" required>
                            <option value="" label>--Seleccione una opcion--</option>
                            @foreach($bancos as $banco)
                            <option value="{{$banco->banco_id}}">{{$banco->bancoLista->banco_lista_nombre}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="cuenta_id" class="col-sm-2 col-form-label"># de Cuenta</label>
                    <div class="col-sm-10">
                        <select class="custom-select" id="cuenta_id" name="cuenta_id" onclick="cargarContable();"
                            required>
                            <option value="" label>--Seleccione una opcion--</option>
                        </select>
                    </div>
                </div>
            </div>
            <h5 class="form-control" style="color:#fff; background:#17a2b8;">Datos del Anticipo</h5>
            <div class="card-body">
                <div class="form-group row">
                    <label for="idCliente" class="col-sm-2 col-form-label">Cliente</label>
                    <div class="col-sm-10">
                        <select class="custom-select select2" id="idCliente" name="idCliente" required>
                            <option value="" label>--Seleccione una opcion--</option>
                            @foreach($clientes as $cliente)
                            <option value="{{$cliente->cliente_id}}">{{$cliente->cliente_nombre}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="idMensaje" class="col-sm-2 col-form-label">Motivo</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="idMensaje" name="idMensaje" required>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="idValor" class="col-sm-2 col-form-label">Valor</label>
                    <div class="col-sm-3">
                        <input type="text" class="form-control derecha-texto" id="idValor" name="idValor" placeholder="0.00" required>
                    </div>                    
                    <label for="idDocAnt" class="col-sm-2 col-form-label"><center>Num. Documento</center></label>
                    <div class="col-sm-2">
                        <input type="text" class="form-control" id="idDocAnt" name="idDocAnt"
                            value='0' required>
                    </div>                    
                </div>
            </div>
        </form>
    </div>
    <!-- /.card-body -->
</div>
<!-- /.card -->
<script type="text/javascript">
function bancoActivar() {
    if(document.getElementById("idTipo").value == "Efectivo"){   
        document.getElementById("idCaja").disabled = false; 
        document.getElementById("idDocAnt").disabled = true;    
        document.getElementById("banco_id").disabled = true;
        document.getElementById("cuenta_id").disabled = true;
        document.getElementById("idCuentaContable").disabled = false;
        document.getElementById("idCuentaContable").innerHTML = "<option value='' label>--Seleccione una opcion--</option>";
        document.getElementById("cuenta_id").innerHTML = "<option value='' label>--Seleccione una opcion--</option>";
        cargarCuentaCaja();
    }
    if(document.getElementById("idTipo").value == "Deposito"){
        document.getElementById("idDocAnt").disabled = false;    
        document.getElementById("idCaja").disabled = true;
        document.getElementById("banco_id").disabled = false;
        document.getElementById("cuenta_id").disabled = false;
        document.getElementById("idCuentaContable").disabled = true;
        document.getElementById("idCuentaContable").innerHTML = "<option value='' label>--Seleccione una opcion--</option>";
    }
}
function verificarDato(){
        /*if (document.getElementById("idDocAnt").value == 0) {                              
            bootbox.alert({
                message: "El Numero de cheque no puede ser CERO",
                size: 'small'
            });
            return false;           
        }*/
        document.getElementById("guardarID").value = "Enviando...";
	    document.getElementById("guardarID").disabled = true;
        return true;          
}
function cargarCuenta() {
    $.ajax({
        url: '{{ url("cuentaBancaria/searchN") }}'+ '/' + document.getElementById("banco_id").value,
        dataType: "json",
        type: "GET",
        data: {
            buscar: document.getElementById("banco_id").value
        },
        success: function(data) {
            document.getElementById("cuenta_id").innerHTML =
                "<option value='' label>--Seleccione una opcion--</option>";
            for (var i = 0; i < data.length; i++) {
                document.getElementById("cuenta_id").innerHTML += "<option value='" + data[i]
                    .cuenta_bancaria_id + "'>" + data[i].cuenta_bancaria_numero + "</option>";
            }
        },
    });
}
</script>
@endsection