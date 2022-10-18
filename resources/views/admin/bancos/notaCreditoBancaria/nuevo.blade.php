@extends ('admin.layouts.admin')
@section('principal')
<form class="form-horizontal" method="POST" action="{{ url("notaCreditoBanco") }}" onsubmit="return validacion()">
    @csrf
    <div class="card card-secondary">
        <!-- /.card-header -->
        <div class="card-header">
            <div class="row">
                <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5">
                    <h2 class="card-title">Nota de Crédito de Banco</h2>
                </div>
                <div class="col-xs-7 col-sm-7 col-md-7 col-lg-7">
                    <div class="float-right">
                        <button id="guardarID" type="submit" class="btn btn-success btn-sm"><i class="fa fa-save"></i><span> Guardar</span></button>
                        <button type="button" id="cancelarID" name="cancelarID" onclick="javascript:location.reload()" class="btn btn-default btn-sm not-active-neo"><i class="fas fa-times-circle"></i><span> Cancelar</span></button>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <h5 class="form-control" style="color:#fff; background:#17a2b8;">Datos de la Nota de Crédito</h5>
            <div class="card-body">
                <div class="form-group row">
                    <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 form-control-label ">
                        <label>Numero</label>
                    </div>
                    <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                        <input id="punto_id" name="punto_id" value="{{ $rangoDocumento->puntoEmision->punto_id }}" type="hidden">
                        <input id="rango_id" name="rango_id" value="{{ $rangoDocumento->rango_id }}" type="hidden">
                        <input type="text" id="nota_serie" name="nota_serie" value="{{ $rangoDocumento->puntoEmision->sucursal->sucursal_codigo }}{{ $rangoDocumento->puntoEmision->punto_serie }}" class="form-control derecha-texto negrita " placeholder="Serie" required readonly>
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                        <input type="text" id="nota_numero" name="nota_numero" value="{{ $secuencial }}" class="form-control  negrita " placeholder="Numero" required readonly>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-sm-6">
                        <div class="form-group row">
                            <label for="banco_id" class="col-sm-2 col-form-label">Banco</label>
                            <div class="col-sm-10">
                                <select class="custom-select" id="banco_id" name="banco_id" onchange="cargarCuenta();" required>
                                    <option value="" label>--Seleccione una opcion--</option>
                                    @foreach($bancos as $banco)
                                    <option value="{{$banco->banco_id}}">{{$banco->bancoLista->banco_lista_nombre}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group row">
                            <label for="cuentaB_id" class="col-sm-2 col-form-label">Cuenta</label>
                            <div class="col-sm-10">
                                <select class="custom-select" id="cuentaB_id" name="cuentaB_id" required>
                                    <option value="" label>--Seleccione una opcion--</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group row">
                            <label for="idFecha" class="col-sm-2 col-form-label">Fecha</label>
                            <div class="col-sm-10">
                                <input type="date" class="form-control" id="idFecha" name="idFecha" value='<?php echo (date("Y") . "-" . date("m") . "-" . date("d")); ?>' required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="idBeneficiario" class="col-sm-2 col-form-label">Beneficiario</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" value="SIN BENEFICIARIO" id="idBeneficiario" name="idBeneficiario" onkeyup="cargarBeneficiario();" required>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group row">
                            <label for="idMensaje" class="col-sm-2 col-form-label">Motivo</label>
                            <div class="col-sm-10">
                                <textarea class="form-control" id="idMensaje" name="idMensaje" rows="3" required></textarea>
                            </div>
                        </div>
                    </div>
                </div>                
            </div>
            <div class="row">
                <div class="col-sm-4" style="margin-bottom: 0px;">
                    <label>Movimiento de Cuenta</label>
                    <div class="form-group">
                        <div class="form-line">
                            <select class="custom-select select2" id="tipo_movimiento" name="tipo_movimiento" onchange="autoDescripcion();" required>
                                <option value="" label>--Seleccione una opcion--</option>
                                @foreach($movimientos as $movimiento)
                                     <option value="{{$movimiento->tipo_id}}">{{$movimiento->tipo_nombre}}</option> 
                                @endforeach
                            </select>
                            <select class="invisible" id="id_movimiento" name="id_movimiento"
                                    data-live-search="true" >
                                <option value="" label>--Seleccione una opcion--</option>
                                @foreach($movimientos as $movimiento)
                                     <option value="{{$movimiento->tipo_id}}">{{$movimiento->tipo_movimiento}}</option> 
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-sm-1" style="margin-bottom: 0px;">
                    <label>Tipo</label>
                    <div class="form-group">
                        <div class="form-line">
                            <input type="text" id="movimiento_id" name="movimiento_id" class="form-control" placeholder="Tipo" readonly>
                        </div>
                    </div>
                </div>
                <div class="col-sm-5" style="margin-bottom: 0px;">
                    <label>Descripción</label>
                    <div class="form-group">
                        <div class="form-line">
                            <input type="text" id="id_descripcion" name="id_descripcion" class="form-control" placeholder="Descripcion">
                        </div>
                    </div>
                </div>               
                <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1" style="margin-bottom: 0px;">
                    <center><label>Valor</label></center>
                    <div class="form-group">
                        <div class="form-line">
                            <input type="number" id="id_haber" name="id_haber" class="form-control centrar-texto" placeholder="0.00" value="0.00" step="any">
                        </div>
                    </div>
                </div>
                <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
                    <a onclick="agregarItem();" class="btn btn-primary btn-venta"><i
                            class="fas fa-plus"></i></a>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    @include ('admin.bancos.notaCreditoBancaria.itemAsiento')
                    <table id="item" class="table table-striped table-hover boder-sar tabla-item-factura" style="white-space: normal!important; border-collapse: collapse;">
                        <thead>
                            <tr class="letra-blanca" style="background-color: #0c7181;">                                
                                <th>MOVIMIENTO</th>
                                <th>TIPO </th>       
                                <th>DESCRIPCIÓN </th>                               
                                <th class="centrar-texto">VALOR</th>
                                <th width="40"></th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-8"></div>              
               
            </div>
            <div class="row">
                <div class="col-sm-8"></div>
                <div class="col-sm-2">
                    <center><label class="col-form-label">TOTAL</label></center>
                </div>
                <div class="col-sm-2">
                    <input type="number" class="form-control centrar-texto" id="IdTotal" name="IdTotal" value="0.00"placeholder="0.00" readonly>
                </div>
            </div>
        </div>
    </div>
</form>
<script type="text/javascript">
    function cargarCuenta() {
        $.ajax({
            url: '{{ url("cuentaBancaria/searchN") }}'+ '/' +document.getElementById("banco_id").value,
            dataType: "json",
            type: "GET",
            data: {
                buscar: document.getElementById("banco_id").value
            },
            success: function(data) {
                document.getElementById("cuentaB_id").innerHTML = "<option value='' label>--Seleccione una opcion--</option>";
                for (var i = 0; i < data.length; i++) {
                    document.getElementById("cuentaB_id").innerHTML += "<option value='" + data[i].cuenta_bancaria_id + "'>" + data[i].cuenta_bancaria_numero + "</option>";
                }
            },
        });
    }
    id_item = 1;
    function agregarItem() {
        if(document.getElementById("id_descripcion").value != '' && document.getElementById("movimiento_id").value != ''){
            if(document.getElementById("id_haber").value > 0){
                combo = document.getElementById("tipo_movimiento");
                texto = combo.options[combo.selectedIndex].text;                
                var linea = $("#plantillaItem").html();
                linea = linea.replace(/{ID}/g, id_item);
                linea = linea.replace(/{idCuenta}/g,document.getElementById("tipo_movimiento").value);               
                linea = linea.replace(/{cuenta}/g, texto);
                linea = linea.replace(/{descripcion}/g, document.getElementById("id_descripcion").value);
                linea = linea.replace(/{tipo}/g, document.getElementById("movimiento_id").value);
                linea = linea.replace(/{haber}/g, Number(document.getElementById("id_haber").value).toFixed(2));
                $("#item tbody").append(linea);
                id_item = id_item + 1;
                totalSeleccion();
                limipiar();
            }
        }
    }
    function totalSeleccion(){     
        document.getElementById("IdTotal").value = 0
        for (var i = 2; i <= id_item; i++) {
          
            if($("input[name='Dtipo[]']")[i].value=='CREDITO'){
                document.getElementById("IdTotal").value = Number(Number(document.getElementById("IdTotal").value) + Number($("input[name='Dhaber[]']")[i].value)).toFixed(2);
            }
            if($("input[name='Dtipo[]']")[i].value=='DEBITO'){
                document.getElementById("IdTotal").value = Number(Number(document.getElementById("IdTotal").value) - Number($("input[name='Dhaber[]']")[i].value)).toFixed(2);
            }
        }
    }
    function limipiar(){
        document.getElementById("id_descripcion").value = '';
        document.getElementById("id_haber").value = "0.00";
    }
    function eliminarItem(id){       
        $("#row_" + id).remove();      
        totalSeleccion();
    }    
    function validacion(){ 
        document.getElementById("guardarID").value = "Enviando...";
	    document.getElementById("guardarID").disabled = true;       
        if(document.getElementById("IdTotal").value <= 0){
            document.getElementById("guardarID").disabled = false; 
            alert('El valor total no puede ser CERO, tampoco negativo');
            
            return false
        }       
        return true;
    }
    function autoDescripcion(){
    combo = document.getElementById("tipo_movimiento");
    texto = combo.options[combo.selectedIndex].text;
    document.getElementById("id_descripcion").value  = 'P/R'+' '+texto;
    document.getElementById("id_haber").value = "0.00";

     document.getElementById("id_movimiento").value=document.getElementById("tipo_movimiento").value;
     combo2 = document.getElementById("id_movimiento");
    texto2 = combo2.options[combo2.selectedIndex].text;
    document.getElementById("movimiento_id").value = texto2;
}
</script>
@endsection
