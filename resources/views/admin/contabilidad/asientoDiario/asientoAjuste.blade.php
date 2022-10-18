@extends ('admin.layouts.admin')
@section('principal')
<form class="form-horizontal" method="POST"  action='{{ url("asientoDiario/asientoAjuste") }}'  onsubmit="return validacion()">
@csrf
    <div class="card card-secondary">
        <div class="card-header">
            <h3 class="card-title">Asiento de Ajuste</h3>
            <div class="float-right">
                <button type="submit" class="btn btn-default btn-sm"><i class="fa fa-save"></i><span>&nbsp;&nbsp;Guardar</span></button>
            </div>
        </div>
        <div class="card-body">
            <input type="hidden" id="IdDiario" name="IdDiario"/>
            <div class="form-group row">
                <label for="fecha_desde" class="col-sm-1 col-form-label">Código :</label>
                <div class="col-sm-3">
                    <input class="form-control" id="IdCodigo" name="IdCodigo" value="{{ $codigo }}" readonly required/>
                </div>
                <label for="fecha_desde" class="col-sm-1 col-form-label">Fecha :</label>
                <div class="col-sm-3">
                    <input class="form-control" type="date" id="IdFecha" name="IdFecha" onchange="codigoDiario();" value='<?php echo(date("Y")."-".date("m")."-".date("d")); ?>' required/>
                </div>
                <label for="fecha_desde" class="col-sm-1 col-form-label">Sucursal :</label>
                <div class="col-sm-3">
                    <select class="form-control select2" id="sucursal_id" name="sucursal_id" style="width: 100%;" required>
                        <option value="" label>--Seleccione--</option>
                        @foreach($sucursales as $sucursal)
                            <option value="{{$sucursal->sucursal_id}}">{{$sucursal->sucursal_nombre}}</option>
                        @endforeach
                    </select>
                </div>

            </div>
            <div class="form-group row">
                <label for="fecha_desde" class="col-sm-2 col-form-label">Número documento :</label>
                <div class="col-sm-2">
                    <input class="form-control" id="IdNumero" name="IdNumero" placeholder="000" required/>
                </div>
                <label for="fecha_desde" class="col-sm-2 col-form-label">Referencia :</label>
                <div class="col-sm-6">
                    <input class="form-control" id="IdReferencia" name="IdReferencia" value="COMPROBANTE DE DIARIO CONTABLE" readonly required/>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group row">
                        <label for="fecha_desde" class="col-sm-4 col-form-label">Tipo documento :</label>
                        <div class="col-sm-8">
                            <input class="form-control" id="IdTipoDocumento" name="IdTipoDocumento" value="DIARIO CONTABLE" readonly required/>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="IdBeneficiario" class="col-sm-4 col-form-label">Beneficiario :</label>
                        <div class="col-sm-8">
                            <input class="form-control" id="IdBeneficiario" name="IdBeneficiario" value="SIN BENEFICIARIO" placeholder="..." required/>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group row">
                        <label for="fecha_desde" class="col-sm-3 col-form-label">Comentario :</label>
                        <div class="col-sm-9">
                            <textarea class="form-control" id="IdComentario" name="IdComentario" rows="3" placeholder="..." required></textarea>
                        </div>

                    </div>
                </div>
            </div>
            <hr style="background: #a3aab3;">
            <div class="row">
                <div class="col-sm-5" style="margin-bottom: 0px;">
                    <label>Cuenta Contable</label>
                    <div class="form-group">
                        <div class="form-line">
                            <select class="form-control select2" id="cuenta_id" name="cuenta_id" style="width: 100%;">
                                @foreach($cuentas as $cuenta)
                                    <option value="{{$cuenta->cuenta_id}}">{{$cuenta->cuenta_numero.' - '.$cuenta->cuenta_nombre}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4" style="margin-bottom: 0px;">
                    <label>Descripción</label>
                    <div class="form-group">
                        <div class="form-line">
                            <input id="id_descripcion" name="id_descripcion" class="form-control" placeholder="Descripcion">
                        </div>
                    </div>
                </div>
                <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1" style="margin-bottom: 0px;">
                <center><label>Debe</label></center>
                    <div class="form-group">
                        <div class="form-line">
                            <input id="id_debe" name="id_debe" class="form-control centrar-texto" placeholder="0.00" value="0.00">
                        </div>
                    </div>
                </div>
                <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1" style="margin-bottom: 0px;">
                    <center><label>Haber</label></center>
                    <div class="form-group">
                        <div class="form-line">
                            <input id="id_haber" name="id_haber" class="form-control centrar-texto" placeholder="0.00" value="0.00">
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
                    @include ('admin.contabilidad.asientoDiario.itemAsiento')
                    <table id="item" class="table table-striped table-hover boder-sar tabla-item-factura" style="white-space: normal!important; border-collapse: collapse;">
                        <thead>
                            <tr class="letra-blanca" style="background-color: #0c7181;">
                                <th>CÓDIGO</th>
                                <th>CUENTA</th>
                                <th>DESCRIPCIÓN </th>
                                <th class="centrar-texto">DEBE</th>
                                <th class="centrar-texto">HABER</th>
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
                <div class="col-sm-2">
                    <div class="form-group">
                        <center><label class="col-form-label">DEBE</label></center>
                        <input type="text" class="form-control centrar-texto" id="IdDebe" name="IdDebe" value="0.00" placeholder="0.00" readonly>
                    </div>
                </div>
                <div class="col-sm-2">
                    <div class="form-group">
                        <center><label class="col-form-label">HABER</label></center>
                        <input type="text" class="form-control centrar-texto" id="IdHaber" name="IdHaber" value="0.00"placeholder="0.00" readonly>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-8"></div>
                <div class="col-sm-2">
                    <center><label class="col-form-label">DIFERENCIA</label></center>
                </div>
                <div class="col-sm-2">
                    <input type="text" class="form-control centrar-texto" id="IdDIF" name="IdDIF" value="0.00"placeholder="0.00" readonly>
                </div>
            </div>
        </div>
    </div>
</form>
<script type="text/javascript">
    id_item = 1;
    function agregarItem() {
        if(document.getElementById("id_descripcion").value != ''){
            if(document.getElementById("id_debe").value > 0 || document.getElementById("id_haber").value > 0){
                cuentaPadre();         
                combo = document.getElementById("cuenta_id");
                texto = combo.options[combo.selectedIndex].text;
                texto = texto.split('-');
                var linea = $("#plantillaItem").html();
                linea = linea.replace(/{ID}/g, id_item);
                linea = linea.replace(/{idCuenta}/g,document.getElementById("cuenta_id").value);
                linea = linea.replace(/{codigo}/g, texto[0].trim());
                linea = linea.replace(/{cuenta}/g, texto[1].trim());
                linea = linea.replace(/{descripcion}/g, document.getElementById("id_descripcion").value);
                linea = linea.replace(/{debe}/g, Number(document.getElementById("id_debe").value).toFixed(2));
                linea = linea.replace(/{haber}/g, Number(document.getElementById("id_haber").value).toFixed(2));
                $("#item tbody").append(linea);
                id_item = id_item + 1;
                totalSeleccion();
                limipiar();
            }
        }
    }
    function totalSeleccion(){
        document.getElementById("IdDebe").value = 0.00;
        document.getElementById("IdHaber").value = 0.00;
        for (var i = 2; i <= id_item; i++) {
            document.getElementById("IdDebe").value = Number(Number(document.getElementById("IdDebe").value) + Number($("input[name='Ddebe[]']")[i].value)).toFixed(2);
            document.getElementById("IdHaber").value = Number(Number(document.getElementById("IdHaber").value) + Number($("input[name='Dhaber[]']")[i].value)).toFixed(2);
        }
        document.getElementById("IdDIF").value = Number(Number(document.getElementById("IdDebe").value) - Number(document.getElementById("IdHaber").value)).toFixed(2);
    }
    function limipiar(){
        document.getElementById("id_descripcion").value = '';
        document.getElementById("id_debe").value = "0.00";
        document.getElementById("id_haber").value = "0.00";
    }
    function eliminarItem(id){
        $("#row_" + (id-1)).remove();
        $("#row_" + id).remove();
        id_item = id_item -2;
        totalSeleccion();
    }
    function codigoDiario(){
        $.ajax({
            async : false,
            url: '{{ url("diarioCodigo/searchN") }}'+ '/' +document.getElementById("IdFecha").value,
            dataType: "text",
            type: "GET",
            data: {
                buscar: document.getElementById("IdFecha").value
            },
            success: function(data){
                document.getElementById("IdCodigo").value = data;   
            },
        });
    }
    function cuentaPadre(){
        $.ajax({
            async : false,
            url: '{{ url("cuentaContablePadre/searchN") }}'+ '/' +document.getElementById("cuenta_id").value,
            dataType: "json",
            type: "GET",
            data: {
                buscar: document.getElementById("cuenta_id").value
            },
            success: function(data){
                var linea = $("#plantillaItemPadre").html();
                linea = linea.replace(/{ID}/g, id_item);
                linea = linea.replace(/{idCuenta}/g, '');
                linea = linea.replace(/{codigo}/g, data.cuenta_numero);
                linea = linea.replace(/{cuenta}/g, data.cuenta_nombre);
                linea = linea.replace(/{descripcion}/g, '');
                linea = linea.replace(/{debe}/g, '0');
                linea = linea.replace(/{haber}/g, '0');
                $("#item tbody").append(linea);
                id_item = id_item + 1;
            },
        });
    }
    function validacion(){
        if(document.getElementById("IdDebe").value <= 0){
            bootbox.alert({
            message: "El debe tiene que ser mayor a 0.",
                size: 'small'
            });
            return false
        }
        if(document.getElementById("IdHaber").value <= 0){
            bootbox.alert({
            message: "El haber tiene que ser mayor a 0.",
                size: 'small'
            });
            return false
        }
        if(document.getElementById("IdDIF").value != 0){
            bootbox.alert({
            message: "El debe no coincide con el haber.",
                size: 'small'
            });
            return false
        }
        return true;
    }
</script>
@endsection